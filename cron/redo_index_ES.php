<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:  01 December 2019  17:34::02  +0100, Mijas Costa, Spain

 Copyright (c) 2019, Inikoo

 Version 3.0
*/

require 'common.php';

require 'vendor/autoload.php';

update_orders_index($db);
update_customers_index($db);


/**
 * @param $db \PDO
 */
function update_orders_index($db) {

    $object_name='Orders';
    $hosts     = get_ES_hosts();
    $print_est = true;

    $total = get_total_objects($db, $object_name);
    $lap_time0 = date('U');
    $contador  = 0;


    $sql = "select `Order Key` from `Order Dimension` order by `Order Key` desc ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $object = get_object('Order', $row['Order Key']);
        $object->index_elastic_search($hosts);
        $contador++;
        if ($print_est) {
            print_lap_times($object_name, $contador, $total, $lap_time0);
        }
    }
}


/**
 * @param $db \PDO
 */
function update_customers_index($db) {
    $object_name='Customers';
    $hosts     = get_ES_hosts();
    $print_est = true;

    $total = get_total_objects($db, $object_name);
    $lap_time0 = date('U');
    $contador  = 0;


    $sql = "select `Customer Key` from `Customer Dimension` order by `Customer Key` desc ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {

        $object = get_object('Customer', $row['Customer Key']);
        $object->index_elastic_search($hosts);

        $contador++;
        if ($print_est) {
            print_lap_times($object_name, $contador, $total, $lap_time0);
        }

    }

}




function print_lap_times($label = '', $contador, $total, $lap_time0) {
    $lap_time1 = date('U');
    print $label.'  '.percentage($contador, $total, 3)."  lap time ".sprintf("%.4f", ($lap_time1 - $lap_time0) / $contador)." EST  ".sprintf(
            "%.4f", (($lap_time1 - $lap_time0) / $contador) * ($total - $contador) / 60
        )."m  ($contador/$total) \r";

}


function get_total_objects($db, $object_name) {

    $total_objects = 0;

    switch ($object_name) {
        case 'Customers':
            $sql = "select count(*) as num from `Customer Dimension`";
            break;
        default:
            return $total_objects;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute();
    if ($row = $stmt->fetch()) {
        $total_objects = $row['num'];
    }

    return $total_objects;
}