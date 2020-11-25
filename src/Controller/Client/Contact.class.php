<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
/**
 * Contact class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class Contact extends Database {

    protected $id;
    protected $client_id;
    protected $type_id;
    protected $number;
    protected $note;


    /**
     * Method that return contacts by Client ID
     * 
     * @param integer $client_id
     * 
     * @return array
     * 
     * @author Dragan Jancikin <dragan.jancikin@gmail.com>
     */
    public function getContactsById($client_id){
        $result = $this->get("SELECT client_contacts.contact_id, contacts.id, contacts.type_id, contacts.number, contacts.note, contacttypes.id, contacttypes.name "
                            . "FROM client_contacts "
                            . "JOIN (contacts, contacttypes)"
                            . "ON (client_contacts.contact_id = contacts.id AND contacts.type_id = contacttypes.id )"
                            . "WHERE client_contacts.client_id = $client_id "
                            . "ORDER BY contacttypes.id ");
        return $result;
    }


    /**
     * Method that return all contact types form table contacttypes
     * 
     * @return array
     * 
     * @author Dragan Jancikin <dragan.jancikin@gmail.com>
     */
    public function getContactTypes (){
        return $this->get("SELECT * FROM contacttypes");
    }


    /**
     * Method that delete contact from client
     * 
     * @param integer $client_id
     * @param integer $contact_id
     * 
     * @author Dragan Jancikin <dragan.jancikin@gmail.com>
     */
    public function delContact($client_id, $contact_id){
        $this->delete("DELETE FROM contacts WHERE id='$contact_id' ");
        $this->delete("DELETE FROM client_contacts WHERE contact_id='$contact_id' ");
        die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
    }

}
