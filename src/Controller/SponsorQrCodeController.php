<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Service\SponsorQrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sponsor/qr')]
final class SponsorQrCodeController extends AbstractController
{
    private SponsorQrCodeService $qrCodeService;
    
    public function __construct(SponsorQrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }
    
    #[Route('/{id_sp}/generate', name: 'app_sponsor_qr_generate', methods: ['GET'])]
    public function generate(
        Sponsor $sponsor,
        Request $request
    ): Response {
        // Récupérer les options de personnalisation depuis la requête
        $options = [
            'size' => (int) $request->query->get('size', 300),
            'margin' => (int) $request->query->get('margin', 10),
            'foregroundColor' => $request->query->get('fgColor', '#601e3e'),
            'backgroundColor' => $request->query->get('bgColor', '#ffffff'),
            'labelText' => $request->query->get('label', $sponsor->getName()),
            'labelFontSize' => (int) $request->query->get('fontSize', 12),
            'includeLogo' => $request->query->getBoolean('logo', true),
        ];
        
        // Déterminer si nous devons renvoyer l'image ou une réponse HTML
        $format = $request->query->get('format', 'html');
        
        if ($format === 'image') {
            return $this->qrCodeService->generateQrCodeResponse($sponsor, $options);
        }
        
        // Générer le QR code en Data URI pour l'affichage HTML
        $qrCodeDataUri = $this->qrCodeService->generateQrCodeDataUri($sponsor, $options);
        
        // Par défaut, renvoyer la vue HTML
        return $this->render('sponsor/qr_code.html.twig', [
            'sponsor' => $sponsor,
            'qrCode' => $qrCodeDataUri,
            'options' => $options
        ]);
    }
    
    #[Route('/customize', name: 'app_sponsor_qr_customize', methods: ['GET'])]
    public function customizeForm(): Response
    {
        // Renvoie un formulaire pour permettre aux utilisateurs de personnaliser les QR codes
        return $this->render('sponsor/qr_customize.html.twig');
    }
}