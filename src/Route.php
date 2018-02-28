<?php

/**
 * Route
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.2
 */

namespace fly;

use fly\helpers\ArrayHelper;
use fly\helpers\FileHelper;

class Route
{

    private static $config = [];
    private static $query = '';
    private static $route = [
        'file' => '',
        'data' => [],
    ];

    /**
     * @param array $config
     *
     * @return bool
     */
    private static function checkConfig($config)
    {
        if (!ArrayHelper::isArray($config)) {
            return FALSE;
        }
        return TRUE;
    }


    /**
     * @param array $config
     */
    private static function setConfig($config)
    {
        self::$config = $config;
    }

    private static function setQuery()
    {
        if (isset($_GET['query'])) {
            self::$query = trim($_GET['query']);
        }
    }

    /**
     * @return bool
     */
    private static function checkRoute()
    {
        foreach (self::$config as $route) {
            if (preg_match($route['route'], self::$query, $matches)) {
                // found in routes
                self::$route['file'] = $route['file'];
                $max_match_key = max(array_keys($matches));
                if (isset($route['data']) && is_array($route['data'])) {
                    foreach ($route['data'] as $key => $value) {
                        // change $1 .. $9 to values from preg_match result
                        //echo '-'.$key.'-';
                        for ($i = $max_match_key; $i >= 1; $i--) {
                            if (isset($matches[$i])) {
                                $value = str_replace('$' . $i, $matches[$i], $value);
                            }
                        }
                        // set data to $module_data
                        self::$route['data'][$key] = $value;
                    }
                }
                // route found. return true
                return TRUE;
            }
        }
        // if route not found - return false
        return FALSE;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private static function checkFileExists()
    {
        if (!FileHelper::checkExistsFile(self::$route['file'])) {
            return FALSE;
        }
        return TRUE;
    }

    private static function includeFile()
    {
        // set variables
        foreach (self::$route['data'] as $key => $value) {
            $$key = $value;
        }

        // include file
        include self::$route['file'];
    }

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public static function Init($config = [])
    {
        if (!self::checkConfig($config)) {
            throw new \Exception('Route::Init(): Expects parameter 1 to be a valid config array');
        }

        self::setConfig($config);
        self::setQuery();

        if (!self::checkRoute()) {
            throw new \Exception('Route::Init(): No routes found');
        }

        if (!self::checkFileExists()) {
            throw new \Exception('Route::Init(): No route file exists');
        }

        self::includeFile();
    }

    /**
     * @return string
     */
    public static function getQuery()
    {
        return self::$query;
    }

}