<?php

namespace App\Service\MusicDb;

use App\Entity\Album;
use App\Entity\Band;

interface MusicDbServiceInterface
{
    public function bandInDb(Band $name): bool;

    /** retrieves the latest release by a band */
    public function getMostRecentAlbum(Band $band): ?Album;
}