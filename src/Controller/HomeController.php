<?php

namespace App\Controller;

use App\Core\EntityManagerFactory;

/**
 * HomeController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class HomeController {

  private $username;
  private $user_role_id;
  private $entityManager;

  /**
   * HomeController constructor.
   */
  public function __construct() {
    $this->username = $_SESSION['username'];
    $this->user_role_id = $_SESSION['user_role_id'];
    $this->entityManager = EntityManagerFactory::getEntityManager();
  }

  public function index() {
    $data = [
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'home',
      'entityManager' => $this->entityManager,
      'stylesheet' => 'libraries/',
    ];

    // Render the view
    $this->render('home', $data);
  }

  /**
   * A helper method to render views.
   *
   * @param $view
   * @param $data
   *
   * @return void
   */
  private function render($view, $data = []) {
    // Extract data array to variables
    extract($data);

    // Include the view file
    require_once __DIR__ . "/../Views/$view.php";
  }

}
