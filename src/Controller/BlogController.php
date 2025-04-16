<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Reservations;
use App\Entity\Users;
use App\Repository\FormationRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(FormationRepository $formationRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $reservations = [];
        if ($user) {
            // On force le chargement à jour des réservations via le Repository
            $reservations = $entityManager->getRepository(Reservations::class)->findBy(['user' => $user]);
        }

        return $this->render('blog/index.html.twig', [
            'formations'   => $formationRepository->findAll(),
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservation/add', name: 'add_reservation', methods: ['POST'])]
    public function addReservation(
        Request $request,
        EntityManagerInterface $entityManager,
        FormationRepository $formationRepository
    ): Response {
        $user = $this->getUser();
        $formationId = $request->request->get('formation_id');
        $formation = $formationRepository->find($formationId);

        if (!$user || !$formation) {
            return new Response('Utilisateur ou formation non trouvé', 404);
        }

        // Vérifier si une réservation existe déjà
        $existingReservation = $entityManager->getRepository(Reservations::class)->findOneBy([
            'user' => $user,
            'formation' => $formation,
        ]);

        if ($existingReservation) {
            return new Response('Vous êtes déjà inscrit à cette formation.', 400);
        }

        $reservation = new Reservations();
        $reservation->setUser($user);
        $reservation->setFormation($formation);
        $reservation->setReservationDate(new \DateTime());
        $reservation->setStatus('en attente');

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('app_blog');
    }

    #[Route('/reservation/remove', name: 'remove_reservation', methods: ['POST'])]
    public function removeReservation(
        Request $request,
        EntityManagerInterface $entityManager,
        FormationRepository $formationRepository
    ): Response {
        $user = $this->getUser();
        $formationId = $request->request->get('formation_id');
        $formation = $formationRepository->find($formationId);

        if (!$user || !$formation) {
            return new Response('Utilisateur ou formation non trouvé', 404);
        }

        $reservation = $entityManager->getRepository(Reservations::class)->findOneBy([
            'user' => $user,
            'formation' => $formation,
        ]);

        if (!$reservation) {
            return new Response('Aucune réservation trouvée pour cette formation.', 404);
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('app_blog');
    }
}
