<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EvenementRepository;

final class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(EvenementRepository $evenementRepository): Response
    {
        // Récupérer tous les événements
        $evenements = $evenementRepository->findAll();
        
        // Formater les événements pour le calendrier
        $formattedEvents = [];
        foreach ($evenements as $event) {
            $formattedEvents[] = [
                'id' => $event->getIdEv(),
                'title' => $event->getNom(),
                'start' => $event->getDateDebut()->format('Y-m-d'),
                'end' => $event->getDateFin()->modify('+1 day')->format('Y-m-d'), // Ajoute 1 jour car FullCalendar exclut le dernier jour
                'description' => $event->getDescription(),
                'type' => $event->getType(),
                'lieu' => $event->getLieu(),
                'backgroundColor' => $this->getEventColor($event->getType()),
            ];
        }

        return $this->render('calendar/index.html.twig', [
            'events' => json_encode($formattedEvents),
        ]);
    }

    private function getEventColor(string $type): string
    {
        // Associer des couleurs en fonction du type d'événement
        $colors = [
            'Conférence' => '#3a87ad',
            'Atelier' => '#f89406',
            'Formation' => '#468847',
            'Compétition' => '#b94a48',
            'Exposition' => '#5bc0de',
            'default' => '#333333'
        ];

        return $colors[$type] ?? $colors['default'];
    }
}