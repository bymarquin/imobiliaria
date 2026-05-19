<?php

// Abre a conexão com o banco de dados SQLite.
// Usa Singleton: abre uma vez e reutiliza sempre — não abre uma conexão nova a cada uso.
class Conexao
{
    private static $instance; // guarda a conexão aberta

    public static function getConn()
    {
        if (!isset(self::$instance)) {
            $dbPath = __DIR__ . '/../database.sqlite'; // caminho do arquivo do banco
            self::$instance = new \PDO('sqlite:' . $dbPath);
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // mostra erros se algo der errado
            self::$instance->exec('PRAGMA foreign_keys = ON;'); // ativa a validação de chaves estrangeiras no SQLite
        }

        return self::$instance;
    }
}
