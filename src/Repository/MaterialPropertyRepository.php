<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class MaterialPropertyRepository extends EntityRepository
{

    /**
     * Method that return Material Properties.
     *
     * @param int $material
     *
     * @return
     */
    public function getMaterialProperties($material) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('mp')
            ->from('App\Entity\MaterialProperty', 'mp')
            ->join('mp.property', 'p', 'WITH', 'mp.property = p.id')
            ->where(
                $qb->expr()->eq('mp.material', $material)
            );
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
  }

}