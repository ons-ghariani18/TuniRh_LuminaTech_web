<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvenementDetailController extends AbstractController
{
    #[Route('/evenement/detail', name: 'app_evenement_detail')]
    public function index(): Response
    {
        return $this->render('evenement_detail/index.html.twig', [
            'controller_name' => 'EvenementDetailController',
        ]);
    }
}
