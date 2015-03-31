<?php
//print_r($_REQUEST);
include_once('tcpdf/tcpdf.php');

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

if ($success_status) {
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
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

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
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
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
    $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

    $currency_code = $_REQUEST['currency_code'];
    $sub_total = number_format($_REQUEST["sub_total"], 2);
    $advance = number_format($_REQUEST["advance"], 2);
    $total_due = number_format($_REQUEST["total_due"], 2);
    $invoice_details_table = "";

    for ($i = 0; $i < count($_REQUEST['item_no']); $i++) {
        $invoice_details_table .= "<tr><td>" . $_REQUEST['item_no'][$i] . "</td><td>" . $_REQUEST['item'][$i] . "</td><td>" . $_REQUEST['description'][$i] . "</td><td> " . $currency_code . " " . number_format($_REQUEST['unit_price'][$i], 2) . "</td><td>" . $_REQUEST['quantity'][$i] . "</td><td> " . $currency_code . " " . number_format($_REQUEST['total'][$i], 2) . "</td></tr>";
    }




//print_r($invoice_details_table);

// Set some content to print
    $html = <<<EOD
    <!-- EXAMPLE OF CSS STYLE -->
<style>
.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #5D6975;
  text-decoration: underline;
}

body {
  position: relative;
  width: 21cm;
  height: 29.7cm;
  margin: 0 auto;
  color: #001028;
  background: #FFFFFF;
  font-family: Arial, sans-serif;
  font-size: 12px;
  font-family: Arial;
}

header {
  padding: 10px 0;
  margin-bottom: 30px;
}

#logo {
  text-align: center;
  margin-bottom: 10px;
}

#logo img {
  width: 90px;
}

h1 {
  border-top: 1px solid  #5D6975;
  border-bottom: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 2.4em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 20px 0;
  background: url(dimension.png);
}

#project {
  float: left;
}

#project span {
  color: #5D6975;
  text-align: right;
  width: 52px;
  margin-right: 10px;
  display: inline-block;
  font-size: 0.8em;
}

#company {
  float: right;
  text-align: right;
}

#project div,
#company div {
  white-space: nowrap;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

table tr:nth-child(2n-1) td {
  background: #F5F5F5;
}

table th,
table td {
  text-align: center;
}

table th {
  padding: 5px 20px;
  color: #5D6975;
  border-bottom: 1px solid #C1CED9;
  white-space: nowrap;
  font-weight: normal;
}

table .service,
table .desc {
  text-align: left;
}

table td {
  padding: 20px;
  text-align: right;
}

table td.service,
table td.desc {
  vertical-align: top;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table td.grand {
  border-top: 1px solid #5D6975;;
}

#notices .notice {
  color: #5D6975;
  font-size: 1.2em;
}

footer {
  color: #5D6975;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 8px 0;
  text-align: center;
}
</style>



      <h1>{$_REQUEST["invoice_title"]} - {$_REQUEST["invoice_number"]}</h1>
      <div id="company" class="clearfix">
        <div>{$_REQUEST["your_company_name"]}</div>
        <div>{$_REQUEST["your_address_1"]}, {$_REQUEST["your_address_2"]}<br /> {$_REQUEST["your_city"]}, {$_REQUEST["your_state"]}, {$_REQUEST["your_country"]}, {$_REQUEST["your_zip_code"]}</div>
        <div>{$_REQUEST["your_phone"]}</div>
        <div><a href="mailto:{$_REQUEST["your_email"]}">{$_REQUEST["your_email"]}</a></div>
      </div>
      <div id="project">
       <div><span>CLIENT</span> {$_REQUEST["customer_company_name"]}</div>
        <div><span>ADDRESS</span> {$_REQUEST["customer_address_1"]}, {$_REQUEST["customer_address_2"]}, {$_REQUEST["customer_city"]}, {$_REQUEST["customer_state"]}, {$_REQUEST["customer_country"]}, {$_REQUEST["customer_zip_code"]}</div>
        <div><span>PHONE</span> {$_REQUEST["customer_phone"]}</div>
        <div><span>EMAIL</span> <a href="mailto:{$_REQUEST["customer_email"]}">{$_REQUEST["customer_email"]}</a></div>
        <div><span>DATE</span> {$_REQUEST["invoice_date"]}</div>

      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th class="service">Line No</th>
            <th class="desc">Item</th>
            <th>Description</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          {$invoice_details_table}
          <tr>
            <td colspan="5">Sub Total</td>
            <td class="total">{$currency_code} {$sub_total}</td>
          </tr>
          <tr>
            <td colspan="5">Taxes</td>
            <td class="total">{$_REQUEST["taxes"]} %</td>
          </tr>
          <tr>
            <td colspan="5">Discounts</td>
            <td class="total">{$_REQUEST["discounts"]} %</td>
          </tr>
          <tr>
            <td colspan="5">Advance</td>
            <td class="total">{$currency_code} {$advance}</td>
          </tr>
          <tr>
            <td colspan="5" class="grand total">Total Due</td>
            <td class="grand total">{$currency_code} {$total_due}</td>
          </tr>
        </tbody>
      </table>
      <div id="notices">
        <div>Terms:</div>
        <div class="notice">{$_REQUEST["terms"]}</div>
      </div>
      <div id="notices">
        <div>Notes:</div>
        <div class="notice">{$_REQUEST["notes"]}</div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>

EOD;

// Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
    $pdf->Output('invoice-' . $_REQUEST['invoice_number'] . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
} else {
    echo "You look like a spam bot. Please make sure you click the captcha tool if your human. <a href='http://invoicer.surendias.com/'>Go Back</a>";
}

