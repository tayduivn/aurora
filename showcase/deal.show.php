<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 10 May 2016 at 18:54:24 GMT+8, Kuala Lumpur, Malaysia
 
 Copyright (c) 2016, Inikoo

 Version 3.0
*/

function get_deal_showcase($data, $smarty) {


    $deal = $data['_object'];
    if (!$deal->id) {
        return "";
    }


    $deal->update_deal_term_allowances();


    $campaign = get_object('DealCampaign', $deal->get('Deal Campaign Key'));

    switch ($campaign->get('Deal Campaign Code')){
        case 'CA':
            $category=get_object('Category',$deal->get('Deal Trigger Key'));
            $smarty->assign('category', $category);

            break;
    }



    $smarty->assign('campaign', $campaign);





    $smarty->assign('deal', $deal);

    return $smarty->fetch('showcase/deal.tpl');


}


?>