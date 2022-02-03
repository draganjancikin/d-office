<?php
$page = 'projects';

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/bootstrap.php';

// Include the main TCPDF library (search for installation path).
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/tcpdf_include.php';

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
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, PDF_MARGIN_BOTTOM);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
// $pdf->setLanguageArray($l);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/srp.php')) {
  require_once(dirname(__FILE__).'/lang/srp.php');
  $pdf->setLanguageArray($l);
}


// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// ---------------------------------------------------------

// add a page
$pdf->AddPage();

// generisanje potrebnih objekata

$project_id = $_GET['project_id'];
$project = $entityManager->find('Roloffice\Entity\Project', $project_id);

$client = $entityManager->find('Roloffice\Entity\Client', $project->getClient()->getId());
$client_city = $entityManager->find('\Roloffice\Entity\City', $client->getCity());

$html = '
  <img src="../images/logo.png" >
  <span>'.COMPANY_STREET.', 21400 Bačka Palanka, tel: +381 21 751112, mob: +381 60 7511123</span>

  <h1 style="text-align: center">ZAPISNIK O MONTAŽI</h1>
  <hr>
  <div>Datum: ______________ Adresa montaže: ___________________________________</div>
  
  <div>Naručilac: <u>   '.$client->getName().', '.$client_city->getName().'   </u></div>
  
  <div>Ugovor broj: _________________________ Projekat broj: <u>   '.str_pad($project->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).'/'.$project->getCreatedAt()->format('Y').'   </u></div> 
  <hr>
  
  <pre style="color: #000000;">

  Ugradjene pozicije: ________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________
  <hr>
  
  Vreme pocetka ugradnje: ________________ Vreme zavrsetka ugradnje: _________________

  Primedbe narucioca: ________________________________________________________________
  
  ____________________________________________________________________________________

  ____________________________________________________________________________________

  ____________________________________________________________________________________
  <hr>
  Napomena: Potpisivanjem ovog zapisnika narucilac potvrdjuje prijem narucenih
  proizvoda i od datuma ugradnje zapocinje garantni rok.
  <hr>
  </pre>
  '.COMPANY_NAME.'    
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  za Narucioca
<pre>
  ____________________                                ____________________
  </pre>
';

$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

ob_end_clean();
//Close and output PDF document
$pdf->Output('test_name.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+

?>