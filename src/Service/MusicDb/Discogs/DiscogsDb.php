<?php

namespace App\Service\MusicDb\Discogs;

use App\Service\MusicDb\MusicDbServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DiscogsDb implements MusicDbServiceInterface
{
    const GET = "GET";

    public function __construct(
        private HttpClientInterface $httpClient,
        private string              $apiKey,
        private string              $apiSecret,
        private string              $baseUrl = "https://api.discogs.com",
    )
    {
    }

    //  "https://api.discogs.com/artists/395130/releases
    //   ?sort=year&sort_order=desc&per_page=1&
    //   key=SHURDELOvxTcQqTuxtnw&secret=iAenJDGMGvvNkJfDCAJvxvqBZmEtddoQ"
    // --user-agent "MusicalReleasesUpdater/0.1 (+thrldr@mail.ru)"
    public function getLatestAlbum(string $bandApiId)
    {
        $endpoint = "/artists/" . $bandApiId . "/releases";
        $parameters = [
            "sort"      => "year",
            "sort_orer" => "desc",
            "per_page"  => 1,
        ];

        $response = $this->requestData($endpoint, $parameters);
        $responseArray = $response->toArray();
        $releaseId = $responseArray["releases"][0]["id"];

        $response = $this->requestData("/releases/" . $releaseId);
        $bandData = $response->toArray();
        return $bandData["date_added"];
    }

    public function getBandServiceId(string $bandName): string
    {
        $endpoint = "/database/search";
        $parameters = [
            "type"     => "artist",
            "query"    => $bandName,
            "per_page" => 1,
        ];

        $response = $this->requestData($endpoint, $parameters);
        /** @var BandSearchResponse $responseObject */
        $responseObject = json_decode($response->getContent());

        $firstMatch = $responseObject->results[0];
        return $firstMatch->id;
    }

    private function requestData(string $endpoint, array $extraParameters = []): ResponseInterface
    {
        $baseParameters = [
            "key"    => $this->apiKey,
            "secret" => $this->apiSecret,
        ];

        $response = $this->httpClient->request(
            self::GET,
            $this->baseUrl . $endpoint,
            ["query" => array_merge($baseParameters, $extraParameters)],
        );

        return $response;
    }
}