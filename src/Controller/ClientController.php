<?php

namespace Roloffice\Controller;

use Roloffice\Core\Database;

/**
 * Client class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ClientController extends Database {

  /**
   * Method that return city by ID
   * 
   * @param integer $id
   * 
   * @return array
   */
  public function getCity($id) {
    $result =  $this->get("SELECT * FROM v6_cities WHERE id = $id");
    return ( empty($result[0]) ? false : $result[0] );
  }

  /**
   * Method that return client data by client ID
   * 
   * @param integer $id
   * 
   * @return array
   */
  /*
  public function getClient($id) {
    $result =  $this->get("SELECT client.id, client.type_id, client.name, client.name_note, client.lb, client.is_supplier, client.state_id, client.city_id, client.street_id, state.name as state_name, city.name as city_name, street.name as street_name, client.home_number, client.address_note, client.note "
                        . "FROM client "
                        . "JOIN (street, city, state)"
                        . "ON (client.state_id = state.id AND client.city_id = city.id AND client.street_id = street.id )"
                        . "WHERE client.id = $id ");
    if(empty($result)) {
      die('<script>location.href = "/clients/" </script>');
    } else {
        ($result[0]['type_id'] == 1 ? $result[0]['type_name'] = "Fizičko lice" : $result[0]['type_name'] = "Pravno lice" );
      return $result[0];
    }
  }
  */
    
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
