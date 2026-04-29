<?php

require_once '../core/Database.php';

class Expedition
{
    /**
     * Create a single expedition entry
     */
    public static function create($taskId, $pn = null, $name = null, $nb = 1, $location = null, $order_nb = null, $destination = null, $account = "ASI", $third_party = 0)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO expedition (TaskID, pn, name, nb, location, order_nb, destination, account, third_party)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $taskId, $pn, $name, $nb, $location, $order_nb, $destination, $account, $third_party
        ]);
    }

    /**
     * Create multiple expedition entries
     */
    public static function createMultiple($taskId, array $expeditionRows, $sharedDestination = null, $sharedOrderNb = null, $sharedLocation = null, $sharedAccount = "ASI", $sharedExpedition = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO expedition (TaskID, pn, name, nb, location, order_nb, destination, account, third_party)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($expeditionRows as $row) {
            // Skip empty rows (important!)
            if (empty($row['pn']) && empty($row['name'])) {
                continue;
            }

            $stmt->execute([
                $taskId,
                $row['pn'] ?? null,
                $row['name'] ?? null,
                $row['nb'] ?? 1,
                $sharedLocation ?? $row['location'] ?? null,
                $sharedOrderNb ?? $row['order_nb'] ?? null,
                $sharedDestination ?? $row['destination'] ?? null,
                $sharedAccount ?? $row['account'] ?? "ASI",
                $sharedExpedition ?? $row['third_party'] ?? 0,
            ]);
        }
    }

    /**
     * Fetch all expedition rows for a task
     */
    public static function findByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            SELECT * FROM expedition WHERE TaskID = ? ORDER BY ID ASC
        ");

        $stmt->execute([$taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Edit multiple expedition rows (delete old, insert new)
     */
    public static function editMultiple($taskId, array $expeditionRows, $sharedDestination = null, $sharedOrderNb = null, $sharedLocation = null, $sharedAccount = "ASI", $sharedExpedition = null)
    {
        self::deleteByTaskId($taskId);
        self::createMultiple($taskId, $expeditionRows, $sharedDestination, $sharedOrderNb, $sharedLocation, $sharedAccount, $sharedExpedition);
    }

    /**
     * Update multiple expedition rows (preserve IDs)
     */
    public static function updateMultiple($taskId, array $expeditionRows, $sharedDestination = null, $sharedOrderNb = null, $sharedLocation = null, $sharedAccount = "ASI", $sharedExpedition = null)
    {
        $db = Database::getInstance()->getPdo();

        // First, delete rows that were removed
        self::deleteByTaskId($taskId);

        // Then insert all current rows (including those with IDs)
        $stmt = $db->prepare("
            INSERT INTO expedition (ID, TaskID, pn, name, nb, location, order_nb, destination, account, third_party)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                pn = VALUES(pn),
                name = VALUES(name),
                nb = VALUES(nb),
                location = VALUES(location),
                order_nb = VALUES(order_nb),
                destination = VALUES(destination),
                account = VALUES(account),
                third_party = VALUES(third_party)
        ");

        foreach ($expeditionRows as $row) {
            if (empty($row['pn']) && empty($row['name'])) {
                continue;
            }

            $stmt->execute([
                $row['ID'] ?? null,
                $taskId,
                $row['pn'] ?? null,
                $row['name'] ?? null,
                $row['nb'] ?? 1,
                $sharedLocation ?? $row['location'] ?? null,
                $sharedOrderNb ?? $row['order_nb'] ?? null,
                $sharedDestination ?? $row['destination'] ?? null,
                $sharedAccount ?? $row['account'] ?? "ASI",
                $sharedExpedition ?? $row['third_party'] ?? 0,
            ]);
        }
    }

    /**
     * Delete all expedition rows for a task
     */
    public static function deleteByTaskId($taskId)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM expedition WHERE TaskID = ?");
        $stmt->execute([$taskId]);
    }

    /**
     * Delete a single expedition row by ID
     */
    public static function deleteById($id)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("DELETE FROM expedition WHERE ID = ?");
        $stmt->execute([$id]);
    }
}