<?php

namespace App\Entity;

use App\Entity\Order;
use App\Repository\OrderMaterialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderMaterial entity.
 */
#[ORM\Entity(repositoryClass: OrderMaterialRepository::class)]
#[ORM\Table(name: 'v6__orders__materials')]
class OrderMaterial
{

    /**
     * Identifier of the OrderMaterial.
     */
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    protected $id;

    /**
     * Many OrderMaterials can belong to the One Order.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(name: "order_id", referencedColumnName: "id")]
    protected $order;

    /**
     * Many OrderMaterials can belong to the One Material.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Material::class)]
    #[ORM\JoinColumn(name: "material_id", referencedColumnName: "id")]
    protected $material;

    /**
     * Order Material pieces.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 0)]
    protected $pieces;

    /**
     * Order Material price.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 4)]
    protected $price;

    /**
     * Order Material discount.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 2)]
    protected $discount;

    /**
     * Order Material tax.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 2)]
    protected $tax;

    /**
     * Order Material weight.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 0)]
    protected $weight;

    /**
     * Order Material note.
     *
     * @var string
     */
    #[ORM\Column(type: "text", nullable: true)]
    protected $note;

    public function getId() {
        return $this->id;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

    public function getOrder() {
        return $this->order;
    }

    public function setMaterial($material) {
        $this->material = $material;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function setPieces($pieces) {
        $this->pieces = $pieces;
    }

    public function getPieces() {
        return $this->pieces;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    public function getDiscount() {
        return $this->discount;
    }

    public function setTax($tax) {
        $this->tax = $tax;
    }

    public function getTax() {
        return $this->tax;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getNote() {
        return $this->note;
    }

}