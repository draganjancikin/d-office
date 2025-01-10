<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="App\Repository\PaymentRepository")
 * @ORM\Table(name="v6__payments")
 */
class Payment {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny Payments belongs to the One Payment Type.
   * @ORM\ManyToOne(targetEntity="PaymentType")
   * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
   * @var int
   */
  protected $type;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=4)
   * @var float
   */
  protected $amount;

  /**
   * @ORM\Column(type="text")
   * @var string
   */
  protected $note;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $date;

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

  public function getId() {
    return $this->id;
  }

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function setAmount($amount) {
    $this->amount = $amount;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function setNote($note) {
    $this->note = $note;
  }

  public function getNote() {
    return $this->note;
  }

  public function setDate(\DateTime $date) {
    $this->date = $date;
  }

  public function getDate() {
    return $this->date;
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

}