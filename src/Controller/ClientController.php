<?php

namespace App\Controller;

use App\Core\BaseController;

/**
 * ClientController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ClientController extends BaseController {

  /**
   * ClientController constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Index action.
   *
   * @return void
   */
  public function index($search = NULL) {
    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'clients',
      'entityManager' => $this->entityManager,
      'search' => $search,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('index', $data);
  }

  /**
   * View Client form.
   *
   * @param $client_id
   *
   * @return void
   */
  public function view($client_id, $contact_id = NULL, $search = NULL) {
    $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'search' => $search,
      'client_id' => $client_id,
      'client' => $client,
      'contact_id' => $contact_id,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('view', $data);
  }

  /**
   * Edit Client form.
   *
   * @param $client_id
   *
   * @return void
   */
  public function edit($client_id) {
    $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '/../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'client_id' => $client_id,
      'client' => $client,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('edit', $data);
  }

  /**
   * @return void
   */
  public function add() {
    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('add', $data);
  }

  /**
   * @return void
   */
  public function addContact($client_id) {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('view', $data);
  }

  /**
   * @return void
   */
  public function addCountry() {
    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('addCountry', $data);
  }

  /**
   * @return void
   */
  public function addCity() {
    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('addCity', $data);
  }

  /**
   * @return void
   */
  public function addStreet() {
    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('addStreet', $data);
  }

  /**
   * @return void
   */
  public function advancedSearch() {
    $data = [
      'page_title' => 'Klijenti',
      'stylesheet' => '../libraries/',
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
    ];

    // If the user is not logged in, redirect them to the login page.
    $this->isUserNotLoggedIn();

    $this->render('advancedSearch', $data);
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
    require_once __DIR__ . "/../Views/client/$view.php";
  }

}
