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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    protected $countries;
    protected $cities;
    protected $streets;
    protected string $app_version;
    protected string $stylesheet;


    /**
     * ClientController constructor.
     *
     * Initializes controller properties, loads app version from composer.json,
     * and sets stylesheet path.
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
     * View Client form.
     *
     * @param $client_id
     *
     * @return void
     */
    public function clientView($client_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();
        $client = $this->entityManager->getRepository(Client::class)->getClientData($client_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client' => $client,
            'tools_menu' => [
                'client' => TRUE,
                'view' => TRUE,
            ],
            'contact_types' => $this->entityManager->getRepository(ContactType::class)->findAll(),
        ];

        $this->render('client/client_view.html.twig', $data);
    }

    /**
     * Edit Client form.
     *
     * @param $client_id
     *
     * @return void
     */
    public function clientEditForm($client_id):void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
              'edit' => TRUE,
            ],
            'contact_types' => $this->entityManager->getRepository(ContactType::class)->findAll(),
        ];

        $this->render('client/client_edit.html.twig', $data);
    }

    /**
     * Edit Client.
     *
     * @param $client_id
     *
     * @return void
     */
    public function clientEdit($client_id): void
    {
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
    public function clientNewForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client_types' => $this->entityManager->getRepository(ClientType::class)->findAll(),
            'countries' => $this->countries,
            'cities' => $this->cities,
            'streets' => $this->streets,
        ];

        $this->render('client/client_new.html.twig', $data);
    }

    /**
     * Add client.
     *
     * @return void
     */
    public function clientAdd(): void
    {
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
     * @param int $client_id
     * @param int $contact_id
     *
     * @return void
     */
    public function editContact(int $client_id, int $contact_id): void
    {
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
     * @param int $client_id
     *
     * @return void
     */
    public function addContact(int $client_id): void
    {
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
     * @param int $client_id
     * @param int $contact_id
     *
     * @return void
     */
    public function removeContact(int $client_id, int $contact_id): void
    {
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
    public function countryNewForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
        ];

        $this->render('client/country_new.html.twig', $data);
    }

    /**
     * Add new country.
     *
     * @return void
     */
    public function countryAdd(): void
    {
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
    public function cityNewForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
        ];

        $this->render('client/city_new.html.twig', $data);
    }

    /**
     * Add new city.
     *
     * @return void
     */
    public function cityAdd(): void
    {
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
    public function streetNewForm(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
        ];

        $this->render('client/street_new.html.twig', $data);
    }

    /**
     * Add new street.
     *
     * @return void
     */
    public function streetAdd():void
    {
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
    public function advancedSearch(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();
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
        ];

        $this->render('client/advanced_search.html.twig', $data);
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
