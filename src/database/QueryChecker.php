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

    // Query type functions

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function _checkQueryTypeAssigned($type)
    {
        if ($this->queryType === $type) {
            // Query type is assigned to $type
            return TRUE;
        }

        // Query type is assigned to another type or not assigned
        return FALSE;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function _checkQueryTypeAvailable($type)
    {
        if ($this->queryType === FALSE) {
            // Query type is not assigned
            return TRUE;
        }

        if ($this->_checkQueryTypeAssigned($type)) {
            // Query type is assigned to $type
            return TRUE;
        }

        // Query type is assigned to another type
        return FALSE;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function _checkQueryTypeAvailableAndSet($type)
    {
        if (!$this->_checkQueryTypeAvailable($type)) {
            // Query type is assigned to another type
            return FALSE;
        }

        // Set query type to $type
        $this->queryType = $type;

        return TRUE;
    }

    /**
     * @return bool
     */
    protected function _checkQueryTypeAvailableAndSetSelect()
    {
        return $this->_checkQueryTypeAvailableAndSet('SELECT');
    }

    /**
     * @return bool
     */
    protected function _checkQueryTypeAvailableAndSetInsert()
    {
        return $this->_checkQueryTypeAvailableAndSet('INSERT');
    }

    // Result format functions

    /**
     * @param string $format
     *
     * @return bool
     */
    protected function _checkResultFormatAssigned($format)
    {
        if ($this->resultFormat === $format) {
            // Result format is assigned to $format
            return TRUE;
        }

        // Result format is assigned to another format or not assigned
        return FALSE;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    protected function _checkResultFormatAvailable($format)
    {
        if ($this->resultFormat === FALSE) {
            // Result format is not assigned
            return TRUE;
        }

        if ($this->_checkResultFormatAssigned($format)) {
            // Result format is assigned to $format
            return TRUE;
        }

        // Result format is assigned to another format
        return FALSE;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    private function _checkResultFormatAvailableAndSet($format)
    {
        if (!$this->_checkResultFormatAvailable($format)) {
            // Result format is assigned to another format
            return FALSE;
        }

        // Set result format to $format
        $this->resultFormat = $format;

        return TRUE;
    }

    /**
     * @return bool
     */
    protected function _checkResultFormatAvailableAndSetAll()
    {
        return $this->_checkResultFormatAvailableAndSet('ALL');
    }

    /**
     * @return bool
     */
    protected function _checkResultFormatAvailableAndSetRow()
    {
        return $this->_checkResultFormatAvailableAndSet('ROW');
    }

    /**
     * @return bool
     */
    protected function _checkResultFormatAvailableAndSetColumn()
    {
        return $this->_checkResultFormatAvailableAndSet('COLUMN');
    }

    /**
     * @return bool
     */
    protected function _checkResultFormatAvailableAndSetValue()
    {
        return $this->_checkResultFormatAvailableAndSet('VALUE');
    }

}