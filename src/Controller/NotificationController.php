<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Service\EventNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EventNotificationService $notificationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventNotificationService $notificationService
    ) {
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
    }

    #[Route('/notification', name: 'app_notification')]
    public function index(): Response
    {
        // Nombre de jours à l'avance pour afficher les notifications (ajustable)
        $upcomingDays = 7; // Affiche les événements à venir dans les 7 prochains jours
        
        // Récupérer tous les événements à venir dans les X prochains jours
        $upcomingEvents = $this->getUpcomingEvents($upcomingDays);
        
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'upcoming_events' => $upcomingEvents,
            'days_range' => $upcomingDays
        ]);
    }

    /**
     * Récupère les événements à venir dans les X prochains jours
     */
    private function getUpcomingEvents(int $days): array
    {
        $now = new \DateTimeImmutable('today');
        $endDate = $now->modify("+{$days} days");
        
        $repository = $this->entityManager->getRepository(Evenement::class);
        return $repository->createQueryBuilder('e')
            ->where('e.date_debut BETWEEN :now AND :end')
            ->setParameter('now', $now)
            ->setParameter('end', $endDate)
            ->orderBy('e.date_debut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}