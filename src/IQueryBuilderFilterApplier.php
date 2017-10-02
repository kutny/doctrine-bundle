<?php

namespace Kutny\DoctrineBundle;

use Doctrine\ORM\QueryBuilder;
use Kutny\DoctrineBundle\Filter\Filter;

interface IQueryBuilderFilterApplier
{
    function apply(Filter $filter, QueryBuilder $queryBuilder);
}
