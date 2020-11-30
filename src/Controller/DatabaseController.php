<?php

namespace Roloffice\Controller;

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../config/dbConfig.php';
// require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
/**
 * Class that contain basic method for manipulate with database
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class DatabaseController {

    private static $instance = null;

    public function __construct() {
        $this->connection = $this->tryConnect();
    }

    protected function tryConnect() {

        // check if $instance == null, to prevent multiple connection to database
        if (self::$instance == null) {
            self::$instance = new \mysqli( DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME );
            self::$instance->set_charset("utf8");
            if ( self::$instance->connect_error ) {
                printf("Connection failed: %s\ ", self::$instance->connect_error);
                exit();
            }
        } 

        return self::$instance;
    }

    /**
     * Method that return result of $query_string
     * 
     * @param string $query_string
     * 
     * @return array aray of arays
     */
    protected function get($query_string) {
        $result = $this->connection->query( $query_string ) or die(mysqli_error($this->connection));
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        return $rows;
    }

    /**
     * Method that return last ID in $table
     * 
     * @param string $table
     * 
     * @return integer
     */
    protected function getLastId($table) {
        $result = $this->connection->query("SELECT id FROM $table ORDER by id desc") or die(mysqli_error($this->connection));
        $row = mysqli_fetch_array($result);
        return $row['id'];
    }

    /**
     * Method that insert by query
     * 
     * @param string $query_string
     */
    protected function insert($query_string) {
        $this->connection->query( $query_string ) or die(mysqli_error($this->connection));
    }

    /**
     * Method that delete by query
     * 
     * @param string $query_string
     */
    protected function delete($query_string) {
        $this->connection->query( $query_string ) or die(mysqli_error($this->connection));
    }
    
    public function getDate() {
        return date('Y-m-d');
    }

    public function numRows($table) {
        $result = $this->connection->query("SELECT * FROM $table ") or die(mysqli_error($this->connection));
        return mysqli_num_rows($result);
    }

    public function getKurs() {
        $result = $this->connection->query("SELECT kurs FROM preferences WHERE id = 1 ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();
        return $row['kurs'];
    }

    public function getTax() {
        $result = $this->connection->query("SELECT tax FROM preferences WHERE id = 1 ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();
        return $row['tax'];
    }

}
