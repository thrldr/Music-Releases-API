<?php

namespace App\Service\MusicDb;

use App\Entity\Album;
use App\Entity\Band;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @deprecated unfortunately last.fm api does not provide album release date information
 */
class LastFmDb implements MusicDbServiceInterface
{
    public function __construct(
        private string $apiKey,
        private string $apiUrl,
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function getBandServiceId(string $bandName): string
    {
        $response = $this->httpClient->request(
            "GET",
            $this->apiUrl,
            ["query" => [
                "method"  => "artist.search",
                "artist"  => $bandName,
                "limit"   => "1",
                "api_key" => $this->apiKey,
                "format"  => "json",
            ]]
        )->toArray();

        $closestBandName = $response["results"]["artistmatches"]["artist"][0]["name"];
        return $closestBandName === $bandName;
    }

    public function getMostRecentAlbum(Band $band)
    {
        $albumName = $this->getAlbumName($band->getName());
        return new Album($albumName, new \DateTime());
    }

    private function getAlbumName(string $name): string
    {
        $responseArray = $this->requestData([
            "method" => "artist.getTopAlbums",
            "artist"  => $name,
            "order"   => "release_date",
            "limit"   => "1",
        ]);

        return $responseArray["topalbums"]["album"]["0"]["name"];
    }

    private function getAlbumInfo(string $mbid): array
    {
        $responseArray = $this->requestData([
            "method" => "album.getInfo",
            "mbid" => $mbid,
        ]);

        return $responseArray["album"];
    }

    private function requestData(array $extraParameters): array
    {
        $baseParameters = [
            "api_key" => $this->apiKey,
            "format"  => "json",
        ];

        return $this->httpClient->request(
            "GET",
            $this->apiUrl,
            ["query" => array_merge($baseParameters, $extraParameters)],
        )->toArray();
    }

    private function getAlbumDuration(array $albumInfo)
    {
        $tracks = $albumInfo["tracks"];
    }
}