<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\Notifiers\Parser\UserNotificationServicesParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    const SUCCESSFUL_REMOVAL_MESSAGE = "all users are removed";

    /** deletes all users */
    #[Route(path: "/users", methods: ["delete"])]
    public function clear(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        foreach ($users as $user) {
            $userRepository->remove($user, true);
        }
        return $this->json(["message" => self::SUCCESSFUL_REMOVAL_MESSAGE]);
    }

    /** lists all users */
    #[Route(path: "/users", methods: ["get"])]
    public function users(UserRepository $userRepository): Response
    {
        $useres = $userRepository->findAll();
        $usersArray = [];
        foreach ($useres as $user) {
            $usersArray[] = $user->getEmail();
        }
        return $this->json(["users" => $usersArray]);
    }
}
