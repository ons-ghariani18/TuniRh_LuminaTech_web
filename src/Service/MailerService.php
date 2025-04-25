<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
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

    public function sendConfirmationEmail(string $to, string $candidateName, string $jobTitle, string $applicationDate, string $cvPath = null): void
    {
        try {
            $email = (new Email())
                ->from(new Address('Hedi.MRABET@esprit.tn', 'Équipe TuniRh'))
                ->replyTo('mrabethedi8@gmail.com')
                ->to($to)
                ->subject('Confirmation de votre candidature - ' . $jobTitle)
                ->html($this->twig->render('emails/confirmation_candidature.html.twig', [
                    'candidateName' => $candidateName,
                    'jobTitle' => $jobTitle,
                    'applicationDate' => $applicationDate,
                ]))
                ->text("Bonjour $candidateName,\n\nNous confirmons la réception de votre candidature pour le poste de $jobTitle.\n\nDétails :\n- Date : $applicationDate\n- Poste : $jobTitle\n\nNous vous recontacterons bientôt.\n\nCordialement,\nL'équipe TuniRh");

            // Attacher le CV si fourni
            if ($cvPath && file_exists($cvPath)) {
                $email->attachFromPath($cvPath, 'CV_' . $candidateName . '.pdf');
            }

            $this->mailer->send($email);
            $this->logger->info('Confirmation email sent to: ' . $to);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email to ' . $to . ': ' . $e->getMessage());
            throw $e;
        }
    }
}