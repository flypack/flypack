<?php

/**
 * QueryMain
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

class QueryMain extends QueryParser
{

    /**
     * Public method for execute query
     *
     * @param int $returnAsArray
     *
     * @return $this|array
     * @throws \Exception
     */
    public function run($returnAsArray = 1)
    {
        $this->prepare()->execute();
        $this->parse();

        return ($returnAsArray ? $this->returnAsArray() : $this);
    }

    /**
     * Public method for execute query with LIMIT = ONE
     *
     * @param int $returnAsArray
     *
     * @return $this|array
     * @throws \Exception
     */
    public function row($returnAsArray = 1)
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetSelect()) {
            throw new \Exception('fly\Database: Query type is not a SELECT');
        }

        // Check query result format
        if (!$this->_checkResultFormatAvailableAndSetRow()) {
            throw new \Exception('fly\Database: Query result format is not a ROW');
        }

        // Set LIMIT 1
        $this->limit(1);

        return $this->run($returnAsArray);
    }

    /**
     * Public method for execute query with LIMIT = ALL
     *
     * @param int|array $limit
     * @param int       $returnAsArray
     *
     * @return $this|array
     * @throws \Exception
     */
    public function all($limit = 0, $returnAsArray = 1)
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetSelect()) {
            throw new \Exception('fly\Database: Query type is not a SELECT');
        }

        // Check query result format
        if (!$this->_checkResultFormatAvailableAndSetAll()) {
            throw new \Exception('fly\Database: Query result format is not a ALL');
        }

        return $this->limit($limit)->run($returnAsArray);
    }

    /**
     * @return array
     */
    private function returnAsArray()
    {
        return $this->parsed;
    }

}