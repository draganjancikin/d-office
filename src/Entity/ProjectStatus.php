<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project Status entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__project__statuses')]
class ProjectStatus
{

    /**
     * Identifier of the Project Status.
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Project Status name.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 48, unique: true)]
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