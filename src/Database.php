<?php

/**
 * Database
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly;

use fly\database\Connection;
use fly\database\QueryMain;
use fly\helpers\ArrayHelper;

class Database
{

    /**
     * @var array $config  array pdo connect config
     * @var array $default default config
     * @var array $opt     array pdo connect options
     */
    private static $config;
    private static $default = [
        'type' => 'mysql',
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ];
    private static $opt;
    private static $count = 0;

    /**
     * @var bool Default value for query cache status
     */
    private static $cache = FALSE;

    /**
     * Validate database connection type
     *
     * @param string $type
     *
     * @return bool
     */
    private static function isConfigTypeValid($type)
    {
        if (!in_array($type, ['mysql'])) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate database connection hostname
     *
     * @param string $hostname
     *
     * @return bool
     */
    private static function isConfigHostnameValid($hostname)
    {
        if (!preg_match('/^[-a-z0-9.:\/]+$/i', $hostname)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate database connection database name
     *
     * @param string $database
     *
     * @return bool
     */
    private static function isConfigDatabaseValid($database)
    {
        if (!preg_match('/^[-_a-z0-9]+$/i', $database)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate database connection username
     *
     * @param string $username
     *
     * @return bool
     */
    private static function isConfigUsernameValid($username)
    {
        if (!preg_match('/^[-_a-z0-9]+$/i', $username)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate database connection charset
     *
     * TODO: add more charsets
     *
     * @param string $type
     *
     * @return bool
     */
    private static function isConfigCharsetValid($type)
    {
        if (!in_array($type, ['utf8'])) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Set connection config
     *
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    private static function setConfig($data)
    {
        // Set type to config
        if (isset($data['type'])) {
            if (self::isConfigTypeValid($data['type'])) {
                self::$config['type'] = $data['type'];
            } else {
                throw new \Exception('fly\Database: Invalid connection type');
            }
        } else {
            self::$config['type'] = self::$default['type'];
        }

        // Set hostname to config
        if (isset($data['hostname'])) {
            if (self::isConfigHostnameValid($data['hostname'])) {
                self::$config['hostname'] = $data['hostname'];
            } else {
                throw new \Exception('fly\Database: Invalid connection hostname');
            }
        } else {
            self::$config['hostname'] = self::$default['hostname'];
        }

        // Set database to config
        if (isset($data['database'])) {
            if (self::isConfigDatabaseValid($data['database'])) {
                self::$config['database'] = $data['database'];
            } else {
                throw new \Exception('fly\Database: Invalid connection database');
            }
        } else {
            return FALSE;
        }

        // Set username to config
        if (isset($data['username'])) {
            if (self::isConfigUsernameValid($data['username'])) {
                self::$config['username'] = $data['username'];
            } else {
                throw new \Exception('fly\Database: Invalid connection username');
            }
        } else {
            self::$config['username'] = self::$default['username'];
        }

        // Set password to config
        if (isset($data['password'])) {
            self::$config['password'] = $data['password'];
        } else {
            self::$config['password'] = self::$default['password'];
        }

        // Set charset to config
        if (isset($data['charset'])) {
            if (self::isConfigCharsetValid($data['charset'])) {
                self::$config['charset'] = $data['charset'];
            } else {
                throw new \Exception('fly\Database: Invalid connection charset');
            }
        } else {
            self::$config['charset'] = self::$default['charset'];
        }

        // Set dsn
        self::$config['dsn'] = self::$config['type'] . ':host=' . self::$config['hostname'] . ';dbname=' . self::$config['database'] . ';charset=' . self::$config['charset'];

        return TRUE;
    }

    /**
     * Set connection options
     *
     * @param array $opt
     *
     * @return bool
     */
    private static function setOptions($opt)
    {
        self::$opt = $opt;

        return TRUE;
    }

    /**
     * Connect to database
     *
     * @param array $config
     * @param array $opt
     *
     * @return bool
     * @throws \Exception
     */
    public static function Connect($config, $opt = [])
    {
        if (!ArrayHelper::isArray($config) || !count($config)) {
            // Config is not an array or empty
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid config array');
        }

        // set config
        if (!self::setConfig($config)) {
            // config is not valid
            throw new \Exception('fly\Database: Invalid config array');
        }

        // set options
        self::setOptions($opt);

        // connect
        if (!Connection::Connect(self::$config['dsn'], self::$config['username'], self::$config['password'], self::$opt)) {
            return FALSE;
        }

        self::$count = 0;

        return TRUE;
    }

    /**
     * Close connection
     */
    public static function Close()
    {
        Connection::Close();
    }

    /**
     * Execute SQL query
     *
     * @param string $sql
     * @param array  $params
     *
     * @return mixed
     */
    public static function SQL($sql, $params = [])
    {
        // get \PDOStatement
        $stmt = Connection::SQL($sql, $params);

        self::$count++;

        $sqlSubStr6 = mb_substr($sql, 0, 6);

        if ($sqlSubStr6 == 'SELECT') {
            // return fetch array
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } elseif ($sqlSubStr6 == 'INSERT' || $sqlSubStr6 == 'UPDATE' || $sqlSubStr6 == 'DELETE') {
            // return row count
            return $stmt->rowCount();
        } else {
            $result = $stmt;
        }

        return $result;
    }

    public static function getCount()
    {
        return self::$count;
    }

    public static function Query()
    {
        return new QueryMain();
    }

    /**
     * Set default cache status
     *
     * @param bool $cache
     *
     * @throws \Exception
     */
    public static function setCacheDefault($cache)
    {
        if (!is_bool($cache)) {
            throw new \Exception('fly\Database: Expects parameter 1 to be a valid default cache status');
        }

        self::$cache = $cache;
    }

    /**
     * Set default cache status to TRUE
     *
     * @throws \Exception
     */
    public static function setCacheDefaultTrue()
    {
        return self::setCacheDefault(TRUE);
    }

    /**
     * Set default cache status to FALSE
     *
     * @throws \Exception
     */
    public static function setCacheDefaultFalse()
    {
        return self::setCacheDefault(FALSE);
    }

}