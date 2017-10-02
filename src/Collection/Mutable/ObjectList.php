<?php

namespace Kutny\DoctrineBundle\Collection\Mutable;

use Kutny\DoctrineBundle\Collection\Imutable\Map;

class ObjectList extends Map
{
    public function put($key, $value)
    {
        parent::put($key, $value);
    }
}
