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

    /**
     * Method that return client types
     * 
     * @return array
     */
    public function getVpses() {
        return array(
            array('id'=>'1', 'name'=>'Fizičko lice'),
            array('id'=>'2', 'name'=>'Pravno lice')
        );
    }

    /**
     * Method that return all states from table state
     * 
     * @return array
     */
    public function getStates() {
        $result = $this->get("SELECT * FROM state ORDER BY name");
        return $result;
    }

    /**
     * Method that return all citys from table city
     * 
     * @return array
     */
    public function getCitys() {
        $result = $this->get("SELECT * FROM city ORDER BY name");
        return $result;
    }

    /**
     * Method that return city by ID
     * 
     * @param integer $id
     * 
     * @return array
     */
    public function getCity($id) {
        $result =  $this->get("SELECT * FROM city WHERE id = $id");
        return ( empty($result[0]) ? false : $result[0] );
    }

    /**
     * Method that return all streets from table street
     * 
     * @return array
     */
    public function getStreets() {
        $result = $this->get("SELECT * FROM street ORDER BY name");
        return $result;
    }

    /**
     * Method that return all client with name or name_note like $name
     * 
     * @param string $name
     * 
     * @return array
     */
    public function search($name) {
        $result = $this->get("SELECT client.id, client.name, client.name_note, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number "
                            . "FROM client "
                            . "JOIN (street, city, state) "
                            . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id ) "
                            . "WHERE (client.name LIKE '%$name%' OR client.name_note LIKE '%$name%') "
                            . "ORDER BY client.name");
        return $result;
    }

    /**
     * Method that return client data by client ID
     * 
     * @param integer $id
     * 
     * @return array
     */
    public function getClient($id) {
        $result =  $this->get("SELECT client.id, client.vps_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number, client.address_note, client.note "
                            . "FROM client "
                            . "JOIN (street, city, state)"
                            . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                            . "WHERE client.id = $id ");
        if(empty($result)) {
            die('<script>location.href = "/clients/" </script>');
        } else {
            ($result[0]['vps_id'] == 1 ? $result[0]['vps_name'] = "Fizičko lice" : $result[0]['vps_name'] = "Pravno lice" );
            return $result[0];
        }
    }

    /**
     * Method that return all clients from table client
     * 
     * @return array
     */
    public function getClients() {
        $result = $this->get("SELECT client.id, client.vps_id, client.name, city.name as city_name "
                            . "FROM client "
                            . "JOIN (city) "
                            . "ON (client.city_id = city.id) "
                            . "ORDER BY name");
        return $result;
    }
    
    /**
     * Method that return all suppliers from table client
     * 
     * @return array
     */
    public function getSuppliers() {
        $result = $this->get("SELECT id, name, is_supplier FROM client WHERE is_supplier = 1 ORDER BY name");
        return $result;
    }
    
    /**
     * Method that return last clients
     * 
     * @param integer $limit
     * 
     * @return array
     */
    public function getLastClients($limit){
        $result = $this->get("SELECT client.id, client.name, client.name_note, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number "
                        . "FROM client "
                        . "JOIN (street, city, state)"
                        . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                        . "ORDER BY client.id DESC LIMIT $limit");
        return $result;
    }

    /**
     * 
     */
    public function checkGetClient($id = FALSE){
        if($id) {
            $new = preg_replace('/[^0-9]/', '', $id);
            return $new;
        } else {
            die('<script>location.href = "/clients/" </script>');
        }
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