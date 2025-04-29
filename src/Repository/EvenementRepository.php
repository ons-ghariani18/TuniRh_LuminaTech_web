<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function save(Evenement $evenement, bool $flush = true): void
    {
        $this->_em->persist($evenement);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Evenement $evenement, bool $flush = true): void
    {
        $this->_em->remove($evenement);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function search(array $searchData, string $sortField = 'date_debut', string $sortDirection = 'ASC'): array
    {
        return $this->findBySearchCriteria($searchData, $sortField, $sortDirection);
    }

    public function findBySearchCriteria(array $criteria, string $sortField = 'date_debut', string $sortDirection = 'ASC'): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->leftJoin('e.sponsor', 's')
            ->addSelect('s');

        if (!empty($criteria['nom'])) {
            $queryBuilder->andWhere('e.nom LIKE :nom')
                ->setParameter('nom', '%' . $criteria['nom'] . '%');
        }

        if (!empty($criteria['type'])) {
            $queryBuilder->andWhere('e.type = :type')
                ->setParameter('type', $criteria['type']);
        }

        if (!empty($criteria['date_debut'])) {
            $queryBuilder->andWhere('e.date_debut >= :date_debut')
                ->setParameter('date_debut', $criteria['date_debut']);
        }

        if (!empty($criteria['date_fin'])) {
            $queryBuilder->andWhere('e.date_fin <= :date_fin')
                ->setParameter('date_fin', $criteria['date_fin']);
        }

        if (!empty($criteria['lieu'])) {
            $queryBuilder->andWhere('e.lieu LIKE :lieu')
                ->setParameter('lieu', '%' . $criteria['lieu'] . '%');
        }

        if (!empty($criteria['budget_min'])) {
            $queryBuilder->andWhere('e.budget >= :budget_min')
                ->setParameter('budget_min', $criteria['budget_min']);
        }

        if (!empty($criteria['budget_max'])) {
            $queryBuilder->andWhere('e.budget <= :budget_max')
                ->setParameter('budget_max', $criteria['budget_max']);
        }

        if (!empty($criteria['sponsor'])) {
            $queryBuilder->andWhere('s.id_sp = :sponsor')
                ->setParameter('sponsor', $criteria['sponsor']);
        }

        $validFields = ['id_ev', 'nom', 'type', 'date_debut', 'date_fin', 'lieu', 'budget'];
        if (in_array($sortField, $validFields)) {
            $queryBuilder->orderBy('e.' . $sortField, $sortDirection);
        } else {
            $queryBuilder->orderBy('e.date_debut', 'ASC');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Trouve les événements à venir limités à un nombre spécifique
     * 
     * @param int $limit Nombre maximal d'événements à retourner
     * @return array<Evenement>
     */
    public function findUpcomingEvents(int $limit = 5): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.date_debut >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.date_debut', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements qui se déroulent entre deux dates
     * Pour être utilisé dans le service EventNotificationService
     */
    public function findUpcomingEventsBetweenDates(string $startDate, string $endDate): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.date_debut >= :startDate')
            ->andWhere('e.date_debut <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('e.date_debut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByMonth(int $year, int $month): array
    {
        $startDate = new \DateTime(sprintf('%d-%02d-01', $year, $month));
        $endDate = clone $startDate;
        $endDate->modify('last day of this month');

        return $this->createQueryBuilder('e')
            ->where('e.date_debut BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('e.date_debut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countAllEvents(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id_ev)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findEventsWithAboveAverageBudget(): array
    {
        $subQuery = $this->createQueryBuilder('e2')
            ->select('AVG(e2.budget)')
            ->getDQL();

        return $this->createQueryBuilder('e')
            ->where('e.budget > (' . $subQuery . ')')
            ->orderBy('e.budget', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllUpcoming(\DateTime $now): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.date_debut >= :now')
            ->setParameter('now', $now)
            ->orderBy('e.date_debut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findEventsInNextDays(\DateTime $now, int $days = 4): array
    {
        $targetDate = clone $now;
        $targetDate->modify("+{$days} days");
        
        return $this->createQueryBuilder('e')
            ->where('e.date_debut >= :now')
            ->andWhere('e.date_debut <= :targetDate')
            ->setParameter('now', $now)
            ->setParameter('targetDate', $targetDate)
            ->orderBy('e.date_debut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}