<?php
$page = "pidb";

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/appConfig.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../vendor/autoload.php';
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/bootstrap.php';

// Include the main TCPDF library (search for installation path).
require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') .'/../config/tcpdf_include.php';

// Create new PDF document.
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Roloffice');
$pdf->SetTitle('ROLOSTIL - Dokument');
$pdf->SetSubject('Rolostil');
$pdf->SetKeywords('Rolostil, PDF, Proforma, Invoice');

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

$accounting_document__id = $_GET['accounting_document__id'];
$accounting_document__data = $entityManager->find("\Roloffice\Entity\AccountingDocument", $accounting_document__id);

switch ($accounting_document__data->getType()->getId()) {
    case 1:
        $accounting_document__type = "Predracun";
        break;
    case 2:
        $accounting_document__type = "Otpremnica";
        break;
    case 4:
        $accounting_document__type = "Povratnica";
        break;
    default:
        # code...
        break;
}

$client_data = $accounting_document__data->getClient();

if ($client_data->getCountry() === null) {
    $client_country = null;
} else {
    $client_country = $entityManager->find('\Roloffice\Entity\Country', $client_data->getCountry());
}
if ($client_data->getCity() === null) {
    $client_city = null;
} else {
    $client_city = $entityManager->find('\Roloffice\Entity\City', $client_data->getCity());
}
if ($client_data->getStreet() === null) {
    $client_street = null;
} else {
    $client_street = $entityManager->find('\Roloffice\Entity\Street', $client_data->getStreet());
}

$client_contacts = $client_data->getContacts();

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
$preferences = $entityManager->find('Roloffice\Entity\Preferences', 1);
$kurs = $preferences->getKurs();
$company_info = $entityManager->find('Roloffice\Entity\CompanyInfo', 1);
$country = $entityManager->find("\Roloffice\Entity\Country", $company_info->getCountry());
$city = $entityManager->find("\Roloffice\Entity\City", $company_info->getCity());
$street = $entityManager->find("\Roloffice\Entity\Street", $company_info->getStreet());

$html = '
<style type="text/css">
    table { padding-top: 5px; padding-bottom: 5px; }
</style>

<table border="0">
    <tr>
        <td width="690px" colspan="3"><h1>' . $company_info->getName() . '</h1></td>
    </tr>
    <tr>
        <td width="340px" colspan="2">'
            . $street->getName() . ' ' . $company_info->getHomeNumber() . '<br />'
            . $city->getName() . '<br />
            PIB: ' . $company_info->getPib() . ', MB: ' . $company_info->getMb() . '<br />'
            . $company_info->getBankAccount1() . '<br />'
            . $company_info->getBankAccount2() . '
        </td>
        <td width="350px">
            Kupac:<br />'
            . $client_data->getName() . ' '
            . ($client_data->getLb()<>""?'<br />
            PIB '.$client_data->getLb():"") . '<br />'
            . ($client_street ? $client_street->getName() : "") . ' '
            . $client_data->getHomeNumber() . '<br />'
            . ($client_city ? $client_city->getName() : "")
            . ($client_country ? $client_country->getName() : "") . '<br />'
            . $contact_item[0] . ', ' . $contact_item[1] . '
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <h2>'
                . $accounting_document__type . ' broj: '
                . str_pad($accounting_document__data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT)
                . ' - '
                . $accounting_document__data->getDate()->format('m') .'
            </h2>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            Datum i mesto izdavanja: ' . $accounting_document__data->getDate()->format('d M Y') . '.g. Bačka Palanka
        </td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$html = '
<table border="1" >
    <tr>
        <td width="30px" align="center">red.<br />br.</td>
        <td ' . ($accounting_document__data->getType()->getId() == 2 ? 'width="495px"' : 'width="190px"') . ' align="center">
            naziv proizvoda
        </td>
        <td width="35px" align="center">jed.<br />mere</td>
        <td width="53px" align="center">kol.</td>
        ' . ($accounting_document__data->getType()->getId() == 2
                ? ""
                : '
                    <td width="70px" align="center">cena po<br />jed. mere</td>
                    <td width="37px" align="center">rabat<br />%</td>
                    <td width="80px" align="center">poreska<br />osnovica</td>
                    <td width="37px" align="center">PDV<br />%</td>
                    <td width="70px" align="center">iznos<br />PDV-a</td>
                    <td width="80px" align="center">ukupno</td>'
            ) . '
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$count = 0;
$total_tax_base = 0;
$total_tax_amount = 0;
$total = 0;
$total_eur = 0;

