<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6_articles_properties")
 */
class ArticleProperty {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="Article")
   * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
   * @var int
   */
  protected $article;

  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="ArticleProperty")
   * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
   * @var int
   */
  protected $property;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $min_size;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $max_size;

  public function getId() {
    return $this->id;
  }

  public function setArticle($article) {
    $this->article = $article;
  }
  
  public function getArticle() {
    return $this->article;
  }

  public function setProperty($property) {
    $this->property = $property;
  }
  
  public function getProperty() {
    return $this->property;
  }

  public function setMinDimension($min_dimension) {
    $this->min_dimension = $min_dimension;
  }
  
  public function getMinDimension() {
    return $this->min_dimension;
  }

  public function setMaxDimension($max_dimension) {
    $this->max_dimension = $max_dimension;
  }
  
  public function getMaxDimension() {
    return $this->max_dimension;
  }
}