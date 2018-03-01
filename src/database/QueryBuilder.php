<?php

/**
 * QueryBuilder
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

use fly\helpers\ArrayHelper;

class QueryBuilder extends QueryValidator
{

    // INSERT

    /**
     * @return $this
     * @throws \Exception
     */
    public function insert()
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetInsert()) {
            throw new \Exception('fly\Database: Query type is not a INSERT');
        }

        return $this;
    }

    /**
     * @param string $table
     *
     * @return $this
     * @throws \Exception
     */
    public function into($table)
    {
        // Check query type
        if (!$this->_checkQueryTypeAssigned('INSERT')) {
            throw new \Exception('fly\Database: Query type required an INSERT');
        }

        // Check table name
        if (!$this->_isTableNameValid($table)) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid table name');
        }

        // Set table name to INTO part
        $this->insertInto = $table;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function values($data)
    {
        // Check query type
        if (!$this->_checkQueryTypeAssigned('INSERT')) {
            throw new \Exception('fly\Database: Query type required an INSERT');
        }

        // Check data
        if (!is_array($data) || (!ArrayHelper::isArrayStrong($data) && !ArrayHelper::isArray2DStrong($data))) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid data array');
        }

        // one-level array
        if (ArrayHelper::isArrayStrong($data)) {
            $this->insertValues[] = $data;
        }

        // two-level array
        if (ArrayHelper::isArray2DStrong($data)) {
            foreach ($data as $row) {
                $this->insertValues[] = $row;
            }
        }

        return $this;
    }

    /* SELECT methods */

    /**
     * Clear select fields and set query format to ALL
     *
     * @return $this
     * @throws \Exception
     */
    public function clearSelect()
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetSelect()) {
            throw new \Exception('fly\Database: Query type is not a SELECT');
        }

        // Clear format and SELECT conditions
        $this->select = [];

        return $this;
    }

    /**
     * Add field or fields for select query
     * Format: '*' OR 'field' OR ['field1', 'field2', ... ]
     *
     * @param string|array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function addSelect($data = '*')
    {
        // Check query type
        if (!$this->_checkQueryTypeAvailableAndSetSelect()) {
            throw new \Exception('fly\Database: Query type is not a SELECT');
        }

        // TODO: rewrite this part of method

        if (ArrayHelper::isArrayStrong($data)) {
            // it is one-level array of new fields
            foreach ($data as $field) {
                if ($this->_isFieldNameValid($field)) {
                    $this->select[] = $field;
                } else {
                    throw new \Exception('fly\Database: Field name in method addSelect() is not valid');
                }
            }

            return $this;
        }

        if (!ArrayHelper::isArray($data) && $this->_isFieldNameValid($data)) {
            // it is a one field
            $this->select[] = $data;

            return $this;
        }

        if ($data === '*') {
            // all fields
            $this->select = ['*'];

            return $this;
        }

        throw new \Exception('fly\Database: Expects parameter 1 to be a valid data array');
    }

    /**
     * Clear select fields and add new fields
     * Format: '*' OR 'field' OR ['field1', 'field2', ... ]
     *
     * @param string|array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function select($data = '*')
    {
        return $this->clearSelect()->addSelect($data);
    }

    /**
     * Set '*' in fields for select query
     *
     * @return $this
     * @throws \Exception
     */
    public function selectAll()
    {
        return $this->select('*');
    }

    /* FROM methods */

    /**
     * Clear FROM part of query
     *
     * @return $this
     */
    public function clearFrom()
    {
        $this->from = [];

        return $this;
    }

    /**
     * Add table name to FROM part of query
     * Format: 'table' OR ['table'] OR ['table', 'alias']
     *
     * @param string|array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function addFrom($data)
    {
        if (ArrayHelper::isArrayStrong($data)) {
            // it is array
            switch (count($data)) {
                case 1:
                    // format: ['table']
                    $tableName = array_shift($data);
                    if ($this->_isTableNameValid($tableName)) {
                        $this->from[] = [
                            $tableName,
                        ];
                    }
                    return $this;
                case 2:
                    // format: ['table', 'alias']
                    $tableName = array_shift($data);
                    $tableAlias = array_shift($data);
                    if ($this->_isTableNameValid($tableName) && $this->_isTableAliasValid($tableAlias)) {
                        $this->from[] = [
                            $tableName,
                            $tableAlias,
                        ];
                    }
                    return $this;
                default:
                    throw new \Exception('fly\Database: Expects parameter 1 to be a valid table name');
            }
        }

        if ($this->_isTableNameValid($data)) {
            // format: 'table'
            $this->from[] = [
                $data
            ];
            return $this;
        }

        throw new \Exception('fly\Database: Expects parameter 1 to be a valid table name');
    }

    /**
     * Clear FROM part of query and new table name
     *
     * @param string|array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function from($data)
    {
        return $this->clearFrom()->addFrom($data);
    }

    /* WHERE methods */

    /**
     * Clear WHERE part of query
     *
     * @return $this
     */
    public function clearWhere()
    {
        $this->where = [];

        return $this;
    }

    /**
     * Transfer data conditions to true conditions of WHERE query part
     *
     * @param array $row
     *
     * @return array
     * @throws \Exception
     */
    private function _analyzeOneWhere($row)
    {
        if (ArrayHelper::isArrayStrong($row)) {
            // one-level array
            if (count($row) === 1) {
                // format: [ SQL ]
                return [
                    array_shift($row)
                ];
            }

            if (count($row) === 2) {
                // format: ['field', 'value']
                // change to
                // format: ['field', 'value', '=']
                $row[] = '=';
            }

            if (count($row) === 3 && in_array($row[2], ['=', '>', '<', '>=', '<=', '<>'])) {
                // format: ['field', 'value', '?']
                list($fieldName, $fieldValue, $fieldOperator) = $row;
                if ($this->_isFieldNameValid($fieldName)) {
                    return [
                        $fieldName,
                        $fieldValue,
                        $fieldOperator,
                    ];
                }
            }
        }

        if (ArrayHelper::isArray2D($row)) {
            // two-level array
            if (count($row) === 2 && ArrayHelper::isArray($row[1]) && count($row[1])) {
                // format: ['field', ['value1', 'value2', 'value3']]
                list($fieldName, $fieldValues) = $row;
                if ($this->_isFieldNameValid($fieldName)) {
                    return [
                        $fieldName,
                        $fieldValues,
                        'IN',
                    ];
                }
            }
        }

        throw new \Exception('fly\Database: Expects parameter 1 to be a valid sql condition');
    }

    /**
     * Transfer data conditions to true conditions of WHERE query part
     *
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    private function _analyzeManyWhere($data)
    {
        $conditions = [];

        if (ArrayHelper::isArray2DStrongMin($data)) {
            // min two-level strong array with several conditions
            $conditions_child = [];

            foreach ($data as $row) {
                $conditions_buffer = $this->_analyzeManyWhere($row);
                if (count($conditions_buffer)) {
                    // have result
                    foreach ($conditions_buffer as $conditions_buffer_element) {
                        // write to conditions array one by one
                        $conditions_child[] = $conditions_buffer_element;
                    }
                }
            }

            // add child conditions to main conditions array
            $conditions[] = $conditions_child;
        } else {
            // not a two-level strong array with one condition
            $conditions[] = $this->_analyzeOneWhere($data);
        }

        return $conditions;
    }

    /**
     * Add WHERE conditions with AND operator
     *
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function andWhere($data)
    {
        // get array of conditions
        $conditions = $this->_analyzeManyWhere($data);

        // write result to $this->where
        if (count($conditions)) {
            foreach ($conditions as $condition) {
                if (is_array($condition) && count($condition)) {
                    $this->where[] = $condition;
                }
            }
        }

        return $this;
    }

    /**
     * Add WHERE conditions with OR operator
     *
     * @param $data
     *
     * @return $this
     * @throws \Exception
     */
    public function orWhere($data)
    {
        // get array of conditions
        $conditions = $this->_analyzeManyWhere($data);

        // write result to $this->where with operator OR
        if (count($conditions)) {
            // add new conditions level
            // transfer elements of $this->where to child conditions
            // add element ['OR']
            // add new conditions
            $this->where = [
                $this->where,
                ['OR'],
                $conditions,
            ];
        }

        return $this;
    }

    /**
     * Clear WHERE part of query and add new conditions
     *
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function where($data = [])
    {
        return $this->clearWhere()->andWhere($data);
    }

    /* ORDER BY methods */

    /**
     * Clear ORDER BY part of query
     *
     * @return $this
     */
    public function clearOrderBy()
    {
        $this->orderBy = [];

        return $this;
    }

    /**
     * Transfer data conditions to true conditions of ORDER BY query part
     *
     * @param array $row
     *
     * @return bool
     * @throws \Exception
     */
    private function _addOneOrderBy($row)
    {
        // format: [ field ]
        // or format: [ field , ASC ]
        if (!ArrayHelper::isArrayStrong($row)) {
            return FALSE;
        }

        // format: [ field ]
        // change to format: [ field , ASC ]
        if (count($row) === 1) {
            $row[] = 'ASC';
        }

        if (count($row) !== 2) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid order array');
        }

        list($fieldName, $fieldSort) = $row;

        if (!$this->_isFieldNameValid($fieldName)) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid order array with valid field name');
        }

        $this->orderBy[] = [
            $fieldName,
            $fieldSort,
        ];

        return TRUE;
    }

    /**
     * Add new ORDER BY conditions to query
     * Format: 'field' OR ['field'] OR ['field', 'DESC'] OR [['field1', 'DESC'], ['field2'], ...]
     *
     * TODO: add format ['field1', 'field2', 'field3', ...]
     *
     * @param string|array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function addOrderBy($data)
    {
        if (ArrayHelper::isArray2DStrong($data)) {
            // two-level array
            foreach ($data as $row) {
                if (!$this->_addOneOrderBy($row)) {
                    throw new \Exception('fly\Database: Expects parameter 1 to be a valid order');
                }
            }

            return $this;
        }

        if (ArrayHelper::isArrayStrong($data)) {
            // one-level array
            // one element
            if (!$this->_addOneOrderBy($data)) {
                throw new \Exception('fly\Database: Expects parameter 1 to be a valid order');
            }

            return $this;
        }

        if ($this->_isFieldNameValid($data)) {
            // string, only field name
            if (!$this->_addOneOrderBy([$data])) {
                throw new \Exception('fly\Database: Expects parameter 1 to be a valid order');
            }

            return $this;
        }

        throw new \Exception('fly\Database: Expects parameter 1 to be a valid order');
    }

    /**
     * Clear ORDER BY part of query and add new conditions
     *
     * @param string|array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function orderBy($data = [])
    {
        return $this->clearOrderBy()->addOrderBy($data);
    }

    /* OFFSET and LIMIT methods */

    /**
     * Set OFFSET part of query
     *
     * @param int $value
     *
     * @return $this
     * @throws \Exception
     */
    public function offset($value = 0)
    {
        if (!is_numeric($value)) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid number');
        }

        $this->offset = $value;

        return $this;
    }

    /**
     * Set LIMIT part of query or OFFSET and LIMIT
     * Format: 'limit' OR ['offset', 'limit']
     *
     * @param int|array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function limit($data = 0)
    {
        if (ArrayHelper::isArrayStrong($data)) {
            // array
            if (count($data) == 2) {
                // format: [ offset , limit ]
                $offset = array_shift($data);
                $limit = array_shift($data);

                return $this->offset($offset)->limit($limit);
            } else {
                // wrong array elements count
                throw new \Exception('fly\Database: Expects parameter 1 to be a valid array with offset and limit');
            }
        }

        if (!is_numeric($data)) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid limit');
        }

        if ($this->_checkQueryTypeAssigned('SELECT') && ($this->_checkResultFormatAssigned('ROW') || $this->_checkResultFormatAssigned('VALUE')) && $data !== 1) {
            throw new \Exception('fly\Database: ROW and VALUE formats need limit = 1');
        }

        $this->limit = $data;

        return $this;
    }

    /* RELATION methods */

    /**
     * Add relationship to query
     *
     * @param string $relationName
     * @param string $relationType
     * @param string $tableName
     * @param array  $conditions
     *
     * @return $this
     * @throws \Exception
     */
    private function _addRelation($relationName, $relationType, $tableName, $conditions)
    {
        // Relation name exists check
        if (isset($this->relations[$relationName])) {
            throw new \Exception('fly\Database: Relation name is already exists');
        }
        // Relation name check
        if (!$this->_isRelationNameValid($relationName)) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid relation name');
        }
        // Relation type check
        if (!$this->_isRelationTypeValid($relationType)) {
            throw new \Exception('fly\Database: Expects parameter 2 to be a valid relation type');
        }
        // Table name check
        if (!$this->_isTableNameValid($tableName)) {
            throw new \Exception('fly\Database: Expects parameter 3 to be a valid table name');
        }
        // Conditions check
        if (!$this->_isRelationConditionsValid($conditions)) {
            throw new \Exception('fly\Database: Expects parameter 4 to be a valid conditions array');
        }

        // Add new relation
        $this->relations[$relationName] = [
            'type' => $relationType,
            'table' => $tableName,
            'conditions' => $conditions,
        ];

        return $this;
    }

    /**
     * Add relationship one-to-one to query
     *
     * @param string $relationName
     * @param string $table
     * @param array  $conditions
     *
     * @return $this
     * @throws \Exception
     */
    public function hasOne($relationName, $table, $conditions)
    {
        return $this->_addRelation($relationName, 'one', $table, $conditions);
    }

    /**
     * Add relationship one-to-many to query
     *
     * @param string $relationName
     * @param string $table
     * @param array  $conditions
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function hasMany($relationName, $table, $conditions)
    {
        return $this->_addRelation($relationName, 'many', $table, $conditions);
    }

}