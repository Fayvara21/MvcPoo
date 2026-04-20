<?php
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Router.php";
require_once __DIR__ . "/../app/models/Task.php";
require_once __DIR__ . "/../app/controllers/TaskController.php";
require_once __DIR__ . '/../app/models/Project.php';
require_once __DIR__ . '/../app/models/Contact.php';
require_once __DIR__ . "/../app/controllers/ContactController.php";
require_once __DIR__ . '/../app/models/Retour.php';
require_once __DIR__ . '/../app/models/Appro.php';
require_once __DIR__ . '/../app/controllers/ProjectController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$router = new Router();

// Root redirect
$router->add("/", function () {
    header('Location: /projects');
    exit;
});

// Task actions
$router->add('/projects/{project_id}/tasks/{task_id}/mark-completed', function ($project_id, $task_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $state = $_POST['state'] ?? 0;
        (new TaskController())->markAsCompleted($task_id, (int)$state);
    }
});

$router->add('/projects/{project_id}/tasks/{task_id}/delete', function ($project_id, $task_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        (new TaskController())->delete($task_id);
    }
});

$router->add('/projects/{project_id}/tasks/{task_id}/edit', function ($project_id, $task_id) {
    (new TaskController())->edit($task_id);
});

// Project & task views
$router->add('/projects', [new ProjectController(), 'index']);
$router->add('/projects/', [new ProjectController(), 'index']);
$router->add('/projects/{id}', [new TaskController(), 'index']);
$router->add('/projects/{id}/tasks', [new TaskController(), 'index']);
$router->add('/projects/{id}/tasks/create', [new TaskController(), 'create']);
$router->add('/projects/tasks/view', [new TaskController(), 'viewall']);
$router->add('/projects/tasks/view/json', [new TaskController(), 'jsonall']);


// Auth
$router->add('/login', [new AuthController(), 'login']);
$router->add('/logout', [new AuthController(), 'logout']);
$router->add("/register", [new AuthController(), 'register']);

$router->add('/contact', [new ContactController(), 'contact']);

$router->dispatch();
