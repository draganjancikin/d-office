<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project task status entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__project_task_statuses')]
class ProjectTaskStatus
{

    /**
     * Identifier of the project task status.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Project task status name.
     *
     * @var string
     */
    #[ORM\Column(type: 'string', length: 48)]
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