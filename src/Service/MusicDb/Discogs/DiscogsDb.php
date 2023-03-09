<?php

namespace App\Service\MusicDb\Discogs;

use App\Entity\Album;
use App\Service\MusicDb\Discogs\ResponseDto\AlbumData;
use App\Service\MusicDb\Discogs\ResponseDto\AlbumDataUrl;
use App\Service\MusicDb\Discogs\ResponseDto\BandMatch;
use App\Service\MusicDb\MusicDbServiceInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscogsDb implements MusicDbServiceInterface
{
    const GET = 'GET';
    const JSON = 'json';
    const BASE_URL = 'https://api.discogs.com';

    public function __construct(
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        private string              $apiKey,
        private string              $apiSecret,
    )
    {
    }

    public function getLatestAlbum(string $bandApiId): ?Album
    {
        $latestReleaseUrl = $this->getLatestAlbumUrl($bandApiId);
        $response = $this->requestData($latestReleaseUrl);

        /** @var AlbumData $data */
        $albumDto = $this->serializer->deserialize($response, AlbumData::class, self::JSON);

        $album = $this->makeAlbum($albumDto);
        return $album;
    }

    private function makeAlbum(AlbumData $albumData): Album
    {
        $album = new Album($albumData->title);
        $releaseDate = date_create($albumData->releaseDate);
        $album->setReleaseDate($releaseDate);

        return $album;
    }

    private function getLatestAlbumUrl(string $bandApiId): string
    {
        $endpoint = self::BASE_URL . "/artists/" . $bandApiId . "/releases";
        $parameters = [
            "sort"       => "year",
            "sort_order" => "desc",
            "per_page"   => 1,
        ];

        $response = $this->requestData($endpoint, $parameters);

        /** @var AlbumDataUrl $releaseInfoUrl */
        $releaseInfoUrl = $this->serializer->deserialize($response, AlbumDataUrl::class, self::JSON);

        return $releaseInfoUrl->url;
    }

    public function getBandServiceId(string $bandName): string
    {
        $endpoint = self::BASE_URL . "/database/search";
        $parameters = [
            "type"     => "artist",
            "query"    => $bandName,
            "per_page" => 1,
        ];

        $response = $this->requestData($endpoint, $parameters);
        /** @var BandMatch $bandMatch */
        $bandMatch = $this->serializer->deserialize($response, BandMatch::class, self::JSON);

        return (string) $bandMatch->id;
    }

    private function requestData(string $endpoint, array $extraParameters = []): string
    {
        $baseParameters = [
            "key"    => $this->apiKey,
            "secret" => $this->apiSecret,
        ];

        $response = $this->httpClient->request(
            self::GET,
            $endpoint,
            ["query" => array_merge($baseParameters, $extraParameters)],
        );

        return $response->getContent();
    }
}