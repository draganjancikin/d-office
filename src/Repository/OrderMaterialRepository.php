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
   * Method that return quantity of material. If material dont have property
   * quantity = 1, if material have one property quantity = property/100. If
   * material have two peoperties, quantity = property_one * proerty_two.
   * 
   * @param int $material_on_order_id
   * @param float $min_obrac_mera
   * @param float $pieces
   *  
   * @return float 
   */
  public function getQuantity($material_on_order_id, $min_obrac_mera, $pieces) {
    $properties = $this->getPropertiesOnOrderMaterial($material_on_order_id);
    $temp_quantity = 1;
    
    foreach ($properties as $property) {
      $temp_quantity = $temp_quantity * ( $property->getQuantity()/100 );
    }

    if($temp_quantity < $min_obrac_mera) $temp_quantity = $min_obrac_mera;

    $quantity = round($pieces * $temp_quantity, 2);

    return $quantity;
  }

}
