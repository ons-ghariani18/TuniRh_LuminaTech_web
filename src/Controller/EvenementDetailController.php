<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementDetailController extends AbstractController
{
    #[Route('/evenement/{id}', name: 'app_evenement_detail')]
    public function index(Evenement $evenement = null): Response
    {
        // Vérifier si l'événement existe
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }
        
        return $this->render('evenement_detail/index.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}