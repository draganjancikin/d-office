<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class CuttingSheetRepository extends EntityRepository {

  /**
   * Method that return number of AccountingDocuments
   *
   * @return int
   */
  public function getNumberOfCuttingSheets() {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('count(cs.id)')
        ->from('Roloffice\Entity\CuttingSheet','cs');
    $count = $qb->getQuery()->getSingleScalarResult();
    return $count;
  }

  /**
   * Method that return last $limit Articles
   * 
   * @return 
   */
  public function getLastCuttingSheets($limit = 5) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('cs')
        ->from('Roloffice\Entity\CuttingSheet', 'cs')
        ->orderBy('cs.id', 'DESC')
        ->setMaxResults( $limit );
    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

  /**
   * Method that return all Articles on CuttingSheet
   * 
   * @param int $cutting_sheet_id
   * 
   * @return array
   */
  public function getArticlesOnCuttingSheet($cutting_sheet_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    $qb->select('csa')
        ->from('Roloffice\Entity\CuttingSheetArticle', 'csa')
        ->join('csa.fence_model', 'fm', 'csa.fence_model = fm.id')
        ->where(
          $qb->expr()->eq('csa.cutting_sheet', $cutting_sheet_id),
        )
        ->orderBy('csa.id', 'ASC');
    $query = $qb->getQuery();
    $result = $query->getResult();
    
    return $result;
  }

  /**
   * 
   */
  /*
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
  */

  /**
   * Method that return Article Properties
   * 
   * @param int $article_id
   * 
   * @return array
   */
  /*
  public function getArticleProperties($article_id) {
    // Create a QueryBilder instance
    $qb = $this->_em->createQueryBuilder();
    
    $qb->select('ap, pr')
      ->from('Roloffice\Entity\ArticleProperty', 'ap')
      ->join('ap.property', 'pr', 'WITH', 'ap.property = pr.id')
      ->where(
        $qb->expr()->eq('ap.article', $article_id),
      )
      ->distinct();
        
      
      
        $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  } 
  */

}
