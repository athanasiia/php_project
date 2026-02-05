<?php

use database\DatabaseConnection;

require_once __DIR__ . '/../bootstrap/autoload.php';

$command = $argv[1] ?? null;
$argument = $argv[2] ?? null;

$db = DatabaseConnection::getInstance()->connect();

$migrations = new system\Migrations($db, BASE_PATH . '/database/migrations');
$seeds = new system\Seeds($db, BASE_PATH . '/database/seeds');

function handleMigrate($migrations, $argument = null)
{
    echo "Running migrations...\n";

    if ($argument) {
        try {
            $result = $migrations->runMigration($argument, "up");

            if (empty($result)) {
                echo "No such migration.\n";
            } else {
                echo "Migration completed successfully!\n";
            }
        } catch (\Exception $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            exit(1);
        }
        return;
    }

    try {
        $results = $migrations->migrate();

        if (empty($results)) {
            echo "No new migrations to run.\n";
        } else {
            foreach ($results as $migration => $success) {
                $status = $success ? '✓' : '✗';
                echo "{$status} {$migration}\n";
            }
            echo "All migrations completed successfully!\n";
        }
    } catch (\Exception $e) {
        echo "Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

function handleRollback($migrations)
{
    echo "Rolling back last migration...\n";

    try {
        $success = $migrations->rollback();

        if ($success) {
            echo "Rollback completed successfully!\n";
        } else {
            echo "No migrations to rollback.\n";
        }
    } catch (\Exception $e) {
        echo "Rollback failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

function handleSeed($seeds, $argument = null) {
    echo "Running seeds...\n";

    try {
        if ($argument) {
            $seeds->run($argument);
            echo "Seed completed successfully!\n";
        } else {
            $seeds->runAll();
            echo "All seeds completed successfully!\n";
        }
    } catch (Exception $e) {
        echo "Seeds failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

switch ($command) {
    case 'migrate':
        handleMigrate($migrations, $argument);
        break;

    case 'migrate:rollback':
        handleRollback($migrations);
        break;

    case 'seed':
        handleSeed($seeds, $argument);
        break;

    default:
        echo "Unknown command: $command\n";
        break;
}