<?php

class Conexao
{
    private static $instance;

    public static function getConn()
    {
        if (!isset(self::$instance)) {
            $dbPath = __DIR__ . '/../database.sqlite';
            self::$instance = new \PDO('sqlite:' . $dbPath);
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance->exec('PRAGMA foreign_keys = ON;');
        }

        return self::$instance;
    }
}
