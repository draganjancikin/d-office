<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Employee entity.
 */
#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table(name: 'v6__employees')]
class Employee {

    /**
     * Identifier of the Employee.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Employee's name.
     *
     * @var string
     */
    #[ORM\Column(type: "string", length: 48)]
    protected $name;

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

}