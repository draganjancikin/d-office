<?php
require_once 'DBconnection.class.php';
/**
 * 
 */
class DB extends DBconnection {

    protected function get ($table){
        
        $tableRow = array();
        $tableRows = array();

        $queryString = "SELECT id, name FROM $table ORDER BY name";

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

    public function numRows($table) {
        $result = $this->connection->query("SELECT * FROM $table ") or die(mysqli_error($this->conn));
        $num_rows = mysqli_num_rows($result);
        return $num_rows;
    }

    public function getKurs(){

        $result = $this->connection->query("SELECT kurs FROM preferences WHERE id = 1 ") or die(mysqli_error($this->connection));
            $row = $result->fetch_assoc();
            $kurs = $row['kurs'];

        return $kurs;
    }

    public function getTax(){

        $result = $this->connection->query("SELECT tax FROM preferences WHERE id = 1 ") or die(mysqli_error($this->connection));
            $row = $result->fetch_assoc();
            $tax = $row['tax'];

        return $tax;
    }

}
