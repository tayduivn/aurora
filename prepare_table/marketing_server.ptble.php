<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Refurbished: 2 October 2015 at 09:40:42 BST, Sheffield, UK
 Copyright (c) 2015, Inikoo

 Version 3

*/

$period_tag = get_interval_db_name($parameters['f_period']);

if (count($user->stores) == 0) {
    $where = "where false";
} else {
    $where = sprintf("where S.`Store Key` in (%s)", join(',', $user->stores));
}
$filter_msg = '';


$group = '';


$wheref = '';
if ($parameters['f_field'] == 'name' and $f_value != '') {
    $wheref .= " and  `Store Name` like '%".addslashes($f_value)."%'";
} elseif ($parameters['f_field'] == 'code' and $f_value != '') {
    $wheref .= " and  `Store Code` like '".addslashes($f_value)."%'";
}


$_order = $order;
$_dir   = $order_direction;


if ($order == 'families') {
    $order = '`Store Families`';
} elseif ($order == 'departments') {
    $order = '`Store Departments`';
} elseif ($order == 'code') {
    $order = '`Store Code`';
} elseif ($order == 'todo') {
    $order = '`Store In Process Products`';
} elseif ($order == 'discontinued') {
    $order = '`Store In Process Products`';
} elseif ($order == 'profit') {
    $order = '`Store '.$period_tag.' Profit`';

} elseif ($order == 'sales') {
    $order = '`Store '.$period_tag.' Invoiced Amount`';


} elseif ($order == 'name') {
    $order = '`Store Name`';
} elseif ($order == 'active') {
    $order = '`Store For Public Sale Products`';
} elseif ($order == 'outofstock') {
    $order = '`Store Out Of Stock Products`';
} elseif ($order == 'stock_error') {
    $order = '`Store Unknown Stock Products`';
} elseif ($order == 'surplus') {
    $order = '`Store Surplus Availability Products`';
} elseif ($order == 'optimal') {
    $order = '`Store Optimal Availability Products`';
} elseif ($order == 'low') {
    $order = '`Store Low Availability Products`';
} elseif ($order == 'critical') {
    $order = '`Store Critical Availability Products`';
} elseif ($order == 'new') {
    $order = '`Store New Products`';
} else {
    $order = 'S.`Store Key`';
}


$table = '`Store Dimension` S ';

$sql_totals
    = "select count(Distinct S.`Store Key`) as num from $table  $where  ";

$fields
    = " `Store Key`,`Store Code`,`Store Name`, (select count(*) from `Deal Campaign Dimension` where `Deal Campaign Status`='Active' and `Deal Campaign Store Key`=S.`Store Key` ) as campaigns
, (select count(*) from `Deal Dimension` where `Deal Status`='Active' and `Deal Store Key`=S.`Store Key` ) as deals
  ";

?>
