<?php

namespace database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?self $instance = null;
    private ?PDO $connection = null;

    private array $config = [
        'host' => 'localhost',
        'database' => 'users_db',
        'username' => 'root',
        'password' => 'password',
        'charset' => 'utf8mb4'
    ];

    private function __construct() {}

    private function __clone() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect(): PDO
    {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
                $this->connection = new PDO(
                    $dsn,
                    $this->config['username'],
                    $this->config['password'],
                );

                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }

        return $this->connection;
    }

    public function createUser(array $data): ?int
    {
        $sql = "INSERT INTO users (email, name, country, city, gender, status) 
                VALUES (:email, :name, :country, :city, :gender, :status)";

        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([
                ':email' => $data['email'],
                ':name' => $data['name'],
                ':country' => $data['country'],
                ':city' => $data['city'],
                ':gender' => $data['gender'],
                ':status' => $data['status'] ?? 'active'
            ]);

            return (int)$this->connect()->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception("Failed to create user: " . $e->getMessage());
        }
    }

    public function updateUser(int $id, array $data): bool
    {
        $setParts = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $setParts[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        if (empty($setParts)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id = :id";

        try {
            $stmt = $this->connect()->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new \Exception("Failed to update user: " . $e->getMessage());
        }
    }

    public function deleteUser(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";

        try {
            $stmt = $this->connect()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new \Exception("Failed to delete user: " . $e->getMessage());
        }
    }

    public function getUser(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();

            return $result ?: null;
        } catch (PDOException $e) {
            throw new \Exception("Failed to get user: " . $e->getMessage());
        }
    }

    public function getAllUsers(array $filters = []): array
    {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['gender'])) {
            $sql .= " AND gender = :gender";
            $params[':gender'] = $filters['gender'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $orderBy = 'id';
        if (!empty($filters['sort'])) {
            $allowedSortFields = ['id', 'name', 'email', 'created_at', 'status'];
            $field = $filters['sort'];
            $orderBy = in_array($field, $allowedSortFields) ? $field : 'id';
        }

        $orderDirection = 'ASC';
        if (!empty($filters['order']) && strtoupper($filters['order']) === 'DESC') {
            $orderDirection = 'DESC';
        }

        $sql .= " ORDER BY $orderBy $orderDirection";

        try {
            $stmt = $this->connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }

            $stmt->execute();
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new \Exception("Failed to get users: " . $e->getMessage());
        }
    }
}