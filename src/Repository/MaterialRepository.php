<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class MaterialRepository extends EntityRepository {

  /**
   * Method that return last $limit material.
   * 
   * @return 
   */
  public function getLastMaterials($limit = 5) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('m')
        ->from('Roloffice\Entity\Material', 'm')
        ->orderBy('m.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

}