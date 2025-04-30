<?php

namespace App\Controller;

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(): Response
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
use App\Entity\Evenement;
use App\Form\EvenementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
<<<<<<< HEAD
=======
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
>>>>>>> origin/gestions-formations

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager
            ->getRepository(Evenement::class)
            ->findAll();

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
<<<<<<< HEAD
    public function new(Request $request, EntityManagerInterface $entityManager): Response
=======
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
>>>>>>> origin/gestions-formations
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
<<<<<<< HEAD
            $entityManager->persist($evenement);
            $entityManager->flush();

=======
            $imageFile = $form->get('imageFile')->getData();

            // Traitement de l'upload d'image si une image est fournie
            if ($imageFile) {
                $this->handleImageUpload($imageFile, $evenement, $slugger);
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès');
>>>>>>> origin/gestions-formations
            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
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
<<<<<<< HEAD
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
=======
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
>>>>>>> origin/gestions-formations
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
<<<<<<< HEAD
            $entityManager->flush();

=======
            $imageFile = $form->get('imageFile')->getData();

            // Traitement de l'upload d'image si une nouvelle image est fournie
            if ($imageFile) {
                // Si l'événement a déjà une image, on la supprime avant d'en ajouter une nouvelle
                if ($evenement->getImage()) {
                    $this->removeExistingImage($evenement->getImage());
                }
                
                $this->handleImageUpload($imageFile, $evenement, $slugger);
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès');
>>>>>>> origin/gestions-formations
            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_ev}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIdEv(), $request->getPayload()->getString('_token'))) {
<<<<<<< HEAD
            $entityManager->remove($evenement);
            $entityManager->flush();
=======
            // Suppression de l'image associée si elle existe
            if ($evenement->getImage()) {
                $this->removeExistingImage($evenement->getImage());
            }
            
            $entityManager->remove($evenement);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'événement a été supprimé avec succès');
>>>>>>> origin/gestions-formations
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionUserReclamation
}
=======
    
    /**
     * Gère l'upload d'une image pour un événement
     */
    private function handleImageUpload(UploadedFile $imageFile, Evenement $evenement, SluggerInterface $slugger): void
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        // Sécuriser le nom du fichier
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
        
        try {
            $imageFile->move(
                $this->getParameter('evenements_images_directory'),
                $newFilename
            );
            
            $evenement->setImage($newFilename);
        } catch (FileException $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
        }
    }
    
    /**
     * Supprime une image existante
     */
    private function removeExistingImage(string $filename): void
    {
        $imagePath = $this->getParameter('evenements_images_directory').'/'.$filename;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
>>>>>>> origin/gestions-formations
