<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\EntityManagerFactory;

/**
 * HomeController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class HomeController extends BaseController {

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
  public function index() {
    $data = [
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'home',
      'entityManager' => $this->entityManager,
      'stylesheet' => 'libraries/',
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('home', $data);
  }

  /**
   * Login method.
   *
   * @return void
   *   The rendered view.
   */
  public function login() {
    $this->render('formLogin');
  }

  /**
   * Login the user.
   *
   * @return void
   */
  public function loginPost() {

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
      // redirekcija ako nije dobar user
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
  public function logout() {
    session_start();
    unset($_SESSION['username']);
    unset($_SESSION['user_role_id']);
    header("Location: /");
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
    // Extract data array to variables.
    extract($data);

    // Include the view file.
    require_once __DIR__ . "/../Views/$view.php";
  }

  /**
   * Redirect the user to the login page if they are not logged in.
   *
   * @return void
   */
  private function isUserNotLoggedIn() {
    if ($this->username === NULL)  {
      header("Location: /login");
    }
  }

}
