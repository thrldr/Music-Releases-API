<?php

namespace App\Service\MusicDb\Discogs\ResponseDto;

use Symfony\Component\Serializer\Annotation\SerializedPath;

class BandMatch
{
    #[SerializedPath('[results][0][id]')]
    public int $id;
}