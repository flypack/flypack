<?php

/**
 * QueryStarter
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

class QueryStarter
{

    // For QueryBuilder Class

    protected $queryType = FALSE;
    protected $resultFormat = FALSE;

    protected $insertInto = '';
    protected $insertValues = [];

    protected $select = [];
    protected $from = [];
    protected $where = [];
    protected $orderBy = [];
    protected $offset = 0;
    protected $limit = 0;

    protected $relations = [];

    // For QueryPreparer Class

    protected $prepared = [];
    protected $preparedSQL = '';
    protected $preparedParams = [];
    protected $preparedParamNumber = 0;

    // For QueryExecuter Class

    protected $executed = [
        'main' => [],
        'relations' => [],
    ];

    protected $executedRows = 0;

    // For QueryParser Class

    protected $parsed = [];

}