<?php

namespace App\Entity;

use App\Entity\AccountingDocument;
use App\Entity\Article;
use App\Repository\AccountingDocumentArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccountingDocumentArticle entity.
 */
#[ORM\Entity(repositoryClass: AccountingDocumentArticleRepository::class)]
#[ORM\Table(name: 'v6__accounting_documents__articles')]
class AccountingDocumentArticle
{

    /**
     * Identifier of the Accounting Document Article.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    protected $id;

    /**
     * Many to one ...
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: AccountingDocument::class)]
    #[ORM\JoinColumn(name: "accounting_document_id", referencedColumnName: "id")]
    protected $accounting_document;

    /**
     * Many to One ...
     *
     * @var int
     */
    #[ORM\ManyToOne(targetEntity: Article::class)]
    #[ORM\JoinColumn(name: "article_id", referencedColumnName: "id")]
    protected $article;

    /**
     * Accounting Document Article pieces.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale:0)]
    protected $pieces;

    /**
     * Accounting Document Article price.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 4)]
    protected $price;

    /**
     * Accounting Document Article discount.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 2)]
    protected $discount;

    /**
     * Accounting Document Article tax.
     *
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 2)]
    protected $tax;

    /**
     * Accounting Document Article weight.
     * @var float
     */
    #[ORM\Column(type: "decimal", precision: 11, scale: 0)]
    protected $weight;

    /**
     * Accounting Document Article note.
     *
     * @var string
     */
    #[ORM\Column(type: "text", length: 255)]
    protected $note;

    public function getId() {
        return $this->id;
    }

    public function setAccountingDocument($accounting_document) {
        $this->accounting_document = $accounting_document;
    }

    public function getAccountingDocument() {
        return $this->accounting_document;
    }

    public function setArticle($article) {
        $this->article = $article;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setPieces($pieces) {
        $this->pieces = $pieces;
    }

    public function getPieces() {
        return $this->pieces;
    }

    public function setPrice($price) {
       $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    public function getDiscount() {
        return $this->discount;
    }

    public function setTax($tax) {
        $this->tax = $tax;
    }

    public function getTax() {
        return $this->tax;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function getNote() {
        return $this->note;
    }

}