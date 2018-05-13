<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 30 April 2018 at 10:50:33 BST, Sheffield. UK
 Copyright (c) 2018, Inikoo

 Version 3

*/

$ar_file = 'ar_suppliers_tables.php';


if ($state['_object']->get('State Index') == 100) {
    $tab  = 'supplier.delivery.costing';
    $tipo = 'delivery.costing';

    $table_views = array(
        'overview' => array('label' => _("Item's descriptions")),

    );
  //  $smarty->assign('aux_templates', array('supplier.delivery.costing.tpl'));
/*
    $smarty->assign(
        'js_code', array(
            'js/injections/supplier.delivery.costing.'.(_DEVEL ? '' : 'min.').'js',
        )
    );
*/

} else {

    exit;

    $tab  = 'supplier.delivery.items';
    $tipo = 'delivery.items';

    $table_views = array(
        'overview' => array('label' => _('Description')),

    );


}


$default = $user->get_tab_defaults($tab);


$table_filters = array(
    'code' => array('label' => _('Code')),
    'name' => array('label' => _('Name')),

);

$parameters = array(
    'parent'     => $state['object'],
    'parent_key' => $state['key'],

);


$table_buttons   = array();

$smarty->assign('table_buttons', $table_buttons);

$smarty->assign(
    'table_metadata', base64_encode(
        json_encode(
            array('parent'     => $state['object'],
                  'parent_key' => $state['key']
            )
        )
    )
);

$smarty->assign('delivery', $state['_object']);

$smarty->assign('currency', $state['_object']->get('Supplier Delivery Currency Code'));
$smarty->assign('currency_account', $account->get('Currency Code'));

$smarty->assign('currency_symbol', currency_symbol($account->get('Currency Code')));


$smarty->assign('table_top_template', 'supplier.delivery.costing.tpl');


include 'utils/get_table_html.php';


?>