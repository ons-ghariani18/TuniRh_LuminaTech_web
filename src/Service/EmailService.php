<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Psr\Log\LoggerInterface;

class EmailService
{
    private $mailer;
    private $sender;
    private $senderName;
    private $logger;

    public function __construct(
        MailerInterface $mailer,
        string $sender,
        string $senderName,
        LoggerInterface $logger = null
    ) {
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->senderName = $senderName;
        $this->logger = $logger;
    }

    public function sendConfirmation(array $candidate, array $job)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->sender, $this->senderName))
            ->to($candidate['email'])
            ->subject('Confirmation pour ' . $job['title'])
            ->htmlTemplate('emails/confirmation.html.twig')
            ->context([
                'candidate' => $candidate,
                'job' => $job
            ]);

        try {
            $this->mailer->send($email);
            $this->logger?->info('Email envoyé à ' . $candidate['email']);
        } catch (\Exception $e) {
            $this->logger?->error('Erreur email: ' . $e->getMessage());
            throw $e;
        }
    }
}