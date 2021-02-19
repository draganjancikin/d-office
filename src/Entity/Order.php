<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="Roloffice\Repository\OrderRepository")
 * @ORM\Table(name="v6_orders")
 */
class Order {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Ordinal number of the Order in the current year (redni broj dokumenta u 
   * tekućoj godini)
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $ordinal_num_in_year;

    /**
   * Date of Order
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $date;

  /**
   * Meny Order belongs to the One Supplier.
   * @ORM\ManyToOne(targetEntity="Client")
   * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
   * @var int
   */
  protected $supplier;

  /**
   * Meny Order belongs to the One Project.
   * @ORM\ManyToOne(targetEntity="Project")
   * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
   * @var int
   */
  protected $project;

  /**
   * @ORM\Column(type="string", length=48)
   * @var string
   */
  protected $title;

  /**
   * @ORM\Column(type="boolean")
   * @var boolean
   */
  protected $is_archived;
  
  /**
   * Order status: 1 => 'draft', 2 => 'ordered', 3 => 'arrived'.
   * (1 => 'nacrt', 2 => 'poručeno', 3 => 'stiglo')
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $status;
  
  /**
   * Accounting Document note
   * @ORM\Column(type="text")
   * @var string
   */
  protected $note;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $created_at;

  /**
   * Many Orders has ben created from One User.
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id")
   * @var int
   */
  protected $created_by_user;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $modified_at;

  /**
   * Many Orders has ben updated from One User.
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id")
   * @var int
   */
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

  public function setProject($project) {
    $this->project = $project;
  }

  public function getProject() {
    return $this->project;
  }

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