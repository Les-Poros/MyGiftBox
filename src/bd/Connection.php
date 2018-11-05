<?php

namespace MyGiftBox\bd;

/**
 * Class Connection
 */
class Connection {

    private static $db;
    private static $tab;

    // Method that makes a connection
    public static function makeConnection() {
        try {
            $dsn = 'mysql:host='.self::$tab['host'] . ';dbname='.self::$tab['dbname'];
            self::$db = new \PDO($dsn, self::$tab['username'], self::$tab['password'], array(
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false
            ));
            self::$db->prepare('SET NAMES \'UTF8\'')->execute();
        
        } catch (\PDOException $e) {
            throw new \PDOException("connection: $dsn " . $e->getMessage());
        }
        return self::$db;
    }

    /**
     * Method that sets a config
     * @param file
     */
    public static function setConfig($file) {
        self::$tab = parse_ini_file($file);
    }
}