$ad_articles = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getArticles($accounting_document__id);

foreach ($ad_articles as $ad_article):

    $ad_a_properties = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticleProperty')
                                     ->findBy(array('accounting_document_article' => $ad_article->getId()), array());
    $property_temp = '';
    $property_counter = 0;
    foreach ($ad_a_properties as $ad_a_property):
        $property_counter ++;
        $property_name = $ad_a_property->getProperty()->getName();
        $property_quantity = number_format($ad_a_property->getQuantity(), 2, ",", ".");
        $property_temp = $property_temp . ( $property_counter==2 ? 'x' : '' ) .$property_quantity . 'cm';
    endforeach;
    
    $count++;
    
    $ad_a_quantity = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')
                                    ->getQuantity($ad_article->getId(), $ad_article->getArticle()->getMinCalcMeasure(), $ad_article->getPieces());

    $tax_base = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')
                                ->getTaxBase($ad_article->getPrice(), $ad_article->getDiscount(), $ad_a_quantity);

    $tax_amount = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')
                                ->getTaxAmount($tax_base, $ad_article->getTax());

    $html = '
    <style type="text/css"> table{ padding: 0px; margin: 0px; }</style>
    <table border="0" style="font-size: 10px">
        <tr>
            <td width="30px" align="center">' . $count . '</td>
            <td ' . ($accounting_document__data->getType()->getId() == 2 ? 'width="495px"' : 'width="190px"') . '>'
                . $ad_article->getArticle()->getName()
                . '<span style="font-size: 7">' . ( $ad_article->getNote() == "" ? "" : ', '.$ad_article->getNote() ) . '</span>'
                . '<br />'
                . $property_temp. ' ' . $ad_article->getPieces() . ' kom
            </td>
            <td align="center" width="35px">' . $ad_article->getArticle()->getUnit()->getName() . '</td>
            <td width="53px" align="right">'. number_format($ad_a_quantity, 2, ",", ".") . '</td>'
            . ($accounting_document__data->getType()->getId() == 2
                ? ""
                : '
                    <td width="70px" align="right">'
                        . number_format( $ad_article->getPrice() * $kurs, 2, ",", ".") . '
                    </td>
                    <td width="37px" align="right">'
                        . number_format( $ad_article->getDiscount(), 2, ",", ".") . '
                    </td>
                    <td width="80px" align="right">'
                        . number_format( $tax_base * $kurs, 2, ",", ".") . '
                    </td>
                    <td width="37px" align="right">'
                        . number_format( $ad_article->getTax(), 2, ",", ".") .'
                    </td>
                    <td width="70px" align="right">'
                        . number_format( $tax_amount * $kurs, 2, ",", ".") . '
                    </td>
                    <td width="80px" align="right">'
                        . number_format(
                            $sub_total = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')
                                                       ->getSubTotal($tax_base, $tax_amount ) * $kurs, 2, ",", "."
                        ) . '
                    </td>'
            ). '
        </tr>
    </table>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');

    $total_tax_base = $total_tax_base + $tax_base;
    $total_tax_amount = $total_tax_amount + $tax_amount;
    $total = $total_tax_base + $total_tax_amount;

endforeach;

$income = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getIncome($accounting_document__id);

