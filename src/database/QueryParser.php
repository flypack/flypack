<?php

/**
 * QueryParser
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

class QueryParser extends QueryExecuter
{

    /**
     * Parse result of query
     *
     * @return $this
     */
    protected function parse()
    {
        return $this->_parse();
    }

    /**
     * TODO: divide to methods by result format
     *
     * @return $this
     */
    private function _parse()
    {
        switch ($this->resultFormat) {
            case 'ALL':
                $this->parsed = $this->executed['main'];
                break;
            case 'ROW':
                if (count($this->executed['main'])) {
                    $this->parsed = $this->executed['main'][0];
                }
                break;
            case 'COLUMN':
                if (count($this->executed['main'])) {
                    foreach ($this->executed['main'] as $i => $buf) {
                        $this->parsed[] = $this->executed['main'][$i][$this->select[0]];
                    }
                }
                break;
            case 'VALUE':
                if (count($this->executed['main'])) {
                    $this->parsed = $this->executed['main'][0][$this->select[0]];
                }
                break;
        }

        return $this;
    }

}