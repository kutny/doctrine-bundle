<?php

namespace Kutny\DoctrineBundle;

use Kutny\DoctrineBundle\Collection\Imutable\ObjectList;

class EntityAggregateList extends ObjectList
{
    /** @deprecated use getValue instead */
    public function getAggregatedValue($entity)
    {
        return $this->getValue($entity);
    }
}
