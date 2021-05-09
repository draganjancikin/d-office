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

/*

    $qb = $this->_em->createQueryBuilder();
    $qb->select('m')
      ->from('Roloffice\Entity\Material', 'm')
    
    
      ->join('m.street', 's', 'WITH', 'm.street = s.id')
      ->join('m.city', 'c', 'WITH', 'm.city = c.id')
      
      ->where(
        $qb->expr()->like('m.name', $qb->expr()->literal("%$term%")),
        )
      ->orderBy('m.name', 'ASC');








$result = $this->get("SELECT article.id, article.name, unit.name as unit_name, article.price "
                        . "FROM $this->table_article "
                        . "JOIN (unit) "
                        . "ON (article.unit_id = unit.id) "
                        . "WHERE (article.group_id = $group_id )"
                        . "ORDER BY article.name ");
        return $result;
*/


  }
}
