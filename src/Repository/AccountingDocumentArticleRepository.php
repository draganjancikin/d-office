<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class AccountingDocumentArticleRepository extends EntityRepository {

  /**
   * Method that return AccountingDocument Article Properties
   * 
   * @param int $ad_article_id AccountingDocument Article ID
   * 
   * @return array
   */
  public function getProperties($ad_article_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('adap')
      ->from('Roloffice\Entity\AccountingDocumentArticleProperty', 'adap')
      ->join('adap.property', 'p', 'adap.property = p.id')
      ->where(
        $qb->expr()->eq('adap.accounting_document_article', $ad_article_id),
        );
      $query = $qb->getQuery();
      $result = $query->getResult();
    
    return $result;
  }

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
    $properties = $this->getProperties($ad_article_id);
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


}
