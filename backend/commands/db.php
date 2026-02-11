<?php

use database\DatabaseConnection;

require_once __DIR__ . '/../bootstrap/autoload.php';

$command = $argv[1] ?? null; // TODO: add validation - check if command is provided, show usage if not
$argument = $argv[2] ?? null;

$db = DatabaseConnection::getInstance()->connect();

$migrations = new system\Migrations($db, BASE_PATH . '/database/migrations'); // try to use __construct() instead of new
$seeds = new system\Seeds($db, BASE_PATH . '/database/seeds'); // try to use __construct() instead of new

//Need to add accessors before methods like public and etc
function handleMigrate($migrations, $argument = null) //add the return type and type of argument
{
    echo "Running migrations...\n";

    if ($argument) {
        try {
            $result = $migrations->runMigration($argument, "up");  //extract magic string "up" to a constant (e.g., MIGRATION_DIRECTION_UP)

            if (empty($result)) {
                echo "No such migration.\n";
            } else {
                echo "Migration completed successfully!\n";
            }
        } catch (\Exception $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            exit(1); // throw error instead of exit
        }
        return;
    } // I think we need fot this condition separate method which can be used

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
        exit(1); //throw error instead of exit
}

function handleRollback($migrations)  //add the return type and type of argument
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
        exit(1); //throw error instead of exit
    }
}

function handleSeed($seeds, $argument = null) //add the return type and type of argument
{
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
        exit(1); //throw error instead of exit
    }
}

switch ($command) // try to use match instead of switch and this logic should be in a function

{
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