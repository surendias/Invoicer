<?php

$currency_code = $_REQUEST['currency_code'];
$invoice_details_table = "";
$sub_total = number_format($_REQUEST["sub_total"], 2);
$total_due = number_format($_REQUEST["total_due"], 2);
$advance = number_format($_REQUEST["advance"], 2);

for($i = 0; $i < count($_REQUEST['item_no']); $i++) {
    $invoice_details_table .= "<tr><td>".$_REQUEST['item_no'][$i]."</td><td>".$_REQUEST['item'][$i]."</td><td>".$_REQUEST['description'][$i]."</td><td> ".$currency_code . " " .number_format($_REQUEST['unit_price'][$i], 2)."</td><td>".$_REQUEST['quantity'][$i]."</td><td> ".$currency_code . " " .number_format($_REQUEST['total'][$i], 2)."</td></tr>";
} ?>
<!doctype html>
<html class="no-js" lang="en">
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<head>
    <link rel="stylesheet" href="css/foundation.css"/>
    <script src="js/vendor/modernizr.js"></script>
    <style>
        h1 {
            background: #000000;
            color: #fff;
            padding: 10px;
            float: left;
            width: 100%;
            font-size: 18px;;
        }

        table {
            width: 100%;
            float: left;
        }
    </style>
</head>
<body>
<div class="row">
    <div class="large-12 columns">
        <h3>Invoice Preview</h3>

        <h4>Actions</h4>
        <div class="panel">
            <a href="" onclick="window.open('/print_preview.php?m=Print&ID=<?php echo $_REQUEST["invoice_number"]; ?>');" class="button success">Print</a>
        </div>
    </div>
</div>
<div class="row text-center">
    <div class="large-12 columns">
        <h1><?php echo $_REQUEST["invoice_title"]; ?></h1>
    </div>
</div>

<div class="row">
    <div class="large-6 columns">
        <p><strong>Invoiced To: </strong><?php echo $_REQUEST["customer_company_name"]; ?><br/>
            <?php echo $_REQUEST["customer_address_1"]; ?>, <?php echo $_REQUEST["customer_address_2"]; ?>,<br/>
            <?php echo $_REQUEST["customer_city"]; ?>, <?php echo $_REQUEST["customer_state"]; ?>,<br/>
            <?php echo $_REQUEST["customer_country"]; ?>, <?php echo $_REQUEST["customer_zip_code"]; ?><br/>
            <strong>T:</strong> <?php echo $_REQUEST["customer_phone"]; ?> <strong>E:</strong> <?php echo $_REQUEST["customer_email"]; ?><br/>

    </div>
    <div class="large-6 columns text-right">
        <p><strong>Invoiced By: </strong><?php echo $_REQUEST["your_company_name"]; ?><br/>
            <?php echo $_REQUEST["your_address_1"]; ?>, <?php echo $_REQUEST["your_address_2"]; ?>,<br/>
            <?php echo $_REQUEST["your_city"]; ?>, <?php echo $_REQUEST["your_state"]; ?>,<br/>
            <?php echo $_REQUEST["your_country"]; ?>, <?php echo $_REQUEST["your_zip_code"]; ?><br/>
            <strong>T:</strong> <?php echo $_REQUEST["your_phone"]; ?> <strong>E:</strong> <?php echo $_REQUEST["your_email"]; ?><br/>

    </div>
</div>

<div class="row text-right">
    <div class="large-12 columns">
        <p><strong>Invoice Date:</strong>  <?php echo $_REQUEST["invoice_date"]; ?></p>
        <p><strong>Invoice Number:</strong>  <?php echo $_REQUEST["invoice_number"]; ?></p>
    </div>
</div>

<div class="row">
    <div class="large-12 columns">

        <table>
            <tr>
                <th>Line No</th>
                <th>Item</th>
                <th>Description</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total</th>

            </tr>
            <?php echo $invoice_details_table; ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <th>Sub Total</th>
                <td><?php echo $currency_code; ?> <?php echo $sub_total; ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <th>Taxes</th>
                <td><?php echo $_REQUEST["taxes"]; ?> %</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <th>Discounts</th>
                <td><?php echo $_REQUEST["discounts"]; ?> %</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <th>Advance</th>
                <td><?php echo $currency_code; ?> <?php echo $advance; ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <th>Total Due</th>
                <td><?php echo $currency_code; ?> <?php echo $total_due; ?></td>
            </tr>
        </table>

    </div>
</div>

<div class="row">
    <div class="large-12 columns">
        <div class="panel">
            <p><strong>Terms:</strong> <?php echo $_REQUEST["terms"]; ?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="large-12 columns">
        <div class="panel">
            <p><strong>Notes:</strong> <?php echo $_REQUEST["notes"]; ?></p>
        </div>
    </div>
</div>


<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script>
    $(document).foundation();


</script>
</body>
</html>