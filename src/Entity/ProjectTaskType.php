<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project task type entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__project_task_types')]
class ProjectTaskType
{

    /**
     * Identifier of the project task type.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Project task type name.
     *
     * @var string
     */
    #[ORM\Column(type: 'string', length: 48)]
    protected $name;

    /**
     * Project task type class.
     *
     * @var string
     */
    #[ORM\Column(type: 'string', length: 32)]
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
