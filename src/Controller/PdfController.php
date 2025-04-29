<?php

// src/Controller/PdfController.php
namespace App\Controller;

use App\Entity\Sponsor;
use Doctrine\ORM\EntityManagerInterface;


use App\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    #[Route('/generate-pdf/{id}', name: 'generate_pdf')]
    public function generate(int $id, EntityManagerInterface $em, PdfGenerator $pdfGenerator): Response
    {
        $sponsor = $em->getRepository(Sponsor::class)->find($id);
    
        if (!$sponsor) {
            throw $this->createNotFoundException('Sponsor non trouvé.');
        }
    
        $html = $this->renderView('pdf/index.html.twig', [
            'sponsor' => $sponsor,
        ]);
    
        return new Response(
            $pdfGenerator->generate($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="contrat_' . $sponsor->getNom() . '.pdf"'
            ]
        );
    }
}
