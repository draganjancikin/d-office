<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/config.php';
/**
 * Description of DB class
 *
 * @author Dragan Jancikin
 */
class DB {
    
    public function connectDB(){
        // create connection
        $conn = new mysqli( DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME );
        $conn -> set_charset("utf8");
        if ( $conn -> connect_error ) {
            // error message
            printf("Connection failed: %s\ ", $conn -> connect_error);
            // stop execution after error message
            exit();
        }
        // echo "con <br>";
        return $conn;
    }
    
}
