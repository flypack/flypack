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

}