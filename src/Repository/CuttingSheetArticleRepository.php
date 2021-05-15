<?php

namespace Roloffice\Repository;

use Doctrine\ORM\EntityRepository;

class CuttingSheetArticleRepository extends EntityRepository {

  /**
   * Method that return sum heights of all pickets in one field
   *  
   * @param $cutting_sheet__article_id
   * 
   * @return float
   */
  public function getArticlePicketLength($cutting_sheet__article_id) {
    // TODO:

    
    // Sum heights of all pickets in one field
    $sum_picket_heights = 0;
    
    // Get fence_model
    $cutting_sheet__article = $this->_em->find('\Roloffice\Entity\CuttingSheetArticle', $cutting_sheet__article_id);
    $fence_model_id = $cutting_sheet__article->getFenceModel()->getId();
    
    $cutting_sheet__article_width = $cutting_sheet__article->getWidth();
    $number_of_pickets =$this->getCuttingSheetArticlePicketNumber($cutting_sheet__article_id);
    $picket_width = $cutting_sheet__article->getPicketWidth();
    $cutting_sheet__article_height = $cutting_sheet__article->getHeight();
    $cutting_sheet__article_mid_height = $cutting_sheet__article->getMidHeight();
    $cutting_fence__article__number_of_fields = $cutting_sheet__article->getNumberOfFields();

    $picket_heights_one_field = 0;

    // Real space between pickets.
    $real_space_between_pickets = ($cutting_sheet__article_width - $number_of_pickets*$picket_width)/($number_of_pickets+1);
    $min_max_l = $cutting_sheet__article_mid_height - $cutting_sheet__article_height;

    switch ($fence_model_id) {

      
      // Classic ===============================================================
      case '1':

        // Loop trough all picket.
        for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
          $picket_height = $cutting_sheet__article_height;
          if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
            $picket_heights_one_field = $picket_heights_one_field + $picket_height;
          }else{
            $picket_heights_one_field = $picket_heights_one_field + $picket_height*2;
          }
        }

        break;


      // Alpina ================================================================
      case '2':

        
        $ugao_alfa = rad2deg(atan($min_max_l/($cutting_sheet__article_width/2) ));
        for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
          $ras_l = $real_space_between_pickets + $picket_width*($i-1) + $real_space_between_pickets*($i-1);
          $vis_raz_l = tan(deg2rad($ugao_alfa))*$ras_l;
          $vis_l = $cutting_sheet__article_height + $vis_raz_l;
          if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
            $picket_heights_one_field = $picket_heights_one_field + $vis_l;
          }else{
            $picket_heights_one_field = $picket_heights_one_field + $vis_l*2;
          }

          }
          break;


      // Arizona ===============================================================
      case '3':

        $tetiva = SQRT((($cutting_sheet__article_width-2*$real_space_between_pickets)/2)*(($cutting_sheet__article_width-2*$real_space_between_pickets)/2) + $min_max_l*$min_max_l);
        $ugao_alfa = rad2deg(atan((2*$min_max_l)/($cutting_sheet__article_width-2*$real_space_between_pickets)));
        $ugao_beta = 90 - $ugao_alfa;
        $r = $tetiva / (2*cos(deg2rad($ugao_beta)));
        for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
            $ras_l = $real_space_between_pickets + $picket_width*($i-1) + $real_space_between_pickets*($i-1);
            $y = sqrt( $r*$r - (($cutting_sheet__article_width/2 - $ras_l)*($cutting_sheet__article_width/2 - $ras_l)) );
            $vis_raz_l = $y - ($r - $min_max_l);
            $vis_l = $cutting_sheet__article_height + $vis_raz_l;
            if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
              $picket_heights_one_field = $picket_heights_one_field + $vis_l;
            }else{
              $picket_heights_one_field = $picket_heights_one_field + $vis_l*2;
            }
        }
        break;

      // Pacific ===============================================================
      case '4':
      
        $min_max_l = $cutting_sheet__article_height - $cutting_sheet__article_mid_height;

        $tetiva = SQRT((($cutting_sheet__article_width-2*$real_space_between_pickets)/2)*(($cutting_sheet__article_width-2*$real_space_between_pickets)/2) + $min_max_l*$min_max_l);
        $ugao_alfa = rad2deg(atan((2*$min_max_l)/($cutting_sheet__article_width-2*$real_space_between_pickets)));
        $ugao_beta = 90 - $ugao_alfa;
        $r = $tetiva / (2*cos(deg2rad($ugao_beta)));
        for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
          $ras_l = $real_space_between_pickets + $picket_width*($i-1) + $real_space_between_pickets*($i-1);
          $y = sqrt( $r*$r - (($cutting_sheet__article_width/2 - $ras_l)*($cutting_sheet__article_width/2 - $ras_l)) );
          $vis_raz_l = $y - ($r - $min_max_l);
          $vis_l = $cutting_sheet__article_height - $vis_raz_l;
          if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
            $picket_heights_one_field = $picket_heights_one_field + $vis_l;
          }else{
            $picket_heights_one_field = $picket_heights_one_field + $vis_l*2;
          }
        }
        break;

      // Panonka ===============================================================
      case '5':

        $omega = 360 / $cutting_sheet__article_width;	//ugaona brzina
        $teta = 90;					// fazno pomeranje za 90stepeni

        for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
            $ras_l = $real_space_between_pickets + $picket_width*($i-1) + $real_space_between_pickets*($i-1);
            $y = sin(deg2rad($omega*$ras_l - $teta));
            $vis_l = $cutting_sheet__article_height + ($min_max_l / 2) + ($y*$min_max_l)/2;

            if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
                $picket_heights_one_field = $picket_heights_one_field + $vis_l;
            }else{
                $picket_heights_one_field = $picket_heights_one_field + $vis_l*2;
            }

        }
        break;
  
  
      default:
        
        break;
    }

    return $picket_heights_one_field;
  }

  /**
   * Method that return number of kaps in one CuttingSheetArticle (fence field).
   * 
   * @param $cutting_sheet_article_id
   * 
   * @return int
   */
  public function getCuttingSheetArticlePicketNumber($cutting_sheet__article_id) {
    
    // Get CuttingSheetArticle by $cutting_sheet_article_id.
    $cutting_sheet__article = $this->_em->find('\Roloffice\Entity\CuttingSheetArticle', $cutting_sheet__article_id);

    // Picket width.
    $picket_width = $cutting_sheet__article->getPicketWidth();
    
    // Field width.
    $width = $cutting_sheet__article->getWidth();
    
    // Space between picket.
    $space = $cutting_sheet__article->getSpace();

    // Number of fields of one article
    $cutting_fence__article__number_of_fields = $cutting_sheet__article->getNumberOfFields();
    
    // ($width - $space) because fence field has one space more then pickets
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
