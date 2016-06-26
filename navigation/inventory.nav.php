<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 29 September 2015 13:01:56 BST, Sheffield, UK

 Copyright (c) 2015, Inikoo

 Version 3.0
*/


function get_inventory_navigation($data, $smarty, $user, $db, $account) {



	$block_view=$data['section'];

	switch ($data['parent']) {
	case 'account':

		break;
	default:
		break;
	}


	$left_buttons=array();



	$right_buttons=array();
	$sections=get_sections('inventory');

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;
	$title=_('Inventory').' ('._('Parts').')';

	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


function get_new_part_navigation($data, $smarty, $user, $db, $account) {






	$up_button=array('icon'=>'arrow-up', 'title'=>_("Inventory"), 'reference'=>'inventory');


	$left_buttons=array();
	$left_buttons[]=$up_button;



	$right_buttons=array();
	$sections=get_sections('inventory');
	$_section='inventory';
	if (isset($sections[$_section]) )$sections[$_section]['selected']=true;

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;
	$title=_('New part');

	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


function get_part_navigation($data, $smarty, $user, $db, $account) {





	$object=$data['_object'];

	$block_view=$data['section'];



	$left_buttons=array();


	if ($data['parent']) {

		switch ($data['parent']) {
		case 'account':
			$tab='inventory.parts';
			$_section='inventory';
			break;
		case 'category':
			$tab='category.parts';
			$_section='inventory';
			break;


		}


		if (isset($_SESSION['table_state'][$tab])) {
			$number_results=$_SESSION['table_state'][$tab]['nr'];
			$start_from=0;
			$order=$_SESSION['table_state'][$tab]['o'];
			$order_direction=($_SESSION['table_state'][$tab]['od']==1 ?'desc':'');
			$f_value=$_SESSION['table_state'][$tab]['f_value'];
			$parameters=$_SESSION['table_state'][$tab];
		}else {

			$default=$user->get_tab_defaults($tab);
			$number_results=$default['rpp'];
			$start_from=0;
			$order=$default['sort_key'];
			$order_direction=($default['sort_order']==1 ?'desc':'');
			$f_value='';
			$parameters=$default;
			$parameters['parent']=$data['parent'];
			$parameters['parent_key']=$data['parent_key'];
		}


		$extra_where=' and `Part Status`="In Use"';

		include_once 'prepare_table/'.$tab.'.ptble.php';

		$_order_field=$order;
		$order=preg_replace('/^.*\.`/', '', $order);
		$order=preg_replace('/^`/', '', $order);
		$order=preg_replace('/`$/', '', $order);
		$_order_field_value=$object->get($order);


		$prev_title='';
		$next_title='';
		$prev_key=0;
		$next_key=0;
		$sql=trim($sql_totals." $wheref");

		if ($result2=$db->query($sql)) {
			if ($row2 = $result2->fetch() and $row2['num']>1) {


				$sql=sprintf("select `Part Reference` object_name,P.`Part SKU` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND P.`Part SKU` < %d))  order by $_order_field desc , P.`Part SKU` desc limit 1",

					prepare_mysql($_order_field_value),
					prepare_mysql($_order_field_value),
					$object->id
				);

				if ($result=$db->query($sql)) {
					if ($row = $result->fetch()) {
						$prev_key=$row['object_key'];
						$prev_title=_("Product").' '.$row['object_name'].' ('.$row['object_key'].')';
					}
				}


				$sql=sprintf("select `Part Reference` object_name,P.`Part SKU` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND P.`Part SKU` > %d))  order by $_order_field   , P.`Part SKU`  limit 1",
					prepare_mysql($_order_field_value),
					prepare_mysql($_order_field_value),
					$object->id
				);

				if ($result=$db->query($sql)) {
					if ($row = $result->fetch()) {
						$next_key=$row['object_key'];
						$next_title=_("Product").' '.$row['object_name'].' ('.$row['object_key'].')';
					}
				}





				if ($order_direction=='desc') {
					$_tmp1=$prev_key;
					$_tmp2=$prev_title;
					$prev_key=$next_key;
					$prev_title=$next_title;
					$next_key=$_tmp1;
					$next_title=$_tmp2;
				}


				switch ($data['parent']) {
				case 'account':


					$up_button=array('icon'=>'arrow-up', 'title'=>_("Inventory"), 'reference'=>'inventory');

					if ($prev_key) {
						$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'part/'.$prev_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'');

					}
					$left_buttons[]=$up_button;


					if ($next_key) {
						$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'part/'.$next_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

					}

					break;

				case 'category':


					$up_button=array('icon'=>'arrow-up', 'title'=>_("Parts's categories"), 'reference'=>'inventory/category/'.$data['parent_key']);

					if ($prev_key) {
						$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'category/'.$data['parent_key'].'/part/'.$prev_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'');

					}
					$left_buttons[]=$up_button;


					if ($next_key) {
						$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title,  'reference'=>'category/'.$data['parent_key'].'/part/'.$next_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

					}

					break;
				}






			}



		}

	}

	else {
		$_section='inventory';

	}



	$right_buttons=array();
	//$right_buttons[]=array('icon'=>'edit','title'=>_('Edit store'),'reference'=>'store/'.$store->id.'/edit');
	$sections=get_sections('inventory');
	$_section='inventory';
	if (isset($sections[$_section]) )$sections[$_section]['selected']=true;


	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_('Part').' <span class="id Part_Reference">'.$object->get('Part Reference').'</span>',
		'search'=>array('show'=>true, 'placeholder'=>_('Search Inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');

	return $html;

}




function get_part_image_navigation($data, $smarty, $user, $db, $account) {





	$object=$data['_object'];

	$block_view=$data['section'];



	$left_buttons=array();


	if ($data['parent']) {

		switch ($data['parent']) {
		case 'part':
			$tab='part.images';
			$_section='inventory';
			break;



		}



		if (isset($_SESSION['table_state'][$tab])) {
			$number_results=$_SESSION['table_state'][$tab]['nr'];
			$start_from=0;
			$order=$_SESSION['table_state'][$tab]['o'];
			$order_direction=($_SESSION['table_state'][$tab]['od']==1 ?'desc':'');
			$f_value=$_SESSION['table_state'][$tab]['f_value'];
			$parameters=$_SESSION['table_state'][$tab];
		}else {

			$default=$user->get_tab_defaults($tab);
			$number_results=$default['rpp'];
			$start_from=0;
			$order=$default['sort_key'];
			$order_direction=($default['sort_order']==1 ?'desc':'');
			$f_value='';
			$parameters=$default;
			$parameters['parent']=$data['parent'];
			$parameters['parent_key']=$data['parent_key'];
		}
		include_once 'prepare_table/'.$tab.'.ptble.php';

		$_order_field=$order;
		$order=preg_replace('/^.*\.`/', '', $order);
		$order=preg_replace('/^`/', '', $order);
		$order=preg_replace('/`$/', '', $order);
		$_order_field_value=$object->get($order);


		$prev_title='';
		$next_title='';
		$prev_key=0;
		$next_key=0;
		$sql=trim($sql_totals." $wheref");

		if ($result2=$db->query($sql)) {
			if ($row2 = $result2->fetch() and $row2['num']>1) {


				$sql=sprintf("select `Part Reference` object_name,P.`Part SKU` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND P.`Part SKU` < %d))  order by $_order_field desc , P.`Part SKU` desc limit 1",

					prepare_mysql($_order_field_value),
					prepare_mysql($_order_field_value),
					$object->id
				);

				if ($result=$db->query($sql)) {
					if ($row = $result->fetch()) {
						$prev_key=$row['object_key'];
						$prev_title=_("Product").' '.$row['object_name'].' ('.$row['object_key'].')';
					}
				}


				$sql=sprintf("select `Part Reference` object_name,P.`Part SKU` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND P.`Part SKU` > %d))  order by $_order_field   , P.`Part SKU`  limit 1",
					prepare_mysql($_order_field_value),
					prepare_mysql($_order_field_value),
					$object->id
				);

				if ($result=$db->query($sql)) {
					if ($row = $result->fetch()) {
						$next_key=$row['object_key'];
						$next_title=_("Product").' '.$row['object_name'].' ('.$row['object_key'].')';
					}
				}





				if ($order_direction=='desc') {
					$_tmp1=$prev_key;
					$_tmp2=$prev_title;
					$prev_key=$next_key;
					$prev_title=$next_title;
					$next_key=$_tmp1;
					$next_title=$_tmp2;
				}


				switch ($data['parent']) {
				case 'account':


					$up_button=array('icon'=>'arrow-up', 'title'=>_("Inventory"), 'reference'=>'inventory');

					if ($prev_key) {
						$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'part/'.$prev_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'');

					}
					$left_buttons[]=$up_button;


					if ($next_key) {
						$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'part/'.$next_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

					}



				}






			}



		}

	}

	else {
		$_section='inventory';

	}



	$right_buttons=array();
	//$right_buttons[]=array('icon'=>'edit','title'=>_('Edit store'),'reference'=>'store/'.$store->id.'/edit');
	$sections=get_sections('inventory');
	$_section='inventory';
	if (isset($sections[$_section]) )$sections[$_section]['selected']=true;


	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_('Part').' <span class="id Part_Reference">'.$data['_parent']->get('Part Reference').'</span> (<span class="id Part_SKU">'.$data['_parent']->get('SKU').'</span>) '._('Image').' ('.$data['_object']->get('Subject Order').'/'.$data['_parent']->get_number_images().')',
		'search'=>array('show'=>true, 'placeholder'=>_('Search Inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');

	return $html;

}


function get_transactions_navigation($data, $smarty, $user, $db, $account) {



	$block_view=$data['section'];

	switch ($data['parent']) {
	case 'account':
		break;
	default:
		break;
	}


	$left_buttons=array();



	$right_buttons=array();
	$sections=get_sections('inventory', '');

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;


	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_('Stock movements'),
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


function get_categories_navigation($data, $smarty, $user, $db, $account) {




	require_once 'class.Store.php';

	switch ($data['parent']) {
	case '':

		break;
	default:

		break;
	}

	$block_view=$data['section'];



	$left_buttons=array();



	$right_buttons=array();

	// $right_buttons[]=array('icon'=>'edit', 'title'=>_('Edit'), 'url'=>"edit_customer_categories.php?store_id=");

	$sections=get_sections('inventory', '');
	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;

	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,
		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_("Parts's Categories"),
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory'))

	);
	$smarty->assign('_content', $_content);
	$html=$smarty->fetch('navigation.tpl');
	return $html;

}




function get_barcodes_navigation($data, $smarty, $user, $db, $account) {



	$block_view=$data['section'];

	switch ($data['parent']) {
	case 'account':

		break;
	default:
		break;
	}


	$left_buttons=array();



	$right_buttons=array();
	$sections=get_sections('inventory');

	if (isset($sections[$data['section']]) )$sections[$data['section']]['selected']=true;
	$title=_('Barcodes');

	$_content=array(

		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


function get_barcode_navigation($data, $smarty, $user, $db, $account) {





	$object=$data['_object'];

	$block_view=$data['section'];



	$left_buttons=array();


	if ($data['parent']) {

		switch ($data['parent']) {
		case 'account':
			$tab='inventory.barcodes';
			$_section='barcodes';
			break;



		}



		if (isset($_SESSION['table_state'][$tab])) {
			$number_results=$_SESSION['table_state'][$tab]['nr'];
			$start_from=0;
			$order=$_SESSION['table_state'][$tab]['o'];
			$order_direction=($_SESSION['table_state'][$tab]['od']==1 ?'desc':'');
			$f_value=$_SESSION['table_state'][$tab]['f_value'];
			$parameters=$_SESSION['table_state'][$tab];
		}else {

			$default=$user->get_tab_defaults($tab);
			$number_results=$default['rpp'];
			$start_from=0;
			$order=$default['sort_key'];
			$order_direction=($default['sort_order']==1 ?'desc':'');
			$f_value='';
			$parameters=$default;
			$parameters['parent']=$data['parent'];
			$parameters['parent_key']=$data['parent_key'];
		}

		include_once 'prepare_table/'.$tab.'.ptble.php';

		$_order_field=$order;
		$order=preg_replace('/^.*\.`/', '', $order);
		$order=preg_replace('/^`/', '', $order);
		$order=preg_replace('/`$/', '', $order);
		$_order_field_value=$object->get($order);


		$prev_title='';
		$next_title='';
		$prev_key=0;
		$next_key=0;
		$sql=trim($sql_totals." $wheref");

		if ($result2=$db->query($sql)) {
			if ($row2 = $result2->fetch() and $row2['num']>1) {


				$sql=sprintf("select `Barcode Number` object_name,B.`Barcode Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND B.`Barcode Key` < %d))  order by $_order_field desc , B.`Barcode Key` desc limit 1",

					prepare_mysql($_order_field_value),
					prepare_mysql($_order_field_value),
					$object->id
				);
				if ($result=$db->query($sql)) {
					if ($row = $result->fetch()) {
						$prev_key=$row['object_key'];
						$prev_title=_("Product").' '.$row['object_name'].' ('.$row['object_key'].')';
					}
				}


				$sql=sprintf("select `Barcode Number` object_name,B.`Barcode Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND B.`Barcode Key` > %d))  order by $_order_field   , B.`Barcode Key`  limit 1",
					prepare_mysql($_order_field_value),
					prepare_mysql($_order_field_value),
					$object->id
				);

				if ($result=$db->query($sql)) {
					if ($row = $result->fetch()) {
						$next_key=$row['object_key'];
						$next_title=_("Product").' '.$row['object_name'].' ('.$row['object_key'].')';
					}
				}





				if ($order_direction=='desc') {
					$_tmp1=$prev_key;
					$_tmp2=$prev_title;
					$prev_key=$next_key;
					$prev_title=$next_title;
					$next_key=$_tmp1;
					$next_title=$_tmp2;
				}


				switch ($data['parent']) {
				case 'account':


					$up_button=array('icon'=>'arrow-up', 'title'=>_("Barcodes"), 'reference'=>'inventory/barcodes');

					if ($prev_key) {
						$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'inventory/barcode/'.$prev_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'');

					}
					$left_buttons[]=$up_button;


					if ($next_key) {
						$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'inventory/barcode/'.$next_key);

					}else {
						$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

					}

					break;


				}






			}



		}

	}

	else {
		$_section='inventory';

	}



	$right_buttons=array();
	$sections=get_sections('inventory');
	if (isset($sections[$_section]) )$sections[$_section]['selected']=true;


	$title=_('Barcode').' <span class="id Barcode_Number">'.$object->get('Number').'</span>';

	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search Inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');

	return $html;

}


function get_deleted_barcode_navigation($data, $smarty, $user, $db, $account) {


	$_section='barcodes';
	$object=$data['_object'];
	$block_view=$data['section'];
	$left_buttons=array();

	$right_buttons=array();
	$sections=get_sections('inventory');
	if (isset($sections[$_section]) )$sections[$_section]['selected']=true;


	$title=_('Deleted barcode').' <span class="id Barcode_Number">'.$object->get('Deleted Number').'</span>';

	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,

		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search Inventory'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');

	return $html;

}


