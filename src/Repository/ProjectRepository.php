<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository {

  /**
   * Method that return number of Projects.
   *
   * @return int
   */
  public function getNumberOfProjects() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(p.id)')
        ->from('Roloffice\Entity\Project','p');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return all active Projects.
   * 
   * @return array 
   */
  public function getAllActiveProjects() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('p')
        ->from('Roloffice\Entity\Project','p')
        ->where(
          $qb->expr()->eq('p.status', 1),
        );
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result;
  }

  /**
   * Method that set OrdinalProject number in year.
   *
   * @param int $project_id
   * @return void
   */
  public function setOrdinalNumInYear($project_id) {
    
    // count number of records in database table project
    $project_count = $this->getNumberOfProjects();
    
    // get ID of last project
    $id_of_last_project = $this->getLastProject()->getId();

    // get year of last project
    $year_of_last_project = $this->getLastProject()->getCreatedAt()->format('Y');
    
    // get ID of project before last
    $id_of_project_before_last = $this->getProjectBeforeLast()->getId();

    // get ordinal number in year of project before last
    $ordinal_number_of_project_before_last = $this->getProjectBeforeLast()->getOrdinalNumInYear();

    // year of project before last
    $year_of_project_before_last = $this->getProjectBeforeLast()->getCreatedAt()->format('Y');

    if($project_count ==0){  // prvi slučaj kada je tabela $table prazna
    
      return die("Tabela project je prazna!");
    
    }elseif($project_count ==1){  // drugi slučaj - kada postoji jedan unos u tabeli $table
    
      $ordinal_number_in_year = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'
    
    }else{  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table
    
      if($year_of_last_project < $year_of_project_before_last){
        return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");
      }elseif($year_of_last_project == $year_of_project_before_last){ //nema promene godine
        $ordinal_number_in_year = $ordinal_number_of_project_before_last + 1;
      }else{  // došlo je do promene godine
        $ordinal_number_in_year = 1;
      }
    
    }

    // update ordinal_number_in_year
    $project = $this->_em->find('\Roloffice\Entity\Project', $project_id);

    if ($project === null) {
      echo "Project with ID $project_id does not exist.\n";
      exit(1);
    }

    $project->setOrdinalNumInYear($ordinal_number_in_year);

    $this->_em->flush();

  }

  /**
   * Method that return array of Cities
   * 
   * @return array 
   */
  public function getCitiesByActiveProject() {
    
    $qb = $this->_em->createQueryBuilder();
    
    /*    
    $qb->select('p')
      ->from('Roloffice\Entity\Project','p')
      ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
      ->join('cl.city', 'ci', 'WITH', 'cl.city = ci.id')
      ->where(
        $qb->expr()->eq('p.status', 1)
      )
    ->orderBy('ci.name', 'ASC');
    */
    
    $qb->select('ci.id, ci.name')
      ->from('Roloffice\Entity\Project','p')
      ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
      ->join('cl.city', 'ci', 'WITH', 'cl.city = ci.id')
      ->where(
        $qb->expr()->eq('p.status', 1)
      )
      ->orderBy('ci.name', 'ASC')
      ->distinct();
      
    $query = $qb->getQuery();
    $result = $query->getResult();

    return $result;
  }

  /**
   * Methot that return all Project with status = $status.
   * 
   * @param int $status
   * 
   * @return array
   */
  public function projectTracking ($status){
    
    $qb = $this->_em->createQueryBuilder();
    $qb->select('p')
      ->from('Roloffice\Entity\Project','p')
      ->where(
        $qb->expr()->eq('p.status', "$status")
      )
      ->orderBy('p.id', 'ASC');
    
    $query = $qb->getQuery();
    $result = $query->getResult();

    return $result;
  }

  /**
   * Methot thar return all Tasks by Project
   */
  public function projectTasks($project_id){
    
    $qb = $this->_em->createQueryBuilder();
    
    
    /*
    $project_task = array();
    $project_tasks = array();

    // izlistavanje iz baze svih zadataka u jednom projektu
    $result = $this->connection->query("SELECT * FROM project_task WHERE (project_id = $project_id ) "
                                     . "ORDER BY date ") or die(mysqli_error($this->connection));

    while($row = $result->fetch_assoc()):
        $id = $row['id'];
        $date = $row['date'];
        $user_id = $row['created_at_user_id'];
        $result_user = $this->connection->query("SELECT * FROM v6_users WHERE id = $user_id ") or die(mysqli_error($this->connection));
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
        
        $result_employee = $this->get("SELECT name "
                                    . "FROM employee "
                                    . "WHERE id = $employee_id");
        
        if ($result_employee) {
            $row_employee = $result_employee[0];
            $employee_name = $row_employee['name'];
        } else {
            $employee_name = " ";
        }

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
    */
    $result = [];
    return $result;
  }

}
