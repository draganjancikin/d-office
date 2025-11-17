<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Client;
use App\Entity\ClientType as ClientEntityType;
use App\Entity\Contact;
use App\Entity\ContactType as ContactEntityType;
use App\Entity\Country;
use App\Entity\Street;
use App\Entity\User;
use App\Form\ClientType;
use App\Form\ContactType;
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

    protected string $page;
    protected string $page_title;
    protected string $stylesheet;

    /**
     * ClientController constructor.
     *
     * Initializes controller properties:
     * - Sets the page identifier and title for client-related views.
     * - Loads the application stylesheet path from environment variables or defaults.
     */
    public function __construct() {
        $this->page = 'clients';
        $this->page_title = 'Klijenti';
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Displays the clients index page with a list of the most recently added clients.
     *
     * Requires the user to be authenticated (session username set).
     * Passes page metadata, user info, and last clients to the template for rendering.
     *
     * @param Request $request
     *   The HTTP request object.
     * @param EntityManagerInterface $em
     *   Doctrine entity manager for database operations.
     *
     * @return Response
     *   The rendered clients index view.
     */
    #[Route('/clients', name: 'clients_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        if ($request->query->has('search')) {
            $term = $request->query->get('search', '');
            $clients= $em->getRepository(Client::class)->search($term);
        } else {
            $clients = $em->getRepository(Client::class)->findAll();
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'tools_menu' => [
                'client' => FALSE,
            ],
            'clients' => $clients,
            'stylesheet' => $this->stylesheet,
            'user_id' => $_SESSION['user_id'],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('client/index.html.twig', $data);
    }

    /**
     * Displays the form for creating a new client and handles its submission.
     *
     * Requires the user to be authenticated (session username set).
     * Renders the Symfony form for client creation, processes form submission, persists the new client,
     * sets the current user as created_by_user, and redirects to the client detail view with a flash message on success.
     *
     * @param Request $request
     *   The HTTP request object.
     * @param EntityManagerInterface $em
     *   Doctrine entity manager for database operations.
     *
     * @return Response
     *   The rendered new client form view or a redirect to the client detail view after creation.
     */
    #[Route('/clients/new', name: 'client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = new Client();
        $user = $em->find(User::class, $_SESSION['user_id']);
        $client->setCreatedByUser($user);

        $form = $this->createForm(ClientType::class, $client); // CORRECT
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            // $this->addFlash('success', 'Client created successfully!');
            return $this->redirectToRoute('client_show', ['client_id' => $client->getId()]);
        }

        return $this->render('client/new.html.twig', [
            'form' => $form->createView(),
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
                'tools_menu' => [
                'client' => FALSE,
            ],
        ]);
    }

    /**
     * Displays the details for a specific client.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves client data and related contact types, and passes them along with user and page metadata to the template.
     *
     * @param int $client_id
     *   The unique identifier of the client to display.
     * @param Request $request
     *    The HTTP request object.
     * @param EntityManagerInterface $em
     *   Doctrine entity manager for database operations.
     *
     * @return Response
     *   The rendered client detail view.
     */
    #[Route('/clients/{client_id}', name: 'client_show', requirements: ['client_id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(int $client_id, Request $request, EntityManagerInterface $em): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = $em->getRepository(Client::class)->getClientData($client_id);
        $client_object = $em->find(Client::class, $client_id);
        $user = $em->find(User::class, $_SESSION['user_id']);

        $contact = new Contact();
        $contact_form = $this->createForm(ContactType::class, $contact);
        $contact_form->handleRequest($request);
        if ($contact_form->isSubmitted() && $contact_form->isValid()) {
            $contact->setModifiedAt(new \DateTime("now"));
            $contact->setCreatedAt(new \DateTime("now"));
            $contact->setCreatedByUser($user);
            $client_object->addContact($contact);

            $em->persist($contact);
            $em->flush();

            // $this->addFlash('success', 'Contact created successfully!');
            return $this->redirectToRoute('client_show', ['client_id' => $client_id]);
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client' => $client,
            'tools_menu' => [
                'client' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
            'contact_types' => $em->getRepository(ContactEntityType::class)->findAll(),
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'contact_form' => $contact_form->createView(),
        ];

        return $this->render('client/view.html.twig', $data);
    }

    /**
     * Displays the edit form for a specific client and handles its submission.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves client data and related entities, processes the Symfony form for editing the client and its contacts.
     * Updates the modifiedAt field for the client and only for contacts that are changed.
     * Sets createdAt for new contacts. Persists all changes and redirects to the client detail view on success.
     *
     * @param int $client_id
     *   The unique identifier of the client to edit.
     * @param Request $request
     *   The HTTP request object.
     * @param EntityManagerInterface $em
     *   Doctrine entity manager for database operations.
     *
     * @return Response
     *   The rendered client edit view or a redirect to the client detail view after editing.
     */
    #[Route('/clients/{client_id}/edit', name: 'client_edit', methods: ['GET', 'POST'])]
    public function edit(int $client_id, Request $request, EntityManagerInterface $em): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = $em->find(Client::class, $client_id);
        $user = $em->find(User::class, $_SESSION['user_id']);
        $form = $this->createForm(ClientType::class, $client);

        $originalContacts = [];
        foreach ($client->getContacts() as $contact) {
            $id = null;
            if (method_exists($contact, 'getId')) {
                try {
                    $id = $contact->getId();
                } catch (\Error $e) {
                    $id = null;
                }
            }
            if ($id) {
                $originalContacts[$id] = clone $contact;
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setModifiedByUser($user);
            $client->setModifiedAt(new \DateTime("now"));

            foreach ($client->getContacts() as $contact) {
                $id = null;
                if (method_exists($contact, 'getId')) {
                    try {
                        $id = $contact->getId();
                    } catch (\Error $e) {
                        $id = null;
                    }
                }
                if (!$id) {
                    if ($contact->getCreatedAt() === null) {
                        $contact->setCreatedAt(new \DateTime());
                    }
                    $contact->setModifiedAt(new \DateTime());
                    continue;
                }
                if (isset($originalContacts[$id])) {
                    $orig = $originalContacts[$id];
                    if (
                        $contact->getType() !== $orig->getType() ||
                        $contact->getBody() !== $orig->getBody() ||
                        $contact->getNote() !== $orig->getNote()
                    ) {
                        $contact->setModifiedAt(new \DateTime());
                    }
                }
            }

            $em->flush();

            // $this->addFlash('success', 'Client updated successfully!');
            return $this->redirectToRoute('client_show', ['client_id' => $client_id]);
        }

        $contact = new Contact();
        $contact_form = $this->createForm(ContactType::class, $contact);
        $contact_form->handleRequest($request);
        if ($contact_form->isSubmitted() && $contact_form->isValid()) {
            $contact->setModifiedAt(new \DateTime("now"));
            $contact->setCreatedAt(new \DateTime("now"));
            $contact->setCreatedByUser($user);
            $client->addContact($contact);

            $em->persist($contact);
            $em->flush();

            // $this->addFlash('success', 'Contact created successfully!');
            return $this->redirectToRoute('client_show', ['client_id' => $client_id]);
        }

        return $this->render('client/edit.html.twig', [
            'form' => $form->createView(),
            'contact_form' => $contact_form->createView(),
            'page' => $this->page,
            'page_title' => $this->page_title,
            'client' => $client,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'client' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
            ],
        ]);
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
     * @param EntityManagerInterface $em
     *   Doctrine entity manager for database operations.
     *
     * @return Response
     *   Redirects to the clients index view after country creation.
     */
    #[Route('/countries/create', name: 'country_create', methods: ['POST'])]
    public function createCountry(EntityManagerInterface $em): Response
    {
        session_start();
        $user = $em->find(User::class, $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_country = $em->getRepository(Country::class)->findBy( array('name' => $name) );
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

        $em->persist($newCountry);
        $em->flush();

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
     * @param EntityManagerInterface $em
     *     Doctrine entity manager for database operations.
     *
     * @return Response
     *   Redirects to the clients index view after city creation.
     */
    #[Route('/cities/create', name: 'city_create', methods: ['POST'])]
    public function createCity(EntityManagerInterface $em): Response
    {
        session_start();
        $user = $em->find(User::class,  $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_name = $em->getRepository(City::class)->findBy( array('name' => $name) );
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

        $em->persist($newCity);
        $em->flush();

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
     * @param EntityManagerInterface $em
     *    Doctrine entity manager for database operations.
     *
     * @return Response
     *   Redirects to the clients index view after street creation.
     */
    #[Route('/streets/create', name: 'street_create', methods: ['POST'])]
    public function createStreet(EntityManagerInterface $em): Response
    {
        session_start();
        $user = $em->find(User::class, $_SESSION['user_id']);

        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        // Check if name already exist in database.
        $control_name = $em->getRepository(Street::class)->findBy( array('name' => $name) );
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

        $em->persist($newStreet);
        $em->flush();

        return $this->redirectToRoute('clients_index');
    }

    /**
     * Displays and processes the advanced search form for clients.
     *
     * Requires the user to be authenticated (session username set).
     * Accepts client, street, and city search terms from POST data, performs advanced search using the Client
     * repository, and passes the results and search terms along with page and user metadata to the template.
     *
     * @param EntityManagerInterface $em
     *   Doctrine entity manager for database operations.
     *
     * @return Response
     *   The rendered advanced search view with results if a search was performed.
     */
    #[Route('/clients/advanced-search', name: 'client_advanced_search', methods: ['GET', 'POST'])]
    public function advancedSearch(EntityManagerInterface $em): Response
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
            $clients_data = $em->getRepository(Client::class)->advancedSearch($term, $street, $city);
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

}
