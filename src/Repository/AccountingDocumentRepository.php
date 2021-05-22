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
   * @return 
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
  
}
