<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository {

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
    
    // TODO Dragan

    return array();

    /*
        $material = array();
        $materials = array();

        // niz $propertys bi mogli iskoristiti da se spakuju svi property-ji jednog artikla
        $result = $this->connection->query("SELECT material.name, material.unit_id, material.min_obrac_mera, orderm_material.id, orderm_material.material_id, orderm_material.note, orderm_material.pieces, orderm_material.price, orderm_material.discounts, orderm_material.tax, unit.name as unit_name "
                                         . "FROM orderm_material "
                                         . "JOIN (material, unit) "
                                         . "ON (orderm_material.material_id = material.id AND material.unit_id = unit.id)"
                                         . "WHERE orderm_material.order_id = $order_id "
                                         . "ORDER BY orderm_material.id") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){

            $id = $row['id'];
            $material_id = $row['material_id'];
            $material_min_obrac_mera = $row['min_obrac_mera'];
            $name = $row['name'];

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

            $unit_id = $row['unit_id'];
            $unit_name = $row['unit_name'];
            $note = $row['note'];
            $pieces = $row['pieces'];

            $quantity = round($pieces * $temp_quantity, 2);

            $price = $row['price'];
            $discounts = $row['discounts'];
            $tax = $row['tax'];

            $tax_base_per_piece = $price - round( $price * ($discounts/100), 4 );
            $tax_amount_per_piece = round( ($tax_base_per_piece * ($tax/100)), 4 );
            
            $tax_base_per_article = $tax_base_per_piece * $quantity;
            $tax_amount_per_article = $tax_amount_per_piece * $quantity;
            $sub_total_per_article = $tax_base_per_article + $tax_amount_per_article;
            
        $material = array(
                'id' => $id,
                'material_id' => $material_id,
                'code' => "",
                'name' => $name,
                'propertys' => $propertys,
                'unit_name' => $unit_name,
                'note' => $note,
                'pieces' => $pieces,
                'quantity' => $quantity,
                'price' => $price,
                'discounts' => $discounts,
                'tax_base' => $tax_base_per_article,
                'tax' => $tax,
                'tax_amount' => $tax_amount_per_article,
                'sub_total' => $sub_total_per_article
            );
            array_push($materials, $material);
        }
    */
  }

}
