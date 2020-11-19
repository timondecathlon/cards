<?php

namespace Bitkit\Core\Database;

class Connect extends \Bitkit\Core\Patterns\Singleton
{
    /**
     * Свойство содержащее объект подключения к БД
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * @return \PDO
     */
    public function getConnection() : \PDO
    {
        if (!$this->pdo) {
            $this->pdo = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("set names utf8");
        }
        return $this->pdo;
    }
}     