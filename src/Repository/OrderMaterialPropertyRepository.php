<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class OrderMaterialPropertyRepository extends EntityRepository {

  /**
   * Method that return Properties by Order Material.
   * 
   * @return 
   */
  public function getOrderMaterialProperties($order_material) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('omp')
      ->from('Roloffice\Entity\OrderMaterialProperty', 'omp')
      ->join('omp.property', 'p', 'WITH', 'omp.property = p.id')
      ->where(
        $qb->expr()->eq('omp.order_material', $order_material)
      );
      $query = $qb->getQuery();
      $result = $query->getResult();
      return $result;
  }

}
