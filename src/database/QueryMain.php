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
     * Private method for execute query
     *
     * @return $this|array|int
     * @throws \Exception
     */
    public function run()
    {
        $this->prepare()->execute();

        if ($this->_checkQueryTypeAssigned('SELECT')) {
            $this->parse();
            return $this->returnAsArray();
        }

        if ($this->_checkQueryTypeAssigned('INSERT-VALUES')) {
            return $this->executedRows;
        }

        throw new \Exception('fly\Database: Invalid query type');
    }

    /**
     * Public method for execute query with LIMIT = ALL
     *
     * @param bool|int|array $limit
     *
     * @return $this|array
     * @throws \Exception
     */
    public function all($limit = FALSE)
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetSelect()) {
            throw new \Exception('fly\Database: Query type is not a SELECT');
        }

        // Check query result format
        if (!$this->_checkResultFormatAvailableAndSetAll()) {
            throw new \Exception('fly\Database: Query result format is not a ALL');
        }

        // Check $limit
        if ((is_numeric($limit) && $limit >= 0) || is_array($limit)) {
            $this->limit($limit);
        }

        return $this->run();
    }

    /**
     * Public method for execute query with LIMIT = ONE
     *
     * @return $this|array
     * @throws \Exception
     */
    public function row()
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

        return $this->run();
    }

    /**
     * @param bool|int|array $limit
     *
     * @return array|$this
     * @throws \Exception
     */
    public function column($limit = FALSE)
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetSelect()) {
            throw new \Exception('fly\Database: Query type is not a SELECT');
        }

        // Check count of select fields
        if (!count($this->select) || count($this->select) > 1 || $this->select[0] === '*') {
            throw new \Exception('fly\Database: Query result format COLUMN require only one field in SELECT part');
        }

        // Check query result format
        if (!$this->_checkResultFormatAvailableAndSetColumn()) {
            throw new \Exception('fly\Database: Query result format is not a COLUMN');
        }

        // Check $limit
        if ((is_numeric($limit) && $limit >= 0) || is_array($limit)) {
            $this->limit($limit);
        }

        return $this->run();
    }

    /**
     * @return array|$this
     * @throws \Exception
     */
    public function value()
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetSelect()) {
            throw new \Exception('fly\Database: Query type is not a SELECT');
        }

        // Check count of select fields
        if (!count($this->select) || count($this->select) > 1 || $this->select[0] === '*') {
            throw new \Exception('fly\Database: Query result format VALUE require only one field in SELECT part');
        }

        // Check query result format
        if (!$this->_checkResultFormatAvailableAndSetValue()) {
            throw new \Exception('fly\Database: Query result format is not a VALUE');
        }

        // Set LIMIT = 1
        $this->limit(1);

        return $this->run();
    }

    /**
     * @return array
     */
    private function returnAsArray()
    {
        return $this->parsed;
    }

}