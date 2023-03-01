<?php

namespace App\Service\MusicDb;

use App\Entity\Band;
use App\Entity\Album;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: "dev")]
class MockMusicDbService implements MusicDbServiceInterface
{
    const MOCK_DB = ["Burzum", "Aphex Twin", "Miles Davis", "Black Sabbath"];

    public function bandInDb(Band $band): bool
    {
        return in_array($band->getName(), self::MOCK_DB);
    }

    /** retrieves the latest release by a band */
    public function getMostRecentAlbum(Band $band): ?Album
    {
        return new Album("Filosofem", new \DateTime(), 50 * 60);
    }
}
