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

}
