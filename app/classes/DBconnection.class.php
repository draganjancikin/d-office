<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/dbConfig.php';
/**
 * Connect to database
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class DBconnection {

    public function __construct() {
        $this->connection = $this->tryConnect();
    }

    protected function tryConnect() {

        $conn = new mysqli( DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME );
        $conn->set_charset("utf8");

        if ( $conn->connect_error ) {
            printf("Connection failed: %s\ ", $conn->connect_error);
            exit();
        }

        return $conn;
    }

}
