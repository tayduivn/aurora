<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 February 2017 at 18:38:11 GMT+8, Cyberjaya , Malydia
 Copyright (c) 2017, Inikoo

 Version 3.0
*/


$account->load_acc_data();



include_once 'widgets/warehouse_alerts.wget.php';

//$state['_object']->get_kpi('Month To Day');

$smarty->assign('warehouse',$state['_object']);

$html = $smarty->fetch('warehouse.kpi.tpl');


$html .= '<div class="widget_container">'.get_warehouse_alerts( $db, $account, $user, $smarty).'</div>';

$html .= '<div id="widget_details" class="hide" style="clear:both;font-size:90%;border-top:1px solid #ccc"><div>';


?>
