<?php

namespace App\Entity;

use App\Entity\Project;
use App\Entity\ProjectTaskType;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project task entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__projects__tasks')]
class ProjectTask
{

    /**
     * Identifier of the project task.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many Projects Tasks belongs to the One Project.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id')]
    protected $project;

    /**
     * Date and time when the project task was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $created_at;

    /**
     * Many Clients can be created by One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id')]
    protected $created_by_user;

    /**
     * Date and time when the project task was last modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $modified_at;

    /**
     * Many clients can be updated by one user.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'modified_by_user_id', referencedColumnName: 'id')]
    protected $modified_by_user;

    /**
     * Many Project Tasks belongs to the One Project Takt Type.
     *    ('1'=>'Merenje', '2'=>'Ponuda', '3'=>'Nabavka', '4'=>'Proizvodnja',
     *    '5'=>'Isporuka', '6'=>'Montaža', '7'=>'Rreklamacija', '8'=>'Popravka')
     */
    #[ORM\ManyToOne(targetEntity: ProjectTaskType::class)]
    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id')]
    protected $type;

    /**
     * Project Task Title.
     *
     * @var string
     */
    #[ORM\Column(type: 'string', length: 64)]
    protected $title;

     /**
     * Many Project Tasks can have One Project Task Status.
     *    ('1'=>'Za realizaciju', '2'=>'U realizaciji', '3'=>'Realizovano')
     */
    #[ORM\ManyToOne(targetEntity: ProjectTaskStatus::class)]
    #[ORM\JoinColumn(name: 'status_id', referencedColumnName: 'id')]
    protected $status;

      /**
     * Many Project Tasks belongs to the One Employee.
     */
      #[ORM\ManyToOne(targetEntity: Employee::class)]
      #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id')]
    protected $employee;

    /**
     * Date and time when the project task starts.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $start_date;

    /**
     * Date and time when the project task ends.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
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