<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 2 October 2015 at 12:17:15 BST, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


$tab     = 'reports';
$ar_file = 'ar_reports_tables.php';
$tipo    = 'reports';

$default = $user->get_tab_defaults($tab);


$table_views = array();

$table_filters = array(

    'name' => array(
        'label' => _('Name'),
        'title' => _('Name')
    ),

);

$parameters = array(
    'parent'     => '',
    'parent_key' => '',
);


include('utils/get_table_html.php');



