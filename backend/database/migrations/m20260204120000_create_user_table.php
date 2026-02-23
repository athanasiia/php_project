<?php

/**
 * Creates table "users" or removes it from the database
 */
class CreateUserTable
{
    private const string TABLE_NAME = 'users';
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Creates table "users" in the database
     *
     * @return void
     * @throws Exception
     */
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

        try {
            $result = $this->db->query($sql);
            if ($result === false) {
                throw new Exception('Database query failed');
            }
        } catch (PDOException $e) {
            throw new Exception('Failed to create table "users": ' . $e->getMessage());
        }
    }

    /**
     * Removes table "users" from the database
     *
     * @return void
     * @throws Exception
     */
    public function down(): void
    {
        $sql = "DROP TABLE IF EXISTS " . self::TABLE_NAME;

        try {
            $result = $this->db->query($sql);
            if ($result === false) {
                throw new Exception('Database query failed');
            }
        } catch (PDOException $e) {
            throw new Exception('Failed to remove table "' . self::TABLE_NAME . '": ' . $e->getMessage());
        }
    }
}
