<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 10 October 2016 at 18:08:12 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2015, Inikoo

 Version 3.0
*/


$supplier = $state['_object'];

//print_r($supplier);

//$supplier->update_locations_with_errors();
$supplier->update_paid_ordered_parts();
//$supplier->update_supplier_parts();

$smarty->assign('supplier', $supplier);

include_once 'widgets/production_alerts.wget.php';

$html = '';


$html .= get_production_alerts($supplier, $db, $account, $user, $smarty);

$html .= '<div id="widget_details" class="hide" style="clear:both;font-size:90%;border-top:1px solid #ccc"><div>';


?>
