<?php
$page = "cutting";

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/bootstrap.php';

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Rolostil');
$pdf->SetTitle('ROLOSTIL - Krojna lista');
$pdf->SetSubject('Rolostil');
$pdf->SetKeywords('Rolostil, PDF, Krojna lista');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
// $pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table

// add a page
$pdf->AddPage();

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';

$cutting_sheet_id = $_GET['cutting_id'];
$cutting_data = $order_data = $entityManager->find("\Roloffice\Entity\CuttingSheet", $cutting_sheet_id);


$html = '<style type="text/css">table { padding-top: 0px; padding-bottom: 0px; }</style>
         <table border="0">
           <tr><td><h1>KROJNA LISTA: KL '.str_pad($cutting_data->getOrdinalNumInYear(), 3, "0", STR_PAD_LEFT).' - '.$cutting_data->getCreatedAt()->format('m').' </h1></td><td>'.$cutting_data->getCreatedAt()->format('d M Y').'</td></tr>
           <tr><td>U vezi sa: '/*.( $cutting_data['task_t_id']=="" ? "" : 'N ' .str_pad($cutting_data['task_t_id'], 4, "0", STR_PAD_LEFT) ). ' - ' */.$cutting_data->getClient()->getName(). '</td></tr>
         </table>';

$pdf->writeHTML($html, true, false, true, false, '');

$picket_number = 0;
$picket_lenght = 0;
$total_picket_lenght = 0;
$kap = 0;
$total_kap = 0;

$cutting_sheet_articles = $entityManager->getRepository('\Roloffice\Entity\CuttingSheet')->getArticlesOnCuttingSheet($cutting_sheet_id);

