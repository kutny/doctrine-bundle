<?php

namespace Kutny\DoctrineBundle;

use Doctrine\ORM\QueryBuilder;
use Exception;
use Kutny\DateTimeBundle\DateTime;
use Kutny\DoctrineBundle\Filter\Filter;
use Kutny\DoctrineBundle\Filter\OrderBy;
use Kutny\DoctrineBundle\QueryBuilderFilter\Condition;
use Kutny\DoctrineBundle\QueryBuilderFilter\Join;
use Kutny\DoctrineBundle\QueryBuilderFilter\JoinWith;
use Kutny\DoctrineBundle\QueryBuilderFilter\LeftJoin;
use Kutny\DoctrineBundle\QueryBuilderFilter\LeftJoinWith;
use Kutny\DoctrineBundle\QueryBuilderFilterApplier\InconsistentJoinsException;

class QueryBuilderFilterApplier implements IQueryBuilderFilterApplier
{
    public function apply(Filter $filter, QueryBuilder $queryBuilder)
    {
        $this->applySelect($filter, $queryBuilder);
        $this->applyJoins($filter->getJoins(), $queryBuilder);
        $this->applyConditions($filter->getConditions(), $queryBuilder);
        $this->applyOrderBys($filter->getOrderBys(), $queryBuilder);
        $this->applyLimit($filter, $queryBuilder);
        $this->applyOffset($filter, $queryBuilder);
    }

    private function applySelect(Filter $filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder->select($filter->getAlias());

        $queryBuilder->from(
            $filter->getEntityClassName(),
            $filter->getAlias()
        );
    }

    /**
     * @param Join[] $joins
     * @param QueryBuilder $queryBuilder
     */
    private function applyJoins(array $joins, QueryBuilder $queryBuilder)
    {
        /** @var Join[] $previousJoins */
        $previousJoins = array();

        foreach ($joins as $join) {
            $joinEntity = $join->getEntityClassName();
            $joinAlias = $join->getAlias();

            if (!array_key_exists($joinEntity, $previousJoins)) {
                $previousJoins[$joinEntity] = $join;

                switch (get_class($join))
{
                    case LeftJoinWith::class:
                        /** @var LeftJoinWith $join */
                        $queryBuilder->leftJoin($joinEntity, $joinAlias, \Doctrine\ORM\Query\Expr\Join::WITH, $join->getWithCondition());
                        break;

                    case LeftJoin::class:
                        $queryBuilder->leftJoin($joinEntity, $joinAlias);
                        break;

                    case JoinWith::class:
                        /** @var JoinWith $join */
                        $queryBuilder->join($joinEntity, $joinAlias, \Doctrine\ORM\Query\Expr\Join::WITH, $join->getWithCondition());
                        break;

                    case Join::class:
                        $queryBuilder->join($joinEntity, $joinAlias);
                        break;

                    default:
                        throw new Exception('Invalid JOIN type: ' . get_class($join));

                }
            }
            else if ($previousJoins[$joinEntity]->getAlias() !== $joinAlias) {
                throw new InconsistentJoinsException($joinEntity, $previousJoins[$joinEntity]->getAlias(), $joinAlias);
            }
        }
    }

    /**
     * @param Condition[] $conditions
     * @param QueryBuilder $queryBuilder
     */
    private function applyConditions(array $conditions, QueryBuilder $queryBuilder)
    {
        foreach ($conditions as $condition) {
            $queryBuilder->andWhere($condition->getCondition());

            $parameters = $condition->getParameters();

            foreach ($parameters as $parameter) {
                $queryBuilder->setParameter($parameter->getName(), $this->resolveParameterValue($parameter->getValue()));
            }
        }
    }

    /**
     * @param OrderBy[] $orderBys
     * @param QueryBuilder $queryBuilder
     */
    private function applyOrderBys(array $orderBys, QueryBuilder $queryBuilder)
    {
        foreach ($orderBys as $orderBy) {
            $queryBuilder->addOrderBy($orderBy->getAttribute(), $orderBy->getOrderDirection());
        }
    }

    private function applyLimit(Filter $filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder->setMaxResults($filter->getLimit());
    }

    private function applyOffset(Filter $filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder->setFirstResult($filter->getOffset());
    }

    private function resolveParameterValue($value)
    {
        if ($value instanceof DateTime) {
            return $value->toDateTime();
        }
        else {
            return $value;
        }
    }
}
