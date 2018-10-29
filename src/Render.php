<?php

namespace fly;

class Render
{

    private static $viewsDir = '';

    /**
     * Set default folder for views
     *
     * @param string $viewsDir
     */
    public static function setViewsDir($viewsDir)
    {
        self::$viewsDir = $viewsDir;
    }

    /**
     * Render a view from a file
     *
     * @param string $name
     * @param array  $variables
     *
     * @return bool
     */
    public static function View($name, $variables = [])
    {
        return self::Rend($name, $variables);
    }

    private static function Rend($__RENDER_NAME, $__RENDER_VARIABLES = [])
    {
        // vars
        if (count($__RENDER_VARIABLES)) {
            foreach ($__RENDER_VARIABLES as $__RENDER_KEY => $__RENDER_VALUE) {
                $$__RENDER_KEY = $__RENDER_VALUE;
            }
        }

        $__RENDER_FILE = self::$viewsDir . '/' . $__RENDER_NAME . '.php';

        // include file if exists
        if (file_exists($__RENDER_FILE)) {
            include $__RENDER_FILE;
            return TRUE;
        }
        // TODO: add throw if else

        return FALSE;
    }

}