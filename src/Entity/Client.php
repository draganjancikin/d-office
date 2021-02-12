<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6_clients")
 */
class Client {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny Clients belongs to the One Type.
   * @ORM\ManyToOne(targetEntity="ClientType")
   * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
   * @var int
   */
  protected $type;

  /**
   * @ORM\Column(type="string", length=48)
   * @var string
   */
  protected $name;

  /**
   * @ORM\Column(type="string", length=128)
   * @var string
   */
  protected $name_note;

  /**
   * @ORM\Column(type="string", length=13)
   * @var string
   */
  protected $lb;

  /**
   * @ORM\Column(type="boolean")
   * @var boolean
   */
  protected $is_supplier;

  /**
   * Meny Clients live in One Country.
   * @ORM\ManyToOne(targetEntity="Country")
   * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
   */
  protected $country;

  /**
   * Meny Clients live in One City.
   * @ORM\ManyToOne(targetEntity="City")
   * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
   */
  protected $city;

  /**
   * Meny Clients live in One Street.
   * @ORM\ManyToOne(targetEntity="Street")
   * @ORM\JoinColumn(name="street_id", referencedColumnName="id")
   */
  protected $street;

  /**
   * @ORM\Column(type="string", length=8)
   * @var string
   */
  protected $home_number;

  /**
   * @ORM\Column(type="string", length=128)
   * @var string
   */
  protected $address_note;

  /**
   * @ORM\Column(type="text")
   * @var string
   */
  protected $note;

  /**
   * Unidirectional - Many users have many contacts
   *
   * @ORM\ManyToMany(targetEntity="Contact")
   * @ORM\JoinTable(name="v6_clients_contacts")
   */
  private $contacts;

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

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function setName($name) {
    $this->name = $name;
  }
  
  public function getName() {
    return $this->name;
  }

  public function setNameNote($name_note) {
    $this->name_note = $name_note;
  }

  public function getNameNote() {
    return $this->name_note;
  }

  public function setLb($lb) {
    $this->lb = $lb;
  }

  public function getLb() {
    return $this->lb;
  }

  public function setIsSupplier($is_supplier) {
    $this->is_supplier = $is_supplier;
  }

  public function getIsSupplier() {
    return $this->is_supplier;
  }

  public function setCountry($country) {
    $this->country = $country;
  }

  public function getCountry() {
    return $this->country;
  }

  public function setCity($city) {
    $this->city = $city;
  }

  public function getCity() {
    return $this->city;
  }

  public function setStreet($street) {
    $this->street = $street;
  }

  public function getStreet() {
    return $this->street;
  }

  public function setHomeNumber($home_number) {
    $this->home_number = $home_number;
  }

  public function getHomeNumber() {
    return $this->home_number;
  }

  public function setAddressNote($address_note) {
    $this->address_note = $address_note;
  }

  public function getAddressNote() {
    return $this->address_note;
  }

  public function setNote($note) {
    $this->note = $note;
  }

  public function getNote() {
    return $this->note;
  }

  public function getContacts() {
    return $this->contacts;
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