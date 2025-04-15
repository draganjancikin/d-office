<?php

namespace App\Entity;

use App\Entity\OrderMaterial;
use App\Entity\Property;
use App\Repository\OrderMaterialPropertyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order Material Property entity.
 */
#[ORM\Entity(repositoryClass: OrderMaterialPropertyRepository::class)]
#[ORM\Table(name: 'v6__orders__materials__properties')]
class OrderMaterialProperty
{

    /**
     * Identifier of the OrderMaterialProperty.
     */
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    protected $id;

    /**
     * Many Order Materials can belong to the One Order.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: OrderMaterial::class)]
    #[ORM\JoinColumn(name: "order_material_id", referencedColumnName: "id")]
    protected $order_material;

    /**
     * Many Order Materials can belong to the One Property.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Property::class)]
    #[ORM\JoinColumn(name: "property_id", referencedColumnName: "id")]
    protected $property;

    /**
     * Order Material Property quantity.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 2)]
    protected $quantity;

    public function getId() {
        return $this->id;
    }

    public function setOrderMaterial($order_material) {
        $this->order_material = $order_material;
    }

    public function getOrderMaterial() {
        return $this->order_material;
    }

    public function setProperty($property) {
        $this->property = $property;
    }

    public function getProperty() {
        return $this->property;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getQuantity() {
        return $this->quantity;
    }

}