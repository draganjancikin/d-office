<?php

namespace Roloffice\Controller;

use Roloffice\Core\Database;
/**
 * Contact class
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ContactController extends Database {

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

}
