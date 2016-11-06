<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 29 July 2016 at 16:17:51 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/


include_once 'utils/date_functions.php';

$where      = "where true  ";
$table
            = "  `Part Location Dimension` PLD  left join `Part Dimension` P on (PLD.`Part SKU`=P.`Part SKU`)  left join `Location Dimension` L on (PLD.`Location Key`=L.`Location Key`) ";
$filter_msg = '';
$wheref     = '';

$fields = '';


if ($parameters['parent'] == 'location') {
    $where = sprintf(
        "where PLD.`Location Key`=%d  ", $parameters['parent_key']
    );
} elseif ($parameters['parent'] == 'warehouse') {
    $where = sprintf(
        "where `Part Location Warehouse Key`=%d  ", $parameters['parent_key']
    );
} else {
    exit("parent not found ".$parameters['parent']);
}


if (isset($extra_where)) {
    $where .= $extra_where;
}


if ($parameters['f_field'] == 'reference' and $f_value != '') {
    $wheref .= " and  `Part Reference` like '".addslashes($f_value)."%'";
}


$_order = $order;
$_dir   = $order_direction;

if ($order == 'id') {
    $order = 'P.`Part SKU`';
} elseif ($order == 'stock') {
    $order = '`Part Current Stock`';
} elseif ($order == 'stock_status') {
    $order = '`Part Stock Status`';
} elseif ($order == 'reference') {
    $order = '`Part Reference`';
} elseif ($order == 'unit_description') {
    $order = '`Part Unit Description`';
} elseif ($order == 'available_for') {
    $order = '`Part Days Available Forecast`';

} elseif ($order == 'sold') {
    $order = ' sold ';
} elseif ($order == 'revenue') {
    $order = ' revenue ';
} elseif ($order == 'lost') {
    $order = ' lost ';
} elseif ($order == 'bought') {
    $order = ' bought ';
} elseif ($order == 'from') {
    $order = '`Part Valid From`';
} elseif ($order == 'to') {
    $order = '`Part Valid To`';
} elseif ($order == 'last_update') {
    $order = '`Part Last Updated`';
} else {

    $order = '`Part SKU`';
}


$sql_totals
    = "select count(Distinct P.`Part SKU`) as num from $table  $where  ";

$fields
    .= "
P.`Part SKU`,`Part Reference`,`Part Unit Description`,`Part Current Stock`,`Part Stock Status`,`Part Days Available Forecast`,
`Location Code`,PLD.`Location Key`,`Part Location Warehouse Key`,
`Quantity On Hand`,`Quantity In Process`,`Stock Value`,`Can Pick`,`Minimum Quantity`,`Maximum Quantity`,`Moving Quantity`,`Last Updated`

";


?>
