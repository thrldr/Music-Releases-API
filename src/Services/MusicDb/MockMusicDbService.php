<?php

namespace App\Services\MusicDb;

use App\Entity\Band;
use App\Entity\Album;

class MockMusicDbService implements MusicDbServiceInterface
{
    const MOCK_DB = ["Burzum", "Aphex Twin", "Miles Davis", "Black Sabbath"];

    public function bandNameInDb(string $name): bool
    {
        return in_array($name, self::MOCK_DB);
    }

    /** retrieves the latest release by a band */
    public function getMostRecentAlbum(Band $band): ?Album
    {
        return new Album("Filosofem", new \DateTime(), 50 * 60);
    }
}
