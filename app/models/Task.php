<?php

require_once '../core/Database.php';
require_once 'Appro.php';
require_once 'Retour.php';

class Task
{
    public static function all()
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->query("SELECT * FROM tasks ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($title, $desc, $projectId, $dueDate = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            INSERT INTO tasks 
            (title, is_completed, created_at, project_id, description, due_date, user_id) 
            VALUES 
            (:title, 0, CURRENT_TIMESTAMP, :project_id, :description, :due_date, :user_id)
        ");

        $stmt->execute([
            'title' => $title,
            'project_id' => $projectId,
            'description' => $desc,
            'due_date' => $dueDate ?: null,
            'user_id' => $_SESSION["user_id"]
        ]);

        return $db->lastInsertId();
    }

    public static function edit($title, $desc, $taskId, $dueDate = null)
    {
        $db = Database::getInstance()->getPdo();

        $stmt = $db->prepare("
            UPDATE tasks 
            SET title = :title, description = :description, due_date = :due_date
            WHERE id = :id
        ");

        $stmt->execute([
            'title' => $title,
            'description' => $desc,
            'due_date' => $dueDate ?: null,
            'id' => $taskId
        ]);
    }

    public static function markAsCompleted($id, $state)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("UPDATE tasks SET is_completed = :state WHERE id = :id");
        $stmt->execute(['id' => $id, 'state' => $state]);
    }

    public static function delete($id)
    {
        Appro::deleteByTaskId($id);
        Retour::deleteByTaskId($id);

        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function findTask($taskID)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$taskID]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($task) {
            $tasksWithData = self::attachApproRetour([$task]);
            return $tasksWithData[0] ?? $task;
        }
        return null;
    }

    public static function getAuthorizedTasksByProject($projectId)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM tasks WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->execute([$projectId]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return self::attachApproRetour($tasks);
    }

    public static function getAllTasksForAllProjects()
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->query("
            SELECT t.*, p.title AS project_title
            FROM tasks t
            INNER JOIN projects p ON t.project_id = p.id
            ORDER BY t.due_date IS NULL, t.due_date ASC
        ");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return self::attachApproRetour($tasks);
    }

    public static function getAuthorizedTasksForAllProjects()
    {
        $db = Database::getInstance()->getPdo();
        $userGroups = isset($_SESSION['group']) ? array_map('trim', explode(',', $_SESSION['group'])) : [];
        if (empty($userGroups)) return [];

        $conditions = array_map(fn($g) => "FIND_IN_SET('$g', p.`groups`)", $userGroups);
        $where = '(' . implode(' OR ', $conditions) . ')';

        $sql = "
            SELECT t.*, p.title AS project_title
            FROM tasks t
            INNER JOIN projects p ON t.project_id = p.id
            WHERE {$where} AND t.is_completed = 0
            ORDER BY t.due_date IS NULL, t.due_date ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return self::attachApproRetour($tasks);
    }

    // Attach APPRO & RETOUR to tasks
    private static function attachApproRetour(array $tasks): array
    {
        if (empty($tasks)) return [];

        $db = Database::getInstance()->getPdo();
        $taskIds = array_column($tasks, 'id');
        $placeholders = implode(',', array_fill(0, count($taskIds), '?'));

        // APPRO
        $stmt = $db->prepare("SELECT TaskID, pn, nb, designation, location, plane, `of`, oe FROM appro WHERE TaskID IN ($placeholders)");
        $stmt->execute($taskIds);
        $approRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $approByTask = [];
        foreach ($approRows as $row) {
            $approByTask[$row['TaskID']][] = $row;
        }

        // RETOUR
        $stmt = $db->prepare("SELECT TaskID, PN, nb, sn, certif FROM retour WHERE TaskID IN ($placeholders)");
        $stmt->execute($taskIds);
        $retourRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $retourByTask = [];
        foreach ($retourRows as $row) {
            $retourByTask[$row['TaskID']][] = $row;
        }

        foreach ($tasks as &$task) {
            $id = $task['id'];
            $task['appro'] = $approByTask[$id] ?? [];
            $task['retour'] = $retourByTask[$id] ?? [];
        }

        return $tasks;
    }
}