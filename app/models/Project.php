<?php

class Project
{
	public static function all()
	{
		$db = Database::getInstance()->getPdo();

		$stmt = $db->query("
			SELECT 
				p.*,
				COUNT(t.id) AS tasks_count,
				SUM(
					CASE 
						WHEN t.is_completed = 0
						AND t.due_date IS NOT NULL
						AND TIMESTAMPDIFF(HOUR, NOW(), t.due_date) <= 24
						AND TIMESTAMPDIFF(HOUR, NOW(), t.due_date) >= 0
						THEN 1
						ELSE 0
					END
				) AS urgent_count
			FROM projects p
			LEFT JOIN tasks t ON t.project_id = p.id
			GROUP BY p.id
			ORDER BY p.id DESC
		");

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public static function allByGroup($group){
		$db = Database::getInstance()->getPdo();
		$stmt = $db->prepare("SELECT * FROM projects WHERE `groups` LIKE :group ORDER BY id DESC");
		$search = "%{$group}%";
		$stmt->bindParam(':group', $search, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

    public static function create($name)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("INSERT INTO projects (`id`, `title`) VALUES (NULL, (:name))");
        $stmt->execute(['name' => $name]);
    }

    public static function find($id)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM projects WHERE id = (:id)");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
