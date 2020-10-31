<?php
require_once 'DBconnection.class.php';
/**
 * 
 */
class DB extends DBconnection {

    protected function get ($table){
        
        $tableRow = array();
        $tableRows = array();

        $queryString = "SELECT * FROM $table ORDER BY name";

        $result = $this->connection->query( $queryString ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){    
            $tableRow = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
            array_push($tableRows, $tableRow);
        }

        return $tableRows;
    }

}
