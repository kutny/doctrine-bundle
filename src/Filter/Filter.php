<?php

namespace Kutny\DoctrineBundle\Filter;

use Kutny\DoctrineBundle\QueryBuilderFilter\Condition;
use Kutny\DoctrineBundle\QueryBuilderFilter\Join;

class Filter
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    private $entityClassName;
    private $alias;
    private $limit;
    private $offset;
    private $conditions;
    private $joins;
    private $orderBys;

    final public function __construct($entityClassName, $alias)
    {
        $this->entityClassName = $entityClassName;
        $this->alias = $alias;
        $this->conditions = array();
        $this->joins = array();
        $this->orderBys = array();
    }

    public function getEntityClassName()
    {
        return $this->entityClassName;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function page($page, $recordsPerPage)
    {
        $this->setLimit($recordsPerPage);

        if ($page > 1) {
            $this->setOffset(($page - 1) * $recordsPerPage);
        }
        return $this;
    }

    /**
     * @return Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return Join[]
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @return OrderBy[]
     */
    public function getOrderBys()
    {
        return $this->orderBys;
    }

    public function add($argument)
    {
        if (is_array($argument)) {
            foreach ($argument as $item) {
                $this->addItem($item);
            }
        }
        else {
            $this->addItem($argument);
        }
    }

    private function addItem($item)
    {
        if ($item instanceof Join) {
            $this->joins[] = $item;
        }
        else if ($item instanceof Condition) {
            $this->conditions[] = $item;
        }
        else if ($item instanceof OrderBy) {
            $this->orderBys[] = $item;
        }
        else {
            throw new \Exception('Unexpected item: ' . get_class($item));
        }
    }
}
