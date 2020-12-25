<?php

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Rolostil');
$pdf->SetTitle('ROLOSTIL - Dnevni izveštaj');
$pdf->SetSubject('Rolostil');
$pdf->SetKeywords('Rolostil, PDF, dnevni izveštaj');

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

// generisanje potrebnih objekata
$pidb = new \Roloffice\Controller\PidbController();

$daily_transactions = $pidb->getDailyCashTransactions();
    
$html = '
  <h1>Dnevni izveštaj</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>vrsta transakcije</th>
        <th>note</th>
        <th align="center">amount</th>
      </tr>
    </thead>
  </table>
    ';
$pdf->writeHTML($html, true, false, true, false, '');

foreach($daily_transactions as $transaction):
  
  $html = '
  <table>
    <tr>
      <td>' . $transaction['id'] . '</td>
      <td>' . $transaction['type_name'] . '</td>
      <td>' . $transaction['note'] . '</td>
      <td align="right">' . $transaction['amount'] . '</td>
    </tr>
  </table>
  ';
  $pdf->writeHTML($html, true, false, true, false, '');
  
endforeach;

$html = '    
    <table>
    <tfoot>
      <tr>
        <th></th>
        <th></th>
        <th align="right">stanje</th>
        <th align="right">_____</th>
      </tr>
    </tfoot>
  </table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
// $pdf->Output('narudzbenica.pdf', 'FI');

$pdf->Output( '__TEST__.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
