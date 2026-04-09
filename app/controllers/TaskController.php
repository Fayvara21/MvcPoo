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

                $shared = [
                    'designation' => $_POST['appro_designation'] ?? null,
                    'of' => $_POST['appro_of'] ?? null,
                    'location' => $_POST['appro_location'] ?? null,
                    'plane' => $_POST['appro_plane'] ?? null,
                    'oe' => $_POST['appro_oe'] ?? null,
                ];

                foreach ($_POST['appro'] as $row) {

                    if (empty(trim($row['pn'] ?? ''))) continue;

                    Appro::create(
                        $taskId,
                        $row['pn'],
                        $row['nb'] ?? 1,
                        $shared['designation'],
                        $shared['of'],
                        $shared['location'],
                        $shared['plane'],
                        $shared['oe']
                    );
                }
            }

            // === RETOUR ===
            if ($type === 'retour' && !empty($_POST['retour'])) {

                $sn = $_POST['retour_sn'] ?? null;
                $certif = $_POST['retour_certif'] ?? null;

                foreach ($_POST['retour'] as $row) {

                    if (empty(trim($row['PN'] ?? ''))) continue;

                    Retour::create(
                        $taskId,
                        $row['PN'], // ✅ keep uppercase
                        $row['nb'] ?? 1,
                        $sn,
                        $certif
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // === UPDATE TASK ===
            Task::edit(
                $_POST['title'] ?? '',
                $_POST['desc'] ?? '',
                $task['id'],
                $_POST['dueDate'] ?: null
            );

            $type = $_POST['type'] ?? '';

            // =========================
            // ======== APPRO ==========
            // =========================
            if ($type === 'appro') {

                $existing = Appro::findByTaskId($task['id']);
                $existingIds = array_column($existing, 'ID');
                $submittedIds = [];

                $shared = [
                    'designation' => $_POST['appro_designation'] ?? null,
                    'of' => $_POST['appro_of'] ?? null,
                    'location' => $_POST['appro_location'] ?? null,
                    'plane' => $_POST['appro_plane'] ?? null,
                    'oe' => $_POST['appro_oe'] ?? null,
                ];

                foreach ($_POST['appro'] ?? [] as $row) {

                    if (empty(trim($row['pn'] ?? ''))) continue;

                    if (!empty($row['id'])) {
                        // UPDATE
                        Appro::update(
                            $row['id'],
                            $row['pn'],
                            $row['nb'] ?? 1,
                            $shared['designation'],
                            $shared['of'],
                            $shared['location'],
                            $shared['plane'],
                            $shared['oe']
                        );

                        $submittedIds[] = $row['id'];

                    } else {
                        // INSERT
                        Appro::create(
                            $task['id'],
                            $row['pn'],
                            $row['nb'] ?? 1,
                            $shared['designation'],
                            $shared['of'],
                            $shared['location'],
                            $shared['plane'],
                            $shared['oe']
                        );
                    }
                }

                // DELETE removed
                $toDelete = array_diff($existingIds, $submittedIds);
                foreach ($toDelete as $id) {
                    Appro::delete($id);
                }

                // clean opposite table
                Retour::deleteByTaskId($task['id']);
            }

            // =========================
            // ======== RETOUR =========
            // =========================
            if ($type === 'retour') {

                $existing = Retour::findByTaskId($task['id']);
                $existingIds = array_column($existing, 'ID');
                $submittedIds = [];

                $sn = $_POST['retour_sn'] ?? null;
                $certif = $_POST['retour_certif'] ?? null;

                foreach ($_POST['retour'] ?? [] as $row) {

                    if (empty(trim($row['PN'] ?? ''))) continue;

                    if (!empty($row['id'])) {
                        // UPDATE
                        Retour::update(
                            $row['id'],
                            $row['PN'], // ✅ keep uppercase
                            $row['nb'] ?? 1,
                            $sn,
                            $certif
                        );

                        $submittedIds[] = $row['id'];

                    } else {
                        // INSERT
                        Retour::create(
                            $task['id'],
                            $row['PN'],
                            $row['nb'] ?? 1,
                            $sn,
                            $certif
                        );
                    }
                }

                // DELETE removed
                $toDelete = array_diff($existingIds, $submittedIds);
                foreach ($toDelete as $id) {
                    Retour::delete($id);
                }

                // clean opposite table
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