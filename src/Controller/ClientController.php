<?php

namespace App\Controller;

use App\Core\EntityManagerFactory;

/**
 * ClientController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ClientController {

  private $user_id;
  private $username;
  private $user_role_id;
  private $entityManager;

  /**
   * ClientController constructor.
   */
  public function __construct() {
    $this->user_id = $_SESSION['user_id'];
    $this->username = $_SESSION['username'];
    $this->user_role_id = $_SESSION['user_role_id'];
    $this->entityManager = EntityManagerFactory::getEntityManager();
  }

  /**
   * @return void
   */
  public function index($search = NULL) {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'clients',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
      'search' => $search,
    ];

    $this->render('index', $data);
  }

  /**
   * @param $client_id
   *
   * @return void
   */
  public function view($client_id, $contact_id = NULL, $search = NULL) {
    $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
      'search' => $search,
      'client_id' => $client_id,
      'client' => $client,
      'contact_id' => $contact_id,
    ];

    $this->render('view', $data);
  }

  /**
   * @param $client_id
   *
   * @return void
   */
  public function edit($client_id) {
    $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($client_id);

    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '/../libraries/',
      'client_id' => $client_id,
      'client' => $client,
    ];

    $this->render('edit', $data);
  }

  /**
   * @return void
   */
  public function add() {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
    ];
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
    $this->render('view', $data);
  }

  /**
   * @return void
   */
  public function addCountry() {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
    ];
    $this->render('addCountry', $data);
  }

  /**
   * @return void
   */
  public function addCity() {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
    ];
    $this->render('addCity', $data);
  }

  /**
   * @return void
   */
  public function addStreet() {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
    ];
    $this->render('addStreet', $data);
  }

  /**
   * @return void
   */
  public function advancedSearch() {
    $data = [
      'user_id' => $this->user_id,
      'username' => $this->username,
      'user_role_id' => $this->user_role_id,
      'page' => 'client',
      'entityManager' => $this->entityManager,
      'stylesheet' => '../libraries/',
    ];
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
    // Extract data array to variables
    extract($data);
    // Include the view file
    require_once __DIR__ . "/../Views/client/$view.php";
  }

}
