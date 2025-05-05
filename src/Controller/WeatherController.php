<?php

// src/Controller/WeatherController.php
namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    private const TUNISIAN_REGIONS = [
        'Tunis' => 'Tunis',
        'Sfax' => 'Sfax',
        'Sousse' => 'Sousse',
        'Kairouan' => 'Kairouan',
        'Bizerte' => 'Bizerte',
        'Gabès' => 'Gabès',
        'Ariana' => 'Ariana',
        'Gafsa' => 'Gafsa',
        'Monastir' => 'Monastir',
        'Ben Arous' => 'Ben Arous',
        'Nabeul' => 'Nabeul',
        'Kasserine' => 'Kasserine',
        'Medenine' => 'Medenine',
        'Mahdia' => 'Mahdia',
        'Zaghouan' => 'Zaghouan',
        'Béja' => 'Béja',
        'Jendouba' => 'Jendouba',
        'Kébili' => 'Kébili',
        'Le Kef' => 'Le Kef',
        'Manouba' => 'Manouba',
        'Médenine' => 'Médenine',
        'Tataouine' => 'Tataouine',
        'Tozeur' => 'Tozeur',
        'Siliana' => 'Siliana'
    ];

    #[Route('/meteo', name: 'app_weather')]
    public function index(Request $request, WeatherService $weatherService): Response
    {
        $selectedRegion = $request->query->get('region', 'Tunis');
        $weatherData = null;
        $error = null;

        try {
            $weatherData = $weatherService->getWeather($selectedRegion);
        } catch (\Exception $e) {
            $error = 'Impossible de récupérer les données météo pour cette région.';
        }

        return $this->render('weather/index.html.twig', [
            'regions' => self::TUNISIAN_REGIONS,
            'selectedRegion' => $selectedRegion,
            'weather' => $weatherData,
            'error' => $error
        ]);
    }
}