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
            
            foreach ($eventData as $key => $value) {
                $setter = 'set' . ucfirst($key);
                if (method_exists($event, $setter)) {
                    $event->$setter($value);
                }
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
     * Supprime un événement
     */
    public function deleteEvent(string $eventId, string $calendarId = 'primary'): void
    {
        try {
            $service = $this->getCalendarService();
            $service->events->delete($calendarId, $eventId);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error deleting calendar event: ' . $e->getMessage());
            }
            throw $e;
        }
    }
    
    /**
     * Révoque l'accès et supprime les tokens
     */
    public function revokeAccess(): void
    {
        try {
            if ($this->client->getAccessToken()) {
                $this->client->revokeToken();
            }
            
            // Supprimer le fichier de token
            if (file_exists($this->tokenPath)) {
                unlink($this->tokenPath);
            }
            
            // Supprimer le token de la session
            if ($this->session && $this->session->has('google_token')) {
                $this->session->remove('google_token');
            }
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Error revoking access: ' . $e->getMessage());
            }
        }
    }
}