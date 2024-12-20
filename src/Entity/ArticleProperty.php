<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="App\Repository\ArticlePropertyRepository")
 * @ORM\Table(name="v6__articles__properties")
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
   * @ORM\ManyToOne(targetEntity="Property")
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

  public function setMinSize($min_size) {
    $this->min_size = $min_size;
  }
  
  public function getMinSize() {
    return $this->min_size;
  }

  public function setMaxSize($max_size) {
    $this->max_size = $max_size;
  }
  
  public function getMaxSize() {
    return $this->max_size;
  }
}