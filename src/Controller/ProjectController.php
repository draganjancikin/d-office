<?php

namespace App\Controller;

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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TCPDF;

/**
 * ProjectController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class ProjectController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private string $page;
    private string $page_title;
    protected string $stylesheet;
    protected string $app_version;

    /**
     * ProjectController constructor.
     *
     * Initializes controller properties and loads the application version.
     *
     * @param EntityManagerInterface $entityManager
     *   The Doctrine entity manager for database operations.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->page = 'project';
        $this->page_title = 'Projekti';
        $this->stylesheet = $_ENV['STYLESHEET_PATH'] ?? getenv('STYLESHEET_PATH') ?? '/libraries/';
        $this->app_version = $this->loadAppVersion();
    }

    /**
     * Displays the list of active and inactive projects with their associated tasks.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves cities with active projects.
     * - Fetches active and inactive projects, and for each project, gathers tasks grouped by status:
     *   - For realization
     *   - In realization
     *   - Completed
     * - Passes all relevant data to the Twig template for rendering.
     *
     * @return Response
     *   The HTTP response with the rendered project index page.
     */
    #[Route('/projects/', name: 'project_index')]
    public function index(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

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
            'cities' => $cities,
            'tools_menu' => [
                'project' => FALSE,
            ],
            'active_projects_data' => $active_projects_data,
            'inactive_projects_data' => $inactive_projects_data,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('project/index.html.twig', $data);
    }

    /**
     * Displays the form for creating a new project.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves the list of clients for selection in the form.
     * - Optionally pre-fills the form with accounting document or client data if provided via query parameters.
     * - Passes all relevant data to the Twig template for rendering the new project form.
     *
     * @param Request $request
     *   The HTTP request object.
     * @return Response
     *   The HTTP response with the rendered new project form.
     */
    #[Route('/projects/new', name: 'project_new')]
    public function new(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $clients_list = $this->entityManager->getRepository(Client::class)->findBy([], ['name' => "ASC"]);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'clients_list' => $clients_list,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'project' => FALSE,
            ],
            'acc_doc_id' => NULL,
            'client' => NULL,
            'app_version' => $this->app_version,
        ];

        $acc_doc_id = $request->query->get('acc_doc_id');
        if ($acc_doc_id) {
            $data['acc_doc_id'] = $acc_doc_id;
        }

        $client_id = $request->query->get('client_id');
        if ($client_id) {
            $data['client'] = $this->entityManager->find(Client::class, $client_id);
        }

        return $this->render('project/project_new.html.twig', $data);
    }

    /**
     * Handles the creation of a new project from POST data.
     *
     * - Requires user authentication (session check).
     * - Retrieves and sanitizes form data from the POST request.
     * - Creates and persists a new Project entity with the provided data.
     * - Optionally links the project to an accounting document if provided.
     * - Sets the ordinal number in year for the new project.
     * - Redirects to the project view page after successful creation.
     *
     * @return Response
     *   Redirects to the project view page for the newly created project.
     *
     * @throws \Doctrine\DBAL\Exception
     *   If a database error occurs during project creation.
     */
    #[Route('/projects/create', name: 'project_create', methods: ['POST'])]
    public function create(): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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

        return $this->redirectToRoute('project_view', ['project_id' => $new_project_id]);
    }

    /**
     * Displays detailed information for a specific project.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves the project, client, orders, accounting documents, and notes.
     * - Gathers and groups project tasks by status (for realization, in realization, completed).
     * - Collects files associated with the project from the upload directory.
     * - Passes all relevant data to the Twig template for rendering the project view page.
     *
     * @param int $project_id
     *   The ID of the project to display.
     *
     * @return Response
     *   The HTTP response with the rendered project view page.
     */
    #[Route('/projects/{project_id}', name: 'project_view', requirements: ['project_id' => '\d+'], methods: ['GET'])]
    public function show(int $project_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

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
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('project/project_view.html.twig', $data);
    }

    /**
     * Displays detailed information for a specific project.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves the project, client, orders, accounting documents, and notes.
     * - Gathers and groups project tasks by status (for realization, in realization, completed).
     * - Collects files associated with the project from the upload directory.
     * - Passes all relevant data to the Twig template for rendering the project view page.
     *
     * @param int $project_id
     *   The ID of the project to display.
     * @return Response
     *   The HTTP response with the rendered project view page.
     */
    #[Route('/projects/{project_id}/edit', name: 'project_edit_form')]
    public function edit(int $project_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $project_data = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project_data->getClient()->getId());
        $priority_list = $this->entityManager->getRepository(ProjectPriority::class)->findBy(array(), array('id' => "ASC"));
        $clients_list = $this->entityManager->getRepository(Client::class)->findBy(array(), array('name' => "ASC"));

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
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
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'app_version' => $this->app_version,
        ];

        return $this->render('project/project_edit.html.twig', $data);
    }

    /**
     * Updates an existing project's details from POST data.
     *
     * - Requires user authentication (session check).
     * - Retrieves and sanitizes form data from the POST request.
     * - Updates the Project entity with new client, title, priority, and status.
     * - Persists changes to the database.
     * - Redirects to the project view page after successful update.
     *
     * @param int $project_id
     *   The ID of the project to update.
     * @return Response
     *   Redirects to the project view page for the updated project.
     */
    #[Route('/projects/{project_id}/update', name: 'project_update', methods: ['POST'])]
    public function update(int $project_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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

        return $this->redirectToRoute('project_view', ['project_id' => $project_id], );
    }

    /**
     * Handles the creation of a new task for a specific project from POST data.
     *
     * - Requires user authentication (session check).
     * - Retrieves and sanitizes form data from the POST request.
     * - Creates and persists a new ProjectTask entity with the provided data.
     * - Sets default start and end dates for the new task.
     * - Redirects to the project view page after successful task creation.
     *
     * @param int $project_id
     *   The ID of the project to which the task will be added.
     * @return Response
     *   Redirects to the project view page for the associated project.
     */
    #[Route('/projects/{project_id}/add-task', name: 'project_task_add', methods: ['POST'])]
    public function createTask(int $project_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);

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

        return $this->redirectToRoute('project_view', ['project_id' => $project_id], );
    }

    /**
     * Edit project task form.
     *
     * @param int $project_id
     * @param int $task_id
     *
     * @return Response
     */
    #[Route('/projects/{project_id}/tasks/{task_id}/edit', name: 'project_task_edit_form')]
    public function editTask(int $project_id, int $task_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

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
            'task_id' => $task_id,
            'task' => $task,
            'task_data' => $task_data,
            'project_id' => $project_id,
            'project' => $project,
            'project_data' => $project_data,
            'employees_list' => $employees_list,
            'task_notes' => $task_notes,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'project' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('project/task_edit.html.twig', $data);
    }

    /**
     * Updates an existing project task with new data from POST request.
     *
     * - Requires user authentication (session check).
     * - Retrieves and sanitizes form data for task title, employee, start, and end dates.
     * - Determines the task status based on the presence and values of start and end dates.
     * - Updates the ProjectTask entity and persists changes to the database.
     * - Redirects to the project view page after successful update.
     *
     * @param int $project_id
     *   The ID of the project containing the task.
     * @param int $task_id
     *   The ID of the task to update.
     * @return Response
     *   Redirects to the project view page for the associated project.
     */
    #[Route('/projects/{project_id}/tasks/{task_id}/update', name: 'project_task_update', methods: ['POST'])]
    public function updateTask(int $project_id, int $task_id): Response
    {
        $alertEnd = NULL;
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);
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

        return $this->redirectToRoute('project_view', ['project_id' => $project_id], );
    }

    /**
     * Deletes a specific task from a project, including all associated task notes.
     *
     * - Removes all notes linked to the specified task.
     * - Deletes the task entity from the database.
     * - Redirects to the project view page after successful deletion.
     *
     * @param int $project_id
     *   The ID of the project containing the task.
     * @param int $task_id
     *   The ID of the task to delete.
     * @return Response
     *   Redirects to the project view page for the associated project.
     */
    #[Route('/projects/{project_id}/tasks/{task_id}/delete', name: 'project_task_delete')]
    public function deleteTask(int $project_id, int $task_id): Response
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

        return $this->redirectToRoute('project_view', ['project_id' => $project_id], );
    }

    /**
     * Sets the start date of a specific project task to the current date and updates its status.
     *
     * - Retrieves the specified task and updates its start date to now.
     * - Sets the task status to "in realization" (status ID 2).
     * - Persists changes to the database.
     * - Redirects to the task edit form after updating.
     *
     * @param int $project_id
     *   The ID of the project containing the task.
     * @param int $task_id
     *   The ID of the task to update.
     * @return Response
     *   Redirects to the task edit form for the updated task.
     */
    #[Route('/projects/{project_id}/tasks/{task_id}/set-start-date', name: 'project_task_set_start_date')]
    public function setStartDate(int $project_id, int $task_id): Response
    {
        $task = $this->entityManager->find(ProjectTask::class, $task_id);

        $status_id = 2;
        $status = $this->entityManager->find(ProjectTaskStatus::class, $status_id);

        $task->setStartDate(new \DateTime("now"));
        $task->setStatus($status);

        $this->entityManager->flush();

        return $this->redirectToRoute('project_task_edit_form', ['project_id' => $project_id, 'task_id' => $task_id] );
    }

    /**
     * Sets the end date of a specific project task to the current date and updates its status.
     *
     * - Retrieves the specified task and checks if the start date is set.
     * - If the start date is not set, redirects back to the task edit form.
     * - Sets the end date to now and updates the task status to "completed" (status ID 3).
     * - Persists changes to the database.
     * - Redirects to the task edit form after updating.
     *
     * @param int $project_id
     *   The ID of the project containing the task.
     * @param int $task_id
     *   The ID of the task to update.
     * @return Response
     *   Redirects to the task edit form for the updated task.
     */
    #[Route('/projects/{project_id}/tasks/{task_id}/set-end-date', name: 'project_task_set_end_date')]
    public function setEndDate(int $project_id, int $task_id): Response
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

        return $this->redirectToRoute('project_task_edit_form', ['project_id' => $project_id, 'task_id' => $task_id] );
    }

    /**
     * Adds a new note to a specific project task from POST data.
     *
     * - Requires user authentication (session check).
     * - Retrieves and sanitizes the note content from the POST request.
     * - Creates and persists a new ProjectTaskNote entity linked to the specified task.
     * - Sets creation and modification timestamps for the note.
     * - Redirects to the task edit form after successful note creation.
     *
     * @param int $project_id
     *   The ID of the project containing the task.
     * @param int $task_id
     *   The ID of the task to which the note will be added.
     * @return Response
     *   Redirects to the task edit form for the updated task.
     */
    #[Route('/projects/{project_id}/tasks/{task_id}/add-note', name: 'project_task_add_note', methods: ['POST'])]
    public function addTaskNote(int $project_id, int $task_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);
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
        return $this->redirectToRoute('project_task_edit_form', ['project_id' => $project_id, 'task_id' => $task_id] );
    }

    /**
     * Deletes a specific note from a project task.
     *
     * - Removes the specified ProjectTaskNote entity from the database.
     * - Redirects to the task edit form after successful deletion.
     *
     * @param int $project_id
     *   The ID of the project containing the task.
     * @param int $task_id
     *   The ID of the task containing the note.
     * @param int $note_id
     *   The ID of the note to delete.
     * @return Response
     *   Redirects to the task edit form for the updated task.
     */
    #[Route('/projects/{project_id}/tasks/{task_id}/notes/{note_id}/delete', name: 'project_task_delete_note')]
    public function deleteTaskNote(int $project_id, int $task_id, int $note_id): Response
    {
        $task_note = $this->entityManager->find(ProjectTaskNote::class, $note_id);

        $this->entityManager->remove($task_note);
        $this->entityManager->flush();

        return $this->redirectToRoute('project_task_edit_form', ['project_id' => $project_id, 'task_id' => $task_id] );
    }

    /**
     * Displays a list of projects filtered by city.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves the selected city and all cities with active projects.
     * - Fetches active projects for the specified city and groups their tasks by status:
     *   - For realization
     *   - In realization
     *   - Completed
     * - Passes all relevant data to the Twig template for rendering.
     *
     * @param Request $request
     *   The HTTP request object containing the city filter.
     * @return Response
     *   The HTTP response with the rendered projects-by-city page.
     */
    #[Route('/projects/by-city', name: 'projects_by_city_view')]
    public function projectByCityView(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $city_id = $request->query->get('city_id');
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
            'city_id' => $city_id,
            'city' => $city,
            'cities' => $cities,
            'city_name' => $city_name,
            'alert' => $alert,
            'active_projects_data' => $active_projects_data,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'project' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('project/projects_by_city.html.twig', $data);
    }

    /**
     * Adds a new note to a specific project from POST data.
     *
     * - Requires user authentication (session check).
     * - Retrieves and sanitizes the note content from the POST request.
     * - Creates and persists a new ProjectNote entity linked to the specified project.
     * - Sets creation and modification timestamps for the note.
     * - Redirects to the project view page after successful note creation.
     *
     * @param int $project_id
     *   The ID of the project to which the note will be added.
     * @return Response
     *   Redirects to the project view page for the updated project.
     */
    #[Route('/projects/{project_id}/notes/create', name: 'project_add_note', methods: ['POST'])]
    public function addNote(int $project_id): Response
    {
        session_start();
        $user = $this->entityManager->find(User::class, $_SESSION['user_id']);
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

        return $this->redirectToRoute('project_view', ['project_id' => $project_id]);
    }

    /**
     * Deletes a specific note from a project.
     *
     * - Removes the specified ProjectNote entity from the database.
     * - Redirects to the project view page after successful deletion.
     *
     * @param int $project_id
     *   The ID of the project containing the note.
     * @param int $note_id
     *   The ID of the note to delete.
     * @return Response
     *   Redirects to the project view page for the updated project.
     */
    #[Route('/projects/{project_id}/notes/{note_id}/delete', name: 'project_delete_note')]
    public function deleteNote(int $project_id, int $note_id): Response
    {
        $note = $this->entityManager->find(ProjectNote::class, $note_id);

        $this->entityManager->remove($note);
        $this->entityManager->flush();

        return $this->redirectToRoute('project_view', ['project_id' => $project_id]);
    }

    /**
     * Generates and returns a PDF document of a project with its notes.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves company info, project, client, and project notes.
     * - Renders the project and notes into an HTML template.
     * - Uses TCPDF to generate a PDF from the rendered HTML.
     * - Returns the PDF as an inline HTTP response.
     *
     * @param int $project_id
     *   The ID of the project to print.
     * @return Response
     *   The HTTP response containing the generated PDF document.
     */
    #[Route('/projects/{project_id}/print-project-with-notes', name: 'project_print_with_notes')]
    public function printProjectTaskWithNotes(int $project_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $project = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project->getClient()->getId());
        $notes = $this->entityManager->getRepository(Project::class)->getNotesByProject($project_id);


        $data = [
            'page' => $this->page,
            'project_id' => $project_id,
            'project' => $project,
            'company_info' => $company_info,
            'client' => $client,
            'notes' => $notes,
        ];

        // Render HTML content from a Twig template (or similar)
        $html = $this->renderView('project/print_project_task_with_notes.html.twig', $data);

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

        // Output PDF document to browser as a Symfony Response
        $filename = 'radni_nalog_' . $client['name'] .'.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        // Remove leading __ from filename for the response
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Generates and returns a PDF document for a specific project.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves company info, project, and client data.
     * - Renders the project details into an HTML template.
     * - Uses TCPDF to generate a PDF from the rendered HTML.
     * - Returns the PDF as an inline HTTP response.
     *
     * @param int $project_id
     *   The ID of the project to print.
     * @return Response
     *   The HTTP response containing the generated PDF document.
     */
    #[Route('/projects/{project_id}/print-project', name: 'project_print_task')]
    public function printProjectTask(int $project_id): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $project = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project->getClient()->getId());

        $data = [
            'page' => $this->page,
            'project_id' => $project_id,
            'company_info' => $company_info,
            'project' => $project,
            'client' => $client,
        ];

        // Render HTML content from a Twig template (or similar)
        $html = $this->renderView('project/print_project_task.html.twig', $data);

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

        // Output PDF document to browser as a Symfony Response
        $filename = 'nalog_' . $client['name'] .'.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        // Remove leading __ from filename for the response
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;
    }

    /**
     * Generates and returns a PDF installation record for a specific project.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves company info, project, and client data.
     * - Renders the installation record into an HTML template.
     * - Uses TCPDF to generate a PDF from the rendered HTML.
     * - Returns the PDF as an inline HTTP response.
     *
     * @param int $project_id
     *   The ID of the project for which to generate the installation record.
     * @return Response
     *   The HTTP response containing the generated PDF document.
     */
    #[Route('/projects/{project_id}/print-installation-record', name: 'project_print_installation_record')]
    public function printInstallationRecord(int $project_id):Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $company_info = $this->entityManager->getRepository(CompanyInfo::class)->getCompanyInfoData(1);
        $project = $this->entityManager->find(Project::class, $project_id);
        $client = $this->entityManager->getRepository(Client::class)->getClientData($project->getClient()->getId());

        $data = [
            'page' => $this->page,
            'project_id' => $project_id,
            'company_info' => $company_info,
            'client' => $client,
            'project' => $project,
        ];

        // Render HTML content from a Twig template (or similar).
        $html = $this->renderView('project/print_installation_record.html.twig', $data);

        require_once '../config/packages/tcpdf_include.php';

        // Create a new TCPDF object / PDF document.
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information.
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

        // Write HTML content.
        $pdf->writeHTML($html, true, false, true, false, '');

        // Reset pointer to the last page.
        $pdf->lastPage();

        // Output PDF document to browser as a Symfony Response.
        $filename = 'nalog_' . $client['name'] .'.pdf';
        $pdfContent = $pdf->Output($filename, 'S');
        // Remove leading __ from filename for the response
        $cleanFilename = ltrim($filename, '_');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $cleanFilename . '"');
        return $response;

    }

    /**
     * Handles file upload and attaches the file to a specific project.
     *
     * - Validates the uploaded file and handles upload errors.
     * - Creates the project upload directory if it does not exist.
     * - Sanitizes the file name and moves the uploaded file to the project directory.
     * - Displays error messages for upload issues or file conflicts.
     * - Redirects to the project view page after successful upload.
     *
     * @param int $project_id
     *   The ID of the project to which the file will be attached.
     * @return Response
     *   Redirects to the project view page for the associated project.
     */
    #[Route('/projects/{project_id}/add-file', name: 'project_add_file', methods: ['POST'])]
    public function addFileToProject(int $project_id): Response
    {
        if ($_FILES["file"]["error"] > 0) {
            if ($_FILES["file"]["error"] == 4) {
                echo "Molimo izaberite fajl! <br>";
                echo "<a href='/projects/" . $project_id . "'>Povratak u projekat</a>";
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

            return $this->redirectToRoute('project_view', ['project_id' => $project_id]);
        }
    }

    /**
     * Searches for projects based on a search term and displays the results.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves the search term from the query parameters.
     * - Searches for projects matching the term.
     * - Groups found projects into active and inactive based on their status.
     * - Gathers and groups tasks for each project by status (for realization, in realization, completed).
     * - Passes the search results and relevant data to the Twig template for rendering.
     *
     * @param Request $request
     *   The HTTP request object containing the search term.
     * @return Response
     *   The HTTP response with the rendered search results page.
     */
    #[Route('/projects/search', name: 'project_search')]
    public function search(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

        $term = $request->query->get('term');
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
            'active_projects_data' => $active_projects_data,
            'inactive_projects_data' => $inactive_projects_data,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'project' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('project/search.html.twig', $data);
    }

    /**
     * Displays the advanced project search form and handles search queries.
     *
     * - Redirects to login if the user is not authenticated.
     * - Retrieves search parameters (client, project title, city) from POST data.
     * - Performs an advanced search for projects based on the provided criteria.
     * - Passes the search results and relevant data to the Twig template for rendering.
     *
     * @return Response
     *   The HTTP response with the rendered advanced search results page.
     */
    #[Route('/projects/advanced-search', name: 'project_advanced_search')]
    public function advancedSearch(): Response
    {
        session_start();
        if (!isset($_SESSION['username'])) {
            return $this->redirectToRoute('login_form');
        }

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
            'project_advanced_search_list_data' => $project_advanced_search_list_data,
            'stylesheet' => $this->stylesheet,
            'user_role_id' => $_SESSION['user_role_id'],
            'username' => $_SESSION['username'],
            'tools_menu' => [
                'project' => FALSE,
            ],
            'app_version' => $this->app_version,
        ];

        return $this->render('project/advanced_search.html.twig', $data);
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
