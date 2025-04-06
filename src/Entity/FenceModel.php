<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FenceModel Entity
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__fence__models')]
class FenceModel
{

    /**
     * Identifier for the fence model.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Fence model name.
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
