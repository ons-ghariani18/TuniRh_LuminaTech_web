<?php

// src/Service/PdfGenerator.php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $this->dompdf = new Dompdf($options);
    }

    public function generate(string $html, string $paperSize = 'A4', string $orientation = 'portrait')
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($paperSize, $orientation);
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    public function stream(string $html, string $filename = 'document.pdf', string $paperSize = 'A4', string $orientation = 'portrait')
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($paperSize, $orientation);
        $this->dompdf->render();
        $this->dompdf->stream($filename);
    }
}
