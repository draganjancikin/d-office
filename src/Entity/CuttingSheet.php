<?php

namespace App\Entity;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\CuttingSheetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CuttingSheetRepository::class)]
#[ORM\Table(name: 'v6__cutting_sheets')]
class CuttingSheet
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Ordinal number of the Order in the current year.
     *
     * @var int
     */
    #[ORM\Column(type: "integer")]
    protected $ordinal_num_in_year;

    /**
     * Many CuttingSheets belongs to the One Client.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    protected $client;

    /**
     * Date and time when the Order was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $created_at;

    /**
     * Many Orders have been created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by_user_id", referencedColumnName: "id")]
    protected $created_by_user;

    /**
     * Date and time when the Order was last modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $modified_at;

    /**
     * Many Orders have been updated from One User.
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