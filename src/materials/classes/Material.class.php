<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Material.class.php
 * 
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

    
    //metoda koja vraća artikle u zavisnosti od datog pojma u pretrazi
    public function search($name){

        $material = array();
        $materials = array();

        // izlistavanje iz baze svih artikala sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT material.id, material.name, material.note, unit.name as unit_name, material_suppliers.price, material_suppliers.client_id "
                                         . "FROM material "
                                         . "JOIN (unit, material_suppliers) "
                                         . "ON (material.unit_id = unit.id AND material.id = material_suppliers.material_id) "
                                         . "WHERE (material.name LIKE '%$name%') "
                                         . "ORDER BY material.name ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $client_id = $row['client_id'];
            $result_client = $this->connection->query("SELECT client.name "
                                         . "FROM client "
                                         . "WHERE id = $client_id ") or die(mysqli_error($this->connection));
            $row_client = mysqli_fetch_array($result_client);
            $material = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'note' => $row['note'],
                'unit_name' => $row['unit_name'],
                'price' => $row['price'],
                'client_name' => $row_client['name']
            );
            array_push($materials, $material);
        endwhile;

        return $materials;
    }


    // metoda koja daje cenu materijala
    public function getPrice ($material_id){
        return $this->get("SELECT price FROM material WHERE id = $material_id")[0]['price'];
    }


    //metoda koja vraća podatke o materialu 
    public function getMaterial($material_id){

        $result = $this->connection->query("SELECT material.id, material.name, material.unit_id, material.weight, material.price, material.note, unit.name as unit_name "
                                         . "FROM material "
                                         . "JOIN (unit) "
                                         . "ON (material.unit_id = unit.id) "
                                         . "WHERE material.id = $material_id ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();
            $material = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'unit_id' => $row['unit_id'],
                'unit_name' => $row['unit_name'],
                'weight' => $row['weight'],
                'price' => $row['price'],
                'note' => $row['note']
            );

        return $material;
    }


    public function getMaterials (){
        return $this->get("SELECT id, name,note FROM material ORDER BY name");
    }
    

    public function getUnits (){
        return $this->get("SELECT * FROM unit");
    }
    

    //metoda koja daje zadnjih $number materijala upisanih u bazu
    public function getLastMaterials($limit){

        $material = array();
        $materials = array();

        // izlistavanje zadnjih $limit materijala
        $result = $this->connection->query("SELECT material.id, material.name, unit.name as unit_name, material_suppliers.price, material_suppliers.client_id "
                                         . "FROM material "
                                         . "JOIN (unit, material_suppliers)"
                                         . "ON (material.unit_id = unit.id AND material.id = material_suppliers.material_id)"
                                         . "ORDER BY material.id DESC LIMIT $limit") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $client_id = $row['client_id'];
            $result_client = $this->connection->query("SELECT client.name "
                                         . "FROM client "
                                         . "WHERE id = $client_id ") or die(mysqli_error($this->connection));
            $row_client = $result_client->fetch_assoc();
            $material = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'unit_name' => $row['unit_name'],
                'price' => $row['price'],
                'client_name' => $row_client['name']
            );
            array_push($materials, $material);
        endwhile;

        return $materials;
    }


    public function getSuppliers (){
        return $this->get("SELECT * FROM client WHERE is_supplier = 1");
    }
    

    public function getMaterialSuppliers($material_id){

        $supplier = array();
        $suppliers = array();

        // izlistavanje zadnjih $limit materijala
        $result = $this->connection->query("SELECT client.id, client.name, material_suppliers.code, material_suppliers.price "
                                         . "FROM material_suppliers "
                                         . "JOIN (client) "
                                         . "ON (material_suppliers.material_id = $material_id AND material_suppliers.client_id = client.id) "
                                         . "ORDER BY client.name") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $supplier = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'code' => $row['code'],
                'price' => $row['price']
            );
            array_push($suppliers, $supplier);
        endwhile;

        return $suppliers;
    }


    // metoda koja briše dobavljač materijala
    public function delMaterialSupplier($material_id, $client_id){
        $this->connection->query("DELETE FROM material_suppliers WHERE material_id='$material_id' AND client_id='$client_id' ") or die(mysqli_error($this->connection));
    }


    // metoda koja vraća property-je materijala, ako postoje, na osnovu material_id-a
    public function getPropertyById($material_id){

        $property = array();
        $propertys = array();

        // sada treba isčitati property-je  materijala na osnovu material_id-a
        $result = $this->connection->query("SELECT property.id, property.name "
                                         . "FROM material_property "
                                         . "JOIN (property) "
                                         . "ON (material_property.property_id = property.id) "
                                         . "WHERE material_property.material_id = $material_id "  ) or die(mysqli_error($this->connection));

        while($row = $result->fetch_assoc()){
            $property = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
            array_push($propertys, $property);
        }

        return $propertys;
    }


    // metoda koja vraća property-je
    public function getPropertys (){
        return $this->get("SELECT * FROM property");
    }
    

    // metoda koja briše osobinu materijala
    public function delMaterialProperty($material_id, $property_id) {
        $this->connection->query("DELETE FROM material_property "
                                ."WHERE ( material_id='$material_id' AND property_id='$property_id') ") or die(mysqli_error($this->connection));
    }

}
