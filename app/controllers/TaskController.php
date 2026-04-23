<?php

require_once '../app/controllers/BaseController.php';
require_once '../app/models/Appro.php';
require_once '../app/models/Retour.php';
require_once '../app/models/Verif_stock.php';

class TaskController extends BaseController
{
    public function index($id)
    {
        $this->requireAuth();
        $project = Project::find($id);
        if (!str_contains($project["groups"], $_SESSION["group"])) {
            http_response_code(403);
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
        $tasks = Task::getAuthorizedTasksByProject($id);
        include __DIR__ . "/../views/tasks/index.php";
    }

    public function create($id)
    {
        $this->requireAuth();

        $project = Project::find($id);
        if (!str_contains($project["groups"], $_SESSION["group"])) {
            http_response_code(403);
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
            $desc = htmlspecialchars($_POST['desc'], ENT_QUOTES, 'UTF-8');
            $dueDate = htmlspecialchars($_POST['dueDate'], ENT_QUOTES, 'UTF-8');
            $type = $_POST['type'] ?? '';

            // Create the main task
            $taskId = Task::create($title, $desc, $id, $dueDate);

            // === APPRO ===
            if ($type === 'appro' && !empty($_POST['appro'])) {
                $approRows = $_POST['appro'];

                // Shared fields
                $sharedFields = [
                    'designation' => $_POST['appro_designation'] ?? null,
                    'of' => $_POST['appro_of'] ?? null,
                    'plane' => $_POST['appro_plane'] ?? null,
                    'oe' => $_POST['appro_oe'] ?? null,
                ];

                foreach ($approRows as $row) {
                    $data = array_merge($row, $sharedFields);
                    Appro::create(
                        $taskId,
                        $data['pn'] ?? null,
                        $data['nb'] ?? 1,
                        $data['designation'] ?? null,
                        $data['of'] ?? null,
                        $data['location'] ?? null,
                        $data['plane'] ?? null,
                        $data['oe'] ?? null,
                    );
                }
            }

            // === RETOUR ===
            if ($type === 'retour' && !empty($_POST['retour'])) {
                $retourRows = $_POST['retour'];

                // Shared fields
                $sharedSn = $_POST['retour_sn'] ?? null;
                $sharedCertif = $_POST['retour_certif'] ?? null;

                foreach ($retourRows as $row) {
                    $data = array_merge($row, [
                        'sn' => $sharedSn,
                        'certif' => $sharedCertif,
                    ]);
                    Retour::create(
                        $taskId,
                        $data['pn'] ?? null,
                        $data['nb'] ?? 1,
                        $data['sn'] ?? null,
                        $data['certif'] ?? null,
                    );
                }
            }

            // === VERIF STOCK ===
            if ($type === 'verif_stock' && !empty($_POST['verif_stock'])) {
                $verifStockRows = $_POST['verif_stock'];

                foreach ($verifStockRows as $row) {
                    Verif_stock::create(
                        $taskId,
                        $row['pn'] ?? null,
                        $row['nb'] ?? 1,
                        $row['name'] ?? null,
                    );
                }
            }

            header("Location: /projects/$id/tasks");
            exit;
        }

        include __DIR__ . '/../views/tasks/create.php';
    }

    public function edit($taskId)
    {
        $this->requireAuth();

        $task = Task::findTask($taskId);
        $projectId = $task["project_id"];
        $project = Project::find($projectId);

        if (!str_contains($project["groups"], $_SESSION["group"])) {
            http_response_code(403);
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }

        if ($task['is_completed'] != 0 && $_SESSION["group"] != "admin") {
            http_response_code(403);
            echo "Cette tâche n'est plus modifiable.";
            return;
        }

        // FETCH VERIF_STOCK DATA FOR THIS TASK
        $verifStockList = Verif_stock::findByTaskId($task['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Update basic task info
            Task::edit(
                $_POST['title'] ?? '',
                $_POST['desc'] ?? '',
                $task['id'],
                $_POST['dueDate'] ?: null,
            );

            $type = $_POST['type'] ?? '';

            // Clear existing entries first
            Appro::deleteByTaskId($task['id']);
            Retour::deleteByTaskId($task['id']);
            Verif_stock::deleteByTaskId($task['id']);

            // === APPRO ===
            if ($type === 'appro' && !empty($_POST['appro'])) {
                foreach ($_POST['appro'] as $row) {
                    Appro::create(
                        $task['id'],
                        $row['pn'] ?? null,
                        $row['nb'] ?? 1,
                        $row['designation'] ?? null,
                        $row['of'] ?? null,
                        $row['location'] ?? null,
                        $row['plane'] ?? null,
                        $row['oe'] ?? null,
                    );
                }
            }

            // === RETOUR ===
            if ($type === 'retour' && !empty($_POST['retour'])) {
                foreach ($_POST['retour'] as $row) {
                    Retour::create(
                        $task['id'],
                        $row['PN'] ?? null,
                        $row['nb'] ?? 1,
                        $row['sn'] ?? null,
                        $row['certif'] ?? null,
                    );
                }
            }

            // === VERIF STOCK ===
            if ($type === 'verif_stock' && !empty($_POST['verif_stock'])) {
                foreach ($_POST['verif_stock'] as $row) {
                    Verif_stock::create(
                        $task['id'],
                        $row['pn'] ?? null,
                        $row['nb'] ?? 1,
                        $row['name'] ?? null,
                    );
                }
            }

            // Redirect to avoid duplicate POST
            header("Location: /projects/{$task['project_id']}/tasks");
            exit;
        }

        // PASS VERIF_STOCK DATA TO THE VIEW
        include __DIR__ . '/../views/tasks/edit.php';
    }

    public function markAsCompleted($id, $state)
    {
        $this->requireAuth();
        Task::markAsCompleted($id, $state);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function delete($id)
    {
        $this->requireAuth();
        Task::delete($id);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function view($id)
    {
        $this->requireAuth();
        $project = Project::find($id);
        if (!str_contains($project["groups"], $_SESSION["group"])) {
            http_response_code(403);
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
        $tasks = Task::getAuthorizedTasksByProject($id);
        include __DIR__ . "/../views/tasks/view.php";
    }

    public function viewall()
    {
        $this->requireAuth();
        $tasks = Task::getAuthorizedTasksForAllProjects();
        include __DIR__ . "/../views/tasks/view.php";
    }

    public function json($projectId)
    {
        $this->requireAuth();
        $tasks = Task::getAuthorizedTasksByProject($projectId);
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }

    public function jsonall()
    {
        $this->requireAuth();
        $tasks = Task::getAllTasksForAllProjects();
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }
}
