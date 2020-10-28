<?php
/**
 * Description of Article class
 *
 * @author Dragan Jancikin
 */

class Article {

    // prvo treba definisati properties, promenljive
    private $id;

    // $name je naziv (ime) klijenta
    private $name;


    // metoda koja se automatski izvršava pri generisanju objekta Client
    public function __construct() {
        // treba konektovati na bazu preko klase koja vrši konekciju
        $db = new DB();
        $this->connection = $db->connectDB();
    }


    // metoda koja daje sve jedinice mere
    public function getUnits (){
    
        $unit = array();
        $units = array();

        // sada treba isčitati sve klijente iz tabele client
        $result = $this->connection->query("SELECT id, unit_name FROM unit ORDER BY unit_name" ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){

            $id = $row['id'];
            $unit_name = $row['unit_name'];

            $unit = array(
                'id' => $id,
                'name' => $unit_name
            );
 
            array_push($units, $unit);
        }

        return $units;
    }


    // metoda koja daje sve grupe artikala
    public function getArticleGroups (){

      $group = array();
      $groups = array();

      // sada treba isčitati sve grupe artikala
      $result = $this->connection->query("SELECT id, group_name FROM article_group " ) or die(mysqli_error($this->connection));
      while($row = $result->fetch_assoc()){

        $id = $row['id'];
        $group_name = $row['group_name'];

        $group = array(
          'id' => $id,
          'name' => $group_name
        );
 
        array_push($groups, $group);
      }

      return $groups;
    }

    // metoda koja daje grupu artikala
    public function getArticleGroupById ($id){

        $result = $this->connection->query("SELECT group_name FROM article_group WHERE id='$id' " ) or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();

        if($row) {
            $group_name = $row['group_name'];
        } else {
            $group_name = "Izaberi grupu";
        }

        return $group_name;
    }


    // metoda koja daje cenu artikla
    public function getPrice ($article_id){

        // sada treba isčitati cenu artikla
        $result = $this->connection->query("SELECT price FROM article WHERE id = $article_id " ) or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();

            $price = $row['price'];

        return $price;
    }


    // metoda koja daje sve artiklove
    public function getArticles (){

        $article = array();
        $articles = array();

        $result = $this->connection->query("SELECT id, name FROM article ORDER BY name"  ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){

            $id = $row['id'];
            $name = $row['name'];

            $article = array(
                'id' => $id,
                'name' => $name
            );

            array_push($articles, $article);
        }

        return $articles;
    }


    // metoda koja daje sve artiklove 
    public function getArticlesByGroup ($group_id){
    
        $article = array();
        $articles = array();
    
        if($group_id == 0 ){
            $where = "";
        }else {
            $where = "WHERE (article.group_id = $group_id )";
        }

        // izlistavanje iz baze svih artikala sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT article.id, article.name, unit.unit_name, article.price "
                                         . "FROM article "
                                         . "JOIN (unit) "
                                         . "ON (article.unit_id = unit.id) "
                                         . $where
                                         . "ORDER BY article.name ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $id = $row['id'];
            $name = $row['name'];
            $unit_name = $row['unit_name'];
            $price = $row['price'];

            $article = array(
                'id' => $id,
                'name' => $name,
                'unit_name' => $unit_name,
                'price' => $price
            );

            array_push($articles, $article);
            
        endwhile;

        return $articles;
    }


