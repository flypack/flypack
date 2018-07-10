<?php

/**
 * QueryValidator
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;

use fly\helpers\ArrayHelper;

class QueryValidator extends QueryChecker
{

    /**
     * Validate table name
     *
     * @param string $tableName
     *
     * @return bool
     */
    protected function _isTableNameValid($tableName)
    {
        if (!preg_match('/^[-_0-9a-z]+$/i', $tableName)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate table alias
     *
     * @param string $tableAlias
     *
     * @return bool
     */
    protected function _isTableAliasValid($tableAlias)
    {
        if (!preg_match('/^[_0-9a-z]+$/i', $tableAlias)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate field name
     *
     * @param string $fieldName
     *
     * @return bool
     */
    protected function _isFieldNameValid($fieldName)
    {
        if (!preg_match('/^[_0-9a-z\.]+$/i', $fieldName)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate relation name
     *
     * @param string $relationName
     *
     * @return bool
     */
    protected function _isRelationNameValid($relationName)
    {
        if (!preg_match('/^[_0-9a-z]+$/i', $relationName)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate relation type
     *
     * @param string $relationType
     *
     * @return bool
     */
    protected function _isRelationTypeValid($relationType)
    {
        if (!in_array($relationType, ['one', 'many'])) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate array of relation conditions
     * Conditions format: [field, field2] OR [[field, field2], [field, field2]]
     *
     * @param array $conditions
     *
     * @return bool
     * @throws \Exception
     */
    protected function _isRelationConditionsValid($conditions)
    {
        if (!ArrayHelper::isArrayStrong($conditions) && !ArrayHelper::isArray2DStrong($conditions)) {
            return FALSE;
        }

        return TRUE;
    }

}