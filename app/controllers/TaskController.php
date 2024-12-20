<?php
class TaskController
{
    public function index($id)
    {

        $project = Project::find($id);
        $tasks = Task::listProjects($id);
        include __DIR__ . "/../views/tasks/index.php";
    }
    
    public function create($id)
    {
        $project = Project::find($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
            Task::create($title, $id);
            header("Location: /projects/$id/tasks");
            exit;
        }

        include __DIR__ . '/../views/tasks/create.php';
    }
    public function markAsCompleted($id)
    {
        Task::markAsCompleted($id);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();

    }
    public function delete($id)
    {
        Task::delete($id);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
