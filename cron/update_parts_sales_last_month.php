<?php

/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created:19 April 2016 at 11:32:28 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'class.Part.php';
require_once 'class.Product.php';
require_once 'class.Page.php';
require_once 'class.Supplier.php';
require_once 'class.Category.php';

$print_est = true;

update_parts_sales($db, $print_est);
//update_categories_sales($db, $print_est);

function update_parts_sales($db, $print_est) {

    $where = " where `Part SKU`=971 ";
    $where = "where true";

    $sql = sprintf("select count(*) as num from `Part Dimension` $where");
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
        "select `Part SKU` from `Part Dimension`  $where  order by `Part SKU`"
    );

    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
            $part = new Part($row['Part SKU']);


            $part->load_acc_data();

            $part->update_sales_from_invoices('Last Month');


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
}


?>
