<?php

namespace App\Service\MusicDb\Discogs\ResponseDto;

use Symfony\Component\Serializer\Annotation\SerializedPath;

class AlbumDataUrl
{
    #[SerializedPath('[releases][0][resource_url]')]
    public string $url;
}