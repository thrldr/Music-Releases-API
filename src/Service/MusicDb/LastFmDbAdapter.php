<?php

namespace App\Service\MusicDb;

use App\Entity\Album;
use App\Entity\Band;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LastFmDbAdapter implements MusicDbServiceInterface
{
    const API_URL = "http://ws.audioscrobbler.com/2.0/";
    public function __construct(
        private string $apiKey,
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function bandInDb(Band $band): bool
    {
        $response = $this->httpClient->request(
            "GET",
            self::API_URL,
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
            self::API_URL,
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