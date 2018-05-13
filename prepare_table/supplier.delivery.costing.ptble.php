<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 30 April 2018 at 11:49:05 BST, Sheffield, UK

 Copyright (c) 2015, Inikoo

 Version 2.0
*/

//exit;


$group_by=' group by P.`Part SKU`';

$where  = sprintf(' where POTF.`Supplier Delivery Key`=%d', $parameters['parent_key']
);
$wheref = '';
if ($parameters['f_field'] == 'code' and $f_value != '') {
    $wheref .= " and `Part Reference` like '".addslashes($f_value)."%'";
}

$_order = $order;
$_dir   = $order_direction;

if ($order == 'reference') {
    $order = '`Part Reference`';
} elseif ($order == 'created') {
    $order = '`Order Date`';
} elseif ($order == 'last_updated') {
    $order = '`Order Last Updated Date`';
} else {
    $order = '`Purchase Order Transaction Fact Key`';
}

$table
    = "
  `Purchase Order Transaction Fact` POTF
left join `Supplier Part Historic Dimension` SPH on (POTF.`Supplier Part Historic Key`=SPH.`Supplier Part Historic Key`)
 left join  `Supplier Part Dimension` SP on (POTF.`Supplier Part Key`=SP.`Supplier Part Key`)
 left join  `Part Dimension` P on (P.`Part SKU`=SP.`Supplier Part Part SKU`)

";

$sql_totals
    = "select count(distinct  P.`Part SKU`) as num from $table $where";


$fields
    = "`Part SKO Image Key`,`Part SKO Barcode`,`Supplier Delivery Quantity`,`Supplier Delivery Key`,`Part Reference`,P.`Part SKU`,`Supplier Delivery Checked Quantity`,`Part Package Description`,`Supplier Delivery Transaction Placed`,`Supplier Delivery Placed Quantity`,`Metadata`,
`Purchase Order Transaction Fact Key`,`Purchase Order Quantity`,POTF.`Supplier Part Key`,`Supplier Part Reference`,POTF.`Supplier Part Historic Key`,
`Supplier Part Description`,`Part Units Per Package`,`Supplier Part Packages Per Carton`,`Supplier Part Carton CBM`,
`Supplier Part Unit Cost`,`Part Package Weight`,`Supplier Delivery CBM`,`Supplier Delivery Weight`,
`Supplier Delivery Net Amount`,`Currency Code`,

sum(`Supplier Delivery Placed Quantity`*`Part Units Per Package`*`Supplier Part Packages Per Carton`) as skos_in,
sum(`Supplier Delivery Net Amount`) as items_amount,
sum(`Supplier Delivery Extra Cost Amount`) as extra_amount,
sum(`Supplier Delivery Extra Cost Account Currency Amount`) as extra_amount_account_currency,

sum(`Supplier Delivery Paid Amount`) as paid_amount,

            

`Supplier Delivery Net Amount`


";


?>