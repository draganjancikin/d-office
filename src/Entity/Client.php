<?php

namespace App\Entity;

use App\Entity\City;
use App\Entity\ClientType;
use App\Entity\Contact;
use App\Entity\Country;
use App\Entity\Street;
use App\Entity\User;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Client entity.
 */
#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'v6__clients')]
class Client
{

    /**
     * Identifier of the Client.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected int $id;

    /**
     * Many clients belong to one type.
     *
     * @var ClientType|null
     */
    #[ORM\ManyToOne(targetEntity: ClientType::class)]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id")]
    protected $type;

    /**
     * Client name.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 48)]
    protected string $name;

    /**
     * Client's name note.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 128)]
    protected string $name_note = '';

    /**
     * Client's LB number.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 13)]
    protected $lb;

    /**
     * Supplier flag.
     *
     * @var boolean
     */
    #[ORM\Column(type: "boolean")]
    protected bool $is_supplier;

    /**
     * Many Clients live in One Country.
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: "country_id", referencedColumnName: "id")]
    protected $country;

    /**
     * Many Clients live in One City.
     */
    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(name: "city_id", referencedColumnName: "id")]
    protected $city;

    /**
     * Many Clients live in One Street.
     */
    #[ORM\ManyToOne(targetEntity: Street::class)]
    #[ORM\JoinColumn(name: "street_id", referencedColumnName: "id")]
    protected $street;

    /**
     * Client's home number.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 8)]
    protected string $home_number = '';

    /**
     * Client's address note.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 128)]
    protected string $address_note = '';

    /**
     * Client's note.
     *
     * @var string
     */
    #[ORM\Column(type: "text")]
    protected string $note = '';

    /**
     * Unidirectional - Many users have many contacts
     *
  //   * @ORM\ManyToMany(targetEntity="Contact")
  //   * @ORM\JoinTable(name="v6__clients__contacts")
     */
    #[ORM\ManyToMany(targetEntity: Contact::class, inversedBy: 'clients')]
    #[ORM\JoinTable(
        name: 'v6__clients__contacts',
        joinColumns: [new ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id')]
    )]
    private Collection $contacts;

    /**
     * Date when the client was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $created_at;

    /**
     * Many Clients have been created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by_user_id", referencedColumnName: "id")]
    protected $created_by_user;

    /**
     * Date when the client was modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $modified_at;

    /**
     * Many Clients have been updated from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "modified_by_user_id", referencedColumnName: "id")]
    protected $modified_by_user;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->name_note = '';
        $this->lb = '';
        $this->home_number = '';
        $this->address_note = '';
        $this->note = '';
        $this->created_at = new \DateTime();
        $this->modified_at = new \DateTime();
    }

    public function getId() {
        return $this->id;
    }

    public function setType(?ClientType $type): void
    {
        $this->type = $type;
    }

    public function getType(): ?ClientType
    {
        return $this->type;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setNameNote($name_note) {
        $this->name_note = $name_note === null ? '' : $name_note;
    }

    public function getNameNote() {
        return $this->name_note;
    }

    public function setLb($lb) {
        $this->lb = $lb === null ? '' : $lb;
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
        $this->home_number = $home_number === null ? '' : $home_number;
    }

    public function getHomeNumber() {
        return $this->home_number;
    }

    public function setAddressNote($address_note) {
        $this->address_note = $address_note === null ? '' : $address_note;
    }

    public function getAddressNote() {
        return $this->address_note;
    }

    public function setNote($note) {
        $this->note = $note === null ? '' : $note;
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