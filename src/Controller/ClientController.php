<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Contact;
use App\Entity\ContactType;
use App\Entity\Country;
use App\Entity\Street;
use App\Entity\User;

/**
 * ClientController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ClientController extends BaseController
{

    protected string $page_title;
    protected string $page;
    protected $countries;
    protected $cities;
    protected $streets;

    /**
     * ClientController constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->page_title = 'Klijenti';
        $this->page = 'clients';
        $this->countries = $this->entityManager->getRepository(Country::class)->findBy([], ['name' => 'ASC']);
        $this->cities = $this->entityManager->getRepository(City::class)->findBy([], ['name' => 'ASC']);
        $this->streets = $this->entityManager->getRepository(Street::class)->findBy([], ['name' => 'ASC']);
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function index($search = NULL): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'search' => $search,
            'tools_menu' => [
                'client' => FALSE,
            ],
            'last_clients' => $this->entityManager->getRepository(Client::class)->getLastClients(10),
        ];

        $this->render('client/index.html.twig', $data);
    }

    /**
     * View Client form.
     *
     * @param $client_id
     *
     * @return void
     */
    public function view($client_id, $contact_id = NULL, $search = NULL) {
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'search' => $search,
            'client' => $client,
            'tools_menu' => [
                'client' => TRUE,
                'view' => TRUE,
            ],
            'contact_types' => $this->entityManager->getRepository(ContactType::class)->findAll(),
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('client/view.html.twig', $data);
    }

    /**
     * Edit Client form.
     *
     * @param $client_id
     *
     * @return void
     */
    public function editClientForm($client_id) {
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'client' => $client,
            'client_types' => $this->entityManager->getRepository(ClientType::class)->findAll(),
            'countries' => $this->countries,
            'cities' => $this->cities,
            'streets' => $this->streets,
            'tools_menu' => [
              'client' => TRUE,
              'edit' => TRUE,
            ],
            'contact_types' => $this->entityManager->getRepository(ContactType::class)->findAll(),
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('client/edit.html.twig', $data);
    }

    /**
     * Edit Client.
     *
     * @param $client_id
     *
     * @return void
     */
    public function editClient($client_id): void {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $type_id = $_POST["type_id"];
        $type = $this->entityManager->find(ClientType::class, $type_id);

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
        $country = $this->entityManager->find(Country::class, $country_id);
        $city_id = $_POST["city_id"];
        $city = $this->entityManager->find(City::class, $city_id);
        $street_id = $_POST["street_id"];
        $street = $this->entityManager->find(Street::class, $street_id);
        $home_number = $this->basicValidation($_POST["home_number"]);
        $address_note = $this->basicValidation($_POST["address_note"]);
        $note = $this->basicValidation($_POST["note"]);

        $client = $this->entityManager->find(Client::class, $client_id);

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
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'client_types' => $this->entityManager->getRepository(ClientType::class)->findAll(),
            'countries' => $this->countries,
            'cities' => $this->cities,
            'streets' => $this->streets,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('client/add.html.twig', $data);
    }

    /**
     * Add client.
     *
     * @return void
     */
    public function addClient(): void {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $type_id = $_POST["type_id"];
        $type = $this->entityManager->find(ClientType::class, $type_id);

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
        $country = $this->entityManager->find(Country::class, $country_id);
        $city_id = $_POST["city_id"];
        $city = $this->entityManager->find(City::class, $city_id);
        $street_id = $_POST["street_id"];
        $street = $this->entityManager->find(Street::class, $street_id);
        $home_number = $this->basicValidation($_POST["home_number"]);
        $address_note = $this->basicValidation($_POST["address_note"]);
        $note = $this->basicValidation($_POST["note"]);

        // check if name already exist in database
        $control_name = $this->entityManager->getRepository(Client::class)->findBy( array('name' => $name) );
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
        $user = $this->entityManager->find(User::class, $this->user_id);

        $contact_type_id = $_POST["contact_type_id"];
        $contact_type = $this->entityManager->find(ContactType::class, $contact_type_id);
        $body = $this->basicValidation($_POST["body"]);
        $note = $this->basicValidation($_POST["note"]);

        $contact = $this->entityManager->find(Contact::class, $contact_id);

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
        $user = $this->entityManager->find(User::class, $this->user_id);
        $client = $this->entityManager->find(Client::class, $client_id);

        $type_id = $_POST["contact_type_id"];
        $type = $this->entityManager->find(ClientType::class, $type_id);

        $contact_type_id = $_POST["contact_type_id"];
        $contact_type = $this->entityManager->find(ContactType::class, $contact_type_id);
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
        $client = $this->entityManager->find(Client::class, $client_id);
        $contact = $this->entityManager->find(Contact::class, $contact_id);

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
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('client/add_country.html.twig', $data);
    }

    /**
     * Add new country.
     *
     * @return void
     */
    public function addCountry(): void {
        $user = $this->entityManager->find(User::class, $this->user_id);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_country = $this->entityManager->getRepository(Country::class)->findBy( array('name' => $name) );
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
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

      $this->render('client/add_city.html.twig', $data);
    }

    /**
     * Add new city.
     *
     * @return void
     */
    public function addCity(): void {
        $user = $this->entityManager->find(User::class, $this->user_id);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_name = $this->entityManager->getRepository(City::class)->findBy( array('name' => $name) );
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
    public function addStreetForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
        ];

        $this->render('client/add_street.html.twig', $data);
    }

    /**
     * Add new street.
     *
     * @return void
     */
    public function addStreet():void {
        $user = $this->entityManager->find(User::class, $this->user_id);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_name = $this->entityManager->getRepository(Street::class)->findBy( array('name' => $name) );
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
        if (isset($_POST['submit'])) {
            $term = $_POST["client"];
            $street = $_POST["street"];
            $city = $_POST["city"];
            $clients_data = $this->entityManager->getRepository(Client::class)->advancedSearch($term, $street, $city);
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'user_id' => $this->user_id,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'clients' => $clients_data ?? NULL,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('client/advanced_search.html.twig', $data);
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
   * Search for clients.
   *
   * @param string $term
   *   Search term.
   *
   * @return void
   */
    public function search(string $term): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $clients= $this->entityManager->getRepository(Client::class)->search($term);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'clients' => $clients,
        ];

        $this->render('client/search.html.twig', $data);
    }
}
