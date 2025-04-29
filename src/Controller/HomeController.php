<?php

namespace App\Controller;

use App\Core\BaseController;

/**
 * HomeController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class HomeController extends BaseController
{

    /**
     * HomeController constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Index method.
     *
     * @return void
     *   The rendered view.
     */
    public function index(): void
    {
        $data = [
            'app_version' => APP_VERSION,
            'page_title' => 'D-Office 2025',
            'stylesheet' => 'libraries/',
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'page' => 'home',
            'entityManager' => $this->entityManager,
            'number_of_clients' => $this->entityManager->getRepository('\App\Entity\Client')->count([]),
            'number_of_accounting_documents' => $this->entityManager->getRepository('\App\Entity\AccountingDocument')->count([]),
            'number_of_cutting_sheets' => $this->entityManager->getRepository('\App\Entity\CuttingSheet')->count([]),
            'number_of_materials' => $this->entityManager->getRepository('\App\Entity\Material')->count([]),
            'number_of_orders' => $this->entityManager->getRepository('\App\Entity\Order')->count([]),
            'number_of_articles' => $this->entityManager->getRepository('\App\Entity\Article')->count([]),
            'number_of_projects' => $this->entityManager->getRepository('\App\Entity\Project')->count([]),
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('home/index.html.twig', $data);
    }

    /**
     * Login method.
     *
     * @return void
     *   The rendered view.
     */
    public function loginForm(): void
    {
        $data = [
            'page_title' => APP_VERSION,
            'stylesheet' => 'libraries/',
        ];

        $this->render('home/login_form.html.twig', $data);
    }

    /**
     * Login the user.
     *
     * @return void
     */
    public function login(): void
    {
        require_once '../config/dbConfig.php';

        $table = "v6__users";    // the table that this script will set up and use.

        // Create connection.
        $mysqli = new \mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check connection.
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $username = $_POST["username"];
        $password = $_POST["password"];

        $match = "select id from $table where username = '".$_POST['username']."'and password = '".$_POST['password']."'";

        $result = mysqli_query($mysqli, $match);
        $num_rows = mysqli_num_rows($result);

        if ($num_rows <= 0) {
            // Redirection ako nije dobar user
            header('location: /login');
            exit();
        }
        else {
            $result_user = mysqli_query($mysqli, "SELECT * FROM $table WHERE username='$username' ") or die(mysqli_error($mysqli));

            $row_user = mysqli_fetch_array($result_user);
            $user_id = $row_user['id'];
            $user_role_id = $row_user['role_id'];

            $_SESSION['username'] = $_POST["username"];
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role_id'] = $user_role_id;

            header('location: /');
        }

    }

    /**
     * Logout the user.
     *
     * @return void
     */
    public function logout(): void
    {
        session_start();
        unset($_SESSION['username']);
        unset($_SESSION['user_role_id']);
        header("Location: /");
    }

}
