<?php

namespace App\Controller;

use App\Entity\Offresemploi;
use App\Entity\Candidatures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('/stats', name: 'app_dashboard_stats', methods: ['GET'])]
    public function stats(EntityManagerInterface $entityManager): Response
    {
        // Requête DQL pour regrouper les candidatures par type_contrat
        $query = $entityManager->createQuery(
            'SELECT o.type_contrat, COUNT(c.id_candidature) as candidature_count
             FROM App\Entity\Offresemploi o
             LEFT JOIN App\Entity\Candidatures c WITH c.offre = o
             GROUP BY o.type_contrat'
        );
        $offres = $query->getResult();

        // Préparer les données pour Chart.js
        $labels = [];
        $data = [];
        $backgroundColors = [
            'CDI' => '#E76F51', // Orange
            'CDD' => '#28a745', // Vert
            'Freelance' => '#6c757d', // Gris
        ];

        $totalCandidatures = 0;
        foreach ($offres as $offre) {
            $typeContrat = $offre['type_contrat'] ?: 'Inconnu';
            $labels[] = $typeContrat;
            $data[] = (int) $offre['candidature_count']; // Assurer que c'est un entier
            $totalCandidatures += $offre['candidature_count'];
        }

        $chartBackgroundColors = array_map(fn($typeContrat) => $backgroundColors[$typeContrat] ?? '#1B263B', $labels);

        // Débogage avec dump() au lieu de logger
        dump([
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $chartBackgroundColors,
            'totalCandidatures' => $totalCandidatures,
        ]);

        return $this->render('dashboard/stats.html.twig', [
            'chartData' => [
                'labels' => $labels,
                'data' => $data,
                'backgroundColors' => $chartBackgroundColors,
            ],
            'totalCandidatures' => $totalCandidatures,
        ]);
    }
}