<?php
$page = "pidb";

require_once '../../config/appConfig.php';
require_once '../../vendor/autoload.php';
require_once '../../config/bootstrap.php';

// Include the main TCPDF library (search for installation path).
require_once '../../config/tcpdf_include.php';

// Create new PDF document.
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$company_info = $entityManager->getRepository('\Roloffice\Entity\CompanyInfo')->getCompanyInfoData(1);

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($company_info['name']);
$pdf->SetTitle($company_info['name'] . ' - Dokument');
$pdf->SetSubject($company_info['name']);
$pdf->SetKeywords($company_info['name'] . ', PDF, nalog');

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

$preferences = $entityManager->find('Roloffice\Entity\Preferences', 1);
$kurs = $preferences->getKurs();

$html = '
<style type="text/css">table { padding-top: 5px; padding-bottom: 5px; }</style>

<table border="0">
    <tr>
        <td width="690px" colspan="3"><h1>' . $company_info['name'] . '</h1></td>
    </tr>
    <tr>
        <td width="340px" colspan="2">'
            . $company_info['street'] . ' ' . $company_info['home_number'] . '<br />'
            . $company_info['city'] . ', ' . $company_info['country'] . '<br />
            PIB: ' . $company_info['pib'] . ', MB: ' . $company_info['mb'] . '<br />'
            . $company_info['bank_account_1'] . '<br />'
            . $company_info['bank_account_2'] . '
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <h2>'
              . $accounting_document__type . ' br: ' . str_pad($accounting_document__data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT)
              . ' - ' . $accounting_document__data->getDate()->format('m') . '
            </h2>
        </td>
    </tr>
    <tr>
        <td colspan="3">Datum i mesto izdavanja: ' . $accounting_document__data->getDate()->format('d M Y') . '.g. Bačka Palanka</td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$html = '
<table border="1">
    <tr>
        <td width="30px" align="center">red.<br />br.</td>
        <td ' . ($accounting_document__data->getType()->getId() == 2 ? 'width="495px"' : 'width="185px"') . ' align="center">
            naziv proizvoda
        </td>
        <td width="35px" align="center">jed.<br />mere</td>
        <td width="53px" align="center">kol.</td>'
        . ($accounting_document__data->getType()->getId() == 2
            ? ""
            : '
                <td width="70px" align="center">cena po<br />jed. mere</td>
                <td width="37px" align="center">rabat<br />%</td>
                <td width="80px" align="center">poreska<br />osnovica</td>
                <td width="37px" align="center">PDV<br />%</td>
                <td width="70px" align="center">iznos<br />PDV-a</td>
                <td width="80px" align="center">ukupno</td>'
        ).'
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
                                ->getTaxAmount($tax_base, $ad_article->getTax() );

    $html = '
    <style type="text/css"> table{ padding: 0px; margin: 0px; }</style>
    <table border="0">
        <tr>
            <td width="30px" align="center">' . $count . '</td>
            <td ' . ($accounting_document__data->getType()->getId() == 2 ? 'width="495px"' : 'width="190px"') . '>'
                . $ad_article->getArticle()->getName()
                . '<span style="font-size: 7">' . ( $ad_article->getNote() == "" ? "" : ', ' . $ad_article->getNote() ) . '</span>'
                . '<br />' . $property_temp . ' ' . $ad_article->getPieces() . ' kom
            </td>
            <td align="center" width="35px">' . $ad_article->getArticle()->getUnit()->getName() . '</td>
            <td width="53px" align="right">'
                . number_format($ad_a_quantity, 2, ",", ".") . '
            </td>'
            . ($accounting_document__data->getType()->getId() == 2
                ? ""
                : '
                <td width="70px" align="right">'
                    . number_format($ad_article->getPrice() * $kurs, 2, ",", ".") . '
                </td>
                <td width="37px" align="right">'
                    . number_format($ad_article->getDiscount(), 2, ",", ".") . '</td>
                <td width="80px" align="right">'
                    . number_format($tax_base * $kurs, 2, ",", ".") . '
                </td>
                <td width="37px" align="right">'
                    . number_format($ad_article->getTax(), 2, ",", ".") . '
                </td>
                <td width="70px" align="right">'
                    . number_format($tax_amount * $kurs, 2, ",", ".") . '
                </td>
                <td width="80px" align="right">'
                    . number_format(
                        $sub_total = $entityManager->getRepository('\Roloffice\Entity\AccountingDocumentArticle')
                                                    ->getSubTotal($tax_base, $tax_amount ) * $kurs, 2, ",", "."
                    ). '
                </td>'
            ) . '
        </tr>
    </table>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');

    $total_tax_base = $total_tax_base + $tax_base;
    $total_tax_amount = $total_tax_amount + $tax_amount;

    $total = $total_tax_base + $total_tax_amount;
endforeach;

$html = '' . ($accounting_document__data->getType()->getId() == 2 ? "" : '
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
    <tr style="font-weight:bold;">
        <td colspan="3"></td>
        <td colspan="5" style="border-bottom-width: inherit;">UKUPNO ZA UPLATU</td>
        <td colspan="2" align="right" style="border-bottom-width: inherit;">'
            . number_format($total * $kurs, 2, ",", ".") . '
        </td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5"></td>
        <td colspan="2" align="right">
            (&#8364; ' . number_format($total, 2, ",", ".") . ')
        </td>
    </tr>
</table>
') . '
';

$pdf->writeHTML($html, true, false, true, false, '');

$html = '
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
}else{
    $html = '
<table>
    <tr><td></td><td></td></tr>
    <tr><td></td><td></td></tr>
    <tr><td align="right"></td><td align="center">___________________________</td></tr>
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
    . str_pad($accounting_document__data->getOrdinalNumInYear(), 4, "0", STR_PAD_LEFT)
    . '-' . $accounting_document__data->getDate()->format('m') . '.pdf', 'I'
);

//============================================================+
// END OF FILE.
//============================================================+
