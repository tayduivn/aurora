<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 6 December 2015 at 19:52:38 GMTT Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


switch ($parameters['parent']) {
    case 'year':
        $where = sprintf(
            " where  Year(`Timesheet Date`)=%d ", $parameters['parent_key']
        );
        break;
    case 'week':
        $where = sprintf(
            " where  yearweek(`Timesheet Date`,3)=%d ", $parameters['parent_key']
        );
        break;
    case 'month':
        $year  = substr($parameters['parent_key'], 0, 4);
        $month = substr($parameters['parent_key'], 4, 2);
        $where = sprintf(
            " where  month(`Timesheet Date`)=%d and Year(`Timesheet Date`)=%d ", $month, $year
        );
        break;
    case 'day':
        $year  = substr($parameters['parent_key'], 0, 4);
        $month = substr($parameters['parent_key'], 4, 2);
        $day   = substr($parameters['parent_key'], 6, 2);

        $where = sprintf(
            " where  `Timesheet Date`=%s ", prepare_mysql("$year-$month-$day")
        );
        break;
    default:
        exit('parent not supported '.$parameters['parent']);
        break;
}

$table = '  `Timesheet Dimension`  ';


switch ($parameters['group_by']) {
    case 'month':
        $group_by = ' group by  Month(`Timesheet Date`) ';
        $sql_totals
                  = "select count(distinct Month(`Timesheet Date`)) as num from $table  $where  ";

        break;
    case 'week':
        $group_by = ' group by  WEEK(`Timesheet Date`,3) ';
        $sql_totals
                  = "select count(distinct WEEK(`Timesheet Date`,1)) as num from $table  $where  ";

        break;
    case 'day':
        $group_by = ' group by  `Timesheet Date` ';
        $sql_totals
                  = "select count(distinct `Timesheet Date`) as num from $table  $where  ";

        break;
    default:
        exit('group not supported '.$parameters['group_by']);
        break;
}


$wheref = '';


$_order = $order;
$_dir   = $order_direction;


if ($order == 'alias') {
    $order = '`Staff Alias`';
} else {
    $order = '`Timesheet Date`';
}


$fields
    = "
sum(`Timesheet Paid Overtime`+`Timesheet Unpaid Overtime`+`Timesheet Working Time`)  worked_time,
sum(`Timesheet Paid Overtime`) paid_overtime,
sum(`Timesheet Unpaid Overtime`) unpaid_overtime,
sum(`Timesheet Working Time`) work_time,
sum(`Timesheet Breaks Time`) breaks,
month(`Timesheet Date`) month,
year(`Timesheet Date`) year,
yearweek(`Timesheet Date`,3) yearweek,
week(`Timesheet Date`,3) week,
adddate(`Timesheet Date`, INTERVAL -WEEKDAY(`Timesheet Date`) DAY)  week_starting,
`Timesheet Date`,
count(distinct `Timesheet Key`) as timesheets,
count(distinct `Timesheet Staff Key`) as employees,
count(distinct `Timesheet Date`) as days

";

?>
