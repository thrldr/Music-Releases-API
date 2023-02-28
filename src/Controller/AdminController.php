<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    #[Route("/admin/promote", methods: "get")]
    public function promote(
        Request $request
    )
    {
        $data = $request->toArray();
        $userEmail = $data["email"];
        if (!isset($userEmail)) {
            return $this->json(["message" => "please provide an email"], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(["email" => $userEmail]);
        if (!isset($user)) {
            return $this->json(["message" => "no user could be found under this email"], Response::HTTP_NOT_FOUND);
        }
        $user->addRole(Roles::admin);
        $this->userRepository->save($user, true);
        return $this->json(["message" => "ok"], Response::HTTP_OK);
    }
}