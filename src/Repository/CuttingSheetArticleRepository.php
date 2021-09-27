<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class CuttingSheetArticleRepository extends EntityRepository {

  /**
   * Method that return sum heights of all pickets in one article (field)
   *  
   * @param $article_id
   * 
   * @return float
   */
  public function getPicketsLength($article_id) {
    
    // Get fence_model.
    $article = $this->_em->find('\Roloffice\Entity\CuttingSheetArticle', $article_id);
    $fence_model_id = $article->getFenceModel()->getId();
    
    // Get Article (fence field) width, height and middle height.
    $article_width = $article->getWidth();
    $article_height = $article->getHeight();
    $article_mid_height = $article->getMidHeight();

    // Get pickets number.
    $pickets_number = $this->getPicketsNumber($article_id);

    // Get picket width.
    $picket_width = $article->getPicketWidth();
        
    // Real space between pickets.
    $space_between_pickets = $this->getSpaceBetweenPickets($article_width, $pickets_number, $picket_width);
    
    // The difference between the highest and the lowest picket.
    $min_max_l = $this->getDiffMinMax($article_height, $article_mid_height);
    $pickets_length = 0;

    // Legs of triangle for angle calculation.
    $heigth_leg = $this->getDiffMinMax($article_height, $article_mid_height);
    $width_leg = $this->getWidthForAngleCalc(
      $this->isEven($pickets_number), 
      $article_width, 
      $space_between_pickets, 
      $picket_width
    );
    
    switch ($fence_model_id) {

      // Classic ===============================================================
      case '1':

        // Loop through all pickets.
        for ( $i = 1; $i <= ceil($pickets_number/2); $i++ ){
          $picket_height = $article_height;
          if( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ){
            $pickets_length = $pickets_length + $picket_height;
          } 
          else {
            $pickets_length = $pickets_length + $picket_height * 2;
          }
        }
    
        break;

      // Alpina ================================================================
      case '2':
          
        $alpha_angle = rad2deg(atan($heigth_leg / $width_leg));
        
        // Loop through all pickets.
        for ( $i=1; $i <= ceil($pickets_number/2); $i++ ) {

          $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1);
          $picket_height_over_post = tan(deg2rad($alpha_angle)) * $picket_x_position;
          $picket_height = $article_height + $picket_height_over_post;
        
          if ( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ) {
            $pickets_length = $pickets_length + $picket_height;
          }
          else {
            $pickets_length = $pickets_length + ($picket_height * 2);
          }

        }

        break;


      // Arizona ===============================================================
      case '3':

        // Tetiva kružnice.
        $tendon = $this->getTendon($article_width, $space_between_pickets, $heigth_leg);
        
        $alpha_angle = rad2deg(atan( ($heigth_leg * 2) / ($article_width - $space_between_pickets * 2)));
        $beta_angle = 90 - $alpha_angle;
        $radius = $tendon / (2*cos(deg2rad($beta_angle)));;
        
        for ( $i = 1; $i <= ceil($pickets_number/2); $i++ ) {
          $corective_factor = 0;
          if ($i > 1 ) {
            $corective_factor = ($space_between_pickets / 2) / ceil($pickets_number/2);
          }
          if ($i > 1 && $this->isEven($pickets_number)) {
            $corective_factor = ($picket_width + $space_between_pickets / 2) / ceil($pickets_number/2);
          }
          $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1) + $corective_factor * $i;
          $y = sqrt( $radius ** 2 - ((($article_width - $space_between_pickets * 2) / 2 - $picket_x_position) ** 2 ) );
          $picket_height_over_post = $y - ($radius - $heigth_leg);
          $picket_height = $article_height + $picket_height_over_post;
          
          if ( $i==ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2))>0 ) {
            $pickets_length = $pickets_length + $picket_height;
          }
          else {
            $pickets_length = $pickets_length + $picket_height * 2;
          }
        }
        break;

      // Pacific ===============================================================
      case '4':
      
        $tendon = $this->getTendon($article_width, $space_between_pickets, $heigth_leg);

        $ugao_alfa = rad2deg(atan(($heigth_leg * 2)/($article_width-$space_between_pickets * 2)));
        $ugao_beta = 90 - $ugao_alfa;
        $radius = $tendon / (2*cos(deg2rad($ugao_beta)));
        for ( $i=1; $i<=ceil($pickets_number/2); $i++ ) {
          $corective_factor = 0;
          if ($i > 1 ) {
            $corective_factor = ($space_between_pickets / 2) / ceil($pickets_number/2);
          }
          if ($i > 1 && $this->isEven($pickets_number)) {
            $corective_factor = ($picket_width + $space_between_pickets / 2) / ceil($pickets_number/2);
          }
          $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1) + $corective_factor * $i;
          $y = sqrt( $radius ** 2 - ((($article_width - $space_between_pickets * 2) / 2 - $picket_x_position) ** 2 ) );
          $picket_height_over_post = $y - ($radius - $heigth_leg);
          $picket_height = $article_height - $picket_height_over_post;
          if ( $i==ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2))>0 ){
            $pickets_length = $pickets_length + $picket_height;
          }
          else {
            $pickets_length = $pickets_length + $picket_height*2;
          }
        }
        break;

      // Panonka ===============================================================
      case '5':

        $omega = 360 / $article_width;	//ugaona brzina
        $teta = 90;										// fazno pomeranje za 90stepeni

        for ( $i=1; $i <= ceil($pickets_number/2); $i++ ) {
          $picket_x_position = $space_between_pickets + $picket_width*($i-1) + $space_between_pickets*($i-1);
          $y = sin(deg2rad($omega*$picket_x_position - $teta));
          $picket_height = $article_height + ($heigth_leg / 2) + ($y * $heigth_leg )/2;

          if ( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ){
            $pickets_length = $pickets_length + $picket_height;
          }
          else {
            $pickets_length = $pickets_length + $picket_height*2;
          }
        }
        
        break;
  
  
      default:
        
        break;
    }

    return $pickets_length;
  }

  /**
   * Method that return number of kaps in one CuttingSheetArticle (fence field).
   * 
   * @param $article_id
   * 
   * @return int
   */
  public function getPicketsNumber($article_id) {
    
    // Get CuttingSheetArticle (fence field) by $article_id.
    $article = $this->_em->find('\Roloffice\Entity\CuttingSheetArticle', $article_id);

    // Picket width.
    $picket_width = $article->getPicketWidth();
    
    // Article (field) width.
    $width = $article->getWidth();
    
    // Space between picket.
    $space = $article->getSpace();

    $control_pickets_number = ($width - $space) / ($picket_width + $space);
    $rounded_cap_number = ceil($control_pickets_number);
    $razlika = $control_pickets_number-($rounded_cap_number-1);

    if($razlika < 0.5){
      $pickets_number = ceil($control_pickets_number) - 1;
    }

    if($razlika >= 0.5){
      $pickets_number = ceil($control_pickets_number);
    }

    return $pickets_number;
  }

  /**
   * Method that return cpace between pickets.
   * 
   * @param int $article_width
   * @param int $pickets_number
   * @param int $picket_width
   * 
   * @return int
   */
  public function getSpaceBetweenPickets($article_width, $pickets_number, $picket_width) {
    return ($article_width - $pickets_number * $picket_width) / ($pickets_number + 1);
  }

  /**
   * Method that return difference between Article (fence field) height and middle height.
   * 
   * @param int $article_height
   * @param int $article_mid_height
   * 
   * @return int
   */
  public function getDiffMinMax($article_height, $article_mid_height) {
    
    if ($article_mid_height > $article_height) {
      $diffMinMax = $article_mid_height - $article_height;
    }
    else if ($article_mid_height < $article_height) {
      $diffMinMax = $article_height - $article_mid_height;
    }
    else {
      return false;
    }

    return $diffMinMax;
  }

  /**
   * Method that return true if argument even.
   * 
   * @param int $pickets_number
   * 
   * @return bool
   */
  public function isEven($pickets_number) {
    return $pickets_number % 2 ? false : true;
  }

  /**
   * Method that return width for angle calculation.
   * 
   * @param bool $is_even
   * @param int $article_width
   * @param int $space_between_pickets
   * @param int $picket_width
   * 
   * @return int
   */
  public function getWidthForAngleCalc(
    $is_even, 
    $article_width, 
    $space_between_pickets, 
    $picket_width
  ) {
    $width = ($article_width / 2) - $space_between_pickets;
    if ($is_even) {
      $width = $width - ($space_between_pickets / 2) - $picket_width;
    }
    else {
      $width = $width - ($picket_width / 2);
    }
    return $width;
  }

  /**
   * Method that return length of tendon.
   * 
   * @param int $article_width
   * @param int $space_between_pickets
   * @param int $min_max_l
   * 
   * @return int
   */
  public function getTendon($article_width, $space_between_pickets, $min_max_l) {
    $width = ($article_width - $space_between_pickets * 2) / 2;
    return SQRT($width ** 2 + $min_max_l ** 2);
  }

  /**
   * Method that return Artiles in CuttingSheet.
   * 
   * @param obj $cs CuttingSheet
   * @return 
   */
  public function getCuttingSheetArticles($cs) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('csa')
      ->from('Roloffice\Entity\CuttingSheetArticle', 'csa')
      ->where(
        $qb->expr()->eq('csa.cutting_sheet', $cs)
      );
      $query = $qb->getQuery();
      $result = $query->getResult();
      return $result;
  }
}
