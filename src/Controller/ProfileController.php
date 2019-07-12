<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile_show")
     *
     * @return Response
     */
    public function show(): Response
    {
        return $this->render('profile/show.html.twig');
    }

    /**
     * @Route("/profile/{id}/edit", name="profile_edit")
     *
     * @param User $user
     * @param Request $request
     *
     * @return Response
     */
    public function edit(
        User $user,
        Request $request
    ): Response
    {
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('profile_show');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user
        ]);
    }
}
