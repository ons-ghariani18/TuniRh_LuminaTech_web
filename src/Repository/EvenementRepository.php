<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
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
    
    /**
     * Recherche des événements selon les critères du formulaire de recherche
     * 
     * @param array $searchData Les données du formulaire de recherche
     * @return Evenement[] Les événements correspondants
     */
    public function search(array $searchData): array
    {
        return $this->findBySearchCriteria($searchData);
    }

    /**
     * Recherche des événements selon plusieurs critères
     * 
     * @param array $criteria Les critères de recherche
     * @return Evenement[] Les événements correspondants
     */
    public function findBySearchCriteria(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->leftJoin('e.sponsor', 's')
            ->addSelect('s');

        // Filtre par nom
        if (!empty($criteria['nom'])) {
            $queryBuilder->andWhere('e.nom LIKE :nom')
                ->setParameter('nom', '%' . $criteria['nom'] . '%');
        }

        // Filtre par type
        if (!empty($criteria['type'])) {
            $queryBuilder->andWhere('e.type = :type')
                ->setParameter('type', $criteria['type']);
        }

        // Filtre par date de début (à partir de)
        if (!empty($criteria['date_debut'])) {
            $queryBuilder->andWhere('e.date_debut >= :date_debut')
                ->setParameter('date_debut', $criteria['date_debut']);
        }

        // Filtre par date de fin (jusqu'à)
        if (!empty($criteria['date_fin'])) {
            $queryBuilder->andWhere('e.date_fin <= :date_fin')
                ->setParameter('date_fin', $criteria['date_fin']);
        }

        // Filtre par lieu
        if (!empty($criteria['lieu'])) {
            $queryBuilder->andWhere('e.lieu LIKE :lieu')
                ->setParameter('lieu', '%' . $criteria['lieu'] . '%');
        }

        // Filtre par budget minimum
        if (!empty($criteria['budget_min'])) {
            $queryBuilder->andWhere('e.budget >= :budget_min')
                ->setParameter('budget_min', $criteria['budget_min']);
        }

        // Filtre par budget maximum
        if (!empty($criteria['budget_max'])) {
            $queryBuilder->andWhere('e.budget <= :budget_max')
                ->setParameter('budget_max', $criteria['budget_max']);
        }

        // Filtre par sponsor
        if (!empty($criteria['sponsor'])) {
            $queryBuilder->andWhere('s.idSp = :sponsor')
                ->setParameter('sponsor', $criteria['sponsor']);
        }

        // Tri par date de début (par défaut: du plus récent au plus ancien)
        $queryBuilder->orderBy('e.date_debut', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Trouve les événements à venir
     * 
     * @param int $limit Nombre maximum d'événements à retourner
     * @return Evenement[] 
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
     * Trouve les événements par mois et année
     * 
     * @param int $year
     * @param int $month
     * @return Evenement[]
     */
    public function findByMonth(int $year, int $month): array
    {
        $startDate = new \DateTime(sprintf('%d-%d-01', $year, $month));
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

    /**
     * Compte le nombre total d'événements
     * 
     * @return int
     */
    public function countAllEvents(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id_ev)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les événements avec un budget supérieur à la moyenne
     * 
     * @return Evenement[]
     */
    public function findEventsWithAboveAverageBudget(): array
    {
        $subQuery = $this->createQueryBuilder('e2')
            ->select('AVG(e2.budget)');

        return $this->createQueryBuilder('e')
            ->where('e.budget > (' . $subQuery->getDQL() . ')')
            ->orderBy('e.budget', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Sauvegarde un événement
     * 
     * @param Evenement $evenement
     * @param bool $flush
     */
    public function save(Evenement $evenement, bool $flush = true): void
    {
        $this->_em->persist($evenement);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Supprime un événement
     * 
     * @param Evenement $evenement
     * @param bool $flush
     */
    public function remove(Evenement $evenement, bool $flush = true): void
    {
        $this->_em->remove($evenement);
        if ($flush) {
            $this->_em->flush();
        }
    }
}