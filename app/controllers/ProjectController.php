<?php

class ProjectController
{
    public function index()
    {
        $projects = Project::all();
        include __DIR__ . '/../views/projects/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
            Project::create($name);
            header('Location: /projects');
            exit;
        }
        include __DIR__ . '/../views/projects/create.php';
    }
}
