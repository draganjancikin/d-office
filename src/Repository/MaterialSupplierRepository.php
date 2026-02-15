<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Material;
use Doctrine\ORM\EntityRepository;

class MaterialSupplierRepository extends EntityRepository
{

    /**
     * Method that return suppliers by material.
     *
     * @return array
     */
    public function getMaterialSuppliers($material): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ms')
            ->from('App\Entity\MaterialSupplier', 'ms')
            ->join('ms.supplier', 'c', 'WITH', 'ms.supplier = c.id')
            ->where($qb->expr()->eq('ms.material', $material));
        return $qb->getQuery()->getResult();
    }

    /**
     * Returns material supplier(s) for a material where supplier_id matches the given supplier id.
     *
     * @param int $material_id
     * @param int $supplier_id
     * @return array
     */
    public function getByMaterialAndSupplierId(int $material_id, int $supplier_id): array
    {
        $material = $this->getEntityManager()->getReference(Material::class, $material_id);
        $supplier = $this->getEntityManager()->getReference(Client::class, $supplier_id);
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ms')
            ->from('App\Entity\MaterialSupplier', 'ms')
            ->where($qb->expr()->eq('ms.material', ':material'))
            ->andWhere($qb->expr()->eq('ms.supplier', ':supplier'))
            ->setParameter('material', $material)
            ->setParameter('supplier', $supplier);
        return $qb->getQuery()->getResult();
    }

}