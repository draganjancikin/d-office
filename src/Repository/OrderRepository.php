<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository {

  /**
   * Method that return number of Orders.
   *
   * @return int
   */
  public function getNumberOfOrders() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(o.id)')
        ->from('Roloffice\Entity\Order','o');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return last $limit Orders.
   * 
   * @return 
   */
  public function getLastOrders($limit = 5) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('o')
        ->from('Roloffice\Entity\Order', 'o')
        ->orderBy('o.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

  /**
   * Method that return all Materials on Order
   * 
   * @param int $order_id
   * 
   * @return array
   */
  public function getMaterialsOnOrder($order_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('om')
        ->from('Roloffice\Entity\OrderMaterial', 'om')
        ->join('om.material', 'm', 'om.material = m.id')
        ->where(
          $qb->expr()->eq('om.order', $order_id),
        )
        ->orderBy('om.id', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result;

    /*
            // treba izčitati sve property-e artikla iz tabele pidb_article_property
            $property = "";
            $temp_quantity = 1;

            $propertys = array();

            $result_propertys = $this->connection->query("SELECT orderm_material_property.quantity, property.name "
                    . "FROM orderm_material_property "
                    . "JOIN (property) "
                    . "ON (orderm_material_property.property_id = property.id) "
                    . "WHERE orderm_material_id = $id" ) or die(mysqli_error($this->connection));
            while($row_property = mysqli_fetch_array($result_propertys)){
                $property_name = $row_property['name'];
                $property_quantity = $row_property['quantity'];

                $property = $property . $property_name . ' <input class="input-box-50" type="text" name="' .$property_name. '" value="' .$property_quantity. '" placeholder="(cm)" /> ';

                $property_niz = array(
                    'property_name' => $property_name,
                    'property_quantity' => $property_quantity
                );

                array_push($propertys, $property_niz);

                $temp_quantity = $temp_quantity * ( $property_quantity/100 );

            }

            if($temp_quantity < $material_min_obrac_mera) $temp_quantity = $material_min_obrac_mera;
    */
  }

}
