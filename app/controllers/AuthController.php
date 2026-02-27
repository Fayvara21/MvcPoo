<?php

require_once '../app/models/User.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        session_start();
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user['username'];
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_part'] = $user["part"];
                header("Location: /projects");
                exit();
            } else {
                $error = "Invalid credentials";
                require '../app/views/login.php';
            }
        } else{
			require '../app/views/login.php';
		}
    }


    public function register()
    {
		if ($_SESSION['user_part'] === 'admin'){
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {	
				$this->userModel->register($_POST['username'], $_POST['password'], $_POST["part"]);
				header("Location: /login");
				exit();
			} else {
				require '../app/views/register.php';
        }
		
		} else {
			$error = "You must be an admin to create an account. Please contact your adminitrator.";
			header("Location: /login");
			exit();
		}
    }
	

    public function dashboard()
    {
		if (!isset($_SESSION['user_id'])) {
			header("Location: /login");
			exit();
		}
        require '../app/views/projects/index.php';
    }

    public function logout()
    {
        session_destroy();
        header("Location: /login");
        exit();
    }
	
}
