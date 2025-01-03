<?php

class DatabaseHandler
{
    private PDO $connection;

    public function __construct() {
        try {
            $this->connection = new PDO(
                sprintf("mysql:host=%s;dbname=%s",
                    Credentials::$url,
                    Credentials::$database),
                Credentials::$username,
                Credentials::$password
            );
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function prepare(string $query, array $options = []): false|PDOStatement
    {
        return $this->connection->prepare($query, $options);
    }

    public function lastInsertId($name = null): false|string
    {
        return $this->connection->lastInsertId($name);
    }
}