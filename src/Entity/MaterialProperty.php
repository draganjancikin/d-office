<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="App\Repository\MaterialPropertyRepository")
 * @ORM\Table(name="v6__materials__properties")
 */
class MaterialProperty {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="Material")
   * @ORM\JoinColumn(name="material_id", referencedColumnName="id")
   * @var int
   */
  protected $material;

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

  public function setMaterial($material) {
    $this->material = $material;
  }

  public function getMaterial() {
    return $this->material;
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