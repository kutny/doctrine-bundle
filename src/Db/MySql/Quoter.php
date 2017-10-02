<?php

namespace Kutny\DoctrineBundle\Db\MySql;

use Doctrine\ORM\EntityManager;
use Kutny\DoctrineBundle\Db\IQuoter;

class Quoter implements IQuoter
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $value to be escaped
     * @return string
     */
    public function quote($value)
    {
        if ($value === false) {
            return '0';
        }
        else if ($value === true) {
            return '1';
        }
        else if ($value === null) {
            return 'NULL';
        }

        return $this->entityManager->getConnection()->quote($value);
    }

    /**
     * @param string $identifier to be escaped
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        return $this->entityManager->getConnection()->quoteIdentifier($identifier);
    }
}
