<?php

namespace App\Controller;

use App\Entity\Band;
use App\Repository\BandRepository;
use App\Repository\UserRepository;
use App\Service\MusicDb\MusicDbServiceInterface;
use Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BandController extends AbstractController
{
    const WRONG_BAND_NAME_MESSAGE = "There's no band under that name";
    const BAND_CREATED_MESSAGE = "Band successfully created";
    const BAND_DELETED_MESSAGE = "Band successfully deleted";

    public function __construct(
        private BandRepository $bandRepository,
        private UserRepository $userRepository,
    )
    {
    }

    /** list subscribed bands of the authenticated user */
    #[Route(path: "/bands", methods: ["get"])]
    public function bands(): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw new TokenNotFoundException();
        }

        $userEntity = $this->userRepository->fetchOneByEmail($user->getUserIdentifier());
        $bands = $userEntity->getSubscribedBands();

        return $this->json(["bands" => $bands], Response::HTTP_OK, [], [
            ObjectNormalizer::GROUPS => 'get_bands',
        ]);
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
            $this->json([
                "message" => self::WRONG_BAND_NAME_MESSAGE],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $band = new Band($bandName);
        $latestAlbum = $musicDbServiceService->getMostRecentAlbum($band);
        $band->setLatestAlbum($latestAlbum);

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
