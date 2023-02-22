<?php

namespace App\Controller;

use App\Entity\Band;
use App\Repository\BandRepository;
use App\Services\MusicDb\MusicDbServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BandController extends AbstractController
{
    const WRONG_BAND_NAME_MESSAGE = "Sorry, there's no band under that name";
    const BAND_CREATED_MESSAGE = "Band successfully created";

    public function __construct(private BandRepository $bandRepository)
    {
    }

    /** get list of bands (based on user id or not) */
    #[Route(path: "/bands", methods: ["get"])]
    public function bands(?int $userId): Response
    {
        if (!isset($userId)) {
            $bands = $this->bandRepository->findAll();
            $bandsArray = [];
            foreach ($bands as $band) {
                $bandsArray[] = $band;
            }
            return $this->json($bandsArray);
        }

        // TODO: add a response based on a user id
        return $this->json([]);
    }

    /** create a new band */
    #[Route(path: "/band", methods: ["post"])]
//    #[Route(path: "/band", methods: ["get"])]
    public function create(
        Request $request,
        BandRepository $bandRepository,
        ValidatorInterface $validator,
        MusicDbServiceInterface $musicDbServiceService,
    ): Response
    {
        $data = $request->toArray();
        $bandName = $data["name"];

        if (!$musicDbServiceService->bandNameInDb($bandName)) {
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
}
