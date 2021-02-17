<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class MaterialPropertyRepository extends EntityRepository {

  /**
   * Method that return Properties by Material.
   * 
   * @return 
   */
  public function getMaterialProperties($material) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('mp')
        ->from('Roloffice\Entity\MaterialProperty', 'mp')
        ->join('mp.property', 'p', 'WITH', 'mp.property = p.id')
        ->where(
          $qb->expr()->eq('mp.material', $material)
        );
        $query = $qb->getQuery();
        $result = $query->getResult();
    return $result;
  }

}