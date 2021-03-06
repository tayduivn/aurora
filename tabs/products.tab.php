<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 5 October 2015 at 17:21:31 BST, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


if($account->get('Account Warehouses')==0){

    $html='<div style="padding:20px">'.sprintf(_('Warehouse missing, set it up %s'),'<span class="marked_link" onClick="change_view(\'/warehouse/new\')" >'._('here').'</span>').'</div>';
    return;
}

if($account->get('Account Stores')==0){

    $html='<div style="padding:20px">'.sprintf(_('There are not stores, create one %s'),'<span class="marked_link" onClick="change_view(\'/store/new\')" >'._('here').'</span>').'</div>';
    return;
}

$tab     = 'store.products';
$ar_file = 'ar_products_tables.php';
$tipo    = 'products';

$default = $user->get_tab_defaults($tab);


$table_views = array(
    'overview'    => array('label' => _('Overview')),
    'performance' => array('label' => _('Performance')),
    'sales'       => array('label' => _('Sales')),
    'sales_y'     => array('label' => _('Invoiced amount (Yrs)')),
    'sales_q'     => array('label' => _('Invoiced amount (Qs)')),

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
    'parent'     => $state['parent'],
    'parent_key' => $state['parent_key'],

);


$table_buttons = array();

//$table_buttons[]=array('icon'=>'edit', 'title'=>_('Edit'),'id'=>'edit_table');

if ($state['parent'] == 'store') {
    $table_buttons[] = array(
        'icon'      => 'plus',
        'title'     => _('New product'),
        'reference' => "products/".$state['store']->id."/new"
    );
}
$smarty->assign('table_buttons', $table_buttons);


include 'utils/get_table_html.php';



