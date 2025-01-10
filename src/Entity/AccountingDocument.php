<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="App\Repository\AccountingDocumentRepository")
 * @ORM\Table(name="v6__accounting_documents")
 */
class AccountingDocument {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Ordinal number of the document in the current year (redni broj dokumenta u 
   * tekućoj godini)
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $ordinal_num_in_year;

  /**
   * Meny Accounting Documents belongs to the One Accounting Document Type.
   * @ORM\ManyToOne(targetEntity="AccountingDocumentType")
   * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
   * @var int
   */
  protected $type;

  /**
   * Date of Accounting Document
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $date;

  /**
   * Meny Accounting Documents belongs to the One Client.
   * @ORM\ManyToOne(targetEntity="Client")
   * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
   * @var int
   */
  protected $client;

  /**
   * Many Accounting Document has One parent Accounting Document 
   * @ORM\ManyToOne(targetEntity="AccountingDocument")
   * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
   * @var int
   */
  protected $parent;

  /**
   * @ORM\Column(type="string", length=64)
   * @var string
   */
  protected $title;

  /**
   * @ORM\Column(type="boolean")
   * @var boolean
   */
  protected $is_archived;

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
   * Many Clients has ben created from One User.
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
   * Many Clients has ben updated from One User.
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id")
   * @var int
   */
  protected $modified_by_user;

  /**
   * Unidirectional - Many Projects have many payments
   *
   * @ORM\ManyToMany(targetEntity="Payment")
   * @ORM\JoinTable(name="v6__accounting_documents__payments")
   */
  private $payments;

  public function getId() {
    return $this->id;
  }

  public function setOrdinalNumInYear($ordinal_num_in_year) {
    $this->ordinal_num_in_year = $ordinal_num_in_year;
  }
  
  public function getOrdinalNumInYear() {
    return $this->ordinal_num_in_year;
  }

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function setDate($date) {
    $this->date = $date;
  }

  public function getDate() {
    return $this->date;
  }

  public function setClient($client) {
    $this->client = $client;
  }

  public function getClient() {
    return $this->client;
  }

  public function setParent($parent) {
    $this->parent = $parent;
  }

  public function getParent() {
    return $this->parent;
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

  public function getPayments() {
    return $this->payments;
  }
  
}