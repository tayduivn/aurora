<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: Sun 14 April  2019 22:03:41 MYT, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/

require_once 'common.php';

$print_est = false;


$editor = array(


    'Author Type'  => '',
    'Author Key'   => '',
    'User Key'     => 0,
    'Date'         => gmdate('Y-m-d H:i:s'),
    'Subject'      => 'System',
    'Subject Key'  => 0,
    'Author Name'  => 'System (Stack sent emails totals)',
    'Author Alias' => 'System (Stack sent emails totals)',
    'v'            => 3


);


$sql = sprintf("SELECT count(*) AS num FROM `Stack Dimension`  where `Stack Operation` in ('email_template_type_update_sent_emails_totals')");
if ($result = $db->query($sql)) {
    if ($row = $result->fetch()) {
        $total = $row['num'];
    } else {
        $total = 0;

    }
} else {
    print_r($error_info = $db->errorInfo());
    exit;
}


$lap_time0 = date('U');
$lap_time1= date('U');

$contador  = 0;


$sql = sprintf(
    "SELECT `Stack Key`,`Stack Object Key`,`Stack Operation` FROM `Stack Dimension`  where `Stack Operation` in ('email_template_type_update_sent_emails_totals') ORDER BY RAND()"
);

if ($result = $db->query($sql)) {
    foreach ($result as $row) {
        /**
         * @var $object \EmailCampaignType
         */
        $object =  get_object('email_template_type',$row['Stack Object Key']);

        if($object->id){


            $sql=sprintf('select `Stack Key`,`Stack Operation` from `Stack Dimension` where `Stack Key`=%d ',$row['Stack Key']);

            if ($result2=$db->query($sql)) {
                if ($row2 = $result2->fetch()) {

                    $sql=sprintf('delete from `Stack Dimension`  where `Stack Key`=%d ',$row['Stack Key']);
                    $db->exec($sql);

                    $editor['Date'] = gmdate('Y-m-d H:i:s');
                    $object->editor = $editor;

                    $object->update_sent_emails_totals();


                }
            }

        }else{
            $sql=sprintf('delete from `Stack Dimension`  where `Stack Key`=%d ',$row['Stack Key']);
            $db->exec($sql);
        }

        $contador++;
        $lap_time1 = date('U');

        if ($print_est) {
            print 'Pa '.percentage($contador, $total, 3)."  lap time ".sprintf("%.2f", ($lap_time1 - $lap_time0) / $contador)." EST  ".sprintf(
                    "%.1f", (($lap_time1 - $lap_time0) / $contador) * ($total - $contador) / 3600
                )."h  ($contador/$total) \r";
        }

    }

} else {
    print_r($error_info = $db->errorInfo());
    exit;
}
if($total>0){
    printf("%s: %s/%s %.2f min Emails template type sent emails totals\n",gmdate('Y-m-d H:i:s'),$contador,$total,($lap_time1 - $lap_time0)/60);
}


