<?php
//print_r($_REQUEST);
include_once ('tcpdf/tcpdf.php');

/* recaptcha validation */
$data = ['secret' => '6LcsmAQTAAAAAATo5gKVZzIvCwuLO-JTGRHG3fmp', 'response' => $_REQUEST['g-recaptcha-response']];
$data = http_build_query($data);
$context = [
    'http' => [
        'method' => 'POST',
        'header' => "custom-header: custom-value\r\n" .
            "custom-header-two: custom-value-2\r\n",
        'content' => $data
    ]
];
$context = stream_context_create($context);
$result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);

$success_status = json_decode($result)->success;

if($success_status) {
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Invoicer');
    $pdf->SetTitle($_REQUEST['invoice_title']);
    $pdf->SetSubject($_REQUEST['invoice_number'] . ' - ' . $_REQUEST['invoice_date']);
    $pdf->SetKeywords('');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));

//$pdf->SetHeaderData(''.$_REQUEST['your_company_logo_url'], PDF_HEADER_LOGO_WIDTH, $_REQUEST['invoice_title'] . ' - ' . $_REQUEST['invoice_number'] . ' - ' . $_REQUEST['invoice_date'], $_REQUEST['invoice_title'] . ' - ' . $_REQUEST['invoice_number'] . ' - ' . $_REQUEST['invoice_date'], array(0,64,255), array(0,64,128));
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

// ---------------------------------------------------------

// set default font subsetting mode
    $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 12, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

// set text shadow effect
    $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

    $currency_code = $_REQUEST['currency_code'];
    $invoice_details_table = "";

    for($i = 0; $i < count($_REQUEST['item_no']); $i++) {
        $invoice_details_table .= "<tr><td>".$_REQUEST['item_no'][$i]."</td><td>".$_REQUEST['item'][$i]."</td><td>".$_REQUEST['description'][$i]."</td><td> ".$currency_code . " " .number_format($_REQUEST['unit_price'][$i], 2)."</td><td>".$_REQUEST['quantity'][$i]."</td><td> ".$currency_code . " " .number_format($_REQUEST['total'][$i], 2)."</td></tr>";
    }



    $sub_total = number_format($_REQUEST["sub_total"], 2);
    $total_due = number_format($_REQUEST["total_due"], 2);

//print_r($invoice_details_table);

// Set some content to print
    $html = <<<EOD
<h4 style="text-align:center">{$_REQUEST["invoice_title"]}</h4>
<p>Invoice Date: {$_REQUEST["invoice_date"]}</p>
<p>Invoice Number: {$_REQUEST["invoice_number"]}</p>
<br/><br/><br/>
<table border="1" cellpadding="5">
<tr>
<td>
<p><u>Invoice By:</u><br/>
{$_REQUEST["your_company_name"]}<br/>
{$_REQUEST["your_address_1"]}<br/>
{$_REQUEST["your_address_2"]}<br/>
{$_REQUEST["your_city"]}<br/>
{$_REQUEST["your_state"]}<br/>
{$_REQUEST["your_country"]}<br/>
{$_REQUEST["your_zip_code"]}<br/>
{$_REQUEST["your_phone"]}<br/>
{$_REQUEST["your_email"]}<br/>
{$_REQUEST["your_fax"]}<br/>
</p>
</td>
<td>
<p><u>Invoice To:</u><br/>
{$_REQUEST["customer_company_name"]}<br/>
{$_REQUEST["customer_address_1"]}<br/>
{$_REQUEST["customer_address_2"]}<br/>
{$_REQUEST["customer_city"]}<br/>
{$_REQUEST["customer_state"]}<br/>
{$_REQUEST["customer_country"]}<br/>
{$_REQUEST["customer_zip_code"]}<br/>
{$_REQUEST["customer_phone"]}<br/>
{$_REQUEST["customer_email"]}<br/>
{$_REQUEST["customer_fax"]}<br/>
</p>
</td>
</tr>
</table>
<br/><br/><br/>
<h4>Invoice Details</h4>

<table border="1" cellpadding="5">
                <thead>
                <tr>
                    <th>Line No</th>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Total</th>

                </tr>
                </thead>
                <tbody>
                {$invoice_details_table}




                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>Sub Total</th>
                    <td>{$currency_code} {$sub_total}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>Taxes</th>
                    <td>{$_REQUEST["taxes"]} %</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>Discounts</th>
                    <td>{$_REQUEST["discounts"]} %</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>Total Due</th>
                    <td>{$currency_code} {$total_due}</td>
                </tr>
</tbody>
            </table>
<br/><br/><br/>
<table border="1" cellpadding="5">
<tr><td><p>Terms: {$_REQUEST["terms"]}</p></td></tr>
<tr><td><p>Notes: {$_REQUEST["notes"]}</p></td></tr>
</table>

EOD;

// Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
    $pdf->Output('invoice-'.$_REQUEST['invoice_number'].'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
} else {
    echo "You look like a spam bot. Please make sure you click the captcha tool if your human. <a href='http://invoicer.surendias.com/'>Go Back</a>";
}

