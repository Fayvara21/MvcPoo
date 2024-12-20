<?php
class Task
{
    public static function all()
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->query("SELECT * FROM tasks ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function create($title, $projectId)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("INSERT INTO `tasks` (`id`, `title`, `is_completed`, `created_at`, `project_id`) VALUES (NULL, :title, 0, current_timestamp(), :project_id);");
        $stmt->execute(['title' => $title, 'project_id' => $projectId]);
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
    public static function listProjects($projectId)
    {
        $db = Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM tasks WHERE project_id = :project_id ORDER BY created_at DESC");
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
