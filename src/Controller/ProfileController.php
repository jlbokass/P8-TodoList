<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
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

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('profile_show');
        }

        $em = $this->getDoctrine()->getManager();
        $em->refresh($user);

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/profile/delete/{id}", name="delete_profile")
     *
     */
    public function deleteProfile(User $user, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/profile/request/delete", name="profile_request_delete")
     *
     * @return Response
     */
    public function requestDeleteProfile(): Response
    {
        return $this->render('profile/requestDeleteProfile.html.twig');
    }
}
