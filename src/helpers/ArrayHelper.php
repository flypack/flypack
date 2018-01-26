<?php

/**
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 */

namespace fly\helpers;

/**
 * ArrayHelper
 *
 * @author Roman Rozinko <r.rozinko@gmail.com>
 * @since  0.1
 */

class ArrayHelper
{

    /**
     * @param $array
     *
     * @return bool
     */
    public static function isArray($array)
    {
        return is_array($array);
    }

    /**
     * @param $array
     * @param $level
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArrayXD($array, $level)
    {
        if (!is_numeric($level)) {
            // $level is not a number
            throw new \Exception('ArrayHelper::isArrayXD(): Expects parameter 2 to be a valid level number');
        }

        if ($level <= 1) {
            // return isArray()
            return self::isArray($array);
        }

        if (!self::isArray($array)) {
            // $array is not an array
            return FALSE;
        }

        foreach ($array as $subArray) {
            // check with level-1
            if (self::isArrayXD($subArray, ($level - 1)) === TRUE) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @param $array
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArray2D($array)
    {
        return self::isArrayXD($array, 2);
    }

    /**
     * @param $array
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArray3D($array)
    {
        return self::isArrayXD($array, 3);
    }

    /**
     * @param $array
     *
     * @return bool
     */
    public static function isArrayStrong($array)
    {
        if (!is_array($array)) {
            return FALSE;
        }

        foreach ($array as $value) {
            if (is_array($value)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * @param $array
     * @param $level
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArrayXDStrong($array, $level)
    {
        if (!is_numeric($level)) {
            // $level is not a number
            throw new \Exception('ArrayHelper::isArrayXDStrong(): Expects parameter 2 to be a valid level number');
        }

        if ($level <= 1) {
            // return isArrayStrong()
            return self::isArrayStrong($array);
        }

        if (!self::isArray($array)) {
            // $array is not an array
            return FALSE;
        }

        if ($level > 1 && empty($array)) {
            return FALSE;
        }

        foreach ($array as $subArray) {
            // check with level-1
            if (self::isArrayXDStrong($subArray, ($level - 1)) === FALSE) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * @param $array
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArray2DStrong($array)
    {
        return self::isArrayXDStrong($array, 2);
    }

    /**
     * @param $array
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArray3DStrong($array)
    {
        return self::isArrayXDStrong($array, 3);
    }

    /**
     * @param $array
     * @param $level
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArrayXDStrongMin($array, $level)
    {
        if (!is_numeric($level)) {
            // $level is not a number
            throw new \Exception('ArrayHelper::isArrayXDStrongMin(): Expects parameter 2 to be a valid level number');
        }

        if ($level <= 1) {
            // return isArrayStrong()
            return self::isArray($array);
        }

        if (!self::isArray($array)) {
            // $array is not an array
            return FALSE;
        }

        if ($level > 1 && empty($array)) {
            return FALSE;
        }

        foreach ($array as $subArray) {
            // check with level-1
            if (self::isArrayXDStrongMin($subArray, ($level - 1)) === FALSE) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * @param $array
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArray2DStrongMin($array)
    {
        return self::isArrayXDStrongMin($array, 2);
    }

    /**
     * @param $array
     *
     * @return bool
     * @throws \Exception
     */
    public static function isArray3DStrongMin($array)
    {
        return self::isArrayXDStrongMin($array, 3);
    }

}