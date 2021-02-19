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


  // is_archived

  // status
  
  /**
   * Accounting Document note
   * @ORM\Column(type="text")
   * @var string
   */
  protected $note;

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

  public function setNote($note) {
    $this->note = $note;
  }
  
  public function getNote() {
    return $this->note;
  }

}