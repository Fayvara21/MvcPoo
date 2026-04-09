<?php

require_once '../core/Database.php';

class Appro
{
    // Create a single APPRO entry
    public static function create($taskId, $pn, $nb = 1, $designation = null, $of = null, $location = null, $plane = null, $oe = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO appro (TaskID, pn, nb, designation, `of`, location, plane, `oe`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $taskId,
            $pn,
            $nb,
            $designation,
            $of,
            $location,
            $plane,
            $oe
        ]);
    }

    // Create multiple APPRO entries with shared fields
    public static function createMultiple($taskId, array $approRows, $designation = null, $of = null, $location = null, $plane = null, $oe = null)
    {
        foreach ($approRows as $row) {
            if (empty($row['pn'])) continue; // skip empty rows
            self::create(
                $taskId,
                $row['pn'] ?? null,
                $row['nb'] ?? 1,
                $designation,
                $of,
                $location,
                $plane,
                $oe
            );
        }
    }

    // Edit multiple APPRO rows: delete old + recreate
    public static function editMultiple($taskId, array $approRows, $designation = null, $of = null, $location = null, $plane = null, $oe = null)
    {
        self::deleteByTaskId($taskId);
        self::createMultiple($taskId, $approRows, $designation, $of, $location, $plane, $oe);
    }

    // Fetch all APPRO rows for a task
    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM appro WHERE TaskID = ? ORDER BY id ASC");
        $stmt->execute([$taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Delete all APPRO rows for a task
    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("DELETE FROM appro WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }
}