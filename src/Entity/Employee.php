<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6__employees")
 */
class Employee {

    /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

    /**
   * @ORM\Column(type="string", length=48)
   * @var string
   */
  protected $name;

  public function getId() {
    return $this->id;
  }

  public function setName($name) {
    $this->name = $name;
  }
  
  public function getName() {
    return $this->name;
  }

}