<?php
/**
 * Description of class Contact
 *
 * @author Dragan Jancikin
 */

class Contact {
    
    // first define properties
    
    // id kontakta
    private $id;
    
    // id klijenta kome pripda kontakt
    private $client_id;
    
    // id tipa kontakta
    private $type_id;
    
    // kontakt: telefonski broj, e-mail, web adresa, fax, mobilni
    private $number;
    
    // beleška uz kontakt
    private $note;
    
    // metoda koja se automatski izvršava pri generisanju objekta Contact
    public function __construct() {
        // treba konektovati na bazu preko klase koja vrši konekciju
        $db = new DB();
        $this->connection = $db->connectDB();
    }
    

    // metoda koja daje sve kontakte klijenta
    public function getContactsById($id){
        
        $contact = array();
        $contacts = array();
        
        // izlistavanje iz baze slih kontakata klijenata sa client_id
        $result = mysqli_query($this->connection,"SELECT client_contacts.contact_id, contacts.id, contacts.type_id, contacts.number, contacts.note, contacttypes.id, contacttypes.name  "
                                                . "FROM client_contacts "
                                                . "JOIN (contacts, contacttypes)"
                                                . "ON (client_contacts.contact_id = contacts.id AND contacts.type_id = contacttypes.id )"
                                                . "WHERE client_contacts.client_id = $id "
                                                . "ORDER BY contacttypes.id ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
         
            $id = $row['contact_id'];
            $type_id = $row['type_id'];
            $type_name = $row['name'];
            $number = $row['number'];
            $note = $row['note'];

            $contact = array(
                'id' => $id,
                'type_id' => $type_id,
                'type_name' => $type_name,
                'number' => $number,
                'note' => $note
            );
 
            array_push($contacts, $contact);
           
        endwhile;
        
        return $contacts;
    }

    
    // metoda koja daje sve tipove kontakata
    public function getContactTypes (){
        
        $type = array();
        $types = array();
        
        // sada treba isčitati sva naselja iz tabele city
        $result = mysqli_query($this->connection, "SELECT id, name FROM contacttypes ORDER BY id" ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){
         
            $id = $row['id'];
            $name = $row['name'];

            $type = array(
                'id' => $id,
                'name' => $name
            );
 
            array_push($types, $type);
        }
        
        return $types;
    }
    
    
    // metoda koja briše kontakt
    public function delContact($client_id, $contact_id){
        
        $this->connection->query("DELETE FROM contacts WHERE id='$contact_id' ") or die(mysqli_error($this->connection));
        
        $this->connection->query("DELETE FROM client_contacts WHERE contact_id='$contact_id' ") or die(mysqli_error($this->connection));
        
    }
    
}
