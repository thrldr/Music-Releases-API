<?php

namespace App\Service\MusicDb;

use App\Entity\Album;
use App\Entity\Band;

interface RemoteMusicDbInterface
{
    public function getBandServiceId(string $bandName): string;

    /** retrieves the latest release by a band */
    public function getLatestAlbum(string $bandApiId): ?Album;
}