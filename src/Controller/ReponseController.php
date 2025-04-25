<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Reclamation;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reponse')]
final class ReponseController extends AbstractController
{
    #[Route(name: 'app_reponse_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('reponse/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $reclamationLocked = false;

        // Pré-sélectionner la réclamation
        $reclamationId = $request->query->get('reclamation');
        if ($reclamationId) {
            $reclamation = $entityManager->getRepository(Reclamation::class)->find($reclamationId);
            if (!$reclamation) {
                throw $this->createNotFoundException('Réclamation non trouvée pour l\'ID ' . $reclamationId);
            }
            $reponse->setIdReclamRep($reclamation);
            $reclamationLocked = true;
        }

        $form = $this->createForm(ReponseType::class, $reponse, [
            'reclamation_locked' => $reclamationLocked,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$reponse->getIdReclamRep()) {
                $this->addFlash('error', 'Veuillez sélectionner une réclamation.');
                return $this->render('reponse/new.html.twig', [
                    'reponse' => $reponse,
                    'form' => $form->createView(),
                ]);
            }

            $entityManager->persist($reponse);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Réponse enregistrée avec succès.');
                return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                // Ajouter cette ligne pour voir l'erreur complète dans les logs
                error_log('Erreur détaillée: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                $this->addFlash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
                // ...
            }
            
            
        }

        return $this->render('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form->createView(),
        ]);
    }
}