<?php

/**
 * QueryCacher
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.4
 */

namespace fly\database;

use fly\Database;

class QueryCacher extends QueryPreparer
{

    /**
     * Get cache status for this query
     *
     * @return bool
     */
    protected function getCacheStatus()
    {
        return Database::getCacheDefault();
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string
     */
    protected function getCacheHash($sql, $params)
    {
        return md5($sql) . md5(serialize($params));
    }

    protected function getExecutedCachedData()
    {
        $hash = $this->getCacheHash($this->preparedSQL, $this->preparedParams);

        if (!isset(Database::$cacheStorage[$hash])) {
            // hash not found, return false
            return FALSE;
        }

        foreach (Database::$cacheStorage[$hash] as $row) {
            if ($row['sql'] === $this->preparedSQL && $row['params'] === $this->preparedParams) {
                return $row['data'];
            }
        }

        // no equal query
        return FALSE;
    }

    protected function cacheExecutedData($data)
    {
        $hash = $this->getCacheHash($this->preparedSQL, $this->preparedParams);

        if (!isset(Database::$cacheStorage[$hash])) {
            // hash not found, create
            Database::$cacheStorage[$hash] = [];
        }

        Database::$cacheStorage[$hash][] = [
            'sql' => $this->preparedSQL,
            'params' => $this->preparedParams,
            'data' => $data,
        ];
    }

}