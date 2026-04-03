<?php

require_once '../core/Database.php';

class Retour
{
    /**
     * Create a single RETOUR entry for a task.
     */
    public static function create($taskId, $pn = null, $nb = 1, $sn = null, $certif = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO retour (TaskID, PN, nb, sn, certif)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $taskId,
            $pn,
            $nb,
            $sn,
            $certif
        ]);
    }

    /**
     * Create multiple RETOUR entries for a task
     * Uses shared SN / certif from form (retour_sn, retour_certif)
     * but allows per-row override if present
     */
    public static function createMultiple($taskId, array $retourRows, $sharedSn = null, $sharedCertif = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO retour (TaskID, PN, nb, sn, certif)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($retourRows as $row) {

            // Handle PN case mismatch safely
            $pn = $row['PN'] ?? $row['pn'] ?? null;

            // Priority: row value > shared value
            $sn = $row['sn'] ?? $sharedSn ?? null;
            $certif = $row['certif'] ?? $sharedCertif ?? null;

            $stmt->execute([
                $taskId,
                $pn,
                $row['nb'] ?? 1,
                $sn,
                $certif
            ]);
        }
    }

    /**
     * Edit multiple RETOUR rows for a task
     */
    public static function editMultiple($taskId, array $retourRows, $sharedSn = null, $sharedCertif = null)
    {
        self::deleteByTaskId($taskId);
        self::createMultiple($taskId, $retourRows, $sharedSn, $sharedCertif);
    }

    /**
     * Fetch all RETOUR rows for a task
     */
    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            SELECT * FROM retour WHERE TaskID = ? ORDER BY ID ASC
        ");

        $stmt->execute([$taskId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Delete all RETOUR rows for a task
     */
    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM retour WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }
}
