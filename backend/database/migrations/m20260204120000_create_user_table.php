<?php

class CreateUserTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function up()
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

        $this->db->query($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS users";
        $this->db->query($sql);
    }
}
