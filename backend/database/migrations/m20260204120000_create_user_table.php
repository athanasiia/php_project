<?php

//add

// TODO: add PHPDoc comment for class - describe what this migration does
class CreateUserTable
{
    private $db; // TODO: add type hint - specify type (e.g., PDO, mysqli, or custom DatabaseConnection type)

    public function __construct($db) // TODO: add type hint for parameter - specify database connection type
    {
        $this->db = $db;
    }

    // TODO: add PHPDoc comment - @return void, describe what this method does
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            country VARCHAR(2) NOT NULL,
            city VARCHAR(100) NOT NULL,
            gender ENUM('male', 'female') NOT NULL,
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'
        ) ENGINE=InnoDB;";

        $this->db->query($sql); // TODO: add error handling - wrap in try-catch, check query result, throw exception on failure 
    }

    // TODO: add PHPDoc comment - @return void, describe what this method does
    public function down(): void
    {
        $sql = "DROP TABLE IF EXISTS users"; // TODO: extract table name to constant - define TABLE_NAME constant for reusability
        $this->db->query($sql); // TODO: add error handling - wrap in try-catch, check query result, throw exception on failure
    }
}
