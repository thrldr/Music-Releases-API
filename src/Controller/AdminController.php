<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class AdminController extends AbstractController
{
    const PROVIDE_EMAIL_MESSAGE = 'please provide an email';
    const USER_PROMOTED_MESSAGE = 'user promoted';

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
            return $this->json(["message" => self::PROVIDE_EMAIL_MESSAGE], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(["email" => $userEmail]);
        if (!isset($user)) {
            throw new UserNotFoundException();
        }

        $user->addRole(Roles::admin);
        $this->userRepository->save($user, true);
        return $this->json(["message" => self::USER_PROMOTED_MESSAGE], Response::HTTP_OK);
    }
}