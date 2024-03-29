<?php

require_once '../../config/appConfig.php';
require_once '../../vendor/autoload.php';
require_once '../../config/bootstrap.php';

// Include the main TCPDF library (search for installation path).
require_once '../../config/tcpdf_include.php';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$company_info = $entityManager->getRepository('\Roloffice\Entity\CompanyInfo')->getCompanyInfoData(1);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($company_info['name']);
$pdf->SetTitle($company_info['name'] . ' - Dnevni izveštaj');
$pdf->SetSubject($company_info['name']);
$pdf->SetKeywords($company_info['name'] . ', PDF, dnevni izveštaj');

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
// Print a table.

// Add a page.
$pdf->AddPage();

$date = $_GET['date'];

$daily_transactions = $entityManager->getRepository('\Roloffice\Entity\Payment')->getDailyCashTransactions($date);
$daily_cash_saldo = $entityManager->getRepository('\Roloffice\Entity\Payment')->getDailyCashSaldo($date);

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
  $accounting_document = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getAccountingDocumentByTransaction($transaction->getId());
  if ($accounting_document) {
    // $pidb_data = $pidb->getPidb($transaction['pidb_id']);
    $accounting_document_data = $accounting_document->getOrdinalNumInYear(). ' ' .$accounting_document->getClient()->getName(). ' ' .$accounting_document->getTitle();
  } else {
    $accounting_document_data = "";
  }
  $html = '
  <table>
    <tr>
      <td>' . $transaction->getType()->getName()  . '</td>
      <td>'.$accounting_document_data.'</td>
      <td>' . $transaction->getNote() . '</td>
      <td align="right">' . $transaction->getAmount() . '</td>
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

$pdf->Output( 'dnevni izvestaj.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
