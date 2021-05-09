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
  
}
