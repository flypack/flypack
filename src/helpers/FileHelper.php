<?php

/**
 * FileHelper
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.1
 */

namespace fly\helpers;

class FileHelper
{

    /**
     * @param string $path
     *
     * @return bool
     * @throws \Exception
     */
    public static function checkExists($path)
    {
        if (!is_string($path)) {
            throw new \Exception('FileHelper::checkExists(): Expects parameter 1 to be a valid path');
        }
        return file_exists($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws \Exception
     */
    public static function checkExistsFile($path)
    {
        if (self::checkExists($path)) {
            return !is_dir($path);
        }
        return FALSE;
    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws \Exception
     */
    public static function checkExistsDir($path)
    {
        if (self::checkExists($path)) {
            return is_dir($path);
        }
        return FALSE;
    }

    /**
     * @param string $path
     * @param bool   $required
     *
     * @return bool|string
     * @throws \Exception
     */
    public static function getFileContent($path, $required = TRUE)
    {
        if (!is_bool($required)) {
            throw new \Exception('FileHelper::getFileContent(): Expects parameter 2 to be a boolean');
        }
        if (!self::checkExistsFile($path)) {
            if ($required === TRUE) {
                throw new \Exception('FileHelper::getFileContent(): File not found');
            }
            return '';
        }
        return file_get_contents($path);
    }

    /**
     * @param array  $paths
     * @param bool   $required
     * @param string $separator
     *
     * @return string
     * @throws \Exception
     */
    public static function getFilesContent($paths, $required = TRUE, $separator = '')
    {
        if (!is_array($paths)) {
            throw new \Exception('FileHelper::getFilesContent(): Expects parameter 1 to be an array of paths');
        }
        $contents = array();
        foreach ($paths as $path) {
            $contents[] = self::getFileContent($path, $required);
        }
        return implode($separator, $contents);
    }

    /**
     * @param string $path
     * @param string $content
     *
     * @return bool|int
     */
    public static function saveFile($path, $content = '')
    {
        return file_put_contents($path, $content);
    }

}