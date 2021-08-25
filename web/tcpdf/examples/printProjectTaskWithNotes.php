<?php

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/bootstrap.php';

require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Rolostil');
$pdf->SetTitle('ROLOSTIL - Radni nalog');
$pdf->SetSubject('Rolostil');
$pdf->SetKeywords('Rolostil, PDF,radni nalog');

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

$project_id = $_GET['project_id'];
$project = $entityManager->find('Roloffice\Entity\Project', $project_id);

$client = $entityManager->find('Roloffice\Entity\Client', $project->getClient()->getId());
$client_country = $entityManager->find('\Roloffice\Entity\Country', $client->getCountry());
$client_city = $entityManager->find('\Roloffice\Entity\City', $client->getCity());
$client_street = $entityManager->find('\Roloffice\Entity\Street', $client->getStreet());

$client_contacts = $client->getContacts();
    
$contact_item[0] = "";
$contact_item[1] = "";

if (!empty($client_contacts)) {
    
  $count = 0;
  foreach ($client_contacts as $client_contact):
    if ( NULL !== $client_contact->getBody() AND $count == 0 ){ 
      $contact_item[0] = $client_contact->getBody();
    } elseif ( NULL !== $client_contact->getBody() AND $count == 1) {
      $contact_item[1] = $client_contact->getBody();
    }
    $count++; 
  endforeach;
    
}

$html = '
  <style type="text/css">table {padding: 3px 10px 3px 10px; }</style>
  
  <table border="0">
    <tr><td width="150px"><h3>RADNI NALOG </h3> </td><td width="400px">za projekat #'.str_pad($project->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT).'</td><td>'.$project->getCreatedAt()->format('d-M-Y').'</td></tr>
  </table>
  
  <table border="0">
    <tr><td width="80px">klijent:</td> <td width="auto">'.$client->getName() . ($client->getNameNote()<>""?', '.$client->getNameNote():"").'</td></tr>
    <tr><td>adresa:</td>               <td>'.$client_street->getName().' '.$client->getHomeNumber().', '.$client_city->getName().', '.$client_country->getName().', '.$client->getAddressNote().'</td></tr>
    <tr><td></td>                      <td>' .$contact_item[0]. '</td></tr>
    ' .( $contact_item[1]=="" ? "" : '<tr><td></td><td>' .$contact_item[1]. '</td></tr>' ). '
  </table>
  
  <table border="1">
    <tr><td>'.$project->getTitle().'</td></tr>
  </table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Get amd print project notes.
$date_temp = "";
$notes = $entityManager->getRepository('\Roloffice\Entity\Project')->getNotesByProject($project_id);
foreach ($notes as $note):
  $html = '
    <table><tr><td width="90px">' . ( $note->getCreatedAt()->format('d-M-Y') != $date_temp ? $note->getCreatedAt()->format('d-M-Y') : "" ) . '</td><td width="695px">'.nl2br($note->getNote()).'</td></tr></table>
  ';
  $pdf->writeHTML($html, true, false, true, false, '');
  $date_temp = $note->getCreatedAt()->format('d-M-Y');
endforeach;
                
// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('nalog_' .$client->getName(). '.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+