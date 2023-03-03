<?php

namespace App\Service\MusicDb;

use App\Entity\Album;
use App\Entity\Band;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LastFmDbAdapter implements MusicDbServiceInterface
{
    public function __construct(
        private string $apiKey,
        private string $apiUrl,
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function bandInDb(Band $band): bool
    {
        $response = $this->httpClient->request(
            "GET",
            $this->apiUrl,
            ["query" => [
                "method"  => "artist.search",
                "artist"  => $band->getName(),
                "limit"   => "1",
                "api_key" => $this->apiKey,
                "format"  => "json",
            ]]
        )->toArray();

        $closestBandName = $response["results"]["artistmatches"]["artist"][0]["name"];
        return $closestBandName === $band->getName();
    }

    public function getMostRecentAlbum(Band $band): ?Album
    {
        $lastestAlbums = $this->httpClient->request(
            "GET",
            $this->apiUrl,
            ["query" => [
                "method"  => "artist.getTopAlbums",
                "artist"  => $band->getName(),
                "order"   => "release_date",
                "limit"   => "1",
                "api_key" => $this->apiKey,
                "format"  => "json",
            ]],
        )->toArray();

        return $lastestAlbums[0];
    }
}