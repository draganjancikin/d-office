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
    protected string $page;
    protected string $page_title;
    protected string $stylesheet;

    /**
     * HomeController constructor.
     *
     * Initializes controller properties, loads app version from composer.json,
     * and sets stylesheet path.
     *
     * @param EntityManagerInterface $entityManager
     *   Doctrine entity manager for database operations.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->page = 'home';
        $this->page_title = 'd-Office 2025';
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
        session_start();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return $this->redirectToRoute('login_form');
        }

        $user = $this->entityManager->getRepository('App\\Entity\\User')->findOneBy(['username' => $username]);

        // Check if user exists and password matches
        if ($user && $user->getPassword() === $password) { // If passwords are hashed, use password_verify($password, $user->getPassword())
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_role_id'] = $user->getRoleId();
            return $this->redirectToRoute('home_index');
        } else {
            // Invalid credentials
            return $this->redirectToRoute('login_form');
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
