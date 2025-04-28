<?php

// src/Controller/WeatherController.php
namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    private $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    #[Route('/weather/{city}', name: 'app_weather')]
    public function index(string $city): Response
    {
        $weather = $this->weatherService->getWeather($city);

        return $this->render('weather/index.html.twig', [
            'weather' => $weather
        ]);
    }
}

