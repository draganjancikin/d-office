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
   * Method that rerurn ID of last project in db table
   *
   * @return object
   */
  public function getLastProject() {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('p')
        ->from('Roloffice\Entity\Project', 'p')
        ->orderBy('p.id', 'DESC')
        ->setMaxResults(1);
    $query = $qb->getQuery();
    $last_project = $query->getResult()[0];
    
    return $last_project;
  }

  /**
   * Method that rerurn ID of Project before last in db table
   *
   * @return object
   */
  public function getProjectBeforeLast() {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('p')
        ->from('Roloffice\Entity\Project', 'p')
        ->orderBy('p.id', 'DESC')
        ->setMaxResults(2);
    $query = $qb->getQuery();
    $project_before_last = $query->getResult()[1];
    
    return $project_before_last;
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
    
    $qb->select('pt')
    ->from('Roloffice\Entity\ProjectTask','pt')
    ->where(
      $qb->expr()->eq("pt.project", "$project_id")
    )
    ->orderBy('pt.created_at', 'ASC');

    $query = $qb->getQuery();
    $result = $query->getResult();

    return $result;
  }

  /**
   * 
   */
  public function projectTrackingByCity($status, $city_id) {

    $qb = $this->_em->createQueryBuilder();

    $qb->select('p')
    ->from('Roloffice\Entity\Project','p')
    ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
    ->where(
      $qb->expr()->andX(
        $qb->expr()->eq("p.status", "$status"),
        $qb->expr()->eq("cl.city", "$city_id")
      )
    );

    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result;
  }

  /**
   * Method that return Project search
   * 
   * @param string $term
   * 
   * @return array
   */
  public function search($term) {
    
    $qb = $this->_em->createQueryBuilder();

    $qb->select('p')
      ->from('Roloffice\Entity\Project','p')
      ->join('p.client', 'cl', 'WITH', 'p.client = cl.id')
      ->where(
        $qb->expr()->orX(
          $qb->expr()->like('cl.name', $qb->expr()->literal("%$term%")),
          $qb->expr()->like('cl.name_note', $qb->expr()->literal("%$term%")),
          $qb->expr()->like('p.title', $qb->expr()->literal("%$term%"))
        )
      );
    
    $query = $qb->getQuery();
    $result = $query->getResult();
  
    return $result;
  }
  
  /**
   * Method that return Notes by Project
   * 
   * @param int $project_id
   * 
   * @return array
   */
  public function getNotesByProject($project_id) {
    
    $qb = $this->_em->createQueryBuilder();

    $qb->select('pn')
      ->from('Roloffice\Entity\ProjectNote','pn')
      ->where(
        $qb->expr()->eq("pn.project", "$project_id")
      )
      ->orderBy('pn.id', 'ASC');;

    $query = $qb->getQuery();
    $result = $query->getResult();
  
    return $result;
  }

  /**
   * Method that return all notes by ProjectTask
   * 
   * @param int $project_task_id
   * 
   * @return array
   */
  /* ima jednostavniji način
  ------------------------------------------------------------------------------
  $task_notes = $entityManager->getRepository('\Roloffice\Entity\ProjectTaskNote')->findBy(array('project_task' => $project_task));
  ------------------------------------------------------------------------------
  public function getNotesByProjectTask($project_task_id) {

    $qb = $this->_em->createQueryBuilder();

    $qb->select('ptn')
      ->from('Roloffice\Entity\ProjectTaskNote','ptn')
      ->where(
        $qb->expr()->eq("ptn.project_task", "$project_task_id")
      )
      ->orderBy('ptn.created_at', 'ASC');;

    $query = $qb->getQuery();
    $result = $query->getResult();
  
    return $result;
  }
  */

}
