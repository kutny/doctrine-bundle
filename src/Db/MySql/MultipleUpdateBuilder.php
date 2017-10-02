<?php

namespace Kutny\DoctrineBundle\Db\MySql;

use Symfony\Component\Config\Definition\Exception\Exception;

class MultipleUpdateBuilder
{
    const TABLE_ALIAS = 't';
    const SUBQUERY_ALIAS = 's';

    private $quoter;

    public function __construct(
        Quoter $quoter
    ) {
        $this->quoter = $quoter;
    }

    public function build($tableName, array $data)
    {
        $firstRow = reset($data);
        if (count($data) === 0 || count($firstRow) === 0) {
            throw new Exception('Data must contain at least one row');
        }
        $firstColumn = $this->arrayKshift($firstRow);
        if (count($firstRow) < 1) {
            throw new Exception('Data must contain at least two columns');
        }

        return '
            UPDATE ' . $tableName .' ' . self::TABLE_ALIAS . '
            JOIN (
                ' . $this->buildSubQueries($data) . '
            ) ' . self::SUBQUERY_ALIAS . '
            ON ' . self::TABLE_ALIAS . '.' . key($firstColumn) . ' = ' . self::SUBQUERY_ALIAS . '.' . key($firstColumn) . '
            SET ' . $this->buildUpdatePart($firstRow) . '
        ';
    }

    private function buildUpdatePart(array $firstRow)
    {
        $updatePart = [];
        foreach ($firstRow as $key => $value) {
            $updatePart[] = self::TABLE_ALIAS . '.' . $key . ' = ' . self::SUBQUERY_ALIAS . '.' . $key;
        }

        return implode(', ', $updatePart);
    }

    private function buildSubQueries(array $data)
    {
        $subQueries = [];
        $firstRow = array_shift($data);
        $subQueries[] = $this->buildFirstRow($firstRow);
        foreach ($data as $row) {
            $subQueries[] = $this->buildRow($row, array_keys($firstRow));
        }

        return implode(' UNION ALL ', $subQueries);
    }

    private function buildFirstRow(array $row)
    {
        $columns = [];
        foreach ($row as $key => $value) {
            if (is_numeric($key) || $key === '') {
                throw new Exception('Data keys must have names');
            }
            $columns[] = $this->quoter->quote($value) . ' as ' . $key;
        }

        return $this->buildSelect($columns);
    }

    private function buildRow(array $row, $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = array_key_exists($key, $row) ? $this->quoter->quote($row[$key]) : 'NULL';
        }

        return $this->buildSelect($result);
    }

    private function buildSelect(array $columns)
    {
        return 'SELECT ' . implode(', ', $columns);
    }

    private function arrayKshift(&$array)
    {
        if (is_array($array) && count($array) > 0) {
            list ($k) = array_keys($array);
            $result = [$k => $array[$k]];
            unset($array[$k]);

            return $result;
        }

        throw new \InvalidArgumentException('Invalid array');
    }
}
