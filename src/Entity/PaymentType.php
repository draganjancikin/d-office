<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentType Entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__payment__types')]
class PaymentType
{

    /**
     * Identifier of the payment type.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Payment type name.
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