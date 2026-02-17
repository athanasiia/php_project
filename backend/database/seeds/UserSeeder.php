<?php

use database\seeds\UserFactory;

class UserSeeder
{
    private PDO $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function run(): bool
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = UserFactory::create();
            $result = $this->insertUser($user);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function insertUser($user): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, name, country, city, gender, status) 
            VALUES (:email, :name, :country, :city, :gender, :status)
        ");

        try {
            $result = $stmt->execute($user);
            if ($result === false) {
                throw new Exception('Database query failed');
            }

            return true;
        } catch (PDOException $e) {
            throw new Exception('Failed to insert data: ' . $e->getMessage());
        }
    }
}