    //metoda koja vraća artikle u zavisnosti od datog pojma u pretrazi
    public function search($name){

        $article = array();
        $articles = array();

        // izlistavanje iz baze svih artikala sa nazivom koji je sličan $name
        $result = $this->connection->query("SELECT article.id, article.name, unit.unit_name, article.price "
                                         . "FROM article "
                                         . "JOIN (unit) "
                                         . "ON (article.unit_id = unit.id) " 
                                         . "WHERE (article.name LIKE '%$name%') "
                                         . "ORDER BY article.name ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $id = $row['id'];
            $name = $row['name'];
            $unit_name = $row['unit_name'];
            $price = $row['price'];

            $article = array(
                'id' => $id,
                'name' => $name,
                'unit_name' => $unit_name,
                'price' => $price
            );

            array_push($articles, $article);
            
        endwhile;

        return $articles;
    }


    //metoda koja vraća podatke o artiklu
    public function getArticle($article_id){

        $article = array();

        $result = $this->connection->query("SELECT article.id, article.group_id, article.name, article.unit_id, article.weight, article.min_obrac_mera, article.price, article.note, unit.unit_name "
                                         . "FROM article "
                                         . "JOIN (unit) "
                                         . "ON (article.unit_id = unit.id) "
                                         . "WHERE article.id = $article_id ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();
            $id = $row['id'];
            $group_id = $row['group_id'];
            $name = $row['name'];
            $unit_id = $row['unit_id'];
            $unit_name = $row['unit_name'];
            $weight = $row['weight'];
            $min_obrac_mera = $row['min_obrac_mera'];
            $price = $row['price'];
            $note = $row['note'];

            $article = array(
                'id' => $id,
                'group_id' => $group_id,
                'name' => $name,
                'unit_id' => $unit_id,
                'unit_name' => $unit_name,
                'weight' => $weight,
                'min_obrac_mera' => $min_obrac_mera,
                'price' => $price,
                'note' => $note
            );

        return $article;
    }


    // metoda koja vraća property-je
    public function getPropertys(){

        $property = array();
        $propertys = array();

        // sada treba isčitati property-je  artikla
        $result = $this->connection->query("SELECT id, name "
                                         . "FROM property "  ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){
            $id = $row['id'];
            $name = $row['name'];

            $property = array(
                'id' => $id,
                'name' => $name
            );

            array_push($propertys, $property);
        }

        return $propertys;
    }


    // metoda koja vraća property-je artikla, ako postoje, na osnovu article_id-a
    public function getPropertyById($article_id){

        $property = array();
        $propertys = array();

        // sada treba isčitati property-je  artikla na osnovu article_id-a
        $result = $this->connection->query("SELECT property.id, property.name "
                                         . "FROM article_property "
                                         . "JOIN (property) "
                                         . "ON (article_property.property_id = property.id) "
                                         . "WHERE article_property.article_id = $article_id "  ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){

            $id = $row['id'];
            $name = $row['name'];

            $property = array(
                'id' => $id,
                'name' => $name
            );

            array_push($propertys, $property);
        }

        return $propertys;
    }


    //metoda koja daje zadnjih $number proizvoda upisanih u bazu
    public function getLastArticles($limit){

        $article = array();
        $articles = array();

        // izlistavanje zadnjih $limit proizvoda
        $result = $this->connection->query("SELECT article.id, article.name, unit.unit_name, article.price "
                                         . "FROM article "
                                         . "JOIN (unit)"
                                         . "ON (article.unit_id = unit.id)"
                                         . "ORDER BY article.id DESC LIMIT $limit") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $id = $row['id'];
            $name = $row['name'];
            $unit_name = $row['unit_name'];
            $price = $row['price'];

            $article = array(
                'id' => $id,
                'name' => $name,
                'unit_name' => $unit_name,
                'price' => $price,
            );

            array_push($articles, $article);

        endwhile;

        return $articles;
    }


    // metoda koja briše osobinu artikla
    public function delArticleProperty($article_id, $property_id) {
        $this->connection->query("DELETE FROM article_property WHERE ( article_id='$article_id' AND property_id='$property_id') ") or die(mysqli_error($this->connection));
    }


    // metoda koja briše materijal iz sastavnice artikla
    public function delArticleMaterijal($article_id, $material_id) {
        $this->connection->query("DELETE FROM article_material WHERE ( article_id='$article_id' AND material_id='$material_id') ") or die(mysqli_error($this->connection));
    }

}
