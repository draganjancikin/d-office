<?php

namespace App\Entity;

use App\Entity\ArticleGroup;
use App\Entity\Unit;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity (repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'v6__articles')]
class Article
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many Articles belongs to the One Article Group.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: ArticleGroup::class)]
    #[ORM\JoinColumn(name: "group_id", referencedColumnName: "id")]
    protected $group;

    /**
     * Article name.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 96)]
    protected $name;

    /**
     * Many Articles have to the One Unit.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Unit::class)]
    #[ORM\JoinColumn(name: "unit_id", referencedColumnName: "id")]
    protected $unit;

    /**
     * Article weight.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 0)]
    protected $weight;

    /**
     * Article minimum calculation measure.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 2)]
    protected $min_calc_measure;

    /**
     * Article price.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 4)]
    protected $price;

    /**
     * Article note.
     *
     * @var string
     */
    #[ORM\Column(type: "text", length: 255)]
    protected $note;

    /**
     * Date of Article creation.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $created_at;

    /**
     * Many Articles have been created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by_user_id", referencedColumnName: "id")]
    protected $created_by_user;

    /**
     * Date of Article modification.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $modified_at;

    /**
     * Many Articles have been updated from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "modified_by_user_id", referencedColumnName: "id")]
    protected $modified_by_user;

    public function getId() {
        return $this->id;
    }

    public function setGroup($group) {
        $this->group = $group;
    }

    public function getGroup() {
        return $this->group;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function setMinCalcMeasure($min_calc_measure) {
        $this->min_calc_measure = $min_calc_measure;
    }

    public function getMinCalcMeasure() {
        return $this->min_calc_measure;
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