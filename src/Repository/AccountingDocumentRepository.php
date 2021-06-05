<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class AccountingDocumentRepository extends EntityRepository {

  /**
   * Method that return number of AccountingDocuments
   *
   * @return int
   */
  public function getNumberOfAccountingDocuments() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(ad.id)')
        ->from('Roloffice\Entity\AccountingDocument','ad');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return last AccountingDocuments
   * 
   * @param int $type Type of AccountingDocument
   * @param int $limit Number of AccountingDocuments
   * 
   * @return array
   */
  public function getLast($type, $isArchived, $limit) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('ad')
      ->from('Roloffice\Entity\AccountingDocument', 'ad')
      ->where(
        $qb->expr()->andX(
          $qb->expr()->eq('ad.type', $type),
          $qb->expr()->eq('ad.is_archived', $isArchived)
        )
      )
      ->orderBy('ad.id', 'DESC')
      ->setMaxResults( $limit );
    return $qb->getQuery()->getResult();
  }

  /**
   * Method that return all Articles on AccountingDocument
   * 
   * @param int $ad_id AccountingDocument ID
   * 
   * @return array
   */
  public function getArticles($ad_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('ada')
        ->from('Roloffice\Entity\AccountingDocumentArticle', 'ada')
        ->join('ada.article', 'a', 'ada.article = a.id')
        ->where(
          $qb->expr()->eq('ada.accounting_document', $ad_id),
        )
        ->orderBy('ada.id', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result;
  }

  /**
   * Methot that return avans income by AccountingDocument
   * 
   * @param $accd_id
   *  AccountingDocument ID
   * 
   * @return float
   */
  public function getAvans($accd_id) {
    // Get all payment for $accd_id where payment type = 1 or 2 (avans gotovinski, avans virmanski)
    $avans = 0;
    $payments = $this->_em->find('\Roloffice\Entity\AccountingDocument', $accd_id)->getPayments();
    foreach ($payments as $payment) {
      if ($payment->getType()->getId() == 1 || $payment->getType()->getId() == 2) {
        // Sabrati sve avanse
        $avans = $avans + $payment->getAmount();
      }
    }
    return $avans;
  }

  /**
   * Method that return income by AccountingDocument
   * 
   * @param $accd_id
   *  AccountingDocument ID
   * 
   * @return float
   */
  public function getIncome($accd_id) {
    // Get all payment for $accd_id where payment type = 3 or 4 (uplata gotovinska, uplata virmanska)
    $income = 0;
    $payments = $this->_em->find('\Roloffice\Entity\AccountingDocument', $accd_id)->getPayments();
    foreach ($payments as $payment) {
      if ($payment->getType()->getId() == 3 || $payment->getType()->getId() == 4) {
        // Sabrati sve uplate
        $income = $income + $payment->getAmount();
      }
    }
    return $income;
  }

  /**
   * Method that return previous AccountingDocument
   * 
   * @param int $accd_id
   * @param int $accd_type_id
   * 
   * @return object
   */
  public function getPrevious($accd_id, $accd_type_id){
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('ad')
        ->from('Roloffice\Entity\AccountingDocument', 'ad')
        ->where(
          $qb->expr()->andX(
            $qb->expr()->lt('ad.id', $accd_id),
            $qb->expr()->eq('ad.type', $accd_type_id)
          )
        )
        ->orderBy('ad.id', 'DESC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result[0];
  }

  /**
   * Method that return next AccountingDocument
   * 
   * @param int $accd_id
   * @param int $accd_type_id
   * 
   * @return object
   */
  public function getNext($accd_id, $accd_type_id){
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('ad')
        ->from('Roloffice\Entity\AccountingDocument', 'ad')
        ->where(
          $qb->expr()->andX(
            $qb->expr()->gt('ad.id', $accd_id),
            $qb->expr()->eq('ad.type', $accd_type_id)
          )
        )
        ->orderBy('ad.id', 'DESC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result[0];
  }
}
