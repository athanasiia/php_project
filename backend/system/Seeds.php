<?php
namespace system;

use PDO;

class Seeds
{
    private PDO $db;
    private string $seedsPath;

    public function __construct($db, $seedsPath)
    {
        $this->db = $db;
        $this->seedsPath = $seedsPath;
    }

    public function runAll() // add return types
    {
        $seedFiles = glob($this->seedsPath . '/*Seeder.php'); // TODO: add error handling - check if glob returns false on error

        foreach ($seedFiles as $seedFile) {
            $this->runSeed($seedFile); // TODO: add error handling - wrap in try-catch to continue with other seeds if one fails
        }
    }

    public function run($seedName)
    {
        $seedFile = $this->seedsPath . '/' . $seedName . '.php'; // TODO: use path concatenation helper - use DIRECTORY_SEPARATOR or Path::join() for cross-platform compatibility

        if (!file_exists($seedFile)) { // TODO: add validation - check if $seedName is not empty and contains only safe characters (prevent directory traversal)
            throw new \Exception("Seed not found: {$seedName}");
        }

        $this->runSeed($seedFile);
    }

    private function runSeed($seedFile)
    {
        require_once $seedFile; // TODO: add error handling - wrap in try-catch to catch parse errors | TODO: consider security - validate file path to prevent directory traversal attacks

        $className = basename($seedFile, '.php'); // TODO: extract to method - create extractClassName() helper method for reusability // Also can use in other places

        if (!class_exists($className)) {
            throw new \Exception("Class {$className} not found in {$seedFile}");
        }

        $seed = new $className($this->db); // TODO: add error handling - wrap in try-catch to catch instantiation errors
        $seed->run(); // TODO: add error handling - wrap in try-catch to catch execution errors | TODO: verify return value - check if run() returns success status

        echo "Seed {$className} completed successfully!\n";
    }
}