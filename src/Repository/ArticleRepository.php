<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository {
  // TODO Dragan: Remove unnecessary code.
  // /**
  //  * Method that return number of AccountingDocuments
  //  *
  //  * @return int
  //  */
  // public function getNumberOfArticles() {
  //   $qb = $this->_em->createQueryBuilder();
  //   $qb->select('count(a.id)')
  //       ->from('Roloffice\Entity\Article','a');
  //   $count = $qb->getQuery()->getSingleScalarResult();
  //   return $count;
  // }

  /**
   * Method that return all Articles sort by name ASC
   * 
   * @return array
   */
  public function findAll() {
    return $this->findBy(array(), array('name' => 'ASC'));
  }
  
  /**
   * Method that return last $limit Articles
   * 
   * @param int $limit
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
   * Method that return all Article in ArticleGroup by $group_id.
   * 
   * @param int $group_id
   * 
   * @return array
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

  // TODO Dragan: Remove unnecessary code.
  /**
   * Method that return Article Properties
   * 
   * @param int $article_id
   * 
   * @return array
   */
  // public function getArticleProperties($article_id) {
  //   // Create a QueryBilder instance
  //   $qb = $this->_em->createQueryBuilder();
  //   /*
  //   $qb->select('ap, pr')
  //     ->from('Roloffice\Entity\ArticleProperty', 'ap')
  //     ->join('ap.property', 'pr', 'WITH', 'ap.property = pr.id')
  //     ->where(
  //       $qb->expr()->eq('ap.article', $article_id),
  //     )
  //     ->distinct();
  //     */
        
      
  //       $query = $qb->getQuery();
  //   $result = $query->getResult();
  //   return $result;
  // } 

  /**
   * Method that return all Article where name $term.
   * 
   * @param string $term
   * 
   * @return array
   */
  public function search($term) {

    $qb = $this->_em->createQueryBuilder();
    $qb->select('a')
      ->from('Roloffice\Entity\Article', 'a')
      ->where(
        $qb->expr()->like('a.name', $qb->expr()->literal("%$term%")),
      )
      ->orderBy('a.name', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

}
