<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class OrderMaterialRepository extends EntityRepository {

  /**
   * 
   * @param int $material_on_order_id
   * @return array
   */
  public function getPropertiesOnOrderMaterial($material_on_order_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('omp')
      ->from('Roloffice\Entity\OrderMaterialProperty', 'omp')
      ->join('omp.property', 'p', 'omp.property = p.id')
      ->where(
        $qb->expr()->eq('omp.order_material', $material_on_order_id),
        );
      $query = $qb->getQuery();
      $result = $query->getResult();
    
    return $result;
  }

    /**
   *  @return float 
   */
  public function getQuantity($material_on_order_id) {
    $properties = $this->getPropertiesOnOrderMaterial($material_on_order_id);
    $quantity = 1;
    foreach ($properties as $property) {
      $quantity = $quantity * ( $property->getQuantity()/100 );
    }

    if($quantity < $material_min_obrac_mera) $quantity = $material_min_obrac_mera;

    return $quantity;
  }

}
