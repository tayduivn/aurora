<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 16 April 2018 at 21:33:30 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

include_once 'ar_web_common_logged_in.php';


$tipo = $_REQUEST['tipo'];

switch ($tipo) {

    case 'get_thanks_html':
        $data = prepare_values(
            $_REQUEST, array(
                         'device_prefix' => array(
                             'type'     => 'string',
                             'optional' => true
                         ),
                         'order_key' => array(
                             'type'     => 'string',
                             'optional' => true
                         )
                     )
        );

        get_thanks_html($data, $customer,$db);


        break;

   
 
   
}

function get_thanks_html($data, $customer,$db) {

    require_once 'external_libs/Smarty/Smarty.class.php';

    $template_suffix=$data['device_prefix'];

    $smarty               = new Smarty();
    $smarty->template_dir = 'templates';
    $smarty->compile_dir  = 'server_files/smarty/templates_c';
    $smarty->cache_dir    = 'server_files/smarty/cache';
    $smarty->config_dir   = 'server_files/smarty/configs';




    $order = get_object('Order', $data['order_key']);

    if(!$order->id or  $order->get('Order Customer Key')!=$customer->id ){
        $response = array(
            'state' => 200,
            'html'  => '',
        );
        echo json_encode($response);
        exit;
    }


    $website = get_object('Website', $_SESSION['website_key']);
    $theme   = $website->get('Website Theme');

    $store   = get_object('Store', $website->get('Website Store Key'));

    $webpage = $website->get_webpage('thanks.sys');

    $content = $webpage->get('Content Data');


    $block_found = false;
    $block_key   = false;
    foreach ($content['blocks'] as $_block_key => $_block) {
        if ($_block['type'] == 'thanks') {
            $block       = $_block;
            $block_key   = $_block_key;
            $block_found = true;
            break;
        }
    }

    if (!$block_found) {
        $response = array(
            'state' => 200,
            'html'  => '',
            'msg'   => 'no thanks in webpage'
        );
        echo json_encode($response);
        exit;
    }
    $smarty->assign('placed_order', $order);
    $smarty->assign('customer', $customer);
    $smarty->assign('website', $website);
    $smarty->assign('store', $store);

    $smarty->assign('key', $block_key);

    $smarty->assign('labels', $website->get('Localised Labels'));

    $smarty->assign('logged_in', true);
    $smarty->assign('order_key', $order->id);

    $placed_order = get_object('Order', $_REQUEST['order_key']);


    require_once 'utils/placed_order_functions.php';


    $smarty->assign('placed_order', $placed_order);



    $placeholders = array(
        '[Greetings]'     => $customer->get_greetings(),
        '[Customer Name]' => $customer->get('Name'),
        '[Name]'          => $customer->get('Customer Main Contact Name'),
        '[Name,Company]'  => preg_replace(
            '/^, /', '', $customer->get('Customer Main Contact Name').($customer->get('Customer Company Name') == '' ? '' : ', '.$customer->get('Customer Company Name'))
        ),
        '[Signature]'     => $webpage->get('Signature'),
        '[Order Number]'  => $order->get('Public ID'),
        '[Order Amount]'  => $order->get('To Pay'),
        '[Pay Info]'      => get_pay_info($order, $website, $smarty),
        '[Order]'         => $smarty->fetch($theme.'/placed_order.'.$theme.'.EcomB2B'.($template_suffix != '' ? '.'.$template_suffix : '').'.tpl'),
        '#order_number'   => $order->get('Public ID')


    );


    $block['text']=strtr($block['text'], $placeholders);


    if($template_suffix1=''){
        $block['text'] = str_replace('<br/>', '',  $block['text']);
        $block['text'] = str_replace('<br>', '',  $block['text']);
        $block['text'] = str_replace('<p></p>', '',  $block['text']);
    }
    $smarty->assign('data', $block);





    $response = array(
        'state' => 200,
        'html'  => $smarty->fetch('theme_1/blk.thanks.theme_1.EcomB2B'.($template_suffix != '' ? '.'.$template_suffix : '').'.tpl'),
    );


    echo json_encode($response);

}

?>