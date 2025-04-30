<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecrutementController extends AbstractController
{
    #[Route('/recrutement', name: 'app_recrutement')]
    public function index(): Response
    {
        return $this->render('recrutement/index.html.twig', [
            'controller_name' => 'RecrutementController',
        ]);
    }
}
