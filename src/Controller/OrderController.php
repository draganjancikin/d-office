<?php

namespace Roloffice\Controller;

use Roloffice\Core\Database;

/**
 * Description of Order class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class OrderController extends Database {

    protected $id;
    protected $o_id;
    protected $date;
    protected $supplier_id;
    protected $project_id;
    protected $title;
    protected $archived;
    protected $status;
    protected $is_archived;
    protected $note;

    /**
     * Method that return last orded ID
     * 
     * @return integer
     */
    public function getLastOrderId() {
        $result = $this->getLastId("orderm");
        return $result;
    }

    /**
     * Method that return las orders
     * 
     * @param integer $limit
     * 
     * @return array
     */
    public function getLastOrders($limit) {
        $result = $this->get("SELECT orderm.id, orderm.o_id, orderm.date, orderm.project_id, orderm.title, orderm.status, orderm.is_archived, client.name as supplier_name "
                            . "FROM orderm "
                            . "JOIN (client) "
                            . "ON (orderm.supplier_id = client.id) "
                            . "ORDER BY orderm.id DESC LIMIT $limit");
        return $result;
    }

    // metoda koja definiše i dodeljuje vrednost o_id 
    public function setOid(){

        // čitamo iz baze, iz tabele order sve zapise 
        $result = $this->connection->query("SELECT * FROM orderm ORDER BY id DESC") or die(mysqli_error($this->connection));

        // brojimo koliko ima zapisa
        $num = mysqli_num_rows($result); // broj kolona u tabeli $table

        $row = mysqli_fetch_array($result);
        $last_id = $row['id'];
        $year_last = date('Y', strtotime($row['date']));

        $row = mysqli_fetch_array($result);
        $year_before_last = date('Y', strtotime($row['date']));

        $o_id_before_last = $row['o_id'];

        if($num ==0){  // prvi slučaj kada je tabela $table prazna

            return die("Tabela task je prazna!");

        }elseif($num ==1){  // drugi slučaj - kada postoji jedan unos u tabeli $table

            $o_id = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'

        }else{  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table

            if($year_last < $year_before_last){

                return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");

            }elseif($year_last == $year_before_last){ //nema promene godine

                $o_id = $o_id_before_last + 1;

            }else{  // došlo je do promene godine

                $o_id = 1;

            }

        }

        $this->connection->query("UPDATE orderm SET o_id = '$o_id' WHERE id = '$last_id' ") or die(mysqli_error($this->connection));
    }

    /**
     * Method that return order data by order ID
     * 
     * @param integer $order_id
     * 
     * @return array
     */
    public function getOrder($order_id) {
        $result =  $this->get("SELECT orderm.id, orderm.o_id, orderm.date, orderm.supplier_id, orderm.project_id, orderm.title, orderm.status, orderm.is_archived, orderm.note, client.name "
                            . "FROM orderm "
                            . "JOIN client "
                            . "ON (orderm.supplier_id = client.id) "
                            . "WHERE orderm.id = $order_id ");
        if(empty($result)) {
            die('<script>location.href = "/orders/" </script>');
        } else {
            return $result[0];
        }
    }


    /**
     * Method that return materials on order
     * 
     * @param integer $order_id
     * 
     * @return array
     */
    public function getMaterialsOnOrder($order_id){

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

        return $materials;
    }

    /**
     * Method that return material in order by order material id
     *
     * @param int $order_material_id
     * @return array
     */
    public function getMaterialInOrder($order_material_id){

        $material = array();

        // need: order_id, material_id, note, pieces, price, discount, tax, weight, propertys
        $result = $this->connection->query("SELECT orderm_material.id, orderm_material.order_id, orderm_material.material_id, orderm_material.note, orderm_material.pieces, orderm_material.price, orderm_material.discounts, orderm_material.tax, orderm_material.weight "
                                         . "FROM orderm_material "
                                         . "JOIN (material, unit) "
                                         . "ON (orderm_material.material_id = material.id AND material.unit_id = unit.id)"
                                         . "WHERE orderm_material.id = $order_material_id ") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);

            $id = $row['id'];
            $order_id = $row['order_id'];
            $material_id = $row['material_id'];
   
            // treba izčitati sve property-e materiala iz tabele orderm_material_property
            $property = "";
            $temp_quantity = 1;
            $propertys = array();
            $result_propertys = $this->connection->query("SELECT orderm_material_property.quantity, property.name "
                                                       . "FROM orderm_material_property "
                                                       . "JOIN (property)"
                                                       . "ON (orderm_material_property.property_id = property.id)"
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

            $note = $row['note'];
            $pieces = $row['pieces'];
            $quantity = round($pieces * $temp_quantity, 2);
            $price = $row['price'];
            $discounts = $row['discounts'];
            $tax = $row['tax'];
            $weight  = $row['weight'];
        $material = array(
                'id' => $id,
                'order_id' => $order_id,
                'material_id' => $material_id,
                'propertys' => $propertys,
                'note' => $note,
                'pieces' => $pieces,
                'quantity' => $quantity,
                'price' => $price,
                'discounts' => $discounts,
                'tax' => $tax,
                'weight' => $weight,
            );

        return $material;
    }

    /**
     * Method that return orders with client name like $name
     * 
     * @param string $name
     * 
     * @return array
     */
    public function search($name) {
        $result = $this->get("SELECT orderm.id, orderm.o_id, orderm.date, orderm.project_id, client.name as supplier_name, orderm.title, orderm.status, orderm.is_archived "
                            . "FROM orderm JOIN (client)"
                            . "ON (orderm.supplier_id = client.id)"
                            . "WHERE (client.name LIKE '%$name%' ) "
                            . "ORDER BY orderm.id DESC ");
        return $result;
    }

    /**
     * Method that delete material from order
     * 
     * @param integer $orderm_material_id
     */
    public function delMaterialFromOrder($orderm_material_id){

        $this->delete("DELETE FROM orderm_material WHERE id='$orderm_material_id' ");
        
        $propertys = $this->get("SELECT * FROM orderm_material_property WHERE orderm_material_id = '$orderm_material_id' ");
        foreach($propertys as $property) {
            $property_id = $property['id'];
            $this->delete("DELETE FROM orderm_material_property WHERE id='$property_id' ");
        }
    }

    /**
     * Method thah return orders by project ID
     * 
     * @param integer $project_id
     * 
     * @return array
     */
    public function getOrdersByProjectId($project_id) {
        $result = $this->get("SELECT orderm.id, orderm.o_id, orderm.date, orderm.project_id, orderm.title, orderm.status, orderm.is_archived, client.name as supplier_name "
                        . "FROM orderm "
                        . "JOIN (client) "
                        . "ON (orderm.supplier_id = client.id) "
                        . "WHERE (orderm.project_id = $project_id) "
                        . "ORDER BY orderm.id DESC ");
        return $result;
    }

    /**
     * Method that duplicate material in order
     * 
     * @param integer $order_material_id
     */
    public function duplicateMaterialInOrder($order_material_id){

        // get material by $order_material_id
        $materialInOrder = $this->getMaterialInOrder($order_material_id);

        $order_id = $materialInOrder['order_id'];
        $material_id = $materialInOrder['material_id'];
        $note = $materialInOrder['note'];
        $pieces = $materialInOrder['pieces'];
        $price = $materialInOrder['price'];
        $tax = $materialInOrder['tax'];
        $weight = $materialInOrder['weight'];
        
        $this->insert("INSERT INTO orderm_material (order_id, material_id, note, pieces, price, tax, weight) " 
                    . " VALUES ('$order_id', '$material_id', '$note', '$pieces', '$price', '$tax', '$weight' )");
        
        // treba nam i order_material_id (id materiala u order dokumentu) to je u stvari zadnji unos
        $order_material_id = $this->connection->insert_id;

        //insert property-a materiala u tabelu orderm_material_property
        $propertys = $this->get( "SELECT * FROM material_property WHERE material_id ='$material_id'");
        foreach($propertys as $property){
            $property_id = $property['property_id'];
            $quantity = 0;
            $this->insert("INSERT INTO orderm_material_property (orderm_material_id, property_id, quantity) " 
                            . " VALUES ('$order_material_id', '$property_id', '$quantity' )");
        }

    }

}
