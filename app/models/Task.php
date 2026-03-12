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
			(title, is_completed, created_at, project_id, description, due_date) 
			VALUES 
			(:title, 0, CURRENT_TIMESTAMP, :project_id, :description, :due_date)
		");

		$stmt->execute([
			'title' => $title,
			'project_id' => $projectId,
			'description' => $desc,
			'due_date' => $dueDate ?: null
		]);

		return $db->lastInsertId();
	}
    public static function markAsCompleted($id)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("UPDATE `tasks` SET `is_completed` = '1' WHERE `tasks`.`id` = (:id);");
        $stmt->execute(['id' => $id]);
    }
    
    public static function delete($id)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("DELETE FROM `tasks` WHERE `tasks`.`id` = (:id)");
        $stmt->execute(['id' => $id]);
    }
	public static function getAuthorizedTasksByProject($projectId)
	{
		$db = Database::getInstance()->getPdo();

		$stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
		$stmt->execute([$projectId]);
		$project = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$project) return [];
		
		$userGroups = explode(',', $_SESSION['group']);
		$projectGroups = explode(',', $project['groups']);
		$hasAccess = false;
		foreach ($userGroups as $g) {
			if (in_array(trim($g), $projectGroups)) {
				$hasAccess = true;
				break;
			}
		}
		if (!$hasAccess) return [];

		$stmt = $db->prepare("
			SELECT 
				t.id,
				t.title,
				t.description,
				t.created_at,
				t.due_date,
				t.project_id,
				t.is_completed,

				a.pn AS appro_pn,
				a.nb AS appro_nb,
				a.designation,
				a.location,
				a.plane,

				r.PN AS retour_pn,
				r.nb AS retour_nb,
				r.sn,
				r.certif

			FROM tasks t
			LEFT JOIN appro a ON a.TaskID = t.id
			LEFT JOIN retour r ON r.TaskID = t.id
			WHERE t.project_id = ?
			ORDER BY t.created_at DESC
		");

		$stmt->execute([$projectId]);

		$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $tasks;
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

}
