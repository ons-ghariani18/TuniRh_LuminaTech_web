<?php

namespace App\Repository;

use App\Entity\Sponsor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SponsorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsor::class);
    }

    public function search(array $criteria, ?string $sortField = null, ?string $sortDirection = 'ASC'): array
    {
        $queryBuilder = $this->createQueryBuilder('s');
        
        // Applique les critères de recherche
        $this->applySearchCriteria($queryBuilder, $criteria);

        // Normalise le champ de tri
        $sortField = $this->normalizeSortField($sortField);
    
        // Applique le tri si spécifié
        if ($sortField && $this->isFieldSortable($sortField)) {
            $queryBuilder->orderBy('s.' . $sortField, $sortDirection);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllSorted(?string $sortField = null, ?string $sortDirection = 'ASC'): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        // Normalise le champ de tri
        $sortField = $this->normalizeSortField($sortField);
        
        if ($sortField && $this->isFieldSortable($sortField)) {
            $queryBuilder->orderBy('s.' . $sortField, $sortDirection);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    private function applySearchCriteria(QueryBuilder $queryBuilder, array $criteria): void
    {
        if (!empty($criteria['nom'])) {
            $queryBuilder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$criteria['nom'].'%');
        }

        if (!empty($criteria['type'])) {
            $queryBuilder->andWhere('s.type = :type')
                ->setParameter('type', $criteria['type']);
        }

        if (!empty($criteria['email'])) {
            $queryBuilder->andWhere('s.email LIKE :email')
                ->setParameter('email', '%'.$criteria['email'].'%');
        }
    }

    private function isFieldSortable(string $field): bool
    {
        // Attention ici : mettre les vrais noms utilisés dans la base !
        $sortableFields = ['id_sp', 'nom', 'type', 'email', 'telephone'];
        return in_array($field, $sortableFields);
    }

    private function normalizeSortField(?string $field): ?string
    {
        if ($field === 'idSp') {
            return 'id_sp';
        }
        return $field;
    }

 

    
}
