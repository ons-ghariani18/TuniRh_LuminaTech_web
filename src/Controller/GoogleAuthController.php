<?php

namespace App\Controller;

use App\Service\GoogleAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleAuthController extends AbstractController
{
    private $googleAuthService;

    public function __construct(GoogleAuthService $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }

    /**
     * @Route("/google/auth", name="google_auth")
     */
    public function authenticate(): RedirectResponse
    {
        return $this->redirect($this->googleAuthService->getAuthUrl());
    }

    /**
     * @Route("/google/callback", name="google_callback")
     */
    public function callback(): RedirectResponse
    {
        try {
            $this->googleAuthService->handleCallback();
            $this->addFlash('success', 'Authentification Google réussie !');
            return $this->redirectToRoute('app_google_calendar');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur d\'authentification: '.$e->getMessage());
            return $this->redirectToRoute('app_home');
        }
    }
}