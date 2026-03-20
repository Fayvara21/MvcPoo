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

   public function register($username, $password, $group)
{
    try {
        // First check if username already exists
        $checkSql = "SELECT id FROM users WHERE username = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$username]);
        
        if ($checkStmt->fetch()) {
            return ['success' => false, 'error' => 'Ce nom d\'utilisateur existe déjà.'];
        }
        
        // If not exists, proceed with registration
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO users (username, password, `group`) VALUES (?, ?, ?)");
        $result = $stmt->execute([$username, $hashed, $group]);
        
        return ['success' => $result];
        
    } catch (PDOException $e) {
        // If duplicate entry error occurs (as fallback)
        if ($e->errorInfo[1] == 1062) {
            return ['success' => false, 'error' => 'Ce nom d\'utilisateur existe déjà.'];
        }
        return ['success' => false, 'error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()];
    }
} 
    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
