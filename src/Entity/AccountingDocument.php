<?php

namespace App\Entity;

use App\Entity\AccountingDocumentType;
use App\Entity\Client;
use App\Entity\Payment;
use App\Entity\User;
use App\Repository\AccountingDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingDocumentRepository::class)]
#[ORM\Table(name: 'v6__accounting_documents')]
class AccountingDocument
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Ordinal number of the document in the current year.
     *
     * @var int
     */
    #[ORM\Column(type: "integer")]
    protected $ordinal_num_in_year;

    /**
     * Many Accounting Documents belongs to the One Accounting Document Type.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: AccountingDocumentType::class)]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id")]
    protected $type;

    /**
     * Date of Accounting Document
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $date;

    /**
     * Many Accounting Documents belongs to the One Client.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    protected $client;

    /**
     * Many Accounting Document has One parent Accounting Document.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: AccountingDocument::class)]
    #[ORM\JoinColumn(name: "parent_id", referencedColumnName: "id")]
    protected $parent;

    /**
     * Accounting Document title.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 64)]
    protected $title;

    /**
     * Accounting Document is archived flag.
     *
     * @var boolean
     */
    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    protected $is_archived;

    /**
     * Accounting Document note
     *
     * @var string
     */
    #[ORM\Column(type: "text", nullable: true)]
    protected $note;

    /**
     * Date of Accounting Document creation.
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
     * Date of Accounting Document modification.
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

    /**
     * Bidirectional - Many Projects have many payments
     */
    #[ORM\ManyToMany(targetEntity: Payment::class)]
    #[ORM\JoinTable(
      name: "v6__accounting_documents__payments",
      joinColumns: [new ORM\JoinColumn(name: "accountingdocument_id", referencedColumnName: "id")],
      inverseJoinColumns: [new ORM\JoinColumn(name: "payment_id", referencedColumnName: "id")]
    )]
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