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
  
}
