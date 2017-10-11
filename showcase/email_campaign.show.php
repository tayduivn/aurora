<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 2 October 2017 at 18:28:52 GMT+8, Kuala umpur, Malaydia

 Copyright (c) 2015, Inikoo

 Version 3.0
*/

function get_email_campaign_showcase($data, $smarty, $user, $db) {

    if (!$data['_object']->id) {
        return "";
    }


    $email_campaign = $data['_object'];

    $smarty->assign('email_campaign',$email_campaign);

    $smarty->assign('store', get_object('store',$email_campaign->get('Store Key')));




    $smarty->assign(
        'object_data', base64_encode(
            json_encode(
                array(
                    'object' => $data['object'],
                    'key'    => $data['key'],

                    'tab' => $data['tab']
                )
            )
        )
    );


switch ($email_campaign->get('Email Campaign Type')){
    case 'AbandonedCart':
        return $smarty->fetch('showcase/abandoned_cart.tpl');
        break;

}





}


?>