<?php

namespace Kutny\DoctrineBundle\Collection\ArrayList;

class StringArrayList extends ArrayList
{
    public function join($glue)
    {
        return implode($glue, $this->items);
    }

    public static function explode($delimiter, $string)
    {
        return new StringArrayList(explode($delimiter, $string));
    }
}
