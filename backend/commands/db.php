<?php

use database\DatabaseConnection;
use system\Migrations;
use system\Seeds;

require_once __DIR__ . '/../bootstrap/autoload.php';

const MIGRATION_DIRECTION_UP = "up";

class AppCommands {
    private Migrations $migrations;
    private Seeds $seeds;
    private PDO $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->connect();
        $this->migrations = new Migrations($this->db, BASE_PATH . '/database/migrations');
        $this->seeds = new Seeds($this->db, BASE_PATH . '/database/seeds');;
    }

    /**
     * @throws Exception
     */
    public function run(string $command, ?string $argument = null): void
    {
        match ($command) {
            'migrate' => $this->handleMigrate($argument),
            'migrate:rollback' => $this->handleRollback(),
            'seed' => $this->handleSeed($argument),
            default => $this->showUsage()
        };
    }

    /**
     * @throws Exception
     */
    private function handleMigrate(?string $argument = null): void
    {
        echo "Running migrations...\n";

        if ($argument !== null) {
            $this->runSingleMigration($argument);
        }

        $this->runAllMigrations();
    }

    /**
     * @throws Exception
     */
    private function runSingleMigration(string $migrationName): void
    {
        try {
            $result = $this->migrations->runMigration($migrationName, MIGRATION_DIRECTION_UP);

            if (empty($result)) {
                echo "No such migration.";
            }

            echo "Migration completed successfully!\n";
        } catch (Exception $e) {
            throw new Exception("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function runAllMigrations(): void
    {
        try {
            $results = $this->migrations->migrate();

            if (empty($results)) {
                echo "No new migrations to run.\n";
            }

            foreach ($results as $migration => $success) {
                $status = $success ? '✓' : '✗';
                echo "$status $migration\n";
            }

            echo "All migrations completed successfully!\n";
        } catch (Exception $e) {
            throw new Exception("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function handleRollback(): void
    {
        echo "Rolling back last migration...\n";

        try {
            $success = $this->migrations->rollback();

            if ($success) {
                echo "Rollback completed successfully!\n";
            } else {
                echo "No migrations to rollback.\n";
            }
        } catch (Exception $e) {
            throw new Exception("Rollback failed: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function handleSeed(?string $argument = null): void
    {
        echo "Running seeds...\n";

        try {
            if ($argument !== null) {
                $this->seeds->run($argument);
                echo "Seed completed successfully!\n";
            } else {
                $this->seeds->runAll();
                echo "All seeds completed successfully!\n";
            }

        } catch (Exception $e) {
            throw new Exception("Seeds failed: " . $e->getMessage());
        }
    }

    private function showUsage(): void
    {
        echo "Usage: php db.php [command] [argument]\n\n" .
            "Commands:\n" .
            "migrate                Run all pending migrations\n" .
            "migrate [name]         Run specific migration\n" .
            "migrate:rollback       Rollback last migration\n" .
            "seed                   Run all seeds\n" .
            "seed [name]            Run specific seed\n";
    }
}

try {
    $command = $argv[1] ?? null;
    $argument = $argv[2] ?? null;

    (new AppCommands())->run($command, $argument);
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}