<?php

namespace App\Services\MusicDb;

use App\Entity\Album;
use App\Entity\Band;

interface MusicDbServiceInterface
{
    public function bandNameInDb(string $name): bool;

    /** retrieves the latest release by a band */
    public function getMostRecentAlbum(Band $band): ?Album;
}