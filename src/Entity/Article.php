<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="Roloffice\Repository\ArticleRepository")
 * @ORM\Table(name="v6_articles")
 */
class Article {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny Articles belongs to the One Article Group.
   * @ORM\ManyToOne(targetEntity="ArticleGroup")
   * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
   * @var int
   */
  protected $group;

  /**
   * @ORM\Column(type="string", length=48)
   * @var string
   */
  protected $name;

  /**
   * Meny Articles have to the One Unit.
   * @ORM\ManyToOne(targetEntity="Unit")
   * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
   * @var int
   */
  protected $unit;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $weight;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=2)
   * @var float
   */
  protected $min_calc_measure;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=4)
   * @var float
   */
  protected $price;

  /**
   * @ORM\Column(type="text")
   * @var string
   */
  protected $note;

    /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $created_at;

  /**
   * Many Articles has ben created from One User.
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id")
   * @var int
   */
  protected $created_by_user;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $modified_at;

  /**
   * Many Articles has ben updated from One User.
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id")
   * @var int
   */
  protected $modified_by_user;

  public function getId() {
    return $this->id;
  }

  public function setGroup($group) {
    $this->group = $group;
  }

  public function getGroup() {
    return $this->group;
  }

  public function setName($name) {
    $this->name = $name;
  }
  
  public function getName() {
    return $this->name;
  }

  public function setUnit($unit) {
    $this->unit = $unit;
  }

  public function getUnit() {
    return $this->unit;
  }

  public function setWeight($weight) {
    $this->weight = $weight;
  }

  public function getWeight() {
    return $this->weight;
  }

  public function setMinCalcMeasure($min_calc_measure) {
    $this->min_calc_measure = $min_calc_measure;
  }

  public function getMinCalcMeasure() {
    return $this->min_calc_measure;
  }

  public function setNote($note) {
    $this->note = $note;
  }

  public function getNote() {
    return $this->note;
  }

  public function setPrice($price) {
    $this->price = $price;
  }

  public function getPrice() {
    return $this->price;
  }

  public function setCreatedAt(\DateTime $created_at) {
    $this->created_at = $created_at;
  }

  public function getCreatedAt() {
    return $this->created_at;
  }

  public function setCreatedByUser($created_by_user) {
    $this->created_by_user = $created_by_user;
  }

  public function getCreatedByUser() {
    return $this->created_by_user;
  }

  public function setModifiedAt(\DateTime $modified_at) {
    $this->modified_at = $modified_at;
  }

  public function getModifiedAt() {
    return $this->modified_at;
  }

  public function setModifiedByUser($modified_by_user) {
    $this->modified_by_user = $modified_by_user;
  }

  public function getModifiedByUser() {
    return $this->modified_by_user;
  }

}