<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 April 2019 at 11:15:11 GMT+8, Kuala Lumpur Malysia
 Copyright (c) 2019, Inikoo

 Version 3

*/

require_once 'common.php';


$editor = array(
    'Author Name'  => '',
    'Author Alias' => '',
    'Author Type'  => '',
    'Author Key'   => '',
    'User Key'     => 0,
    'Date'         => gmdate('Y-m-d H:i:s')
);




$sql = sprintf('SELECT `Page Key` FROM `Page Store Dimension`  left join `Website Dimension` on (`Website Key`=`Webpage Website Key`)  where    `Website Key`=5  ');
if ($result=$db->query($sql)) {
    foreach ($result as $row) {

        $webpage = get_object('Webpage', $row['Page Key']);

        // print_r(json_decode($webpage->data['Webpage Navigation Data']));

        $webpage->update_navigation();

        //$webpage = get_object('Webpage', $row['Page Key']);

        //  print_r(json_decode($webpage->data['Webpage Navigation Data']));

print $webpage->get('Code')."\n";



    }
}else {
    print_r($error_info=$db->errorInfo());
    print "$sql\n";
    exit;
}



$sql = sprintf('SELECT `Page Key` FROM `Page Store Dimension` left join `Website Dimension` on (`Website Key`=`Webpage Website Key`)  where     `Website Key`=5  ');
if ($result=$db->query($sql)) {
    foreach ($result as $row) {

        $webpage = get_object('Webpage', $row['Page Key']);




        $webpage->reindex_items();

        }
}else {
    print_r($error_info=$db->errorInfo());
    print "$sql\n";
    exit;
}
//exit;





?>
