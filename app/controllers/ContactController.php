<?php

require_once '../app/models/Contact.php';

class ContactController
{
    private $contactModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->contactModel = new Contact();
    }


    public function contact()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // HANDLE CONTACT FORM SUBMISSION

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
            $user = htmlspecialchars($_POST['user'], ENT_QUOTES, 'UTF-8');

            $this->contactModel->create($email, $type, $description, $user);

            header("Location: /projects");
            exit();


        }

        if (isset($_SESSION['group']) && $_SESSION['group'] === 'admin') {
            $contacts = Contact::view();
            require "../app/views/contacts/index.php";
        }
        else{
            require '../app/views/contacts/create.php';
        }
        

    }

}
