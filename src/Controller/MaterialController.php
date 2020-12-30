<?php

namespace Roloffice\Controller;

use Roloffice\Core\Database;

/**
 * Material class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class MaterialController extends Database {

    protected $id;
    protected $name;
    protected $unit_id;
    protected $weight;
    protected $min_obrac_mera;
    protected $price;
    protected $note;
    protected $date;

    /**
     * Method that return all material with name like $name
     * 
     * @param string $name
     * 
     * @return array
     */
    public function search($name) {
        $result = $this->get("SELECT material.id, material.name, material.note, unit.name as unit_name, material_suppliers.price, material_suppliers.client_id, client.name as client_name "
                            . "FROM material "
                            . "JOIN (unit, material_suppliers, client) "
                            . "ON (material.unit_id = unit.id AND material.id = material_suppliers.material_id AND client.id = client_id) "
                            . "WHERE (material.name LIKE '%$name%') "
                            . "ORDER BY material.name ");
        return $result;
    }

    /**
     * Method that return material price
     * 
     * @param integer $material_id
     * 
     * @return double
     */
    public function getPrice($material_id) {
        $result = $this->get("SELECT price FROM material WHERE id = $material_id");
        return $result[0]['price'];
    }

    /**
     * Method that return material ba ID
     * 
     * @param integer $material_id
     * 
     * @return array
     */
    public function getMaterial($material_id) {
        $result = $this->get("SELECT material.id, material.name, material.unit_id, material.weight, material.price, material.note, unit.name as unit_name "
                            . "FROM material "
                            . "JOIN (unit) "
                            . "ON (material.unit_id = unit.id) "
                            . "WHERE material.id = $material_id ");
        if(empty($result)) {
            die('<script>location.href = "/materials/" </script>');
        } else {
            return $result[0];
        }
    }

    /**
     * Method that return all materials from table material
     * 
     * @return array
     */
    public function getMaterials (){
        $result = $this->get("SELECT id, name,note FROM material ORDER BY name");
        return $result;
    }

    /**
     * Method that return all unit
     * 
     * @return array
     */
    public function getUnits (){
        $result = $this->get("SELECT * FROM unit");
        return $result;
    }

    /**
     * Method thah return last materials
     * 
     * @param integer $limit
     * 
     * @return array
     */
    public function getLastMaterials($limit){
        $result = $this->get("SELECT material.id, material.name, unit.name as unit_name, material_suppliers.price, material_suppliers.client_id, client.name as client_name "
                            . "FROM material "
                            . "JOIN (unit, material_suppliers, client)"
                            . "ON (material.unit_id = unit.id AND material.id = material_suppliers.material_id AND client_id = client.id)"
                            . "ORDER BY material.id DESC LIMIT $limit");
        return $result;
    }

    /**
     * Method that return all suppliers from table client
     * 
     * @return array
     */
    public function getSuppliers (){
        $result = $this->get("SELECT * FROM client WHERE is_supplier = 1");
        return $result;
    }

    /**
     * Method that return all material suppliers
     * 
     * @param integer $material_id
     * 
     * @return array
     */
    public function getMaterialSuppliers ($material_id){
        $result = $this->get("SELECT client.id, client.name, material_suppliers.code, material_suppliers.price "
                            . "FROM material_suppliers "
                            . "JOIN (client) "
                            . "ON (material_suppliers.material_id = $material_id AND material_suppliers.client_id = client.id) "
                            . "ORDER BY client.name");
        return $result;
    }

    /**
     * Method that delete supplier of material
     * 
     * @param integer $material_id
     * @param integer $client_id
     */
    public function delMaterialSupplier($material_id, $client_id){
        $this->connection->query("DELETE FROM material_suppliers WHERE material_id='$material_id' AND client_id='$client_id' ") or die(mysqli_error($this->connection));
    }

    /**
     * Method that return propertys by material ID
     * 
     * @param integer $material_id
     * 
     * @return array
     */
    public function getPropertysByMaterialId($material_id){
        $result = $this->get("SELECT property.id, property.name "
                            . "FROM material_property "
                            . "JOIN (property) "
                            . "ON (material_property.property_id = property.id) "
                            . "WHERE material_property.material_id = $material_id ");
        return $result;
    }

    /**
     * Method that return all propertys from table property
     * 
     * @return array
     */
    public function getPropertys (){
        $result = $this->get("SELECT * FROM property");
        return $result;
    }

    /**
     * Method that delete material property
     * 
     * @param integer $material_id
     * @param integer $property_id
     */
    public function delMaterialProperty($material_id, $property_id) {
        $this->connection->query("DELETE FROM material_property "
                                ."WHERE ( material_id='$material_id' AND property_id='$property_id') ") or die(mysqli_error($this->connection));
    }

}
