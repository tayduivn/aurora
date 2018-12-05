<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 1 December 2018 at 12:44:40 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/


require_once 'common.php';
require_once 'utils/get_addressing.php';
require_once 'class.Country.php';


set_shipping_zones($db);
//test_shipping_zones($db);
//migrate_shipping_zones($db);


function test_shipping_zones($db) {


    $sql = sprintf('SELECT `Order Key` FROM `Order Dimension` where `Order Shipping Method`!="Set" and `Order For Collection`!="Yes"  and `Order Number Items`>0 and `Order Ship To Postal Code` like "J%%" ');

    $sql = sprintf('SELECT `Order Key` FROM `Order Dimension` where `Order Shipping Method`!="Set" and `Order For Collection`!="Yes"  and `Order Number Items`>0 and `Order Store Key` not in (10,14) and `Order Key`=1376 ');

    // $sql = sprintf('SELECT `Order Key` FROM `Order Dimension` where `Order Key`=2157388 ');
    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
            $order = get_object('order', $row['Order Key']);



            if ($order->id) {
                $store = get_object('Store', $order->get('Store Key'));

                if ($store->get('Store Version') == 1) {




                    if ($order->data['Order Number Items'] == 0) {
                      continue;
                    }


                    if ($order->data['Order For Collection'] == 'Yes') {
                        continue;
                    }

                    if ($order->data['Order Shipping Method'] == 'Set') {
                        continue;
                    }



                    $country= new Country('code',$order->data['Order Ship To Country Code']);


                    $_data = array(
                        'Store Key'  => $order->get('Order Store Key'),
                        'Order Data' => array(
                            'Order Items Net Amount'                      => $order->data['Order Items Net Amount'],
                            'Order Delivery Address Postal Code'          => $order->data['Order Ship To Postal Code'],
                            'Order Delivery Address Country 2 Alpha Code' => $country->get('Country 2 Alpha Code'),
                        )


                    );

                    include_once 'nano_services/shipping_for_order.ns.php';

                    $shipping_data = (new shipping_for_order($order->db))->get($_data);

                    print $order->id.' '.$country->get('Country 2 Alpha Code')."\tPC: ".$order->get('Order Ship To Postal Code')."\t ".$order->get('Order Items Net Amount')."\t";
                    print "P ".$shipping_data['price']." s:".$shipping_data['step']."\n";

                    //print_r($shipping_data);

                    

                } else {
                    include_once 'nano_services/migrate_order.ns.php';

                    $order = (new migrate_order($db))->migrate($order->id);
                    //$data_old_method = $order->get_shipping_to_delete();

                    $data_new_method = $order->get_shipping();
/*
                    if ($data_new_method[0] != $data_old_method[0]) {


                        print_r($order);


                        print_r($data_new_method);
                        print_r($data_old_method);
                        exit;
                    }
*/
                    print $order->get('Order Delivery Address Country 2 Alpha Code')."\tPC: ".$order->get('Order Delivery Address Postal Code')."\t ".$order->get('Order Items Net Amount')."\t";
                    print "P ".$data_new_method['0']."\n";

                }


            
            }


            // print_r($data_new_method);

            //    $data_old_method = $order->get_shipping_to_delete();
            /*
                        print_r($data_new_method);
                        print_r($data_old_method);
                        print_r($row['Order Delivery Address Country 2 Alpha Code']);
                        print '-------'."\n";
            */

            /*
                        if ($data_new_method[0] != $data_old_method[0]) {
            
                            //  print_r($row);
            
                            print_r($data_new_method);
                            print_r($data_old_method);
                            exit;
                        }
            
            */

        }
    }
}


function migrate_shipping_zones($db) {


    $sql = sprintf(
        'SELECT * FROM `Order Dimension` where `Order Store Key` not in (10,14) '
    );
    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
            $order = get_object('order', $row['Order Key']);


            $data_new_method = $order->get_shipping();

            $data_old_method = $order->get_shipping_to_delete();
            /*
                        print_r($data_new_method);
                        print_r($data_old_method);
                        print_r($row['Order Delivery Address Country 2 Alpha Code']);
                        print '-------'."\n";
            */


            if ($data_new_method[0] != $data_old_method[0]) {

                //  print_r($row);

                print_r($data_new_method);
                print_r($data_old_method);
                exit;
            }


        }
    }
}

