<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v6__projects__tasks")
 */
class ProjectTask {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Many Projects Tasks belongs to the One Project.
   * @ORM\ManyToOne(targetEntity="Project")
   * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
   * @var int
   */
  protected $project;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $created_at;

  /**
   * Many Clients can be created by One User.
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
   * Many clients can be updated by one user.
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id")
   * @var int
   */
  protected $modified_by_user;

  /**
   * Many Project Tasks belongs to the One Project Tast Type.
   *    ('1'=>'Merenje', '2'=>'Ponuda', '3'=>'Nabavka', '4'=>'Proizvodnja',
   *    '5'=>'Isporuka', '6'=>'Montaža', '7'=>'Rreklamacija', '8'=>'Popravka')
   * @ORM\ManyToOne(targetEntity="ProjectTaskType")
   * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
   * @var int
   */
  protected $type;

    /**
   * @ORM\Column(type="string", length=64)
   * @var string
   */
  protected $title;

   /**
   * Many Project Tasks can have One Project Task Status.
   *    ('1'=>'Za realizaciju', '2'=>'U realizaciji', '3'=>'Realizovano') 
   * @ORM\ManyToOne(targetEntity="ProjectTaskStatus")
   * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
   * @var int
   */
  protected $status;

    /**
   * Meny Projects belongs to the One Employee.
   * @ORM\ManyToOne(targetEntity="Employee")
   * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
   * @var int
   */
  protected $employee;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $start_date;

  /**
   * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
   * @var DateTime
   */
  protected $end_date;

  public function getId() {
    return $this->id;
  }

  public function setProject($project) {
    $this->project = $project;
  }

  public function getProject() {
    return $this->project;
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

  public function setType($type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function setTitle($title) {
    $this->title = $title;
  }
  
  public function getTitle() {
    return $this->title;
  }

  public function setStatus($status) {
    $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }

  public function setEmployee($employee) {
    $this->employee = $employee;
  }

  public function getEmployee() {
    return $this->employee;
  }

  public function setStartDate(\DateTime $start_date) {
    $this->start_date = $start_date;
  }

  public function getStartDate() {
    return $this->start_date;
  }

  public function setEndDate(\DateTime $end_date) {
    $this->end_date = $end_date;
  }

  public function getEndDate() {
    return $this->end_date;
  }

}