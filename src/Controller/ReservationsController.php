<?php

namespace App\Controller;

use App\Entity\Reservations;
use App\Form\ReservationsType;
use App\Repository\ReservationsRepository;



use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservations')]
final class ReservationsController extends AbstractController
{
    #[Route(name: 'app_reservations_index', methods: ['GET'])]
    public function index(ReservationsRepository $reservationsRepository): Response
    {
        return $this->render('reservations/index.html.twig', [
            'reservations' => $reservationsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservations();
        $form = $this->createForm(ReservationsType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservations/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservations_show', methods: ['GET'])]
    public function show(Reservations $reservation): Response
    {
        return $this->render('reservations/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservations $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationsType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservations/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    /**
     * @throws PusherException
     * @throws GuzzleException
     * @throws ApiErrorException
     */
    #[Route('/{id}/confirm', name: 'app_reservations_confirm', methods: ['GET'])]
    public function confirm(Reservations $reservation, EntityManagerInterface $entityManager): Response
    {
        // Met à jour le statut à "confirmed" si la réservation est en attente (optionnel: vérifier le statut actuel)
        if ($reservation->getStatus() === 'pending' || 1==1) {
            $oldStatus = $reservation->getStatus();
            $reservation->setStatus('confirmed');
            $entityManager->flush();

            // Configure Pusher
            $options = array(
                'cluster' => 'eu',
                'useTLS' => true
            );
            $pusher = new Pusher(
                '58993209563f20fc12d9',
                '217d71865e8574a13f50',
                '1977733',
                $options
            );

            // Create meaningful notification data
            $formationTitle = $reservation->getFormation()->getTitle();
            $data = [
                'title' => 'Réservation confirmée',
                'message' => "Votre réservation pour '{$formationTitle}' a été confirmée !",
                'type' => 'success',
                'reservation_id' => $reservation->getId(),
                'formation_id' => $reservation->getFormation()->getId(),
                'old_status' => $oldStatus,
                'new_status' => 'confirmed'
            ];

            // Trigger notification to user's channel
            $userId = $reservation->getUser()->getId();
            $pusher->trigger('my-channel', 'my-event', $data);

            // Also trigger to user-specific channel if needed
            $pusher->trigger("user-{$userId}-channel", 'reservation-status-change', $data);
        }

        return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @throws PusherException
     * @throws ApiErrorException
     * @throws GuzzleException
     */
    #[Route('/{id}/cancel', name: 'app_reservations_cancel', methods: ['GET'])]
    public function cancel(Reservations $reservation, EntityManagerInterface $entityManager): Response
    {
        // Met à jour le statut à "cancelled" si la réservation est en attente (optionnel: vérifier le statut actuel)
        if ($reservation->getStatus() === 'pending'|| 1==1) {
            $oldStatus = $reservation->getStatus();
            $reservation->setStatus('cancelled');
            $entityManager->flush();

            // Configure Pusher
            $options = array(
                'cluster' => 'eu',
                'useTLS' => true
            );
            $pusher = new Pusher(
                '58993209563f20fc12d9',
                '217d71865e8574a13f50',
                '1977733',
                $options
            );

            // Create meaningful notification data
            $formationTitle = $reservation->getFormation()->getTitle();
            $data = [
                'title' => 'Réservation annulée',
                'message' => "Votre réservation pour '{$formationTitle}' a été annulée.",
                'type' => 'danger',
                'reservation_id' => $reservation->getId(),
                'formation_id' => $reservation->getFormation()->getId(),
                'old_status' => $oldStatus,
                'new_status' => 'cancelled'
            ];

            // Trigger notification to user's channel
            $userId = $reservation->getUser()->getId();
            $pusher->trigger('my-channel', 'my-event', $data);

            // Also trigger to user-specific channel if needed
            $pusher->trigger("user-{$userId}-channel", 'reservation-status-change', $data);
        }

        return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_reservations_delete', methods: ['POST'])]
    public function delete(Request $request, Reservations $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/mes-reservations', name: 'app_reservations_user')]
    public function userReservations(ReservationsRepository $repo): Response
    {
        // Récupère l'utilisateur authentifié
        $user = $this->getUser();
        if (!$user) {
            // redirige vers la page de login si besoin
            return $this->redirectToRoute('app_login');
        }

        // Cherche ses réservations les plus récentes en premier
        $reservations = $repo->findBy(
            ['user' => $user],
            ['reservationDate' => 'DESC']
        );

        return $this->render('reservations/user_reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    public function testNotification(NotificationServer $notificationServer): Response
    {
        // This method will trigger a test message to all connected clients
        $notificationServer->sendNotification("Test message");

        return new Response('Notification sent');
    }

}

