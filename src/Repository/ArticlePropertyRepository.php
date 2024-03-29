<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class ArticlePropertyRepository extends EntityRepository {

  /**
   * Method that return Article Properties ID in array.
   * 
   * @param int $article_id
   * 
   * @return array
   */
  public function getArticleProperties($article_id) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('ap')
      ->from('Roloffice\Entity\ArticleProperty', 'ap')
      
      ->where(
        $qb->expr()->eq('ap.article', $article_id)
      );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

}