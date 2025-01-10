<?php
$page = "nabavka";

require_once '../config/appConfig.php';
require_once '../vendor/autoload.php';
require_once '../config/bootstrap.php';

// Include the main TCPDF library (search for installation path).
require_once '../config/tcpdf_include.php';

// Create new PDF document.
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$company_info = $entityManager->getRepository('\App\Entity\CompanyInfo')->getCompanyInfoData(1);

$company_accounts = (strlen($company_info['bank_account_1']) > 0 ? $company_info['bank_account_1'] . ', ' : "")
	. (strlen($company_info['bank_account_2']) > 0 ? '<br />' . $company_info['bank_account_2'] . ', ' : "");

$company_contacts = (strlen($company_info['phone_1']) > 0 ? $company_info['phone_1'] . ', ' : "")
	. (strlen($company_info['phone_2']) > 0 ? $company_info['phone_2'] . ', ' : "")
	. (strlen($company_info['email_1']) > 0 ? $company_info['email_1'] . ', ' : "")
	. (strlen($company_info['email_2']) > 0 ? $company_info['email_2'] . ', ' : "")
	. (strlen($company_info['website_1']) > 0 ? $company_info['website_1'] . ', ' : "");

// Set document information.
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($company_info['name']);
$pdf->SetTitle($company_info['name'] . ' - Narudzbenica');
$pdf->SetSubject($company_info['name']);
$pdf->SetKeywords($company_info['name'] . ', PDF, narudzbenica');

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

$order_data = $entityManager->find("\App\Entity\Order", $order_id);

$supplier_data = $entityManager->getRepository('\App\Entity\Client')->getClientData($order_data->getSupplier());
$supplier_contacts = $supplier_data['contacts'];

$contact_item[0] = "";
$contact_item[1] = "";

$i=0;
foreach ($supplier_contacts as $supplier_contact) {
	if ($i < 2 && NULL !== $supplier_contact->getBody()) {
		$contact_item[$i] = $supplier_contact->getBody();
	}
	else {
		$contact_item[$i] = "";
	}
	$i++;
}

$html = '
<style>table { padding-top: 5px; padding-bottom: 5px; }</style>

<table>
	<tr>
		<td width="685px" colspan="3"><h1>' . $company_info['name'] . '</h1></td>
	</tr>
	<tr>
		<td width="340px" colspan="2">'
			. $company_info['street'] . ' ' . $company_info['home_number'] . '<br />'
			. $company_info['city'] . ', ' . $company_info['country'] . '<br />'
			. 'PIB: ' . $company_info['pib'] . ', MB: ' . $company_info['mb'] . '<br />'
	    . 'žiro računi: <br />' . $company_accounts . '<br />'
	    . 'kontakti: <br />' . $company_contacts . '
		</td>
		<td width="350px">Dobavljač:<br />'
			. $supplier_data['name'] . '<br />'
			. ($supplier_data['street'] ?? "") . ' ' . $supplier_data['home_number']
			. ($supplier_data['street'] && $supplier_data['city'] ? "<br />" : "")
			. ($supplier_data['city'] ?? "")
			. ($supplier_data['city'] && $supplier_data['country'] ? "<br />" : "")
			. ($supplier_data['country'] ?? "") . '<br />'
			. $contact_item[0] . ', ' . $contact_item[1] . '
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<h2>
				Narudžbenica: ' . str_pad($order_data->getOrdinalNumInYear(), 3, "0", STR_PAD_LEFT) . ' - '
				. $order_data->getDate()->format('Y') . '
			</h2>
		 </td>
	</tr>
	<tr>
		<td colspan="3">Datum i mesto: '.$order_data->getDate()->format('Y').'.g. Bačka Palanka</td>
	</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$html = '
<style>
table {
  padding: 0;
  margin: 0;
  border-collapse: collapse;
}
table tr th {
  border: 1px solid black;
  text-align: center;
}
.ordinal_num {
  width: 35px;
}
.article_name {
  width: 440px;
}
</style>
<table>
  <tr>
    <th class="ordinal_num">red.<br />broj</th>
    <th width="100px">šifra</th>
    <th class="article_name">naziv proizvoda</th>
    <th width="45px">jed.<br />mere</th>
    <th width="65px">količina</th>
  </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

$count = 0;
$total_tax_base = 0;
$total_tax_amount = 0;
$total = 0;
$total_eur = 0;

$materials_on_order = $entityManager->getRepository('\App\Entity\Order')->getMaterialsOnOrder($order_id);

