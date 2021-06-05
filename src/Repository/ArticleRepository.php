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
   * @return array
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

  /**
   * 
   */
  public function getArticlesByGroup($group_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('a')
      ->from('Roloffice\Entity\Article', 'a')
      ->where(
        $qb->expr()->eq('a.group', $group_id),
      )
      ->orderBy('a.name', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

  /**
   * Method that return Article Properties
   * 
   * @param int $article_id
   * 
   * @return array
   */
  public function getArticleProperties($article_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    /*
    $qb->select('ap, pr')
      ->from('Roloffice\Entity\ArticleProperty', 'ap')
      ->join('ap.property', 'pr', 'WITH', 'ap.property = pr.id')
      ->where(
        $qb->expr()->eq('ap.article', $article_id),
      )
      ->distinct();
      */
    
      
      
        $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  } 

}
