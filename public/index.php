<?php
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Router.php";
require_once __DIR__ . "/../app/models/Task.php";
require_once __DIR__ . "/../app/controllers/TaskController.php";
require_once __DIR__ . '/../app/models/Project.php';
require_once __DIR__ . '/../app/controllers/ProjectController.php';


$router = new Router();
$router->add("/", function() {
    header('Location: /projects');
    exit;
}); 

$router->add('/projects/{project_id}/tasks/{task_id}/complete', function ($project_id, $task_id) {
    (new TaskController)->markAsCompleted($task_id);
});

$router->add('/projects/{project_id}/tasks/{task_id}/delete', function ($project_id, $task_id) {
    (new TaskController)->delete($task_id);
});

$router->add('/projects', [new ProjectController, 'index']);
$router->add('/projects/create', [new ProjectController, 'create']);
$router->add('/projects/{id}/tasks', [new TaskController, 'index']);
$router->add('/projects/{id}/tasks/create', [new TaskController, 'create']);
$router->dispatch();

