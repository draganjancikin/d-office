<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/dbConfig.php';
/**
 * Description of Database class
 *
 * @author Dragan Jancikin
 */

class Database {
  
    protected $mysqli;

    public function __construct() {
        $this->db_connect();
        // echo "connected";
    }

    private function db_connect(){
        // create connection
        $this->mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        $this->mysqli -> set_charset("utf8");
        if ( $this->mysqli -> connect_error ) {
            // error message
            printf("Connection failed: %s\ ", $this->mysqli -> connect_error);
            // stop execution after error message
            exit();
        }
        return $this->mysqli;
    }

    // raturn num of rows
    public function numRows($table) {
        $result = $this->mysqli->query("SELECT * FROM $table ");
        $num_rows = mysqli_num_rows($result);
        return $num_rows;
    }
    
}
