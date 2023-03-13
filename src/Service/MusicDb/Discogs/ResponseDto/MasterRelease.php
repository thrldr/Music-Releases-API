<?php

namespace App\Service\MusicDb\Discogs\ResponseDto;

use Symfony\Component\Serializer\Annotation\SerializedPath;

class MasterRelease
{
    #[SerializedPath('[releases][0][main_release]')]
    public ?int $mainReleaseId;

    #[SerializedPath('[releases][0][resource_url]')]
    public ?string $mainReleaseUrl;
}