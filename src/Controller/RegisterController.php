<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\TokenRepository;
use App\Services\TokenSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/registration", name="app_register")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface       $manager
     * @param TokenSender                  $sender
     *
     * @return Response
     */
    public function registration(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager,
        TokenSender $sender): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()
            //$user->getPassword()
            )
            );
            $user->setRoles(['ROLE_USER']);
            $token = new Token($user);
            $sender->sendToken($user, $token);
            $manager->persist($user);
            $manager->persist($token);
            $manager->flush();
            $this->addFlash(
                'success',
                'Please check your email'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirmation/{token}", name="token_validation")
     *
     * @param $token
     * @param TokenRepository     $repository
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function validateToken(
        $token,
        TokenRepository $repository,
        EntityManagerInterface $manager
    ): Response
    {
        $token = $repository->findOneBy(['token' => $token]);
        $user = $token->getUser();
        if ($user->getIsEnable()) {
            return $this->render('registration/alreadyRegister.html.twig');
        }
        if ($token->getExpiresAt()) {
            $user->setIsEnable(true);
            $manager->flush();
            return $this->render('/registration/activated.html.twig');
        }
        $manager->remove($token);
        $manager->flush();
        $this->addFlash(
            'notice',
            'date expired'
        );
        return $this->redirectToRoute('app_login');
    }
}
