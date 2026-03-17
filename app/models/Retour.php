<?php

require_once '../core/Database.php';

class Retour
{
    private $db;

    public static function create($taskId, $pn, $nb, $sn, $certif)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO retour (TaskID, pn, nb, sn, certif)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $taskId, $pn, $nb, $sn, $certif
        ]);
    }

    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            SELECT * FROM retour 
            WHERE TaskID = ?
            LIMIT 1
        ");

        $stmt->execute([$taskId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public static function edit($taskId, $pn, $nb, $sn, $certif)
    {
        $db = Database::getInstance()->getPdo();

        // Check if a RETOUR record already exists for this task
        $existing = self::findByTaskId($taskId);

        if ($existing) {
            // Update existing record
            $stmt = $db->prepare("
                UPDATE retour 
                SET pn = ?, nb = ?, sn = ?, certif = ?
                WHERE TaskID = ?
            ");

            $stmt->execute([
                $pn, $nb, $sn, $certif, $taskId
            ]);
        } else {
            // Create new record if it doesn't exist
            self::create($taskId, $pn, $nb, $sn, $certif);
        }
    }

    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM retour WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }
}