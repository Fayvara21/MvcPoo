<?php

class Project
{
    public static function all()
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->query("SELECT * FROM projects ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($name)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("INSERT INTO projects (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    }

    public static function find($id)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM projects WHERE id = (:id)");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
