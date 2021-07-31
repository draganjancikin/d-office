<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class AccountingDocumentRepository extends EntityRepository {

  /**
   * Method that return number of AccountingDocuments with given AccountingDocumentType ID
   *
   * @param $type_id
   * 
   * @return int
   */
  public function getNumberOfAccountingDocuments($type_id = NULL) {
    $qb = $this->_em->createQueryBuilder();

    if ($type_id) {
      // If exist type_id query only AccountingDocument for given type_id
      $qb->select('count(ad.id)')
      ->from('Roloffice\Entity\AccountingDocument','ad')
      ->where(
        $qb->expr()->eq('ad.type', $type_id),
      );
    } else {
      // If type_id dont exist query all Accounting Document
      $qb->select('count(ad.id)')
        ->from('Roloffice\Entity\AccountingDocument','ad');
    }
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
   * @param int $accd_id
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
   * @param int $accd_id
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
        ->orderBy('ad.id', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return ( isset($result[0]) ? $result[0] : NULL );
  }

  /**
   * Method that set Ordinal AccountingDocument number in year for given AccountingDocument.
   *
   * @param int $acd_id
   *  AccountingDocument ID
   * @return void
   */
  public function setOrdinalNumInYear($acd_id) {
    
    // Given AccountingDocument.
    $acd = $this->_em->find("\Roloffice\Entity\AccountingDocument", $acd_id);
    // Type of given AccountingDocument.
    $acd__type_id = $acd->getType()->getId();
    
    // count number of records in database table v6__accounting_documents for given AccountingDocumentType
    $acd_count = $this->getNumberOfAccountingDocuments($acd__type_id);

    // get year of last AccountingDocument
    $year_of_last_acd = $this->getLastAccountingDocument()->getCreatedAt()->format('Y');
    
    // get ordinal number in year of AccountingDocument before last with same type_id
    $ordinal_number_of_acd_before_last = $this->getAccountingDocumentBeforeLast($acd__type_id)->getOrdinalNumInYear();
    
    // year of AccountingDocument before last
    $year_of_acd_before_last = $this->getAccountingDocumentBeforeLast($acd__type_id)->getCreatedAt()->format('Y');

    if($acd_count == 0){  // prvi slučaj kada je tabela $table prazna
    
      return die("Table of AccountingDocument is empty!");
    
    }elseif($acd_count == 1){  // drugi slučaj - kada postoji jedan unos u tabeli $table
    
      $ordinal_number_in_year = 1; // pošto postoji samo jedan unos u tabelu $table $b_on dobija vrednost '1'
    
    }else{  // svi ostali slučajevi kada ima više od jednog unosa u tabeli $table
    
      if($year_of_last_acd < $year_of_acd_before_last){
        return die("Godina zadnjeg unosa je manja od godine predzadnjeg unosa! Verovarno datum nije podešen");
      }elseif($year_of_last_acd == $year_of_acd_before_last){ //nema promene godine
        $ordinal_number_in_year = $ordinal_number_of_acd_before_last + 1;
      }else{  // došlo je do promene godine
        $ordinal_number_in_year = 1;
      }
    
    }

    // update ordinal_number_in_year
    $acd = $this->_em->find('\Roloffice\Entity\AccountingDocument', $acd_id);

    if ($acd === null) {
      echo "AccountingDocument with ID $acd_id does not exist.\n";
      exit(1);
    }

    $acd->setOrdinalNumInYear($ordinal_number_in_year);

    $this->_em->flush();

  }

  /**
   * Method that rerurn ID of last AccountingDocument in db table
   *
   * @return object
   */
  public function getLastAccountingDocument() {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('ad')
        ->from('Roloffice\Entity\AccountingDocument', 'ad')
        ->orderBy('ad.id', 'DESC')
        ->setMaxResults(1);
    $query = $qb->getQuery();
    $last_accd = $query->getResult()[0];
    
    return $last_accd;
  }

  /**
   * Method that rerurn ID of AccountingDocument before last in db table for given AccountingDocumentType
   *
   * @param int $type_id
   * 
   * @return object
   */
  public function getAccountingDocumentBeforeLast($type_id) {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('ad')
        ->from('Roloffice\Entity\AccountingDocument', 'ad')
        ->orderBy('ad.id', 'DESC')
        ->where(
          $qb->expr()->eq('ad.type', $type_id)
        )
        ->setMaxResults(2);
    $query = $qb->getQuery();
    $accd_before_last = $query->getResult()[1];
    
    return $accd_before_last;
  }

}
