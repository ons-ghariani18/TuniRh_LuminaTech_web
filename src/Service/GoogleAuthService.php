<?php

namespace App\Service;

use Google_Client;
use Google_Service_Calendar;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GoogleAuthService
{
    private $client;
    private $session;
    private $requestStack;

    public function __construct(
        SessionInterface $session,
        RequestStack $requestStack,
        string $googleClientId,
        string $googleClientSecret,
        string $googleRedirectUri
    ) {
        $this->session = $session;
        $this->requestStack = $requestStack;

        $this->client = new Google_Client();
        $this->client->setClientId($googleClientId);
        $this->client->setClientSecret($googleClientSecret);
        $this->client->setRedirectUri($googleRedirectUri);
        $this->client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $code = $request->query->get('code');

        if ($code) {
            try {
                $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
                
                if (!isset($accessToken['error'])) {
                    $this->client->setAccessToken($accessToken);
                    $this->session->set('google_access_token', $accessToken);
                }
            } catch (\Exception $e) {
                throw new \RuntimeException('Google Auth Error: '.$e->getMessage());
            }
        }
    }

    public function getClient(): Google_Client
    {
        $accessToken = $this->session->get('google_access_token');

        if ($accessToken) {
            $this->client->setAccessToken($accessToken);

            // Refresh token if expired
            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = $this->client->getRefreshToken();
                if ($refreshToken) {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    $this->session->set('google_access_token', $newToken);
                }
            }
        }

        return $this->client;
    }
}