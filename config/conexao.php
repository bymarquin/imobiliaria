<?php

/**
 * Cuida da conexão com o banco de dados.
 *
 * A ideia aqui é simples: não faz sentido abrir uma conexão nova toda vez que
 * alguma parte do sistema precisar falar com o banco. Então a gente usa o
 * padrão Singleton — abre uma vez, guarda na variável estática $instance,
 * e reutiliza sempre que alguém chamar getConn().
 *
 * O PDO é a camada que o PHP usa pra se comunicar com bancos de dados.
 * Funciona com MySQL, PostgreSQL, SQLite e outros — tudo com a mesma API.
 */
class Conexao
{
    // A conexão fica guardada aqui depois de ser criada pela primeira vez
    private static $instance;

    /**
     * Devolve a conexão ativa com o banco. Se ainda não existe, cria uma agora.
     */
    public static function getConn()
    {
        if (!isset(self::$instance)) {
            // Conecta no PostgreSQL local
            self::$instance = new \PDO('pgsql:host=localhost;dbname=imobiliaria', 'marquin', 'Senha123!');

            // Sem essa linha, erros de SQL passariam em silêncio.
            // Com ela, qualquer problema vira uma exceção visível.
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$instance;
    }
}
