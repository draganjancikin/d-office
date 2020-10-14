<?php
$page = "project";
require_once('../config/lang/srp.php');
require_once('../tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Rolostil');
$pdf->SetTitle('Zapisnik o ugradnji (montaži)');
$pdf->SetSubject('Rolostil');
$pdf->SetKeywords('Rolostil, PDF, zapisnik');

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
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// add a page
$pdf->AddPage();

// potreban je konfiguracioni fajl aplikacije
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../app/config/conf.php';

// generisanje potrebnih objekata



$html = '
  <img src="../images/logo.png" >
  <span>Vojvode Živojina Mišića 237, 21400 Bačka Palanka, tel: +381 21 751112, mob: +381 60 7511123</span>

  <h1 style="text-align: center">ZAPISNIK O UGRADNJI</h1>
  <pre>
  <hr>
  
  Datum: ______________ Mesto: ____________________

  Narucilac: ______________________________________
  <hr>
  
  Ugovor broj: _________________________ Radni nalog broj: ___________________________ 

  Ugradjene pozicije jedninice: ______________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________
  <hr>
  
  Vreme pocetka ugradnje: ________________ Vreme zavrsetka ugradnje: _________________

  Primedbe narucioca: ________________________________________________________________
  
  ____________________________________________________________________________________

  ____________________________________________________________________________________
  <hr>
  <br>
  Napomena: Potpisivanjem ovog zapisnika narucilac potvrdjuje prijem narucenih
  proizvoda i od datuma ugradnje zapocinje garantni rok.
  <hr>
  <br>
  Za ROLOSTIL szr                                     za Narucioca

  ____________________                                ____________________
  </pre>
';

$pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('test_name.pdf', 'I');


//============================================================+
// END OF FILE                                                
//============================================================+

?>