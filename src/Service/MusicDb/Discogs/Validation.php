<?php

namespace App\Service\MusicDb\Discogs;

class Validation
{
}

/**
 * @property-read BandMatch[] $results
 */
class BandSearchResponse {}

/**
 * @property-read string $title
 * @property-read int $id
 */
class BandMatch {}

/**
 * @property-read \DateTime $releaseDate
 * @property-read string $name
 */
class AlbumDataResponse {}
