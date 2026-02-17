<?php
namespace system;

use Exception;
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

    /**
     * @throws Exception
     */
    public function runAll(): void
    {
        $seedFiles = glob($this->seedsPath . '/*Seeder.php');
        if ($seedFiles === false) {
            throw new Exception("Failed to read seeds directory: $this->seedsPath");
        }

        foreach ($seedFiles as $seedFile) {
            try {
                $this->runSeed($seedFile);
            } catch (Exception $e) {
                echo "Error in " . basename($seedFile) . ": " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * @throws Exception
     */
    public function run($seedName): void
    {
        if (empty($seedName) || !preg_match('/^\w+$/', $seedName)) {
            throw new Exception("Invalid seed name: $seedName");
        }

        $seedFile = $this->joinPath($this->seedsPath, $seedName . '.php');

        if (!file_exists($seedFile)) {
            throw new Exception("Seed not found: $seedName");
        }

        $this->runSeed($seedFile);
    }

    /**
     * @throws Exception
     */
    private function runSeed($seedFile): void
    {
        $realPath = realpath($seedFile);
        if ($realPath === false || !str_starts_with($realPath, realpath($this->seedsPath))) {
            throw new Exception("Invalid seed file path: $seedFile");
        }

        try {
            require_once $seedFile;
        } catch (Exception $e) {
            throw new Exception("Failed to include seed file: " . $e->getMessage());
        }

        $className = $this->extractClassName($seedFile);

        if (!class_exists($className)) {
            throw new Exception("Class $className not found in $seedFile");
        }

        try {
            $seed = new $className($this->db);
        } catch (Exception $e) {
            throw new Exception("Failed to instantiate $className: " . $e->getMessage());
        }

        try {
            $result = $seed->run();
            if ($result === false) {
                throw new Exception("Seed execution failed: $seedFile");
            }
        } catch (Exception $e) {
            throw new Exception("Failed to run seed: " . $e->getMessage());
        }

        echo "Seed $className completed successfully!\n";
    }

    private function joinPath(string ...$parts): string
    {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    private function extractClassName(string $file): string {
        return basename($file, '.php');
    }
}