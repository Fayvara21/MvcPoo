<?php

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
    
    public static function markAsCompleted($id, $state)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("UPDATE `tasks` SET `is_completed` = (:state) WHERE `tasks`.`id` = (:id);");
        $stmt->execute(['id' => $id, 'state' => $state]);
    }
    
	public static function delete($id)
	{
		$db = Database::getInstance()->getPdo();

		$stmt = $db->prepare("DELETE FROM `appro` WHERE `TaskID` = :id");
		$stmt->execute(['id' => $id]);

		$stmt = $db->prepare("DELETE FROM `retour` WHERE `TaskID` = :id");
		$stmt->execute(['id' => $id]);

		$stmt = $db->prepare("DELETE FROM `tasks` WHERE `id` = :id");
		$stmt->execute(['id' => $id]);
	}
        
    public static function getAuthorizedTasksForAllProjects()
    {
        $db = Database::getInstance()->getPdo();

        // Get all user groups
        $userGroups = explode(',', $_SESSION['group']); // e.g., "admin,user"

        // Build FIND_IN_SET OR conditions dynamically
        $conditions = [];
        foreach ($userGroups as $group) {
            $g = trim($group);
            if ($g !== '') {
                $conditions[] = "FIND_IN_SET('$g', p.`groups`)";
            }
        }

        if (empty($conditions)) return []; // no groups

        $where = '(' . implode(' OR ', $conditions) . ')';

        // Fetch all in-progress tasks
        $sql = "
            SELECT 
                t.id,
                t.title,
                t.description,
                t.created_at,
                t.due_date,
                t.project_id,
                t.is_completed,

                p.title AS project_title,

                a.pn AS appro_pn,
                a.nb AS appro_nb,
                a.designation AS designation,
                a.location AS location,
                a.plane AS plane,
                a.of AS `of`,
                a.oe AS oe,

                r.pn AS retour_pn,
                r.nb AS retour_nb,
                r.sn AS sn,
                r.certif AS certif

            FROM tasks t
            INNER JOIN projects p ON t.project_id = p.id
            LEFT JOIN appro a ON t.id = a.TaskID
            LEFT JOIN retour r ON t.id = r.TaskID

            WHERE {$where} 
            AND t.is_completed = 0

            ORDER BY t.due_date IS NULL, t.due_date ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getAuthorizedProjects()
    {
        $db = Database::getInstance()->getPdo();

        $userGroups = explode(',', $_SESSION['group']);

        $conditions = [];
        foreach ($userGroups as $group) {
            $g = trim($group);
            if ($g !== '') {
                $conditions[] = "FIND_IN_SET('$g', `groups`)";
            }
        }

        if (empty($conditions)) return [];

        $where = '(' . implode(' OR ', $conditions) . ')';

        $stmt = $db->query("
            SELECT id, title
            FROM projects
            WHERE {$where}
            ORDER BY title ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function findTask($taskID)
    {
        $db = Database::getInstance()->getPdo();
        
        $stmt = $db->prepare("
            SELECT 
                t.*,
                a.pn AS appro_pn,
                a.nb AS appro_nb,
                a.designation AS appro_designation,
                a.location AS appro_location,
                a.plane AS appro_plane,
                a.of AS appro_of,
                a.oe AS appro_oe,
                
                r.pn AS retour_pn,
                r.nb AS retour_nb,
                r.sn AS retour_sn,
                r.certif AS retour_certif
                
            FROM tasks t
            LEFT JOIN appro a ON a.TaskID = t.id
            LEFT JOIN retour r ON r.TaskID = t.id
            WHERE t.id = ?
        ");
        
        $stmt->execute([$taskID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function edit($title, $desc, $taskId, $dueDate = null)
    {
        $db = Database::getInstance()->getPdo();
        
        $stmt = $db->prepare("
            UPDATE tasks 
            SET title = :title, 
                description = :description, 
                due_date = :due_date
            WHERE id = :id
        ");
        
        $stmt->execute([
            'title' => $title,
            'description' => $desc,
            'due_date' => $dueDate ?: null,
            'id' => $taskId
        ]);
    }
	
	public static function getAllProjects()
	{
		$db = Database::getInstance()->getPdo();
		$stmt = $db->query("SELECT id, title FROM projects ORDER BY title ASC");
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public static function getAllTasksForAllProjects()
	{
		$db = Database::getInstance()->getPdo();

		$stmt = $db->query("
			SELECT 
				t.id,
				t.title,
				t.description,
				t.created_at,
				t.due_date,
				t.project_id,
				t.is_completed,

				p.title AS project_title,

				a.pn AS appro_pn,
				a.nb AS appro_nb,
				a.designation AS designation,
				a.location AS location,
				a.plane AS plane,
				a.of AS `of`,
				a.oe AS oe,

				r.pn AS retour_pn,
				r.nb AS retour_nb,
				r.sn AS sn,
				r.certif AS certif

			FROM tasks t
			INNER JOIN projects p ON t.project_id = p.id
			LEFT JOIN appro a ON t.id = a.TaskID
			LEFT JOIN retour r ON t.id = r.TaskID
			ORDER BY t.due_date IS NULL, t.due_date ASC
		");

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

public static function getAuthorizedTasksByProject($projectId)
{
    $db = Database::getInstance()->getPdo();

    // 1. Get project
    $stmt = $db->prepare("SELECT id, `groups` FROM projects WHERE id = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        return [];
    }

    // 2. Check access (handle NULL groups safely)
    $userGroups = isset($_SESSION['group'])
        ? array_map('trim', explode(',', $_SESSION['group']))
        : [];

    $projectGroups = !empty($project['groups'])
        ? array_map('trim', explode(',', $project['groups']))
        : [];

    if (empty(array_intersect($userGroups, $projectGroups))) {
        return [];
    }

    // 3. Get tasks
    $stmt = $db->prepare("
        SELECT 
            id,
            title,
            description,
            created_at,
            due_date,
            project_id,
            is_completed
        FROM tasks
        WHERE project_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$projectId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tasks)) {
        return [];
    }

    // 4. Extract task IDs
    $taskIds = array_column($tasks, 'id');
    $placeholders = implode(',', array_fill(0, count($taskIds), '?'));

    // 5. Fetch appro
    $stmt = $db->prepare("
        SELECT 
            TaskID,
            pn,
            nb,
            designation,
            location,
            plane,
            `of`,
            oe
        FROM appro
        WHERE TaskID IN ($placeholders)
    ");
    $stmt->execute($taskIds);
    $approRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Fetch retour
    $stmt = $db->prepare("
        SELECT 
            TaskID,
            PN,
            nb,
            sn,
            certif
        FROM retour
        WHERE TaskID IN ($placeholders)
    ");
    $stmt->execute($taskIds);
    $retourRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 7. Group appro by TaskID
    $approByTask = [];
    foreach ($approRows as $row) {
        $approByTask[$row['TaskID']][] = $row;
    }

    // 8. Group retour by TaskID
    $retourByTask = [];
    foreach ($retourRows as $row) {
        $retourByTask[$row['TaskID']][] = $row;
    }

    // 9. Attach data to tasks
    foreach ($tasks as &$task) {
        $taskId = $task['id'];

        $task['appro'] = $approByTask[$taskId] ?? [];
        $task['retour'] = $retourByTask[$taskId] ?? [];
    }

    return $tasks;
}

}