foreach ($materials_on_order as $material_on_order):
	// $propertys = $material_on_order['propertys'];
	// $material_on_order_properties = $entityManager->getRepository('\App\Entity\OrderMaterial')->getPropertiesOnOrderMaterial($material_on_order->getId());
	$material_on_order_properties = $entityManager->getRepository('\App\Entity\OrderMaterial')
																									->getProperties($material_on_order->getId());
	$property_temp = '';
	$property_counter = 0;
	foreach ($material_on_order_properties as $material_on_order_property):
		$property_counter ++;
		$property_name = $material_on_order_property->getProperty()->getName();
		$property_quantity = number_format($material_on_order_property->getQuantity(), 1, ",", ".");
		$property_temp = $property_temp . ( $property_counter==2 ? ' x ' : '' ) .$property_quantity . 'cm';
		// old $property_temp = $property_temp . ', ' .$property_name . ' ' .$property_quantity . ' cm';
		// $property_temp = $property_temp . ' x' .$property_quantity . ' cm';
	endforeach;

	$count++;

	$html = '
	<style>
	table {
		padding: 0;
		margin: 0;
	}
	</style>
	<table border="0" style="font-size:14px">
		<tr>
			<td width="35px" align="center">'.$count.'</td>
			<td width="100px" class="center">' . ' ' . '</td>
			<td width="440px">'.$material_on_order->getMaterial()->getName()
				. ( $material_on_order->getNote() == "" ? "" : ', ' . $material_on_order->getNote() ) . '<br />'
				. $property_temp . ' - ' . $material_on_order->getPieces()
				. (($material_on_order->getMaterial()->getUnit()->getName() == "set" OR $material_on_order->getMaterial()->getUnit()->getName() == "par" ) ? " " . $material_on_order->getMaterial()->getUnit()->getName() : " kom " ) . '
			</td>
			<td align="center" width="45px">' . $material_on_order->getMaterial()->getUnit()->getName() . '</td>
			<td width="65px" align="right">'
				. number_format(
					$material_on_order_quantity = $entityManager->getRepository('\App\Entity\OrderMaterial')
					                                            ->getQuantity($material_on_order->getId(), $material_on_order->getMaterial()->getMinCalcMeasure(), $material_on_order->getPieces()),
					2,",", "."
				) . '
			</td>
		</tr>
	</table>
	';

	$pdf->writeHTML($html, true, false, true, false, '');

	$total_tax_base = $total_tax_base + $entityManager->getRepository('\App\Entity\OrderMaterial')
																										->getTaxBase($material_on_order->getPrice(), $material_on_order->getDiscount(), $material_on_order_quantity);
	$total_tax_amount = $total_tax_amount + $entityManager->getRepository('\App\Entity\OrderMaterial')
																												->getTaxAmount($total_tax_base, $material_on_order->getTax() );
	$total = $total_tax_base + $total_tax_amount;
endforeach;

$html = '
<style type="text/css"> table { padding: 0px; margin: 0px; }</style>
<table border="1">
	<tr>
		<td width="690px">Napomena:<br />'.nl2br($order_data->getNote()).'</td>
	</tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Reset pointer to the last page.
$pdf->lastPage();

// ---------------------------------------------------------

// Close and output PDF document.
// $pdf->Output('narudzbenica.pdf', 'FI');
$root = 'D:/ROLOSTIL/PORUDZBINE';
$folder = '';

$folder = match ($order_data->getSupplier()->getId()) {
	12 => "ALUMIL NS",
	126 => "ALUPLAST",
	1 => "ALUROLL BG",
	150 => "AURA DEKOR",
	3 => "EKV",
	289 => "ENTUZIAST",
	86 => "FEROLNOR",
	10 => "GU",
	20 => "HELISA",
	7 => "INFOMARKET",
	9 => "LALIC LINE",
	844 => "LIBELA",
	110 => "MIGRO",
	4776 => "NS-GLASS",
	18 => "MIREX",
	725 => "PORTA ROYAL",
	19 => "PRIVREDNO DRUSTVO METRO",
	58 => "PROFINE",
	774 => "ROLLPLAST",
	16 => "ROLOEXPRES",
	4 => "ROLOPLAST",
	15 => "ROLO REMONT",
	125 => "ROLOSTIL plus",
	84 => "ROLO-TIM",
	5 => "ROLO-TIM NS",
	648 => "SI-LINE",
	57 => "STAKLORAM plus",
	113 => "STUBLINA",
	81 => "TEHNI",
	116 => "TEHNOMARKET",
	131 => "TOMOVIC PLAST",
	526 => "VABIS",
	4567 => "VIDOVIC ALU-PLAST",
	91 => "WURTH",
	default => '',
};

$file_name = $supplier_data['name'] . ' - '
	. str_pad($order_data->getOrdinalNumInYear(), 3, "0", STR_PAD_LEFT) . '-'
	. $order_data->getDate()->format('m'). ' - '
	. $order_data->getDate()->format('d M') . '.pdf';

// Check if folder exist on local machine.
if (is_dir($root . '/' . $folder)) {
	// Close and output PDF document and save PDF to "$root.$folder./" .
	$pdf->Output($root . '/' . $folder . '/' . $file_name, 'FI');
}
else {
	// Close and output PDF document.
	$pdf->Output($file_name, 'I');
}

//============================================================+
// END OF FILE.
//============================================================+
