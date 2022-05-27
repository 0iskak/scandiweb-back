<?php

namespace app;

use mysqli;

class MySQL extends mysqli
{
    private static MySQL $instance;

    private const HOSTNAME = 'localhost';
    private const USERNAME = 'scandiweb';
    private const PASSWORD = 'scandiweb';
    private const DATABASE = 'scandiweb';

    public function __construct()
    {
        parent::__construct(self::HOSTNAME, self::USERNAME,
            self::PASSWORD, self::DATABASE);
    }

    public static function getInstance(): MySQL
    {
        return self::$instance;
    }

    public static function setInstance(MySQL $instance): void
    {
        self::$instance = $instance;
    }

    public function createTable(string $table, string $columns): void
    {
        $this->query(
            sprintf(
                'CREATE TABLE IF NOT EXISTS `%s` (%s)',
                $table,
                $columns
            )
        );
    }

    public function getAll(string $table): array
    {
        $result = $this->query(
            sprintf(
                'SELECT * FROM `%s`',
                $table
            )
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insert(string $table, string $keys, string $values): void
    {
        $this->query(
            sprintf(
                'INSERT INTO `%s` (%s) VALUES (%s);',
                $table,
                $keys,
                $values
            )
        );
    }

    public function getById(string $table, int $id): array
    {
        return $this->query(
            sprintf(
                'SELECT * FROM `%s` WHERE `id` = %d;',
                $table,
                $id
            )
        )->fetch_assoc();
    }

    public function deleteById(string $table, int $id): void
    {
        MySQL::getInstance()->query(
            sprintf(
                'DELETE FROM `%s` WHERE `id` = %d;',
                $table,
                $id
            )
        );
    }
}