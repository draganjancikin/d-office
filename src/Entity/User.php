<?php

namespace App\Entity;

use App\Entity\UserRole;
use Doctrine\ORM\Mapping as ORM;

/**
 * User entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__users')]
class User
{

    /**
     * Identifier of the User.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Username of the User.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 25, unique: true)]
    protected $username;

    /**
     * User's password.
     *
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 64)]
    protected $password;

    /**
     * Many Users belong to the One Role.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: UserRole::class)]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    protected $role;

    /**
     * Date when the user was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $created_at;

    /**
     * Many User have been created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by_user_id", referencedColumnName: "id")]
    protected $created_by_user;

    /**
     * Date when the user was modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    protected $modified_at;

    /**
     * Many User have been updated from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "modified_by_user_id", referencedColumnName: "id")]
    protected $modified_by_user;

    public function getId() {
        return $this->id;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setRoleId($role_id) {
        $this->role_id = $role_id;
    }

    public function getRoleId() {
        return $this->role_id;
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