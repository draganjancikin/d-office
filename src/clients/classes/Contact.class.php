<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Contact class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class Contact extends DB {

    protected $id;
    protected $client_id;
    protected $type_id;
    protected $number;
    protected $note;


    /**
     * @author Dragan Jancikin <dragan.jancikin@gmail.com>
     */
    public function getContactsById($id){

        // izlistavanje iz baze slih kontakata klijenata sa client_id
        $result = $this->connection->query("SELECT client_contacts.contact_id, contacts.id, contacts.type_id, contacts.number, contacts.note, contacttypes.id, contacttypes.name "
                                                . "FROM client_contacts "
                                                . "JOIN (contacts, contacttypes)"
                                                . "ON (client_contacts.contact_id = contacts.id AND contacts.type_id = contacttypes.id )"
                                                . "WHERE client_contacts.client_id = $id "
                                                . "ORDER BY contacttypes.id ") or die(mysqli_error($this->connection));
        $result->fetch_all(MYSQLI_ASSOC);
        return $result;
    }

    /**
    * @author Dragan Jancikin <dragan.jancikin@gmail.com>
    */
    public function getContactTypes (){
        return $this->get("contacttypes");
    }
    
    /**
    * @author Dragan Jancikin <dragan.jancikin@gmail.com>
    */
    public function delContact($client_id, $contact_id){
        $this->connection->query("DELETE FROM contacts WHERE id='$contact_id' ") or die(mysqli_error($this->connection));
        $this->connection->query("DELETE FROM client_contacts WHERE contact_id='$contact_id' ") or die(mysqli_error($this->connection));
    }

}
