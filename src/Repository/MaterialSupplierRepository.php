<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class MaterialSupplierRepository extends EntityRepository
{

    /**
     * Method that return suppliers by material.
     *
     * @return
     */
    public function getMaterialSuppliers($material) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ms')
            ->from('App\Entity\MaterialSupplier', 'ms')
            ->join('ms.supplier', 'c', 'WITH', 'ms.supplier = c.id')
            ->where(
                $qb->expr()->eq('ms.material', $material)
            );
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}