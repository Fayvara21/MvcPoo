
<link rel="stylesheet" href="/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>





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

