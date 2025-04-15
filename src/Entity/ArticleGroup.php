<?php

namespace App\Entity;

use App\Repository\ArticleGroupRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleGroup entity.
 */
#[ORM\Entity(repositoryClass: ArticleGroupRepository::class)]
#[ORM\Table(name: 'v6__article__groups')]
class ArticleGroup
{

    /**
     * Identifier of the Article Group.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Article Group name.
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