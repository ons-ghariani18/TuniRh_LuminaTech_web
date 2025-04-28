<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Test email sending');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $email = (new Email())
        ->from('gharianions98@gmail.com')
        ->to('gharianions77@gmail.com')
        ->subject('Test Email '.date('Y-m-d H:i:s')) // Ajoute un timestamp
        ->text('This is a test email from Symfony');

    try {
        $this->mailer->send($email);
        $output->writeln('<info>Email envoyé avec succès !</info>');
        $output->writeln('<comment>Vérifiez vos spams et console Mailer</comment>');
        return Command::SUCCESS;
    } catch (\Exception $e) {
        $output->writeln('<error>ERREUR: '.$e->getMessage().'</error>');
        return Command::FAILURE;
    }
}
}