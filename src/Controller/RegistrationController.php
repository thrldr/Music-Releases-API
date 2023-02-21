<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    const SUCCESSFUL_REGISTRATION_MESSAGE = "User successfully registered";
    const INSUFFICIENT_DATA_MESSAGE = "You must provide a valid email and password to register";

    #[Route("/clear")]
    public function clear(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        foreach ($users as $user) {
            $userRepository->remove($user, true);
        }
        return $this->json(["message" => "all useres are removed"]);
    }

    #[Route("/users")]
    public function users(UserRepository $userRepository): Response
    {
        $useres = $userRepository->findAll();
        $usersArray = [];
        foreach ($useres as $user) {
            $usersArray[] = $user->getEmail();
        }
        return $this->json(["users" => $usersArray]);
    }

    #[Route(path: "/register", methods: ["post"])]
    public function index(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
        ValidatorInterface $validator,
    ): Response
    {
        $data = $request->toArray();
        $email = $data["email"];
        $plainPassword = $data["password"];
        $notificationServices = $data["notification services"] ?? 0;

        try {
            if (!isset($email) or !isset($plainPassword)) {
                throw(new \InvalidArgumentException(self::INSUFFICIENT_DATA_MESSAGE));
            }

            $user = new User($email, $plainPassword, $notificationServices);

            $hashedPassword = $hasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                throw(new \InvalidArgumentException((string) $errors));
            }

            $user->eraseCredentials();
            $userRepository->save($user, true);
            $statusCode = 201;
            $responseData = self::SUCCESSFUL_REGISTRATION_MESSAGE;

        } catch (\InvalidArgumentException $exception) {
            $statusCode = 400;
            $responseData = $exception->getMessage();
        } catch (\Exception $exception) {
            $statusCode = 500;
            $responseData = $exception->getMessage();
        }

        return $this->json(data: ["message" => $responseData], status: $statusCode);
    }
}
