<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 23 May 2018 at 14:43:24 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/natural_language.php';
require_once 'utils/order_functions.php';




  $sql = sprintf("SELECT `Deal Key` FROM `Deal Dimension` where `Deal Status` not in ('Finished') ");
    if ($result = $db->query($sql)) {
        foreach ($result as $row) {
        

            $deal = get_object('Deal', $row['Deal Key']);



            $deal->update_status_from_dates(false);

            foreach ($deal->get_deal_components('objects', 'all') as $component) {


                $component->update_status_from_dates();
            }


        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


?>