$html = ''.($accounting_document__data->getType()->getId() == 2 ? "" : '
<style type="text/css">table {	padding: 0px; margin: 0px; }</style>

<table><tr><td width="685px" colspan="10" style="border-bottom-width: inherit;"></td></tr></table>

<table border="0">
    <tr>
        <td colspan="3" width="265px"></td>
        <td colspan="2" width="135px" style="border-bottom-width: inherit;">ukupno poreska osnovica</td>
        <td colspan="2" width="100px" align="right" style="border-bottom-width: inherit;">'
            . number_format($total_tax_base * $kurs, 2, ",", ".") . '
        </td>
        <td colspan="2" width="105px"></td><td width="80px"></td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="4" style="border-bottom-width: inherit;">ukupno iznos PDV-a</td>
        <td colspan="2" align="right" style="border-bottom-width: inherit;">'
            . number_format($total_tax_amount * $kurs, 2, ",", ".") . '
        </td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="border-bottom-width: inherit;">UKUPNO</td>
        <td colspan="2" align="right" style="border-bottom-width: inherit;">'
            . number_format($total * $kurs, 2, ",", ".") . '
        </td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="border-bottom-width: inherit;">Avans</td>
        <td colspan="2" align="right" style="border-bottom-width: inherit;">'
            . number_format(
                ($avans = $entityManager->getRepository('\Roloffice\Entity\AccountingDocument')->getAvans($accounting_document__id)) * $kurs, 2, ",", "."
            ).'
        </td>
        <td></td>
    </tr>
    <tr style="font-weight:bold;">
        <td colspan="3"></td>
        <td colspan="5" style="border-bottom-width: inherit;">OSTALO ZA UPLATU</td>
        <td colspan="2" align="right" style="border-bottom-width: inherit;">'
            . number_format(($total-$avans-$income) * $kurs, 2, ",", ".") . '
        </td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5"></td>
        <td colspan="2" align="right" style="font-size: 11px;">
            ( &#8364; '.number_format($total-$avans-$income, 4, ",", ".").' )
        </td>
    </tr>
</table>
').'
';

$pdf->writeHTML($html, true, false, true, false, '');

$html = '' . ($accounting_document__data->getType()->getId() == 2 ? "" : '
<style type="text/css">table { padding: 0px; margin: 0px; }</style>
<table>
    <tr><td width="105px">Slovima: </td><td width="580px" style="background-color: #E0E0E0;"></td></tr>
    <tr><td>Način plaćanja: </td>       <td style="background-color: #E0E0E0;">Virmanom - nalogom za prenos</td></tr>
    <tr><td>Rok plaćanja: </td>         <td style="background-color: #E0E0E0;"></td></tr>
    <tr><td>Poziv na broj: </td>        <td style="background-color: #E0E0E0;">' . str_pad($accounting_document__data->getOrdinalNumInyear(), 3, "0", STR_PAD_LEFT).' - '.$accounting_document__data->getDate()->format('m') . '</td></tr>
    <tr><td></td></tr>
</table>
').'
<table border="1">
    <tr><td width="685px">Napomena:<br />' . nl2br($accounting_document__data->getNote()) . '</td></tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

if ($accounting_document__data->getType()->getId() == 2) {
    $html = '
    <table>
        <tr><td></td><td></td><td></td></tr>
        <tr><td></td><td></td><td></td></tr>
        <tr><td>______________________</td><td></td><td align="right">______________________</td></tr>
        <tr><td>robu izdao</td><td></td><td align="right">robu primio</td></tr>
        <tr><td></td><td></td><td></td></tr>
        <tr><td></td><td></td><td></td></tr>
        <tr><td align="center"></td><td></td><td align="center"></td></tr>
    </table>
    ';
    $pdf->writeHTML($html, true, false, true, false, '');
} else {
    $html = '
    <table>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td align="right"></td><td align="center">___________________________<br />odgovorno lice</td></tr>
    </table>
    ';
    $pdf->writeHTML($html, true, false, true, false, '');
}

// Reset pointer to the last page.
$pdf->lastPage();

// ---------------------------------------------------------

// Close and output PDF document.
$pdf->Output(
    $accounting_document__type . '_'
    . str_pad($accounting_document__data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT) . '-'
    . $accounting_document__data->getDate()->format('m') . '.pdf', 'I'
);

//============================================================+
// END OF FILE.
//============================================================+
