<?php

/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 28 September 2016 at 23:03:34 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

require_once 'common.php';

require_once 'class.Category.php';

$print_est = true;

update_parts_sales($db, $print_est);
//update_categories_sales($db, $print_est);

function update_parts_sales($db, $print_est) {

    $where = "where true";
    //$where=" where `Part Reference` like 'jbb-%' ";

    $sql = sprintf(
        "SELECT count(DISTINCT `Category Key`) AS num FROM `Category Dimension` %s AND  `Category Scope`='Product' ", $where
    );

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
        "SELECT `Category Key` FROM `Category Dimension` %s AND  `Category Scope`='Product' ", $where
    );

    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
            $category = new Category($row['Category Key']);

            $category->update_product_category_previous_quarters_data();

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
