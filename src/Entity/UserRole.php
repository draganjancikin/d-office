<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserRole entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__user__roles')]
class UserRole
{

    /**
     * Identifier of the UserRole.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * UserRole's name.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 16)]
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