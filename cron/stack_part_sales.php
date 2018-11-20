<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 21 November 2018 at 01:37:25 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/

require_once 'common.php';

$print_est = true;


$editor = array(


    'Author Type'  => '',
    'Author Key'   => '',
    'User Key'     => 0,
    'Date'         => gmdate('Y-m-d H:i:s'),
    'Subject'      => 'System',
    'Subject Key'  => 0,
    'Author Name'  => 'System (Stack part sales)',
    'Author Alias' => 'System (Stack part sales)',
    'v'            => 3


);

$intervals = array(
    'Total',
    'Year To Day',
    'Quarter To Day',
    'Month To Day',
    'Week To Day',
    'Today',
    '1 Year',
    '1 Month',
    '1 Week',
);

$sql = sprintf("SELECT count(*) AS num FROM `Stack Dimension`  where `Stack Operation`='part_sales'");
if ($result = $db->query($sql)) {
    if ($row = $result->fetch()) {
        $total = $row['num'];
    } else {
        $total = 0;
    }
} else {
    print_r($error_info = $db->errorInfo());
    exit;
}


$lap_time0 = date('U');
$contador  = 0;


$sql = sprintf(
    "SELECT `Stack Key`,`Stack Object Key` FROM `Stack Dimension`  where `Stack Operation`='part_sales' "
);

if ($result = $db->query($sql)) {
    foreach ($result as $row) {
        $part =  get_object('Part',$row['Stack Object Key']);

        if($part->id){

            $editor['Date'] = gmdate('Y-m-d H:i:s');
            $part->editor = $editor;

            foreach ($intervals as $interval) {
                $part->update_sales_from_invoices($interval, true, false);
            }



            $sql=sprintf('delete from `Stack Dimension`  where `Stack Key`=%d ',$row['Stack Key']);
            $db->exec($sql);
        }

        $contador++;
        $lap_time1 = date('U');

        if ($print_est) {
            print 'Pa '.percentage($contador, $total, 3)."  lap time ".sprintf("%.2f", ($lap_time1 - $lap_time0) / $contador)." EST  ".sprintf(
                    "%.1f", (($lap_time1 - $lap_time0) / $contador) * ($total - $contador) / 3600
                )."h  ($contador/$total) \r";
        }

    }

} else {
    print_r($error_info = $db->errorInfo());
    exit;
}


?>
