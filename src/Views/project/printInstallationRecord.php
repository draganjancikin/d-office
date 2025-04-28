<?php
$page = 'projects';

require_once '../config/appConfig.php';
require_once '../vendor/autoload.php';

// Include the main TCPDF library (search for installation path).
require_once '../config/tcpdf_include.php';

// Create new PDF document.
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$company_info = $entityManager->getRepository('\App\Entity\CompanyInfo')->getCompanyInfoData(1);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($company_info['name']);
$pdf->SetTitle('Zapisnik o ugradnji (montaži)');
$pdf->SetSubject($company_info['name']);
$pdf->SetKeywords($company_info['name'] . ', PDF, zapisnik');

// Remove default header/footer.
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default monospaced font.
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins.
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, PDF_MARGIN_BOTTOM);

// Set auto page breaks.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor.
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set some language-dependent strings.
// $pdf->setLanguageArray($l);

// Set some language-dependent strings (optional).
if (@file_exists(dirname(__FILE__).'/lang/srp.php')) {
  require_once(dirname(__FILE__).'/lang/srp.php');
  $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// Set font.
$pdf->SetFont('dejavusans', '', 10);

// ---------------------------------------------------------

// Add a page.
$pdf->AddPage();

$project = $entityManager->find('App\Entity\Project', $project_id);
$client = $entityManager->getRepository('\App\Entity\Client')->getClientData($project->getClient()->getId());

$html = '
  <img src="../images/logo.png" >
  <span>' . $company_info['name'] . ', ' . $company_info['city']. ', tel: +381 21 751112, mob: +381 60 7511123</span>

  <h1 style="text-align: center">ZAPISNIK O MONTAŽI</h1>
  <hr>
  <div>Datum: ______________ Adresa montaže: ___________________________________</div>

  <div>Naručilac: <u>   ' . $client['name'] . ', ' . $client['city'] . '   </u></div>

  <div>Ugovor broj: _________________________ Projekat broj: <u>   ' . str_pad($project->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '/' . $project->getCreatedAt()->format('Y') . '   </u></div> 
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
  ' . $company_info['name'] . '
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  za Narucioca
  <pre>
____________________                            ____________________
  </pre>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Reset pointer to the last page.
$pdf->lastPage();

// -----------------------------------------------------------------------------

ob_end_clean();
//Close and output PDF document
$pdf->Output('test_name.pdf', 'I');

//==============================================================================
// END OF FILE
//==============================================================================
