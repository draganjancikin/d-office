<?php

namespace App\Repository;

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
        ->from('App\Entity\Material', 'm')
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
      ->from('App\Entity\Material', 'm')
    
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

  /**
   * Method that return all Materials from one Supplier
   * 
   * @param int $supplier_id
   * 
   * @return null|Material[] $material 
   */
  public function getSupplierMaterials($supplier_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('ms')
      ->from('App\Entity\MaterialSupplier', 'ms')
      ->join('ms.material', 'm', 'WITH', "ms.material = m.id")
      ->where(
        $qb->expr()->eq('ms.supplier', $supplier_id)
      )
      ->orderBy('m.name', 'ASC');
    $query = $qb->getQuery();
    $materials = $query->getResult();
    return $materials;
  }

}