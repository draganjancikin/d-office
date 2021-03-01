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
   * 
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

}
