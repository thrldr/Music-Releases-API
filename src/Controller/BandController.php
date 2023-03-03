<?php

namespace App\Controller;

use App\Entity\Band;
use App\Repository\BandRepository;
use App\Service\MusicDb\MusicDbServiceInterface;
use App\Service\Notification\NotificationMaker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BandController extends AbstractController
{
    const WRONG_BAND_NAME_MESSAGE = "Sorry, there's no band under that name";
    const BAND_CREATED_MESSAGE = "Band successfully created";
    const BAND_DELETED_MESSAGE = "Band successfully deleted";

    public function __construct(private BandRepository $bandRepository)
    {
    }

    /** get list of bands (based on user id or not) */
    #[Route(path: "/bands/{id}", methods: ["get"])]
    public function bands(?int $id = null): Response
    {
        if (!isset($id)) {
            $bands = $this->bandRepository->findAll();
            $serializedBands = [];

            foreach ($bands as $band) {
                $subscribers = $band->getSubscribedUsers();

                $subscribersArray = [];
                foreach ($subscribers as $subscriber) {
                    $subscribersArray[] = $subscriber->getEmail();
                }
                $serializedBands[] = [
                    $band->getName() => $subscribersArray,
                ];
            }
            return $this->json($serializedBands);
        }

        // TODO: add a response based on a user id
        return $this->json([]);
    }

    /** create a new band */
    #[Route(path: "/band", methods: ["post"])]
    public function create(
        Request $request,
        BandRepository $bandRepository,
        ValidatorInterface $validator,
        MusicDbServiceInterface $musicDbServiceService,
    ): Response
    {
        $data = $request->toArray();
        $bandName = $data["name"];

        if (!$musicDbServiceService->bandInDb($bandName)) {
            $this->json(data: ["message" => self::WRONG_BAND_NAME_MESSAGE], status: 400);
        }

        $band = new Band($bandName);
        $latestAlbum = $musicDbServiceService->getMostRecentAlbum($band);
        $band->setLastAlbum($latestAlbum);

        $errors = $validator->validate($band);
        if (count($errors) > 0) {
            $errorsMessage = (string) $errors;
            return $this->json(data: ["message" => $errorsMessage], status: 400);
        }

        $bandRepository->save($band, true);
        return $this->json(data: self::BAND_CREATED_MESSAGE, status: 201);
    }

    /** delete a band */
    #[Route(path: "/band", methods: "delete")]
    public function delete(
        Request $request,
        BandRepository $bandRepository,
    )
    {
        $data = $request->toArray();
        $bandName = $data["name"];
        $band = $bandRepository->findOneBy(["name" => $bandName]);
        $bandRepository->remove($band, true);
        return $this->json(["message" => self::BAND_DELETED_MESSAGE]);
    }
}
