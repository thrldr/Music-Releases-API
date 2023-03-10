<?php

namespace App\Service\MusicDb\Discogs\ResponseDto;

use Symfony\Component\Serializer\Annotation\SerializedName;

class AlbumData
{
    #[SerializedName("released")]
    public null|string|int $releaseDate = null;

    public ?string $title = null;
}