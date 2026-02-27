<?php

require_once '../core/Database.php';

class User
{
    private $db;

    public function __construct()
    {
        // Use singleton pattern with getPdo()
        $this->db = Database::getInstance()->getPdo();
    }

    public function register($username, $password, $part)
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, part) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hashed, $part]);
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
