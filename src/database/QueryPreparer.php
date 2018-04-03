<?php

/**
 * QueryPreparer
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

use fly\helpers\ArrayHelper;

class QueryPreparer extends QueryBuilder
{

    /**
     * Prepare query for execute
     *
     * @return $this
     * @throws \Exception
     */
    protected function prepare()
    {
        $this->_prepare();
        $this->_prepareSQL();

        return $this;
    }

    /**
     * Prepare query for execute by query types
     *
     * @return $this
     * @throws \Exception
     */
    private function _prepare()
    {
        if ($this->_checkQueryTypeAssigned('SELECT')) {
            $this->_prepareSelect();

            return $this;
        }

        if ($this->_checkQueryTypeAssigned('INSERT-VALUES')) {
            return $this->_prepareInsertValues();
        }

        if ($this->_checkQueryTypeAssigned('UPDATE')) {
            return $this->_prepareUpdate();
        }

        throw new \Exception('fle\Database: Wrong query type');
    }

    /**
     * Prepare SELECT query for execute
     *
     * @return $this
     * @throws \Exception
     */
    private function _prepareSelect()
    {
        $this->_preparePartSelect();
        $this->_preparePartFrom();
        $this->_preparePartWhere();
        $this->_preparePartOrderBy();
        $this->_preparePartLimit();

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function _prepareInsertValues()
    {
        $this->_preparePartInsert();
        $this->_preparePartInto();
        $this->_preparePartValues();

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function _prepareUpdate()
    {
        $this->_preparePartUpdate();
        $this->_preparePartSet();
        $this->_preparePartWhere();
        $this->_preparePartOrderBy();
        $this->_preparePartLimit();

        return $this;
    }

    /**
     * Prepare SQL for execute
     *
     * @return $this
     */
    private function _prepareSQL()
    {
        if ($this->_checkQueryTypeAssigned('SELECT') || $this->_checkQueryTypeAssigned('UPDATE')) {
            $this->preparedSQL = trim(implode(' ', $this->prepared)) . ';';
        }

        if ($this->_checkQueryTypeAssigned('INSERT-VALUES')) {
            $this->preparedSQL = [];
            foreach ($this->prepared['VALUES'] as $key => $valuesPart) {
                $this->preparedSQL[$key] = $this->prepared['INSERT'] . ' ' . $this->prepared['INTO'] . ' ' . $valuesPart . ';';
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function _preparePartInsert()
    {
        $this->prepared['INSERT'] = 'INSERT';

        return $this;
    }

    /**
     * @return $this
     */
    private function _preparePartInto()
    {
        $this->prepared['INTO'] = 'INTO `' . $this->insertInto . '`';

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function _preparePartValues()
    {
        if (ArrayHelper::isArray2DStrong($this->insertValues)) {
            // Insert many rows
            $this->prepared['VALUES'] = [];

            foreach ($this->insertValues as $valuesKey => $data) {
                $this->prepared['VALUES'][$valuesKey] = $this->_preparePartValuesOne($data, $valuesKey);
            }
        } else {
            // Insert one row
            $this->prepared['VALUES'] = $this->_preparePartValuesOne($this->insertValues);
        }

        return $this;
    }

    private function _preparePartValuesOne($data, $preparedKey = FALSE)
    {
        $keys = $values = [];
        $this->_resetPreparedParamNumber();

        foreach ($data as $key => $value) {
            $keys[] = $key;
            if ($preparedKey !== FALSE) {
                $values[] = $this->_setPrepareParam($value, $preparedKey);
            } else {
                $values[] = $this->_setPrepareParam($value);
            }
        }

        return '(`' . implode('`, `', $keys) . '`) VALUES (' . implode(', ', $values) . ')';
    }

    private function _preparePartUpdate()
    {
        $this->prepared['UPDATE'] = 'UPDATE `' . $this->update . '`';

        return $this;
    }

    private function _preparePartSet()
    {
        $buffer = [];

        foreach ($this->set as $row) {
            $buffer[] = '`' . $row['field'] . '` = ' . $this->_setPrepareParam($row['value']);
        }

        $this->prepared['SET'] = 'SET ' . implode(', ', $buffer);

        return $this;
    }

    /**
     * Prepare SELECT part
     *
     * @return $this
     */
    private function _preparePartSelect()
    {
        $buffer = [];

        foreach ($this->select as $row) {
            if ($row === '*') {
                $buffer[] = '*';
            } else {
                $buffer[] = '`' . str_replace('.', '`.`', $row) . '`';
            }
        }

        $this->prepared['SELECT'] = 'SELECT ' . implode(', ', $buffer);

        return $this;
    }

    /**
     * Prepare FROM part
     *
     * @return $this
     */
    private function _preparePartFrom()
    {
        $buffer = [];

        foreach ($this->from as $row) {
            if (count($row) === 1) {
                // format: [ table ]
                $buffer[] = '`' . $row[0] . '`';
            } else {
                // format: [ table , alias ]
                $buffer[] = '`' . $row[0] . '` AS `' . $row[1] . '`';
            }
        }

        $this->prepared['FROM'] = 'FROM ' . implode(', ', $buffer);

        return $this;
    }

    /**
     * Prepare WHERE part
     *
     * @return $this
     * @throws \Exception
     */
    private function _preparePartWhere()
    {
        $this->prepared['WHERE'] = '';

        $this->prepared['WHERE'] = $this->_preparePartWhereMany($this->where);

        if (str_replace(['(', ')'], '', $this->prepared['WHERE']) !== '') {
            $this->prepared['WHERE'] = 'WHERE ' . str_replace('AND OR AND', 'OR', $this->prepared['WHERE']);
        } else {
            $this->prepared['WHERE'] = '';
        }

        return $this;
    }

    /**
     * @param array $conditions
     *
     * @return string
     * @throws \Exception
     */
    private function _preparePartWhereMany($conditions)
    {
        if (ArrayHelper::isArray2DStrongMin($conditions)) {
            // list of conditions
            foreach ($conditions as $key => $condition) {
                $conditions[$key] = $this->_preparePartWhereMany($conditions[$key]);
            }
            return '(' . implode(' AND ', $conditions) . ')';
        } else {
            // one condition
            return $this->_preparePartWhereOne($conditions);
        }
    }

    /**
     * @param array $conditions
     *
     * @return string
     */
    private function _preparePartWhereOne($conditions)
    {
        if (count($conditions) === 1) {
            return $conditions[0];
        }

        if (count($conditions) === 3 && in_array($conditions[2], ['=', '>', '<', '>=', '<=', '<>'])) {
            return "" . $conditions[0] . " " . $conditions[2] . " " . $this->_setPrepareParam($conditions[1]);
        }

        if (count($conditions) === 3 && ArrayHelper::isArray($conditions[1]) && in_array($conditions[2], ['IN', 'NOT IN'])) {
            foreach ($conditions[1] as $key => $condition) {
                $conditions[1][$key] = $this->_setPrepareParam($condition);
            }
            return "" . $conditions[0] . " " . $conditions[2] . " (" . implode(", ", $conditions[1]) . ")";
        }

        // TODO: what do with this?
        return '';
    }

    /**
     * Prepare ORDER BY part
     *
     * @return $this
     */
    private function _preparePartOrderBy()
    {
        $buffer = [];

        foreach ($this->orderBy as $row) {
            // format: ['table', 'ASC'] OR ['table.alias', 'DESC']
            $buffer[] = '`' . str_replace('.', '`.`', $row[0]) . '` ' . $row[1];
        }

        if (count($buffer)) {
            $this->prepared['ORDER_BY'] = 'ORDER BY ' . implode(', ', $buffer);
        }

        return $this;
    }

    /**
     * Prepare OFFSET and LIMIT parts
     *
     * @return $this
     */
    private function _preparePartLimit()
    {
        if ($this->offset > 0 && $this->limit > 0) {
            $this->prepared['LIMIT'] = 'LIMIT ' . $this->offset . ', ' . $this->limit;
        } elseif ($this->limit > 0) {
            $this->prepared['LIMIT'] = 'LIMIT ' . $this->limit;
        }

        return $this;
    }

    /**
     * Prepare params for PDO
     *
     * @param $value
     * @param $key
     *
     * @return string
     */
    private function _setPrepareParam($value, $key = FALSE)
    {
        $this->preparedParamNumber++;
        $preparedName = ':value' . $this->preparedParamNumber;

        if ($key !== FALSE) {
            $this->preparedParams[$key][$preparedName] = $value;
        } else {
            $this->preparedParams[$preparedName] = $value;
        }

        return $preparedName;
    }

    private function _resetPreparedParamNumber()
    {
        $this->preparedParamNumber = 0;
    }

}