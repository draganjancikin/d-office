<?php
$page = "projects";
/*
require_once('../config/lang/srp.php');
require_once('../tcpdf.php');
*/
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

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/autoload.php';

// generisanje potrebnih objekata
$client = new ClientController();
$contact = new ContactController();
$project = new ProjectController();
$date = date('d M Y');

$project_id = $_GET['project_id'];

$project_data = $project->getProject($project_id);
$client_data = $client->getClient($project_data['client_id']);

$contacts = $contact->getContactsById($project_data['client_id']);
    
$contact_item[0] = "";
$contact_item[1] = "";

if (!empty($contacts)) {
    
    $count = 0;
    foreach ($contacts as $contact):
        if (isset($contact['number']) AND $count == 0 ){ 
            $contact_item[0] = $contact['number'];
        } elseif (isset($contact['number']) AND $count == 1) {
            $contact_item[1] = $contact['number'];
        }
        $count++; 
    endforeach;
    
} 

$html = '
  <style type="text/css">table {padding: 3px 10px 3px 10px; }</style>
  
  <table border="0">
    <tr><td width="150px"><h3>RADNI NALOG </h3> </td><td width="400px">za projekat #'.str_pad($project_data['pr_id'], 4, "0", STR_PAD_LEFT).'</td><td>'.date('d-M-Y', strtotime($project_data['date'])).'</td></tr>
  </table>
  
  <table border="0">
    <tr><td width="80px">klijent:</td> <td width="auto">'.$client_data['name'] . ($client_data['name_note']<>""?', '.$client_data['name_note']:"").'</td></tr>
    <tr><td>adresa:</td>               <td>'.$client_data['street_name'].' '.$client_data['home_number'].', '.$client_data['city_name'].', '.$client_data['state_name'].', '.$client_data['address_note'].'</td></tr>
    <tr><td></td>                      <td>' .$contact_item[0]. '</td></tr>
    ' .( $contact_item[1]=="" ? "" : '<tr><td></td><td>' .$contact_item[1]. '</td></tr>' ). '
  </table>
  
  <table border="1">
    <tr><td>'.$project_data['title'].'</td></tr>
  </table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// ispisivanje beležaka uz projekat
$project_notes = $project->getNotesByProject($project_id);
foreach ($project_notes as $project_note):
    
  $html = '
    <table><tr><td width="90px">' . date('d-M-Y', strtotime($project_note['date'])) . '</td><td width="695px">'.nl2br($project_note['note']).'</td></tr></table>
  ';
  $pdf->writeHTML($html, true, false, true, false, '');
    
endforeach;
                
// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('nalog_' .$client_data['name']. '.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+