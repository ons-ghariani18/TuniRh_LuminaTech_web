<?php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class QrCodeController extends AbstractController
{
    #[Route('/qr/code', name: 'app_qr_code')]
    public function index(): Response
    {
        // 1. Créer le QR code directement
        $qrCode = new QrCode('https://www.symfony.com');

        // 2. Utiliser le Writer pour générer l'image
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // 3. Obtenir le Data URI
        $qrCodeDataUri = $result->getDataUri();

        // 4. Envoyer vers la vue Twig
        return $this->render('qr_code/index.html.twig', [
            'qrCode' => $qrCodeDataUri,
            'qrContent' => 'https://www.symfony.com',
        ]);
    }
}
