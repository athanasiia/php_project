<?php

use database\seeds\UserFactory;

class UserSeeder
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function run() //return type
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = UserFactory::create();
            $this->insertUser($user);
        }
    }

    private function insertUser($user) // return type
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, name, country, city, gender, status) 
            VALUES (:email, :name, :country, :city, :gender, :status)
        ");

        $stmt->execute($user); // add try catch
    }
}