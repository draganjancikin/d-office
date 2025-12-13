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
use App\Form\CountryType;
use App\Form\CityType;
use App\Form\StreetType;
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

    private EntityManagerInterface $em;
    protected string $page = 'clients';
    protected string $pageTitle = 'Klijenti';
    protected string $stylesheet;

    /**
     * ClientController constructor.
     *
     * @param EntityManagerInterface $em The Doctrine entity manager for database operations.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
    }

    /**
     * Displays the clients index page with a list of the most recently added clients.
     *
     * Requires the user to be authenticated (session username set).
     * Passes page metadata, user info, and last clients to the template for rendering.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered clients index view.
     */
    #[Route('/clients', name: 'clients_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        if ($request->query->has('search')) {
            $term = $request->query->get('search', '');
            $clients= $this->em->getRepository(Client::class)->search($term);
        } else {
            $clients = $this->em->getRepository(Client::class)->findAll();
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['clients'] = $clients;

        return $this->render('client/index.html.twig', $data);
    }

    /**
     * Displays the form for creating a new client and handles its submission.
     *
     * Requires the user to be authenticated (session username set).
     * Renders the Symfony form for client creation, processes form submission, persists the new client,
     * sets the current user as created_by_user, and redirects to the client detail view with a flash message on success.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered new client form view or a redirect to the client detail view after creation.
     */
    #[Route('/clients/new', name: 'client_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = new Client();
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $client->setCreatedByUser($user);

        $form = $this->createForm(ClientType::class, $client); // CORRECT
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($client);
            $this->em->flush();

            // $this->addFlash('success', 'Client created successfully!');
            return $this->redirectToRoute('client_show', ['id' => $client->getId()]);
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['form'] = $form->createView();

        return $this->render('client/new.html.twig', $data);
    }

    /**
     * Displays the details for a specific client.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves client data and related contact types, and passes them along with user and page metadata to the template.
     *
     * @param int $id The unique identifier of the client to display.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered client detail view.
     */
    #[Route('/clients/{id}', name: 'client_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(int $id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = $this->em->find(Client::class, $id);
        $user = $this->em->find(User::class, $_SESSION['user_id']);

        $contact = new Contact();
        $contactForm = $this->createForm(ContactType::class, $contact);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $contact->setModifiedAt(new \DateTime("now"));
            $contact->setCreatedAt(new \DateTime("now"));
            $contact->setCreatedByUser($user);
            $client->addContact($contact);

            $this->em->persist($contact);
            $this->em->flush();

            // $this->addFlash('success', 'Contact created successfully!');
            return $this->redirectToRoute('client_show', ['id' => $id]);
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => TRUE,
            'view' => TRUE,
            'edit' => FALSE,
        ];
        $data['client'] = $client;
        $data['contact_form'] = $contactForm->createView();

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
     * @param int $id The unique identifier of the client to edit.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered client edit view or a redirect to the client detail view after editing.
     */
    #[Route('/clients/{id}/edit', name: 'client_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $client = $this->em->find(Client::class, $id);
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $form = $this->createForm(ClientType::class, $client);

        $originalContacts = [];
        foreach ($client->getContacts() as $contact) {
            $contactId = null;
            if (method_exists($contact, 'getId')) {
                try {
                    $contactId = $contact->getId();
                } catch (\Error $e) {
                    $contactId = null;
                }
            }
            if ($contactId) {
                $originalContacts[$contactId] = clone $contact;
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setModifiedByUser($user);
            $client->setModifiedAt(new \DateTime("now"));

            foreach ($client->getContacts() as $contact) {
                $contactId = null;
                if (method_exists($contact, 'getId')) {
                    try {
                        $contactId = $contact->getId();
                    } catch (\Error $e) {
                        $contactId = null;
                    }
                }
                if (!$contactId) {
                    if ($contact->getCreatedAt() === null) {
                        $contact->setCreatedAt(new \DateTime());
                    }
                    $contact->setModifiedAt(new \DateTime());
                    continue;
                }
                if (isset($originalContacts[$contactId])) {
                    $orig = $originalContacts[$contactId];
                    if (
                        $contact->getType() !== $orig->getType() ||
                        $contact->getBody() !== $orig->getBody() ||
                        $contact->getNote() !== $orig->getNote()
                    ) {
                        $contact->setModifiedAt(new \DateTime());
                    }
                }
            }

            $this->em->flush();

            // $this->addFlash('success', 'Client updated successfully!');
            return $this->redirectToRoute('client_show', ['id' => $id]);
        }

        $contact = new Contact();
        $contactForm = $this->createForm(ContactType::class, $contact);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $contact->setModifiedAt(new \DateTime("now"));
            $contact->setCreatedAt(new \DateTime("now"));
            $contact->setCreatedByUser($user);
            $client->addContact($contact);

            $this->em->persist($contact);
            $this->em->flush();

            // $this->addFlash('success', 'Contact created successfully!');
            return $this->redirectToRoute('client_show', ['id' => $id]);
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => TRUE,
            'view' => FALSE,
            'edit' => TRUE,
        ];
        $data['client'] = $client;
        $data['form'] = $form->createView();
        $data['contact_form'] = $contactForm->createView();

        return $this->render('client/edit.html.twig', $data);
    }

    /**
     * Displays the form for creating a new country.
     *
     * Requires the user to be authenticated (session username set).
     * Passes user and page metadata to the template for rendering the new country form.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered new country form view.
     */
    #[Route('/countries/new', name: 'country_new', methods: ['GET', 'POST'])]
    public function newCountry(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $country = new Country();

        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $country->setCreatedByUser($user);

        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($country->getAbbr() === null) {
                $country->setAbbr('');
            }
            $country->setCreatedAt(new \DateTime());
            $country->setModifiedAt(new \DateTime());
            $this->em->persist($country);
            $this->em->flush();

            // $this->addFlash('success', 'Country created successfully!');
            return $this->redirectToRoute('country_show', ['country_id' => $country->getId()]);
        }

        $allCountries = $this->em->getRepository(Country::class)->findAll();

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['form'] = $form->createView();
        $data['all_countries'] = $allCountries;

        return $this->render('client/country_new.html.twig', $data);
    }

    /**
     * Displays the details for a specific country.
     *
     * @param int $country_id The unique identifier of the country to display.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered country detail view.
     */
    #[Route('/countries/{country_id}', name: 'country_show', requirements: ['country_id' => '\d+'], methods: ['GET'])]
    public function showCountry(int $country_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $country = $this->em->getRepository(Country::class)->find($country_id);
        $allClientsWhereCountryUse = $this->em->getRepository(Client::class)->findBy(['country' => $country]);

        $allowToDelete = FALSE;
        if (count($allClientsWhereCountryUse) === 0) {
            $allowToDelete = TRUE;
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['country'] = $country;
        $data['allow_to_delete'] = $allowToDelete;
        $data['all_clients_where_country_use'] = $allClientsWhereCountryUse;

        return $this->render('client/country_view.html.twig', $data);
    }

    /**
     * Edit Country form.
     *
     * @param int $country_id The unique identifier of the country to edit.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered country edit view or a redirect to the country detail view after editing.
     *
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route('/countries/{country_id}/edit', name: 'country_edit', methods: ['GET', 'POST'])]
    public function editCountry(int $country_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }
        $country = $this->em->find(Country::class, $country_id);
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $country->setModifiedByUser($user);
            $country->setModifiedAt(new \DateTime("now"));
            $this->em->flush();
            return $this->redirectToRoute('country_show', ['country_id' => $country_id]);
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['form'] = $form->createView();

        return $this->render('client/country_edit.html.twig', $data);
    }

    /**
     * Deletes a country if it is not used by any clients.
     *
     * @param int $country_id The unique identifier of the country to delete.
     * @param Request $request The HTTP request object.
     *
     * @return Response Redirects to the country list or shows an error if not allowed.
     */
    #[Route('/countries/{country_id}/delete', name: 'country_delete', requirements: ['country_id' => '\d+'], methods:
        ['POST', 'GET'])]
    public function deleteCountry(int $country_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $country = $this->em->getRepository(Country::class)->find($country_id);
        if (!$country) {
            throw $this->createNotFoundException('Country not found.');
        }

        $allClientsWhereCountryUse = $this->em->getRepository(Client::class)->findBy(['country' => $country]);
        if (count($allClientsWhereCountryUse) > 0) {
            // $this->addFlash('error', 'Cannot delete country: it is used by clients.');
            return $this->redirectToRoute('country_show', ['country_id' => $country_id]);
        }

        $this->em->remove($country);
        $this->em->flush();
        // $this->addFlash('success', 'Country deleted successfully.');
        return $this->redirectToRoute('country_new');
    }


    /**
     * Displays the form for creating a new city.
     *
     * Requires the user to be authenticated (session username set).
     * Passes user and page metadata to the template for rendering the new city form.
     *
     * @return Response The rendered new city form view.
     */
    #[Route('/cities/new', name: 'city_new', methods: ['GET', 'POST'])]
    public function newCity(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $city = new City();
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $city->setCreatedByUser($user);

        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $city->setCreatedAt(new \DateTime("now"));
            $city->setModifiedAt(new \DateTime("now"));
            $this->em->persist($city);
            $this->em->flush();
            return $this->redirectToRoute('city_show', ['city_id' => $city->getId()]);
        }

        $allCities = $this->em->getRepository(City::class)->findAll();

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['form'] = $form->createView();
        $data['all_cities'] = $allCities;

        return $this->render('client/city_new.html.twig', $data);
    }

    /**
     * Displays the details for a specific city.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves city data and passes it along with user and page metadata to the template.
     *
     * @param int $city_id The unique identifier of the city to display.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered city detail view.
     */
    #[Route('/cities/{city_id}', name: 'city_show', requirements: ['city_id' => '\d+'], methods: ['GET'])]
    public function showCity(int $city_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $city = $this->em->find(City::class, $city_id);
        if (!$city) {
            throw $this->createNotFoundException('City not found.');
        }
        $allClientsWhereCityUse = $this->em->getRepository(Client::class)->findBy(['city' => $city]);

        $allowToDelete = FALSE;
        if (count($allClientsWhereCityUse) === 0) {
            $allowToDelete = TRUE;
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['city'] = $city;
        $data['allow_to_delete'] = $allowToDelete;
        $data['all_clients_where_city_use'] = $allClientsWhereCityUse;

        return $this->render('client/city_view.html.twig', $data);
    }

    /**
     * Edit City form.
     *
     * @param int $city_id The unique identifier of the city to edit.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered city edit view or a redirect to the city detail view after editing.
     */
    #[Route('/cities/{city_id}/edit', name: 'city_edit', requirements: ['$city_id' => '\d+'], methods: ['GET', 'POST'])]
    public function editCity(int $city_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }
        $city = $this->em->find(City::class, $city_id);
        if (!$city) {
            throw $this->createNotFoundException('City not found.');
        }
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $city->setModifiedByUser($user);
            $city->setModifiedAt(new \DateTime("now"));
            $this->em->flush();
            return $this->redirectToRoute('city_show', ['city_id' => $city_id]);
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['form'] = $form->createView();

        return $this->render('client/city_edit.html.twig', $data);
    }

    /**
     * Deletes a city if it is not used by any clients.
     *
     * @param int $city_id The unique identifier of the city to delete.
     * @param Request $request The HTTP request object.
     *
     * @return Response Redirects to the city list or shows an error if not allowed.
     */
    #[Route('/cities/{city_id}/delete', name: 'city_delete', requirements: ['city_id' => '\d+'], methods: ['POST', 'GET'])]
    public function deleteCity(int $city_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $city = $this->em->find(City::class, $city_id);
        if (!$city) {
            throw $this->createNotFoundException('City not found.');
        }

        $allClientsWhereCityUse = $this->em->getRepository(Client::class)->findBy(['city' => $city]);
        if (count($allClientsWhereCityUse) > 0) {
            $this->addFlash('error', 'Cannot delete city: it is used by clients.');
            return $this->redirectToRoute('city_show', ['city_id' => $city_id]);
        }

        $this->em->remove($city);
        $this->em->flush();
        // $this->addFlash('success', 'City deleted successfully.');
        return $this->redirectToRoute('city_new');
    }

    /**
     * Add Street form.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered new street form view.
     */
    #[Route('/streets/new', name: 'street_new', methods: ['GET', 'POST'])]
    public function newStreet(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
          return $this->redirectToRoute('login_form');
        }

        $street = new Street();
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $street->setCreatedByUser($user);

        $form = $this->createForm(StreetType::class, $street);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $street->setCreatedAt(new \DateTime('now'));
            $street->setModifiedAt(new \DateTime('now'));
            $this->em->persist($street);
            $this->em->flush();
            return $this->redirectToRoute('street_show', ['street_id' => $street->getId()]);
        }

        $allStreets = $this->em->getRepository(Street::class)->findAll();

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['form'] = $form->createView();
        $data['all_streets'] = $allStreets;

        return $this->render('client/street_new.html.twig', $data);
    }

    /**
     * Displays the details for a specific street.
     *
     * Requires the user to be authenticated (session username set).
     * Retrieves street data and passes it along with user and page metadata to the template.
     *
     * @param int $street_id The unique identifier of the street to display.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered street detail view.
     */
    #[Route('/streets/{street_id}', name: 'street_show', requirements: ['street_id' => '\\d+'], methods: ['GET'])]
    public function showStreet(int $street_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $street = $this->em->find(Street::class, $street_id);
        if (!$street) {
            throw $this->createNotFoundException('Street not found.');
        }
        $allClientsWhereStreetUse = $this->em->getRepository(Client::class)->findBy(['street' => $street]);

        $allowToDelete = FALSE;
        if (count($allClientsWhereStreetUse) === 0) {
            $allowToDelete = TRUE;
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['street'] = $street;
        $data['allow_to_delete'] = $allowToDelete;
        $data['all_clients_where_street_use'] = $allClientsWhereStreetUse;

        return $this->render('client/street_view.html.twig', $data);
    }

    /**
     * Edit Street form.
     *
     * @param int $street_id The unique identifier of the street to edit.
     * @param Request $request The HTTP request object.
     *
     * @return Response The rendered street edit view or a redirect to the street detail view after editing.
     */
    #[Route('/streets/{street_id}/edit', name: 'street_edit', requirements: ['street_id' => '\\d+'], methods: ['GET', 'POST'])]
    public function editStreet(int $street_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }
        $street = $this->em->find(Street::class, $street_id);
        if (!$street) {
            throw $this->createNotFoundException('Street not found.');
        }
        $user = $this->em->find(User::class, $_SESSION['user_id']);
        $form = $this->createForm(StreetType::class, $street);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $street->setModifiedByUser($user);
            $street->setModifiedAt(new \DateTime("now"));
            $this->em->flush();
            return $this->redirectToRoute('street_show', ['street_id' => $street_id]);
        }

        $data = $this->getDefaultData();
        $data['tools_menu'] = [
            'client' => FALSE,
        ];
        $data['form'] = $form->createView();

        return $this->render('client/street_edit.html.twig', $data);
    }

    /**
     * Deletes a street if it is not used by any clients.
     *
     * @param int $street_id The unique identifier of the street to delete.
     * @param Request $request The HTTP request object.
     *
     * @return Response Redirects to the street list or shows an error if not allowed.
     */
    #[Route('/streets/{street_id}/delete', name: 'street_delete', requirements: ['street_id' => '\d+'], methods: ['POST', 'GET'])]
    public function deleteStreet(int $street_id, Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $street = $this->em->find(Street::class, $street_id);
        if (!$street) {
            throw $this->createNotFoundException('Street not found.');
        }

        $allClientsWhereStreetUse = $this->em->getRepository(Client::class)->findBy(['street' => $street]);
        if (count($allClientsWhereStreetUse) > 0) {
            // $this->addFlash('error', 'Cannot delete street: it is used by clients.');
            return $this->redirectToRoute('street_show', ['street_id' => $street_id]);
        }

        $this->em->remove($street);
        $this->em->flush();
        // $this->addFlash('success', 'Street deleted successfully.');
        return $this->redirectToRoute('street_new');
    }

    /**
     * Returns default data array for views.
     *
     * @return array An associative array containing default data for views.
     */
    private function getDefaultData(): array
    {
        return [
            'page' => $this->page,
            'page_title' => $this->pageTitle,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];
    }
}
