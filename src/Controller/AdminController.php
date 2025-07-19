<?php

namespace App\Controller;

use App\Core\BaseController;

use App\Entity\City;
use App\Entity\CompanyInfo;
use App\Entity\Country;
use App\Entity\Employee;
use App\Entity\Street;

require_once __DIR__ . '/../../config/dbConfig.php';

/**
 * AdminController class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class AdminController extends BaseController
{

    private $page;
    private $page_title;

    /**
     * AdminController constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->page = 'admin';
        $this->page_title = 'Admin';
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function index($search = NULL):void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'tools_menu' => [
                'admin' => FALSE,
            ],
        ];

        $this->render('admin/index.html.twig', $data);
    }

    /**
     * View company info.
     *
     * @return void
     */
    public function viewCompanyInfo(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
            ],
        ];

        $this->render('admin/viewCompanyInfo.html.twig', $data);
    }

    /**
     * Edit company info form.
     *
     * @return void
     */
    public function editCompanyInfoForm(): void
    {

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
            ],
        ];

        $this->render('admin/editCompanyInfo.html.twig', $data);
    }

    /**
     * Edit company info.
     *
     * @return void
     */
    public function editCompanyInfo(): void
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

        die('<script>location.href = "/admin/company-info" </script>');
    }

    /**
     * Database backup.
     *
     * @return void
     */
    public function baseBackup (): void
    {
        $dump_dir = '';
        $dumpfile = "roloffice_" . date("Y-m-d_H-i-s") . "_" . ENV . "_" . APP_VERSION . ".sql";

        // Check OS version.
        $os = PHP_OS;
        if ($os == 'Windows') {
            // $root = "D:/Documents/BackUps/MYSQL/";
            $dump_dir = getenv('HOMEDRIVE') . getenv('HOMEPATH') . '\Downloads';
        }
        elseif ($os == 'Linux') {
            // @HOLMES - Need define Download folder for Linux systems.
            $dump_dir = __DIR__ . '/../home/dragan/Downloads/';
        }

        $command = "C:/xampp/mysql/bin/mysqldump --opt --host=" . DB_SERVER
          . " --user=" . DB_USERNAME
          . " --password=" . DB_PASSWORD
          . " "  . DB_NAME . " > " . $dump_dir . $dumpfile;

        try {
            exec($command);
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        echo '
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> Obaveštenje!</h4>
                Backup baze je izvšen u fajl: <br />' . $dump_dir . $dumpfile . '
            </div>
            ';

        passthru("tail -1 $dumpfile");
    }

    /**
     * Employees action.
     *
     * @return void
     */
    public function employees():void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $data = [
            'page' => $this->page . '/employees',
            'page_title' => $this->page_title,
            'last_employees' => $this->entityManager->getRepository(Employee::class)->getLastEmployees(10),
            'tools_menu' => [
                'employees' => TRUE,
            ],
        ];

        $this->render('admin/employees.html.twig', $data);
    }

  /**
   * View employee details.
   *
   * @param int $employee_id
   *
   * @return void
   */
    public function employeeView(int $employee_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $employee_data = $this->entityManager->find(Employee::class, $employee_id);

        $data = [
            'page' => $this->page . '/employee',
            'page_title' => $this->page_title . ' | Employee -' . $employee_data->getName(),
            'employee' => $employee_data,
            'tools_menu' => [
                'admin' => TRUE,
                'employee' => [
                    'view' => TRUE,
                    'edit' => FALSE,
                ],
            ],
        ];

        $this->render('admin/viewEmployee.html.twig', $data);
    }

  /**
   * Employee edit form.
   *
   * @param int $employee_id
   *
   * @return void
   */
    public function employeeEditForm(int $employee_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

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
            ],
        ];

        $this->render('admin/editEmployee.html.twig', $data);
    }

  /**
   * Edit employee.
   *
   * @param int $employee_id
   *
   * @return void
   */
    public function employeeEdit(int $employee_id):void
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

        die('<script>location.href = "/admin/employee/' . $employee_id . '" </script>');
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

    public function employeesSearch(string $term)
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $employees= $this->entityManager->getRepository(Employee::class)->search($term);

        $data = [
          'page' => $this->page . '/employees',
          'page_title' => $this->page_title,
          'employees' => $employees,
        ];

        $this->render('admin/search_employees.html.twig', $data);
    }

}
