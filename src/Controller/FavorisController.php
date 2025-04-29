<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    #[Route('/favoris/toggle/{id}', name: 'app_favoris_toggle', methods: ['POST'])]
    public function toggleFavori(Evenement $evenement, EntityManagerInterface $em): JsonResponse
    {
        $evenement->setFavori(!$evenement->isFavori());
        $em->flush();

        return $this->json([
            'isFavori' => $evenement->isFavori(),
            'favorisCount' => $em->getRepository(Evenement::class)->count(['favori' => true])
        ]);
    }

    #[Route('/favoris', name: 'app_favoris_index')]
    public function index(EvenementRepository $repository): Response
    {
        $favoris = $repository->findBy(['favori' => true], ['dateDebut' => 'ASC']);

        return $this->render('favoris/index.html.twig', [
            'favoris' => $favoris,
        ]);
    }
}