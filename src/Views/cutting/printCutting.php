<?php
$page = "cutting";

// Include the main TCPDF library.
require_once '../config/packages/tcpdf_include.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Rolostil');
$pdf->SetTitle('ROLOSTIL - Krojna lista');
$pdf->SetSubject('Rolostil');
$pdf->SetKeywords('Rolostil, PDF, Krojna lista');

// Remove default header/footer.
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default monospaced font.
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins.
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// Set auto page breaks.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor.
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set some language-dependent strings.
// $pdf->setLanguageArray($l);

// ---------------------------------------------------------

// Set font.
$pdf->SetFont('dejavusans', '', 10);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table.

// Add a page.
$pdf->AddPage();

$id = $cutting_id;
$cutting_sheet = $entityManager->find("\App\Entity\CuttingSheet", $id);

$html =
'<style type="text/css">table { padding-top: 0px; padding-bottom: 0px; }</style>
    <table border="0">
        <tr>
            <td>
                <h1>KROJNA LISTA: KL ' . str_pad($cutting_sheet->getOrdinalNumInYear(), 3, "0", STR_PAD_LEFT) . ' - '
                    . $cutting_sheet->getCreatedAt()->format('m') . '</h1>
            </td>
            <td>' . $cutting_sheet->getCreatedAt()->format('d M Y') . '</td>
        </tr>
        <tr>
            <td>U vezi sa: '/*.( $cutting_sheet['task_t_id']=="" ? "" : 'N ' .str_pad($cutting_sheet['task_t_id'], 4, "0", STR_PAD_LEFT) ). ' - ' */
                . $cutting_sheet->getClient()->getName() . '</td>
        </tr>
    </table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pickets_number = 0;

$articles = $entityManager->getRepository('\App\Entity\CuttingSheet')->getArticlesOnCuttingSheet($id);

