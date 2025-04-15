<?php

namespace App\Entity;

use App\Entity\Client;
use App\Entity\Material;
use App\Entity\User;
use App\Repository\MaterialSupplierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * MaterialSupplier Entity.
 */
#[ORM\Entity(repositoryClass: MaterialSupplierRepository::class)]
#[ORM\Table(name: 'v6__materials__suppliers')]
class MaterialSupplier
{

    /**
     * Identifier of the MaterialSupplier.
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many Supplier has the One Material.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Material::class)]
    #[ORM\JoinColumn(name: 'material_id', referencedColumnName: 'id')]
    protected $material;

    /**
     * Many Materials have the One Supplier.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'id')]
    protected $supplier;

    /**
     * Material Supplier note.
     *
     * @var string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected $note;

    /**
     * Material Supplier price.
     *
     * @var float
     */
    #[ORM\Column(type: 'decimal', precision: 11, scale: 4)]
    protected $price;

    /**
     * Date and time when the MaterialSupplier was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $created_at;

    /**
     * Many Material Suppliers has been created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id')]
    protected $created_by_user;

    /**
     * Date and time when the MaterialSupplier was modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $modified_at;

    /**
     * Many Material Suppliers has been modified from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'modified_by_user_id', referencedColumnName: 'id')]
    protected $modified_by_user;

    public function getId() {
        return $this->id;
    }

    public function setMaterial($material) {
        $this->material = $material;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function setSupplier($supplier) {
        $this->supplier = $supplier;
    }

    public function getSupplier() {
        return $this->supplier;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getNote() {
        return $this->note;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setCreatedAt(\DateTime $created_at) {
        $this->created_at = $created_at;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedByUser($created_by_user) {
        $this->created_by_user = $created_by_user;
    }

    public function getCreatedByUser() {
        return $this->created_by_user;
    }

    public function setModifiedAt(\DateTime $modified_at) {
        $this->modified_at = $modified_at;
    }

    public function getModifiedAt() {
        return $this->modified_at;
    }

    public function setModifiedByUser($modified_by_user) {
        $this->modified_by_user = $modified_by_user;
    }

    public function getModifiedByUser() {
       return $this->modified_by_user;
    }

}
