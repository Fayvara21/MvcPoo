<?php
class TaskController
{
    public function index()
    {
        $projectId = $_GET['project_id'] ?? null;
        $project = Project::find($projectId);
        var_dump($_GET);
        $tasks = Task::all($projectId);
        include __DIR__ . "/../views/tasks/index.php";
    }
    
    public function create()
    {
      $projectId = $_GET['project_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            Task::create($title, $projectId);
            header("Location: /tasks?project_id=$projectId");
            exit;
        }
    
        $project = Project::find($projectId);
        include __DIR__ . '/../views/tasks/create.php';
    }
    public function markAsCompleted($id)
    {
        Task::markAsCompleted($id);
        header("Location: /");
        exit();

    }
    public function delete($id)
    {
        Task::delete($id);
        header("Location: /");
        exit();
    }
}
