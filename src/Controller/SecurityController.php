<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SecurityController extends AbstractController
{
    const SUCCESSFUL_REGISTRATION_MESSAGE = "User successfully registered";

    public function __construct(
        private SerializerInterface $serializer,
        private UserRepository $userRepository,
    )
    {
    }

    #[Route(path: '/register', methods: ['post', 'get'])]
    public function register(
        Request $request,
    ): Response
    {
        $data = $request->getContent();

        /** @var User $user */
        $user = $this->serializer->deserialize(
            $data,
            User::class,
            'json',
            [ObjectNormalizer::GROUPS => 'credentials']
        );

        $this->userRepository->createRegistered($user, true);

        $responseMessage = self::SUCCESSFUL_REGISTRATION_MESSAGE;
        return $this->json(["message" => $responseMessage], Response::HTTP_CREATED);
    }
}
