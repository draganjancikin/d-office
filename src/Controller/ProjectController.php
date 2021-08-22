<?php

namespace Roloffice\Controller;

use Roloffice\Core\Database;

/**
 * Description of Project class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class ProjectController extends Database {

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

        $result = $this->connection->query("SELECT v6_clients.city_id, v6_cities.name "
                                            . "FROM v6_clients "
                                            . "JOIN (v6_cities)"
                                            . "ON (v6_clients.city_id = v6_cities.id )"
                                            . "WHERE v6_clients.id = $client_id ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();

        return $row['name'];
    }


    // metoda koja daje podatke o projektu
    public function getProject($project_id) {

        $result = $this->get("SELECT project.id, project.pr_id, project.date, project.client_id, project.title, project.priority_id, project.status, project.note, project.created_at_user_id, v6_clients.name as client_name "
                            . "FROM project "
                            . "JOIN (v6_clients) "
                            . "ON (project.client_id = v6_clients.id ) "
                            . "WHERE project.id = $project_id ");
        if( !empty($result) ){
            $row = $result[0];
            $id = $row['id'];

            $created_at_user_id = $row['created_at_user_id'];
            $result_user_created = $this->get("SELECT v6_users.username "
                                                  . "FROM v6_users "
                                                  . "WHERE v6_users.id = $created_at_user_id ");
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
        $result = $this->connection->query("SELECT project.id, project.pr_id, project.client_id, project.title, v6_clients.name "
                                    . "FROM project "
                                    . "JOIN (v6_clients) "
                                    . "ON (project.client_id = v6_clients.id) "
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


    // praćenje projekata iz određenog naselja
    public function projectAdvancedSearch ($client, $project_title, $city){

        $project = array();
        $projects = array();

        // metoda koja vraća projekat/kte u zavisnosti od parametara u pretrazi
        if(!$client==""){
            // postoji upis u polje ime klijenta
            $where_client = " AND (v6_clients.name LIKE '%$client%' OR v6_clients.name_note LIKE '%$client%' )";

            // proveravamo da li je upisano nešto u polje naslov
            if(!$project_title==""){
                $where_project_title = " AND project.title LIKE '%$project_title%' ";
                if(!$city==""){
                    $where_city = " AND v6_cities.name LIKE '%$city%' ";
                }else{
                    $where_city = "";
                }
            }else{
               $where_project_title = "";
               if(!$city==""){
                    $where_city = " AND v6_cities.name LIKE '%$city%' ";
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
                    $where_city = " AND v6_cities.name LIKE '%$city%' ";
                }else{
                    $where_city = "";
                }
            }else{
               $where_project_title = "";
               if(!$city==""){
                    $where_city = " AND v6_cities.name LIKE '%$city%' ";
                }else{
                    $where_city = "";
                }
            }
        }

        $where = "WHERE (type_id = 1 OR type_id = 2) " . $where_client . $where_project_title . $where_city;

        // =======================================================================
        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result =  $this->connection->query("SELECT project.id, project.pr_id, v6_clients.name, v6_cities.name as city_name, project.title, project.status, project.client_id "
                                    . "FROM v6_clients "
                                    . "JOIN (v6_streets, v6_cities, project)"
                                    . "ON (v6_clients.city_id = v6_cities.id AND v6_clients.street_id = v6_streets.id AND project.client_id = v6_clients.id )"
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

/*
    // metoda koja daje podatke o zadatku (tasku)
    public function getTask ($task_id){

        $result = $this->connection->query("SELECT * "
                                         . "FROM project_task "
                                         // . "JOIN (client) "
                                         // . "ON (project.client_id = v6_clients.id ) "
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
                $result_user = $this->connection->query("SELECT * FROM v6_users WHERE id = $user_id ") or die(mysqli_error($this->connection));
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
    */

/*
    public function getEmployees (){
        return $this->get("SELECT id, name FROM employee ORDER BY name");
    }
*/

    public function delNoteFromProjectTask($project_task_note_id){
        return $this->delete("DELETE FROM project_task_note WHERE id=$project_task_note_id");
    }

}
