<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 06-09-2019 19:28:27 MYT, Kuala Lumpur Malysia
 Copyright (c) 2019, Inikoo

 Version 3

*/

$tab     = 'supplier_part.feedback';
$ar_file = 'ar_suppliers_tables.php';
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



