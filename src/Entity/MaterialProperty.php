<?php

namespace App\Entity;

use App\Entity\Material;
use App\Entity\Property;
use App\Repository\MaterialPropertyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * MaterialProperty Entity.
 */
#[ORM\Entity(repositoryClass: MaterialPropertyRepository::class)]
#[ORM\Table(name: 'v6__materials__properties')]
class MaterialProperty
{

    /**
     * Identifier of the MaterialProperty.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many Materials have the One Property.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Material::class)]
    #[ORM\JoinColumn(name: 'material_id', referencedColumnName: 'id')]
    protected $material;

    /**
     * Many Properties have the One Material.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Property::class)]
    #[ORM\JoinColumn(name: 'property_id', referencedColumnName: 'id')]
    protected $property;

    /**
     * Material Property minimum size.
     *
     * @var float
     */
    #[ORM\Column(type: 'decimal', precision: 11, scale: 0)]
    protected $min_size;

    /**
     * Material Property maximum size.
     *
     * @var float
     */
    #[ORM\Column(type: 'decimal', precision: 11, scale: 0)]
    protected $max_size;

    public function getId() {
        return $this->id;
    }

    public function setMaterial($material) {
        $this->material = $material;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function setProperty($property) {
        $this->property = $property;
    }

    public function getProperty() {
        return $this->property;
    }

    public function setMinSize($min_size) {
        $this->min_size = $min_size;
    }

    public function getMinSize() {
        return $this->min_size;
    }

    public function setMaxSize($max_size) {
        $this->max_size = $max_size;
    }

    public function getMaxSize() {
        return $this->max_size;
    }

}