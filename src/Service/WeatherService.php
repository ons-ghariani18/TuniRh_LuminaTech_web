<?php
// src/Service/WeatherService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private string $apiKey;
    private HttpClientInterface $client;
    private string $baseUrl = 'http://api.openweathermap.org/data/2.5/weather';

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function getWeather(string $city)
    {
        $response = $this->client->request('GET', $this->baseUrl, [
            'query' => [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'fr'
            ]
        ]);

        // Traitez la réponse JSON ici
        return $response->toArray();  // Convertit la réponse en tableau associatif
    }
}