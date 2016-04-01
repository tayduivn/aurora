<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 29 September 2015 13:01:56 BST, Sheffield, UK

 Copyright (c) 2015, Inikoo

 Version 3.0
*/

include_once 'class.Warehouse.php';

function get_warehouses_navigation($data, $smarty, $user, $db, $account) {




	$block_view=$data['section'];




	$left_buttons=array();



	$right_buttons=array();
	$sections=array();

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;


	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_('Warehouses'),
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory all warehouses'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


function get_new_warehouse_navigation($data, $smarty, $user, $db, $account) {




	$block_view=$data['section'];




	$left_buttons=array();

	$left_buttons[]=array('icon'=>'arrow-up', 'title'=>_('Warehouses'), 'reference'=>'warehouses', 'parent'=>'');


	$right_buttons=array();
	$sections=array();

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;


	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_('New Warehouse'),
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory all warehouses'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


function get_warehouse_navigation($data, $smarty, $user, $db, $account) {




	$block_view=$data['section'];

	$warehouse=new Warehouse($data['key']);



	$left_buttons=array();
	$left_buttons[]=array('icon'=>'arrow-up', 'title'=>_('Warehouses'), 'reference'=>'warehouses', 'parent'=>'');



	$right_buttons=array();
	$sections=get_sections($data['module'], $warehouse->id);

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;


	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_('Warehouse').' <span  class="id Warehouse_Code" >'.$warehouse->get('Code').'</span>',
		'search'=>array('show'=>true, 'placeholder'=>_('Search locations'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


function get_locations_navigation($data, $smarty, $user, $db, $account) {




	$block_view=$data['section'];

	switch ($data['parent']) {
	case 'warehouse':
		$warehouse=new Warehouse($data['parent_key']);
		break;
	default:
		break;
	}


	$left_buttons=array();



	$right_buttons=array();
	$sections=get_sections($data['module'], $warehouse->id);

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;


	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_('Locations').' <span class="id">'.$warehouse->get('Warehouse Code').'</span>',
		'search'=>array('show'=>true, 'placeholder'=>_('Search suppliers'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}



?>
