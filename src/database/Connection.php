<?php

/**
 * Connection
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database;


class Connection
{

    /**
     * @var $pdo \PDO
     */
    private static $pdo;

    /**
     * Get PDO connection object
     *
     * @return \PDO
     */
    public static function getConnection()
    {
        return self::$pdo;
    }

    /**
     * Connect to database
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array  $options
     *
     * @return \PDO
     * @throws \Exception
     */
    public static function Connect($dsn, $username = 'root', $password = '', $options = [])
    {
        self::$pdo = new \PDO($dsn, $username, $password, $options);

        if (!self::$pdo) {
            // pdo connect error
            // TODO: add error info
            throw new \Exception('fly\Database: PDO connect error');
        }

        return self::getConnection();
    }

    /**
     * Close PDO connection
     */
    public static function Close()
    {
        self::$pdo = null;
    }

}