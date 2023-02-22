<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route(path: "/users", methods: ["delete"])]
    public function clear(UserRepository $userRepository): Response
    {
        try {
            $users = $userRepository->findAll();
            foreach ($users as $user) {
                $userRepository->remove($user, true);
            }
            return $this->json(["message" => "all useres are removed"]);
        } catch (\Exception $exception) {
            return $this->json(data: ["message" => $exception->getMessage()], status: 500);
        }
    }

    #[Route("/users")]
    public function users(UserRepository $userRepository): Response
    {
        try {
            $useres = $userRepository->findAll();
            $usersArray = [];
            foreach ($useres as $user) {
                $usersArray[] = $user->getEmail();
            }
            return $this->json(["users" => $usersArray]);
        } catch (\Exception $exception) {
            return $this->json(data: ["message" => $exception->getMessage()], status: 500);
        }
    }
}