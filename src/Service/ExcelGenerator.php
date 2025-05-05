<?php
// src/Service/ExcelGenerator.php
namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelGenerator
{
    public function generateSponsorsExcel(array $sponsors): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Type');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Téléphone');

        // Données
        $row = 2;
        foreach ($sponsors as $sponsor) {
            $sheet->setCellValue('A'.$row, $sponsor->getIdSp());
            $sheet->setCellValue('B'.$row, $sponsor->getNom());
            $sheet->setCellValue('C'.$row, $sponsor->getType());
            $sheet->setCellValue('D'.$row, $sponsor->getEmail());
            $sheet->setCellValue('E'.$row, $sponsor->getTelephone());
            $row++;
        }

        // Style
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Création de la réponse
        $writer = new Xlsx($spreadsheet);
        
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="sponsors.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}