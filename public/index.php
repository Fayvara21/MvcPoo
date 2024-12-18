
<link rel="stylesheet" href="/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Router.php";
require_once __DIR__ . "/../app/models/Task.php";
require_once __DIR__ . "/../app/controllers/TaskController.php";
require_once __DIR__ . '/../app/models/Project.php';
require_once __DIR__ . '/../app/controllers/ProjectController.php';

$router = new Router();
$router->add("/", [new TaskController(), "index"]);
$router->add("/create", [new TaskController(), "create"]);
$router->add("/complete", function () {
    $id = $_POST["id"] ?? null;
    (new TaskController())->markAsCompleted($id);
});
$router->add("/delete", function () {
    $id = $_POST["id"] ?? null;
    (new TaskController())->delete($id);
});
$router->add('/tasks', [new TaskController, 'index']);
$router->add('/tasks/create', [new TaskController, 'create']);
$router->dispatch();

