<?php
require_once 'DBconnection.class.php';
/**
 * Conf.class.php
 * 
 * Description of Conf
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class Conf extends DBconnection {

    protected $kurs;

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
