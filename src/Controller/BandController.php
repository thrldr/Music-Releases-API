<?php

namespace App\Controller;

use App\Entity\Band;
use App\Repository\BandRepository;
use App\Repository\UserRepository;
use App\Service\MusicDb\RemoteMusicDbInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BandController extends AbstractController
{
    const WRONG_BAND_NAME_MESSAGE = 'There\'s no band under that name';
    const BAND_CREATED_MESSAGE    = 'Band successfully created';
    const BAND_DELETED_MESSAGE    = 'Band successfully deleted';
    const SUBSCRIBED_MESSAGE      = 'Sussessfully subscribed to the band';

    public function __construct(
        private BandRepository         $bandRepository,
        private UserRepository         $userRepository,
        private RemoteMusicDbInterface $remoteMusicDb,
    )
    {
    }

    /** list subscribed bands of the authenticated user */
    #[Route(path: "/bands", methods: ["get"])]
    public function bands(): Response
    {
        $user = $this->getUserOrThrowException();

        $bands = $user->getSubscribedBands();

        return $this->json(['bands' => $bands], Response::HTTP_OK, [], [
            ObjectNormalizer::GROUPS => 'get_bands',
        ]);
    }

    private function getUserOrThrowException()
    {
        $userProvider = $this->getUser();
        if ($userProvider === null) {
            throw new TokenNotFoundException();
        }

        $userEntity = $this->userRepository->fetchOneByEmail($userProvider->getUserIdentifier());
        return $userEntity;
    }

    #[Route(path: 'band/subscribe', methods: ['get'])]
    public function subscribe(Request $request)
    {
        $user = $this->getUserOrThrowException();

        $bandName = $request->query->get('band');

        $band = $this->bandRepository->findOneBy(['name' => $bandName]);

        try {
            if ($band == null) {
                $band = new Band($bandName);
                $latestAlbum = $this->remoteMusicDb->getLatestAlbum($bandName);
                $band->setLatestAlbum($latestAlbum);
                $this->bandRepository->save($band, true);
            }
        } catch (\Exception $exception) {
            throw new InvalidArgumentException("Can't find a band under such name");
        }

        $user->addSubscribedBand($band);
        $this->userRepository->save($user, true);

        return $this->json(['message' => self::SUBSCRIBED_MESSAGE], Response::HTTP_OK);
    }

    /** create a new band */
    #[Route(path: "/band", methods: ["post"])]
    public function create(
        Request                $request,
        BandRepository         $bandRepository,
        ValidatorInterface     $validator,
        RemoteMusicDbInterface $musicDbServiceService,
    ): Response
    {
        $data = $request->toArray();
        $bandName = $data['name'];

        if (!$musicDbServiceService->bandInDb($bandName)) {
            $this->json([
                'message' => self::WRONG_BAND_NAME_MESSAGE],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $band = new Band($bandName);
        $latestAlbum = $musicDbServiceService->getMostRecentAlbum($band);
        $band->setLatestAlbum($latestAlbum);

        $errors = $validator->validate($band);
        if (count($errors) > 0) {
            $errorsMessage = (string) $errors;
            return $this->json(data: ['message' => $errorsMessage], status: 400);
        }

        $bandRepository->save($band, true);
        return $this->json(data: self::BAND_CREATED_MESSAGE, status: 201);
    }

    /** delete a band */
    #[Route(path: '/band', methods: 'delete')]
    public function delete(
        Request $request,
        BandRepository $bandRepository,
    )
    {
        $data = $request->toArray();
        $bandName = $data['name'];
        $band = $bandRepository->findOneBy(['name' => $bandName]);
        $bandRepository->remove($band, true);
        return $this->json(['message' => self::BAND_DELETED_MESSAGE]);
    }
}
