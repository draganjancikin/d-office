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

$date = $_GET['date'];

$daily_transactions = $pidb->getDailyCashTransactions($date);
$daily_cash_saldo = $pidb->getDailyCashSaldo($date);

$html = '
  <h1>Dnevni izveštaj</h1>
  <h4>'.$date.'.g</h4>
  <table>
    <thead>
      <tr>
        <th style="border-bottom: 1px solid black;">vrsta transakcije</th>
        <th style="border-bottom: 1px solid black;">vezani dokument</th>
        <th style="border-bottom: 1px solid black;">beleška</th>
        <th style="border-bottom: 1px solid black;" align="center">iznos</th>
      </tr>
    </thead>
  </table>
    ';
$pdf->writeHTML($html, true, false, true, false, '');

foreach($daily_transactions as $transaction):
  if ($transaction['pidb_id'] <> 0) {
    $pidb_data = $pidb->getPidb($transaction['pidb_id']);
    $pidb_data = $pidb_data['y_id']. ' ' .$pidb_data['client_name']. ' ' .$pidb_data['title'];
  } else {
    $pidb_data = "";
  }
  $html = '
  <table>
    <tr>
      <td>' . $transaction['type_name'] . '</td>
      <td>'.$pidb_data.'</td>
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
        <th style="border-top: 1px solid black;"></th>
        <th style="border-top: 1px solid black;"></th>
        <th style="border-top: 1px solid black;" align="right">stanje</th>
        <th style="border-top: 1px solid black;" align="right">'.$daily_cash_saldo.'</th>
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
