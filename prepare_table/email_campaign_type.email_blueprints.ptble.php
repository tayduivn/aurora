<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 July 2017 at 18:16:46 GMT+8, Cyberjaya , Malaysia
 Copyright (c) 2017, Inikoo

 Version 3

*/


$table = '`Email Blueprint Dimension` EB left join `Staff Dimension`  on (`Email Blueprint Created By`=`Staff Key`)';

$fields = "`Email Blueprint Name`,`Email Blueprint Created`,`Email Blueprint Key`,`Email Blueprint Image Key`,`Staff Alias`";

switch ($parameters['parent']) {

    case 'EmailCampaign':
    case 'EmailCampaignType':


        $email_campaign_type = get_object('EmailCampaignType', $parameters['parent_key']);

        $where = sprintf(
            " where  `Email Blueprint Role`=%s", prepare_mysql($email_campaign_type->get('Email Campaign Type Code'))
        );
        break;

        break;
    case 'Webpage':
        $where = sprintf(
            " where `Email Blueprint Scope`=%s and `Email Blueprint Scope Key`=%d", prepare_mysql($parameters['parent']), $parameters['parent_key']
        );
        break;

    case 'account':
        // $table='`Email Blueprint Dimension` I ';
        $where = ' where true';
        break;
    default:

        exit('email blueprint parent not done yet '.$parameters['parent']);

}


$wheref = '';


$_order = $order;
$_dir   = $order_direction;

if ($order == 'name') {
    $order = '`Email Blueprint Name`';
} elseif ($order == 'author') {
    $order = '`Staff Alias`';
} elseif ($order == 'data') {
    $order = '`Email Blueprint Created`';
} else {
    $order = '`Email Blueprint Key`';
}


$sql_totals = "select count(Distinct EB.`Email Blueprint Key`) as num from $table $where  ";
//print $sql_totals;

?>
