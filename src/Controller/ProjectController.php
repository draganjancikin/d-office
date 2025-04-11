<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Entity\Project;
use App\Entity\ProjectNote;
use App\Entity\ProjectTask;
use App\Entity\ProjectTaskNote;

/**
 * ProjectController class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class ProjectController extends BaseController
{

    private $page = 'project';
    private $page_title = 'Projekti';
    private $stylesheet = '/../libraries/';

    /**
     * ArticleController constructor.
     */
    public function __construct() {
        parent::__construct();
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
        $cities = $this->entityManager->getRepository('\App\Entity\Project')->getCitiesByActiveProject();

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'search' => $search,
            'cities' => $cities,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('index', $data);
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
        $clients_list = $this->entityManager->getRepository('\App\Entity\Client')->findBy(array(), array('name' => "ASC"));

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'clients_list' => $clients_list,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('add', $data);
    }

    /**
     * Add project.
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function add() {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $ordinal_num_in_year = 0;

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find("\App\Entity\Client", $client_id);

        $title = htmlspecialchars($_POST['title']);

        $project_priority_id = htmlspecialchars($_POST['project_priority_id']);
        $project_priority = $this->entityManager->find("\App\Entity\ProjectPriority", $project_priority_id);

        // $note = htmlspecialchars($_POST['note']);
        $note = "";

        $project_status = $this->entityManager->find("\App\Entity\ProjectStatus", 1);

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
        $this->entityManager->getRepository('App\Entity\Project')->setOrdinalNumInYear($new_project_id);

        if (isset($_POST['acc_doc_id'])) {
            $acc_doc_id = $_POST['acc_doc_id'];


            // Insert Project and AccountingDocument to table v6__projects__accounting_documents.
            // @HOLMES - Dragan: Find better way to connect to db.
            $conn = \Doctrine\DBAL\DriverManager::getConnection([
                'dbname' => DB_NAME,
                'user' => DB_USERNAME,
                'password' => DB_PASSWORD,
                'host' => DB_SERVER,
                'driver' => 'mysqli',
            ]);
            $queryBuilder = $conn->createQueryBuilder();

            $queryBuilder
                ->insert('v6__projects__accounting_documents')
                ->values([
                    'project_id' => ':project_id',
                    'accountingdocument_id' => ':accountingdocument_id'
                ])
                ->setParameters([
                    'project_id' => $new_project_id,
                    'accountingdocument_id' => $acc_doc_id,
                ]);

            $queryBuilder->execute();
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
        $project_data = $this->entityManager->find('\App\Entity\Project', $project_id);
        $notes = $this->entityManager->getRepository('\App\Entity\Project')->getNotesByProject($project_id);

        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
            'project_data' => $project_data,
            'notes' => $notes,
        ];
        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('view', $data);
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
        $project_data = $this->entityManager->find('\App\Entity\Project', $project_id);
        $client = $this->entityManager->getRepository('\App\Entity\Client')->getClientData($project_data->getClient()->getId());
        $priority_list = $this->entityManager->getRepository('\App\Entity\ProjectPriority')->findBy(array(), array('id' => "ASC"));
        $clients_list = $this->entityManager->getRepository('\App\Entity\Client')->findBy(array(), array('name' => "ASC"));

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
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('edit', $data);
    }

    /**
     * Edit project.
     *
     * @param int $project_id
     * @return void
     */
    public function edit(int $project_id): void
    {
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $project = $this->entityManager->find("\App\Entity\Project", $project_id);

        $project_priority_id = htmlspecialchars($_POST["project_priority_id"]);
        $project_priority = $this->entityManager->find("\App\Entity\ProjectPriority", $project_priority_id);

        $client_id = htmlspecialchars($_POST["client_id"]);
        $client = $this->entityManager->find("\App\Entity\Client", $client_id);

        $title = htmlspecialchars($_POST["title"]);

        $status_id = htmlspecialchars($_POST["status_id"]);
        $status = $this->entityManager->find("\App\Entity\ProjectStatus", $status_id);

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
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);

        $project = $this->entityManager->find("\App\Entity\Project", $project_id);

        $type_id = $_POST["type_id"];
        $type = $this->entityManager->find("\App\Entity\ProjectTaskType", $type_id);

        $status_id = $_POST["status_id"];
        $status = $this->entityManager->find("\App\Entity\ProjectTaskStatus", $status_id);

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
        $task = $this->entityManager->find("\App\Entity\ProjectTask", $task_id);
        $project = $this->entityManager->find("\App\Entity\Project", $project_id);
        $project_data = $this->entityManager->find('\App\Entity\Project', $project_id);

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
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('editTask', $data);
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
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);
        $project = $this->entityManager->find("\App\Entity\Project", $project_id);

        $title = htmlspecialchars($_POST["title"]);

        $employee_id = htmlspecialchars($_POST["employee_id"]);
        $employee = $this->entityManager->find("\App\Entity\Employee", $employee_id);

        $task = $this->entityManager->find("\App\Entity\ProjectTask", $task_id);

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

        $status = $this->entityManager->find("\App\Entity\ProjectTaskStatus", $status_id);

        $task->setProject($project);
        $task->setTitle($title);
        $task->setStatus($status);
        $task->setEmployee($employee);
        $task->setStartDate(new \DateTime($start));
        $task->setEndDate(new \DateTime($end));

        $task->setModifiedAt(new \DateTime("now"));

        $task->setModifiedByUser($user);

        // echo "editing in progress ...";
        // exit();
        // --------------------------------------------------------------------------

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
        $task = $this->entityManager->find("\App\Entity\ProjectTask", $task_id);

        // First deleting task notes.
        $task_notes = $this->entityManager->getRepository('\App\Entity\ProjectTaskNote')->findBy(array('project_task' => $task_id), array('id' => "ASC"));
        foreach ($task_notes as $task_note) {
            $task_note = $this->entityManager->find("\App\Entity\ProjectTaskNote", $task_note->getId());
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
        $task = $this->entityManager->find("\App\Entity\ProjectTask", $task_id);

        $status_id = 2;
        $status = $this->entityManager->find("\App\Entity\ProjectTaskStatus", $status_id);

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
        $task = $this->entityManager->find("\App\Entity\ProjectTask", $task_id);
        $start = $task->getStartDate()->format('Y-m-d H:i:s');

        if ($start == '1970-01-01 00:00:00') {
            // $end = '0000-00-00 00:00:00';
            // $status_id = 1;
            die('<script>location.href = "/project/' . $project_id . '/task/' . $task_id . '/edit" </script>');
        }
        $status_id = 3;
        $status = $this->entityManager->find("\App\Entity\ProjectTaskStatus", $status_id);

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
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);
        $task = $this->entityManager->find("\App\Entity\ProjectTask", $task_id);

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
        $task_note = $this->entityManager->find("\App\Entity\ProjectTaskNote", $note_id);

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
        $city = $this->entityManager->find("\App\Entity\City", $city_id);
        $cities = $this->entityManager->getRepository('\App\Entity\Project')->getCitiesByActiveProject();

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
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('viewByCity', $data);
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
        $user = $this->entityManager->find("\App\Entity\User", $this->user_id);
        $project = $this->entityManager->find("\App\Entity\Project", $project_id);
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
        $note = $this->entityManager->find("\App\Entity\ProjectNote", $note_id);

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
        $data = [
            'page' => $this->page,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printProjectTaskWithNotes', $data);
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
        $data = [
            'page' => $this->page,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printProjectTask', $data);
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
        $data = [
            'page' => $this->page,
            'entityManager' => $this->entityManager,
            'project_id' => $project_id,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('printInstallationRecord', $data);
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
     * Advanced project search.
     *
     * @return void
     */
    public function advancedSearch(): void
    {
        $data = [
            'page' => $this->page,
            'page_title' => $this->page_title,
            'stylesheet' => $this->stylesheet,
            'username' => $this->username,
            'user_role_id' => $this->user_role_id,
            'entityManager' => $this->entityManager,
        ];

        // If the user is not logged in, redirect them to the login page.
        $this->isUserNotLoggedIn();

        $this->render('advancedSearch', $data);
    }

    /**
     * A helper method to render views.
     *
     * @param $view
     * @param array $data
     *
     * @return void
     */
    private function render($view, array $data = []): void
    {
        // Extract data array to variables.
        extract($data);
        // Include the view file.
        require_once __DIR__ . "/../Views/$page/$view.php";
    }

}
