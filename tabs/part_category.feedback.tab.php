<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 14-09-2019 23:15:01 MYT, Kuala Lumpur Malaysia
 Copyright (c) 2019, Inikoo

 Version 3

*/

$tab     = 'part_category.feedback';
$ar_file = 'ar_inventory_tables.php';
$tipo    = 'feedback';

$default = $user->get_tab_defaults($tab);


$table_views = array(


);

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


$table_buttons = array();


include 'utils/get_table_html.php';



