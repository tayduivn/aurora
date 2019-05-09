<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:09-05-2019 09:49:20 CEST , Tranava, Sloavakia

 Copyright (c) 2018, Inikoo

 Version 2.0
*/


require_once 'common.php';

require_once 'utils/object_functions.php';


$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
if (!$id) {
    exit;
}
$invoice = get_object('Invoice', $id);
if (!$invoice->id) {
    exit;
}

$store    = get_object('Store', $invoice->get('Invoice Store Key'));
$customer = get_object('Customer', $invoice->get('Invoice Customer Key'));


$number_orders = 0;
$number_dns    = 0;

$order = get_object('Order', $invoice->get('Invoice Order Key'));

if ($order->id) {
    $smarty->assign('order', $order);
    $number_orders = 1;

    $delivery_note = get_object('Delivery_Note', $order->get('Order Delivery Note Key'));


    if ($delivery_note->id) {
        $smarty->assign('delivery_note', $delivery_note);
        $number_dns = 1;

    }

}
$smarty->assign('customer', $customer);


$smarty->assign('number_orders', $number_orders);
$smarty->assign('number_dns', $number_dns);

if ($account->get('Account Country 2 Alpha Code') == $invoice->get('Invoice Address Country 2 Alpha Code')) {
    $invoice_numeric_code          = 100;
    $invoice_alpha_code            = 'OF';
    $invoice_numeric_code_total    = 100;
    $invoice_numeric_code_shipping = 101;
    $invoice_numeric_code_charges  = 102;

} else {
    $invoice_numeric_code          = 300;
    $invoice_numeric_code_total    = 200;
    $invoice_numeric_code_shipping = 201;
    $invoice_numeric_code_charges  = 202;

    $invoice_alpha_code = 'zOF';
}

$text = "R00\tT01\r\n";

$invoice_header_data = array(
    'R01',
    $invoice->get('Invoice Public ID'),
    $invoice->get('Invoice Customer Name'),
    $invoice->get('Invoice Registration Number'),
    date('d.m.Y', strtotime($invoice->get_date('Invoice Tax Liability Date'))),
    date('d.m.Y', strtotime($invoice->get_date('Invoice Tax Liability Date'))),
    date('d.m.Y', strtotime($invoice->get_date('Invoice Tax Liability Date'))),
    '0.00',
    $invoice->get('Invoice Items Net Amount') + $invoice->get('Invoice Shipping Net Amount') + $invoice->get('Invoice Charges Net Amount'),
    '0.00',
    '0.00',
    0,
    20,
    '0.00',
    $invoice->get('Invoice Total Tax Amount'),
    $invoice->get('Invoice Total Amount'),
    0




);

$invoice_header = "";
foreach ($invoice_header_data as $header_item) {
    $invoice_header .= $header_item."\t";
}
$invoice_header .= "\r\n";


$text .= $invoice_header;

/*
$row_data = array(
    'R02',
    0,
    311,
    200,
    '',
    '',
    $invoice->get('Invoice Total Amount'),
    round($invoice->get('Invoice Total Amount') * $invoice->get('Invoice Currency Exchange'), 2),
    $invoice->get('Invoice Customer Name'),
    'S',
    '',
    ''


);

$invoice_row = "";
foreach ($row_data as $column) {
    $invoice_row .= $column."\t";
}
$invoice_row .= "\r\n";
$text        .= $invoice_row;

$row_data = array(
    'R02',
    0,
    '',
    '',
    604,
    $invoice_numeric_code_total,
    $invoice->get('Invoice Items Net Amount'),
    round($invoice->get('Invoice Items Net Amount') * $invoice->get('Invoice Currency Exchange'), 2),
    'Items '.$store->get('Code').' '.$invoice->get('Invoice Tax Code'),
    '3',
    '',
    'X',
    '(Nedefinované)',
    'X',
    '(Nedefinované)',
    'X',
    '(Nedefinované)',
    'X',
    '(Nedefinované)',
    '','','','','',
    'X',
    '','','',0,0
);


$invoice_row = "";
foreach ($row_data as $column) {
    $invoice_row .= $column."\t";
}
$invoice_row .= "\r\n";
$text        .= $invoice_row;


if ($invoice->get('Invoice Shipping Net Amount') != 0) {
    $row_data = array(
        'R02',
        0,
        '',
        '',
        604,
        $invoice_numeric_code_shipping,
        $invoice->get('Invoice Shipping Net Amount'),
        round($invoice->get('Invoice Shipping Net Amount') * $invoice->get('Invoice Currency Exchange'), 2),
        'Shipping '.$store->get('Code').' '.$invoice->get('Invoice Tax Code'),
        '3',
        '',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        '','','','','',
        'A1',
        '','','',0,0
    );

    $invoice_row = "";
    foreach ($row_data as $column) {
        $invoice_row .= $column."\t";
    }
    $invoice_row .= "\r\n";
    $text        .= $invoice_row;

}


if ($invoice->get('Invoice Charges Net Amount') != 0) {
    $row_data = array(
        'R02',
        0,
        '',
        '',
        604,
        $invoice_numeric_code_charges,
        $invoice->get('Invoice Charges Net Amount'),
        round($invoice->get('Invoice Charges Net Amount') * $invoice->get('Invoice Currency Exchange'), 2),
        'Charges '.$store->get('Code').' '.$invoice->get('Invoice Tax Code'),
        '3',
        '',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        '','','','','',
        'A1',
        '','','',0,0
    );

    $invoice_row = "";
    foreach ($row_data as $column) {
        $invoice_row .= $column."\t";
    }
    $invoice_row .= "\r\n";
    $text        .= $invoice_row;

}


if ($invoice->get('Invoice Total Tax Amount') != 0) {
    $row_data = array(
        'R02',
        0,
        '',
        '',
        343,
        220,
        $invoice->get('Invoice Total Tax Amount'),
        round($invoice->get('Invoice Total Tax Amount') * $invoice->get('Invoice Currency Exchange'), 2),
        'Tax '.$store->get('Code').' '.$invoice->get('Invoice Tax Code'),
        '4',
        '',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        'X',
        '(Nedefinované)',
        '','','','','',
        'A1',
        '','','',0,0
    );

    $invoice_row = "";
    foreach ($row_data as $column) {
        $invoice_row .= $column."\t";
    }
    $invoice_row .= "\r\n";
    $text        .= $invoice_row;

}

*/
$text = mb_convert_encoding($text, 'iso-8859-2', 'auto');

header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=".$invoice->get('Invoice Public ID').".txt");


print $text;