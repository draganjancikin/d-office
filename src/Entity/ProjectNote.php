<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6__projects__notes")
 */
class ProjectNote {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny Project Notes belongs to the One Project.
   * @ORM\ManyToOne(targetEntity="Project")
   * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
   * @var int
   */
  protected $project;

  /**
   * @ORM\Column(type="text")
   * @var string
   */
  protected $note;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $created_at;

  /**
   * Many ProjectNotes has ben created from One User.
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
   * Many ProjectNotes has ben updated from One User.
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id")
   * @var int
   */
  protected $modified_by_user;

  public function getId() {
    return $this->id;
  }

  public function setProject($project) {
    $this->project = $project;
  }

  public function getProject() {
    return $this->project;
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

}