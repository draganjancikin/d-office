<?php
require_once 'DBconnection.class.php';
/**
 * Class that contain basic method for manipulate with database
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class DB extends DBconnection {

    /**
     * Method that SELECT $columns and getting all rows FROM $table WHERE $filter 
     * ORDER BY $sort
     * 
     * @param string $table table name
     * @param string $columns optional clause wich columns selecting
     * @param string $sort optional clause for sort data
     * @param string $filter optional clause for filtering data
     * 
     * @return array aray of arays
     * 
     * @author Dragan Jancikin <dragan.jancikin@gmail.com>
     */
    protected function get ($table, $columns = NULL, $sort = NULL, $filter = NULL) {
        
        // ------------- list of arguments -----------------------
        // extract(func_get_args(), EXTR_PREFIX_ALL, "data");
        // -------------------------------------------------------
        // $arg_list = func_get_args();
        // for ($i = 0; $i < $numargs; $i++) {
        //    echo "Argument $i is: " . $arg_list[$i] . "<br />\n";
        // }

        $select = "SELECT $columns";
        $from = "FROM $table";
        (!$filter ? $where = "" : $where = "WHERE $filter");
        (!$sort ? $order_by ="" : $order_by = "ORDER BY $sort" );
        
        $query_str = "$select $from $where $order_by";
        // echo $query_str;
        $result = $this->connection->query( $query_str ) or die(mysqli_error($this->connection));
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        return $rows;
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
