<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HomeController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class HomeController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    protected string $page_title;
    protected string $page;
    protected string $app_version;
    protected string $stylesheet;

    /**
     * HomeController constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->page_title = 'd-Office 2025';
        $this->page = 'home';

        $composerJsonPath = __DIR__ . '/../../composer.json';
        if (file_exists($composerJsonPath)) {
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            $this->app_version = $composerData['version'] ?? 'unknown';
        } else {
            $this->app_version = 'unknown';
        }

        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Index method.
     *
     * @return Response
     *   The rendered view.
     */
    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page_title' => $this->page_title,
            'page' => $this->page,
            'number_of_clients' => $this->entityManager->getRepository('\App\Entity\Client')->count([]),
            'number_of_accounting_documents' => $this->entityManager->getRepository('\App\Entity\AccountingDocument')->count([]),
            'number_of_cutting_sheets' => $this->entityManager->getRepository('\App\Entity\CuttingSheet')->count([]),
            'number_of_materials' => $this->entityManager->getRepository('\App\Entity\Material')->count([]),
            'number_of_orders' => $this->entityManager->getRepository('\App\Entity\Order')->count([]),
            'number_of_articles' => $this->entityManager->getRepository('\App\Entity\Article')->count([]),
            'number_of_projects' => $this->entityManager->getRepository('\App\Entity\Project')->count([]),
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'user_id' => $_SESSION['user_id'],
            'app_version' => $this->app_version,
            'stylesheet' => $this->stylesheet,
        ];

        return $this->render('home/index.html.twig', $data);
    }

    /**
     * Render the login form.
     *
     * @return Response
     */
    #[Route('/login', name: 'login_form', methods: ['GET'])]
    public function loginForm(): Response
    {
        $data = [
            'page_title' => $this->page_title,
            'page' => $this->page,
            'stylesheet' => $this->stylesheet,
            'app_version' => $this->app_version,
        ];

        return $this->render('home/login_form.html.twig', $data);
    }

    /**
     * Login the user.
     *
     * @return Response
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): Response
    {

        $table = "v6__users";    // the table that this script will set up and use.

        // Create connection.
        $mysqli = new \mysqli("db", "user", "user", "default");

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
            return $this->redirectToRoute('login_form');
        }
        else {
            $result_user = mysqli_query($mysqli, "SELECT * FROM $table WHERE username='$username' ") or die(mysqli_error($mysqli));

            $row_user = mysqli_fetch_array($result_user);
            $user_id = $row_user['id'];
            $user_role_id = $row_user['role_id'];

            session_start();
            $_SESSION['username'] = $_POST["username"];
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role_id'] = $user_role_id;

            return $this->redirectToRoute('home_index');
        }

    }

    /**
     * Logout the user.
     *
     * @return Response
     */
    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): Response
    {
        session_start();
        $_SESSION = [];
        session_unset();
        session_destroy();

        return $this->redirectToRoute('home_index');
    }

}
