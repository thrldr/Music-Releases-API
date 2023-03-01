<?php

namespace App\Service\MusicDb;

use App\Entity\Album;
use App\Entity\Band;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LastFmDbAdapter implements MusicDbServiceInterface
{
    private const API_KEY = "7614a44b9085a4fc167c51629b4399e4";

    public function __construct(
        private HttpClientInterface $httpClient,
    )
    {
//        $this->token = $this->requestToken();
    }

//    public function requestToken(): string
//    {
//        $tokenResponse = $this->httpClient->request(
//            "GET",
//            self::AUTH_URL,
//        );
//
//        if ($tokenResponse->getStatusCode() != Response::HTTP_OK) {
//            throw new \HttpException("Can not get a token to DB");
//        }
//
//        $tokenArray = $tokenResponse->toArray();
//        return $tokenArray["token"];
//    }

    public function test(string $name)
    {
        $lastestAlbums = $this->httpClient->request(
            "GET",
            "http://ws.audioscrobbler.com/2.0/",
            ["query" => [
                "method"  => "artist.getTopAlbums",
                "artist"  => $name,
                "order"   => "release_date",
                "limit"   => "1",
                "api_key" => self::API_KEY,
                "format"  => "json",
            ]],
        )->toArray();

        return $lastestAlbums;
    }

    public function bandInDb(Band $band): bool
    {
        $response = $this->httpClient->request(
            "GET",
            "http://ws.audioscrobbler.com/2.0/",
            ["query" => [
                "method"  => "artist.search",
                "artist"  => $band->getName(),
                "limit"   => "1",
                "api_key" => self::API_KEY,
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
            "http://ws.audioscrobbler.com/2.0/",
            ["query" => [
                "method"  => "artist.getTopAlbums",
                "artist"  => $band->getName(),
                "order"   => "release_date",
                "limit"   => "1",
                "api_key" => self::API_KEY,
                "format"  => "json",
            ]],
        )->toArray();

        return $lastestAlbums[0];
    }
}