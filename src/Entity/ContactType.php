<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactType entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__contact__types')]
class ContactType
{

    /**
     * Identifier of the ContactType.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected int $id;

    /**
     * ContactType's name.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 48)]
    protected string $name;

    public function getId(): int {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

}