<?php

declare(strict_types = 1);

namespace Kutny\DoctrineBundle\Db\MySql;

use Doctrine\DBAL\Connection;

class AutoincrementGapCreator
{
    private $db;

    public function __construct(
        Connection $db
    ) {
        $this->db = $db;
    }

    public function createGap(string $tableName, string $dbName, int $gapSize): string
    {
        $this->db->query('
            BEGIN;

            SET @currentAutoIncrement = (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = ' . $this->db->quote($dbName) . ' AND TABLE_NAME = ' . $this->db->quote($tableName) . ');
            SET @newAutoIncrement = @currentAutoIncrement + ' . $gapSize . ';
            SET @alterAutoIncrementSql = CONCAT("ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = ", @newAutoIncrement);
            
            SELECT @alterAutoIncrementSql;
            PREPARE st FROM @alterAutoIncrementSql;
            EXECUTE st;
            
            COMMIT;
        ');

        $startingId = $this->db->query('SELECT @currentAutoIncrement')->fetchColumn();

        if ($startingId < 1) {
            throw new \Exception('currentAutoIncrement is ' . var_export($startingId, true) . ', but must be >= 1');
        }

        return $startingId;
    }
}
