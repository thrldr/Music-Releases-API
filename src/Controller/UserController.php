<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\User;
use App\Repository\BandRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    const ALL_USERS_REMOVED_MESSAGE = "all users are removed";
    const USER_SUBSCRIBED_BAND_MESSAGE = "user subscribed to a band";
    const NO_SUCH_BAND_MESSAGE = "no band under this name";

    public function __construct(
        private UserRepository $userRepository,
        private BandRepository $bandRepository,
    )
    {
    }

    /** deletes all users */
    #[Route(path: "/users", methods: ["delete"])]
    public function clear(): Response
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->userRepository->remove($user, true);
        }
        return $this->json(["message" => self::ALL_USERS_REMOVED_MESSAGE]);
    }

    /** lists all users and their bands */
    #[Route(path: "/users", methods: ["get"])]
    public function users(): Response
    {
        $useres = $this->userRepository->findAll();
        $usersArray = [];
        foreach ($useres as $user) {
            $bands = $user->getSubscribedBands();
            $bandStrings = array_map((fn(Band $band) => $band->getName()), $bands->toArray());
            $usersArray[] = [$user->getEmail(), $bandStrings];
        }
        return $this->json(["users" => $usersArray]);
    }

    #[Route(path: "user", methods: ["patch"])]
    public function registerGroup(
        Request $request,
    ): Response
    {
        $data = $request->toArray();
        $user = $this->userRepository->findOneBy(["email" => "test@mail.com"]);

        $newServices = $data["notifiers"] ?? $user->getNotificationServices();
        $user->setNotificationServices($newServices);

        $bandNames = $data["band-names"] ?? [];
        foreach ($bandNames as $bandName) {
            $band = $this->bandRepository->findOneBy(["name" => $bandName]);
            if (!isset($band)) {
                return $this->json(["message" => self::NO_SUCH_BAND_MESSAGE], 400);
            }
            $user->addSubscribedBand($band);
        }

//        TODO: add user retrieval from session
//        /** @var User $user */
//        $user = $this->getUser();

        $this->userRepository->save($user, true);
        return $this->json(["message" => self::USER_SUBSCRIBED_BAND_MESSAGE]);
    }
}
