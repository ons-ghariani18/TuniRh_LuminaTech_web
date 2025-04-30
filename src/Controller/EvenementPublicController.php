<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementPublicController extends AbstractController
{
    #[Route('/evenement/detail/{id}', name: 'app_evenement_public_detail')]
    public function detail(Evenement $evenement = null): Response
    {
        // Vérifier si l'événement existe
        if (!$evenement) {
            throw $this->createNotFoundException('Événement non trouvé');
        }
        
        return $this->render('evenement_public/detail.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}