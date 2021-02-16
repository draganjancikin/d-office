<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6_materials_properties")
 */
class MaterialProperty {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  // TODO Dragan
  // material_id

  // property_id

  // min

  // max

  public function getId() {
    return $this->id;
  }

}