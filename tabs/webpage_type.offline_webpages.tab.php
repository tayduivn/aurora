<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 18-06-2019 17:14:37 MYT Kuala Lumpur, Malaysia
 Copyright (c) 2019, Inikoo

 Version 3

*/


$tab     = 'webpage_type.offline_webpages';
$ar_file = 'ar_websites_tables.php';
$tipo    = 'offline_webpages';

$default = $user->get_tab_defaults($tab);


$table_views = array();

$table_filters = array(
    'code'  => array('label' => _('Code')),

);

$parameters = array(
    'parent'     => $state['object'],
    'parent_key' => $state['key'],
);


include('utils/get_table_html.php');



