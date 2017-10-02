<?php

namespace Kutny\DoctrineBundle;

use Doctrine\ORM\EntityManager;
use Kutny\DoctrineBundle\Filter\Filter;

class QueryBuilderFactory
{
    private $entityManager;
    private $queryBuilderFilterApplier;

    public function __construct(
        EntityManager $entityManager,
        QueryBuilderFilterApplier $queryBuilderFilterApplier
    ) {
        $this->entityManager = $entityManager;
        $this->queryBuilderFilterApplier = $queryBuilderFilterApplier;
    }

    public function createFromFilter(Filter $filter)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $this->queryBuilderFilterApplier->apply($filter, $queryBuilder);

        return $queryBuilder;
    }
}
