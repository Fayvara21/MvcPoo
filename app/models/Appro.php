<?php

require_once '../core/Database.php';

class Appro
{
    private $db;

    public static function create($taskId, $pn, $nb, $designation, $of, $location, $plane, $oe)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO appro (TaskID, pn, nb, designation, `of`, location, plane, `oe`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $taskId, $pn, $nb, $designation, $of, $location, $plane, $oe
        ]);
    }

    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            SELECT * FROM appro 
            WHERE TaskID = ?
            LIMIT 1
        ");

        $stmt->execute([$taskId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public static function edit($taskId, $pn, $nb, $designation, $of, $location, $plane, $oe)
    {
        $db = Database::getInstance()->getPdo();

        // Check if an APPRO record already exists for this task
        $existing = self::findByTaskId($taskId);

        if ($existing) {
            // Update existing record
            $stmt = $db->prepare("
                UPDATE appro 
                SET pn = ?, nb = ?, designation = ?, `of` = ?, location = ?, plane = ?, `oe` = ?
                WHERE TaskID = ?
            ");

            $stmt->execute([
                $pn, $nb, $designation, $of, $location, $plane, $oe, $taskId
            ]);
        } else {
            // Create new record if it doesn't exist
            self::create($taskId, $pn, $nb, $designation, $of, $location, $plane, $oe);
        }
    }

    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM appro WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }
}
