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

}