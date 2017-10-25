<?php

namespace Kutny\DoctrineBundle\QueryBuilderFilterApplier;

use Exception;

class InvalidOrderException extends Exception
{
    public function __construct($position)
    {
        parent::__construct('Multiple order items at position ' . $position);
    }
}
