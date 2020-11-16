<?php
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/classes/DB.class.php';
/**
 * Article class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */

class Article extends DB {

    protected $id;
    protected $group_id;
    protected $name;
    protected $unit_id;
    protected $weight;
    protected $min_obrac_mera;
    protected $price;
    protected $date;
    protected $note;

    
    // metoda koja daje sve jedinice mere
    public function getUnits() {
        return $this->get("SELECT * FROM unit");
    }
    

    public function getArticleGroups() {
        return $this->get("SELECT * FROM article_group");
    }


    public function getArticleGroupById($id) {
        $result = $this->get("SELECT id, name FROM article_group WHERE id='$id' ");
        return ( empty($result[0]) ? false : $result[0] );
    }


    public function getPrice($article_id) {
        return $this->get("SELECT price FROM article WHERE id = $article_id")[0]['price'];
    }


    public function getArticles() {
        return $this->get("SELECT * FROM article ORDER BY name");
    }


    public function getArticlesByGroup($group_id) {
        return $this->get("SELECT article.id, article.name, unit.name as unit_name, article.price "
                        . "FROM article "
                        . "JOIN (unit) "
                        . "ON (article.unit_id = unit.id) "
                        . "WHERE (article.group_id = $group_id )"
                        . "ORDER BY article.name ");
    }


    public function search($name) {
        return $this->get("SELECT article.id, article.name, unit.name as unit_name, article.price "
                        . "FROM article "
                        . "JOIN (unit) "
                        . "ON (article.unit_id = unit.id) " 
                        . "WHERE (article.name LIKE '%$name%') "
                        . "ORDER BY article.name ");
    }


    public function getArticle($article_id) {
        $result = $this->get("SELECT article.id, article.group_id, article.name, article.unit_id, article.weight, article.min_obrac_mera, article.price, article.note, unit.name as unit_name "
                            . "FROM article "
                            . "JOIN (unit) "
                            . "ON (article.unit_id = unit.id) "
                            . "WHERE article.id = $article_id ");
        return $result[0];
    }
    

    // metoda koja vraća property-je
    public function getPropertys() {
        return $this->get("SELECT * FROM property");
    }


    public function getPropertyByArticleId($article_id) {
        return $this->get("SELECT property.id, property.name "
                        . "FROM article_property "
                        . "JOIN (property) "
                        . "ON (article_property.property_id = property.id) "
                        . "WHERE article_property.article_id = $article_id ");
    }


    public function getLastArticles($limit) {
        return $this->get("SELECT article.id, article.name, unit.name as unit_name, article.price "
                        . "FROM article "
                        . "JOIN (unit)"
                        . "ON (article.unit_id = unit.id)"
                        . "ORDER BY article.id DESC LIMIT $limit");
    }


    public function delArticleProperty($article_id, $property_id) {
        $this->connection->query("DELETE FROM article_property WHERE ( article_id='$article_id' AND property_id='$property_id') ") or die(mysqli_error($this->connection));
    }


    public function delArticleMaterijal($article_id, $material_id) {
        $this->connection->query("DELETE FROM article_material WHERE ( article_id='$article_id' AND material_id='$material_id') ") or die(mysqli_error($this->connection));
    }

}
