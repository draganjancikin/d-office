<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6__project_task_types")
 */
class ProjectTaskType {

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

    /**
   * @ORM\Column(type="string", length=32)
   * @var string
   */
  protected $class;

  public function getId() {
    return $this->id;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setClass($class) {
    $this->class = $class;
  }

  public function getClass() {
    return $this->class;
  }

}