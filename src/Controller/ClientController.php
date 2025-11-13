<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Client;
use App\Entity\ClientType;
use App\Entity\Contact;
use App\Entity\ContactType;
use App\Entity\Country;
use App\Entity\Street;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ClientController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ClientController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    protected string $page;
    protected string $page_title;
    protected array $countries;
    protected array $cities;
    protected array $streets;
    protected string $app_version;
    protected string $stylesheet;

    /**
     * ClientController constructor.
     *
     * Initializes controller properties, loads app version from composer.json, and sets stylesheet path.
     *
     * @param EntityManagerInterface $entityManager
     *   Doctrine entity manager for database operations.
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->page = 'clients';
        $this->page_title = 'Klijenti';
        $this->countries = $this->entityManager->getRepository(Country::class)->findBy([], ['name' => 'ASC']);
        $this->cities = $this->entityManager->getRepository(City::class)->findBy([], ['name' => 'ASC']);
        $this->streets = $this->entityManager->getRepository(Street::class)->findBy([], ['name' => 'ASC']);
        $this->app_version = $this->loadAppVersion();
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Displays the clients index page with a list of the most recently added clients.
     *
     * Requires the user to be authenticated (session username set).
     * Passes page metadata, user info, and last clients to the template.
     *
     * @return Response
     *   The rendered clients index view.
     */
    #[Route('/clients', name: 'clients_index', methods: ['GET'])]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'tools_menu' => [
                'client' => FALSE,
            ],
            'last_clients' => $this->entityManager->getRepository(Client::class)->getLastClients(10),
            'app_version' => $this->app_version,
            'stylesheet' => $this->stylesheet,
            'user_id' => $_SESSION['user_id'],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('client/index.html.twig', $data);
    }

    /**
     * Displays the form for creating a new client.
     *
     * Requires the user to be authenticated (session username set).
     * Passes client types, countries, cities, streets, and user/page metadata to the template.
     *
     * @return Response
     *   The rendered new client form view.
     */
    #[Route('/clients/new', name: 'client_new', methods: ['GET'])]
    public function new(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'tools_menu' => [
                'client' => FALSE,
            ],
            'client_types' => $this->entityManager->getRepository(ClientType::class)->findAll(),
            'countries' => $this->countries,
            'cities' => $this->cities,
            'streets' => $this->streets,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('client/client_new.html.twig', $data);
    }

    /**
     * Displays the details for a specific client.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves client data and related contact types, and passes them along with user and page metadata to the
     * template.
     *
     * @param int $client_id
     *   The unique identifier of the client to display.
     *
     * @return Response
     *   The rendered client detail view.
     */
    #[Route('/clients/{client_id}', name: 'client_show', requirements: ['client_id' => '\d+'], methods: ['GET'])]
    public function show(int $client_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client' => $client,
            'tools_menu' => [
                'client' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
            'contact_types' => $this->entityManager->getRepository(ContactType::class)->findAll(),
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('client/client_view.html.twig', $data);
    }

    /**
     * Displays the edit form for a specific client.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves client data, client types, countries, cities, streets, and contact types, and passes them along with
     * user and page metadata to the template for editing.
     *
     * @param int $client_id
     *   The unique identifier of the client to edit.
     *
     * @return Response
     *   The rendered client edit view.
     */
    #[Route('/clients/{client_id}/edit', name: 'client_edit', methods: ['GET'])]
    public function edit(int $client_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client' => $client,
            'client_types' => $this->entityManager->getRepository(ClientType::class)->findAll(),
            'countries' => $this->countries,
            'cities' => $this->cities,
            'streets' => $this->streets,
            'tools_menu' => [
                'client' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
            'contact_types' => $this->entityManager->getRepository(ContactType::class)->findAll(),
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];
        return $this->render('client/client_edit.html.twig', $data);
    }

    /**
     * Handles the update of a client's information from the edit form submission.
     *
     * Requires the user to be authenticated (session username set).
     * Validates and updates client fields, sets modification metadata, and persists changes to the database. Redirects
     * to the client detail view after successful update.
     *
     * @param int $client_id
     *   The unique identifier of the client to update.
     *
     * @return Response
     *   Redirects to the client detail view after update.
     */
    #[Route('/clients/{client_id}/edit', name: 'client_update', methods: ['POST'])]
    public function update(int $client_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        $type_id = $_POST["type_id"];
        $type = $this->entityManager->find(ClientType::class, $type_id);

        if (empty($_POST['name'])) {
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

        return $this->redirectToRoute('client_show', ['client_id' => $client_id]);
    }

    /**
     * Handles the creation of a new client from the new client form submission.
     *
     * Requires the user to be authenticated (session username set).
     * Validates and sets client fields, checks for duplicate names, persists the new client to the database, and
     * redirects to the client detail view after successful creation.
     *
     * @return Response
     *   Redirects to the client detail view after creation.
     */
    #[Route('/clients/create', name: 'client_create', methods: ['POST'])]
    public function create(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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

        // Check if name already exist in database.
        $control_name = $this->entityManager->getRepository(Client::class)->findBy( array('name' => $name) );
        if ($control_name) {
            echo "Username already exist in database. Please choose new username!";
            echo '<br><a href="/clients/new">Povratak na stranicu za kreiranje novog klijenta</a>';
            exit(1);
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
        return $this->redirectToRoute('client_show', ['client_id' => $new_client_id]);
    }

    /**
     * Handles the creation of a new contact for a specific client from the contact form submission.
     *
     * Requires the user to be authenticated (session username set).
     * Validates and sets contact fields, persists the new contact to the database, associates it with the client, and
     * redirects to the client detail view after successful creation.
     *
     * @param int $client_id
     *   The unique identifier of the client to associate the new contact with.
     *
     * @return Response
     *   Redirects to the client detail view after contact creation.
     */
    #[Route('/clients/{client_id}/contacts/create', name: 'client_contact_create', methods: ['POST'])]
    public function createContact(int $client_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);
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
        $newContact->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newContact);
        $this->entityManager->flush();

        // Add $newContact to table v6_client_contacts.
        $client->getContacts()->add($newContact);

        $this->entityManager->flush();

        return $this->redirectToRoute('client_show', ['client_id' => $client_id]);
    }

  /**
   * Handles the update of an existing contact for a specific client from the contact edit form submission.
   *
   * Requires the user to be authenticated (session username set).
   * Validates and updates contact fields, sets modification metadata, persists changes to the database, and redirects
   * to the client detail view after successful update.
   *
   * @param int $client_id
   *   The unique identifier of the client associated with the contact.
   * @param int $contact_id
   *   The unique identifier of the contact to update.
   *
   * @return Response
   *   Redirects to the client detail view after contact update.
   */
    #[Route('/clients/{client_id}/contacts/{contact_id}/update', name: 'client_contact_edit', methods: ['POST'])]
    public function updateContact(int $client_id, int $contact_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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

        return $this->redirectToRoute('client_show', ['client_id' => $client_id]);
    }

    /**
     * Handles the deletion of a contact from a specific client.
     *
     * Requires the user to be authenticated (session username set).
     * Removes the contact from the client's contact collection and deletes it from the database.
     * Redirects to the client detail view after successful deletion.
     *
     * @param int $client_id
     *   The unique identifier of the client associated with the contact.
     * @param int $contact_id
     *   The unique identifier of the contact to delete.
     *
     * @return Response
     *   Redirects to the client detail view after contact deletion.
     */
    #[Route('/clients/{client_id}/contacts/{contact_id}/delete', name: 'client_contact_remove', methods: ['GET'])]
    public function deleteContact(int $client_id, int $contact_id): Response
    {
        $client = $this->entityManager->find(Client::class, $client_id);
        $contact = $this->entityManager->find(Contact::class, $contact_id);

        // Remove $contact from table v6_client_contacts.
        $client->getContacts()->removeElement($contact);

        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        return $this->redirectToRoute('client_show', ['client_id' => $client_id]);
    }

    /**
     * Displays the form for creating a new country.
     *
     * Requires the user to be authenticated (session username set).
     * Passes user and page metadata to the template for rendering the new country form.
     *
     * @return Response
     *   The rendered new country form view.
     */
    #[Route('/countries/new', name: 'country_new', methods: ['GET'])]
    public function newCountry(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'client' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('client/country_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new country from the new country form submission.
     *
     * Requires the user to be authenticated (session username set).
     * Validates and sets country fields, checks for duplicate names, persists the new country to the database,
     * and redirects to the clients index view after successful creation.
     *
     * @return Response
     *   Redirects to the clients index view after country creation.
     */
    #[Route('/countries/create', name: 'country_create', methods: ['POST'])]
    public function createCountry(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_country = $this->entityManager->getRepository(Country::class)->findBy( array('name' => $name) );
        if ($control_country) {
            echo 'Country with name: "<strong>'.$name.'</strong>" already exist in database. Please choose new name!';
            echo '<br><a href="/countries/new">Povratak na stranicu za kreiranje nove države</a>';
            exit(1);
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
        $newCountry->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newCountry);
        $this->entityManager->flush();

        return $this->redirectToRoute('clients_index');
    }

    /**
     * Displays the form for creating a new city.
     *
     * Requires the user to be authenticated (session username set).
     * Passes user and page metadata to the template for rendering the new city form.
     *
     * @return Response
     *   The rendered new city form view.
     */
    #[Route('/cities/new', name: 'city_new', methods: ['GET'])]
    public function newCity(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'client' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('client/city_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new city from the new city form submission.
     *
     * Requires the user to be authenticated (session username set).
     * Validates and sets city fields, checks for duplicate names, persists the new city to the database, and redirects
     * to the clients index view after successful creation.
     *
     * @return Response
     *   Redirects to the clients index view after city creation.
     */
    #[Route('/cities/create', name: 'city_create', methods: ['POST'])]
    public function createCity(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class,  $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_name = $this->entityManager->getRepository(City::class)->findBy( array('name' => $name) );
        if ($control_name) {
            echo 'Naselje sa nazivom: "<strong>'.$name.'</strong>", već postoji u bazi!';
            echo '<br><a href="/cities/new">Povratak na stranicu za kreiranje novog grada</a>';
            exit(1);
            // die('<script>location.href = "?alert&ob=2" </script>');
        }

        $newCity = new City();

        $newCity->setName($name);
        $newCity->setCreatedAt(new \DateTime("now"));
        $newCity->setCreatedByUser($user);
        $newCity->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newCity);
        $this->entityManager->flush();

        return $this->redirectToRoute('clients_index');
    }

    /**
     * Add Street form.
     *
     * @return Response
     */
    #[Route('/streets/new', name: 'street_new', methods: ['GET'])]
    public function newStreet(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
          return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'client' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('client/street_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new street from the new street form submission.
     *
     * Requires the user to be authenticated (session username set).
     * Validates and sets street fields, checks for duplicate names, persists the new street to the database, and
     * redirects to the clients index view after successful creation.
     *
     * @return Response
     *   Redirects to the clients index view after street creation.
     */
    #[Route('/streets/create', name: 'street_create', methods: ['POST'])]
    public function createStreet(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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
            echo 'Ulica sa nazivom: "<strong>'.$name.'</strong>", već postoji u bazi. Unesite novi naziv!';
            echo '<br><a href="/streets/new">Povratak na stranicu za kreiranje nove ulice</a>';
            exit(1);
        }

        $newStreet = new Street();

        $newStreet->setName($name);
        $newStreet->setCreatedAt(new \DateTime("now"));
        $newStreet->setCreatedByUser($user);
        $newStreet->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newStreet);
        $this->entityManager->flush();

        return $this->redirectToRoute('clients_index');
    }

    /**
     * Displays and processes the advanced search form for clients.
     *
     * Requires the user to be authenticated (session username set).
     * Accepts client, street, and city search terms from POST data, performs advanced search using the Client
     * repository, and passes the results and search terms along with page and user metadata to the template.
     *
     * @return Response
     *   The rendered advanced search view with results if a search was performed.
     */
    #[Route('/clients/advanced-search', name: 'client_advanced_search', methods: ['GET', 'POST'])]
    public function advancedSearch(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client_name = $street_name = $city_name = NULL;

        if (isset($_POST['submit'])) {
            $term = $_POST["client"];
            if ($term) {
                $client_name = $this->basicValidation($term);
            }

            $street = $_POST["street"];
            if ($street) {
                $street_name = $this->basicValidation($street);
            }

            $city = $_POST["city"];
            if ($city) {
                $city_name = $this->basicValidation($city);
            }
            $clients_data = $this->entityManager->getRepository(Client::class)->advancedSearch($term, $street, $city);
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'clients' => $clients_data ?? NULL,
            'client_name' => $client_name,
            'street_name' => $street_name,
            'city_name' => $city_name,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'client' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('client/advanced_search.html.twig', $data);
    }

    /**
     * Basic validation method.
     *
     * @param $str
     *
     * @return string
     */
    public function basicValidation($str): string
    {
        return trim(htmlspecialchars($str));
    }

    /**
     * Searches for clients by a search term provided as a query parameter.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves the 'term' from the query string, performs the search using the Client repository, and passes the
     * results along with page metadata to the template.
     *
     * @param Request $request
     *   The HTTP request object containing the search term as a query parameter.
     *
     * @return Response
     *   The rendered client search results view.
     */
    #[Route('/clients/search', name: 'client_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term', '');
        $clients= $this->entityManager->getRepository(Client::class)->search($term);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'clients' => $clients,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'client' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('client/search.html.twig', $data);
    }

    /**
     * Loads the application version from composer.json.
     *
     * @return string
     *   The app version, or 'unknown' if not found.
     */
    private function loadAppVersion(): string
    {
        $composerJsonPath = __DIR__ . '/../../composer.json';
        if (file_exists($composerJsonPath)) {
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            return $composerData['version'] ?? 'unknown';
        }
        return 'unknown';
    }
}
