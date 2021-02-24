<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6_orders_materials_properties")
 */
class OrderMaterialProperty {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="OrderMaterial")
   * @ORM\JoinColumn(name="order_material_id", referencedColumnName="id")
   * @var int
   */
  protected $order_material;
  
  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="Property")
   * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
   * @var int
   */
  protected $property;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=2)
   * @var float
   */
  protected $quantity;

  public function getId() {
    return $this->id;
  }

  public function setOrderMaterial($order_material) {
    $this->order_material = $order_material;
  }

  public function getOrderMaterial() {
    return $this->order_material;
  }

  public function setProperty($property) {
    $this->property = $property;
  }

  public function getProperty() {
    return $this->property;
  }

  public function setQuantity($quantity) {
    $this->quantity = $quantity;
  }

  public function getQuantity() {
    return $this->quantity;
  }

}