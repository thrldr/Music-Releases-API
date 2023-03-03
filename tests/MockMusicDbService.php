<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\Band;
use App\Service\MusicDb\MusicDbServiceInterface;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: "dev")]
class MockMusicDbService implements MusicDbServiceInterface
{
    const MOCK_BANDS = ["Burzum", "Aphex Twin", "Miles Davis", "Black Sabbath"];
    const MOCK_ALBUMS = ["Filosofem", "Drukqs", "Kind Of Blue", "Paranoid"];

    public function bandInDb(Band $band): bool
    {
        return in_array($band->getName(), self::MOCK_BANDS);
    }

    /** retrieves the latest release by a band */
    public function getMostRecentAlbum(Band $band): ?Album
    {
        $albumIndex = random_int(0, count(self::MOCK_ALBUMS) - 1);
        $albumName = self::MOCK_ALBUMS[$albumIndex];
        return new Album($albumName, new \DateTime(), random_int(30, 100));
    }
}
