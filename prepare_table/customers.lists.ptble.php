<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 13 September 2015 18:30:16 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

$table = '`List Dimension`';

$where = ' where `List Scope`="Customer" and `List Use Type`="UserCreated" ';


if (in_array($parameters['parent_key'], $user->stores)) {
    $where .= sprintf(' and `List Parent Key`=%d  ', $parameters['parent_key']);

}


if (($parameters['f_field'] == 'name') and $f_value != '') {
    $wheref = sprintf(
        '  and  `List Name`  REGEXP "\\\\b%s" ', addslashes($f_value)
    );

} else {
    $wheref = '';
}


$_order = $order;
$_dir   = $order_direction;


if ($order == 'name') {
    $order = '`List Name`';
} elseif ($order == 'creation_date') {
    $order = '`List Creation Date`';
} elseif ($order == 'type') {
    $order = '`List Type`';
} elseif ($order == 'items') {
    $order = '`List Number Items`';
} else {
    $order = '`List Key`';
}


$fields
    = '`List Number Items`, CLD.`List key`,CLD.`List Name`,CLD.`List Parent Key`,CLD.`List Creation Date`,CLD.`List Type`';


$sql_totals = "select count(*) as num from $table $where";


?>
