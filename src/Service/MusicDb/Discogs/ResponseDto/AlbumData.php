<?php

namespace App\Service\MusicDb\Discogs\ResponseDto;

use Symfony\Component\Serializer\Annotation\SerializedName;

class AlbumData
{
    #[SerializedName("released")]
    public string $releaseDate;
    public string $title;
}