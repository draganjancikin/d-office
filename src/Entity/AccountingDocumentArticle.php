<?php

namespace Roloffice\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="Roloffice\Repository\AccountingDocumentArticleRepository")
 * @ORM\Table(name="v6__accounting_documents__articles")
 */
class AccountingDocumentArticle {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="AccountingDocument")
   * @ORM\JoinColumn(name="accounting_document_id", referencedColumnName="id")
   * @var int
   */
  protected $accounting_document;

  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="Article")
   * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
   * @var int
   */
  protected $article;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $pieces;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=4)
   * @var float
   */
  protected $price;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=2)
   * @var float
   */
  protected $discount;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=2)
   * @var float
   */
  protected $tax;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=0)
   * @var float
   */
  protected $weight;

  /**
   * @ORM\Column(type="text")
   * @var string
   */
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