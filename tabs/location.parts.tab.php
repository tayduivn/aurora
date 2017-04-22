<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 29 July 2016 at 15:51:13 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/


$tab     = 'location.parts';
$ar_file = 'ar_warehouse_tables.php';
$tipo    = 'parts';

$default = $user->get_tab_defaults($tab);


$table_views = array();

$table_filters = array(
    'reference' => array(
        'label' => _('Reference'),
        'title' => _('Part reference')
    ),

);


$parameters = array(
    'parent'     => $state['object'],
    'parent_key' => $state['key'],

);

$smarty->assign('table_top_template', 'location.parts.edit.tpl');


include 'utils/get_table_html.php';


?>
