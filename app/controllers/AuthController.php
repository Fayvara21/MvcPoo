<?php

require_once '../app/models/User.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
	if (session_status() === PHP_SESSION_NONE){
		session_start();
	} 
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
				$_SESSION['group'] = $user["group"];
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $part = $_POST['part'];

        $allowed = ['magasin', 'adv', 'part145', 'part21'];

        // Only admin can create admin accounts
        if (isset($_SESSION['group']) && $_SESSION['group'] === 'admin') {
            $allowed[] = 'admin';
        }

        if (!in_array($part, $allowed)) {
            $_SESSION['error'] = "Rôle non autorisé.";
            header("Location: /register");
            exit();
        }

        $result = $this->userModel->register($_POST['username'], $_POST['password'], $part);
        
        if ($result['success']) {
            $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: /login");
            exit();
        } else {
            $_SESSION['error'] = $result['error'] ?? "Erreur lors de l'inscription.";
            header("Location: /register");
            exit();
        }
    }
    require '../app/views/register.php';
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
