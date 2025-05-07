<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PdfTestController extends AbstractController
{
    private Pdf $snappyPdf;
    private FormationRepository $formationRepo;

    public function __construct(Pdf $snappyPdf, FormationRepository $formationRepo)
    {
        $this->snappyPdf = $snappyPdf;
        $this->formationRepo = $formationRepo;
    }

    public function generate(int $formationId): Response
    {
        // Récupère les données
        $formation = $this->formationRepo->find($formationId);
        $userName = $this->getUser()->getFullName();  // ou comment tu obtiens le nom
        $date = new \DateTime();

        // Rendu HTML depuis Twig
        $html = $this->renderView('quiz/attestation.html.twig', [
            'formation' => $formation,
            'userName' => $userName,
            'date' => $date,
        ]);

        // Génère le PDF en sortie brute
        $pdfContent = $this->snappyPdf->getOutputFromHtml(
            $html,
            [],   // options additionnelles si besoin
        );

        // Retourne la réponse PDF
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="attestation-' . $formationId . '.pdf"',
        ]);
    }
}