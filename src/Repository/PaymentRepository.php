<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\Users;
use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * Vérifie si un utilisateur a payé pour une formation spécifique
     */
    public function hasUserPaidForFormation(Users $user, Formation $formation): bool
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.formation = :formation')
            ->andWhere('p.status = :status')
            ->setParameter('user', $user)
            ->setParameter('formation', $formation)
            ->setParameter('status', true)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    /**
     * Trouve un paiement par son ID de session Stripe
     */
    public function findByStripeSessionId(string $sessionId): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stripeSessionId = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 