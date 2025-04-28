<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:test-notification',
    description: 'Envoie une notification de test par email via Symfony Notifier',
)]
class TestNotificationCommand extends Command
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $notification = new EmailTestNotification(
            '🔔 Notification de test',
            'Ceci est un test d\'envoi de notification par email avec Symfony Notifier.'
        );

        $recipient = new EmailRecipient('gharianions77@gmail.com');

        $this->notifier->send($notification, $recipient);

        $output->writeln('<info>✅ Notification envoyée avec succès!</info>');
        $output->writeln('Vérifiez votre boîte mail (et les spams).');

        return Command::SUCCESS;
    }
}

class EmailRecipient extends Recipient implements EmailRecipientInterface
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

class EmailTestNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        string $subject,
        private string $content
    ) {
        parent::__construct($subject);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = (new Email())
            ->from('gharianions98@gmail.com')
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->text($this->content);

        return new EmailMessage($email);
    }
}