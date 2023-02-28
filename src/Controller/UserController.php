<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\User;
use App\Repository\BandRepository;
use App\Repository\UserRepository;
use App\Service\Notifier\EmailNotifier;
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

    #[Route("/send-email")]
    public function sendMail(EmailNotifier $emailNotifier): Response
    {
        $emailNotifier->notify("lol");

        return new Response("ok");
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

    /** lists the authenticated user's bands */
    #[Route(path: "/user", methods: ["get"])]
    public function user(): Response
    {
        $user = $this->getUser();
        $bands = $user->getSubscribedBands();
        $bandsArray = array_map((fn(Band $band) => $band->getName()), $bands->toArray());
        return $this->json([$user->getEmail() => $bandsArray]);
    }

    /** lists all users and their bands */
    #[Route(path: "/users", methods: ["get"])]
    public function users(): Response
    {
        $users = $this->userRepository->findAll();
        return $this->json($users, Response::HTTP_OK,
            context: ['groups' => ['band']]
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

        $this->userRepository->save($user, true);
        return $this->json(["message" => self::USER_SUBSCRIBED_BAND_MESSAGE]);
    }
}
