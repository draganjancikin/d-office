<?php
/**
 * Description of Client class
 *
 * @author Server
 */

class Client {
    
    // first define properties
    
    // $id je id klijenta
    private $id;
    
    // $vps_id je id vrste pravnog subjekta (fizičko ili pravno lice)
    private $vps_id;
    
    // $name je naziv (ime) klijenta
    private $name;
    
    // $name_note je beleška uz naziv (ime) klijenta
    private $name_note;
    
    // $lb je lični broj, ako je fizičko lice to je JMBG, ako je firma to je PIB
    private $lb;
    
    // $state_id je id države iz koje je klijent
    private $state_id;
    
    // $city_id je id grada iz kojeg je klijent
    private $city_id;
    
    // $street_id je id ulice gde se klijent nalazi (živi)
    private $street_id;
    
    // $home_number je broj kuće (zgrade) gde se klijent nalazi (živi)
    private $home_number;
    
    // $adress_note je beleška uz adresu
    private $adress_note;
    
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
    
    
    // then define methods (functions)
    
    // metoda koja se automatski izvršava pri generisanju objekta Client
    public function __construct() {
        // treba konektovati na bazu preko klase koja vrši konekciju
        $db = new DB();
        $this->connection = $db->connectDB();
    }
    
    
    // metoda koja daje vps_id klijenata
    public function getVpses (){
        
        $vpses = array(
            array('id'=>'1', 'name'=>'Fizičko lice'),
            array('id'=>'2', 'name'=>'Pravno lice'),
        );
        
        return $vpses;
    }
    
    
    // metoda koja daje sve države
    public function getStates (){
        
        $state = array();
        $states = array();
        
        // sada treba isčitati sve države iz tabele state
        $result = $this->connection->query("SELECT id, state_name FROM state ORDER BY state_name" ) or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){
            
            $id = $row['id'];
            $state_name = $row['state_name'];
            
            $state = array(
                'id' => $id,
                'name' => $state_name
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
        $result = $this->connection->query("SELECT id, city_name FROM city ORDER BY city_name" ) or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){
            
            $id = $row['id'];
            $city_name = $row['city_name'];
            
            $city = array(
                'id' => $id,
                'name' => $city_name
            );
            
            array_push($citys, $city);
        }
        
        return $citys;
    }
    
    
    // metoda koja daje sva naselja
    public function getCity ($id){
        
        $city = array();
        $citys = array();
        
        // sada treba isčitati sva naselja iz tabele city
        $result = $this->connection->query("SELECT id, city_name FROM city WHERE id = $id ORDER BY city_name" ) or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);
            
