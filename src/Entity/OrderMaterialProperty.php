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

  public function getId() {
    return $this->id;
  }

}