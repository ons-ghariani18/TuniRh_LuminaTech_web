<?php

// src/Service/WeatherService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class WeatherService
{
    private string $apiKey;
    private HttpClientInterface $client;
    private string $baseUrl = 'http://api.openweathermap.org/data/2.5/weather';
    private FilesystemAdapter $cache;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->cache = new FilesystemAdapter();
    }

    public function getWeather(string $city)
    {
        $cacheKey = md5('weather_'.$city);
        
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($city) {
            $item->expiresAfter(3600); // Cache pour 1 heure
            
            $response = $this->client->request('GET', $this->baseUrl, [
                'query' => [
                    'q' => $city.',TN',
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'fr'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur API Météo: '.$response->getContent(false));
            }

            return $response->toArray();
        });
    }
}