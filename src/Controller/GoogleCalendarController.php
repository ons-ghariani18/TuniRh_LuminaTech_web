<?php

namespace App\Controller;

use App\Service\GoogleCalendarService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

final class GoogleCalendarController extends AbstractController
{
    public function __construct(
        private GoogleCalendarService $googleCalendarService,
        private RequestStack $requestStack,
        private LoggerInterface $logger
    ) {}

    #[Route('/google/calendar', name: 'app_google_calendar')]
    public function index(): Response
    {
        $client = $this->googleCalendarService->getClient();
        $this->logger->debug('Checking Google access token');

        if (!$client->getAccessToken()) {
            $this->logger->warning('No access token found, redirecting to auth');
            return $this->redirectToRoute('google_auth');
        }

        try {
            $this->logger->info('Fetching calendar events');
            $events = $this->googleCalendarService->listEvents('primary');
            
            $this->logger->debug('Successfully fetched events', [
                'event_count' => count($events)
            ]);
            
            return $this->render('google_calendar/index.html.twig', [
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Calendar error: '.$e->getMessage(), [
                'exception' => $e
            ]);
            $this->addFlash('error', 'Erreur: '.$e->getMessage());
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/google/auth', name: 'google_auth')]
    public function googleAuth(): Response
    {
        try {
            $authUrl = $this->googleCalendarService->getAuthUrl();
            $this->logger->debug('Generated auth URL', ['url' => $authUrl]);
            return $this->redirect($authUrl);
        } catch (\Exception $e) {
            $this->logger->error('Auth URL generation failed: '.$e->getMessage());
            $this->addFlash('error', 'Erreur d\'authentification: '.$e->getMessage());
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/google/callback', name: 'google_callback')]
    public function callback(Request $request): Response
    {
        $this->logger->info('Google callback initiated');
        
        if ($request->query->has('error')) {
            $error = $request->query->get('error');
            $this->logger->error('Google auth error', ['error' => $error]);
            $this->addFlash('error', 'Erreur Google: '.$error);
            return $this->redirectToRoute('app_home');
        }

        $code = $request->query->get('code');
        $this->logger->debug('Received auth code', ['code' => $code]);

        if (!$code) {
            $this->logger->error('Missing authorization code');
            $this->addFlash('error', 'Code d\'autorisation manquant');
            return $this->redirectToRoute('app_home');
        }

        try {
            $this->logger->info('Processing authorization code');
            $token = $this->googleCalendarService->handleCallback($code);
            
            $this->logger->debug('Token data', [
                'access_token' => !empty($token['access_token']),
                'expires_in' => $token['expires_in'] ?? null,
                'refresh_token' => !empty($token['refresh_token'])
            ]);
            
            $this->addFlash('success', 'Authentification Google réussie!');
            return $this->redirectToRoute('app_google_calendar');
        } catch (\Exception $e) {
            $this->logger->error('Callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addFlash('error', 'Erreur: '.$e->getMessage());
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/google/logout', name: 'google_logout')]
    public function logout(): Response
    {
        try {
            $this->googleCalendarService->logout();
            $this->logger->info('User logged out from Google');
            $this->addFlash('success', 'Déconnecté de Google Calendar');
        } catch (\Exception $e) {
            $this->logger->error('Logout failed', ['error' => $e->getMessage()]);
            $this->addFlash('error', 'Erreur lors de la déconnexion: '.$e->getMessage());
        }
        
        return $this->redirectToRoute('app_home');
    }
}