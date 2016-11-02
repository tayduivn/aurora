<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 3 January 2016 at 17:32:37 GMT+8, Penang, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

$tab     = 'payments';
$ar_file = 'ar_payments_tables.php';
$tipo    = 'payments';

$default = $user->get_tab_defaults($tab);


$table_views = array(
    'overview' => array(
        'label' => _('Overview'),
        'title' => _('Overview')
    ),

);

$table_filters = array(
    'reference' => array(
        'label' => _('Reference'),
        'title' => _('Reference')
    ),

);

$parameters = array(
    'parent'     => $state['parent'],
    'parent_key' => $state['parent_key'],

);


include('utils/get_table_html.php');


?>
