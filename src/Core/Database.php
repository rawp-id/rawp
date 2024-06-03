<?php
// src/Core/Database.php

namespace Core;

class Database {
    protected static $pdo;

    public function __construct() {
        // Memuat variabel lingkungan dari file .env
        $host = Env::get('DB_HOST');
        $dbname = Env::get('DB_DATABASE');
        $username = Env::get('DB_USERNAME');
        $password = Env::get('DB_PASSWORD');

        // Membuat koneksi ke database
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function connect() {
        if (!isset(self::$pdo)) {
            // Load environment variables from the .env file
            $host = Env::get('DB_HOST');
            $dbname = Env::get('DB_DATABASE');
            $username = Env::get('DB_USERNAME');
            $password = Env::get('DB_PASSWORD');

            // Create a connection to the database
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new \PDO($dsn, $username, $password, $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
    }


    public static function getPdo() {
        self::connect();
        return self::$pdo;
    }

    public static function query($sql, $params = []) {
        $pdo = self::getPdo();
        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
}
