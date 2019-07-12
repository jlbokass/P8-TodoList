<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\EmailToResetPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Services\ResetPasswordSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordController extends AbstractController
{
    /**
     * @Route("/reset", name="password_reset")
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ResetPasswordSender $sender
     *
     * @return Response
     */
    public function index(
        Request $request,
        UserRepository $userRepository,
        ResetPasswordSender $sender
    ): Response
    {
        $form = $this->createForm(EmailToResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash(
                    'warning',
                    'Email non valide'
                )
                ;

                return $this->redirectToRoute('password_reset');
            }

            $token = new Token($user);
            $sender->sendToken($user, $token);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($token);
            $manager->flush();

            return $this->render('password/resetRequested.html.twig');
        }

        return $this->render('password/forgot.html.twig', [
            'emailToResetPassword' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirmation/reset-password/{token}", name="reset_token_validation")
     *
     * @param $token
     * @param TokenRepository $repository
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function validateResetToken(
        $token,
        TokenRepository $repository,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $token = $repository->findOneBy(['token' => $token]);

        if (!$token->getExpiresAt()) {
            return $this->render('password/tokenExpired.html.twig');
        }

        /** @var User $user */
        $user = $token->getUser();

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()));

            $manager = $this->getDoctrine()->getManager();

            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('password/reset.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