            $id = $row['id'];
            $city_name = $row['city_name'];

        
        return $city_name;
    }
    
    
    // metoda proverava da li ostoji naselje sa dati id-em
    public function checkCity ($id){
        
        $result = $this->connection->query("SELECT city_name FROM city WHERE id = $id") or die(mysqli_error($this->connection));
            $row = mysqli_fetch_array($result);
            
            // var_dump($row);
            
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
        $result = $this->connection->query("SELECT id, street_name FROM street ORDER BY street_name" ) or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){
            
            $id = $row['id'];
            $street_name = $row['street_name'];
            
            $street = array(
                'id' => $id,
                'name' => $street_name
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
        $result = $this->connection->query("SELECT client.id, client.name, client.name_note, state.state_name, city.city_name, street.street_name, client.home_number "
                                         . "FROM client "
                                         . "JOIN (street, city, state)"
                                         . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                         . "WHERE (client.name LIKE '%$name%' OR client.name_note LIKE '%$name%') "
                                         . "ORDER BY client.name ") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):
            $id = $row['id'];
            $name = $row['name'];
            $street_name = $row['street_name'];
            $home_number = $row['home_number'];
            $city_name = $row['city_name'];
            $state_name = $row['state_name'];
            
            $client = array(
                'id' => $id,
                'name' => $name,
                'state_name' => $state_name,
                'city_name' => $city_name,
                'street_name' => $street_name,
                'home_number' => $home_number
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
            $result = $this->connection->query("SELECT client.id, client.vps_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.state_name, city.city_name, street.street_name, client.home_number, client.adress_note, client.note "
            . "FROM client "
            . "JOIN (street, city, state)"
            . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
            . "WHERE client.id = $id ") or die(mysqli_error($this->connection));
            $row = mysqli_fetch_array($result);
            $id = $row['id'];
            $vps_id = $row['vps_id'];
            if($vps_id == 1){
            $vps_name = "Fizičko lice";
            }else{
            $vps_name = "Pravno lice";
            }
            $name = $row['name'];
            $name_note = $row['name_note'];
            $pib = $row['lb'];
            $is_supplier = $row['is_supplier'];
            $street_id = $row['street_id'];
            $street_name = $row['street_name'];
            $home_number = $row['home_number'];
            $city_id = $row['city_id'];
            $city_name = $row['city_name'];
            $state_id = $row['state_id'];
            $state_name = $row['state_name'];
            $adress_note = $row['adress_note'];
            $note = $row['note'];

            $client = array(
            'id' => $id,
            'vps_id' => $vps_id,
            'vps_name' => $vps_name,
            'name' => $name,
            'name_note' => $name_note,
            'pib' => $pib,
            'is_supplier' => $is_supplier,
            'state_id' => $state_id,
            'state_name' => $state_name,
            'city_id' => $city_id,
            'city_name' => $city_name,
            'street_id' => $street_id,
            'street_name' => $street_name,
            'home_number' => $home_number,
            'adress_note' => $adress_note,
            'note' => $note
            );

            return $client;
        }
        
    }
    

    // metoda koja vraća sve podatke o supplier-u na osnovu supplier_id
    public function getSupplier($id){
        
        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT client.id, client.vps_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.state_name, city.city_name, street.street_name, client.home_number, client.adress_note, client.note "
                                        . "FROM client "
                                        . "JOIN (street, city, state)"
                                        . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id AND client.is_supplier = 1)"
                                        . "WHERE client.id = $id ") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);
            $id = $row['id'];
            $vps_id = $row['vps_id'];
            if($vps_id == 1){
                $vps_name = "Fizičko lice";
            }else{
                $vps_name = "Pravno lice";
            }
            $name = $row['name'];
            $name_note = $row['name_note'];
            $pib = $row['lb'];
            $is_supplier = $row['is_supplier'];
            $street_id = $row['street_id'];
            $street_name = $row['street_name'];
            $home_number = $row['home_number'];
            $city_id = $row['city_id'];
            $city_name = $row['city_name'];
            $state_id = $row['state_id'];
            $state_name = $row['state_name'];
            $adress_note = $row['adress_note'];
            $note = $row['note'];
            
            $client = array(
                'id' => $id,
                'vps_id' => $vps_id,
                'vps_name' => $vps_name,
                'name' => $name,
                'name_note' => $name_note,
                'pib' => $pib,
                'is_supplier' => $is_supplier,
                'state_id' => $state_id,
                'state_name' => $state_name,
                'city_id' => $city_id,
                'city_name' => $city_name,
                'street_id' => $street_id,
                'street_name' => $street_name,
                'home_number' => $home_number,
                'adress_note' => $adress_note,
                'note' => $note
            );
            
        return $client;
    }

    
    // metoda koja daje sve klijente
    public function getClients (){
        
        $client = array();
        $clients = array();
        
        // sada treba isčitati sve klijente iz tabele client
        $result = $this->connection->query("SELECT client.id, client.vps_id, client.name, city.city_name "
                                         . "FROM client "
                                         . "JOIN (city) "
                                         . "ON (client.city_id = city.id) "
                                         . "ORDER BY name" ) or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){
            
            $id = $row['id'];
            $vps_id = $row['vps_id'];
            $name = $row['name'];
            $city_name = $row['city_name'];
            
            $client = array(
                'id' => $id,
                'vps_id' => $vps_id,
                'name' => $name,
                'city_name' => $city_name
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
        $result = $this->connection->query("SELECT client.id, client.vps_id, client.name, client.is_supplier, city.city_name "
                                            . "FROM client "
                                            . "JOIN (city) "
                                            . "ON (client.city_id = city.id AND client.is_supplier = 1) "
                                            . "ORDER BY name" ) or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)){
            
            $id = $row['id'];
            $vps_id = $row['vps_id'];
            $name = $row['name'];
            $city_name = $row['city_name'];
            
            $client = array(
                'id' => $id,
                'vps_id' => $vps_id,
                'name' => $name,
                'city_name' => $city_name
            );
            
            array_push($clients, $client);
        }
        
        return $clients;
    }
    
    
    //metoda koja daje zadnjih $number klijenata upisanih u bazu
    public function getLastClients($limit){
        
        $client = array();
        $clients = array();
        
        // izlistavanje iz baze slih klijenata sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT client.id, client.name, client.name_note, state.state_name, city.city_name, street.street_name, client.home_number "
                                         . "FROM client "
                                         . "JOIN (street, city, state)"
                                         . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                                         
                                         . "ORDER BY client.id DESC LIMIT $limit") or die(mysqli_error($this->connection));
        while($row = mysqli_fetch_array($result)):
            $id = $row['id'];
            $name = $row['name'];
            $street_name = $row['street_name'];
            $home_number = $row['home_number'];
            $city_name = $row['city_name'];
            $state_name = $row['state_name'];
            
            $client = array(
                'id' => $id,
                'name' => $name,
                'state_name' => $state_name,
                'city_name' => $city_name,
                'street_name' => $street_name,
                'home_number' => $home_number
            );
            
            array_push($clients, $client);
            
        endwhile;
        
        return $clients;
    }
    
}
