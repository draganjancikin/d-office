<?php
require_once 'DBconnection.class.php';
/**
 * Class that contain basic method for manipulate with database
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class DB extends DBconnection {

    /**
     * Method that getting all rows FROM $table ORDER BY $order_by
     * 
     * @param string $table table name
     * @param string $order_by optional clause for order data
     * 
     * @return array
     * 
     * @author Dragan Jancikin <dragan.jancikin@gmail.com>
     */
    protected function get(string $table, string $order_by = "name") {
        
        // ------------- list of arguments -----------------------
        // extract(func_get_args(), EXTR_PREFIX_ALL, "data");
        // -------------------------------------------------------
        // $arg_list = func_get_args();
        // for ($i = 0; $i < $numargs; $i++) {
        //    echo "Argument $i is: " . $arg_list[$i] . "<br />\n";
        // }
        
        $query_string = "SELECT id, name FROM $table ORDER BY $order_by";

        $result = $this->connection->query( $query_string ) or die(mysqli_error($this->connection));
        $result -> fetch_all(MYSQLI_ASSOC);

        return $result;
    }


    public function numRows($table) {
        $result = $this->connection->query("SELECT * FROM $table ") or die(mysqli_error($this->connection));
        return mysqli_num_rows($result);
    }

    public function getKurs(){

        $result = $this->connection->query("SELECT kurs FROM preferences WHERE id = 1 ") or die(mysqli_error($this->connection));
            $row = $result->fetch_assoc();
        
        return $row['kurs'];
    }

    public function getTax(){

        $result = $this->connection->query("SELECT tax FROM preferences WHERE id = 1 ") or die(mysqli_error($this->connection));
            $row = $result->fetch_assoc();
        
        return $row['tax'];
    }

}
