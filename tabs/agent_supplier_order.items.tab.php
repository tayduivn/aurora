<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 20 August 2018 at 13:43:04 GMT+8, Legian, Bali, Kuala Lumpur
 Copyright (c) 2018, Inikoo

 Version 3

*/

$tab     = 'agent_supplier_order.items';
$ar_file = 'ar_agents_tables.php';
$tipo    = 'agent_supplier_order.items';

$default = $user->get_tab_defaults($tab);


$table_views = array(
    'overview' => array(
        'label' => _('Description'),
        'title' => _('Description')
    ),

);

$table_filters = array(
    'code' => array('label' => _('Code')),
    'name' => array('label' => _('Name')),

);

$parameters = array(
    'parent'     => $state['object'],
    'parent_key' => $state['key'],

);


$table_buttons = array();

/*
$table_buttons[]=array('icon'=>'stop', 'id'=>'all_available_items', 'class'=>'items_operation'.($state['_object']->get('Purchase Order State')!='InProcess'?' hide':''), 'title'=>_("All supplier's parts"), 'change_tab'=>'supplier.order.all_supplier_parts');



$table_buttons[]=array(
	'icon'=>'plus',
	'title'=>_('New item'),
	'id'=>'new_item',
	'class'=>'items_operation'.($state['_object']->get('Purchase Order State')!='InProcess'?' hide':''),
	'add_item'=>
	array(

		'field_label'=>_("Supplier's part").':',
		'metadata'=>base64_encode(json_encode(array(
					'scope'=>'supplier_part',
					'parent'=>$state['_object']->get('Purchase Order Parent'),
					'parent_key'=>$state['_object']->get('Purchase Order Parent Key'),
					'options'=>array()
				)))

	)

);
*/
$smarty->assign('table_buttons', $table_buttons);





$smarty->assign(
    'table_metadata', base64_encode(
        json_encode(
            array(
                'parent'     => $state['object'],
                'parent_key' => $state['key'],
                'field'      => 'Purchase Order Quantity'
            )
        )
    )
);
$smarty->assign('table_top_template', 'agent.order.edit.tpl');


include 'utils/get_table_html.php';


?>
