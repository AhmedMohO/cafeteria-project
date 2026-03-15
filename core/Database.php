<?php

namespace Core;

use PDO;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $env = parse_ini_file(__DIR__ . '/../.env');
        
        $host = $env['DB_HOST'] ?? 'localhost';
        $db   = $env['DB_NAME'] ?? 'cafeteria';
        $user = $env['DB_USER'] ?? 'root';
        $pass = $env['DB_PASS'] ?? '';
        $charset = $env['DB_CHARSET'] ?? 'utf8';

        $this->connection = new PDO(
            "mysql:host=$host;dbname=$db;charset=$charset",
            $user,
            $pass
        );

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}