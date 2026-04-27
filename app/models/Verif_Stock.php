<?php

require_once '../core/Database.php';

class Verif_Stock
{
    /**
     * Create a single VERIF_STOCK entry
     */
    public static function create($taskId, $pn, $nb, $name, $location)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO verif_stock (TaskID, pn, nb, name, location)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $taskId, $pn, $nb, $name, $location
        ]);
    }

    /**
     * Create multiple VERIF_STOCK entries
     */
    public static function createMultiple($taskId, array $verifStockRows)
    {
        $db = Database::getInstance()->getPdo();


        $stmt = $db->prepare("
            INSERT INTO verif_stock (TaskID, pn, nb, name, location)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($verifStockRows as $row) {
            // Skip empty rows (important!)
            if (empty($row['pn'])) {
                continue;
            }

            $stmt->execute([
                $taskId,
                $row['pn'] ?? null,
                $row['nb'] ?? 1,
                $row['name'] ?? null,
                $row['location'] ?? null,
            ]);
        }
    }

    /**
     * Fetch all VERIF_STOCK rows for a task
     */
    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            SELECT * FROM verif_stock WHERE TaskID = ? ORDER BY id ASC
        ");

        $stmt->execute([$taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Edit multiple VERIF_STOCK rows (delete old, insert new)
     */
    public static function editMultiple($taskId, array $verifStockRows)
    {
        self::deleteByTaskId($taskId);
        self::createMultiple($taskId, $verifStockRows);
    }

    /**
     * Update multiple VERIF_STOCK rows (preserve IDs)
     */
    public static function updateMultiple($taskId, array $verifStockRows)
    {
        $db = Database::getInstance()->getPdo();

        // First, delete rows that were removed
        self::deleteByTaskId($taskId);

        // Then insert all current rows (including those with IDs)
        $stmt = $db->prepare("
            INSERT INTO verif_stock (id, TaskID, pn, nb, name, location)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                pn = VALUES(pn),
                nb = VALUES(nb),
                name = VALUES(name),
                location = VALUES(location)
        ");

        foreach ($verifStockRows as $row) {
            if (empty($row['pn'])) {
                continue;
            }

            $stmt->execute([
                $row['id'] ?? null,
                $taskId,
                $row['pn'] ?? null,
                $row['nb'] ?? 1,
                $row['name'] ?? null,
                $row['location'] ?? null,
            ]);
        }
    }

    /**
     * Delete all VERIF_STOCK rows for a task
     */
    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM verif_stock WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }

    /**
     * Delete a single VERIF_STOCK row by ID
     */
    public static function deleteById($id)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM verif_stock WHERE id = ?");
        $stmt->execute([$id]);
    }
}
