<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:   08 November 2019  10:08::07  +0100, Mijas Costa, Spain
 Copyright (c) 2015, Inikoo

 Version 3

*/

$tab     = 'picker.delivery_notes';
$ar_file = 'ar_orders_tables.php';
$tipo    = 'delivery_notes';

$default = $user->get_tab_defaults($tab);

$table_views = array();

$table_filters = array();

$parameters = array(
    'parent'     => 'picker',
    'parent_key' => $state['key'],
);


include('utils/get_table_html.php');

