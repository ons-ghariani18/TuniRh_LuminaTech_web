<?php
// src/Service/GoogleCalendarService.php
namespace App\Service;

use Google\Client;
use Google\Service\Calendar;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GoogleCalendarService
{
    private Client $client;
    private string $tokenPath;
    private ?LoggerInterface $logger;
    private ?SessionInterface $session;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        LoggerInterface $logger = null,
        RequestStack $requestStack = null
    ) {
        $this->client = new Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setRedirectUri($redirectUri);
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        
        // Configuration crucial pour recevoir un refresh token
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');  // Force l'affichage du consentement pour obtenir un refresh token
        $this->client->setIncludeGrantedScopes(true);
        
        $this->tokenPath = __DIR__.'/../../var/google_token.json';
        $this->logger = $logger;
        $this->session = $requestStack?->getSession();
        
        // Charge le token existant s'il existe
        $this->loadExistingToken();
    }

    /**
     * Charge un token existant depuis le fichier de stockage ou la session
     */
    private function loadExistingToken(): void
    {
        // Essayer de charger depuis le fichier
        if (file_exists($this->tokenPath)) {
            $accessToken = json_decode(file_get_contents($this->tokenPath), true);
            if ($accessToken) {
                $this->client->setAccessToken($accessToken);
                return;
            }
        }
        
        // Sinon, essayer de charger depuis la session
        if ($this->session && $this->session->has('google_token')) {
            $accessToken = $this->session->get('google_token');
            if ($accessToken) {
                $this->client->setAccessToken($accessToken);
            }
        }
    }

    /**
     * Enregistre le token à la fois dans le fichier et dans la session
     */
    private function saveToken(array $accessToken): void
    {
        // Sauvegarder dans le fichier
        try {
            $tokenDir = dirname($this->tokenPath);
            if (!is_dir($tokenDir)) {
                mkdir($tokenDir, 0777, true);
            }
            file_put_contents($this->tokenPath, json_encode($accessToken));
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Failed to save token to file: ' . $e->getMessage());
            }
        }
        
        // Sauvegarder dans la session
        if ($this->session) {
            $this->session->set('google_token', $accessToken);
        }
    }

    /**
     * Génère l'URL d'authentification
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Échange le code d'autorisation contre un token d'accès
     */
    public function setAccessToken(string $code): void
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($accessToken['error'])) {
                throw new \RuntimeException('Error fetching access token: ' . $accessToken['error_description'] ?? $accessToken['error']);
            }
            
            $this->client->setAccessToken($accessToken);
            $this->saveToken($accessToken);
            
            // Vérifier qu'un refresh token a bien été obtenu
            if (!isset($accessToken['refresh_token'])) {
                if ($this->logger) {
                    $this->logger->warning('No refresh token was obtained. User may need to revoke access and reconnect.');
                }
            }
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error setting access token: ' . $e->getMessage());
            }
            throw new \RuntimeException('Failed to authenticate with Google: ' . $e->getMessage());
        }
    }

    /**
     * Vérifie si le token d'accès est expiré
     */
    public function isAccessTokenExpired(): bool
    {
        return $this->client->isAccessTokenExpired();
    }

    /**
     * Raffraîchit le token si nécessaire
     * @throws \RuntimeException si aucun refresh token n'est disponible
     */
    public function refreshToken(): void
    {
        $refreshToken = $this->client->getRefreshToken();
        
        if (!$refreshToken) {
            // Essayer de récupérer le refresh token depuis le stockage
            if (file_exists($this->tokenPath)) {
                $storedToken = json_decode(file_get_contents($this->tokenPath), true);
                if (isset($storedToken['refresh_token'])) {
                    $refreshToken = $storedToken['refresh_token'];
                    $this->client->setRefreshToken($refreshToken);
                }
            }
        }
        
        if (!$refreshToken) {
            throw new \RuntimeException('No refresh token available. Please reconnect to Google Calendar.');
        }
        
        try {
            $accessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            
            if (isset($accessToken['error'])) {
                throw new \RuntimeException('Error refreshing token: ' . $accessToken['error_description'] ?? $accessToken['error']);
            }
            
            // Conserver le refresh token car Google ne le renvoie pas à chaque fois
            if (!isset($accessToken['refresh_token']) && $refreshToken) {
                $accessToken['refresh_token'] = $refreshToken;
            }
            
            $this->client->setAccessToken($accessToken);
            $this->saveToken($accessToken);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error refreshing token: ' . $e->getMessage());
            }
            throw new \RuntimeException('Failed to refresh Google authentication: ' . $e->getMessage());
        }
    }

    /**
     * S'assure que le token est valide avant d'appeler l'API
     * @throws \RuntimeException si l'authentification est requise
     */
    public function ensureValidToken(): void
    {
        if ($this->isAccessTokenExpired()) {
            try {
                $this->refreshToken();
            } catch (\RuntimeException $e) {
                // Journaliser l'erreur
                if ($this->logger) {
                    $this->logger->error('Token validation failed: ' . $e->getMessage());
                }
                throw new \RuntimeException('Authentication required: ' . $e->getMessage());
            }
        }
    }

    /**
     * Retourne une instance du service Calendar
     */
    public function getCalendarService(): Calendar
    {
        $this->ensureValidToken();
        return new Calendar($this->client);
    }

    /**
     * Récupère les événements d'un calendrier
     */
    public function getEvents(string $calendarId = 'primary', array $params = []): array
    {
        try {
            $service = $this->getCalendarService();
            return $service->events->listEvents($calendarId, $params)->getItems();
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error fetching calendar events: ' . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Crée un nouvel événement dans le calendrier
     */
    public function createEvent(array $eventData, string $calendarId = 'primary'): object
    {
        try {
            $service = $this->getCalendarService();
            $event = new \Google\Service\Calendar\Event($eventData);
            return $service->events->insert($calendarId, $event);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error creating calendar event: ' . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Met à jour un événement existant
     */
    public function updateEvent(string $eventId, array $eventData, string $calendarId = 'primary'): object
    {
        try {
            $service = $this->getCalendarService();
            $event = $service->events->get($calendarId, $eventId);
            
            // Mise à jour des champs simples
            if (isset($eventData['summary'])) {
                $event->setSummary($eventData['summary']);
            }
            if (isset($eventData['description'])) {
                $event->setDescription($eventData['description']);
            }
            if (isset($eventData['location'])) {
                $event->setLocation($eventData['location']);
            }
            
            // Gestion correcte des dates pour les événements sur toute la journée (all-day events)
            if (isset($eventData['start'])) {
                $start = new \Google\Service\Calendar\EventDateTime();
                if (isset($eventData['start']['date'])) {
                    // Pour les événements sur toute la journée
                    $start->setDate($eventData['start']['date']);
                } elseif (isset($eventData['start']['dateTime'])) {
                    // Pour les événements avec heure spécifique
                    $start->setDateTime($eventData['start']['dateTime']);
                }
                if (isset($eventData['start']['timeZone'])) {
                    $start->setTimeZone($eventData['start']['timeZone']);
                }
                $event->setStart($start);
            }
            
            if (isset($eventData['end'])) {
                $end = new \Google\Service\Calendar\EventDateTime();
                if (isset($eventData['end']['date'])) {
                    // Pour les événements sur toute la journée
                    $end->setDate($eventData['end']['date']);
                } elseif (isset($eventData['end']['dateTime'])) {
                    // Pour les événements avec heure spécifique
                    $end->setDateTime($eventData['end']['dateTime']);
                }
                if (isset($eventData['end']['timeZone'])) {
                    $end->setTimeZone($eventData['end']['timeZone']);
                }
                $event->setEnd($end);
            }
            
            // Mise à jour des rappels si fournis
            if (isset($eventData['reminders'])) {
                $reminders = new \Google\Service\Calendar\EventReminders();
                $reminders->setUseDefault($eventData['reminders']['useDefault']);
                if (isset($eventData['reminders']['overrides'])) {
                    $reminders->setOverrides($eventData['reminders']['overrides']);
                }
                $event->setReminders($reminders);
            }
            
            return $service->events->update($calendarId, $eventId, $event);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error updating calendar event: ' . $e->getMessage());
            }
            throw $e;
        }
    }
    
    /**
     * Correction facultative pour EvenementController - méthode prepareGoogleCalendarEventData 
     * (pour rendre le code plus explicite et faciliter le débogage)
     */
    private function prepareGoogleCalendarEventData(Evenement $evenement): array
    {
        // Convertir les dates au format attendu par Google Calendar
        $start = clone $evenement->getDateDebut();
        $end = clone $evenement->getDateFin();
        
        return [
            'summary' => $evenement->getNom(),
            'location' => $evenement->getLieu(),
            'description' => $evenement->getDescription() . "\n\nType: " . $evenement->getType() . "\nBudget: " . $evenement->getBudget() . " €",
            'start' => [
                // Utiliser explicitement 'date' pour les événements sur toute la journée
                'date' => $start->format('Y-m-d'),
                'timeZone' => 'Europe/Paris',
            ],
            'end' => [
                // Utiliser explicitement 'date' pour les événements sur toute la journée
                'date' => $end->format('Y-m-d'),
                'timeZone' => 'Europe/Paris',
            ],
            'reminders' => [
                'useDefault' => true,
            ],
        ];
    }

    public function createMeetingWithUrl(
        string $title,
        string $description,
        \DateTime $startDateTime,
        \DateTime $endDateTime,
        array $attendees = []
    ): array {
        try {
            $this->ensureValidToken();
            $service = $this->getCalendarService();
            
            // Préparation des données de l'événement
            $eventData = [
                'summary' => $title,
                'description' => $description,
                'start' => [
                    'dateTime' => $startDateTime->format('c'),
                    'timeZone' => 'Europe/Paris',
                ],
                'end' => [
                    'dateTime' => $endDateTime->format('c'),
                    'timeZone' => 'Europe/Paris',
                ],
            ];
            
            // Ajouter les participants
            if (!empty($attendees)) {
                $eventAttendees = [];
                foreach ($attendees as $email) {
                    $eventAttendees[] = ['email' => $email];
                }
                $eventData['attendees'] = $eventAttendees;
            }
            
            // Configuration pour Google Meet
            $conferenceData = new \Google\Service\Calendar\ConferenceData();
            $conferenceRequest = new \Google\Service\Calendar\CreateConferenceRequest();
            $conferenceRequest->setRequestId(md5($title . time()));
            $conferenceData->setCreateRequest($conferenceRequest);
            
            // Création de l'événement avec configuration Google Meet
            $event = new \Google\Service\Calendar\Event($eventData);
            $event->setConferenceData($conferenceData);
            
            $createdEvent = $service->events->insert('primary', $event, [
                'conferenceDataVersion' => 1,
                'sendUpdates' => 'all'  // Envoyer des invitations par email
            ]);
            
            // Récupérer l'URL Meet
            $meetUrl = null;
            if ($createdEvent->getConferenceData() && $createdEvent->getConferenceData()->getEntryPoints()) {
                foreach ($createdEvent->getConferenceData()->getEntryPoints() as $entryPoint) {
                    if ($entryPoint->getEntryPointType() === 'video') {
                        $meetUrl = $entryPoint->getUri();
                        break;
                    }
                }
            }
            
            return [
                'success' => true,
                'eventId' => $createdEvent->getId(),
                'meetUrl' => $meetUrl ?? $createdEvent->getHangoutLink(), // Fallback sur hangoutLink si disponible
                'event' => $createdEvent
            ];
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error creating meeting with Meet URL: ' . $e->getMessage());
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
}