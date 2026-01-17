<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\CompanyInfo;
use App\Entity\Country;
use App\Entity\Employee;
use App\Entity\Street;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * AdminController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class AdminController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private string $page;
    private string $page_title;
    protected string $stylesheet;

    /**
     * AdminController constructor.
     *
     * - Initializes controller properties for page, page title, stylesheet path, and application version.
     * - Sets up the Doctrine EntityManager for database operations.
     * - Loads the stylesheet path from environment variables or defaults to '/libraries/'.
     * - Loads the current application version.
     *
     * @param EntityManagerInterface $entityManager
     *   The Doctrine entity manager for database access.
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->page = 'admin';
        $this->page_title = 'Admin';
    }

    /**
     * Displays the admin dashboard page.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Prepares data for the template, including page metadata, user role, username, stylesheet, and app version.
     * - Renders the 'admin/index.html.twig' template with the admin dashboard data.
     *
     * @return Response
     *   The HTTP response with the rendered admin dashboard or a redirect to the login page.
     */
    #[Route('/admin/', name: 'admin_index')]
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
                'admin' => FALSE,
            ],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('admin/index.html.twig', $data);
    }

    /**
     * Displays the company information page in the admin panel.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the company information entity from the database.
     * - Prepares data for the template, including page metadata, user role, username, stylesheet, and app version.
     * - Renders the 'admin/company_info_view.html.twig' template with the company information and related data.
     *
     * @return Response
     *   The HTTP response with the rendered company information page or a redirect to the login page.
     */
    #[Route('/admin/company-info', name: 'admin_company_info')]
    public function showCompanyInfo(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company = $this->entityManager->find(CompanyInfo::class, '1');

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'company' => $company,
            'tools_menu' => [
                'admin' => TRUE,
                'company_info' => [
                    'view' => TRUE,
                    'edit' => FALSE,
                ],
                'employee' => [
                    'view' => FALSE,
                    'edit' => FALSE,
                ],
            ],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('admin/company_info_view.html.twig', $data);
    }

    /**
     * Displays the form for editing company information in the admin panel.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the company information entity from the database.
     * - Fetches all countries, cities, and streets, ordered by name, for form selection fields.
     * - Prepares data for the template, including page metadata, user role, username, stylesheet, and app version.
     * - Renders the 'admin/company_info_edit.html.twig' template with the company information and related data for editing.
     *
     * @return Response
     *   The HTTP response with the rendered company information edit form or a redirect to the login page.
     */
    #[Route('/admin/company-info/edit', name: 'admin_company_info_edit')]
    public function editCompanyInfo(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company = $this->entityManager->find(CompanyInfo::class, '1');

        $states = $this->entityManager->getRepository(Country::class)->findBy([], ['name' => 'ASC']);
        $cities = $this->entityManager->getRepository(City::class)->findBy([], ['name' => 'ASC']);
        $streets = $this->entityManager->getRepository(Street::class)->findBy([], ['name' => 'ASC']);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'company' => $company,
            'states' => $states,
            'cities' => $cities,
            'streets' => $streets,
            'tools_menu' => [
                'admin' => TRUE,
                'company_info' => [
                    'view' => FALSE,
                    'edit' => TRUE,
                ],
                'employee' => [
                    'view' => FALSE,
                    'edit' => FALSE,
                ],
            ],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('admin/company_info_edit.html.twig', $data);
    }

    /**
     * Handles updating the company information with data from a POST request.
     *
     * - Retrieves and sanitizes all relevant company info fields from POST data (name, PIB, MB, address, contacts, etc.).
     * - Finds the CompanyInfo entity and related Country, City, and Street entities by their IDs.
     * - Updates the CompanyInfo entity with the new data.
     * - Persists the changes to the database.
     * - Redirects to the company information view page after successful update.
     *
     * @return Response
     *   Redirects to the company information view page after update.
     */
    #[Route('/admin/company-info/update', name: 'admin_company_info_update', methods: ['POST'])]
    public function updateCompanyInfo(): Response
    {
        $name = $_POST["name"] ?? "";
        $pib = $_POST["pib"] ?? "";
        $mb = $_POST["mb"] ?? "";

        $country_id = $_POST["country_id"] ?? null;
        $country = $this->entityManager->find(Country::class, $country_id);
        $city_id = $_POST["city_id"] ?? null;
        $city = $this->entityManager->find(City::class, $city_id);
        $street_id = $_POST["street_id"] ?? null;
        $street = $this->entityManager->find(Street::class, $street_id);

        $home_number = $_POST["home_number"] ?? "";
        $bank_account_1 = $_POST["bank_account_1"] ?? "";
        $bank_account_2 = $_POST["bank_account_2"] ?? "";
        $phone_1 = $_POST["phone_1"] ?? "";
        $phone_2 = $_POST["phone_2"] ?? "";
        $email_1 = $_POST["email_1"] ?? "";
        $email_2 = $_POST["email_2"] ?? "";
        $website_1 = $_POST["website_1"] ?? "";

        $company_info = $this->entityManager->find(CompanyInfo::class, 1);

        $company_info->setName($name);
        $company_info->setPib($pib);
        $company_info->setMb($mb);
        $company_info->setCountry($country);
        $company_info->setCity($city);
        $company_info->setStreet($street);
        $company_info->setHomeNumber($home_number);
        $company_info->setBankAccount1($bank_account_1);
        $company_info->setBankAccount2($bank_account_2);
        $company_info->setPhone1($phone_1);
        $company_info->setPhone2($phone_2);
        $company_info->setEmail1($email_1);
        $company_info->setEmail2($email_2);
        $company_info->setWebsite1($website_1);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_company_info');
    }

    /**
     * Creates a backup of the database and stores it in the var/backups directory.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Reads database credentials from environment variables.
     * - Ensures the backup directory exists.
     * - Constructs a filename with the current date and environment.
     * - Runs the mysqldump command using Symfony's Process component to export the database.
     * - Saves the dump output to a file in the backup directory.
     * - Adds a success flash message with the backup file path.
     * - Redirects to the admin index page after completion.
     *
     * @return Response
     *   Redirects to the admin index page after backup, or to the login page if not authenticated.
     *
     * @throws ProcessFailedException
     *   If the mysqldump process fails.
     */
    #[Route('/admin/backup-database', name: 'admin_backup_database')]
    public function backupDatabase(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        // Read database credentials from .env
        $dbHost = $_ENV['DB_SERVER'] ?? 'localhost';
        $dbUser = $_ENV['DB_USER'] ?? 'root';
        $dbPass = $_ENV['DB_PASSWORD'] ?? 'root';
        $dbName = $_ENV['DB_NAME'] ?? 'default';

        // Create backup directory
        $backupDir = $this->getParameter('kernel.project_dir') . '/var/backups';
        if (!is_dir($backupDir)) {
          mkdir($backupDir, 0755, true);
        }

        $env = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'production';

        // Create filename (e.g. backup_2025-11-11_19-40.sql)
        $fileName = sprintf('backup_%s.sql', date('Y-m-d_H-i-s'));
        $filePath = $backupDir . '/' . $env . '_' . $fileName;

        // mysqldump command.
        $command = [
          'mysqldump',
          '-h', $dbHost,
          '-u', $dbUser,
          sprintf('--password=%s', $dbPass),
          $dbName,
        ];

        // Run process.
        $process = new Process($command);
        $process->run();

        // Check for errors.
        if (!$process->isSuccessful()) {
          throw new ProcessFailedException($process);
        }

        // Save dump output to file.
        file_put_contents($filePath, $process->getOutput());

        $this->addFlash('success', 'Backup has been created in file ' . $filePath);
        return $this->redirectToRoute('admin_index');
    }

    /**
     * Displays the list of the most recent employees in the admin panel.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the 10 most recently added employees from the database.
     * - Prepares data for the template, including page metadata, user role, username, stylesheet, and app version.
     * - Renders the 'admin/employees_list.html.twig' template with the employees data.
     *
     * @return Response
     *   The HTTP response with the rendered employees list or a redirect to the login page.
     */
    #[Route('/admin/employees', name: 'admin_employees_list')]
    public function employees(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $data = [
            'page' => $this->page . '/employees',
            'page_title' => $this->page_title,
            'last_employees' => $this->entityManager->getRepository(Employee::class)->getLastEmployees(10),
            'tools_menu' => [
                'employees' => TRUE,
              'admin' => FALSE,
            ],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('admin/employees_list.html.twig', $data);
    }

    /**
     * Displays the details of a specific employee in the admin panel.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the employee entity by its ID from the database.
     * - Prepares data for the template, including page metadata, user role, username, stylesheet, and app version.
     * - Renders the 'admin/employee_view.html.twig' template with the employee's details and related data.
     *
     * @param int $employee_id
     *   The ID of the employee to display.
     *
     * @return Response
     *   The HTTP response with the rendered employee details or a redirect to the login page.
     */
    #[Route('/admin/employee/{employee_id}', name: 'admin_employee_view')]
    public function showEmployee(int $employee_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $employee_data = $this->entityManager->find(Employee::class, $employee_id);

        $data = [
            'page' => $this->page . '/employee',
            'page_title' => $this->page_title . ' | Employee -' . $employee_data->getName(),
            'employee' => $employee_data,
            'tools_menu' => [
                'company_info' => [
                    'view' => FALSE,
                    'edit' => FALSE,
                ],
                'admin' => TRUE,
                'employee' => [
                    'view' => TRUE,
                    'edit' => FALSE,
                ],
            ],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('admin/employee_view.html.twig', $data);
    }

    /**
     * Displays the form for editing an existing employee in the admin panel.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the employee entity by its ID from the database.
     * - Prepares data for the template, including page metadata, user role, username, stylesheet, and app version.
     * - Renders the 'admin/employee_edit.html.twig' template with the employee's data for editing.
     *
     * @param int $employee_id
     *   The ID of the employee to edit.
     *
     * @return Response
     *   The HTTP response with the rendered employee edit form or a redirect to the login page.
     */
    #[Route('/admin/employee/{employee_id}/edit', name: 'admin_employee_edit_form')]
    public function editEmployee(int $employee_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $employee_data = $this->entityManager->find(Employee::class, $employee_id);

        $data = [
            'page' => $this->page . '/employee',
            'page_title' => $this->page_title . ' | Employee -' . $employee_data->getName(),
            'employee' => $employee_data,
            'tools_menu' => [
                'admin' => TRUE,
                'employee' => [
                    'view' => FALSE,
                    'edit' => TRUE,
                ],
                'company_info' => [
                    'view' => FALSE,
                    'edit' => FALSE,
                ],
            ],
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
        ];

        return $this->render('admin/employee_edit.html.twig', $data);
    }

    /**
     * Handles updating an existing employee with data from a POST request.
     *
     * - Validates the 'name' field from POST data and halts with an error if missing.
     * - Retrieves the employee entity by its ID from the database.
     * - Updates the employee's name with validated input.
     * - Persists the changes to the database.
     * - Adds a success flash message upon successful update.
     * - Redirects to the employee details page after update.
     *
     * @param int $employee_id
     *   The ID of the employee to update.
     *
     * @return Response
     *   Redirects to the employee details page after update, or halts if validation fails.
     */
    #[Route('/admin/employee/{employee_id}/update', name: 'admin_employee_update', methods: ['POST'])]
    public function updateEmployee(int $employee_id): Response
    {
        if (empty($_POST['name'])) {
            $nameError = 'Ime mora biti upisano';
            die('<script>location.href = "?new&name_error" </script>');
        }
        else {
            $name = $this->basicValidation($_POST['name']);
        }

        $employee = $this->entityManager->find(Employee::class, $employee_id);

        $employee->setName($name);

        $this->entityManager->flush();

        $this->addFlash('success', 'Podaci o zaposlenom su uspesno izmenjeni.');
        return $this->redirectToRoute('admin_employee_view', ['employee_id' => $employee_id]);
    }

    /**
     * Basic validation method.
     *
     * @param string $str
     *
     * @return string
     */
    public function basicValidation(string $str): string
    {
        return trim(htmlspecialchars($str));
    }

    /**
     * Searches for employees based on a search term provided in the request query.
     *
     * - Starts a session and checks if the user is logged in (redirects to login if not).
     * - Retrieves the search term from the request query parameters.
     * - Uses the Employee repository to search for employees matching the term.
     * - Prepares data for the template, including the search results, page metadata, user role, username, stylesheet, and app version.
     * - Renders the 'admin/table/employees_search.html.twig' template with the search results.
     *
     * @param Request $request
     *   The HTTP request containing the search term in the query string.
     *
     * @return Response
     *   The HTTP response with the rendered search results or a redirect to the login page.
     */
    #[Route('/admin/employees/search', name: 'admin_employees_search')]
    public function employeesSearch(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term');
        $employees= $this->entityManager->getRepository(Employee::class)->search($term);

        $data = [
            'page' => $this->page . '/employees',
            'page_title' => $this->page_title,
            'employees' => $employees,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'admin' => FALSE,
            ],
        ];

        return $this->render('admin/table/employees_search.html.twig', $data);
    }

}
