<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Material class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */
class Material extends DB {

    protected $id;
    protected $name;
    protected $unit_id;
    protected $weight;
    protected $min_obrac_mera;
    protected $price;
    protected $note;
    protected $date;


    public function search($name) {
        return $this->get("SELECT material.id, material.name, material.note, unit.name as unit_name, material_suppliers.price, material_suppliers.client_id, client.name as client_name "
                        . "FROM material "
                        . "JOIN (unit, material_suppliers, client) "
                        . "ON (material.unit_id = unit.id AND material.id = material_suppliers.material_id AND client.id = client_id) "
                        . "WHERE (material.name LIKE '%$name%') "
                        . "ORDER BY material.name ");
    }


    public function getPrice($material_id) {
        return $this->get("SELECT price FROM material WHERE id = $material_id")[0]['price'];
    }


    public function getMaterial($material_id) {
        $result = $this->get("SELECT material.id, material.name, material.unit_id, material.weight, material.price, material.note, unit.name as unit_name "
                            . "FROM material "
                            . "JOIN (unit) "
                            . "ON (material.unit_id = unit.id) "
                            . "WHERE material.id = $material_id ");
        return $result[0];
    }


    public function getMaterials (){
        return $this->get("SELECT id, name,note FROM material ORDER BY name");
    }


    public function getUnits (){
        return $this->get("SELECT * FROM unit");
    }


    public function getLastMaterials($limit){
        return $this->get("SELECT material.id, material.name, unit.name as unit_name, material_suppliers.price, material_suppliers.client_id, client.name as client_name "
                        . "FROM material "
                        . "JOIN (unit, material_suppliers, client)"
                        . "ON (material.unit_id = unit.id AND material.id = material_suppliers.material_id AND client_id = client.id)"
                        . "ORDER BY material.id DESC LIMIT $limit");
    }


    public function getSuppliers (){
        return $this->get("SELECT * FROM client WHERE is_supplier = 1");
    }


    public function getMaterialSuppliers ($material_id){
        return $this->get("SELECT client.id, client.name, material_suppliers.code, material_suppliers.price "
                        . "FROM material_suppliers "
                        . "JOIN (client) "
                        . "ON (material_suppliers.material_id = $material_id AND material_suppliers.client_id = client.id) "
                        . "ORDER BY client.name");
    }


    // metoda koja briše dobavljač materijala
    public function delMaterialSupplier($material_id, $client_id){
        $this->connection->query("DELETE FROM material_suppliers WHERE material_id='$material_id' AND client_id='$client_id' ") or die(mysqli_error($this->connection));
    }


    public function getPropertysByMaterialId($material_id){
        return $this->get("SELECT property.id, property.name "
                        . "FROM material_property "
                        . "JOIN (property) "
                        . "ON (material_property.property_id = property.id) "
                        . "WHERE material_property.material_id = $material_id ");
    }


    public function getPropertys (){
        return $this->get("SELECT * FROM property");
    }


    public function delMaterialProperty($material_id, $property_id) {
        $this->connection->query("DELETE FROM material_property "
                                ."WHERE ( material_id='$material_id' AND property_id='$property_id') ") or die(mysqli_error($this->connection));
    }

}
