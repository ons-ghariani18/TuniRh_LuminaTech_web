<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\SponsorType;
use App\Form\SponsorSearchType;
use App\Repository\SponsorRepository;
use App\Service\SponsorQrCodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sponsor')]
final class SponsorController extends AbstractController
{
    #[Route(name: 'app_sponsor_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        SponsorRepository $sponsorRepository
    ): Response {
        // Création du formulaire de recherche
        $searchForm = $this->createForm(SponsorSearchType::class);
        $searchForm->handleRequest($request);

        // Paramètres de tri par défaut
        $sortField = $request->query->get('sort', 'id_sp');
        $sortDirection = $request->query->get('direction', 'asc');

        $sponsors = [];

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Récupération des critères de recherche
            $searchCriteria = $searchForm->getData();
            // Recherche avec les critères
            $sponsors = $sponsorRepository->search($searchCriteria, $sortField, $sortDirection);
        } else {
            // Récupération de tous les sponsors si pas de recherche
            $sponsors = $sponsorRepository->findBy([], [$sortField => $sortDirection]);
        }

        return $this->render('sponsor/index.html.twig', [
            'sponsors' => $sponsors,
            'searchForm' => $searchForm->createView(),
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/new', name: 'app_sponsor_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $sponsor = new Sponsor();
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($sponsor);
                $entityManager->flush();
                
                $this->addFlash('success', 'Sponsor créé avec succès');
                return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du sponsor');
            }
        }

        return $this->render('sponsor/new.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id_sp}', name: 'app_sponsor_show', methods: ['GET'])]
    public function show(Sponsor $sponsor, SponsorQrCodeService $qrCodeService): Response
    {
        // Générer le QR code pour ce sponsor
        $sponsorQrCode = $qrCodeService->generateQrCodeForSponsor($sponsor);
        $qrCodeDataUri = $qrCodeService->generateQrCodeDataUri($sponsor);
        
        return $this->render('sponsor/show.html.twig', [
            'sponsor' => $sponsor,
            'qrCode' => $qrCodeDataUri,
        ]);
    }

    #[Route('/{id_sp}/edit', name: 'app_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Sponsor $sponsor,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Sponsor mis à jour avec succès');
                return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du sponsor');
            }
        }

        return $this->render('sponsor/edit.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id_sp}', name: 'app_sponsor_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Sponsor $sponsor,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$sponsor->getIdsp(), $request->getPayload()->get('_token'))) {
            try {
                $entityManager->remove($sponsor);
                $entityManager->flush();
                $this->addFlash('success', 'Sponsor supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du sponsor');
            }
        }

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }

    public function exportExcel(ExcelGenerator $excelGenerator): StreamedResponse
{
    $sponsors = $this->getDoctrine()->getRepository(Sponsor::class)->findAll();
    return $excelGenerator->generateSponsorsExcel($sponsors);
}
}