<?php
$page = "projects";

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
$pdf->SetTitle($company_info['name'] . ' - Radni nalog');
$pdf->SetSubject($company_info['name']);
$pdf->SetKeywords($company_info['name'] . ', PDF,radni nalog');

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

$project = $entityManager->find('App\Entity\Project', $project_id);

$client = $entityManager->getRepository('\App\Entity\Client')->getClientData($project->getClient()->getId());
$client_contacts = $client['contacts'];

$contact_item[0] = "";
$contact_item[1] = "";

if (!empty($client_contacts)) {
  $count = 0;
  foreach ($client_contacts as $client_contact) {
      if (NULL !== $client_contact->getBody() and $count == 0) {
          $contact_item[0] = $client_contact->getBody();
      } elseif (NULL !== $client_contact->getBody() and $count == 1) {
          $contact_item[1] = $client_contact->getBody();
      }
      $count++;
  }
}

$html = '
<style type="text/css">table {padding: 3px 10px 3px 10px; }</style>

<table border="0">
    <tr>
        <td width="150px">
            <h3>PROJEKAT</h3>
        </td>
        <td width="400px">
            za projekat #' . str_pad($project->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '
        </td>
        <td>'. $project->getCreatedAt()->format('d-M-Y') . '</td>
    </tr>
</table>

<table border="0">
    <tr>
        <td width="80px">klijent:</td>
        <td width="auto">' . $client['name'] . ($client['name_note'] <> "" ? ', ' . $client['name_note'] : "") . '</td>
    </tr>
    <tr>
        <td>adresa:</td>
        <td>'
            . ($client['street'] ? $client['street'] . ' ' . $client['home_number'] : '')
            . ($client['street'] && $client['city'] ? ", " : "")
            . ($client['city'] ?? '')
            . ($client['city'] && $client['country'] ? ", " : "")
            . ($client['country'] ?? '')
            . ($client['country'] && $client['address_note'] ? ", " : "")
            . $client['address_note'] . '
        </td>
    </tr>
    <tr>
        <td></td>
        <td>' . $contact_item[0] . '</td>
    </tr>'
    . ($contact_item[1]=="" ? "" : '<tr><td></td><td>' . $contact_item[1] . '</td></tr>' ) . '
</table>

<table border="1">
    <tr><td>'.$project->getTitle().'</td></tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Reset pointer to the last page.
$pdf->lastPage();

// ---------------------------------------------------------

// Close and output PDF document.
$pdf->Output('nalog_' . $client['name'] . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
