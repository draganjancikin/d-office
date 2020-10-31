<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DBconnection.class.php';
/**
 * Article.class.php
 * 
 * Article class
 *
 * @author Dragan Jancikin <dragan.jancikin@gamil.com>
 */

class Article extends DBconnection {

    protected $id;
    protected $group_id;
    protected $name;
    protected $weight;
    protected $price;
    protected $note;


    // metoda koja daje sve jedinice mere
    public function getUnits (){
    
        $unit = array();
        $units = array();

        // sada treba isčitati sve klijente iz tabele client
        $result = $this->connection->query("SELECT * FROM unit ORDER BY name" ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){
            $unit = array(
                'id' => $row['id'],
                'name' => $row['name']
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
        $result = $this->connection->query("SELECT * FROM article_group " ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){
            $group = array(
                'id' => $row['id'],
                'name' => $row['name']
            );
            array_push($groups, $group);
        }

        return $groups;
    }


    // metoda koja daje grupu artikala
    public function getArticleGroupById ($id){

        $result = $this->connection->query("SELECT name FROM article_group WHERE id='$id' " ) or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();

        if($row) {
            $group_name = $row['name'];
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
        return $row['price'];
    }


    // metoda koja daje sve artiklove
    public function getArticles (){

        $article = array();
        $articles = array();

        $result = $this->connection->query("SELECT id, name FROM article ORDER BY name"  ) or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()){
            $article = array(
                'id' => $row['id'],
                'name' => $row['name']
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
        $result = $this->connection->query("SELECT article.id, article.name, unit.name as unit_name, article.price "
                                         . "FROM article "
                                         . "JOIN (unit) "
                                         . "ON (article.unit_id = unit.id) "
                                         . $where
                                         . "ORDER BY article.name ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $article = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'unit_name' => $row['unit_name'],
                'price' => $row['price']
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
        $result = $this->connection->query("SELECT article.id, article.name, unit.name as unit_name, article.price "
                                         . "FROM article "
                                         . "JOIN (unit) "
                                         . "ON (article.unit_id = unit.id) " 
                                         . "WHERE (article.name LIKE '%$name%') "
                                         . "ORDER BY article.name ") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $article = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'unit_name' => $row['unit_name'],
                'price' => $row['price']
            );
            array_push($articles, $article);
        endwhile;

        return $articles;
    }


    //metoda koja vraća podatke o artiklu
    public function getArticle($article_id){

        $article = array();

        $result = $this->connection->query("SELECT article.id, article.group_id, article.name, article.unit_id, article.weight, article.min_obrac_mera, article.price, article.note, unit.name as unit_name "
                                         . "FROM article "
                                         . "JOIN (unit) "
                                         . "ON (article.unit_id = unit.id) "
                                         . "WHERE article.id = $article_id ") or die(mysqli_error($this->connection));
        $row = $result->fetch_assoc();
            $article = array(
                'id' => $row['id'],
                'group_id' => $row['group_id'],
                'name' => $row['name'],
                'unit_id' => $row['unit_id'],
                'unit_name' => $row['unit_name'],
                'weight' => $row['weight'],
                'min_obrac_mera' => $row['min_obrac_mera'],
                'price' => $row['price'],
                'note' => $row['note']
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
            $property = array(
                'id' => $row['id'],
                'name' => $row['name']
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
            $property = array(
                'id' => $row['id'],
                'name' => $row['name']
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
        $result = $this->connection->query("SELECT article.id, article.name, unit.name as unit_name, article.price "
                                         . "FROM article "
                                         . "JOIN (unit)"
                                         . "ON (article.unit_id = unit.id)"
                                         . "ORDER BY article.id DESC LIMIT $limit") or die(mysqli_error($this->connection));
        while($row = $result->fetch_assoc()):
            $article = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'unit_name' => $row['unit_name'],
                'price' => $row['price'],
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
