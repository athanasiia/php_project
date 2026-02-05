<?php
namespace system;

use PDO;

class Migrations
{
    private PDO $db;
    private string $migrationsPath;

    public function __construct($db, $migrationsPath)
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
    }

    public function getAllMigrations()
    {
        $files = glob($this->migrationsPath . '/m*.php');
        sort($files);
        return $files;
    }

    public function getExecutedMigrations()
    {
        $stmt = $this->db->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function migrate()
    {
        $this->createMigrationsTable();

        $allMigrations = $this->getAllMigrations();
        $executedMigrations = $this->getExecutedMigrations();
        $results = [];

        foreach ($allMigrations as $migrationFile) {
            $migrationName = basename($migrationFile, '.php');

            if (!in_array($migrationName, $executedMigrations)) {
                $results[$migrationName] = $this->runMigration($migrationFile, 'up');
            }
        }

        return $results;
    }

    public function rollback()
    {
        $executedMigrations = $this->getExecutedMigrations();

        if (empty($executedMigrations)) {
            return false;
        }

        $lastMigration = end($executedMigrations);
        $migrationFile = $this->migrationsPath . '/' . $lastMigration . '.php';

        if (file_exists($migrationFile)) {
            return $this->runMigration($migrationFile, 'down');
        }

        return false;
    }

    private function runMigration($migrationFile, $direction)
    {
        require_once $migrationFile;

        $className = $this->getClassNameFromFile($migrationFile);

        if (!class_exists($className)) {
            throw new \Exception("Class {$className} not found in {$migrationFile}");
        }

        $migration = new $className($this->db);

        try {
            $this->db->beginTransaction();

            $migration->$direction();

            $migrationName = basename($migrationFile, '.php');

            if ($direction === 'up') {
                $this->recordMigration($migrationName);
            } else {
                $this->removeMigration($migrationName);
            }

            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function createMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->db->exec($sql);
    }

    private function recordMigration($migrationName)
    {
        $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$migrationName]);
    }

    private function removeMigration($migrationName)
    {
        $stmt = $this->db->prepare("DELETE FROM migrations WHERE migration = ?");
        $stmt->execute([$migrationName]);
    }

    private function getClassNameFromFile($filename)
    {
        $name = basename($filename, '.php');
        $name = preg_replace('/^m\d+_/', '', $name);
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);

        return $name;
    }
}