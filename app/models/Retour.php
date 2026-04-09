<?php

require_once '../core/Database.php';

class Retour
{
    // Create a single RETOUR entry
    public static function create($taskId, $pn = null, $nb = 1, $sn = null, $certif = null)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("INSERT INTO retour (TaskID, PN, nb, sn, certif) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$taskId, $pn, $nb, $sn, $certif]);
    }

    // Create multiple RETOUR entries with shared SN/certif
    public static function createMultiple($taskId, array $retourRows, $sharedSn = null, $sharedCertif = null)
    {
        foreach ($retourRows as $row) {
            $pn = $row['PN'] ?? $row['pn'] ?? null;
            $sn = $row['sn'] ?? $sharedSn ?? null;
            $certif = $row['certif'] ?? $sharedCertif ?? null;
            if (empty($pn)) continue; // skip empty rows
            self::create(
                $taskId,
                $pn,
                $row['nb'] ?? 1,
                $sn,
                $certif
            );
        }
    }

    // Edit multiple RETOUR rows: delete old + recreate
    public static function editMultiple($taskId, array $retourRows, $sharedSn = null, $sharedCertif = null)
    {
        self::deleteByTaskId($taskId);
        self::createMultiple($taskId, $retourRows, $sharedSn, $sharedCertif);
    }

    // Fetch all RETOUR rows for a task
    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM retour WHERE TaskID = ? ORDER BY id ASC");
        $stmt->execute([$taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Delete all RETOUR rows for a task
    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("DELETE FROM retour WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }
}