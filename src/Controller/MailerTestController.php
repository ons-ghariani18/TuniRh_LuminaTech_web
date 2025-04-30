<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EmailService;
use App\Entity\Formation;
use App\Entity\Users;

class MailerTestController extends AbstractController
{
    #[Route('/test-mailer', name: 'app_test_mailer')]
    public function index(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('contact@tunirh.com')
                ->to('sadok0703@gmail.com')
                ->subject('Test email from Symfony')
                ->text('This is a test email sent from Symfony Mailer.')
                ->html('<p>This is a test email sent from <strong>Symfony Mailer</strong>.</p>');

            $mailer->send($email);

            return new Response('Email sent successfully!');
        } catch (\Exception $e) {
            return new Response('Error sending email: ' . $e->getMessage(), 500);
        }
    }

    #[Route('/test-email-service', name: 'app_test_email_service')]
    public function testEmailService(EmailService $emailService): Response
    {
        try {
            // Créer des objets fictifs pour le test
            $user = new Users();
            $user->setEmail('sadok0703@gmail.com');

            $formation = new Formation();
            $formation->setTitle('Formation Test');
            $formation->setCost(99.99);

            // Tester l'email de succès
            $emailService->sendPaymentSuccessEmail($user, $formation, 99.99);

            return new Response('Email de test envoyé avec succès via EmailService!');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage(), 500);
        }
    }
    #[Route('/send-mail', name: 'app_send_mail')]
    public function sendMail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('you@example.com')
            ->to('sadok0703@gmail.com') // <-- ici c'est important
            ->subject('Test Email')
            ->text('Bonjour Sadok, ceci est un test.');

        $mailer->send($email);

        return new Response('Email envoyé avec succès.');
    }

}