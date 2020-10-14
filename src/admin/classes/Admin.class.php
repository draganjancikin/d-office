<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/config.php';
/**
 * Description of Admin class
 *
 * @author Dragan Jancikin
 */
class Admin extends DB{
    
  // metoda koja se automatski izvršava pri generisanju objekta Client
  public function __construct() {
    // treba konektovati na bazu preko klase koja vrši konekciju
    $db = new DB();
    $this->connection = $db->connectDB();
  }
    
    
  // metoda koja daje sve jedinicec mere
  public function baseBackup (){
    $dumpfile = "roloffice_" . date("Y-m-d_H-i-s") . ".sql";
        
    // passthru("C:/xampp/mysql/bin/mysqldump --opt --host=$dbhost --user=$dbuser --password=$dbpwd $dbname > c:/$dumpfile");
    passthru("C:/xampp/mysql/bin/mysqldump --opt --host=SERVER --user=DB_USERNAME --password=DB_PASSWORD DB_NAME > D:/Documents/BackUps/MYSQL/$dumpfile");    
    echo '
      <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-info"></i> Obaveštenje!</h4>
        Backup baze je izvšen u fajl: <br />D:/Documents/BackUps/MYSQL/'.$dumpfile.'
      </div>
        '; 
    passthru("tail -1 $dumpfile");
  }
    
}
