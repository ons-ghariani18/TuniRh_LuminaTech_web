<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvenementsController extends AbstractController
{
    #[Route('/evenements', name: 'app_evenements')]
    public function index(EvenementRepository $evenementRepository): Response
    {
        // Récupère tous les événements
        $evenements = $evenementRepository->findAll();
        
        return $this->render('evenements/liste.html.twig', [
            'evenements' => $evenements, // Envoie les événements au template
        ]);
    }
}