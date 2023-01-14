<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table(name="v6__projects")
 */
class Project {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Ordinal number of the project in the current year (redni broj projekta u 
   * tekućoj godini)
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $ordinal_num_in_year;

  /**
   * Meny Projects belongs to the One Client.
   * @ORM\ManyToOne(targetEntity="Client")
   * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
   * @var int
   */
  protected $client;

  /**
   * @ORM\Column(type="string", length=64)
   * @var string
   */
  protected $title;

  /**
   * Many Projects can have One Project Priority.
   * @ORM\ManyToOne(targetEntity="ProjectPriority")
   * @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
   * @var int
   */
  protected $priority;

  /**
   * Project note
   * @ORM\Column(type="text")
   * @var string
   */
  protected $note;

 /**
   * Many Projects can have One Project Status.
   *  ('1'=>'Is active', '2'=>'On wait', '3'=>'Is archived')
   * @ORM\ManyToOne(targetEntity="ProjectStatus")
   * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
   * @var int
   */
  protected $status;

  /**
   * Unidirectional - Many projects have many accounting documents
   *
   * @ORM\ManyToMany(targetEntity="AccountingDocument")
   * @ORM\JoinTable(name="v6__projects__accounting_documents")
   */
  private $accounting_documents;

  /**
   * Unidirectional - Many Projects have many Orders
   *
   * @ORM\ManyToMany(targetEntity="Order")
   * @ORM\JoinTable(name="v6__projects__orders")
   */
  private $orders;

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

  public function getId() {
    return $this->id;
  }

  public function setOrdinalNumInYear($ordinal_num_in_year) {
    $this->ordinal_num_in_year = $ordinal_num_in_year;
  }
  
  public function getOrdinalNumInYear() {
    return $this->ordinal_num_in_year;
  }

  public function setClient($client) {
    $this->client = $client;
  }

  public function getClient() {
    return $this->client;
  }

  public function setTitle($title) {
    $this->title = $title;
  }
  
  public function getTitle() {
    return $this->title;
  }

  public function setPriority($priority) {
    $this->priority = $priority;
  }

  public function getPriority() {
    return $this->priority;
  }

  public function setNote($note) {
    $this->note = $note;
  }

  public function getNote() {
    return $this->note;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getAccountingDocuments() {
    return $this->accounting_documents;
  }

  public function getOrders() {
    return $this->orders;
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