function set_shipping_zones($db) {

    $account = get_object('Account', 1);


    $shipping_zones_data = get_shipping_zones_data($account->get('Code'));


    $sql = sprintf('truncate `Shipping Zone Schema Dimension`;truncate `Shipping Zone Schema Data`;truncate `Shipping Zone Dimension`;truncate `Shipping Zone Data`;');
    $db->exec($sql);

    $sql = sprintf(
        'SELECT `Store Key` FROM `Store Dimension`  '
    );
    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
            $store                = get_object('store', $row['Store Key']);
            $shipping_zone_schema = $store->create_shipping_zone_schema(
                array(
                    'Shipping Zone Schema Label'         => sprintf(_('%s shipping zones'), $store->get('Code')),
                    'Shipping Zone Schema Type'          => 'Current',
                    'Shipping Zone Schema Default Price' => json_encode(
                        array(
                            'type' => 'TBC'
                        )
                    )
                )
            );


            $store->fast_update_json_field('Store Properties', 'current_shipping_zone_schema', $shipping_zone_schema->id);


            if (isset($shipping_zones_data[$store->get('Code')])) {

                foreach (array_reverse($shipping_zones_data[$store->get('Code')]) as $_data) {
                    $shipping_zone_schema->create_shipping_zone($_data);
                }


            }


        }
    }
}


