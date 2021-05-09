<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository {

  /**
   * Method that return number of AccountingDocuments
   *
   * @return int
   */
  public function getNumberOfArticles() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(a.id)')
        ->from('Roloffice\Entity\Article','a');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }
  
}
