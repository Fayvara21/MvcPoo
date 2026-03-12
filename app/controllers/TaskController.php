<?php
require_once '../app/controllers/BaseController.php';
//require_once '../app/models/Appro.php';
//require_once '../app/models/Retour.php';

class TaskController extends BaseController
{
    public function index($id)
    {
		$this->requireAuth();
        $project = Project::find($id);
		if(!str_contains($project["groups"], $_SESSION["group"])){
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

		if(!str_contains($project["groups"], $_SESSION["group"])){
			http_response_code(403);
			echo json_encode(["error" => "Unauthorized"]);
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {

			$title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
			$desc  = htmlspecialchars($_POST['desc'], ENT_QUOTES, 'UTF-8');
			$dueDate  = htmlspecialchars($_POST['dueDate'], ENT_QUOTES, 'UTF-8');
			
			$type  = $_POST['type'];

			$taskId = Task::create($title,$desc,$id,$dueDate);

			if ($type === "appro") {

				Appro::create(
					$taskId,
					$_POST['appro_pn'],
					$_POST['appro_nb'],
					$_POST['appro_designation'],
					$_POST['appro_of'],
					$_POST['appro_location'],
					$_POST['appro_plane'],
					$_POST['appro_oe']
				);
			}

			if ($type === "retour") {

				Retour::create(
					$taskId,
					$_POST['retour_pn'],
					$_POST['retour_nb'],
					$_POST['retour_sn'],
					$_POST['retour_certif']
				);
			}

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
	public function view($id)
    {
		$this->requireAuth();
        $project = Project::find($id);
		if(!str_contains($project["groups"], $_SESSION["group"])){
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
		$tasks = Task::getAuthorizedTasksForAllProjects();
		header('Content-Type: application/json');
		echo json_encode($tasks);
	}
}
