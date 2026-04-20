<?php

require_once '../core/Database.php';

class Contact
{
    /**
     * Create a single APPRO entry
     */
    public static function create($email, $type, $description, $user)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO contact (email, type, description, user)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $email, $type, $description, $user
        ]);
    }
    public static function view(){
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECt * FROM contact");
        $stmt->execute();
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $contacts;
    }
}