// Ovde pocinje petlja iscitavanja artikala iz krojnie liste.
foreach ($cutting_sheet_articles as $cutting_sheet_article):
    
    $count = 0;
    
    $cutting_fence_model_id = $cutting_sheet_article->getFenceModel()->getId();
    $cutting_sheet__article_width = $cutting_sheet_article->getWidth();
    $article_picket_width = $cutting_sheet_article->getPicketWidth();
    $article_height = $cutting_sheet_article->getHeight();
    $article_mid_height = $cutting_sheet_article->getMidHeight();
    $article_space = $cutting_sheet_article->getSpace();
    $article_field_number = $cutting_sheet_article->getNumberOfFields();

    // Izracunavanje broja letvica u zavisnosti od sirine polja.
    $number_of_pickets = $entityManager->getRepository('\Roloffice\Entity\CuttingSheetArticle')->getCuttingSheetArticlePicketNumber($cutting_sheet_article->getId());

    // Variable reset.
    $duzina_letvica_polja = 0;
    
    // Real space between pickets.
    $real_space_between_pickets = ($cutting_sheet__article_width - $number_of_pickets*$article_picket_width)/($number_of_pickets+1);

    // Classic =================================================================
    if($cutting_fence_model_id==1){
        
        $count++;
        
        $html = '<hr />
                <table>
                  <tr>
                    <td><h3>model: CLASSIC (letvica '.$article_picket_width.'mm)</h3></td>
                    <td colspan="3">
                      <h3>broj polja: '.$article_field_number.'</h3>
                    </td>
                  </tr>
                  <tr><td>širina polja: <br />'.$cutting_sheet__article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td colspan="2">razmak među letvicama: <br />'.number_format($real_space_between_pickets, 1, ",", ".").'mm </td></tr>
                 </table>
                 <hr />
                 <table>
                   <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
                   <tr><td></td></tr>
                   <tr><td>'.$count .'</td><td>'.number_format($article_height, 0, ",", ".").' mm</td><td>'.$number_of_pickets.' kom</td></tr>
                 </table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
    }
    
    // Alpina ==================================================================
    if($cutting_fence_model_id==2){
        
        $min_max_l = $article_mid_height - $article_height;
        
        $ugao_alfa = rad2deg(atan($min_max_l/(($cutting_sheet__article_width-2*$real_space_between_pickets)/2) ));
        
        $html = '<hr />
                <table>
                  <tr>
                    <td><h3>model: ALPINA  (letvica '.$article_picket_width.'mm)</h3></td>
                    <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
                  </tr>
                  <tr><td>širina polja: <br />'.$cutting_sheet__article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak među letvicama: <br />'.number_format($real_space_between_pickets, 1, ",", ".").'mm </td></tr>
                </table>
                <hr />
                <table>
		              <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
		            </table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
	for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
	    $count++;
            $ras_l = $article_picket_width*($i-1) + $real_space_between_pickets*($i-1);
            $vis_raz_l = tan(deg2rad($ugao_alfa))*$ras_l;
            $vis_l = $article_height + $vis_raz_l;
            
	    if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
	        $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>1 kom</td></tr>
                         </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }else{
                $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>2 kom</td></tr>
                         </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }
        }
        
    }
    
    // Arizona =================================================================
    if($cutting_fence_model_id==3){
        
        $min_max_l = $article_mid_height - $article_height;
        $tetiva = SQRT((($cutting_sheet__article_width-2*$real_space_between_pickets)/2)*(($cutting_sheet__article_width-2*$real_space_between_pickets)/2) + $min_max_l*$min_max_l);
        $ugao_alfa = rad2deg(atan((2*$min_max_l)/($cutting_sheet__article_width-2*$real_space_between_pickets)));
        $ugao_beta = 90 - $ugao_alfa;
        $r = $tetiva / (2*cos(deg2rad($ugao_beta)));
        
        $html = '<hr />
                <table>
                  <tr>
                    <td><h3>model: ARIZONA (letvica '.$article_picket_width.'mm)</h3></td>
                    <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
                  </tr>
                  <tr><td>širina polja: <br />'.$cutting_sheet__article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak među letvicama: <br />'.number_format($real_space_between_pickets, 1, ",", ".").'mm </td></tr>
                </table>
                <hr />
                <table>
                  <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
                </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        
	for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
            $count++;
            $ras_l = $real_space_between_pickets*($i-1) + $real_space_between_pickets*($i-1);
            $y = sqrt( $r*$r - ((($cutting_sheet__article_width-2*$real_space_between_pickets)/2 - $ras_l)*(($cutting_sheet__article_width-2*$real_space_between_pickets)/2 - $ras_l)) );
            $vis_raz_l = $y - ($r - $min_max_l);
            $vis_l = $article_height + $vis_raz_l;
            
            if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
                $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>1 kom</td></tr>
                         </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }else{
                $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>2 kom</td></tr>
                         </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }
        }
        $html = '	';
        $pdf->writeHTML($html, true, false, true, false, '');
        
    }
    
    // Pacific =================================================================
    if($cutting_fence_model_id==4){
        $html = '<hr />
		            <table>
                  <tr>
                    <td><h3>model: PACIFIC (letvica '.$article_picket_width.'mm)</h3></td>
                    <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
                  </tr>
                  <tr><td colspan="4"><hr /></td></tr>
                  <tr><td>širina polja: <br />'.$cutting_sheet__article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak među letvicama: <br />'.number_format($real_space_between_pickets, 1, ",", ".").'mm </td></tr>
		            </table>
                <hr />';
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // $raz_l = ($cutting_sheet__article_width - $number_of_pickets*$real_space_between_pickets)/($number_of_pickets+1);
        $min_max_l = $article_height - $article_mid_height;
        $tetiva = SQRT((($cutting_sheet__article_width-2*$real_space_between_pickets)/2)*(($cutting_sheet__article_width-2*$real_space_between_pickets)/2) + $min_max_l*$min_max_l);
        $ugao_alfa = rad2deg(atan((2*$min_max_l)/($cutting_sheet__article_width-2*$real_space_between_pickets)));
        $ugao_beta = 90 - $ugao_alfa;
        $r = $tetiva / (2*cos(deg2rad($ugao_beta)));

        $html = '<table>
                   <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
                 </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        
        for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
            $count++;
            $ras_l = $real_space_between_pickets*($i-1) + $real_space_between_pickets*($i-1);
            $y = sqrt( $r*$r - ((($cutting_sheet__article_width-2*$real_space_between_pickets)/2 - $ras_l)*(($cutting_sheet__article_width-2*$real_space_between_pickets)/2 - $ras_l)) );
            $vis_raz_l = $y - ($r - $min_max_l);
            $vis_l = $article_height - $vis_raz_l;
            
            if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
                $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>1 kom</td></tr>
                         </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }else{
                $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>2 kom</td></tr>
                         </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }
        }
        $html = '';
        $pdf->writeHTML($html, true, false, true, false, '');
    }
    
    // Panonka  ================================================================
    if($cutting_fence_model_id==5){
        $html = '<hr />
                <table>
                  <tr>
                    <td><h3>model: PANONKA (letvica '.$article_picket_width.'mm)</h3></td>
                    <td colspan="3"><h3>broj polja: '.$article_field_number.'</h3></td>
                  </tr>
                  <tr><td colspan="4"><hr /></td></tr>
                  <tr><td>sirina polja: <br />'.$cutting_sheet__article_width.'mm</td><td>visina polja: <br />'.$article_height.'mm</td><td>visina sredine polja: <br />'.$article_mid_height.'mm</td><td>razmak medju letvicama: <br />'.number_format($real_space_between_pickets, 1, ",", ".").'mm </td></tr>
                </table>
                <hr />';
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $raz_l = ($cutting_sheet__article_width - $number_of_pickets*$article_picket_width)/($number_of_pickets+1);
        $min_max_l = $article_mid_height - $article_height;
        
        $omega = 360 / $cutting_sheet__article_width;	//ugaona brzina
        $teta = 90;										// fazno pomeranje za 90stepeni
        
        $html = '<table>
                   <tr><th width="60px">red. br.</th><th width="100px">dužina letvice</th><th>količina</th></tr>
                 </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        
        for( $i=1; $i<=ceil($number_of_pickets/2); $i++ ){
            $count++;
            $ras_l = $raz_l + $article_picket_width*($i-1) + $raz_l*($i-1);
            
            $y = sin(deg2rad($omega*$ras_l - $teta));
            
            $vis_l = $article_height + ($min_max_l/2) + ($y*$min_max_l )/2;
            
            if( $i==ceil($number_of_pickets/2) AND (ceil($number_of_pickets/2)-($number_of_pickets/2))>0 ){
                $duzina_letvica_polja = $duzina_letvica_polja + $vis_l;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>1 kom</td></tr>
			                  </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }else{
                $duzina_letvica_polja = $duzina_letvica_polja + $vis_l*2;
                $html = '<table>
                           <tr><td width="60px">'.$count.'</td><td width="100px">'.number_format($vis_l, 0, ",", ".").' mm</td><td>2 kom</td></tr>
                         </table>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }
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
