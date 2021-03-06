<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 15 October 2015 at 12:10:57 BST, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/

$tab     = 'customer.marketing.products';
$ar_file = 'ar_customers_tables.php';
$tipo    = 'products';

$default = $user->get_tab_defaults($tab);

$table_views = array(
    'overview' => array(
        'label' => _('Overview')),
);

$table_filters = array(
    'code' => array(
        'label' => _('Code'),
        'title' => _('Product code')
    ),
    'name' => array(
        'label' => _('Name'),
        'title' => _('Product name')
    ),

);

$parameters = array(
    'parent'     => $state['object'],
    'parent_key' => $state['key'],

);

include('utils/get_table_html.php');


