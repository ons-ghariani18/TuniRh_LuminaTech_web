<?php
// src/Controller/MeetController.php
namespace App\Controller;

use App\Service\GoogleCalendarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/meet')]

class MeetController extends AbstractController
{
    #[Route('/', name: 'app_meet_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('meet/index.html.twig');
    }

    #[Route('/create', name: 'app_meet_create', methods: ['GET', 'POST'])]
    public function create(Request $request, GoogleCalendarService $googleCalendarService): Response
    {
        $meetUrl = null;
        $eventId = null;
        $errorMessage = null;
        
        if ($request->isMethod('POST')) {
            try {
                // Récupérer les données du formulaire
                $title = $request->request->get('title', 'Réunion avec sponsor');
                $description = $request->request->get('description', 'Discussion sur le partenariat');
                
                // Traiter les dates
                $startDate = new \DateTime($request->request->get('start_date'));
                $endDate = new \DateTime($request->request->get('end_date'));
                
                // Traiter les participants
                $attendees = [];
                $attendeesString = $request->request->get('attendees', '');
                if (!empty($attendeesString)) {
                    $attendees = array_map('trim', explode(',', $attendeesString));
                    $attendees = array_filter($attendees); // Supprime les entrées vides
                }
                
                // Créer l'événement avec Google Meet
                $result = $googleCalendarService->createMeetingWithUrl(
                    $title,
                    $description,
                    $startDate,
                    $endDate,
                    $attendees
                );
                
                if ($result['success']) {
                    $meetUrl = $result['meetUrl'];
                    $eventId = $result['eventId'];
                    $this->addFlash('success', 'Le lien Google Meet a été créé avec succès !');
                } else {
                    $errorMessage = $result['error'] ?? 'Une erreur est survenue lors de la création du Meet.';
                    $this->addFlash('error', $errorMessage);
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->addFlash('error', 'Erreur : ' . $errorMessage);
            }
        }
        
        return $this->render('meet/create.html.twig', [
            'meetUrl' => $meetUrl,
            'eventId' => $eventId,
            'error' => $errorMessage
        ]);
    }

    #[Route('/launch/{eventId}', name: 'app_meet_launch', methods: ['GET'])]
    public function launch(string $eventId, GoogleCalendarService $googleCalendarService): Response
    {
        try {
            $service = $googleCalendarService->getCalendarService();
            $event = $service->events->get('primary', $eventId);
            
            // Récupérer l'URL du Meet
            $meetUrl = $event->getHangoutLink();
            
            if (!$meetUrl && $event->getConferenceData()) {
                foreach ($event->getConferenceData()->getEntryPoints() as $entryPoint) {
                    if ($entryPoint->getEntryPointType() === 'video') {
                        $meetUrl = $entryPoint->getUri();
                        break;
                    }
                }
            }
            
            if ($meetUrl) {
                return $this->redirect($meetUrl);
            }
            
            $this->addFlash('error', 'Aucun lien Google Meet trouvé pour cet événement.');
            return $this->redirectToRoute('app_meet_index');
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération du lien Meet : ' . $e->getMessage());
            return $this->redirectToRoute('app_meet_index');
        }
    }

    #[Route('/cancel/{eventId}', name: 'app_meet_cancel', methods: ['POST'])]
    public function cancel(Request $request, string $eventId, GoogleCalendarService $googleCalendarService): Response
    {
        if (!$this->isCsrfTokenValid('cancel-meet-'.$eventId, $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_meet_index');
        }

        try {
            $googleCalendarService->getCalendarService()->events->delete('primary', $eventId);
            $this->addFlash('success', 'La réunion a été annulée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'annulation de la réunion : ' . $e->getMessage());
        }
        
        return $this->redirectToRoute('app_meet_index');
    }

    #[Route('/confirm-cancel/{eventId}', name: 'app_meet_confirm_cancel', methods: ['GET'])]
    public function confirmCancel(string $eventId): Response
    {
        return $this->render('meet/confirm_cancel.html.twig', [
            'eventId' => $eventId
        ]);
    }
}