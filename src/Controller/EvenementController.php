<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Form\EvenementSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\EvenementRepository;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(Request $request, EvenementRepository $evenementRepository): Response
    {
        // Récupération des paramètres de tri
     // Ligne 19-20
$sortField = $request->query->get('sort', 'dateDebut'); // Utilisez le même format que votre entité
$sortDirection = $request->query->get('direction', 'ASC');
        // Validation de la direction du tri
        if (!in_array($sortDirection, ['ASC', 'DESC'])) {
            $sortDirection = 'ASC';
        }
        
        $searchForm = $this->createForm(EvenementSearchType::class);
        $searchForm->handleRequest($request);

        $evenements = [];
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchData = $searchForm->getData();
            $evenements = $evenementRepository->search($searchData, $sortField, $sortDirection);
        } else {
            $evenements = $evenementRepository->findBySearchCriteria([], $sortField, $sortDirection);
        }

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'searchForm' => $searchForm->createView(),
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $imageFile = $form->get('imageFile')->getData();

                if ($imageFile) {
                    $uploadResult = $this->handleImageUpload($imageFile, $evenement, $slugger);
                    if (!$uploadResult) {
                        return $this->redirectToRoute('app_evenement_new');
                    }
                }

                $entityManager->persist($evenement);
                $entityManager->flush();

                $this->addFlash('success', 'L\'événement a été créé avec succès');
                return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire');
            }
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_ev}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id_ev}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $imageFile = $form->get('imageFile')->getData();

                if ($imageFile) {
                    if ($evenement->getImage()) {
                        $this->removeExistingImage($evenement->getImage());
                    }
                    
                    $uploadResult = $this->handleImageUpload($imageFile, $evenement, $slugger);
                    if (!$uploadResult) {
                        return $this->redirectToRoute('app_evenement_edit', ['id_ev' => $evenement->getIdEv()]);
                    }
                }

                $entityManager->flush();

                $this->addFlash('success', 'L\'événement a été modifié avec succès');
                return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire');
            }
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_ev}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIdEv(), $request->getPayload()->get('_token'))) {
            if ($evenement->getImage()) {
                $this->removeExistingImage($evenement->getImage());
            }
            
            $entityManager->remove($evenement);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'événement a été supprimé avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
    
    private function handleImageUpload(UploadedFile $imageFile, Evenement $evenement, SluggerInterface $slugger): bool
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
        
        try {
            $imageFile->move(
                $this->getParameter('evenements_images_directory'),
                $newFilename
            );
            
            $evenement->setImage($newFilename);
            return true;
        } catch (FileException $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
            return false;
        }
    }
    
    private function removeExistingImage(string $filename): void
    {
        $imagePath = $this->getParameter('evenements_images_directory').'/'.$filename;
        if (file_exists($imagePath)) {
            try {
                unlink($imagePath);
            } catch (\Exception $e) {
                $this->addFlash('warning', 'L\'image n\'a pas pu être supprimée');
            }
        }
    }
}