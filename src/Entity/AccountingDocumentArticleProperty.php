<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="App\Repository\AccountingDocumentArticlePropertyRepository")
 * @ORM\Table(name="v6__accounting_documents__articles__properties")
 */
class AccountingDocumentArticleProperty {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue
   * @var int
   */
  protected $id;

  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="AccountingDocumentArticle")
   * @ORM\JoinColumn(name="accounting_document_article_id", referencedColumnName="id")
   * @var int
   */
  protected $accounting_document_article;
  
  /**
   * Meny ...
   * @ORM\ManyToOne(targetEntity="Property")
   * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
   * @var int
   */
  protected $property;

  /**
   * @ORM\Column(type="decimal", precision=11, scale=2)
   * @var float
   */
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