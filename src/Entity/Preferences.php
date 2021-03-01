<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6_preferences")
 */
class Preferences {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=4)
   * @var float
   */
  protected $kurs;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=4)
   * @var float
   */
  protected $tax;

  public function getId() {
    return $this->id;
  }

  public function setKurs($kurs) {
    $this->kurs = $kurs;
  }

  public function getKurs() {
    return $this->kurs;
  }

  public function setTax($tax) {
    $this->tax = $tax;
  }

  public function getTax() {
    return $this->tax;
  }

}
