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
   * Ordinal number of the document in the current year (redni broj dokumenta u 
   * tekućoj godini)
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $ordinal_num_in_year;

  // supplier_id


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

  // note


  public function getId() {
    return $this->id;
  }

}