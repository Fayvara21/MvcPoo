<?php
require_once '../app/controllers/BaseController.php';

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

            $title = $_POST['title'] ?? '';
            $desc = $_POST['desc'] ?? '';
            $dueDate = $_POST['dueDate'] ?? null;
            $type = $_POST['type'] ?? '';

            $taskId = Task::create($title, $desc, $id, $dueDate);

            // === APPRO ===
            if ($type === 'appro' && !empty($_POST['appro'])) {
                Appro::createMultiple(
                    $taskId,
                    $_POST['appro'],
                    $_POST['appro_designation'] ?? null,
                    $_POST['appro_of'] ?? null,
                    $_POST['appro_location'] ?? null,
                    $_POST['appro_plane'] ?? null,
                    $_POST['appro_oe'] ?? null
                );
            }

            // === RETOUR ===
            if ($type === 'retour' && !empty($_POST['retour'])) {
                Retour::createMultiple(
                    $taskId,
                    $_POST['retour'],
                    $_POST['retour_sn'] ?? null,
                    $_POST['retour_certif'] ?? null
                );
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // === UPDATE TASK ===
            Task::edit(
                $_POST['title'] ?? '',
                $_POST['desc'] ?? '',
                $task['id'],
                $_POST['dueDate'] ?: null
            );

            $type = $_POST['type'] ?? '';

            // === APPRO ===
            if ($type === 'appro') {
                Appro::editMultiple(
                    $task['id'],
                    $_POST['appro'] ?? [],
                    $_POST['appro_designation'] ?? null,
                    $_POST['appro_of'] ?? null,
                    $_POST['appro_location'] ?? null,
                    $_POST['appro_plane'] ?? null,
                    $_POST['appro_oe'] ?? null
                );

                // Ensure RETOUR is cleared
                Retour::deleteByTaskId($task['id']);
            }

            // === RETOUR ===
            if ($type === 'retour') {
                Retour::editMultiple(
                    $task['id'],
                    $_POST['retour'] ?? [],
                    $_POST['retour_sn'] ?? null,
                    $_POST['retour_certif'] ?? null
                );

                // Ensure APPRO is cleared
                Appro::deleteByTaskId($task['id']);
            }

            header("Location: /projects/{$task['project_id']}/tasks");
            exit;
        }

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