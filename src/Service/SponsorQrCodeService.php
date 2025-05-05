<?php

namespace App\Service;

use App\Entity\Sponsor;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;

use Endroid\QrCode\RoundBlockSizeMode;

use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;

class SponsorQrCodeService
{
    private string $websiteBaseUrl;
    private string $logoDirectory;
    
    public function __construct(
        string $websiteBaseUrl = 'https://example.com',
        string $logoDirectory = 'uploads/sponsor/logos/'
    ) {
        $this->websiteBaseUrl = $websiteBaseUrl;
        $this->logoDirectory = $logoDirectory;
    }
    
    /**
     * Génère un QR code personnalisé pour un sponsor
     */
    public function generateQrCodeForSponsor(Sponsor $sponsor, array $options = [])
    {
        // L'URL ou le contenu qui sera encodé dans le QR code
        $sponsorUrl = $options['url'] ?? ($this->websiteBaseUrl . '/sponsor/' . $sponsor->getIdSp());
    
        
        // Options de personnalisation avec valeurs par défaut
        $size = $options['size'] ?? 300;
        $margin = $options['margin'] ?? 10;
        
        // Couleurs personnalisées (avec couleurs par défaut)
        $fgColor = $this->createColorFromHex($options['foregroundColor'] ?? '#601e3e');
        $bgColor = $this->createColorFromHex($options['backgroundColor'] ?? '#ffffff');
        $labelColor = $this->createColorFromHex($options['labelColor'] ?? '#601e3e');
        
        // Configuration du texte du label
        $labelText = $options['labelText'] ?? $sponsor->getNom();
        $labelFontSize = $options['labelFontSize'] ?? 12;

        $qrCode = new QrCode(
            data: '$sponsorUrl',
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        // Writer pour générer l'image
        $writer = new PngWriter();
        
        // Préparation du logo (si nécessaire)
        $logo = null;
        if ($sponsor->hasLogo() && (!isset($options['includeLogo']) || $options['includeLogo'] === true)) {
            $logoPath = $this->logoDirectory . $sponsor->getLogoFilename();
           
        } elseif (isset($options['logoPath']) && file_exists($options['logoPath'])) {
            $logo = new Logo(
                path: $options['logoPath'],
                resizeToWidth: intval($size / 4),
                resizeToHeight: intval($size / 4),
                punchoutBackground: true
            );
        }
        
        // Préparation du label (si nécessaire)
        $label = null;
        if ($labelText) {
            $label = new Label(
                text: $labelText,
                font: new OpenSans($labelFontSize),
                textColor: $labelColor
            );
        }
        
        // Génération du QR code avec logo et label
        return $writer->write($qrCode, $logo, $label);
    }
    
    /**
     * Génère un QR code et renvoie son Data URI
     */
    public function generateQrCodeDataUri(Sponsor $sponsor, array $options = []): string
    {
        $result = $this->generateQrCodeForSponsor($sponsor, $options);
        return $result->getDataUri();
    }
    
    /**
     * Génère un QR code et renvoie une réponse HTTP avec l'image
     */
    public function generateQrCodeResponse(Sponsor $sponsor, array $options = []): Response
    {
        $result = $this->generateQrCodeForSponsor($sponsor, $options);
        
        // Créer une réponse avec le contenu de l'image
        $response = new Response($result->getString());
        $response->headers->set('Content-Type', $result->getMimeType());
        $response->headers->set(
            'Content-Disposition', 
            'inline; filename="sponsor-qrcode-' . $sponsor->getIdSp() . '.png"'
        );
        
        return $response;
    }
    
    /**
     * Crée un objet Color à partir d'une valeur hexadécimale
     */
    private function createColorFromHex(string $hex): Color
    {
        // Supprimer le # si présent
        $hex = ltrim($hex, '#');
        
        // Convertir la valeur hexadécimale en RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return new Color($r, $g, $b);
    }
}