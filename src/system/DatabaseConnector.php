<?php

namespace Src\System;

use PDO;

class DatabaseConnector
{
    private PDO $dbConnection;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db = $_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USERNAME'];
        $pass = $_ENV['DB_PASSWORD'];

        try {
            $this->dbConnection = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db",
                $user,
                $pass
            );
        } catch (\PDOException $e) {
            exit('error connect db: ' . $e->getMessage());
        }
    }

    public function getDbConnection(): PDO
    {
        return $this->dbConnection;
    }
}
