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
}
