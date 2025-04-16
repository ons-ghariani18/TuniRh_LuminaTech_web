<?php

namespace App\Controller;

use App\Entity\Offresemploi;
use App\Form\OffresemploiType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offresemploi')]
final class OffresemploiController extends AbstractController
{
    #[Route(name: 'app_offresemploi_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Offresemploi::class);
        $allOffres = $repository->findAll(); // Récupérer toutes les offres

        // Paramètres de pagination
        $page = $request->query->getInt('page', 1); // Page actuelle, par défaut 1
        $limit = 5; // Nombre d'éléments par page (ajustable)
        $totalOffres = count($allOffres); // Nombre total d'offres
        $totalPages = max(1, ceil($totalOffres / $limit)); // Nombre total de pages
        $page = max(1, min($page, $totalPages)); // S'assurer que la page est valide

        // Pagination : Découper les résultats
        $offset = ($page - 1) * $limit;
        $offresemplois = array_slice($allOffres, $offset, $limit);

        return $this->render('offresemploi/index.html.twig', [
            'offresemplois' => $offresemplois,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/new', name: 'app_offresemploi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offresemploi = new Offresemploi();
        $form = $this->createForm(OffresemploiType::class, $offresemploi);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($offresemploi);
                $entityManager->flush();

                $this->addFlash('success', 'Offre d\'emploi créée avec succès !');
                return $this->redirectToRoute('app_offresemploi_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('offresemploi/new.html.twig', [
            'offresemploi' => $offresemploi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offresemploi_show', methods: ['GET'])]
    public function show(Offresemploi $offresemploi): Response
    {
        return $this->render('offresemploi/show.html.twig', [
            'offresemploi' => $offresemploi,
        ]);
    }

    #[Route('/{id_offre}/edit', name: 'app_offresemploi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offresemploi $offresemploi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffresemploiType::class, $offresemploi);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();

                $this->addFlash('success', 'Offre d\'emploi modifiée avec succès !');
                return $this->redirectToRoute('app_offresemploi_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('offresemploi/edit.html.twig', [
            'offresemploi' => $offresemploi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offresemploi_delete', methods: ['POST'])]
    public function delete(Request $request, Offresemploi $offresemploi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offresemploi->getIdOffre(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offresemploi);
            $entityManager->flush();

            $this->addFlash('success', 'Offre d\'emploi supprimée avec succès !');
        }

        return $this->redirectToRoute('app_offresemploi_index', [], Response::HTTP_SEE_OTHER);
    }
}