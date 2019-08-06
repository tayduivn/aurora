<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 06-08-2019 13:28:26 MYT, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/


$parameters = array(
    'parent'     => $state['object'],
    'parent_key' => $state['key'],

);


$tab     = 'product.mailshots';
$ar_file = 'ar_mailshots_tables.php';
$tipo    = 'mailshots';


$default = $user->get_tab_defaults($tab);


$table_views = array(
    'overview' => array(
        'label' => _('Overview'),
        'title' => _('Overview')
    )

);

$table_filters = array(
    'name' => array(
        'label' => _('Name'),
        'title' => _('name')
    )

);


$table_buttons = array();




$table_buttons[] = array(
    'icon'  => 'bomb',
    'title' => _('Create mail bomb').' ('.$state['_object']->properties('spread_marketing_customers').' '._('customers').')',
    'id'    => 'new_spread_mailshot',
    'attr'  => array(
        'parent'     => 'Store',
        'parent_key' => $state['_object']->get('Store Key'),

    )

);


$table_buttons[] = array(
    'icon'  => 'scrubber',
    'title' => _('Create donut mailshot').' ('.$state['_object']->properties('donut_marketing_customers').' '._('customers').')',
    'id'    => 'new_donut_mailshot',
    'attr'  => array(
        'parent'     => 'Store',
        'parent_key' => $state['_object']->get('Store Key'),

    )

);

$table_buttons[] = array(
    'icon'  => 'bullseye-arrow',
    'title' => _('Create precision mailshot').' ('.$state['_object']->properties('targeted_marketing_customers').' '._('customers').')',
    'id'    => 'new_targeted_mailshot',
    'attr'  => array(
        'parent'     => 'Store',
        'parent_key' => $state['_object']->get('Store Key'),

    )

);


$smarty->assign(
    'js_code', 'js/injections/new_marketing_mailshot.'.(_DEVEL ? '' : 'min.').'js'
);



$smarty->assign('table_buttons', $table_buttons);


include 'utils/get_table_html.php';



