<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 23 June 2016 at 19:58:30 BST, Heathrow Airport, UK
 Copyright (c) 2016, Inikoo

 Version 3

*/


$tab     = 'users.suppliers';
$ar_file = 'ar_users_tables.php';
$tipo    = 'suppliers';

$default = $user->get_tab_defaults($tab);


$table_views = array(
    'overview'   => array('label' => _('Overview')),
    'weblog'     => array('label' => _('Syslog')),
);

$table_filters = array(
    'handle' => array(
        'label' => _('Handle'),
        'title' => _('User handle')
    ),
    'name'   => array(
        'label' => _('Code'),
        'title' => _('Supplier code')
    ),

);

$parameters = array(
    'parent'     => 'account',
    'parent_key' => $state['parent_key'],


);


include('utils/get_table_html.php');



