<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/dbConfig.php';
/**
 * Connect to database
 * 
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class DBconnection {

    private static $instance = null;
    
    public function __construct() {
        $this->connection = $this->tryConnect();
    }

    protected function tryConnect() {

        if (self::$instance == null) {
            self::$instance = new mysqli( DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME );
            self::$instance->set_charset("utf8");
            if ( self::$instance->connect_error ) {
                printf("Connection failed: %s\ ", self::$instance->connect_error);
                exit();
            }
        } 

        return self::$instance;
    }

}
