<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project task note entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'v6__projects_tasks_notes')]
class ProjectTaskNote
{

    /**
     * Identifier of the project task note.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

      /**
     * Many Project Task Notes belongs to the One Project Task.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: ProjectTask::class)]
    #[ORM\JoinColumn(name: 'project_task_id', referencedColumnName: 'id')]
    protected $project_task;

    /**
     * Project task note text.
     *
     * @var string
     */
    #[ORM\Column(type: 'text')]
    protected $note;

    /**
     * Date and time when the project task note was created.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $created_at;

    /**
     * Many ProjectTaskNotes have been created from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id')]
    protected $created_by_user;

    /**
     * Date and time when the project task note was last modified.
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $modified_at;

    /**
     * Many ProjectTaskNotes has ben updated from One User.
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'modified_by_user_id', referencedColumnName: 'id')]
    protected $modified_by_user;

    public function getId() {
        return $this->id;
    }

    public function setProjectTask($project_task) {
        $this->project_task = $project_task;
    }

    public function getProjectTask() {
        return $this->project_task;
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
