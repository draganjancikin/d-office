<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class CuttingSheetArticleRepository extends EntityRepository {

  /**
   * Method that return sum all picket in one field
   *  
   * @param $cutting_sheet_article_id
   * 
   * @return float
   */
  public function getArticlePicketLength($cutting_sheet_article_id) {
    // TODO:
    
    return 0;
  }

  /**
   * Method that return number of kaps in one field
   * 
   * @param $cutting_sheet_article_id
   * 
   * @return int
   */
  public function getArticleKapNumber($cutting_sheet_article_id) {
    //TODO:

    return 0;
  }

}
