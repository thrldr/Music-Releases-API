<?php

namespace App\Service\MusicDb;

use App\Entity\Album;
use App\Entity\Band;

interface MusicDbServiceInterface
{
    public function getBandServiceId(string $name): string;

    /** retrieves the latest release by a band */
    public function getLatestAlbum(string $bandApiId): ?Album;
}