<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * BaseController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class BaseController {

    public $user_id;
    public $username;
    public $user_role_id;
    public $entityManager;
    protected $twig;

    /**
     * BaseController constructor.
     */
    public function __construct() {
        if (isset($_SESSION['user_id'])) $this->user_id = $_SESSION['user_id'];
        if (isset($_SESSION['username'])) $this->username = $_SESSION['username'];
        if (isset($_SESSION['user_role_id'])) $this->user_role_id = $_SESSION['user_role_id'];
        $this->entityManager = EntityManagerFactory::getEntityManager();

        // Set up Twig.
        $loader = new FilesystemLoader(__DIR__ . '/../../templates'); // adjust to your actual path
        $this->twig = new Environment($loader, [
            'cache' => false, // or use a path like __DIR__ . '/../../cache/twig' for caching
            'debug' => true,
        ]);
    }

    /**
     * Render a Twig template.
     *
     * @param string $template
     *   Template file (e.g., 'home/index.html.twig')
     * @param array $context
     *   Associative array of variables to pass
     */
    public function render(string $template, array $context = []) {
        echo $this->twig->render($template, $context);
    }

    /**
     * Redirect the user to the login page if they are not logged in.
     *
     * @return void
     */
    public function isUserNotLoggedIn() {
        if ($this->username === NULL)  {
            header("Location: /login");
        }
    }

}
