<?php

/**
 * QueryExecuter
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

use fly\Database;

class QueryExecuter extends QueryPreparer
{

    /**
     * Execute query
     *
     * @return $this
     * @throws \Exception
     */
    protected function execute()
    {
        if ($this->_checkQueryTypeAssigned('SELECT')) {
            $this->_executeSelect()->_executeRelations();
        }

        return $this;
    }

    /**
     * Execute main query
     *
     * @return $this
     */
    private function _executeSelect()
    {
        $this->executed['main'] = Database::SQL($this->preparedSQL, $this->preparedParams);

        return $this;
    }

    /**
     * @throws \Exception
     */
    private function _executeRelationsBefore()
    {
        $this->_executeRelationsBeforeCopy();
        $this->_executeRelationsBeforeGetValues();
        $this->_executeRelationsBeforeExecuteQueries();
        $this->_executeRelationsBeforeSetRelationsValues();
    }

    private function _executeRelationsBeforeCopy()
    {
        foreach ($this->relations as $key => $data) {
            $this->executed['relations'][$key]['data'] = $data;
        }
    }

    private function _executeRelationsBeforeGetValues()
    {
        foreach ($this->executed['relations'] as $key => &$relation) {
            $relation['values'] = [];
            $field = $relation['data']['conditions'][1];

            foreach ($this->executed['main'] as $row) {
                if (isset($row[$field])) {
                    $relation['values'][] = $row[$field];
                }
            }

            if (count($relation['values'])) {
                $relation['values'] = array_unique($relation['values']);
                sort($relation['values']);
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function _executeRelationsBeforeExecuteQueries()
    {
        foreach ($this->executed['relations'] as $key => &$relation) {
            $relation['query'] = Database::Query()
                ->select()
                ->from($relation['data']['table'])
                ->where([$relation['data']['conditions'][0], $relation['values']])
                ->limit(0)
                ->all();
        }
    }

    private function _executeRelationsBeforeSetRelationsValues()
    {
        foreach ($this->executed['main'] as &$row) {
            foreach ($this->executed['relations'] as $relationName => &$relation) {
                //print_r([$row, $relationName, $relation]);
                switch ($relation['data']['type']) {
                    case 'one':
                        $row[$relationName] = FALSE;
                        foreach ($relation['query'] as $relationRow) {
                            if ($relationRow[$relation['data']['conditions'][0]] == $row[$relation['data']['conditions'][1]]) {
                                $row[$relationName] = $relationRow;
                            }
                        }
                        break;
                    case 'many':
                        $row[$relationName] = [];
                        foreach ($relation['query'] as $relationRow) {
                            if ($relationRow[$relation['data']['conditions'][0]] == $row[$relation['data']['conditions'][1]]) {
                                $row[$relationName][] = $relationRow;
                            }
                        }
                        break;
                }
            }
        }
    }

    /**
     * Execute relationships
     *
     * @return $this
     * @throws \Exception
     */
    private function _executeRelations()
    {
        $this->_executeRelationsBefore();

        return $this;
    }

}