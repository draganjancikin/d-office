<?php
/**
 * Description of Conf
 *
 * @author Dragan Jancikin
 */
class Conf {
    
    protected $kurs;
    
    // metoda koja se automatski izvršava pri generisanju objekta Client
    public function __construct() {
        // treba konektovati na bazu preko klase koja vrši konekciju
        
        
        $db = new DB();
        $this->conn = $db->connectDB();
    }
    
    public function getKurs(){
        
        $result = $this->conn->query("SELECT kurs FROM preferences WHERE id = 1 ") or die(mysqli_error($this->conn));
            // $row = mysqli_fetch_array($result);
            $row = $result->fetch_assoc();
            $kurs = $row['kurs'];
            
        return $kurs;
    }
    
    public function getTax(){
        
        $result = $this->conn->query("SELECT tax FROM preferences WHERE id = 1 ") or die(mysqli_error($this->conn));
            // $row = mysqli_fetch_array($result);
            $row = $result->fetch_assoc();
            $tax = $row['tax'];
            
        return $tax;
    }
    
}
