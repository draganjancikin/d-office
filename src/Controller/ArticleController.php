<?php

namespace Roloffice\Controller;

//require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';
/**
 * Article class
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class ArticleController extends DatabaseController {

    private $table_article = "article";

    protected $id;
    protected $group_id;
    protected $name;
    protected $unit_id;
    protected $weight;
    protected $min_obrac_mera;
    protected $price;
    protected $date;
    protected $note;

    /**
     * Method that return all articles from table article
     * 
     * @return array
     */
    public function getAllArticles() {
        $result = $this->get("SELECT * FROM $this->table_article ORDER BY name");
        return $result;
    }

    /**
     * Method that return article by $article_id
     * 
     * @param integer $article_id
     * 
     * @return array
     */
    public function getArticleById($article_id) {
        $result = $this->get("SELECT article.id, article.group_id, article.name, article.unit_id, article.weight, article.min_obrac_mera, article.price, article.note, unit.name as unit_name "
                            . "FROM $this->table_article "
                            . "JOIN (unit) "
                            . "ON (article.unit_id = unit.id) "
                            . "WHERE article.id = $article_id ");
        if(!$result) {
            die('<script>location.href = "/articles/" </script>');
        }else{
            return $result[0];
        }
    }

    /**
     * Method thet return last articles
     * 
     * @param integer $limit
     * 
     * @return array
     */
    public function getLastArticles($limit) {
        $result = $this->get("SELECT article.id, article.name, unit.name as unit_name, article.price "
                        . "FROM $this->table_article "
                        . "JOIN (unit)"
                        . "ON (article.unit_id = unit.id)"
                        . "ORDER BY article.id DESC LIMIT $limit");
        return $result;
    }

    /**
     * Method that return all articles in group
     * 
     * @param integer $group_id
     * 
     * @return array
     */
    public function getArticlesByGroup($group_id) {
        $result = $this->get("SELECT article.id, article.name, unit.name as unit_name, article.price "
                        . "FROM $this->table_article "
                        . "JOIN (unit) "
                        . "ON (article.unit_id = unit.id) "
                        . "WHERE (article.group_id = $group_id )"
                        . "ORDER BY article.name ");
        return $result;
    }

    /**
     * Method that return array of article groups
     * 
     * @return array
     */
    public function getArticleGroups() {
        $result = $this->get("SELECT * FROM article_group");
        return $result;
    }

    /**
     * Method that return article group by article group id
     * 
     * @param integer $id
     * 
     * @return array
     */
    public function getArticleGroupById($id) {
        $result = $this->get("SELECT id, name FROM article_group WHERE id='$id' ");
        return ( empty($result[0]) ? false : $result[0] );
    }

    /**
     * Method that return array of measure unit
     * 
     * @return array
     */
    public function getUnits() {
        $result = $this->get("SELECT * FROM unit");
        return $result;
    }

    /**
     * Method that returns propertys
     * 
     * @return array
     */
    public function getPropertys() {
        $result = $this->get("SELECT * FROM property");
        return $result;
    }

    /**
     * Methor that return propertys by article ID
     * 
     * @param integer $article_id
     * 
     * @return array 
     */
    public function getPropertyByArticleId($article_id) {
        $result = $this->get("SELECT property.id, property.name "
                        . "FROM article_property "
                        . "JOIN (property) "
                        . "ON (article_property.property_id = property.id) "
                        . "WHERE article_property.article_id = $article_id ");
        return $result;
    }

    /**
     * Method that return article price
     * 
     * @param integer $article_id
     * 
     * @return decimal
     */
    public function getPrice($article_id) {
        $result = $this->get("SELECT price FROM $this->table_article WHERE id = '$article_id' ")[0]['price'];
        return $result;
    }

    /**
     * Method that return articles with name like $name
     * 
     * @param string $name
     * 
     * @return array
     */
    public function search($name) {
        $result = $this->get("SELECT article.id, article.name, unit.name as unit_name, article.price, article.note "
                        . "FROM $this->table_article "
                        . "JOIN (unit) "
                        . "ON (article.unit_id = unit.id) " 
                        . "WHERE (article.name LIKE '%$name%') "
                        . "ORDER BY article.name ");
        return $result;
    }

    /**
     * Method that delete article property
     * 
     * @param integer $article_id
     * @param integer $property_id
     */
    public function delArticleProperty($article_id, $property_id) {
        $this->delete("DELETE FROM article_property "
                    . "WHERE ( article_id='$article_id' AND property_id='$property_id') ");
    }

}
