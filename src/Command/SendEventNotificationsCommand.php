<?php

namespace App\Command;

use App\Entity\Evenement;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-event-notifications',
    description: 'Envoie des notifications pour les événements à venir',
)]
class SendEventNotificationsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ?LoggerInterface $logger;
    private NotifierInterface $notifier;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger = null,
        NotifierInterface $notifier
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->notifier = $notifier;
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'days',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Nombre de jours avant l\'événement pour envoyer la notification',
                3
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                'Active le mode debug avec plus d\'informations'
            )
            ->addOption(
                'list-all',
                null,
                InputOption::VALUE_NONE,
                'Liste tous les événements de la base de données'
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $daysBeforeEvent = (int) $input->getOption('days');
        $debug = (bool) $input->getOption('debug');
        $listAll = (bool) $input->getOption('list-all');
        
        $io->title('Vérification des événements à venir');
        
        if ($listAll) {
            $this->listAllEvents($io);
            return Command::SUCCESS;
        }
        
        $io->text("Recherche des événements qui commencent dans {$daysBeforeEvent} jours...");
        
        if ($debug) {
            $io->note('Mode débogage activé');
        }
        
        $events = $this->checkUpcomingEvents($daysBeforeEvent, $debug);
        
        if (empty($events)) {
            $io->warning('Aucun événement trouvé dans la période spécifiée.');
            
            if ($debug && $io->confirm('Voulez-vous créer un événement de test dans '.$daysBeforeEvent.' jours ?', false)) {
                $this->createTestEvent($io, $daysBeforeEvent);
            }
            
            return Command::SUCCESS;
        }
        
        // Envoyer les notifications pour chaque événement
        $notifiedEvents = [];
        foreach ($events as $event) {
            if ($this->sendEventNotification($event)) {
                $notifiedEvents[] = $event;
            }
        }
        
        $eventList = [];
        foreach ($notifiedEvents as $event) {
            $eventList[] = [
                $event->getIdEv(),
                $event->getNom(),
                $event->getDateDebut()->format('d/m/Y H:i'),
                $event->getLieu()
            ];
        }
        
        $io->success(sprintf('Notifications envoyées pour %d événement(s) !', count($notifiedEvents)));
        $io->table(['ID', 'Nom', 'Date de début', 'Lieu'], $eventList);
        
        return Command::SUCCESS;
    }

    /**
     * Envoie une notification pour un événement
     */
    private function sendEventNotification(Evenement $event): bool
    {
        $notification = new EventNotification(
            '🔔 Rappel d\'événement: ' . $event->getNom(),
            sprintf(
                "L'événement '%s' approche!\n\n".
                "Date: %s\n".
                "Lieu: %s\n\n".
                "Nous espérons vous y voir!",
                $event->getNom(),
                $event->getDateDebut()->format('d/m/Y H:i'),
                $event->getLieu()
            )
        );

        // Ici vous devriez récupérer les destinataires depuis votre système
        // Pour l'exemple, on utilise une adresse fixe
        $recipient = new EmailRecipient('gharianions77@gmail.com');

        $this->notifier->send($notification, $recipient);

        return true;
    }

    /**
     * Vérifie les événements à venir dans X jours
     */
    public function checkUpcomingEvents(int $daysBeforeEvent, bool $debug = false): array
    {
        $now = new \DateTimeImmutable('today');
        $targetDate = $now->modify("+{$daysBeforeEvent} days");
        $startOfDay = $targetDate->setTime(0, 0, 0);
        $endOfDay = $targetDate->setTime(23, 59, 59);

        if ($debug && $this->logger) {
            $this->logger->info("[DEBUG] Recherche des événements entre {$startOfDay->format('Y-m-d H:i')} et {$endOfDay->format('Y-m-d H:i')}");
        }

        $repository = $this->entityManager->getRepository(Evenement::class);
        $events = $repository->createQueryBuilder('e')
            ->where('e.date_debut BETWEEN :start AND :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getResult();

        if ($debug && $this->logger) {
            $this->logger->info('[DEBUG] ' . count($events) . ' événement(s) trouvé(s).');
        }

        return $events;
    }
    
    private function listAllEvents(SymfonyStyle $io): void
    {
        $repository = $this->entityManager->getRepository(Evenement::class);
        $events = $repository->findAll();
        
        if (empty($events)) {
            $io->warning('Aucun événement trouvé dans la base de données.');
            return;
        }
        
        $eventList = [];
        foreach ($events as $event) {
            $eventList[] = [
                $event->getIdEv(),
                $event->getNom(),
                $event->getDateDebut()->format('d/m/Y H:i'),
                $event->getLieu()
            ];
        }
        
        $io->section('Tous les événements disponibles');
        $io->table(['ID', 'Nom', 'Date de début', 'Lieu'], $eventList);
        $io->info(sprintf('Total: %d événement(s)', count($events)));
    }
    
    private function createTestEvent(SymfonyStyle $io, int $daysBeforeEvent): void
    {
        try {
            $event = new Evenement();
            $event->setNom('Événement test ' . uniqid());
            $event->setLieu('Lieu de test');
            
            $dateDebut = new \DateTime();
            $dateDebut->add(new \DateInterval("P{$daysBeforeEvent}D"));
            $event->setDateDebut($dateDebut);
            
            $dateFin = clone $dateDebut;
            $dateFin->add(new \DateInterval('PT2H'));
            $event->setDateFin($dateFin);
            
            if (method_exists($event, 'setDescription')) {
                $event->setDescription('Événement créé pour les tests de notification');
            }
            
            if (method_exists($event, 'setType')) {
                $event->setType('Test');
            }
            
            if (method_exists($event, 'setBudget')) {
                $event->setBudget(1000.00);
            }
            
            $this->entityManager->persist($event);
            $this->entityManager->flush();
            
            $io->success('Événement de test créé avec succès!');
        } catch (\Exception $e) {
            $io->error('Erreur lors de la création de l\'événement de test: ' . $e->getMessage());
            
            if ($this->logger) {
                $this->logger->error('Erreur création événement test: ' . $e->getMessage());
            }
        }
    }
}

class EmailRecipient extends Recipient implements EmailRecipientInterface
{
    private string $email;

    public function __construct(string $email)
    {
        parent::__construct('', $email);
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

class EventNotification extends Notification implements EmailNotificationInterface
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