<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class OrderMaterialRepository extends EntityRepository
{

    /**
     *
     * @param int $material_on_order_id
     * @return array
     */
    public function getProperties($material_on_order_id) {
        // Create a QueryBuilder instance.
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('omp')
            ->from('App\Entity\OrderMaterialProperty', 'omp')
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
        $properties = $this->getProperties($material_on_order_id);
        $temp_quantity = 1;

        foreach ($properties as $property) {
            $temp_quantity = $temp_quantity * ( $property->getQuantity()/100 );
        }

        if ($temp_quantity < $min_obrac_mera) $temp_quantity = $min_obrac_mera;

        $quantity = round($pieces * $temp_quantity, 2);

        return $quantity;
    }

    /**
     *  Method that return Tax Base by Material on Order.
     *
     * @param float $price
     *  Price of Material
     * @param float $discount
     *  Discount of Material
     * @param float $quantity
     *  Quantity of Material
     *
     * @return float
     */
    public function getTaxBase($price, $discount, $quantity) {
        return ($price - round( $price * ($discount/100), 4 ) ) * $quantity;
    }

    /**
     * Methot that return Amount of Tax by Material on order.
     *
     * @param float $tax_base
     * @param float $tax
     * @param float $kurs
     *
     * @return float
     */
    public function getTaxAmount($tax_base, $tax) {
        return round( ($tax_base * ($tax/100)), 4 );
    }

    /**
     * @param float $tax_base
     * @param float $tax_amount
     */
    public function getSubTotal($tax_base, $tax_amount) {
        return $tax_base + $tax_amount;
    }

    /**
     * Method that return Materials by Order.
     *
     * @return
     */
    public function getOrderMaterials($order) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('om')
            ->from('App\Entity\OrderMaterial', 'om')
            ->join('om.material', 'm', 'WITH', 'om.material = m.id')
            ->where(
                $qb->expr()->eq('om.order', $order)
            );
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}
