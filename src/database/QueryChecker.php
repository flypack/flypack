<?php

/**
 * QueryChecker
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

class QueryChecker extends QueryStarter
{

    /**
     * Check and set query type
     *
     * @param string $type
     *
     * @return bool
     */
    protected function _isQueryType($type)
    {
        // check type of query
        if ($this->queryType !== FALSE && $this->queryType !== $type) {
            return FALSE;
        }

        // set query type
        $this->queryType = $type;

        return TRUE;
    }

    /**
     * Check and set SELECT query type
     *
     * @return bool
     */
    protected function _isQueryTypeSelect()
    {
        return $this->_isQueryType('SELECT');
    }

    /**
     * Check and set query result format
     *
     * @param string $format
     *
     * @return bool
     */
    protected function _isResultFormat($format)
    {
        // check format of query result
        if ($this->resultFormat !== FALSE && $this->resultFormat !== $format) {
            return FALSE;
        }

        // set query result format
        $this->resultFormat = $format;

        return TRUE;
    }

    /**
     * Check and set query result format ALL
     *
     * @return bool
     */
    protected function _isResultFormatAll()
    {
        return $this->_isResultFormat('ALL');
    }

    /**
     * Check and set query result format ROW
     *
     * @return bool
     */
    protected function _isResultFormatRow()
    {
        return $this->_isResultFormat('ROW');
    }

    /**
     * Check and set query result format COLUMN
     *
     * @return bool
     */
    protected function _isResultFormatColumn()
    {
        return $this->_isResultFormat('COLUMN');
    }

    /**
     * Check and set query result format VALUE
     *
     * @return bool
     */
    protected function _isResultFormatValue()
    {
        return $this->_isResultFormat('VALUE');
    }

}