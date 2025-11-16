<?php

namespace App\Entity;

use App\Entity\ContactType;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contact entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__contacts')]
class Contact
{

    /**
     * Identifier of the Contact.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected int $id;

    /**
     * Many Contacts belong to the One Type.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: ContactType::class )]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id")]
    protected $type;

    /**
     * Contact's body.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 48)]
    protected string $body;

    /**
     * Contact's note.
     *
     * @var string
     */
    #[ORM\Column(type: "string")]
    protected string $note;

    /**
     * Date when the contact was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $created_at;

    /**
     * Many Contacts have been created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by_user_id", referencedColumnName: "id")]
    protected $created_by_user;

    /**
     * Date when the contact was modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $modified_at;

    /**
     * Many Contacts have been updated from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "modified_by_user_id", referencedColumnName: "id")]
    protected $modified_by_user;

    /**
     * @var Collection|ArrayCollection
     */
    #[ORM\ManyToMany(targetEntity: Client::class, mappedBy: 'contacts')]
    private Collection $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->note = '';
    }


    public function getId() {
        return $this->id;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function getBody() {
        return $this->body;
    }

    public function setNote($note) {
        $this->note = $note === null ? '' : $note;
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

}