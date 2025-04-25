<?php

namespace App\Controller;

use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupération des 3 prochains événements
        $evenements = $entityManager->getRepository(Evenement::class)
            ->createQueryBuilder('e')
            ->where('e.date_debut >= :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('e.date_debut', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }
    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return new Response('Test OK ✅');
    }


}