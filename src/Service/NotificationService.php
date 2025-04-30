<?php

namespace App\Service;

use App\Entity\Users;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class NotificationService
{
    private $mailer;
    private $twig;
    private $logger;

    public function __construct(MailerInterface $mailer, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function sendVerificationCodeEmail(Users $user, string $code)
    {
        $this->logger->info('Tentative envoi email à: ' . $user->getEmail() . ' avec code: ' . $code);

        // Valider l'adresse email
        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->logger->error('Adresse email invalide: ' . $user->getEmail());
            throw new \InvalidArgumentException('Adresse email invalide');
        }

        try {
            $email = (new Email())
                ->from('hamraouissamar@gmail.com') // Utiliser l'adresse Gmail authentifiée
                ->to($user->getEmail())
                ->subject('Code de vérification - TuniRh')
                ->html($this->twig->render('emails/verification_code.html.twig', [
                    'user' => $user,
                    'code' => $code,
                ]));

            $this->mailer->send($email);
            $this->logger->info('Email envoyé avec succès à: ' . $user->getEmail());
        } catch (\Throwable $e) {
            $this->logger->critical('Erreur envoi email à ' . $user->getEmail() . ': ' . $e->getMessage());
            throw $e;
        }
    }
}