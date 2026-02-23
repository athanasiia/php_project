<?php
namespace system;

use Exception;
use PDO;
use PDOException;

class Migrations
{
    private const string MIGRATION_DIRECTION_UP = "up";
    private const string MIGRATION_DIRECTION_DOWN = "down";
    private PDO $db;
    private string $migrationsPath;

    public function __construct($db, $migrationsPath)
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
    }

    public function getAllMigrations(): false|array
    {
        $files = glob($this->migrationsPath . '/m*.php');
        sort($files);
        return $files;
    }

    public function getExecutedMigrations(): array
    {
        return $this->db->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @throws Exception
     */
    public function migrate(): array
    {
        $this->createMigrationsTable();

        $allMigrations = $this->getAllMigrations();
        $executedMigrations = $this->getExecutedMigrations();
        $results = [];

        foreach ($allMigrations as $migrationFile) {
            $migrationName = basename($migrationFile, '.php');

            if (!in_array($migrationName, $executedMigrations)) {
                $results[$migrationName] = $this->runMigration($migrationFile, self::MIGRATION_DIRECTION_UP);
            }
        }

        return $results;
    }

    public function rollback(): bool
    {
        $executedMigrations = $this->getExecutedMigrations();

        if (empty($executedMigrations)) {
            return false;
        }

        $lastMigration = end($executedMigrations);
        $migrationFile = $this->migrationsPath . '/' . $lastMigration . '.php';

        if (file_exists($migrationFile)) {
            return $this->runMigration($migrationFile, self::MIGRATION_DIRECTION_DOWN);
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function runMigration($migrationFile, $direction): bool
    {
        require_once $migrationFile;

        $className = $this->getClassNameFromFile($migrationFile);

        if (!class_exists($className)) {
            throw new Exception("Class $className not found in $migrationFile");
        }

        $migration = new $className($this->db);

        try {
            $migration->$direction();

            $migrationName = basename($migrationFile, '.php');

            if ($direction === self::MIGRATION_DIRECTION_UP)
            {
                $this->recordMigration($migrationName);
            } else {
                $this->removeMigration($migrationName);
            }

            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $result = $this->db->exec($sql);
            if ($result === false) {
                throw new Exception('Database query failed');
            }
        } catch (PDOException $e) {
            throw new Exception('Failed to create table "migrations": ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function recordMigration(string $migrationName): void
    {
        $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES (?)");

        try {
            $result = $stmt->execute([$migrationName]);;
            if ($result === false) {
                throw new Exception('Database query failed');
            }
        } catch (PDOException $e) {
            throw new Exception('Failed to insert migration: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function removeMigration(string $migrationName): void
    {
        $stmt = $this->db->prepare("DELETE FROM migrations WHERE migration = ?");

        try {
            $result = $stmt->execute([$migrationName]);;
            if ($result === false) {
                throw new Exception('Database query failed');
            }
        } catch (PDOException $e) {
            throw new Exception('Failed to remove migration: ' . $e->getMessage());
        }
        $stmt->execute([$migrationName]);
    }

    private function getClassNameFromFile(string $filename): array|string
    {
        $name = basename($filename, '.php');
        $name = preg_replace('/^m\d+_/', '', $name);
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        return str_replace(' ', '', $name);
    }
}