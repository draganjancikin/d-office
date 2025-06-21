<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\CompanyInfo;
use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\ProjectNote;
use App\Entity\ProjectPriority;
use App\Entity\ProjectStatus;
use App\Entity\ProjectTask;
use App\Entity\ProjectTaskNote;
use App\Entity\ProjectTaskType;
use App\Entity\ProjectTaskStatus;
use App\Entity\User;
use TCPDF;

/**
 * ProjectController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class ProjectController extends BaseController
{

    private $page;
    private $page_title;
    private $stylesheet;

    /**
     * ArticleController constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->page = 'project';
        $this->page_title = 'Projekti';
        $this->stylesheet = '/../libraries/';
    }

    /**
     * Index action.
     *
     * @param string|null $search
     *
     * @return void
     */
    public function index($search = NULL): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $cities = $this->entityManager->getRepository(Project::class)->getCitiesByActiveProject();

        $active_projects = $this->entityManager->getRepository(Project::class)->projectTracking('1');
        $active_projects_data = [];
        foreach ($active_projects as $active_project) {
            $project_tasks = $this->entityManager->getRepository(Project::class)->projectTasks($active_project->getId());
            $tasks_for_realization = [];
            $tasks_in_realization = [];
            $tasks_completed = [];
            foreach ($project_tasks as $project_task) {
                if ($project_task->getStatus()->getId() == 1){
                    $tasks_for_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),

                    ];
                }

                if ($project_task->getStatus()->getId() == 2){
                    $tasks_in_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }

                if ($project_task->getStatus()->getId() == 3){
                    $tasks_completed[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }
            }
            $active_projects_data[] = [
                'id' => $active_project->getId(),
                'ordinal_num_in_year' => $active_project->getOrdinalNumInYear(),
                'title' => $active_project->getTitle(),
                'client' => $active_project->getClient()->getName(),
                'created_at' => $active_project->getCreatedAt()->format('Y-m-d H:i:s'),
                'city' => $active_project->getClient()->getCity(),
                'tasks_for_realization' => $tasks_for_realization,
                'tasks_in_realization' => $tasks_in_realization,
                'tasks_completed' => $tasks_completed,
            ];
        }

        $inactive_projects = $this->entityManager->getRepository(Project::class)->projectTracking('2');
        $inactive_projects_data = [];

        foreach ($inactive_projects as $inactive_project) {
            $project_tasks = $this->entityManager->getRepository(Project::class)->projectTasks($inactive_project->getId());
            $tasks_for_realization = [];
            $tasks_in_realization = [];
            $tasks_completed = [];
            foreach ($project_tasks as $project_task) {
                if ($project_task->getStatus()->getId() == 1){
                    $tasks_for_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }

                if ($project_task->getStatus()->getId() == 2){
                    $tasks_in_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }

                if ($project_task->getStatus()->getId() == 3){
                    $tasks_completed[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }

            }
            $inactive_projects_data[] = [
                'id' => $inactive_project->getId(),
                'ordinal_num_in_year' => $inactive_project->getOrdinalNumInYear(),
                'title' => $inactive_project->getTitle(),
                'client' => $inactive_project->getClient()->getName(),
                'created_at' => $inactive_project->getCreatedAt()->format('Y-m-d H:i:s'),
                'city' => $inactive_project->getClient()->getCity(),
                'tasks_for_realization' => $tasks_for_realization,
                'tasks_in_realization' => $tasks_in_realization,
                'tasks_completed' => $tasks_completed,
            ];
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'search' => $search,
            'cities' => $cities,
            'tools_menu' => [
                'project' => FALSE,
            ],
            'active_projects_data' => $active_projects_data,
            'inactive_projects_data' => $inactive_projects_data,
        ];

        $this->render('project/index.html.twig', $data);
    }

    /**
     * Add project form.
     *
     * @param int $client_id
     * @param int $acc_doc_id
     *
     * @return void
     */
    public function addForm(int $client_id = NULL, int $acc_doc_id = NULL): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $clients_list = $this->entityManager->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'clients_list' => $clients_list,
        ];

        if ($acc_doc_id) {
            $data['acc_doc_id'] = $acc_doc_id;
        }

        if ($client_id) {
            $data['client'] = $this->entityManager->find(Client::class, $client_id);
        }

        $this->render('project/add.html.twig', $data);
    }

    /**
     * Add project.
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function add(): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $ordinal_num_in_year = 0;

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find(Client::class, $client_id);

        $title = htmlspecialchars($_POST['title']);

        $project_priority_id = htmlspecialchars($_POST['project_priority_id']);
        $project_priority = $this->entityManager->find(ProjectPriority::class, $project_priority_id);

        // $note = htmlspecialchars($_POST['note']);
        $note = "";

        $project_status = $this->entityManager->find(ProjectStatus::class, 1);

        // Save a new Project.
        $newProject = new Project();

        $newProject->setOrdinalNumInYear($ordinal_num_in_year);
        $newProject->setClient($client);
        $newProject->setTitle($title);
        $newProject->setPriority($project_priority);
        $newProject->setNote($note);
        // New Project has status '1' => 'Is active'.
        $newProject->setStatus($project_status);

        $newProject->setCreatedAt(new \DateTime("now"));
        $newProject->setCreatedByUser($user);
        $newProject->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newProject);
        $this->entityManager->flush();

        // Get id of last Project.
        $new_project_id = $newProject->getId();

        // Set Ordinal Number In Year.
        $this->entityManager->getRepository(Project::class)->setOrdinalNumInYear($new_project_id);

        if (isset($_POST['acc_doc_id'])) {
            $acc_doc_id = $_POST['acc_doc_id'];

            // Insert project_id and accountingdocument_id to table v6__projects__accounting_documents.
            $this->entityManager
                ->getConnection()
                ->insert('v6__projects__accounting_documents', [
                    'project_id' => $new_project_id,
                    'accountingdocument_id' => $acc_doc_id
                ]);
        }

        die('<script>location.href = "/project/' . $new_project_id . '" </script>');
    }

    /**
     * Project view.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function view(int $project_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $project_data = $this->entityManager->find(Project::class, $project_id);
        $notes = $this->entityManager->getRepository(Project::class)->getNotesByProject($project_id);

        $client = $this->entityManager->getRepository(Client::class)
            ->getClientData($project_data->getClient()->getId());

        $orders = $this->entityManager->find(Project::class, $project_id)->getOrders();

        $order_status_classes = [
            0 => [
                'class' => 'badge-light',
                'icon' => 'N',
                'title' => 'Nacrt',
            ],
            1 => [
                'class' => 'badge-warning',
                'icon' => 'P',
                'title' => 'Poručeno',
            ],
            2 => [
                'class' => 'badge-success',
                'icon' => 'S',
                'title' => 'Stiglo',
            ],
        ];

        $orders_data = [];
        if ($orders) {
            foreach ($orders as $order) {
                $orders_data[] = [
                    'id' => $order->getId(),
                    'ordinal_num_in_year' => $order->getOrdinalNumInYear(),
                    'title' => $order->getTitle(),
                    'created_at' => $order->getCreatedAt()->format('m_Y'),
                    'status' => $order->getStatus(),
                    'is_archived' => $order->getIsArchived(),
                    'supplier_name' => $order->getSupplier() ? $order->getSupplier()->getName() : '',
                ];
            }
        }

        $accounting_documents = $this->entityManager->find(Project::class, $project_id)->getAccountingDocuments();
        $accounting_documents_data = [];
        foreach ($accounting_documents as $accounting_document) {
            $accounting_documents_data[] = [
                'id' => $accounting_document->getId(),
                'ordinal_num_in_year' => $accounting_document->getOrdinalNumInYear(),
                'title' => $accounting_document->getTitle(),
                'created_at' => $accounting_document->getCreatedAt()->format('m_Y'),
                'client_name' => $accounting_document->getClient() ? $accounting_document->getClient()->getName() : '',
                'type' => $accounting_document->getType(),
            ];
        }

        $project_tasks = $this->entityManager->getRepository(Project::class)->projectTasks($project_id);
        $tasks_for_realization = [];
        $tasks_in_realization = [];
        $tasks_completed = [];
        foreach ($project_tasks as $project_task) {

            if ($project_task->getStatus()->getId() == 1){
                $task_notes = $this->entityManager
                  ->getRepository(ProjectTaskNote::class)->findBy(array('project_task' => $project_task));
                $tasks_for_realization[] = [
                    'id' => $project_task->getId(),
                    'title' => $project_task->getTitle(),
                    'type' => $project_task->getType()->getName(),
                    'class' => $project_task->getType()->getClass(),
                    'created_by_user' => $project_task->getCreatedByUser()->getUsername(),
                    'start_date' => $project_task->getStartDate()->format('Y-m-d H:i:s'),
                    'end_date' => $project_task->getEndDate()->format('Y-m-d H:i:s'),
                    'employee' => $project_task->getEmployee() ? $project_task->getEmployee()->getName() : '',
                    'task_notes' => $task_notes,
                ];
            }

            if ($project_task->getStatus()->getId() == 2){
                $task_notes = $this->entityManager
                    ->getRepository(ProjectTaskNote::class)->findBy(array('project_task' => $project_task));
                $tasks_in_realization[] = [
                    'id' => $project_task->getId(),
                    'title' => $project_task->getTitle(),
                    'type' => $project_task->getType()->getName(),
                    'class' => $project_task->getType()->getClass(),
                    'created_by_user' => $project_task->getCreatedByUser()->getUsername(),
                    'start_date' => $project_task->getStartDate()->format('Y-m-d H:i:s'),
                    'end_date' => $project_task->getEndDate()->format('Y-m-d H:i:s'),
                    'employee' => $project_task->getEmployee() ? $project_task->getEmployee()->getName() : '',
                    'task_notes' => $task_notes,
                ];
            }

            if ($project_task->getStatus()->getId() == 3){
                $task_notes = $this->entityManager
                    ->getRepository(ProjectTaskNote::class)->findBy(array('project_task' => $project_task));
                $tasks_completed[] = [
                    'id' => $project_task->getId(),
                    'title' => $project_task->getTitle(),
                    'type' => $project_task->getType()->getName(),
                    'class' => $project_task->getType()->getClass(),
                    'created_by_user' => $project_task->getCreatedByUser()->getUsername(),
                    'start_date' => $project_task->getStartDate()->format('Y-m-d H:i:s'),
                    'end_date' => $project_task->getEndDate()->format('Y-m-d H:i:s'),
                    'employee' => $project_task->getEmployee() ? $project_task->getEmployee()->getName() : '',
                    'task_notes' => $task_notes,
                ];
            }
        }

        $dir = $_SERVER["DOCUMENT_ROOT"] . '/upload/projects/project_id_'.$project_id;
        $files = [];
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($entry = readdir($handle))){
                    if ($entry != "." && $entry != ".." && $entry != "Thumbs.db"){
                        $files[] = $entry;
                    }
                }
            }
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
            'project_data' => $project_data,
            'client' => $client,
            'orders_data' => $orders_data,
            'notes' => $notes,
            'order_status_classes' => $order_status_classes,
            'accounting_documents_data' => $accounting_documents_data,
            'project_tasks' => $project_tasks,
            'tasks_for_realization' => $tasks_for_realization,
            'tasks_in_realization' => $tasks_in_realization,
            'tasks_completed' => $tasks_completed,
            'files' => $files,
            'tools_menu' => [
                'project' => TRUE,
                'view' => TRUE,
                'edit' => FALSE,
            ],
        ];

        $this->render('project/view.html.twig', $data);
    }

    /**
     * Project edit form.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function editForm(int $project_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $project_data = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project_data->getClient()->getId());
        $priority_list = $this->entityManager->getRepository(ProjectPriority::class)->findBy(array(), array('id' => "ASC"));
        $clients_list = $this->entityManager->getRepository(Client::class)->findBy(array(), array('name' => "ASC"));

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
            'project_data' => $project_data,
            'client' => $client,
            'priority_list' => $priority_list,
            'clients_list' => $clients_list,
            'tools_menu' => [
                'project' => TRUE,
                'view' => FALSE,
                'edit' => TRUE,
          ],
        ];

        $this->render('project/edit.html.twig', $data);
    }

    /**
     * Edit project.
     *
     * @param int $project_id
     * @return void
     */
    public function edit(int $project_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $project = $this->entityManager->find(Project::class, $project_id);

        $project_priority_id = htmlspecialchars($_POST["project_priority_id"]);
        $project_priority = $this->entityManager->find(ProjectPriority::class, $project_priority_id);

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find(Client::class, $client_id);

        $title = htmlspecialchars($_POST["title"]);

        $status_id = htmlspecialchars($_POST["status_id"]);
        $status = $this->entityManager->find(ProjectStatus::class, $status_id);

        // $note = htmlspecialchars($_POST["note"]);

        $project->setPriority($project_priority);
        $project->setClient($client);
        $project->setTitle($title);
        $project->setStatus($status);
        $project->setModifiedAt(new \DateTime("now"));

        $this->entityManager->flush();

        die('<script>location.href = "/project/' . $project_id . '" </script>');
    }

    /**
     * Add project task.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function addTask(int $project_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);

        $project = $this->entityManager->find(Project::class, $project_id);

        $type_id = $_POST["type_id"];
        $type = $this->entityManager->find(ProjectTaskType::class, $type_id);

        $status_id = $_POST["status_id"];
        $status = $this->entityManager->find(ProjectTaskStatus::class, $status_id);

        $title = htmlspecialchars($_POST['title']);

        // Save a new Task.
        $newTask = new ProjectTask();

        $newTask->setProject($project);
        $newTask->setType($type);
        $newTask->setStatus($status);
        $newTask->setTitle($title);
        $newTask->setStartDate(new \DateTime("1970-01-01 00:00:00"));
        $newTask->setEndDate(new \DateTime("1970-01-01 00:00:00"));

        $newTask->setCreatedAt(new \DateTime("now"));
        $newTask->setCreatedByUser($user);
        $newTask->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newTask);
        $this->entityManager->flush();

        // Redirect to view Project page.
        die('<script>location.href = "/project/' . $project_id . '" </script>');
    }

    /**
     * Edit project task form.
     *
     * @param int $project_id
     * @param int $task_id
     *
     * @return void
     */
    public function editTaskForm(int $project_id, int $task_id): void
    {
        $task = $this->entityManager->find(ProjectTask::class, $task_id);
        $project = $this->entityManager->find(Project::class, $project_id);
        $project_data = $this->entityManager->find(Project::class, $project_id);

        $task_data['class'] = match($task->getType()->getId()) {
            1 => 'info',
            2 => 'warning',
            3 => 'secondary',
            4 => 'success',
            5 => 'isporuka',
            6 => 'yellow',
            7 => 'danger',
            8 => 'popravka',

            default => 'default',
        };

        $employees_list = $this->entityManager->getRepository(Employee::class)->findBy([], ['name' => "ASC"]);
        $task_notes = $this->entityManager
            ->getRepository(ProjectTaskNote::class)->findBy(['project_task' => $task_id], []);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'task_id' => $task_id,
            'task' => $task,
            'task_data' => $task_data,
            'project_id' => $project_id,
            'project' => $project,
            'project_data' => $project_data,
            'employees_list' => $employees_list,
            'task_notes' => $task_notes,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('project/editTask.html.twig', $data);
    }

    /**
     * Edit project task.
     *
     * @param int $project_id
     * @param int $task_id
     *
     * @return void
     */
    public function editTask(int $project_id, int $task_id): void
    {
        $alertEnd = NULL;

        $user = $this->entityManager->find(User::class, $this->user_id);
        $project = $this->entityManager->find(Project::class, $project_id);

        $title = htmlspecialchars($_POST["title"]);

        $employee_id = htmlspecialchars($_POST["employee_id"]);
        $employee = $this->entityManager->find(Employee::class, $employee_id);

        $task = $this->entityManager->find(ProjectTask::class, $task_id);

        $start = htmlspecialchars($_POST["start"]);
        $end = htmlspecialchars($_POST["end"]);

        // Check if $start is empty string.
        if ($start <> '') {
            $start = $task->getStartDate()->format('Y-m-d H:i:s');
            // $start = '1970-01-01 00:00:00';
        }
        else {

        }

        // Check if $end is empty string.
        if ($end <> '') {
            $end = $task->getEndDate()->format('Y-m-d H:i:s');
            // $end = '1970-01-01 00:00:00';
        }
        else {

        }

        if ($start == '1970-01-01 00:00:00' AND $end == '1970-01-01 00:00:00') {
            // zadatak je nov i još nije setovan ni start ni end
            $status_id = 1;
            // echo 'zadatak je nov i još nije setovan ni start ni end';
            // exit();
        }
        elseif ($start <> '' AND $start <> '1970-01-01 00:00:00' AND $end == '1970-01-01 00:00:00') {
            // start postoji i ne menja se, a end nije setovan
            // $result_start = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
            //   $row_start = mysqli_fetch_array($result_start);
            //   $start = $row_start['start'];
            $status_id = 2;
            // echo 'start postoji i ne menja se, a end nije setovan';
            // exit();
        }
        elseif (($start == '' OR $start <> '1970-01-01 00:00:00' ) AND $end == '1970-01-01 00:00:00') {
            //start postoji pa je brisan u formi a end nije setovan
            $start = '1970-01-01 00:00:00';
            $status_id = 1;
            // echo 'start postoji pa je brisan u formi a end nije setovan';
            // exit();
        }
        elseif ($start <> '1970-01-01 00:00:00' AND $start <> '' AND $end <> '' AND $start <> '1970-01-01 00:00:00') {
            // start postoji i nemenja se i end postoji i nemenja se
            // $result_start_end = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
            // $row_start_end = mysqli_fetch_array($result_start_end);
            //   $start = $row_start_end['start'];
            //   $end = $row_start_end['end'];
            $status_id = 3;

            // echo 'start je setovan i nemenja se i end je setovan i nemenja se';
            // exit();
        }
        elseif ($start <> '1970-01-01 00:00:00' AND $start <> '' AND $end =='') {
            // start postoji i nemenja se a end postoji pa je brisan u formi
            // $result_start = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
            //   $row_start = mysqli_fetch_array($result_start);
            //   $start = $row_start['start'];
            $end = '1970-01-01 00:00:00';
            $status_id = 2;

            // echo 'start postoji i nemenja se a end postoji pa je brisan u formi';
            // exit();
        }
        elseif ($start == '' AND $end <> '' AND $end <> '1970-01-01 00:00:00') {
            // end postoji i ne menja se a start postoji pa je obrisan u formi
            // $result_start_end = $db->connection->query("SELECT * FROM project_task WHERE id = '$task_id' ") or die(mysqli_error($db->connection));
            //   $row_start_end = mysqli_fetch_array($result_start_end);
            //  $start = $row_start_end['start'];
            //   $end = $row_start_end['end'];
            $status_id = 3;
            // echo 'end postoji i ne menja se a start postoji pa je obrisan u formi';
            // exit();
        }
        elseif ($start == '' AND $end == '') {
            // i start i end su postojali pa su brisani u formi
            $status_id = 1;
        }

        $status = $this->entityManager->find(ProjectTaskStatus::class, $status_id);

        $task->setProject($project);
        $task->setTitle($title);
        $task->setStatus($status);
        $task->setEmployee($employee);
        $task->setStartDate(new \DateTime($start));
        $task->setEndDate(new \DateTime($end));

        $task->setModifiedAt(new \DateTime("now"));

        $task->setModifiedByUser($user);

        $this->entityManager->flush();


        // $db->connection->query("UPDATE project_task SET title='$title', status_id='$status_id', employee_id='$employee_id', start='$start', end='$end'  WHERE id = '$task_id' ") or die(mysqli_error($db->connection));

        die('<script>location.href = "/project/' . $project_id . '" </script>');
    }

    /**
     * Delete project task.
     *
     * @param int $project_id
     * @param int $task_id
     *
     * @return void
     */
    public function deleteTask(int $project_id, int $task_id): void
    {
        $task = $this->entityManager->find(ProjectTask::class, $task_id);

        // First deleting task notes.
        $task_notes = $this->entityManager
            ->getRepository(ProjectTaskNote::class)->findBy(['project_task' => $task_id], ['id' => "ASC"]);

        foreach ($task_notes as $task_note) {
            $task_note = $this->entityManager->find(ProjectTaskNote::class, $task_note->getId());
            $this->entityManager->remove($task_note);
            $this->entityManager->flush();
        }

        // Second deleting task.
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        die('<script>location.href = "/project/' . $project_id . '" </script>');
    }

    /**
     * Set start date for project task.
     *
     * @param int $project_id
     * @param int $task_id
     *
     * @return void
     */
    public function setStartDate(int $project_id, int $task_id): void
    {
        $task = $this->entityManager->find(ProjectTask::class, $task_id);

        $status_id = 2;
        $status = $this->entityManager->find(ProjectTaskStatus::class, $status_id);

        $task->setStartDate(new \DateTime("now"));
        $task->setStatus($status);

        $this->entityManager->flush();

        die('<script>location.href = "/project/' . $project_id . '/task/' . $task_id . '/edit" </script>');
    }

    /**
     * Set end date for project task.
     *
     * @param int $project_id
     * @param int $task_id
     *
     * @return void
     */
    public function setEndDate(int $project_id, int $task_id): void
    {
        $task = $this->entityManager->find(ProjectTask::class, $task_id);
        $start = $task->getStartDate()->format('Y-m-d H:i:s');

        if ($start == '1970-01-01 00:00:00') {
            // $end = '0000-00-00 00:00:00';
            // $status_id = 1;
            die('<script>location.href = "/project/' . $project_id . '/task/' . $task_id . '/edit" </script>');
        }
        $status_id = 3;
        $status = $this->entityManager->find(ProjectTaskStatus::class, $status_id);

        $task->setEndDate(new \DateTime("now"));
        $task->setStatus($status);

        $this->entityManager->flush();

        die('<script>location.href = "/project/' . $project_id . '/task/' . $task_id . '/edit" </script>');
    }

    /**
     * Add project task note.
     *
     * @param int $project_id
     * @param int $task_id
     *
     * @return void
     */
    public function addTaskNote(int $project_id, int $task_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);
        $task = $this->entityManager->find(ProjectTask::class, $task_id);

        $note = htmlspecialchars($_POST['note']);

        // Save a new Task note.
        $newTaskNote = new ProjectTaskNote();

        $newTaskNote->setProjectTask($task);
        $newTaskNote->setNote($note);

        $newTaskNote->setCreatedAt(new \DateTime("now"));
        $newTaskNote->setCreatedByUser($user);
        $newTaskNote->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newTaskNote);
        $this->entityManager->flush();

        // ovde link da vodi na pregled zadatka
        die('<script>location.href = "/project/' .$project_id. '/task/' . $task_id . '/edit" </script>');
    }

    /**
     * Delete project task note.
     *
     * @param int $project_id
     * @param int $task_id
     * @param int $note_id
     *
     * @return void
     */
    public function deleteTaskNote(int $project_id, int $task_id, int $note_id): void
    {
        $task_note = $this->entityManager->find(ProjectTaskNote::class, $note_id);

        $this->entityManager->remove($task_note);
        $this->entityManager->flush();

        die('<script>location.href = "/project/' . $project_id . '/task/' . $task_id . '/edit" </script>');
    }

    /**
     * List of projects by city.
     *
     * @param int $city_id
     *
     * @return void
     */
    public function viewByCity(int $city_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $city = $this->entityManager->find(City::class, $city_id);
        $cities = $this->entityManager->getRepository(Project::class)->getCitiesByActiveProject();

        $alert = NULL;
        $city_name = '';
        if (!$city){
            $alert = 'Traženo mesto ne postoji!';
        }
        else {
            $city_name = $city->getName();
        }

//        $active_projects = $this->entityManager->getRepository(Project::class)->projectTracking('1');

        $active_projects = $this->entityManager->getRepository(Project::class)->projectTrackingByCity('1', $city_id);

        $active_projects_data = [];
        foreach ($active_projects as $active_project) {
            $project_tasks = $this->entityManager->getRepository(Project::class)->projectTasks($active_project->getId());
            $tasks_for_realization = [];
            $tasks_in_realization = [];
            $tasks_completed = [];
            foreach ($project_tasks as $project_task) {
                if ($project_task->getStatus()->getId() == 1){
                    $tasks_for_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),

                    ];
                }

                if ($project_task->getStatus()->getId() == 2){
                    $tasks_in_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }

                if ($project_task->getStatus()->getId() == 3){
                    $tasks_completed[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }
            }
            $active_projects_data[] = [
                'id' => $active_project->getId(),
                'ordinal_num_in_year' => $active_project->getOrdinalNumInYear(),
                'title' => $active_project->getTitle(),
                'client' => $active_project->getClient()->getName(),
                'created_at' => $active_project->getCreatedAt()->format('Y-m-d H:i:s'),
                'city' => $active_project->getClient()->getCity(),
                'tasks_for_realization' => $tasks_for_realization,
                'tasks_in_realization' => $tasks_in_realization,
                'tasks_completed' => $tasks_completed,
            ];
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'city_id' => $city_id,
            'city' => $city,
            'cities' => $cities,
            'city_name' => $city_name,
            'alert' => $alert,
            'active_projects_data' => $active_projects_data,
        ];

        $this->render('project/view_by_city.html.twig', $data);
    }

    /**
     * Add project note.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function addNote(int $project_id): void
    {
        $user = $this->entityManager->find(User::class, $this->user_id);
        $project = $this->entityManager->find(Project::class, $project_id);
        $note = htmlspecialchars($_POST['note']);

        // Save a new Task note.
        $newProjectNote = new ProjectNote();

        $newProjectNote->setNote($note);

        $newProjectNote->setProject($project);
        $newProjectNote->setCreatedAt(new \DateTime("now"));

        $newProjectNote->setCreatedByUser($user);
        $newProjectNote->setModifiedAt(new \DateTime("1970-01-01 00:00:00"));

        $this->entityManager->persist($newProjectNote);
        $this->entityManager->flush();

        // ovde link da vodi na pregled projekta
        die('<script>location.href = "/project/' . $project_id . '" </script>');
    }

    /**
     * Delete project note.
     *
     * @param int $project_id
     * @param int $note_id
     *
     * @return void
     */
    public function deleteNote(int $project_id, int $note_id): void
    {
        $note = $this->entityManager->find(ProjectNote::class, $note_id);

        $this->entityManager->remove($note);
        $this->entityManager->flush();

        die('<script>location.href = "/project/' . $project_id . '" </script>');
    }

    /**
     * Print project task with notes.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function printProjectTaskWithNotes(int $project_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $project = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project->getClient()->getId());
        $notes = $this->entityManager->getRepository(Project::class)->getNotesByProject($project_id);


        $data = [
            'page' => $this->page,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
            'project' => $project,
            'company_info' => $company_info,
            'client' => $client,
            'notes' => $notes,
        ];

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('project/print_project_task_with_notes.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Radni nalog');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF,radni nalog');

        // Remove default header/footer.
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set default monospaced font.
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins.
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Set auto page breaks.
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor.
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set font.
        $pdf->SetFont('dejavusans', '', 10);

        // Add a page.
        $pdf->AddPage();

        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Reset pointer to the last page.
        $pdf->lastPage();

        // Close and output PDF document to browser.
        $pdf->Output('radni_nalog_' .$client['name']. '.pdf', 'I');
    }

    /**
     * Print project task.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function printProjectTask(int $project_id): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $project = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project->getClient()->getId());

        $data = [
            'page' => $this->page,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
            'company_info' => $company_info,
            'project' => $project,
            'client' => $client,
        ];

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('project/print_project_task.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle($company_info['name'] . ' - Radni nalog');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF,radni nalog');

        // Remove default header/footer.
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set default monospaced font.
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins.
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Set auto page breaks.
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor.
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set font.
        $pdf->SetFont('dejavusans', '', 10);

        // Add a page.
        $pdf->AddPage();

        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Reset pointer to the last page.
        $pdf->lastPage();

        // Close and output PDF document to browser.
        $pdf->Output('nalog_' . $client['name'] . '.pdf', 'I');
    }

    /**
     * Print installation record.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function printInstallationRecord(int $project_id):void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $project = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project->getClient()->getId());

        $data = [
            'page' => $this->page,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
            'company_info' => $company_info,
            'client' => $client,
            'project' => $project,
        ];

        // Render HTML content from a Twig template (or similar)
        ob_start();
        $this->render('project/print_installation_record.html.twig', $data);
        $html = ob_get_clean();

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company_info['name']);
        $pdf->SetTitle('Zapisnik o ugradnji (montaži)');
        $pdf->SetSubject($company_info['name']);
        $pdf->SetKeywords($company_info['name'] . ', PDF, zapisnik');

        // Remove default header/footer.
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set default monospaced font.
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins.
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Set auto page breaks.
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor.
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set font.
        $pdf->SetFont('dejavusans', '', 10);

        // Add a page.
        $pdf->AddPage();

        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Reset pointer to the last page.
        $pdf->lastPage();

        // Close and output PDF document to browser.
        $pdf->Output('test_name.pdf', 'I');
    }

    /**
     * Add file to project.
     *
     * @param int $project_id
     *
     * @return void
     */
    public function addFileToProject(int $project_id): void
    {
        if ($_FILES["file"]["error"] > 0) {
            if ($_FILES["file"]["error"] == 4) {
                echo "Molimo izaberite fajl! <br>";
                echo "<a href='/project/" . $project_id . "'>Povratak u projekat</a>";
            }
            elseif ($_FILES["file"]["error"] == 1) {
                echo"Fajl koji ste izabrali je prevelik!";
            }
            else {
                echo "Greška: " . $_FILES["file"]["error"] . "<br>";
            }
        }
        else {
            // Check if exist projects folder.
            if (!is_dir('upload/projects/project_id_'.$project_id)) {
              mkdir('upload/projects/project_id_'.$project_id);
            }

            $path = 'upload/projects/project_id_'.$project_id.'/';

            // Sanitize file name.
            $file_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $_FILES["file"]["name"]);

            if (file_exists($path . $file_name)) {
              // Add date on the end of the file name.
              $file_name = preg_replace('/\.[^.]+$/', '_' . date('Y-m-d_H-i-s') . '$0', $file_name);
            }

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $path . $file_name)) {
                echo "jeah";
            }

            die('<script>location.href = "/project/' . $project_id . '" </script>');
        }
    }

    /**
     * Search for projects.
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

        $project_list = $this->entityManager->getRepository(Project::class)->search($term);
        $active_projects_data = [];
        foreach ($project_list as $project_item) {
            if ($project_item->getStatus()->getId() == 1 || $project_item->getStatus()->getId() == 2){
                $project_tasks = $this->entityManager->getRepository(Project::class)->projectTasks($project_item->getId());
                $tasks_for_realization = [];
                $tasks_in_realization = [];
                $tasks_completed = [];
                foreach ($project_tasks as $project_task) {
                    if ($project_task->getStatus()->getId() == 1){
                        $tasks_for_realization[] = [
                            'id' => $project_task->getId(),
                            'title' => $project_task->getTitle(),
                            'type' => $project_task->getType()->getName(),
                            'class' => $project_task->getType()->getClass(),
                              ];
                    }

                    if ($project_task->getStatus()->getId() == 2){
                        $tasks_in_realization[] = [
                            'id' => $project_task->getId(),
                            'title' => $project_task->getTitle(),
                            'type' => $project_task->getType()->getName(),
                            'class' => $project_task->getType()->getClass(),
                        ];
                    }

                    if ($project_task->getStatus()->getId() == 3){
                        $tasks_completed[] = [
                            'id' => $project_task->getId(),
                            'title' => $project_task->getTitle(),
                            'type' => $project_task->getType()->getName(),
                            'class' => $project_task->getType()->getClass(),
                        ];
                    }
                }

                $active_projects_data[] = [
                    'id' => $project_item->getId(),
                    'ordinal_num_in_year' => $project_item->getOrdinalNumInYear(),
                    'title' => $project_item->getTitle(),
                    'client' => $project_item->getClient()->getName(),
                    'created_at' => $project_item->getCreatedAt()->format('d M Y'),
                    'city' => $project_item->getClient()->getCity(),
                    'tasks_for_realization' => $tasks_for_realization,
                    'tasks_in_realization' => $tasks_in_realization,
                    'tasks_completed' => $tasks_completed,
                ];
            }
        }

        $inactive_projects_data = [];
        foreach ($project_list as $project_item) {
            if ($project_item->getStatus()->getId() == 3){
                $project_tasks = $this->entityManager->getRepository(Project::class)->projectTasks($project_item->getId());
                $tasks_for_realization = [];
                $tasks_in_realization = [];
                $tasks_completed = [];
                foreach ($project_tasks as $project_task) {
                    if ($project_task->getStatus()->getId() == 1){
                        $tasks_for_realization[] = [
                            'id' => $project_task->getId(),
                            'title' => $project_task->getTitle(),
                            'type' => $project_task->getType()->getName(),
                            'class' => $project_task->getType()->getClass(),
                        ];
                    }

                    if ($project_task->getStatus()->getId() == 2){
                        $tasks_in_realization[] = [
                            'id' => $project_task->getId(),
                            'title' => $project_task->getTitle(),
                            'type' => $project_task->getType()->getName(),
                            'class' => $project_task->getType()->getClass(),
                        ];
                    }

                    if ($project_task->getStatus()->getId() == 3){
                        $tasks_completed[] = [
                            'id' => $project_task->getId(),
                            'title' => $project_task->getTitle(),
                            'type' => $project_task->getType()->getName(),
                            'class' => $project_task->getType()->getClass(),
                        ];
                    }
                }

                $inactive_projects_data[] = [
                    'id' => $project_item->getId(),
                    'ordinal_num_in_year' => $project_item->getOrdinalNumInYear(),
                    'title' => $project_item->getTitle(),
                    'client' => $project_item->getClient()->getName(),
                    'created_at' => $project_item->getCreatedAt()->format('d M Y'),
                    'city' => $project_item->getClient()->getCity(),
                    'tasks_for_realization' => $tasks_for_realization,
                    'tasks_in_realization' => $tasks_in_realization,
                    'tasks_completed' => $tasks_completed,
                ];
            }
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'active_projects_data' => $active_projects_data,
            'inactive_projects_data' => $inactive_projects_data,
        ];

        $this->render('project/search.html.twig', $data);
    }

    /**
     * Advanced project search.
     *
     * @return void
     */
    public function advancedSearch(): void
    {
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $client = $_POST["client"] ?? '';
        $project_title = $_POST["project_title"] ?? '';
        $city = $_POST["city"] ?? '';

        $project_advanced_search_list = $this->entityManager
            ->getRepository(Project::class)->advancedSearch($client, $project_title, $city);

        $project_advanced_search_list_data = [];
        foreach ($project_advanced_search_list as $project_item) {
            $project_tasks = $this->entityManager->getRepository(Project::class)->projectTasks($project_item->getId());
            $tasks_for_realization = [];
            $tasks_in_realization = [];
            $tasks_completed = [];
            foreach ($project_tasks as $project_task) {
                if ($project_task->getStatus()->getId() == 1){
                    $tasks_for_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }

                if ($project_task->getStatus()->getId() == 2){
                    $tasks_in_realization[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }

                if ($project_task->getStatus()->getId() == 3){
                    $tasks_completed[] = [
                        'id' => $project_task->getId(),
                        'title' => $project_task->getTitle(),
                        'type' => $project_task->getType()->getName(),
                        'class' => $project_task->getType()->getClass(),
                    ];
                }
            }

            $project_advanced_search_list_data[] = [
                'id' => $project_item->getId(),
                'ordinal_num_in_year' => $project_item->getOrdinalNumInYear(),
                'title' => $project_item->getTitle(),
                'client' => $project_item->getClient()->getName(),
                'created_at' => $project_item->getCreatedAt()->format('d M Y'),
                'city' => $project_item->getClient()->getCity(),
                'tasks_for_realization' => $tasks_for_realization,
                'tasks_in_realization' => $tasks_in_realization,
                'tasks_completed' => $tasks_completed,
                'status_id' => $project_item->getStatus()->getId(),
            ];
        }

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
//            'project_advanced_search_list' =>$project_advanced_search_list,
            'project_advanced_search_list_data' => $project_advanced_search_list_data,
        ];

        $this->render('project/advanced_search.html.twig', $data);
    }

}
