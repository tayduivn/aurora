<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 10 July 2016 at 11:04:43 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

$ar_file='ar_suppliers_tables.php';


if ($state['_object']->get('State Index')>=40) {
	$tab='supplier.delivery.check_items';
	$tipo='delivery.checking_items';
}else {

	$tab='supplier.delivery.items';
	$tipo='delivery.items';

}


$default=$user->get_tab_defaults($tab);


$table_views=array(
	'overview'=>array('label'=>_('Description')),

);

$table_filters=array(
	'code'=>array('label'=>_('Code')),
	'name'=>array('label'=>_('Name')),

);

$parameters=array(
	'parent'=>$state['object'],
	'parent_key'=>$state['key'],

);



$table_buttons=array();
$table_buttons[]=array('icon'=>'stop', 'id'=>'all_available_items', 'class'=>'items_operation'.($state['_object']->get('Supplier Delivery State')!='In Process'?' hide':''), 'title'=>_("All supplier's parts"), 'change_tab'=>'supplier.order.all_supplier_parts');


$table_buttons[]=array(
	'icon'=>'plus',
	'title'=>_('New item'),
	'id'=>'new_item',
	'class'=>'items_operation'.($state['_object']->get('Supplier Delivery State')!='In Process'?' hide':''),
	'add_item'=>
	array(

		'field_label'=>_("Supplier's part").':',
		'metadata'=>base64_encode(json_encode(array(
					'scope'=>'supplier_part',
					'parent'=>$state['_object']->get('Supplier Delivery Parent'),
					'parent_key'=>$state['_object']->get('Supplier Delivery Parent Key'),
					'options'=>array()
				)))

	)

);
$smarty->assign('table_buttons', $table_buttons);

$smarty->assign('js_code', 'js/injections/supplier.order.'.(_DEVEL?'':'min.').'js');
$smarty->assign('table_metadata', base64_encode(json_encode(array('parent'=>$state['object'], 'parent_key'=>$state['key'])))  );



include 'utils/get_table_html.php';


?>
