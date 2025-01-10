<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Contact;
use App\Entity\Country;
use App\Entity\Street;

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
  public function editClientForm($client_id) {
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
   * Edit Client.
   *
   * @param $client_id
   *
   * @return void
   */
  public function editClient($client_id): void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    $type_id = $_POST["type_id"];
    $type = $this->entityManager->find("\App\Entity\ClientType", $type_id);

    if (empty($_POST['name'])) {
      $nameError = 'Ime mora biti upisano';
      die('<script>location.href = "?new&name_error" </script>');
    }
    else {
      $name = $this->basicValidation($_POST['name']);
    }

    $name_note = $this->basicValidation($_POST["name_note"]);

    $lb = "";
    if (isset($_POST["lb"])) {
      $lb = $_POST["lb"];
    }

    $is_supplier = 0;
    if (isset($_POST["is_supplier"])) {
      $is_supplier = $_POST["is_supplier"];
    }

    $country_id = $_POST["country_id"];
    $country = $this->entityManager->find("\App\Entity\Country", $country_id);
    $city_id = $_POST["city_id"];
    $city = $this->entityManager->find("\App\Entity\City", $city_id);
    $street_id = $_POST["street_id"];
    $street = $this->entityManager->find("\App\Entity\Street", $street_id);
    $home_number = $this->basicValidation($_POST["home_number"]);
    $address_note = $this->basicValidation($_POST["address_note"]);
    $note = $this->basicValidation($_POST["note"]);

    $client = $this->entityManager->find('\App\Entity\Client', $client_id);

    if ($client === null) {
      echo "Client with ID $client_id does not exist.\n";
      exit(1);
    }

    $client->setType($type);
    $client->setName($name);
    $client->setNameNote($name_note);
    $client->setLb($lb);
    $client->setIsSupplier($is_supplier);
    $client->setCountry($country);
    $client->setCity($city);
    $client->setStreet($street);
    $client->setHomeNumber($home_number);
    $client->setAddressNote($address_note);
    $client->setNote($note);
    $client->setModifiedByUser($user);
    $client->setModifiedAt(new \DateTime("now"));

    $this->entityManager->flush();

    die('<script>location.href = "/client/'.$client_id.'" </script>');
  }

  /**
   * @return void
   */
  public function addClientForm() {
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

    $this->render('addClientForm', $data);
  }

  /**
   * Add client.
   *
   * @return void
   */
  public function addClient(): void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    $type_id = $_POST["type_id"];
    $type = $this->entityManager->find("\App\Entity\ClientType", $type_id);

    if (empty($_POST['name'])) {
      $nameError = 'Ime mora biti upisano';
      die('<script>location.href = "?new&name_error" </script>');
    }
    else {
      $name = $this->basicValidation($_POST['name']);
    }

    $name_note = $this->basicValidation($_POST["name_note"]);

    $lb = $_POST["lb"] ?? '';
    $is_supplier = $_POST["is_supplier"] ?? 0;

    $country_id = $_POST["country_id"];
    $country = $this->entityManager->find("\App\Entity\Country", $country_id);
    $city_id = $_POST["city_id"];
    $city = $this->entityManager->find("\App\Entity\City", $city_id);
    $street_id = $_POST["street_id"];
    $street = $this->entityManager->find("\App\Entity\Street", $street_id);
    $home_number = $this->basicValidation($_POST["home_number"]);
    $address_note = $this->basicValidation($_POST["address_note"]);
    $note = $this->basicValidation($_POST["note"]);

    // check if name already exist in database
    $control_name = $this->entityManager->getRepository('\App\Entity\Client')->findBy( array('name' => $name) );
    if ($control_name) {
      echo "Username already exist in database. Please choose new username!";
      exit(1);
      // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $newClient = new Client();

    $newClient->setType($type);
    $newClient->setName($name);
    $newClient->setNameNote($name_note);
    $newClient->setLb($lb);
    $newClient->setIsSupplier($is_supplier);
    $newClient->setCountry($country);
    $newClient->setCity($city);
    $newClient->setStreet($street);
    $newClient->setHomeNumber($home_number);
    $newClient->setAddressNote($address_note);
    $newClient->setNote($note);
    $newClient->setCreatedAt(new \DateTime("now"));
    $newClient->setCreatedByUser($user);
    $newClient->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

    $this->entityManager->persist($newClient);
    $this->entityManager->flush();

    // Get last id and redirect.
    $new_client_id = $newClient->getId();
    die('<script>location.href = "/client/'.$new_client_id.'" </script>');
  }

  /**
   * Edit contact.
   *
   * @param $client_id
   * @param $contact_id
   *
   * @return void
   */
  public function editContact($client_id, $contact_id): void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    $contact_type_id = $_POST["contact_type_id"];
    $contact_type = $this->entityManager->find("\App\Entity\ContactType", $contact_type_id);
    $body = $this->basicValidation($_POST["body"]);
    $note = $this->basicValidation($_POST["note"]);

    $contact = $this->entityManager->find('\App\Entity\Contact', $contact_id);

    if ($contact === null) {
      echo "Contact with ID $contact_id does not exist.\n";
      exit(1);
    }

    $contact->setType($contact_type);
    $contact->setBody($body);
    $contact->setNote($note);
    $contact->setModifiedByUser($user);
    $contact->setModifiedAt(new \DateTime("now"));

    $this->entityManager->flush();

    die('<script>location.href = "/client/'.$client_id.'" </script>');
  }

  /**
   * Add contact to client.
   *
   * @return void
   */
  public function addContact($client_id): void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);
    $client = $this->entityManager->find("\App\Entity\Client", $client_id);

    $type_id = $_POST["contact_type_id"];
    $type = $this->entityManager->find("\App\Entity\ClientType", $type_id);

    $contact_type_id = $_POST["contact_type_id"];
    $contact_type = $this->entityManager->find("\App\Entity\ContactType", $contact_type_id);
    $body = $this->basicValidation($_POST["body"]);
    $note = $this->basicValidation($_POST["note"]);

    $newContact = new Contact();

    $newContact->setType($contact_type);
    $newContact->setBody($body);
    $newContact->setNote($note);
    $newContact->setCreatedAt(new \DateTime("now"));
    $newContact->setCreatedByUser($user);
    $newContact->setModifiedAt(new \DateTime("0000-01-01 00:00:00"));

    $this->entityManager->persist($newContact);
    $this->entityManager->flush();

    // Add $newContact to table v6_client_contacts.
    $client->getContacts()->add($newContact);

    $this->entityManager->flush();

    die('<script>location.href = "/client/'.$client_id.'" </script>');
  }

  /**
   * Remove contact from client.
   *
   * @param $client_id
   * @param $contact_id
   *
   * @return void
   */
  public function removeContact($client_id, $contact_id): void {
    $client = $this->entityManager->find("\App\Entity\Client", $client_id);
    $contact = $this->entityManager->find("\App\Entity\Contact", $contact_id);

    // Remove $contact from table v6_client_contacts.
    $client->getContacts()->removeElement($contact);

    $this->entityManager->remove($contact);
    $this->entityManager->flush();

    die('<script>location.href = "/client/'.$client_id.'" </script>');
  }

  /**
   * Add country form.
   *
   * @return void
   */
  public function addCountryForm(): void {
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
   * Add new country.
   *
   * @return void
   */
  public function addCountry(): void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    if (empty($_POST['name'])) {
      $nameError = 'Ime mora biti upisano';
      die('<script>location.href = "?new&name_error" </script>');
    }
    else {
      $name = $this->basicValidation($_POST['name']);
    }

    // Check if name already exist in database.
    $control_country = $this->entityManager->getRepository('\App\Entity\Country')->findBy( array('name' => $name) );
    if ($control_country) {
      echo 'Country with name: "<strong>'.$name.'</strong>" already exist in database. Please choose new name!';
      exit(1);
      // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $abbr = "";
    if (!empty($_POST['abbr'])) {
      $abbr = $this->basicValidation($_POST['abbr']);
    }

    $newCountry = new Country();

    $newCountry->setName($name);
    $newCountry->setAbbr($abbr);
    $newCountry->setCreatedAt(new \DateTime("now"));
    $newCountry->setCreatedByUser($user);
    $newCountry->setModifiedAt(new \DateTime("0000-01-01 00:00:00"));

    $this->entityManager->persist($newCountry);
    $this->entityManager->flush();

    die('<script>location.href = "/clients/" </script>');
  }

  /**
   * Add city form.
   *
   * @return void
   */
  public function addCityForm(): void {
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
   * Add new city.
   *
   * @return void
   */
  public function addCity(): void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    if (empty($_POST['name'])) {
      $nameError = 'Ime mora biti upisano';
      die('<script>location.href = "?new&name_error" </script>');
    }
    else {
      $name = $this->basicValidation($_POST['name']);
    }

    // Check if name already exist in database.
    $control_name = $this->entityManager->getRepository('\App\Entity\City')->findBy( array('name' => $name) );
    if ($control_name) {
      echo 'City wit name: "<strong>'.$name.'</strong>" already exist in database!';
      exit(1);
      // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $newCity = new City();

    $newCity->setName($name);
    $newCity->setCreatedAt(new \DateTime("now"));
    $newCity->setCreatedByUser($user);
    $newCity->setModifiedAt(new \DateTime("0001-01-01 00:00:00"));

    $this->entityManager->persist($newCity);
    $this->entityManager->flush();

    die('<script>location.href = "/clients/" </script>');
  }

  /**
   * Add Street form.
   *
   * @return void
   */
  public function addStreetForm(): void {
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
   * Add new street.
   *
   * @return void
   */
  public function addStreet():void {
    $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

    if (empty($_POST['name'])) {
      $nameError = 'Ime mora biti upisano';
      die('<script>location.href = "?new&name_error" </script>');
    }
    else {
      $name = $this->basicValidation($_POST['name']);
    }

    // Check if name already exist in database.
    $control_name = $this->entityManager->getRepository('\App\Entity\Street')->findBy( array('name' => $name) );
    if ($control_name) {
      echo 'Street wit name: "<strong>'.$name.'</strong>" already exist in database. Please choose new name!';
      exit(1);
      // die('<script>location.href = "?alert&ob=2" </script>');
    }

    $newStreet = new Street();

    $newStreet->setName($name);
    $newStreet->setCreatedAt(new \DateTime("now"));
    $newStreet->setCreatedByUser($user);
    $newStreet->setModifiedAt(new \DateTime("0000-01-01 00:00:00"));

    $this->entityManager->persist($newStreet);
    $this->entityManager->flush();

    die('<script>location.href = "/clients/" </script>');
  }

  /**
   * Advanced search.
   *
   * @return void
   */
  public function advancedSearch(): void {
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
   * Basic validation method.
   *
   * @param $str
   *
   * @return string
   */
  public function basicValidation($str): string {
    return trim(htmlspecialchars($str));
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
