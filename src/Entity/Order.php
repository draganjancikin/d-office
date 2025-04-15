<?php

namespace App\Entity;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'v6__orders')]
class Order
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Ordinal number of the Order in the current year.
     *
     * @var int
     */
    #[ORM\Column(type: "integer")]
    protected $ordinal_num_in_year;

    /**
     * Date of the Order.
     *
     * @var DateTime
     */
      #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $date;

    /**
     * Many Order belongs to the One Supplier.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "supplier_id", referencedColumnName: "id")]
    protected $supplier;

    /**
     * Many Orders can belong to the One Project.
     *
     * @var int
     */

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: "project_id", referencedColumnName: "id")]
    //  protected $project;


    /**
     * Order title.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 196)]
    protected $title;

    /**
     * Flag indicating if the Order is archived.
     *
     * @var boolean
     */
    #[ORM\Column(type: "boolean", options: ["default" => false])]
    protected $is_archived;

    /**
     * Order status.
     *
     * Can be: 0 => 'draft', 1 => 'ordered', 2 => 'arrived' (0 => 'nacrt',
     * 1 => 'poručeno', 2 => 'stiglo').
     *
     * @var int
     */
    #[ORM\Column(type: "integer", options: ["default" => 0] )]
    protected $status;

    /**
     * Accounting Document note.
     *
     * @var string
     */
    #[ORM\Column(type: "text", nullable: true)]
    protected $note;

    /**
     * Date when the Order was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $created_at;

    /**
     * Many orders can be created by one user.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by_user_id", referencedColumnName: "id")]
    protected $created_by_user;

    /**
     * Date when the Order was last modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $modified_at;

    /**
     * Many orders can be updated by one user.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "modified_by_user_id", referencedColumnName: "id")]
    protected $modified_by_user;

    public function getId() {
        return $this->id;
    }

    public function setOrdinalNumInYear($ordinal_num_in_year) {
        $this->ordinal_num_in_year = $ordinal_num_in_year;
    }

    public function getOrdinalNumInYear() {
        return $this->ordinal_num_in_year;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

    /*
    public function setProject($project) {
        $this->project = $project;
    }

    public function getProject() {
        return $this->project;
    }
    */

    public function setSupplier($supplier) {
        $this->supplier = $supplier;
    }

    public function getSupplier() {
        return $this->supplier;
    }

    public function setTitle($title) {
      $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setIsArchived($is_archived) {
        $this->is_archived = $is_archived;
    }

    public function getIsArchived() {
        return $this->is_archived;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getNote() {
        return $this->note;
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