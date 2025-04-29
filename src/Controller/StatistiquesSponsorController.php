<?php

namespace App\Controller;

use App\Repository\SponsorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class StatistiquesSponsorController extends AbstractController
{
    #[Route('/statistiques/sponsor', name: 'app_statistiques_sponsor')]
    public function index(SponsorRepository $sponsorRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le nombre total de sponsors
        $totalSponsors = $sponsorRepository->count([]);

        // Définir les types possibles
        $types = ['Financier', 'Matériel', 'Services', 'Médiatique'];

        // Initialiser le tableau pour Google Charts
        $data = [['Type', 'Nombre']];
        $typeCounts = [];

        // Compter le nombre de sponsors par type
        foreach ($types as $type) {
            $count = $sponsorRepository->count(['type' => $type]);
            $data[] = [$type, $count];
            $typeCounts[$type] = $count;
        }

        // Déterminer le type le plus fréquent
        $typePlusFrequent = 'Aucun';
        if (!empty($typeCounts)) {
            $typePlusFrequent = array_keys($typeCounts, max($typeCounts))[0];
        }

        // Récupérer le nombre de contrats générés
        // Hypothèse: un sponsor peut être lié à plusieurs contrats
        $contratsGeneres = 0;
        
        // Option 1: Si vous avez une méthode dans le repository
        // $contratsGeneres = $sponsorRepository->countContratsGeneres();
        
        // Option 2: Si vous avez une entité Contrat liée aux sponsors
        // $contratsGeneres = $entityManager->createQuery(
        //     'SELECT COUNT(c) FROM App\Entity\Contrat c WHERE c.sponsor IS NOT NULL'
        // )->getSingleScalarResult();
        
        // Option 3: Pour test, utiliser un nombre fixe
        $contratsGeneres = $totalSponsors * 2; // Exemple: 2 contrats par sponsor en moyenne

        // Préparer les données d'évolution temporelle
        // Format attendu: [['Mois', 'Nombre de sponsors'], ['Janvier', 42], ['Février', 56], ...]
        
        // Option 1: Utiliser des données réelles depuis la base de données
        // Exemple: récupérer le nombre de sponsors créés par mois
        /*
        $evolutionData = [['Mois', 'Nombre de sponsors']];
        $currentYear = date('Y');
        
        $query = $entityManager->createQuery(
            'SELECT MONTH(s.dateCreation) as month, COUNT(s) as count 
             FROM App\Entity\Sponsor s 
             WHERE YEAR(s.dateCreation) = :year 
             GROUP BY month 
             ORDER BY month ASC'
        )->setParameter('year', $currentYear);
        
        $results = $query->getResult();
        
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        
        foreach ($results as $result) {
            $evolutionData[] = [$months[$result['month']], (int)$result['count']];
        }
        */
        
        // Option 2: Pour test, utiliser des données statiques
        $evolutionData = [
            ['Mois', 'Nombre de sponsors'],
            ['Janvier', 10],
            ['Février', 15],
            ['Mars', 20],
            ['Avril', 25],
            ['Mai', 22],
            ['Juin', 30]
        ];


        $topSponsorsData = [
            ['Nom du Sponsor', 'Nombre de contrats'],
            ['Tunisair', 5],
            ['Ooredoo', 8],
            ['Banque Zitouna', 3],
            ['SFBT', 7],
        ];


        
        // 3. Add to the render parameters (this part remains the same)
        return $this->render('statistiques_sponsor/index.html.twig', [
            'chartData' => json_encode($data),
            'evolutionData' => json_encode($evolutionData),
            'topSponsorsData' => json_encode($topSponsorsData),
            'totalSponsors' => $totalSponsors,
            'typePlusFrequent' => $typePlusFrequent,
            'contratsGeneres' => $contratsGeneres,
        ]);
    }
}