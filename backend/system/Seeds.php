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

    public function runAll()
    {
        $seedFiles = glob($this->seedsPath . '/*Seeder.php');

        foreach ($seedFiles as $seedFile) {
            $this->runSeed($seedFile);
        }
    }

    public function run($seedName)
    {
        $seedFile = $this->seedsPath . '/' . $seedName . '.php';

        if (!file_exists($seedFile)) {
            throw new \Exception("Seed not found: {$seedName}");
        }

        $this->runSeed($seedFile);
    }

    private function runSeed($seedFile)
    {
        require_once $seedFile;

        $className = basename($seedFile, '.php');

        if (!class_exists($className)) {
            throw new \Exception("Class {$className} not found in {$seedFile}");
        }

        $seed = new $className($this->db);
        $seed->run();

        echo "Seed {$className} completed successfully!\n";
    }
}