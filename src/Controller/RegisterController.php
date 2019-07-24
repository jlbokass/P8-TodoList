<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
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
     *
     * @return Response
     */
    public function registration(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()
            ))
            ;
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte à bin été créé!'
            );

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
