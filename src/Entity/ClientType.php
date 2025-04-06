<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientType entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__client__types')]
class ClientType
{

    /**
     * Identifier of the ClientType.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * ClientType's name.
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