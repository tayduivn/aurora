<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 22 June 2018 at 15:08:31 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/new_fork.php';



$time=date('H:i');

if($time=='00:00'  ){

    new_housekeeping_fork(
        'au_housekeeping', array(
        'type'                    => 'create_today_ISF',

    ), $account->get('Account Code')
    );

    new_housekeeping_fork(
        'au_housekeeping', array(
        'type'                    => 'update_deals_status_from_dates',

    ), $account->get('Account Code')
    );

    new_housekeeping_fork(
        'au_housekeeping', array(
        'type'                    => 'create_yesterday_timeseries',

    ), $account->get('Account Code')
    );

    $account->load_acc_data();
    $account->update_orders();

    $account->update(
        array(
            'Account Today Start Orders In Warehouse Number' => $account->get('Account Orders In Warehouse Number') + $account->get('Account Orders Packed Number') + $account->get('Account Orders Dispatch Approved Number')
        )

    );


    $sql = sprintf("SELECT `Store Key` FROM `Store Dimension`");
    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
            $store = get_object('Store', $row['Store Key']);

            $store->load_acc_data();

            $store->update_orders();


            $store->update(
                array('Store Today Start Orders In Warehouse Number' => $store->get('Store Orders In Warehouse Number') + $store->get('Store Orders Packed Number') + $store->get('Store Orders Dispatch Approved Number'))

            );

            $store->update_new_products();

        }
    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }

    $msg = new_housekeeping_fork(
        'au_asset_sales', array(
        'type'     => 'update_stores_sales_data',
        'interval' => 'Today',
        'mode'     => array(
            true,
            true
        )
    ), $account->get('Account Code')
    );

    $msg = new_housekeeping_fork(
        'au_asset_sales', array(
        'type'     => 'update_invoices_categories_sales_data',
        'interval' => 'Today',
        'mode'     => array(
            true,
            true
        )
    ), $account->get('Account Code')
    );


    $msg = new_housekeeping_fork(
        'au_asset_sales', array(
        'type'     => 'update_stores_sales_data',
        'interval' => 'Yesterday',
        'mode'     => array(
            true,
            true
        )
    ), $account->get('Account Code')
    );

    $msg = new_housekeeping_fork(
        'au_asset_sales', array(
        'type'     => 'update_invoices_categories_sales_data',
        'interval' => 'Yesterday',
        'mode'     => array(
            true,
            true
        )
    ), $account->get('Account Code')
    );


    $intervals = array(
        'Year To Day',
        'Quarter To Day',
        'Month To Day',
        'Week To Day',

    );
    foreach ($intervals as $interval) {


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_stores_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_invoices_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_products_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_parts_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_part_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_product_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_suppliers_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_supplier_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );


    }


    $intervals = array(

        '1 Year',
        '1 Quarter',
        '1 Month',
        '1 Week'
    );
    foreach ($intervals as $interval) {


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_stores_sales_data',
            'interval' => $interval,
            'mode'     => array(
                true,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_invoices_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                true,
                true
            )
        ), $account->get('Account Code')
        );


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_products_sales_data',
            'interval' => $interval,
            'mode'     => array(
                true,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_parts_sales_data',
            'interval' => $interval,
            'mode'     => array(
                true,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_part_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                true,
                true
            )
        ), $account->get('Account Code')
        );

        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_product_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                true,
                true
            )
        ), $account->get('Account Code')
        );


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_suppliers_data',
            'interval' => $interval,
            'mode'     => array(
                true,
                true
            )
        ), $account->get('Account Code')
        );


        $msg = new_housekeeping_fork(
            'au_asset_sales', array(
            'type'     => 'update_supplier_categories_sales_data',
            'interval' => $interval,
            'mode'     => array(
                false,
                true
            )
        ), $account->get('Account Code')
        );


    }


}
elseif($time=='22:00'){

    new_housekeeping_fork(
        'au_housekeeping', array(
        'type'                    => 'update_active_parts_commercial_value'
    ), $account->get('Account Code')
    );

}



if($time=='05:00'   ){

    new_housekeeping_fork(
        'au_housekeeping', array(
        'type'                    => 'update_parts_cost'
    ), $account->get('Account Code')
    );






}



if($time=='01:00'   ){


    $sql = sprintf(
        'SELECT `Part SKU` FROM `Part Dimension`  ORDER BY `Part SKU`  DESC '
    );

    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
            $part =  get_object('Part',$row['Part SKU']);
            $part->update_next_deliveries_data();

        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;

    }




}

send_periodic_email_mailshots($time,$db, $account);



function send_periodic_email_mailshots($time,$db, $account) {


    $sql = sprintf('select `Email Campaign Type Code`,`Email Campaign Type Metadata`,`Email Campaign Type Key` from `Email Campaign Type Dimension` where `Email Campaign Type Status`="Active" ');

    if ($result = $db->query($sql)) {
        foreach ($result as $row) {

            if ($row['Email Campaign Type Metadata'] != '') {
                $metadata = json_decode($row['Email Campaign Type Metadata'], true);

                if (isset($metadata['Schedule'])) {

                    date_default_timezone_set($metadata['Schedule']['Timezone']);



                    if ($metadata['Schedule']['Time'] == $time.':00') {
                        if (isset($metadata['Schedule']['Days'])) {
                            if ($metadata['Schedule']['Days'][iso_860_to_day_name(date('N'))] == 'Yes') {

                                new_housekeeping_fork(
                                    'au_housekeeping', array(
                                    'type'                    => 'create_and_send_mailshot',
                                    'email_template_type_key' => $row['Email Campaign Type Key'],

                                ), $account->get('Account Code')
                                );


                            }
                        }
                    }


                }


            }


        }
    } else {
        print_r($error_info = $db->errorInfo());
        print "$sql\n";
        exit;
    }


}


function iso_860_to_day_name($num) {
    switch ($num) {
        case 1:
            return 'Monday';
            break;
        case 2:
            return 'Tuesday';
            break;
        case 3:
            return 'Wednesday';
            break;
        case 4:
            return 'Thursday';
            break;
        case 5:
            return 'Friday';
            break;
        case 6:
            return 'Saturday';
            break;
        case 7:
            return 'Sunday';
            break;
        default:
            break;
    }
}


?>
