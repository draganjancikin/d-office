<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class MaterialRepository extends EntityRepository {

  /**
   * Method that return number of Materials.
   *
   * @return int
   */
  public function getNumberOfMaterials() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(m.id)')
        ->from('Roloffice\Entity\Material','m');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

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

  /**
   * Search method by criteria: name and name note.
   *  
   * @return array
   */
  public function search($term) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('m')
      ->from('Roloffice\Entity\Material', 'm')
    
    /*
      ->join('m.street', 's', 'WITH', 'm.street = s.id')
      ->join('m.city', 'c', 'WITH', 'm.city = c.id')
      */
      ->where(
        $qb->expr()->like('m.name', $qb->expr()->literal("%$term%")),
        )
      ->orderBy('m.name', 'ASC');

    $query = $qb->getQuery();
    $materials = $query->getResult();
    return $materials;
  }

}