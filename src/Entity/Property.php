<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Property entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__properties')]
class Property
{

    /**
     * Identifier of the Property.
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Property name.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 48)]
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