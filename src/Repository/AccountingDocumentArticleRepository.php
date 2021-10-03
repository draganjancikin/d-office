<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class AccountingDocumentArticleRepository extends EntityRepository {

  /**
   * Method that return quantity of article. If article dont have property
   * quantity = 1, if article have one property quantity = property/100. If
   * article have two peoperties, quantity = property_one * proerty_two.
   * 
   * @param int $ad_article_id
   * @param float $min_calc_measure
   * @param float $pieces
   *  
   * @return float 
   */
  public function getQuantity($ad_article_id, $min_calc_measure, $pieces) {
    
    $properties = $this->_em->getRepository('\Roloffice\Entity\AccountingDocumentArticleProperty')->findBy(array('accounting_document_article' => $ad_article_id), array());
    
    $temp_quantity = 1;
    
    foreach ($properties as $property) {
      $temp_quantity = $temp_quantity * ( $property->getQuantity()/100 );
    }

    if($temp_quantity < $min_calc_measure) $temp_quantity = $min_calc_measure;

    $quantity = round($pieces * $temp_quantity, 2);

    return $quantity;
  }

  /**
   *  Method that return Tax Base by Article on AccountingDocument.
   * 
   * @param float $price
   *  Price of Article.
   * @param float $discount
   *  Discount of Article.
   * @param float $quantity
   *  Quantity of Article.
   * 
   * @return float 
   */
  public function getTaxBase($price, $discount, $quantity) {
    return ($price - round( $price * ($discount/100), 4 ) ) * $quantity;
  }

  /**
   * Methot that return Amount of Tax by Article on AccountingDocument.
   * 
   * @param float $tax_base
   * @param float $tax
   * @param float $kurs
   * 
   * @return float 
   */
  public function getTaxAmount($tax_base, $tax) {
    return round( ($tax_base * ($tax/100)), 4 );
  }

  /**
   * @param float $tax_base
   * @param float $tax_amount
   */
  public function getSubTotal($tax_base, $tax_amount) {
    return $tax_base + $tax_amount;
  }

  /**
   * Method that duplicate article in document (Accounting Document)
   * 
   * @param integer $accounting_document___article__id
   */
  public function duplicateArticleInAccountingDocument($accounting_document___article__id){
    $accounting_document__article = $this->_em->find("\Roloffice\Entity\AccountingDocumentArticle", $accounting_document___article__id);

    $article = $this->_em->find("\Roloffice\Entity\Article", $accounting_document__article->getArticle()->getId());
        
    $newAccountingDocumentArticle = new \Roloffice\Entity\AccountingDocumentArticle();

    $newAccountingDocumentArticle->setAccountingDocument($accounting_document__article->getAccountingDocument());
    $newAccountingDocumentArticle->setArticle($accounting_document__article->getArticle());
    $newAccountingDocumentArticle->setPieces($accounting_document__article->getPieces());
    $newAccountingDocumentArticle->setPrice($accounting_document__article->getPrice());
    $newAccountingDocumentArticle->setDiscount($accounting_document__article->getDiscount());
    $newAccountingDocumentArticle->setTax($accounting_document__article->getTax());
    $newAccountingDocumentArticle->setWeight($accounting_document__article->getWeight());
    $newAccountingDocumentArticle->setNote($accounting_document__article->getNote());

    $this->_em->persist($newAccountingDocumentArticle);
    $this->_em->flush();


    //insert Article properties in table v6__accounting_documents__articles__properties
    $article_properties = $this->_em->getRepository('\Roloffice\Entity\ArticleProperty')->getArticleProperties($article->getId());
    
    foreach ($article_properties as $article_property) {
      // insert to table v6__accounting_documents__articles__properties
      $newAccountingDocumentArticleProperty = new \Roloffice\Entity\AccountingDocumentArticleProperty();
    
      $newAccountingDocumentArticleProperty->setAccountingDocumentArticle($newAccountingDocumentArticle);
      $newAccountingDocumentArticleProperty->setProperty($article_property->getProperty());
      $newAccountingDocumentArticleProperty->setQuantity(0);

      $this->_em->persist($newAccountingDocumentArticleProperty);
      $this->_em->flush();
    }
    
  }

}
