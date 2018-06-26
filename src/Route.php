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

    const NOT_FOUND = 'Object not found';
    const NOT_FOUND_DESCRIPTION = 'Sorry. The requested URL was not found on this server. Please, check your spelling and try again.';

    const FORBIDDEN = 'Forbidden';
    const FORBIDDEN_DESCRIPTION = 'Sorry. You don\'t have permission to access this URL on this server.';

    private static $configRoutes = [];
    private static $route = '';
    private static $activeRoute = [
        'file' => '',
        'data' => [],
        'allow' => TRUE,
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
     * Returns active route
     *
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
        self::$activeRoute = [
            'file' => '',
            'data' => [],
            'allow' => TRUE,
        ];
        foreach (self::$configRoutes as $configRow) {
            if (is_array($configRow)) {
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

                    // set allow option
                    self::$activeRoute['allow'] = $configRow['allow'] ?? TRUE;

                    // route found. return true
                    return TRUE;
                }
            }
        }
        // if route not found - return false
        return FALSE;
    }

    /**
     * @since 0.3
     *
     * @return bool
     */
    private static function checkAllow()
    {
        if (self::$activeRoute['allow']) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private static function checkFileExists()
    {
        if (is_array(self::$activeRoute['file'])) {
            foreach (self::$activeRoute['file'] as $file) {
                if (!FileHelper::checkExistsFile($file)) {
                    return FALSE;
                }
            }
            return TRUE;
        } else {
            return FileHelper::checkExistsFile(self::$activeRoute['file']);
        }
    }

    private static function includeFile()
    {
        // set variables
        foreach (self::$activeRoute['data'] as $key => $value) {
            $$key = $value;
        }

        // include file
        if (is_array(self::$activeRoute['file'])) {
            foreach (self::$activeRoute['file'] as $file) {
                include $file;
            }
        } else {
            include self::$activeRoute['file'];
        }
    }

    /**
     * @param array $config Config of routes
     *
     * @throws \Exception
     */
    public static function Init($config = [])
    {
        if (!self::checkConfig($config)) {
            // Invalid route config.
            throw new \Exception('Route::Init(): Expects parameter 1 to be a valid config array');
        }

        self::setConfig($config);
        self::setRoute();

        if (!self::checkRoute()) {
            // Route not found in config. Generate error 404 page. Object not found.
            self::sendGeneratedPageNotFound();
            return;
        }

        if (!self::checkAllow()) {
            // Route is not allowed. Generate error 403 page. Forbidden.
            self::sendGeneratedPageForbidden();
            return;
        }

        if (!self::checkFileExists()) {
            // Route file not exists.
            throw new \Exception('Route::Init(): No route file exists');
        }

        self::includeFile();
    }

    /**
     * Generate content for default error pages and return as string
     *
     * @param string $title
     * @param string $description
     *
     * @since 0.3
     *
     * @return string
     */
    protected static function getGeneratedPageContent($title, $description)
    {
        return '<html><head><title>' . $title . '</title><meta charset="UTF-8"></head><body><h1>' . $title . '.</h1><p>' . $description . '</p><span>This page generated by <a href="https://github.com/flypack/flypack" target="_blank">Fly Pack</a>.</span></body></html>';
    }

    /**
     * Generate page 'Error 403. Forbidden'
     *
     * @since 0.3
     * @throws \Exception
     */
    protected static function sendGeneratedPageForbidden()
    {
        http_response_code(403);
        if (!isset(self::$configRoutes['Error403'])) {
            echo self::getGeneratedPageContent(self::FORBIDDEN, self::FORBIDDEN_DESCRIPTION);
        } else {
            if (!FileHelper::checkExistsFile(self::$configRoutes['Error403'])) {
                throw new \Exception('Route::Init(): No custom error 403 file exists');
            }
            include self::$configRoutes['Error403'];
        }
        return;
    }

    /**
     * Generate page 'Error 404. Object not found'
     *
     * @since 0.3
     * @throws \Exception
     */
    protected static function sendGeneratedPageNotFound()
    {
        http_response_code(404);
        if (!isset(self::$configRoutes['Error404'])) {
            echo self::getGeneratedPageContent(self::NOT_FOUND, self::NOT_FOUND_DESCRIPTION);
        } else {
            if (!FileHelper::checkExistsFile(self::$configRoutes['Error404'])) {
                throw new \Exception('Route::Init(): No custom error 404 file exists');
            }
            include self::$configRoutes['Error404'];
        }
        return;
    }

}