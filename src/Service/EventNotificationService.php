<?php

namespace App\Service;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use DateTime;
use DateInterval;

class EventNotificationService
{
    private NotifierInterface $notifier;
    private EvenementRepository $eventRepository;

    public function __construct(
        NotifierInterface $notifier,
        EvenementRepository $eventRepository
    ) {
        $this->notifier = $notifier;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Vérifie les événements qui commencent dans un nombre de jours spécifié
     * et envoie des notifications
     */
    public function checkUpcomingEvents(int $daysBeforeEvent): array
    {
        // Calculer la date cible (aujourd'hui + nombre de jours)
        $today = new DateTime();
        $targetDate = (new DateTime())->add(new DateInterval("P{$daysBeforeEvent}D"));
        
        // Formatage pour la requête
        $targetDateStart = $targetDate->format('Y-m-d 00:00:00');
        $targetDateEnd = $targetDate->format('Y-m-d 23:59:59');
        
        // Récupérer les événements qui commencent à la date cible
        $upcomingEvents = $this->eventRepository->findUpcomingEventsBetweenDates($targetDateStart, $targetDateEnd);
        
        // Envoyer une notification pour chaque événement
        $notifiedEvents = [];
        
        foreach ($upcomingEvents as $event) {
            $this->sendEventNotification($event, $daysBeforeEvent);
            $notifiedEvents[] = $event;
        }
        
        return $notifiedEvents;
    }

    /**
     * Envoie une notification pour un événement spécifique
     */
    private function sendEventNotification(Evenement $event, int $daysBeforeEvent): void
    {
        $notification = new EventEmailNotification(
            sprintf('🗓️ Rappel: "%s" commence dans %d jour(s)', $event->getNom(), $daysBeforeEvent),
            $this->createEventMessage($event, $daysBeforeEvent)
        );
        
        $recipient = new EmailRecipient('gharianions77@gmail.com');
        
        $this->notifier->send($notification, $recipient);
    }

    /**
     * Crée le contenu du message de notification
     */
    private function createEventMessage(Evenement $event, int $daysBeforeEvent): string
    {
        $message = "Bonjour,\n\n";
        $message .= "Nous vous rappelons que l'événement suivant commence dans {$daysBeforeEvent} jour(s):\n\n";
        $message .= "📌 Nom: " . $event->getNom() . "\n";
        $message .= "📅 Date: " . $event->getDateDebut()->format('d/m/Y à H:i') . "\n";
        $message .= "📍 Lieu: " . $event->getLieu() . "\n";
        
        if ($event->getDescription()) {
            $message .= "\nDescription: " . $event->getDescription() . "\n";
        }
        
        $message .= "\nCet email est envoyé automatiquement, merci de ne pas y répondre.\n";
        $message .= "\nCordialement,\nL'équipe de gestion des événements";
        
        return $message;
    }
}

/**
 * Classe EmailRecipient (reprise du premier exemple)
 */
class EmailRecipient extends \Symfony\Component\Notifier\Recipient\Recipient implements \Symfony\Component\Notifier\Recipient\EmailRecipientInterface
{
    private string $email;
    
    public function __construct(string $email)
    {
        parent::__construct('', $email); // (phone, email)
        $this->email = $email;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
}

/**
 * Classe EventEmailNotification (basée sur le premier exemple)
 */
class EventEmailNotification extends \Symfony\Component\Notifier\Notification\Notification implements \Symfony\Component\Notifier\Notification\EmailNotificationInterface
{
    public function __construct(
        string $subject,
        private string $content
    ) {
        parent::__construct($subject);
    }
    
    public function asEmailMessage(\Symfony\Component\Notifier\Recipient\EmailRecipientInterface $recipient, string $transport = null): ?\Symfony\Component\Notifier\Message\EmailMessage
    {
        $email = (new \Symfony\Component\Mime\Email())
            ->from('gharianions98@gmail.com')
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->text($this->content);
        
        return new \Symfony\Component\Notifier\Message\EmailMessage($email);
    }
}