<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\ExcelImportType;
use App\Service\ExcelGenerator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class SponorExcelController extends AbstractController
{
    #[Route('/sponsor/excel', name: 'app_sponsor_excel')]
    public function index(): Response
    {
        return $this->render('sponsor_excel/index.html.twig', [
            'controller_name' => 'SponorExcelController',
        ]);
    }

    #[Route('/sponsor/excel/export', name: 'app_sponsor_excel_export')]
    public function exportExcel(ExcelGenerator $excelGenerator, EntityManagerInterface $em): StreamedResponse
    {
        $sponsors = $em->getRepository(Sponsor::class)->findAll();
        return $excelGenerator->generateSponsorsExcel($sponsors);
    }

    #[Route('/sponsor/excel/import', name: 'app_sponsor_excel_import')]
    public function importExcel(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ExcelImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichierExcel = $form->get('fichier_excel')->getData();
            
            try {
                $spreadsheet = IOFactory::load($fichierExcel->getPathname());
                $sheet = $spreadsheet->getActiveSheet();
                
                $importedCount = 0;
                
                foreach ($sheet->getRowIterator(2) as $row) {
                    $nom = $sheet->getCell('B'.$row->getRowIndex())->getValue();
                    $type = $sheet->getCell('C'.$row->getRowIndex())->getValue();
                    
                    if (empty($nom)) continue;
                    
                    $sponsor = new Sponsor();
                    $sponsor->setNom($nom);
                    $sponsor->setType($type);
                    // Ajoutez d'autres champs selon votre structure
                    
                    $em->persist($sponsor);
                    $importedCount++;
                    
                    if ($importedCount % 20 === 0) {
                        $em->flush();
                        $em->clear();
                    }
                }
                
                $em->flush();
                
                $this->addFlash('success', sprintf('%d sponsors importés avec succès !', $importedCount));
                return $this->redirectToRoute('app_sponsor_excel');
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'import : '.$e->getMessage());
            }
        }

        return $this->render('sponsor_excel/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}