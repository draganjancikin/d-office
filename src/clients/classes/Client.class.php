<?php
/**
 * Description of Client class
 *
 * @author Dragan Jancikin
 */

class Client {

    // client id
    private $id;

    // type of client (person or company)
    private $vps_id;

    // client name
    private $name;

    // name note
    private $name_note;

    // personal number or companu tax number 
    private $lb;

    // client state id
    private $state_id;

    // client city id
    private $city_id;

    // client street id
    private $street_id;

    // home number
    private $home_number;

    // address note
    private $address_note;

    // $note je beleška uz klijenta
    private $note;

    // $created_at_date je datum-vreme kada je klijent kreiran
    private $created_at_date;

    // $created_at_user_id id usewr-a koji je kreirao klijenta
    private $created_at_user_id;

    // $modified_at_date je datum-vreme kada je klijent menjan
    private $modified_at_date;

    // $modified_at_user_id id user-a koji je menjao klijenta
    private $modified_at_user_id;


    // metoda koja se automatski izvršava pri generisanju objekta Client
    public function __construct() {
        // treba konektovati na bazu preko klase koja vrši konekciju
        $db = new DB();
        $this->conn = $db->connectDB();
    }


    // get client types
    public function getVpses (){
        $vpses = array(
            array('id'=>'1', 'name'=>'Fizičko lice'),
            array('id'=>'2', 'name'=>'Pravno lice'),
        );
        return $vpses;
    }


    // get all states from database
    public function getStates (){

        $state = array();
        $states = array();

        // sada treba isčitati sve države iz tabele state
        $result = $this->conn->query("SELECT id, name FROM state ORDER BY name" ) or die(mysqli_error($this->conn));
        while($row = $result->fetch_assoc()){    
            $state = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
            array_push($states, $state);
        }

        return $states;
    }


    // metoda koja daje sva naselja
    public function getCitys (){

        $city = array();
        $citys = array();

        // sada treba isčitati sva naselja iz tabele city
        $result = $this->conn->query("SELECT * FROM city ORDER BY name" ) or die(mysqli_error($this->conn));
        while($row = $result->fetch_assoc()){
            $city = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
            array_push($citys, $city);
        }

        return $citys;
    }


    // metoda koja daje naselje
    public function getCity ($id){

        // sada treba isčitati sva naselja iz tabele city
        $result = $this->conn->query("SELECT id, name FROM city WHERE id = $id ORDER BY name" ) or die(mysqli_error($this->conn));
        $row = $result->fetch_assoc();

            $id = $row['id'];
            $city_name = $row['name'];

        return $city_name;
    }


    // metoda proverava da li postoji naselje sa dati id-em
    public function checkCity ($id){

        $result = $this->conn->query("SELECT name FROM city WHERE id = $id") or die(mysqli_error($this->conn));
            $row = $result->fetch_assoc();

            if (empty($row)) {
                $rezultat = false;
            }else {
                $rezultat = true;
            }

        return $rezultat;
    }


    // metoda koja daje sve ulice
    public function getStreets (){

        $street = array();
        $streets = array();

        // sada treba isčitati sve ulice iz tabele street
        $result = $this->conn->query("SELECT id, name FROM street ORDER BY name" ) or die(mysqli_error($this->conn));
        while($row = $result->fetch_assoc()){
            $street = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
            array_push($streets, $street);
        }

        return $streets;
    }


    //metoda koja vraća naziv klijenta-e u zavisnosti od datog pojma u pretrazi
    public function search($name){

        $client = array();
        $clients = array();

        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->conn->query("SELECT client.id, client.name, client.name_note, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number "
                                         . "FROM client "
                                         . "JOIN (street, city, state)"
                                         . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                         . "WHERE (client.name LIKE '%$name%' OR client.name_note LIKE '%$name%') "
                                         . "ORDER BY client.name ") or die(mysqli_error($this->conn));
        while($row = $result->fetch_assoc()):
            $client = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'state_name' => $row['state_name'],
                'city_name' => $row['city_name'],
                'street_name' => $row['street_name'],
                'home_number' => $row['home_number']
            );
            array_push($clients, $client);
        endwhile;

        return $clients;
    }


    // metoda koja vraća sve podatke o kllijent-u na osnovu client_id
    public function getClient($id){
        if($id == 0) {

        } else {
            // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
            $result = $this->conn->query("SELECT client.id, client.vps_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number, client.address_note, client.note "
                                            . "FROM client "
                                            . "JOIN (street, city, state)"
                                            . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                            . "WHERE client.id = $id ") or die(mysqli_error($this->conn));
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
        $result = $this->conn->query("SELECT client.id, client.vps_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number, client.address_note, client.note "
                                        . "FROM client "
                                        . "JOIN (street, city, state)"
                                        . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id AND client.is_supplier = 1)"
                                        . "WHERE client.id = $id ") or die(mysqli_error($this->conn));
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


    // metoda koja daje sve klijente
    public function getClients (){

        $client = array();
        $clients = array();

        // sada treba isčitati sve klijente iz tabele client
        $result = $this->conn->query("SELECT client.id, client.vps_id, client.name, city.city_name "
                                         . "FROM client "
                                         . "JOIN (city) "
                                         . "ON (client.city_id = city.id) "
                                         . "ORDER BY name" ) or die(mysqli_error($this->conn));
        while($row = $result->fetch_assoc()){
            $client = array(
                'id' => $row['id'],
                'vps_id' => $row['vps_id'],
                'name' => $row['name'],
                'city_name' => $row['city_name']
            );
            array_push($clients, $client);
        }

        return $clients;
    }


    // metoda koja daje sve suppliers-e
    public function getSuppliers (){

        $client = array();
        $clients = array();

        // sada treba isčitati sve klijente iz tabele client
        $result = $this->conn->query("SELECT client.id, client.vps_id, client.name, client.is_supplier, city.name as city_name "
                                            . "FROM client "
                                            . "JOIN (city) "
                                            . "ON (client.city_id = city.id AND client.is_supplier = 1) "
                                            . "ORDER BY name" ) or die(mysqli_error($this->conn));
        while($row = $result->fetch_assoc()):
            $client = array(
                'id' => $row['id'],
                'vps_id' => $row['vps_id'],
                'name' => $row['name'],
                'city_name' => $row['city_name']
            );
            array_push($clients, $client);
        endwhile;

        return $clients;
    }


    //metoda koja daje zadnjih $number klijenata upisanih u bazu
    public function getLastClients($limit){

        $client = array();
        $clients = array();

        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->conn->query("SELECT client.id, client.name, client.name_note, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number "
                                         . "FROM client "
                                         . "JOIN (street, city, state)"
                                         . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                         
                                         . "ORDER BY client.id DESC LIMIT $limit") or die(mysqli_error($this->conn));
        while($row = $result->fetch_assoc()):
            $client = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'state_name' => $row['state_name'],
                'city_name' => $row['city_name'],
                'street_name' => $row['street_name'],
                'home_number' => $row['home_number']
            );
            array_push($clients, $client);
        endwhile;

        return $clients;
    }

}
