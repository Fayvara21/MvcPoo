<?php

require_once '../core/Database.php';

class ThirdParty
{
    /**
     * Create a single 3rd_party entry
     */
    public static function create($taskId, $bp = null, $equipement = null, $nb = 1, $destination = null, $order_nb = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO 3rd_party (TaskID, bp, equipement, nb, destination, order_nb)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $taskId, $bp, $equipement, $nb, $destination, $order_nb
        ]);
    }

    /**
     * Create multiple 3rd_party entries
     */
    public static function createMultiple($taskId, array $thirdPartyRows, $sharedDestination = null, $sharedOrderNb = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO 3rd_party (TaskID, bp, equipement, nb, destination, order_nb)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($thirdPartyRows as $row) {
            // Skip empty rows (important!)
            if (empty($row['bp']) && empty($row['equipement'])) {
                continue;
            }

            $stmt->execute([
                $taskId,
                $row['bp'] ?? null,
                $row['equipement'] ?? null,
                $row['nb'] ?? 1,
                $sharedDestination ?? $row['destination'] ?? null,
                $sharedOrderNb ?? $row['order_nb'] ?? null,
            ]);
        }
    }

    /**
     * Fetch all 3rd_party rows for a task
     */
    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            SELECT * FROM 3rd_party WHERE TaskID = ? ORDER BY ID ASC
        ");

        $stmt->execute([$taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Edit multiple 3rd_party rows (delete old, insert new)
     */
    public static function editMultiple($taskId, array $thirdPartyRows, $sharedDestination = null, $sharedOrderNb = null)
    {
        self::deleteByTaskId($taskId);
        self::createMultiple($taskId, $thirdPartyRows, $sharedDestination, $sharedOrderNb);
    }

    /**
     * Update multiple 3rd_party rows (preserve IDs)
     */
    public static function updateMultiple($taskId, array $thirdPartyRows, $sharedDestination = null, $sharedOrderNb = null)
    {
        $db = Database::getInstance()->getPdo();

        // First, delete rows that were removed
        self::deleteByTaskId($taskId);

        // Then insert all current rows (including those with IDs)
        $stmt = $db->prepare("
            INSERT INTO 3rd_party (ID, TaskID, bp, equipement, nb, destination, order_nb)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                bp = VALUES(bp),
                equipement = VALUES(equipement),
                nb = VALUES(nb),
                destination = VALUES(destination),
                order_nb = VALUES(order_nb)
        ");

        foreach ($thirdPartyRows as $row) {
            if (empty($row['bp']) && empty($row['equipement'])) {
                continue;
            }

            $stmt->execute([
                $row['ID'] ?? null,
                $taskId,
                $row['bp'] ?? null,
                $row['equipement'] ?? null,
                $row['nb'] ?? 1,
                $sharedDestination ?? $row['destination'] ?? null,
                $sharedOrderNb ?? $row['order_nb'] ?? null,
            ]);
        }
    }

    /**
     * Delete all 3rd_party rows for a task
     */
    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM 3rd_party WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }

    /**
     * Delete a single 3rd_party row by ID
     */
    public static function deleteById($id)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM 3rd_party WHERE ID = ?");
        $stmt->execute([$id]);
    }
}