function get_shipping_zones_data($account_code) {

    $shipping_zones_data =
        array(
            'AWEU' => array(
                'SK' => array(
                    array(
                        'Shipping Zone Code'        => 'SK',
                        'Shipping Zone Name'        => 'Slovensko',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 150,
                                        'price' => 4.95
                                    ),
                                    array(
                                        'from'  => 150,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'SK'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zóna 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 250,
                                        'price' => 9.95
                                    ),
                                    array(
                                        'from'  => 250,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'CZ'
                                ),
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'HU'
                                ),
                                array(
                                    'country_code' => 'PL'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zóna 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 350,
                                        'price' => 14.95
                                    ),
                                    array(
                                        'from'  => 350,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'LU'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'HR'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'IT'
                                ),
                                array(
                                    'country_code' => 'SI'
                                ),
                                array(
                                    'country_code' => 'GB'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z3',
                        'Shipping Zone Name'        => 'Zóna 3',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 795,
                                        'price' => 29.95
                                    ),
                                    array(
                                        'from'  => 795,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BG'
                                ),
                                array(
                                    'country_code' => 'EE'
                                ),
                                array(
                                    'country_code' => 'FI'
                                ),
                                array(
                                    'country_code' => 'SE'
                                ),
                                array(
                                    'country_code' => 'IE'
                                ),
                                array(
                                    'country_code' => 'LT'
                                ),
                                array(
                                    'country_code' => 'LV'
                                ),
                                array(
                                    'country_code' => 'PT'
                                ),
                                array(
                                    'country_code' => 'RO'
                                ),
                                array(
                                    'country_code' => 'ES'
                                )
                            )
                        )
                    )
                ),
                'AT' => array(

                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 250,
                                        'price' => 9.95
                                    ),
                                    array(
                                        'from'  => 250,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'SK'
                                ),
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'CZ'
                                ),
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'HU'
                                ),
                                array(
                                    'country_code' => 'PL'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zone 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 350,
                                        'price' => 14.95
                                    ),
                                    array(
                                        'from'  => 350,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'LU'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'HR'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'IT'
                                ),
                                array(
                                    'country_code' => 'SI'
                                ),
                                array(
                                    'country_code' => 'GB'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z3',
                        'Shipping Zone Name'        => 'Zone 3',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 795,
                                        'price' => 29.95
                                    ),
                                    array(
                                        'from'  => 795,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BG'
                                ),
                                array(
                                    'country_code' => 'EE'
                                ),
                                array(
                                    'country_code' => 'FI'
                                ),
                                array(
                                    'country_code' => 'SE'
                                ),
                                array(
                                    'country_code' => 'IE'
                                ),
                                array(
                                    'country_code' => 'LT'
                                ),
                                array(
                                    'country_code' => 'LV'
                                ),
                                array(
                                    'country_code' => 'PT'
                                ),
                                array(
                                    'country_code' => 'RO'
                                ),
                                array(
                                    'country_code' => 'ES'
                                )
                            )
                        )
                    )
                ),
                'EU' => array(

                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 250,
                                        'price' => 9.95
                                    ),
                                    array(
                                        'from'  => 250,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'SK'
                                ),
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'CZ'
                                ),
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'HU'
                                ),
                                array(
                                    'country_code' => 'PL'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zone 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 350,
                                        'price' => 14.95
                                    ),
                                    array(
                                        'from'  => 350,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'LU'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'HR'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'IT'
                                ),
                                array(
                                    'country_code' => 'SI'
                                ),
                                array(
                                    'country_code' => 'GB'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z3',
                        'Shipping Zone Name'        => 'Zone 3',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 795,
                                        'price' => 29.95
                                    ),
                                    array(
                                        'from'  => 795,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BG'
                                ),
                                array(
                                    'country_code' => 'EE'
                                ),
                                array(
                                    'country_code' => 'FI'
                                ),
                                array(
                                    'country_code' => 'SE'
                                ),
                                array(
                                    'country_code' => 'IE'
                                ),
                                array(
                                    'country_code' => 'LT'
                                ),
                                array(
                                    'country_code' => 'LV'
                                ),
                                array(
                                    'country_code' => 'PT'
                                ),
                                array(
                                    'country_code' => 'RO'
                                ),
                                array(
                                    'country_code' => 'ES'
                                )
                            )
                        )
                    )
                ),
                'CZ' => array(
                    array(
                        'Shipping Zone Code'        => 'CZ',
                        'Shipping Zone Name'        => 'Czechia',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 3994.99,
                                        'price' => 245
                                    ),
                                    array(
                                        'from'  => 3994.99,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'CZ'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 6495,
                                        'price' => 290
                                    ),
                                    array(
                                        'from'  => 6495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'SK'
                                ),
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'HU'
                                ),
                                array(
                                    'country_code' => 'PL'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zone 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 8995,
                                        'price' => 390
                                    ),
                                    array(
                                        'from'  => 8995,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'LU'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'HR'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'IT'
                                ),
                                array(
                                    'country_code' => 'SI'
                                ),
                                array(
                                    'country_code' => 'GB'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z3',
                        'Shipping Zone Name'        => 'Zone 3',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 20495,
                                        'price' => 790
                                    ),
                                    array(
                                        'from'  => 20495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BG'
                                ),
                                array(
                                    'country_code' => 'EE'
                                ),
                                array(
                                    'country_code' => 'FI'
                                ),
                                array(
                                    'country_code' => 'SE'
                                ),
                                array(
                                    'country_code' => 'IE'
                                ),
                                array(
                                    'country_code' => 'LT'
                                ),
                                array(
                                    'country_code' => 'LV'
                                ),
                                array(
                                    'country_code' => 'PT'
                                ),
                                array(
                                    'country_code' => 'RO'
                                ),
                                array(
                                    'country_code' => 'ES'
                                )
                            )
                        )
                    )
                ),

            ),
            'AW'   => array(
                'UK' => array(
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zone 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 495,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code'          => 'GB',
                                    'included_postal_codes' => '/^((BT|IM|IV|KW|HS|ZE)\d+)|(AB(36|37|38|55|56)|FK(17|18|19|20|21)|PA[2-8][0-9]|PH(19|[2-5][0-9])|KA(27|28)|PO[3-4][0-9]|TR(21|22|23|24|25))\s.{3}$/',
                                    //

                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 7.5
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'GB',
                                    'excluded_postal_codes' => '/^(JE|GY)\d+\s.{3}$/',
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z3',
                        'Shipping Zone Name'        => 'Zone 3',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 250,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 250,
                                        'to'    => 495,
                                        'price' => 35
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'JE'
                                ),
                                array(
                                    'country_code' => 'GG'
                                ),
                                array(
                                    'country_code' => 'GB',
                                    'included_postal_codes' => '/^(JE|GY)\d+\s.{3}$/',

                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z4',
                        'Shipping Zone Name'        => 'Zone 4',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 10
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 495,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'IE'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z5',
                        'Shipping Zone Name'        => 'Zone 5',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 400,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 400,
                                        'to'    => 750,
                                        'price' => 35
                                    ),
                                    array(
                                        'from'  => 750,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'LX'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'CZ'
                                ),
                                array(
                                    'country_code' => 'SK'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z6',
                        'Shipping Zone Name'        => 'Zone 6',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 20
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 450,
                                        'price' => 40
                                    ),
                                    array(
                                        'from'  => 450,
                                        'to'    => 975,
                                        'price' => 60
                                    ),
                                    array(
                                        'from'  => 975,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'FI'
                                ),
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'IT'
                                ),
                                array(
                                    'country_code' => 'SE'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z7',
                        'Shipping Zone Name'        => 'Zone 7',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 450,
                                        'price' => 45
                                    ),
                                    array(
                                        'from'  => 450,
                                        'to'    => 975,
                                        'price' => 65
                                    ),
                                    array(
                                        'from'  => 975,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BG'
                                ),
                                array(
                                    'country_code' => 'EE'
                                ),
                                array(
                                    'country_code' => 'HU'
                                ),
                                array(
                                    'country_code' => 'LV'
                                ),
                                array(
                                    'country_code' => 'LT'
                                ),
                                array(
                                    'country_code' => 'PL'
                                ),
                                array(
                                    'country_code' => 'PT'
                                ),
                                array(
                                    'country_code' => 'RO'
                                ),
                                array(
                                    'country_code' => 'SI'
                                ),
                                array(
                                    'country_code' => 'ES'
                                )

                            )
                        )
                    ),


                ),
                'DE' => array(

                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 350,
                                        'price' => 14.95
                                    ),
                                    array(
                                        'from'  => 350,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'CZ'
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zone 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 495,
                                        'price' => 18.85
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'LX'
                                ),
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'FR'
                                )
                            )
                        )
                    )


                ),
                'FR' => array(

                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 495,
                                        'price' => 18.85
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'LX'
                                )
                            )
                        )
                    ),


                ),
                'IT' => array(

                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 495,
                                        'price' => 14.95
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'IT'
                                )
                            )
                        )
                    ),


                ),
                'PL' => array(

                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 1500,
                                        'price' => 65
                                    ),
                                    array(
                                        'from'  => 1500,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'PL'
                                )
                            )
                        )
                    ),


                ),
                'HA' => array(
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zone 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 495,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code'          => 'GB',
                                    'included_postal_codes' => '/^((BT|IM|IV|KW|HS|ZE)\d+)|(AB(36|37|38|55|56)|FK(17|18|19|20|21)|PA[2-8][0-9]|PH(19|[2-5][0-9])|KA(27|28)|PO[3-4][0-9]|TR(21|22|23|24|25))\s.{3}$/',
                                    //

                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 7.5
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'GB',
                                    'excluded_postal_codes' => '/^(JE|GY)\d+\s.{3}$/',
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z3',
                        'Shipping Zone Name'        => 'Zone 3',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 250,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 250,
                                        'to'    => 495,
                                        'price' => 35
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'JE'
                                ),
                                array(
                                    'country_code' => 'GG'
                                ),
                                array(
                                    'country_code' => 'GB',
                                    'included_postal_codes' => '/^(JE|GY)\d+\s.{3}$/',

                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z4',
                        'Shipping Zone Name'        => 'Zone 4',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 10
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 495,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'IE'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z5',
                        'Shipping Zone Name'        => 'Zone 5',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 400,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 400,
                                        'to'    => 750,
                                        'price' => 35
                                    ),
                                    array(
                                        'from'  => 750,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'LX'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'CZ'
                                ),
                                array(
                                    'country_code' => 'SK'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z6',
                        'Shipping Zone Name'        => 'Zone 6',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 20
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 450,
                                        'price' => 40
                                    ),
                                    array(
                                        'from'  => 450,
                                        'to'    => 975,
                                        'price' => 60
                                    ),
                                    array(
                                        'from'  => 975,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'FI'
                                ),
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'IT'
                                ),
                                array(
                                    'country_code' => 'SE'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z7',
                        'Shipping Zone Name'        => 'Zone 7',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 450,
                                        'price' => 45
                                    ),
                                    array(
                                        'from'  => 450,
                                        'to'    => 975,
                                        'price' => 65
                                    ),
                                    array(
                                        'from'  => 975,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BG'
                                ),
                                array(
                                    'country_code' => 'EE'
                                ),
                                array(
                                    'country_code' => 'HU'
                                ),
                                array(
                                    'country_code' => 'LV'
                                ),
                                array(
                                    'country_code' => 'LT'
                                ),
                                array(
                                    'country_code' => 'PL'
                                ),
                                array(
                                    'country_code' => 'PT'
                                ),
                                array(
                                    'country_code' => 'RO'
                                ),
                                array(
                                    'country_code' => 'SI'
                                ),
                                array(
                                    'country_code' => 'ES'
                                )

                            )
                        )
                    ),


                ),
                'AC' => array(
                    array(
                        'Shipping Zone Code'        => 'Z2',
                        'Shipping Zone Name'        => 'Zone 2',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 495,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code'          => 'GB',
                                    'included_postal_codes' => '/^((BT|IM|IV|KW|HS|ZE)\d+)|(AB(36|37|38|55|56)|FK(17|18|19|20|21)|PA[2-8][0-9]|PH(19|[2-5][0-9])|KA(27|28)|PO[3-4][0-9]|TR(21|22|23|24|25))\s.{3}$/',
                                    //

                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z1',
                        'Shipping Zone Name'        => 'Zone 1',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 7.5
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'GB',
                                    'excluded_postal_codes' => '/^(JE|GY)\d+\s.{3}$/',
                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z3',
                        'Shipping Zone Name'        => 'Zone 3',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 250,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 250,
                                        'to'    => 495,
                                        'price' => 35
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'JE'
                                ),
                                array(
                                    'country_code' => 'GG'
                                ),
                                array(
                                    'country_code' => 'GB',
                                    'included_postal_codes' => '/^(JE|GY)\d+\s.{3}$/',

                                )
                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z4',
                        'Shipping Zone Name'        => 'Zone 4',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 10
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 495,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 495,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'IE'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z5',
                        'Shipping Zone Name'        => 'Zone 5',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 100,
                                        'price' => 15
                                    ),
                                    array(
                                        'from'  => 100,
                                        'to'    => 400,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 400,
                                        'to'    => 750,
                                        'price' => 35
                                    ),
                                    array(
                                        'from'  => 750,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BE'
                                ),
                                array(
                                    'country_code' => 'FR'
                                ),
                                array(
                                    'country_code' => 'LX'
                                ),
                                array(
                                    'country_code' => 'NL'
                                ),
                                array(
                                    'country_code' => 'CZ'
                                ),
                                array(
                                    'country_code' => 'SK'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z6',
                        'Shipping Zone Name'        => 'Zone 6',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 20
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 450,
                                        'price' => 40
                                    ),
                                    array(
                                        'from'  => 450,
                                        'to'    => 975,
                                        'price' => 60
                                    ),
                                    array(
                                        'from'  => 975,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'AT'
                                ),
                                array(
                                    'country_code' => 'DK'
                                ),
                                array(
                                    'country_code' => 'FI'
                                ),
                                array(
                                    'country_code' => 'DE'
                                ),
                                array(
                                    'country_code' => 'IT'
                                ),
                                array(
                                    'country_code' => 'SE'
                                )

                            )
                        )
                    ),
                    array(
                        'Shipping Zone Code'        => 'Z7',
                        'Shipping Zone Name'        => 'Zone 7',
                        'Shipping Zone Price'       => json_encode(
                            array(
                                'type'  => 'Step Order Items Net Amount',
                                'steps' => array(
                                    array(
                                        'from'  => 0,
                                        'to'    => 175,
                                        'price' => 25
                                    ),
                                    array(
                                        'from'  => 175,
                                        'to'    => 450,
                                        'price' => 45
                                    ),
                                    array(
                                        'from'  => 450,
                                        'to'    => 975,
                                        'price' => 65
                                    ),
                                    array(
                                        'from'  => 975,
                                        'to'    => 'INF',
                                        'price' => 0
                                    )

                                )
                            )
                        ),
                        'Shipping Zone Territories' => json_encode(
                            array(
                                array(
                                    'country_code' => 'BG'
                                ),
                                array(
                                    'country_code' => 'EE'
                                ),
                                array(
                                    'country_code' => 'HU'
                                ),
                                array(
                                    'country_code' => 'LV'
                                ),
                                array(
                                    'country_code' => 'LT'
                                ),
                                array(
                                    'country_code' => 'PL'
                                ),
                                array(
                                    'country_code' => 'PT'
                                ),
                                array(
                                    'country_code' => 'RO'
                                ),
                                array(
                                    'country_code' => 'SI'
                                ),
                                array(
                                    'country_code' => 'ES'
                                )

                            )
                        )
                    ),


                ),
            )


        );

    return $shipping_zones_data[$account_code];

}


?>
