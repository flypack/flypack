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
    public function one($returnAsArray = 1)
    {
        return $this->offset(0)->limit(1)->run($returnAsArray);
    }

    /**
     * Public method for execute query with LIMIT = ALL
     *
     * @param int $returnAsArray
     *
     * @return $this|array
     * @throws \Exception
     */
    public function all($returnAsArray = 1)
    {
        return $this->offset(0)->limit(0)->run($returnAsArray);
    }

    /**
     * @return array
     */
    private function returnAsArray()
    {
        return $this->parsed;
    }

}