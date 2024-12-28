<?php

namespace App\Core;

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

  /**
   * BaseController constructor.
   */
  public function __construct() {
    if (isset($_SESSION['user_id'])) $this->user_id = $_SESSION['user_id'];
    if (isset($_SESSION['username'])) $this->username = $_SESSION['username'];
    if (isset($_SESSION['user_role_id'])) $this->user_role_id = $_SESSION['user_role_id'];
    $this->entityManager = EntityManagerFactory::getEntityManager();
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
