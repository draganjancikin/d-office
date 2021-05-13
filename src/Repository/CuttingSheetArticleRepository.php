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
   * Method that return number of kaps in one CuttingSheetArticle (fence field).
   * 
   * @param $cutting_sheet_article_id
   * 
   * @return int
   */
  public function getCuttingSheetArticleCapNumber($cutting_sheet_article_id) {
    
    // Get CuttingSheetArticle by $cutting_sheet_article_id.
    $cutting_sheet_article = $this->_em->find('\Roloffice\Entity\CuttingSheetArticle', $cutting_sheet_article_id);

    // Field width.
    $width = $cutting_sheet_article->getWidth();
    
    // Space between picket.
    $space = $cutting_sheet_article->getSpace();
    
    // Picket width.
    // TODO: ubaciti na krojnu listu odrešivanje i širine letvice
    $picket_width = 80;

    $control_cap_number = ($width - $space) / ($picket_width + $space);

    $rounded_cap_number = ceil(($width - $space) / ($picket_width + $space));

    $razlika = $control_cap_number-($rounded_cap_number-1);

    if($razlika < 0.5){
      $cap_number = ceil(($width - $space) / ($picket_width + $space))-1;
    }

    if($razlika >= 0.5){
      $cap_number = ceil(($width - $space) / ($picket_width + $space));
    }

    return $cap_number;
  }

}
