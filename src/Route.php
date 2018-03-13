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

    private static $configRoutes = [];
    private static $route = '';
    private static $activeRoute = [
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
        self::$configRoutes = $config;
    }

    /**
     * @return string
     */
    public static function getRoute()
    {
        return self::$route;
    }

    private static function setRoute()
    {
        if (isset($_GET['route'])) {
            self::$route = trim($_GET['route']);
        }
    }

    /**
     * @return bool
     */
    private static function checkRoute()
    {
        foreach (self::$configRoutes as $configRow) {
            if (preg_match($configRow['route'], self::$route, $matches)) {
                // found in routes
                self::$activeRoute['file'] = $configRow['file'];
                $max_match_key = max(array_keys($matches));
                if (isset($configRow['data']) && is_array($configRow['data'])) {
                    foreach ($configRow['data'] as $key => $value) {
                        // change $1 .. $9 to values from preg_match result
                        //echo '-'.$key.'-';
                        for ($i = $max_match_key; $i >= 1; $i--) {
                            if (isset($matches[$i])) {
                                $value = str_replace('$' . $i, $matches[$i], $value);
                            }
                        }
                        // set data to $module_data
                        self::$activeRoute['data'][$key] = $value;
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
        return FileHelper::checkExistsFile(self::$activeRoute['file']);
    }

    private static function includeFile()
    {
        // set variables
        foreach (self::$activeRoute['data'] as $key => $value) {
            $$key = $value;
        }

        // include file
        include self::$activeRoute['file'];
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
        self::setRoute();

        if (!self::checkRoute()) {
            throw new \Exception('Route::Init(): No routes found');
        }

        if (!self::checkFileExists()) {
            throw new \Exception('Route::Init(): No route file exists');
        }

        self::includeFile();
    }

}