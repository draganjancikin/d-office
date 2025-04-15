<?php

namespace App\Entity;

use App\Entity\AccountingDocument;
use App\Entity\Client;
use App\Entity\Order;
use App\Entity\ProjectPriority;
use App\Entity\ProjectStatus;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity (repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'v6__projects')]
class Project
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Ordinal number of the project in the current year.
     *
     * @var int
     */
    #[ORM\Column(type: "integer")]
    protected $ordinal_num_in_year;

    /**
     * Many Projects can belong to One Client.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    protected $client;

    /**
     * Project title.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 64)]
    protected $title;

    /**
     * Many Projects can have One Project Priority.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: ProjectPriority::class)]
    #[ORM\JoinColumn(name: "priority_id", referencedColumnName: "id")]
    protected $priority;

    /**
     * Project note.
     *
     * @var string
     */
    #[ORM\Column(type: "text", nullable: true)]
    protected $note;

    /**
     * Many Projects can have One Project Status.
     *  ('1'=>'Is active', '2'=>'On wait', '3'=>'Is archived')
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: ProjectStatus::class)]
    #[ORM\JoinColumn(name: "status_id", referencedColumnName: "id")]
    protected $status;

    /**
     * Unidirectional - Many projects have many accounting documents.
     *
     */
    #[ORM\ManyToMany(targetEntity: AccountingDocument::class)]
    #[ORM\JoinTable(name: "v6__projects__accounting_documents")]
    private $accounting_documents;

    /**
     * Unidirectional - Many Projects have many Orders
     */
    #[ORM\ManyToMany(targetEntity: Order::class)]
    #[ORM\JoinTable(name: "v6__projects__orders")]
    private $orders;

    /**
     * Date when the Project was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $created_at;

    /**
     * Many Clients can be created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by_user_id", referencedColumnName: "id")]
    protected $created_by_user;

    /**
     * Date when the Project was modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $modified_at;

    /**
     * Many Clients can be updated from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "modified_by_user_id", referencedColumnName: "id")]
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