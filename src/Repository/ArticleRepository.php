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

    /**
   * Method that return last $limit Articles
   * 
   * @return 
   */
  public function getLastArticles($limit = 5) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('a')
        ->from('Roloffice\Entity\Article', 'a')
        ->orderBy('a.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }
  
}
