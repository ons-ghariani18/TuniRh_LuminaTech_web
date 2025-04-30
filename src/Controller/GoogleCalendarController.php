<?php

namespace App\Controller;

use App\Service\GoogleCalendarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleCalendarController extends AbstractController
{
    #[Route('/google/connect', name: 'google_connect')]
    public function connect(GoogleCalendarService $calendarService): Response
    {
        $authUrl = $calendarService->getAuthUrl();
        return $this->redirect($authUrl);
    }

    #[Route('/google/callback', name: 'google_callback')]
    public function callback(Request $request, GoogleCalendarService $calendarService): Response
    {
        $code = $request->query->get('code');
        
        if ($code) {
            $calendarService->setAccessToken($code);
            
            // Redirect to calendar page after successful authentication
            return $this->redirectToRoute('google_calendar');
        }
        
        // If no code is provided, redirect back to connect page
        return $this->redirectToRoute('google_connect');
    }

    #[Route('/google/calendar', name: 'google_calendar')]
    public function calendar(GoogleCalendarService $calendarService): Response
    {
        // Check if token is expired and refresh if needed
        if ($calendarService->isAccessTokenExpired()) {
            $calendarService->refreshToken();
        }

        // Get calendar service and possibly list events or show calendar
        $service = $calendarService->getCalendarService();
        
        // Here you would typically fetch events and pass them to a template
        
        return $this->render('google_calendar/index.html.twig', [
            'controller_name' => 'GoogleCalendarController',
            // Pass additional data as needed
        ]);
    }
}