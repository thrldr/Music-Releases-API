<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\User;
use App\Repository\BandRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

class UserController extends AbstractController
{
    const USER_SUBSCRIBED_BAND_MESSAGE = "user subscribed to a band";
    const NO_SUCH_BAND_MESSAGE = "no band under this name";

    public function __construct(
        private UserRepository $userRepository,
        private BandRepository $bandRepository,
    )
    {
    }

    /** lists the authenticated user's bands */
    #[Route(path: "/user/{id}", methods: ["get"])]
    public function user(): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw new TokenNotFoundException("No access token provided");
        }
        $bands = $user->getSubscribedBands();
        $bandsArray = array_map((fn(Band $band) => $band->getName()), $bands->toArray());
        return $this->json([$user->getEmail() => $bandsArray]);
    }

    /** lists all users and their bands */
    #[Route(path: "/users", methods: ["get"])]
    public function users(): Response
    {
        $users = $this->userRepository->fetchAll();
        return $this->json($users, Response::HTTP_OK,
            ['groups' => ['band']]
        );
    }

    #[Route(path: "user", methods: ["patch"])]
    public function registerGroup(
        Request $request,
    ): Response
    {
        $data = $request->toArray();

        /** @var User $user */
        $user = $this->getUser();

        $newServices = $data["notifiers"] ?? $user->getNotifiers();
        $user->setNotifiers($newServices);

        $bandNames = $data["band-names"] ?? [];
        foreach ($bandNames as $bandName) {
            $band = $this->bandRepository->findOneBy(["name" => $bandName]);
            if (!isset($band)) {
                return $this->json(["message" => self::NO_SUCH_BAND_MESSAGE], 400);
            }
            $user->addSubscribedBand($band);
        }

        $this->userRepository->save($user, true);
        return $this->json(["message" => self::USER_SUBSCRIBED_BAND_MESSAGE]);
    }
}