function get_parts_new_main_category_navigation($data, $smarty, $user, $db, $account) {


	$sections=get_sections('inventory', $data['parent_key']);
	$left_buttons=array();
	$left_buttons[]=array('icon'=>'arrow-up', 'title'=>_('Categories'), 'reference'=>'inventory/categories', 'parent'=>'');

	$right_buttons=array();

	$sections['categories']['selected']=true;



	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,
		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>_("New main parts's category"),
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventry'))

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('navigation.tpl');
	return $html;


}


function get_parts_category_navigation($data, $smarty, $user, $db, $account) {

	$category=$data['_object'];

	$left_buttons=array();
	$right_buttons=array();

	switch ($data['parent']) {
	case 'category':

		$parent_category=new Category($data['parent_key']);



		$up_button=array('icon'=>'arrow-up', 'title'=>_("Part's Categories"), 'reference'=>'inventory/category/'.$parent_category->id);

		if ($data['tab']=='category.subjects') {
			$tab='subject_categories';

		}else {

			$tab='category.categories';
		}
		$parent_categories=$parent_category->get('Category Position');
		break;
	case 'account':


		$up_button=array('icon'=>'arrow-up', 'title'=>_("Parts's Categories"), 'reference'=>'inventory/categories');
		$tab='parts.categories';
		$parent_categories='';
		break;

	default:

		break;
	}


	if (isset($_SESSION['table_state'][$tab])) {
		$number_results=$_SESSION['table_state'][$tab]['nr'];
		$start_from=0;
		$order=$_SESSION['table_state'][$tab]['o'];
		$order_direction=($_SESSION['table_state'][$tab]['od']==1 ?'desc':'');
		$f_value=$_SESSION['table_state'][$tab]['f_value'];
		$parameters=$_SESSION['table_state'][$tab];
	}else {

		$default=$user->get_tab_defaults($tab);
		$number_results=$default['rpp'];
		$start_from=0;
		$order=$default['sort_key'];
		$order_direction=($default['sort_order']==1 ?'desc':'');
		$f_value='';
		$parameters=$default;

	}
	$parameters['parent']=$data['parent'];
	$parameters['parent_key']=$data['parent_key'];
	include_once 'prepare_table/'.$tab.'.ptble.php';


	$_order_field=$order;
	$order=preg_replace('/^.*\.`/', '', $order);
	$order=preg_replace('/^`/', '', $order);
	$order=preg_replace('/`$/', '', $order);



	$_order_field_value=$category->get($order);
	$extra_field='';



	$prev_title='';
	$next_title='';
	$prev_key=0;
	$next_key=0;
	$prev_extra_field_value='';
	$next_extra_field_value='';


	$sql=trim($sql_totals." $wheref");
	//print $sql;

	if ($result2=$db->query($sql)) {
		if ($row2 = $result2->fetch() and $row2['num']>1) {


			$sql=sprintf("select C.`Category Label` object_name,C.`Category Key` as object_key %s from %s
	                and ($_order_field < %s OR ($_order_field = %s AND C.`Category Key` < %d))  order by $_order_field desc , C.`Category Key` desc limit 1",
				$extra_field,
				"$table $where $wheref",
				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$category->id
			);

			if ($result=$db->query($sql)) {
				if ($row = $result->fetch()) {
					$prev_key=$row['object_key'];
					$prev_title=_("Part").' '.$row['object_name'].' ('.$row['object_key'].')';
					if ($extra_field) {
						$prev_extra_field_value=$row['extra_field'];
					}
				}
			}else {
				print_r($error_info=$db->errorInfo());
				exit;
			}


			$sql=sprintf("select C.`Category Label` object_name,C.`Category Key` as object_key %s from %s
	                and ($_order_field  > %s OR ($_order_field  = %s AND C.`Category Key`> %d))  order by $_order_field   , C.`Category Key` limit 1",
				$extra_field,
				"$table $where $wheref",
				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$category->id
			);


			if ($result=$db->query($sql)) {
				if ($row = $result->fetch()) {
					$next_key=$row['object_key'];
					$next_title=_("Part").' '.$row['object_name'].' ('.$row['object_key'].')';
					if ($extra_field) {
						$next_extra_field_value=$row['extra_field'];
					}
				}
			}else {
				print_r($error_info=$db->errorInfo());
				exit;
			}





			if ($order_direction=='desc') {
				$_tmp1=$prev_key;
				$_tmp2=$prev_title;
				$prev_key=$next_key;
				$prev_title=$next_title;
				$next_key=$_tmp1;
				$next_title=$_tmp2;
				if ($extra_field) {
					$_tmp3=$prev_extra_field_value;
					$prev_extra_field_value=$next_extra_field_value;
					$next_extra_field_value=$_tmp3;
				}

			}









		}
	}else {
		print_r($error_info=$db->errorInfo());
		exit;
	}




	if ($prev_key) {
		$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'inventory/category/'.$parent_categories.$prev_key);

	}else {
		$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'');

	}
	$left_buttons[]=$up_button;


	if ($next_key) {
		$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'inventory/category/'.$parent_categories.$next_key);

	}else {
		$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

	}









	if ($account->get('Account Part Family Category Key')==$data['_object']->get('Category Root Key')) {
		$title='<span class="Category_Code id">'.$data['_object']->get('Code').'</span>';
	}else {
		$title=_('Category').' <span class="Category_Label">'.$data['_object']->get('Label').'</span> (<span class="Category_Code id">'.$data['_object']->get('Code').'</span>)';
	}





	$right_buttons[]=array('icon'=>'sticky-note', 'title'=>_('Sticky note'), 'id'=>'sticky_note_button', 'click'=>"show_sticky_note_edit_dialog('sticky_note_button')",  'class'=> ($category->get('Sticky Note')==''?'':'hide'));

	//$right_buttons[]=array('icon'=>'edit', 'title'=>_('Edit'), 'url'=>"edit_product_categories.php?store_id=".$data['store']->id);

	$sections=get_sections('inventory', $data['store']->id);
	$sections['categories']['selected']=true;

	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,
		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search inventory'))

	);
	$smarty->assign('_content', $_content);
	$html=$smarty->fetch('navigation.tpl');
	return $html;

}


?>