// Ovde pocinje petlja iscitavanja artikala iz krojnie liste.
foreach ($articles as $article):
    
  $count = 0;
  $article_repo = $entityManager->getRepository('\App\Entity\CuttingSheetArticle');
  
  $fence_model_id = $article->getFenceModel()->getId();
  
  $article_width = $article->getWidth();
  $article_height = $article->getHeight();
  $article_mid_height = $article->getMidHeight();
  
  $picket_width = $article->getPicketWidth();
  $article_space = $article->getSpace();
  $article_field_number = $article->getNumberOfFields();

  // Izracunavanje broja letvica u zavisnosti od sirine polja.
  $pickets_number = $article_repo->getPicketsNumber($article->getId());

  // Real space between pickets.
  $space_between_pickets = $article_repo->getSpaceBetweenPickets($article_width, $pickets_number, $picket_width);
  
  // Legs of triangle for angle calculation.
  $heigth_leg = $article_repo->getDiffMinMax($article_height, $article_mid_height);
  $width_leg = $article_repo->getWidthForAngleCalc(
    $article_repo->isEven($pickets_number), 
    $article_width, 
    $space_between_pickets, 
    $picket_width
  );

  // Classic =================================================================
  if($fence_model_id == 1){
      
      $count++;
      
      $html = '<hr />
              <table>
                <tr>
                  <td><h3>model: CLASSIC (letvica '.$picket_width.'mm)</h3></td>
                  <td colspan="3">
                    <h3>broj polja: '.$article_field_number.'</h3>
                  </td>
                </tr>
                <tr><td>širina polja: <br />'.$article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td colspan="2">razmak među letvicama: <br />'.number_format($space_between_pickets, 1, ",", ".").'mm </td></tr>
                </table>
                <hr />
                <table>
                  <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
                  <tr><td></td></tr>
                  <tr><td>'.$count .'</td><td>'.number_format($article_height, 0, ",", ".").' mm</td><td>'.$pickets_number.' kom</td></tr>
                </table>';
      
      $pdf->writeHTML($html, true, false, true, false, '');
  }
  
  // Alpina ==================================================================
  if ($fence_model_id == 2){
      
    $alpha_angle = rad2deg(atan($heigth_leg / $width_leg));
      
    $html = '<hr />
            <table>
              <tr>
                <td><h3>model: ALPINA  (letvica '.$picket_width.'mm)</h3></td>
                <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
              </tr>
              <tr><td>širina polja: <br />'.$article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak među letvicama: <br />'.number_format($space_between_pickets, 1, ",", ".").'mm </td></tr>
            </table>
            <hr />
            <table>
              <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
            </table>';
      
    $pdf->writeHTML($html, true, false, true, false, '');
      
    for ( $i=1; $i <= ceil($pickets_number/2); $i++ ) {
      $count++;
      $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1);
      $picket_height_over_post = tan(deg2rad($alpha_angle)) * $picket_x_position;
      $picket_height = $article_height + $picket_height_over_post;

      if ( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ) {
        $pieces = 1;
      }
      else {
        $pieces = 2;
      }
      $html = '<table>
                  <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($picket_height, 0, ",", ".").' mm</td><td>'.$pieces.' kom</td></tr>
                </table>';
      $pdf->writeHTML($html, true, false, true, false, '');
    }
      
  }
  
  // Arizona ===================================================================
  if($fence_model_id == 3){
    
    // Tendon of circle.
    $tendon = $article_repo->getTendon($article_width, $space_between_pickets, $heigth_leg);
    
    $alpha_angle = rad2deg(atan( ($heigth_leg * 2) / ($article_width - $space_between_pickets * 2)));
    $beta_angle = 90 - $alpha_angle;
    $radius = $tendon / (2*cos(deg2rad($beta_angle)));
      
    $html = '<hr />
            <table>
              <tr>
                <td><h3>model: ARIZONA (letvica '.$picket_width.'mm)</h3></td>
                <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
              </tr>
              <tr><td>širina polja: <br />'.$article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak među letvicama: <br />'.number_format($space_between_pickets, 1, ",", ".").'mm </td></tr>
            </table>
            <hr />
            <table>
              <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
            </table>';
    $pdf->writeHTML($html, true, false, true, false, '');
      
    for ( $i = 1; $i <= ceil($pickets_number/2); $i++ ) {
      $count++;
      $corective_factor = 0;
      if ($i > 1 ) {
        $corective_factor = ($space_between_pickets / 2) / ceil($pickets_number/2);
      }
      if ($i > 1 && $article_repo->isEven($pickets_number)) {
        $corective_factor = ($picket_width + $space_between_pickets / 2) / ceil($pickets_number/2);
      }
      $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1) + $corective_factor * $i;
      $y = sqrt( $radius ** 2 - ((($article_width - $space_between_pickets * 2) / 2 - $picket_x_position) ** 2 ) );
      $picket_height_over_post = $y - ($radius - $heigth_leg);
      $picket_height = $article_height + $picket_height_over_post;
        if ( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ) {
          $pieces = 1;
        }
        else {
          $pieces = 2;
        }
        $html = '<table>
                  <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($picket_height, 0, ",", ".").' mm</td><td>'.$pieces.' kom</td></tr>
                </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
      }
      $html = '	';
      $pdf->writeHTML($html, true, false, true, false, '');
  }
  
  // Pacific ===================================================================
  if ($fence_model_id == 4) {
    $html = '<hr />
            <table>
              <tr>
                <td><h3>model: PACIFIC (letvica '.$picket_width.'mm)</h3></td>
                <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
              </tr>
              <tr><td colspan="4"><hr /></td></tr>
              <tr><td>širina polja: <br />'.$article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak među letvicama: <br />'.number_format($space_between_pickets, 1, ",", ".").'mm </td></tr>
            </table>
            <hr />';
    $pdf->writeHTML($html, true, false, true, false, '');
      
      $tendon = $article_repo->getTendon($article_width, $space_between_pickets, $heigth_leg);

      $alpha_angle = rad2deg(atan(($heigth_leg * 2)/($article_width-$space_between_pickets * 2)));
      $beta_angle = 90 - $alpha_angle;
      $radius = $tendon / (2*cos(deg2rad($beta_angle)));

      $html = '<table>
                  <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
                </table>';
      $pdf->writeHTML($html, true, false, true, false, '');
      
      for ( $i=1; $i<=ceil($pickets_number/2); $i++ ) {
        $count++;
        $corective_factor = 0;
        if ($i > 1 ) {
          $corective_factor = ($space_between_pickets / 2) / ceil($pickets_number/2);
        }
        if ($i > 1 && $article_repo->isEven($pickets_number)) {
          $corective_factor = ($picket_width + $space_between_pickets / 2) / ceil($pickets_number/2);
        }
        $picket_x_position = $picket_width*($i-1) + $space_between_pickets*($i-1) + $corective_factor * $i;;
        $y = sqrt( $radius ** 2 - ((($article_width - $space_between_pickets * 2) / 2 - $picket_x_position) ** 2 ) );
        $picket_height_over_post = $y - ($radius - $heigth_leg);
        $picket_height = $article_height - $picket_height_over_post;

        if ( $i==ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2))>0 ) {
          $pieces = 1;
        }
        else {
          $pieces = 2;
        }
        $html = '<table>
                  <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($picket_height, 0, ",", ".").' mm</td><td>'.$pieces.' kom</td></tr>
                </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
      }
      $html = '';
      $pdf->writeHTML($html, true, false, true, false, '');
  }
  
  // Panonka  ================================================================
  if ($fence_model_id == 5) {
    $html = '<hr />
            <table>
              <tr>
                <td><h3>model: PANONKA (letvica '.$picket_width.'mm)</h3></td>
                <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
              </tr>
              <tr><td colspan="4"><hr /></td></tr>
              <tr><td>sirina polja: <br />'.$article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak medju letvicama: <br />'.number_format($space_between_pickets, 1, ",", ".").'mm </td></tr>
            </table>
            <hr />';
    $pdf->writeHTML($html, true, false, true, false, '');
      
    $omega = 360 / $article_width;	//ugaona brzina
    $teta = 90;										// fazno pomeranje za 90stepeni
      
    $html = '<table>
              <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
            </table>';
    $pdf->writeHTML($html, true, false, true, false, '');
      
    for ( $i = 1; $i <= ceil($pickets_number/2); $i++ ) {
      $count++;
      $picket_x_position = $space_between_pickets + $picket_width*($i-1) + $space_between_pickets*($i-1);
      
      $y = sin(deg2rad($omega*$picket_x_position - $teta));
          
      $picket_height = $article_height + ($heigth_leg / 2) + ($y * $heigth_leg )/2;
          
      if ( $i == ceil($pickets_number/2) AND (ceil($pickets_number/2)-($pickets_number/2)) > 0 ){
        $pieces = 1;
      }
      else {
        $pieces = 2;
      }
      $html = '<table>
                <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($picket_height, 0, ",", ".").' mm</td><td>'.$pieces.' kom</td></tr>
              </table>';
      $pdf->writeHTML($html, true, false, true, false, '');
    }
    $html = '';
    $pdf->writeHTML($html, true, false, true, false, '');
  }
        
endforeach;
// kraj petlje za iscitavanje artikala iz tabele cutting_article.

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('rolostil_krojna_lista.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
