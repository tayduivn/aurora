<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 1 December 2015 at 18:35:53 GMT, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/ar_common.php';
require_once 'utils/table_functions.php';
require_once 'utils/text_functions.php';
include_once 'search_functions.php';


if (!isset($_REQUEST['tipo'])) {
    $response = array(
        'state' => 405,
        'resp'  => 'Non acceptable request (t)'
    );
    echo json_encode($response);
    exit;
}

$tipo = $_REQUEST['tipo'];


switch ($tipo) {
    case 'search':

        $data = prepare_values(
            $_REQUEST, array(
                         'query' => array('type' => 'string'),
                         'state' => array('type' => 'json array')
                     )
        );

        $data['user'] = $user;

        if ($user->get('User Type') == 'Agent') {
            agent_search($db, $account, $user, $memcache_ip, $data);
        } else {


            // print_r($data);


            if ($data['state']['module'] == 'customers') {
                if ($data['state']['current_store']) {
                    $data['scope']     = 'store';
                    $data['scope_key'] = $data['state']['current_store'];
                } else {
                    $data['scope'] = 'stores';
                }
                search_customers($db, $account, $memcache_ip, $data);
            } elseif ($data['state']['module'] == 'orders') {
                if ($data['state']['current_store']) {
                    $data['scope']     = 'store';
                    $data['scope_key'] = $data['state']['current_store'];
                } else {
                    $data['scope'] = 'stores';
                }
                search_orders($db, $account, $memcache_ip, $data);
            } elseif ($data['state']['module'] == 'products') {
                if ($data['state']['current_store']) {
                    $data['scope']     = 'store';
                    $data['scope_key'] = $data['state']['current_store'];
                } else {
                    $data['scope'] = 'stores';
                }

                if (in_array(
                    $data['state']['section'], array(
                    'website',
                    'webpage',
                    '',
                    ''
                )
                )) {
                    search_webpages($db, $account, $memcache_ip, $data);
                } else {
                    search_products($db, $account, $data);
                }

                //  print_r($data['state']);


            } elseif ($data['state']['module'] == 'products_server') {

                $data['scope'] = 'stores';

                search_products($db, $account, $data);
            } elseif ($data['state']['module'] == 'inventory') {
                if ($data['state']['current_warehouse']) {
                    $data['scope']     = 'warehouse';
                    $data['scope_key'] = $data['state']['current_warehouse'];
                } else {
                    $data['scope'] = 'warehouses';
                }
                search_inventory($db, $account, $data);
            } elseif ($data['state']['module'] == 'hr') {
                search_hr($db, $account, $memcache_ip, $data);

            } elseif ($data['state']['module'] == 'suppliers') {
                search_suppliers($db, $account, $memcache_ip, $data);

            } elseif ($data['state']['module'] == 'delivery_notes') {
                if ($data['state']['current_store']) {
                    $data['scope']     = 'store';
                    $data['scope_key'] = $data['state']['current_store'];
                } else {
                    $data['scope'] = 'stores';
                }
                search_delivery_notes($db, $account, $memcache_ip, $data);
            } elseif ($data['state']['module'] == 'delivery_notes_server') {

                $data['scope'] = 'stores';

                search_delivery_notes($db, $account, $memcache_ip, $data);
            } elseif ($data['state']['module'] == 'orders_server') {
                $data['scope'] = 'stores';
                search_orders($db, $account, $memcache_ip, $data);
            } elseif ($data['state']['module'] == 'invoices_server') {
                $data['scope'] = 'stores';
                search_invoices($db, $account, $memcache_ip, $data);
            } elseif ($data['state']['module'] == 'invoices') {
                if ($data['state']['current_store']) {
                    $data['scope']     = 'store';
                    $data['scope_key'] = $data['state']['current_store'];
                } else {
                    $data['scope'] = 'stores';
                }
                search_invoices($db, $account, $memcache_ip, $data);
            } elseif ($data['state']['module'] == 'warehouses') {
                if ($data['state']['current_warehouse']) {
                    $data['scope']     = 'warehouse';
                    $data['scope_key'] = $data['state']['current_warehouse'];
                } else {
                    $data['scope'] = 'warehouses';
                }
                search_locations($db, $account, $data);
            }
        }
        break;


    default:
        $response = array(
            'state' => 405,
            'resp'  => 'Tab not found '.$tab
        );
        echo json_encode($response);
        exit;
        break;
}



?>
