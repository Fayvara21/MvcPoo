<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';

$action = $_GET['action'] ?? 'login';

$controller = new AuthController();

switch ($action) {
    case 'register':
        $controller->register();
        break;
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->login();
}
