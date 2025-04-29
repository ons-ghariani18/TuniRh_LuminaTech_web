<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    //    /**
    //     * @return Formation[] Returns an array of Formation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Formation
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findBySearchAndPrice(?string $search, string $price): array
    {
        $qb = $this->createQueryBuilder('f');

        // Filtre recherche sur titre et description
        if ($search) {
            $qb->andWhere('LOWER(f.title) LIKE :term OR LOWER(f.description) LIKE :term')
                ->setParameter('term', '%'.strtolower($search).'%');
        }

        // Filtre type de coût
        switch ($price) {
            case 'free':
                $qb->andWhere('f.cost = 0');
                break;
            case 'paid':
                $qb->andWhere('f.cost > 0');
                break;
            case 'all':
            default:
                // pas de condition
                break;
        }

        // Tri par date de début descendant par défaut
        $qb->orderBy('f.startDate', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
