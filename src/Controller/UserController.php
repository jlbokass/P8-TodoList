<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function list(UserRepository $userRepository)
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }
}
