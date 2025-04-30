<?php

namespace App\Controller;

<<<<<<< HEAD
=======
use App\Repository\EvenementRepository;
>>>>>>> origin/gestions-formations
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvenementsController extends AbstractController
{
    #[Route('/evenements', name: 'app_evenements')]
<<<<<<< HEAD
    public function index(): Response
    {
        return $this->render('evenements/index.html.twig', [
            'controller_name' => 'EvenementsController',
        ]);
    }
}
=======
    public function index(EvenementRepository $evenementRepository): Response
    {
        // Récupère tous les événements
        $evenements = $evenementRepository->findAll();
        
        return $this->render('evenements/liste.html.twig', [
            'evenements' => $evenements, // Envoie les événements au template
        ]);
    }
}
>>>>>>> origin/gestions-formations
