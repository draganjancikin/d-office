<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Client class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class Client extends DB {

    protected $id;
    protected $vps_id;
    protected $name;
    protected $name_note;
    protected $lb;
    protected $is_supplier;
    protected $state_id;
    protected $city_id;
    protected $street_id;
    protected $home_number;
    protected $address_note;
    protected $note;
    protected $created_at_date;
    protected $created_at_user_id;
    protected $modified_at_date;
    protected $modified_at_user_id;


    // get client types
    public function getVpses (){
        return array(
            array('id'=>'1', 'name'=>'Fizičko lice'),
            array('id'=>'2', 'name'=>'Pravno lice')
        );
    }


    public function getStates (){
        return $this->get("SELECT * FROM state ORDER BY name");
    }


    public function getCitys (){
        return $this->get("SELECT * FROM city ORDER BY name");
    }


    public function getCity ($id){
        $result =  $this->get("SELECT * FROM city WHERE id = $id");
        return ( empty($result[0]) ? false : $result[0] );
    }

    
    public function getStreets (){
        return $this->get("SELECT * FROM street ORDER BY name");
    }


    //metoda koja vraća naziv klijenta-e u zavisnosti od datog pojma u pretrazi
    public function search($name){

        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT client.id, client.name, client.name_note, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number "
                                         . "FROM client "
                                         . "JOIN (street, city, state)"
                                         . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                         . "WHERE (client.name LIKE '%$name%' OR client.name_note LIKE '%$name%') "
                                         . "ORDER BY client.name ") or die(mysqli_error($this->connection));
        $result -> fetch_all(MYSQLI_ASSOC);
        return $result;
    }


    // metoda koja vraća sve podatke o kllijent-u na osnovu client_id
    public function getClient($id){
        if($id == 0) {

        } else {
            // izlistavanje iz baze slih klijenata sa id = $id
            $result = $this->connection->query("SELECT client.id, client.vps_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number, client.address_note, client.note "
                                            . "FROM client "
                                            . "JOIN (street, city, state)"
                                            . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                            . "WHERE client.id = $id ") or die(mysqli_error($this->connection));
            $row = $result->fetch_assoc();
                $vps_id = $row['vps_id'];
                if($vps_id == 1){
                    $vps_name = "Fizičko lice";
                }else{
                    $vps_name = "Pravno lice";
                }
                $client = array(
                    'id' => $row['id'],
                    'vps_id' => $vps_id,
                    'vps_name' => $vps_name,
                    'name' => $row['name'],
                    'name_note' => $row['name_note'],
                    'pib' => $row['lb'],
                    'is_supplier' => $row['is_supplier'],
                    'state_id' => $row['state_id'],
                    'state_name' => $row['state_name'],
                    'city_id' => $row['city_id'],
                    'city_name' => $row['city_name'],
                    'street_id' => $row['street_id'],
                    'street_name' => $row['street_name'],
                    'home_number' => $row['home_number'],
                    'address_note' => $row['address_note'],
                    'note' => $row['note']
                );

            return $client;
        }
        
    }


    // metoda koja vraća sve podatke o supplier-u na osnovu supplier_id
    public function getSupplier($id){

        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT client.id, client.vps_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number, client.address_note, client.note "
                                        . "FROM client "
                                        . "JOIN (street, city, state)"
                                        . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id AND client.is_supplier = 1)"
                                        . "WHERE client.id = $id ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();
            $vps_id = $row['vps_id'];
            if($vps_id == 1){
                $vps_name = "Fizičko lice";
            }else{
                $vps_name = "Pravno lice";
            }
            $supplier = array(
                'id' => $row['id'],
                'vps_id' => $vps_id,
                'vps_name' => $vps_name,
                'name' => $row['name'],
                'name_note' => $row['name_note'],
                'pib' => $row['lb'],
                'is_supplier' => $row['is_supplier'],
                'state_id' => $row['state_id'],
                'state_name' => $row['state_name'],
                'city_id' => $row['city_id'],
                'city_name' => $row['city_name'],
                'street_id' => $row['street_id'],
                'street_name' => $row['street_name'],
                'home_number' => $row['home_number'],
                'address_note' => $row['address_note'],
                'note' => $row['note']
            );

        return $supplier;
    }


    // metoda koja daje sve klijente
    public function getClients (){

        // sada treba isčitati sve klijente iz tabele client
        $result = $this->connection->query("SELECT client.id, client.vps_id, client.name, city.name as city_name "
                                         . "FROM client "
                                         . "JOIN (city) "
                                         . "ON (client.city_id = city.id) "
                                         . "ORDER BY name" ) or die(mysqli_error($this->connection));
        $result -> fetch_all(MYSQLI_ASSOC);
        return $result;
    }


    // metoda koja daje sve suppliers-e
    public function getSuppliers (){
        return $this->get("SELECT id, name, is_supplier FROM client WHERE is_supplier = 1 ORDER BY name");
    }
    

    //metoda koja daje zadnjih $number klijenata upisanih u bazu
    public function getLastClients($limit){

        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT client.id, client.name, client.name_note, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number "
                                         . "FROM client "
                                         . "JOIN (street, city, state)"
                                         . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                         
                                         . "ORDER BY client.id DESC LIMIT $limit") or die(mysqli_error($this->connection));
        $result -> fetch_all(MYSQLI_ASSOC);
        return $result;
    }

}

/*
class Products extends DbConnect {

    protected $id;
    protected $sku;
    protected $name;
    protected $price;
    protected $type;
    protected $attributes;

    

    public function select() {
        $query = "SELECT * FROM products";
        $result = $this->connect()->query($query);

        $row = $result->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['Id'];
        $this->sku = $row['SKU'];
        $this->name = $row['Name'];
        $this->price = $row['Price'];
        $this->type = $row['Type'];
        $this->attributes = $row['Attributes'];
    }

    public function getId() {
        return $this->id;
    }

    public function getSKU() {
        return $this->sku;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getType() {
        return $this->type;
    }

    public function getAttributes() {
        return $this->attributes;
    }

}



PHP

class Person{
    private $firstname = null;
    public function setFirstName($aFirstName){
         $this->firstname = $aFirstName;
    }

    public function getFirstName(){
         return $this->firstname;
    }
}

$person = new Person();

if(isset($_POST['submit'])){
    $person->setFirstName($_POST['firstname']);
}
HTML

<form action="" method="post">
    First Name: <input type="text" name="firstname" value="<?php if($person->getFirstName() != null){ echo $person->getFirstName(); } ?>"/>
    <input type="submit" name="submit" value="Submit" />
</form>
*/