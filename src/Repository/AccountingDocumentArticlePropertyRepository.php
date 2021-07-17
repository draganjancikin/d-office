<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class AccountingDocumentArticlePropertyRepository extends EntityRepository {

  /**
   * Method that return Properties by AccountingDocumentArticle.
   * 
   * @return 
   */
  public function getAccountingDocumentArticleProperties($accounting_document__article) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('adap')
      ->from('Roloffice\Entity\AccountingDocumentArticleProperty', 'adap')
      ->join('adap.property', 'p', 'WITH', 'adap.property = p.id')
      ->where(
        $qb->expr()->eq('adap.accounting_document_article', $accounting_document__article)
      );
      $query = $qb->getQuery();
      $result = $query->getResult();
      return $result;
  }

}
