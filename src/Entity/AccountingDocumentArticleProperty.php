<?php

namespace App\Entity;

use App\Entity\AccountingDocumentArticle;
use App\Entity\Property;
use App\Repository\AccountingDocumentArticlePropertyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccountingDocumentArticleProperty entity.
 */
#[ORM\Entity(repositoryClass: AccountingDocumentArticlePropertyRepository::class)]
#[ORM\Table(name: 'v6__accounting_documents__articles__properties')]
class AccountingDocumentArticleProperty
{

    /**
     * Identifier of the Accounting Document Article Property.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many to One ...
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: AccountingDocumentArticle::class)]
    #[ORM\JoinColumn(name: "accounting_document_article_id", referencedColumnName: "id")]
    protected $accounting_document_article;

    /**
     * Many to One ...
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Property::class)]
    #[ORM\JoinColumn(name: "property_id", referencedColumnName: "id")]
    protected $property;

    /**
     * Accounting Document Article Property quantity.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 2)]
    protected $quantity;

    public function getId() {
        return $this->id;
    }

    public function setAccountingDocumentArticle($accounting_document_article) {
        $this->accounting_document_article = $accounting_document_article;
    }

    public function getAccountingDocumentArticle() {
        return $this->accounting_document_article;
    }

    public function setProperty($property) {
        $this->property = $property;
    }

    public function getProperty() {
        return $this->property;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getQuantity() {
        return $this->quantity;
    }

}