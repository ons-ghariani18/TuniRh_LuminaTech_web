<?php

namespace App\Service;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class GoogleCalendarService
{
    private $client;
    private $redirectUri;
    private $requestStack;

    public function __construct(
        ParameterBagInterface $params, 
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
        
        // Initialisation du client Google
        $this->client = new Google_Client();
        $this->client->setAuthConfig($params->get('credentials.json'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent'); // Force à demander un refresh token
        
        // Set the redirect URI
        $this->redirectUri = $requestStack->getCurrentRequest() 
            ? $requestStack->getCurrentRequest()->getSchemeAndHttpHost() . '/google/callback'
            : 'http://localhost/google/callback'; // Fallback
        
        $this->client->setRedirectUri($this->redirectUri);
        
        // Charger le token s'il existe
        $session = $this->getSession();
        if ($session && $session->has('google_token')) {
            $this->client->setAccessToken($session->get('google_token'));
            
            // Refresh token if it's expired
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    $session->set('google_token', $this->client->getAccessToken());
                } else {
                    // Clear the invalid token
                    $session->remove('google_token');
                }
            }
        }
    }

    private function getSession()
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request ? $request->getSession() : null;
    }

    public function getClient()
    {
        return $this->client;
    }
    
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }
    
    public function handleCallback($code)
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            // Vérifier s'il y a des erreurs
            if (isset($token['error'])) {
                throw new \Exception('Error getting token: ' . $token['error_description']);
            }
            
            $this->client->setAccessToken($token);
            
            // Stocker le token dans la session
            $session = $this->getSession();
            if ($session) {
                $session->set('google_token', $token);
            }
            
            return $token;
        } catch (\Exception $e) {
            throw new \Exception('Failed to get access token: ' . $e->getMessage());
        }
    }
    
    public function isAuthenticated(): bool
    {
        return $this->client->getAccessToken() !== null && !$this->client->isAccessTokenExpired();
    }
    
    public function logout()
    {
        $session = $this->getSession();
        if ($session) {
            $session->remove('google_token');
        }
    }

    public function createEvent($calendarId, $eventData)
    {
        if (!$this->isAuthenticated()) {
            throw new \Exception('Not authenticated with Google Calendar');
        }
        
        $service = new Google_Service_Calendar($this->client);
        $event = new Google_Service_Calendar_Event($eventData);
        return $service->events->insert($calendarId, $event);
    }

    public function listEvents($calendarId)
    {
        if (!$this->isAuthenticated()) {
            throw new \Exception('Not authenticated with Google Calendar');
        }
        
        $service = new Google_Service_Calendar($this->client);
        $events = $service->events->listEvents($calendarId);
        return $events->getItems();
    }
}