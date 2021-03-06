<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Refurbished: 22 November 2015 at 13:47:40 GMT Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


switch ($parameters['parent']) {
    case 'employee':
        $where = sprintf(
            " where  TRD.`Timesheet Record Staff Key`=%d ", $parameters['parent_key']
        );
        break;
    case 'timesheet':
    case 'Timesheet':
    $where = sprintf(
            " where  TRD.`Timesheet Record Timesheet Key`=%d ", $parameters['parent_key']
        );
        break;
    case 'account':
        $where = sprintf(" where true ");
        break;
    default:
        exit('parent not supported '.$parameters['parent']);
        break;
}


if (isset($parameters['period'])) {
    list($db_interval, $from, $to, $from_date_1yb, $to_1yb)
        = calculate_interval_dates(
        $db, $parameters['period'], $parameters['from'], $parameters['to']
    );

    $where_interval = prepare_mysql_dates(
        $from, $to, '`Timesheet Record Date`'
    );
    $where .= $where_interval['mysql'];
}


$wheref = '';
if ($parameters['f_field'] == 'alias' and $f_value != '') {
    $wheref .= " and  SD.`Staff Alias` like '".addslashes($f_value)."%'    ";
} elseif ($parameters['f_field'] == 'name' and $f_value != '') {
    $wheref = sprintf(
        '  and  SD.`Staff Name`  REGEXP "\\\\b%s" ', addslashes($f_value)
    );
}


$_order = $order;
$_dir   = $order_direction;


if ($order == 'alias') {
    $order = 'SD.`Staff Alias`';
} elseif ($order == 'name') {
    $order = 'SD.`Staff Name`';
} elseif ($order == 'time') {
    $order = '`Timesheet Record Date`';
} elseif ($order == 'date') {
    $order = '`Timesheet Record Date`';
} elseif ($order == 'ignored') {
    $order = '`Timesheet Record Ignored`';
} else {
    $order = '`Timesheet Record Key`';
}


$table
    = '  `Timesheet Record Dimension` as TRD 
            left join `Timesheet Dimension` TD on (TD.`Timesheet Key`=TRD.`Timesheet Record Timesheet Key`) 
            left join `Staff Dimension` SD on (SD.`Staff Key`=TRD.`Timesheet Record Staff Key`) 
            left join `Staff Dimension` A on (A.`Staff Key`=TRD.`Timesheet Authoriser Key`)  
            left join `Staff Dimension` I on (I.`Staff Key`=TRD.`Timesheet Remover Key`)    ';

$sql_totals = "select count(*) as num from $table  $where  ";

//print $sql_totals;
$fields
    = "
A.`Staff Alias` authoriser,`Timesheet Record Note`,
I.`Staff Alias` ignorer,

`Timesheet Record Ignored Due Missing End`,`Timesheet Timezone`,
`Timesheet Record Ignored`,`Timesheet Record Timesheet Key`,SD.`Staff ID`,`Timesheet Record Key`,`Timesheet Record Source`,SD.`Staff Alias`,`Timesheet Record Staff Key`,SD.`Staff Name`,`Timesheet Record Date`,`Timesheet Record Type`,`Timesheet Record Action Type`,`Timesheet Record Action Type`
";


