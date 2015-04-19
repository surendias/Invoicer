<?php
include('mpdf/mpdf.php');

/* recaptcha validation */
$data = ['secret' => '6LcElgUTAAAAAFF2FpM8XJoGbiPvstYEbT9_6-SJ', 'response' => $_REQUEST['g-recaptcha-response']];
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

    //Configure the PDF generator settings
    //new mPDF($mode, $format, $font_size, $font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation);
        $mpdf=new mPDF('win-1252','A4','','',20,15,0,0,10,10);
        $mpdf->useOnlyCoreFonts = true;    // false is default
    //$mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("Invoice_" . $_REQUEST["invoice_number"]);
        $mpdf->SetAuthor($_REQUEST["your_company_name"]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setAutoTopMargin = 'stretch';
    //$mpdf->setAutoBottomMargin = 'stretch';

        $currency_code = $_REQUEST['currency_code'];
        $invoice_details_table = "";
        $sub_total = number_format($_REQUEST["sub_total"], 2);
        $total_due = number_format($_REQUEST["total_due"], 2);
        $advance = number_format($_REQUEST["advance"], 2);

        for($i = 0; $i < count($_REQUEST['item_no']); $i++) {
            $invoice_details_table .= '<tr><td align="center">'.$_REQUEST['item_no'][$i].'</td><td>'.$_REQUEST['item'][$i].'</td><td>'.$_REQUEST['description'][$i].'</td><td>'. $currency_code .' '. number_format($_REQUEST['unit_price'][$i], 2) .'</td><td align="center">'.$_REQUEST['quantity'][$i].'</td><td align="right">'.$currency_code . " " .number_format($_REQUEST['total'][$i], 2)."</td></tr>";
        }

        $html = '
    <!doctype html>
    <html class="no-js" lang="en">
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <head>
        <link rel="stylesheet" href="css/foundation.css"/>
        <link rel="stylesheet" href="css/style.css"/>
    </head>
    <body>

    <!--mpdf
    <htmlpageheader name="myheader">
        <div class="row text-center">
            <div class="large-12 columns">
                <h1 style="background: #000000; color: #fff; float: left; width: 100%; font-size: 18px;"> ' . $_REQUEST["invoice_title"] . '</h1>
            </div>
        </div>
    </htmlpageheader>

    <htmlpagefooter name="myfooter">
        <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
            Page {PAGENO} of {nb}
        </div>
    </htmlpagefooter>

    <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
    <sethtmlpagefooter name="myfooter" value="on" />
    mpdf-->

        <table cellpadding="10" style="border:0px;">
            <tr>
                <td>
                    <p><strong>Invoiced By: </strong>' . $_REQUEST["your_company_name"] . '<br/>
                        ' . $_REQUEST["your_address_1"]. ', ' . $_REQUEST["your_address_2"] . ',<br/>
                        ' . $_REQUEST["your_city"] . ', ' . $_REQUEST["your_state"] . ',<br/>
                        ' . $_REQUEST["your_country"] . ', ' . $_REQUEST["your_zip_code"] . '<br/>
                        <strong>T:</strong> ' . $_REQUEST["your_phone"] . ' <strong>E:</strong> ' . $_REQUEST["your_email"] . '<br/></p>
                </td>

                <td align="right">
                    <p><strong>Invoiced To: </strong>' . $_REQUEST["customer_company_name"] . '<br/>
                    ' . $_REQUEST["customer_address_1"] . ', ' . $_REQUEST["customer_address_2"] . ',<br/>
                    ' . $_REQUEST["customer_city"] . ', ' . $_REQUEST["customer_state"] . ',<br/>
                    ' . $_REQUEST["customer_country"] . ', ' . $_REQUEST["customer_zip_code"] . '<br/>
                    <strong>T:</strong> ' . $_REQUEST["customer_phone"] . ' <strong>E:</strong> ' . $_REQUEST["customer_email"] . '<br/></p>
                </td>
            </tr>
        </table>

        <div class="row text-right">
            <div class="large-12 columns">
                <p><strong>Invoice Date:</strong>  ' . $_REQUEST["invoice_date"] . '</p>
                <p><strong>Invoice Number:</strong>  ' . $_REQUEST["invoice_number"] . '</p>
            </div>
        </div>

        <style>
            tr:nth-child(2n) { background-color: #F9F9F9; }
        </style>


        <div class="row">
            <div class="large-12 columns">

                <table class="zebra">
                    <tr>
                        <th align="center">Line No</th>
                        <th align="center">Item</th>
                        <th align="center">Description</th>
                        <th align="center">Unit Price</th>
                        <th align="center">Quantity</th>
                        <th align="center">Total</th>
                    </tr>
                        ' .  $invoice_details_table . '
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Sub Total</th>
                        <td align="right">' . $currency_code . ' ' . $sub_total . '</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Taxes</th>
                        <td align="right">' . $_REQUEST["taxes"] . ' %</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Discounts</th>
                        <td align="right">' . $_REQUEST["discounts"] . ' %</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Advance</th>
                        <td align="right">' . $currency_code . ' ' . $advance . '</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Total Due</th>
                        <td align="right">' . $currency_code . ' ' . $total_due . '</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="large-12 columns">
                <div class="panel">
                    <p><strong>Terms:</strong> ' . $_REQUEST["terms"] . '</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="large-12 columns">
                <div class="panel">
                    <p><strong>Notes:</strong> ' . $_REQUEST["notes"] . '</p>
                </div>
            </div>
        </div>

        <script src="js/vendor/modernizr.js"></script>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script> $(document).foundation(); </script>
    </body>
    ';

    $mpdf->WriteHTML($html);

    $mpdf->Output(); exit;

    exit;
} else {
    echo "You look like a spam bot. Please make sure you click the captcha tool if your human. <a href='http://invoicer.surendias.com/'>Go Back</a>";
}