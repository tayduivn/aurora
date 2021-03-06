<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:  01 June 2020  15:42::17  +0800, Kuala Lumpur, Malaysia
 Copyright (c) 2020, Inikoo

 Version 3

*/
use ReallySimpleJWT\Token;
require_once '../vendor/autoload.php';
require __DIR__.'/keyring/au_deploy_conf.php';

require __DIR__.'/utils/web_common.php';
require __DIR__.'/utils/natural_language.php';
require __DIR__.'/utils/general_functions.php';
require __DIR__.'/utils/web_locale_functions.php';

session_start();
if (!get_logged_in()) {

    echo json_encode(
        [
            'state' => 400,
            'resp'  => 'log out'
        ]
    );
    exit;
}

require __DIR__.'/keyring/dns.php';
require __DIR__.'/utils/new_fork.php';

$db = new PDO(
    "mysql:host=$dns_host;port=$dns_port;dbname=$dns_db;charset=utf8mb4", $dns_user, $dns_pwd
);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


if (!empty($_REQUEST['webpage_key']) and !empty($_REQUEST['device'])) {
    try {
        new_housekeeping_fork(
            'au_web_analytics', array(
            'type'         => 'website_user_visit',
            'server_data'  => $_SERVER,
            'session_data' => $_SESSION,
            'webpage_key'  => $_REQUEST['webpage_key'],
            'device'       => $_REQUEST['device'],
            'datetime'     => gmdate('Y-m-d H:i:s')
        ), DNS_ACCOUNT_CODE
        );
    } catch (Exception $e) {
        Sentry\captureException($e);
    }
}

if(empty($_SESSION['UTK']['CUR']) or empty($_SESSION['UTK']['LOC'])    ){
    //this is a temporal stuff to regenerate old cookies
    regenerate_utk_cookie();
}

$locale = set_locate($_SESSION['UTK']['LOC']);


if (!empty($_REQUEST['store_type'])) {
    if ($_REQUEST['store_type'] == 'Dropshipping') {
        $sql  = "select `Customer Account Balance` from `Customer Dimension` where `Customer Key`=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(
            array(
                $_SESSION['customer_key']
            )
        );
        if ($row = $stmt->fetch()) {

            echo json_encode(
                array(
                    'state'            => 200,
                    'customer_balance' =>  money($row['Customer Account Balance'], $_SESSION['UTK']['CUR'])
                )
            );
        }

    } else {
        //todo make this more efficient e.g. dont use classes but direct sql
        require_once 'utils/public_object_functions.php';
        $customer = get_object('Customer', $_SESSION['customer_key']);
        $order         = get_object('Order', $customer->get_order_in_process_key());

        if (empty($_SESSION['website_key'])) {
            $redis = new Redis();
            $redis->connect(REDIS_HOST, REDIS_PORT);
            include_once(__DIR__.'/utils/find_website_key.include.php');
            $_SESSION['website_key']=get_website_key_from_domain($redis);
        }



        $website = get_object('Website', $_SESSION['website_key']);
        $labels  = $website->get('Localised Labels');
        if (!$order->id) {
            $total = 0;
            $label = (isset($labels['_total']) ? $labels['_total'] : _('Total'));
            $items = 0;
        } else {
            if (!empty($website->settings['Info Bar Basket Amount Type']) and $website->settings['Info Bar Basket Amount Type'] == 'items_net') {
                $total = $order->get('Items Net Amount');
                $label = (isset($labels['_items_net']) ? $labels['_items_net'] : _('Items Net'));
                $items = $order->get('Products');

            } else {
                $total = $order->get('Total');
                $label = (isset($labels['_total']) ? $labels['_total'] : _('Total'));
                $items = $order->get('Products');
            }
        }
        echo json_encode(
            array(
                'state' => 200,
                'total' => $total,
                'items' => $items,
                'label' => $label
            )
        );
    }

} else {

}


function regenerate_utk_cookie(){
    require_once '../vendor/autoload.php';

    require_once 'utils/public_object_functions.php';

    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    if (empty($_SESSION['website_key'])) {
        include_once(__DIR__.'/utils/find_website_key.include.php');
        $_SESSION['website_key']=get_website_key_from_domain($redis);
    }

    $website = get_object('Website', $_SESSION['website_key']);


    $_SESSION['UTK']['LOC']=$website->get('Website Locale');
    $_SESSION['UTK']['CUR']= $website->get('Currency Code');
    $token = Token::customPayload($_SESSION['UTK'], JWT_KEY);
    setcookie('UTK', $token, time() + 157680000,'/');


}
