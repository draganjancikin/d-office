<?php

namespace App\Entity;

use App\Entity\Article;
use App\Entity\Property;
use App\Repository\ArticlePropertyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleProperty Entity.
 */
#[ORM\Entity(repositoryClass: ArticlePropertyRepository::class)]
#[ORM\Table(name: 'v6__articles__properties')]
class ArticleProperty
{

    /**
     * Identifier of the entity.
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many  to One ...
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Article::class)]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')]
    protected $article;

    /**
     * Many to One ...
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Property::class)]
    #[ORM\JoinColumn(name: 'property_id', referencedColumnName: 'id')]
    protected $property;

    /**
     * Article property minimum size.
     *
     * @var float
     */
    #[ORM\Column(name: 'min_size', type: 'decimal', precision: 11, scale: 0)]
    protected $min_size;

    /**
     * Article property maximum size.
     *
     * @var float
     */
    #[ORM\Column(name: 'max_size', type: 'decimal', precision: 11, scale: 0)]
    protected $max_size;

    public function getId() {
        return $this->id;
    }

    public function setArticle($article) {
        $this->article = $article;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setProperty($property) {
        $this->property = $property;
    }

    public function getProperty() {
        return $this->property;
    }

    public function setMinSize($min_size) {
        $this->min_size = $min_size;
    }

    public function getMinSize() {
        return $this->min_size;
    }

    public function setMaxSize($max_size) {
        $this->max_size = $max_size;
    }

    public function getMaxSize() {
        return $this->max_size;
    }

}