<?php

// src/Controller/PdfController.php
namespace App\Controller;

use App\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    #[Route('/generate-pdf', name: 'generate_pdf')]
    public function generate(PdfGenerator $pdfGenerator): Response
    {
        $html = '<h1>Hello from PDF service!</h1>';
        
        return new Response(
            $pdfGenerator->generate($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="myfile.pdf"'
            ]
        );
    }
}
