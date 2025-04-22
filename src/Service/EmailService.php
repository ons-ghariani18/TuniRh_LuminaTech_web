<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Entity\Users;
use App\Entity\Formation;

class EmailService
{
    private $mailer;
    private $sender;

    public function __construct(MailerInterface $mailer, string $sender = 'contact@tunirh.com')
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
    }

    /**
     * Envoie un email de confirmation de paiement réussi
     */
    public function sendPaymentSuccessEmail(Users $user, Formation $formation, float $amount): void
    {
        $email = (new Email())
            ->from(new Address($this->sender, 'TuniRH Formation'))
            ->to($user->getEmail())
            ->subject('Confirmation de paiement - ' . $formation->getTitle())
            ->html($this->renderPaymentSuccessTemplate($user, $formation, $amount));

        $this->mailer->send($email);
    }

    /**
     * Envoie un email en cas d'échec de paiement
     */
    public function sendPaymentFailureEmail(Users $user, Formation $formation, float $amount, string $reason = ''): void
    {
        $email = (new Email())
            ->from(new Address($this->sender, 'TuniRH Formation'))
            ->to($user->getEmail())
            ->subject('Échec de paiement - ' . $formation->getTitle())
            ->html($this->renderPaymentFailureTemplate($user, $formation, $amount, $reason));

        $this->mailer->send($email);
    }

    /**
     * Génère le template HTML pour un email de paiement réussi
     */
    private function renderPaymentSuccessTemplate(Users $user, Formation $formation, float $amount): string
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #e67e22; padding: 15px; color: white; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                .button { display: inline-block; padding: 10px 20px; background-color: #e67e22; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Confirmation de Paiement</h1>
                </div>
                <div class='content'>
                    <p>Bonjour " . $user->getFullName() . ",</p>
                    
                    <p>Nous vous confirmons que votre paiement de <strong>" . number_format($amount, 2) . " €</strong> pour la formation <strong>" . $formation->getTitle() . "</strong> a été traité avec succès.</p>
                    
                    <p>Détails de la transaction :</p>
                    <ul>
                        <li>Formation : " . $formation->getTitle() . "</li>
                        <li>Montant : " . number_format($amount, 2) . " €</li>
                        <li>Date : " . (new \DateTime())->format('d/m/Y H:i') . "</li>
                    </ul>
                    
                    <p>Vous pouvez désormais télécharger votre attestation depuis votre espace personnel.</p>
                    
                    <p>Merci pour votre confiance et bonne formation !</p>
                </div>
                <div class='footer'>
                    <p>Ce message a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    <p>&copy; " . date('Y') . " TuniRH - Tous droits réservés</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Génère le template HTML pour un email d'échec de paiement
     */
    private function renderPaymentFailureTemplate(Users $user, Formation $formation, float $amount, string $reason): string
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #e74c3c; padding: 15px; color: white; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                .button { display: inline-block; padding: 10px 20px; background-color: #e67e22; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Problème de Paiement</h1>
                </div>
                <div class='content'>
                    <p>Bonjour " . $user->getFullName() . ",</p>
                    
                    <p>Nous sommes désolés de vous informer que votre paiement de <strong>" . number_format($amount, 2) . " €</strong> pour la formation <strong>" . $formation->getTitle() . "</strong> n'a pas pu être traité.</p>
                    
                    " . ($reason ? "<p><strong>Raison de l'échec:</strong> " . $reason . "</p>" : "") . "
                    
                    <p>Détails de la tentative de paiement :</p>
                    <ul>
                        <li>Formation : " . $formation->getTitle() . "</li>
                        <li>Montant : " . number_format($amount, 2) . " €</li>
                        <li>Date : " . (new \DateTime())->format('d/m/Y H:i') . "</li>
                    </ul>
                    
                    <p>Vous pouvez réessayer de faire le paiement depuis votre espace personnel ou nous contacter si vous rencontrez des difficultés.</p>
                    
                    <p>Nous vous prions de nous excuser pour tout inconvénient causé.</p>
                </div>
                <div class='footer'>
                    <p>Ce message a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    <p>&copy; " . date('Y') . " TuniRH - Tous droits réservés</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
} 