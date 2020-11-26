<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Description of Project class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class Project extends DB {

    protected $id;
    protected $date;
    protected $pr_id;
    protected $created_at_user_id;
    protected $client_id;
    protected $title;
    protected $priority_id;
    protected $note;
    protected $status;


    // metoda koja daje naziv naselja klijenta $client_id
    public function getCityName($client_id){

        $result = $this->connection->query("SELECT client.city_id, city.name "
                                            . "FROM client "
                                            . "JOIN (city)"
                                            . "ON (client.city_id = city.id )"
                                            . "WHERE client.id = $client_id ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();

        return $row['name'];
    }


    // metoda koja definiše i dodeljuje vrednost pr_id 
    public function setPrId(){

        // čitamo iz baze, iz tabele project sve zapise 
        $result = $this->connection->query("SELECT * FROM project ORDER BY id DESC") or die(mysqli_error($this->connection));

        // brojimo koliko ima zapisa
        $num = mysqli_num_rows($result); // broj kolona u tabeli $table

        $row = $result->fetch_assoc();
        $last_id = $row['id'];
        $year_last = date('Y', strtotime($row['date']));

        $row = $result->fetch_assoc();
        $year_before_last = date('Y', strtotime($row['date']));

        $pr_id_before_last = $row['pr_id'];

        if($num ==0){  // prvi slučaj kada je tabela $table prazna

            return die("Tabela project je prazna!");

        }elseif($num ==1){  // drugi slučaj - kada postoji jedan unos u tabeli $table

            $pr_id = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'

        }else{  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table

            if($year_last < $year_before_last){
                return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");
            }elseif($year_last == $year_before_last){ //nema promene godine
                $pr_id = $pr_id_before_last + 1;
            }else{  // došlo je do promene godine
                $pr_id = 1;
            }

        }

        $this->connection->query("UPDATE project SET pr_id = '$pr_id' WHERE id = '$last_id' ") or die(mysqli_error($this->connection));
    }


    // metoda koja daje podatke o projektu
    public function getProject($project_id) {

        $result = $this->get("SELECT project.id, project.pr_id, project.date, project.client_id, project.title, project.priority_id, project.status, project.note, project.created_at_user_id, client.name as client_name "
                            . "FROM project "
                            . "JOIN (client) "
                            . "ON (project.client_id = client.id ) "
                            . "WHERE project.id = $project_id ");
        if( !empty($result) ){
            $row = $result[0];
            $id = $row['id'];

            $created_at_user_id = $row['created_at_user_id'];
            $result_user_created = $this->get("SELECT admin.username "
                                                  . "FROM admin "
                                                  . "WHERE admin.id = $created_at_user_id ");
            $row_user_created = $result_user_created[0];
            $project = array(
                'id' => $id,
                'pr_id' => $row['pr_id'],
                'date' => $row['date'],
                'client_id' => $row['client_id'],
                'client_name' => $row['client_name'],
                'title' => $row['title'],
                'priority_id' => $row['priority_id'],
                'status' => $row['status'],
                'note' => $row['note'],
                'created_at_user' => $row_user_created['username']
            );
            return $project;
        }else{

            return array(
                'id' => '0',
                'pr_id' => '',
                'client_name' => '',
                'title' => ''
            );
        };

    }


    // metoda koja daje sve projekte
    public function getProjects (){

        $project = array();
        $projects = array();

        // sada treba isčitati sve klijente iz tabele client
        $result = $this->connection->query("SELECT project.id, project.pr_id, project.client_id, project.title, client.name "
                                    . "FROM project "
                                    . "JOIN (client) "
                                    . "ON (project.client_id = client.id) "
                                    . "ORDER BY id" ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){
            $project = array(
                'id' => $row['id'],
                'pr_id' => $row['pr_id'],
                'client_name' => $row['name'],
                'title' => $row['title']
            );
            array_push($projects, $project);
        }
        return $projects;
    }


    // provera koliko dana je nalog u sistemu a nije zatvoren
    public function style($project_date){
        $date_control = date ('Y-m-d');
        $datetime1 = date_create($project_date);
        $datetime2 = date_create($date_control);
        $interval = date_diff($datetime1, $datetime2);
        $days_number = $interval->format('%a');

        if( 7 > $days_number && $days_number >= 3 ) return $style = 'style="background-color:#F0F0F0;"'; //sivo ++
        if( 11 > $days_number && $days_number >= 7 ) return $style = 'style="background-color:#FFFFCC;"'; // svetlo žuto ++
        if( 16 > $days_number && $days_number >= 11 ) return $style = 'style="background-color:#FFFF99;"'; // žuto ++
        if( 32 > $days_number && $days_number >= 16 ) return $style = 'style="background-color:#FFCC99;"'; // svetlo narandžasto ++
        if( $days_number && $days_number >= 32 ) return $style = 'style="background-color:#FFB2B2;"'; // crveno ++

    }


    // praćenje projekata
    public function projectTracking ($status){

        $project = array();
        $projects = array();

        // listamo sve projekte koji nisu arhivirani, tj, status <> '9'
        $result = $this->connection->query("SELECT project.id, project.pr_id, project.date, project.title, project.status, project.client_id, client.name "
                                    . "FROM project "
                                    . "JOIN client "
                                    . "ON project.client_id=client.id "
                                    . "WHERE status <> '9' AND status = $status ") or die(mysqli_error($this->connection));
        while($row_project = $result->fetch_assoc()):
            $project_date = date('Y-m-d',strtotime($row_project['date']));
            $style = $this->style($project_date);
            $client_id = $row_project['client_id'];

            // pozivanje funkcije koja vraća naziv naselja za klijenta $client_id
            $client_city_name = self::getCityName($client_id);

            $client_name = $row_project['name'];

            $project = array(
                'id' => $row_project['id'],
                'pr_id' => $row_project['pr_id'],
                'date' => $project_date,
                'style' => $style,
                'title' => $row_project['title'],
                'status' => $row_project['status'],
                'client_name' => $client_name,
                'client_city_name' => $client_city_name

            );
            array_push($projects, $project);
        endwhile;

        return $projects;
    }


    // metoda koja daje sva naselja aktivnih projekata
    public function getCitysByActiveProject (){

        $city = array();
        $citys = array();

        // sada treba isčitati sva naselja iz tabele city
        $result = $this->connection->query("SELECT DISTINCT city.id, city.name as city_name "
                                    . "FROM city "
                                    . "JOIN (client, project) "
                                    . "ON (client.city_id=city.id AND project.client_id=client.id) "
                                    . "WHERE project.status='1'"
                                    . "ORDER BY city.name") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){
            $city = array(
                'id' => $row['id'],
                'name' => $row['city_name']
            );
            array_push($citys, $city);
        }
        return $citys;
    }


    // praćenje projekata iz određenog naselja
    public function projectTrackingByCity ($status, $city_id){

        $project = array();
        $projects = array();

        // listamo sve projekte koji nisu arhivirani, tj, status <> '9' i pripadaju mestu $city_id
        $result = $this->connection->query("SELECT project.id, project.pr_id, project.title, project.status, project.client_id, client.name "
                                    . "FROM project "
                                    . "JOIN client "
                                    . "ON project.client_id=client.id "
                                    . "WHERE status <> '9' AND status = $status AND city_id = $city_id ") or die(mysqli_error($this->connection));
        while($row_project = $result->fetch_assoc()):
            $client_id = $row_project['client_id'];
            // pozivanje funkcije koja vraća naziv naselja za klijenta $client_id
            $client_city_name = self::getCityName($client_id);
            $project = array(
                'id' => $row_project['id'],
                'pr_id' => $row_project['pr_id'],
                'title' => $row_project['title'],
                'status' => $row_project['status'],
                'client_name' => $row_project['name'],
                'client_city_name' => $client_city_name
            );
            array_push($projects, $project);
        endwhile;

        return $projects;
    }


    // praćenje projekata iz određenog naselja
    public function projectAdvancedSearch ($client, $project_title, $city){

        $project = array();
        $projects = array();

        // metoda koja vraća projekat/kte u zavisnosti od parametara u pretrazi
        if(!$client==""){
            // postoji upis u polje ime klijenta
            $where_client = " AND (client.name LIKE '%$client%' OR client.name_note LIKE '%$client%' )";

            // proveravamo da li je upisano nešto u polje naslov
            if(!$project_title==""){
                $where_project_title = " AND project.title LIKE '%$project_title%' ";
                if(!$city==""){
                    $where_city = " AND city.name LIKE '%$city%' ";
                }else{
                    $where_city = "";
                }
            }else{
               $where_project_title = "";
               if(!$city==""){
                    $where_city = " AND city.name LIKE '%$city%' ";
                }else{
                    $where_city = "";
                }
            }
        }else{
            // ne postoji upis u polje ime klijenta
            $where_client = "";

            // proveravamo da li je upisano nešto u polje naslov
            if(!$project_title==""){
                $where_project_title = " AND project.title LIKE '%$project_title%' ";
                if(!$city==""){
                    $where_city = " AND city.name LIKE '%$city%' ";
                }else{
                    $where_city = "";
                }
            }else{
               $where_project_title = "";
               if(!$city==""){
                    $where_city = " AND city.name LIKE '%$city%' ";
                }else{
                    $where_city = "";
                }
            }
        }

        $where = "WHERE (vps_id = 1 OR vps_id = 2) " . $where_client . $where_project_title . $where_city;

        // =======================================================================
        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result =  $this->connection->query("SELECT project.id, project.pr_id, client.name, city.name as city_name, project.title, project.status, project.client_id "
                                    . "FROM client "
                                    . "JOIN (street, city, project)"
                                    . "ON (client.city_id = city.id AND client.street_id = street.id AND project.client_id = client.id )"
                                    . $where
                                    . "ORDER BY project.pr_id ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $project = array(
                'id' => $row['id'],
                'pr_id' => $row['pr_id'],
                'title' => $row['title'],
                'status' => $row['status'],
                'client_name' => $row['name'],
                'client_city_name' => $row['city_name']
            );
            array_push($projects, $project);
        endwhile;

        return $projects;
    }


    //metoda koja vraća projekte u pretrazi
    public function search($name){

        $project = array();
        $projects = array();

        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT project.id, project.pr_id, project.date, project.client_id, client.name, project.status, project.title, project.note "
                                    ."FROM project "
                                    ."JOIN client "
                                    ."ON (project.client_id = client.id) "
                                    ."WHERE (client.name LIKE '%$name%' OR client.name_note LIKE '%$name%'OR project.title LIKE '%$name%') "
                                    ."ORDER BY client.name ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $client_id = $row['client_id'];
            // pozivanje funkcije koja vraća naziv naselja za klijenta $client_id
            $client_city_name = self::getCityName($client_id);
            $status = $row['status'];
            $title = $row['title'];
            $project = array(
                'id' => $row['id'],
                'pr_id' => $row['pr_id'],
                'date' => $row['date'],
                'client_name' => $row['name'],
                'client_city_name' => $client_city_name,
                'status' => $status,
                'title' => $title
            );
            array_push($projects, $project);
        endwhile;

        return $projects;
    }


    // sve beleške jednog projekta
    public function getNotesByProject($project_id){

        $note = array();
        $notes = array();

        // izlistavanje iz baze svih beležaka u jednom projektu
        $result = $this->connection->query("SELECT * FROM project_note WHERE (project_id = $project_id ) "
                                    . "ORDER BY id, date ") or die(mysqli_error($this->connection));

        while($row = $result->fetch_assoc()):
            $user_id = $row['created_at_user_id'];
            $result_user = $this->connection->query("SELECT * FROM admin WHERE id = $user_id ") or die(mysqli_error($this->connection));
            $row_user = $result_user->fetch_assoc();
            $note = array(
                'id' => $row['id'],
                'date' => $row['date'],
                'user_id' => $user_id,
                'user_name' => $row_user['username'],
                'note' => $row['note']
            );
            array_push($notes, $note);
        endwhile;

        return $notes;
    }


    // svi zadatci jednog projekta
    public function projectTasks($project_id){

        $project_task = array();
        $project_tasks = array();

        // izlistavanje iz baze svih zadataka u jednom projektu
        $result = $this->connection->query("SELECT * FROM project_task WHERE (project_id = $project_id ) "
                                         . "ORDER BY date ") or die(mysqli_error($this->connection));

        while($row = $result->fetch_assoc()):
            $id = $row['id'];
            $date = $row['date'];
            $user_id = $row['created_at_user_id'];
            $result_user = $this->connection->query("SELECT * FROM admin WHERE id = $user_id ") or die(mysqli_error($this->connection));
            $row_user = $result_user->fetch_assoc();
            $user_name = $row_user['username'];
            $status_id = $row['status_id'];
            $tip_id = $row['tip_id'];
            switch ($tip_id) {
                case "1":
                    $tip = "Merenje";
                    $class = "info";
                    break;
                case "2":
                    $tip = "Ponuda";
                    $class = "warning";
                    break;
                case "3":
                    $tip = "Nabavka";
                    $class = "secondary";
                    break;
                case "4":
                    $tip = "Proizvodnja";
                    $class = "success";
                    break;
                case "5":
                    $tip = "Isporuka";
                    $class = "isporuka";
                    break;
                 case "6":
                    $tip = "Montaža";
                    $class = "yellow";
                    break;
                 case "7":
                    $tip = "Rreklamacija";
                    $class = "danger";
                    break;
                 case "8":
                    $tip = "Popravka";
                    $class = "popravka";
                    break;
                default:
                     $tip = "";
            }

            $title = $row['title'];
            $employee_id = $row['employee_id'];
            $result_employee = $this->connection->query("SELECT * "
                                                      . "FROM employee "
                                                      . "WHERE id = $employee_id") or die(mysqli_error($this->connection));
            $row_employee = $result_employee->fetch_assoc();
                $employee_name = $row_employee['name'];

            $start = $row['start'];
            $end = $row['end'];

            $project_task = array(
                'id' => $id,
                'date' => $date,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'status_id' => $status_id,
                'tip_id' => $tip_id,
                'tip' => $tip,
                'class' => $class,
                'title' => $title,
                'employee_name' => $employee_name,
                'start' => $start,
                'end' => $end
            );

            array_push($project_tasks, $project_task);

        endwhile;

        return $project_tasks;
    }


    // metoda koja daje podatke o zadatku (tasku)
    public function getTask ($task_id){

        $result = $this->connection->query("SELECT * "
                                         . "FROM project_task "
                                         // . "JOIN (client) "
                                         // . "ON (project.client_id = client.id ) "
                                         . "WHERE id = $task_id ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();
            $id = $row['id'];
            if(!empty($id)){
                $tip_id = $row['tip_id'];
                switch ($tip_id) {
                    case "1":
                        $tip = "Merenje";
                        $class = "info";
                        break;
                    case "2":
                        $tip = "Ponuda";
                        $class = "warning";
                        break;
                    case "3":
                        $tip = "Nabavka";
                        $class = "default";
                        break;
                    case "4":
                        $tip = "Proizvodnja";
                        $class = "success";
                        break;
                    case "5":
                        $tip = "Isporuka";
                        $class = "isporuka";
                        break;
                     case "6":
                        $tip = "Montaža";
                        $class = "yellow";
                        break;
                     case "7":
                        $tip = "Rreklamacija";
                        $class = "danger";
                        break;
                     case "8":
                        $tip = "Popravka";
                        $class = "popravka";
                        break;
                    default:
                         $tip = "";
                }

                $title = $row['title'];

                $date = $row['date'];
                $user_id = $row['created_at_user_id'];
                $result_user = $this->connection->query("SELECT * FROM admin WHERE id = $user_id ") or die(mysqli_error($this->connection));
                $row_user = $result_user->fetch_assoc();
                $user_name = $row_user['username'];
                $employee_id = $row['employee_id'];

                if($employee_id == 0 OR $employee_id == ""){
                    $employee_name = "Izaberi izvršioca";
                }else{

                    $result_employee = $this->connection->query("SELECT * "
                                                          . "FROM employee "
                                                          . "WHERE id = $employee_id") or die(mysqli_error($this->connection));
                    $row_employee = $result_employee->fetch_assoc();
                    $employee_name = $row_employee['name'];

                }

                $start = $row['start'];
                $end = $row['end'];
                $task = array(
                    'id' => $id,
                    'date' => $date,
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'tip_id' => $tip_id,
                    'tip' => $tip,
                    'title' => $title,
                    'class' => $class,
                    'employee_id' => $employee_id, 
                    'employee_name' => $employee_name,
                    'start' => $start,
                    'end' => $end
                );

                return $task;
            }else {

                return 'noProject';
            }

    }


    // sve beleške jednog projekta
    public function getTaskNotesByProject($task_id){

        $task_note = array();
        $task_notes = array();

        // izlistavanje iz baze svih beležaka u jednom projektu
        $result = $this->connection->query("SELECT * FROM project_task_note WHERE (project_task_id = $task_id ) "
                                         . "ORDER BY date ") or die(mysqli_error($this->connection));

        while($row = $result->fetch_assoc()):
            $user_id = $row['created_at_user_id'];
            $result_user = $this->connection->query("SELECT * FROM admin WHERE id = $user_id ") or die(mysqli_error($this->connection));
            $row_user = mysqli_fetch_array($result_user);
            $task_note = array(
                'id' => $row['id'],
                'date' => $row['date'],
                'user_id' => $user_id,
                'user_name' => $row_user['username'],
                'note' => $row['note']
            );
            array_push($task_notes, $task_note);
        endwhile;

        return $task_notes;
    }


    public function getEmployees (){
        return $this->get("SELECT id, name FROM employee ORDER BY name");
    }


    public function delNoteFromProjectTask($project_task_note_id){
        return $this->delete("DELETE FROM project_task_note WHERE id=$project_task_note_id");
    }

}
