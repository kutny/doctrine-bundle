<?php

namespace Kutny\DoctrineBundle\Filter;

class OrderBy
{
    private $attribute;
    private $orderDirection;

    public function __construct($attribute, $orderDirection)
    {
        $this->attribute = $attribute;
        $this->orderDirection = $orderDirection;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function getOrderDirection()
    {
        return $this->orderDirection;
    }
}
