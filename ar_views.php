<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 26 August 2015 23:49:27 GMT+8 Singapore

 Copyright (c) 2015, Inikoo

 Version 3.0
*/

require_once 'common.php';
require_once 'utils/ar_common.php';
require_once 'utils/object_functions.php';



$tipo=$_REQUEST['tipo'];


switch ($tipo) {
case 'views':

	require_once 'utils/parse_request.php';




	$data=prepare_values($_REQUEST, array(
			'request'=>array('type'=>'string'),
			'old_state'=>array('type'=>'json array'),
			'tab'=>array('type'=>'string', 'optional'=>true),
			'subtab'=>array('type'=>'string', 'optional'=>true),
			'otf'=>array('type'=>'string', 'optional'=>true),
			'metadata'=>array('type'=>'json array', 'optional'=>true),

		));


	if (isset($data['metadata']['help']) and $data['metadata']['help'] ) {
		get_help($data, $modules, $db);
		return;
	}


	if (isset($data['metadata']['reload']) and $data['metadata']['reload'] ) {
		$reload=true;
	}else {
		$reload=false;
	}

	$state=parse_request($data, $db, $modules, $account, $user);




	$state['current_store']=$_SESSION['current_store'];
	$state['current_website']=$_SESSION['current_website'];
	$state['current_warehouse']=$_SESSION['current_warehouse'];




	$store='';
	$website='';
	$warehouse='';



	switch ($state['parent']) {

	case 'store':
		include_once 'class.Store.php';
		$_parent=new Store($state['parent_key']);
		$state['current_store']=$_parent->id;
		$store=$_parent;
		break;
	case 'part':
		include_once 'class.Part.php';
		include_once 'class.Warehouse.php';

		$_parent=new Part($state['parent_key']);
		$warehouse=new Warehouse($state['current_warehouse']);

		break;
	case 'website':
		$_parent=new Site($state['parent_key']);
		$website=$_parent;
		$state['current_website']=$_parent->id;
		$website=$_parent;

		break;
	case 'warehouse':
		include_once 'class.Warehouse.php';
		$_parent=new Warehouse($state['parent_key']);
		$warehouse=$_parent;
		$state['current_warehouse']=$_parent->id;

		break;
	case 'category':
		include_once 'class.Category.php';
		include_once 'class.Store.php';
		$_parent=new Category($state['parent_key']);

		if ($_parent->get('Category Scope')=='Product' or $_parent->get('Category Scope')=='Customer') {
			$store=new Store($_parent->get('Store Key'));

		}

		break;

	default:
		$_parent=false;
	}
	$state['_parent']=$_parent;



	if ($state['object']!='') {




		$_object=get_object($state['object'], $state['key']);

		if (is_numeric($_object->get('Store Key'))) {
			include_once 'class.Store.php';
			$store=new Store($_object->get('Store Key'));
			$state['current_store']=$store->id;
		}
		if (is_numeric($_object->get('Warehouse Key'))) {
			include_once 'class.Warehouse.php';
			$warehouse=new Warehouse($_object->get('Warehouse Key'));
			$state['current_warehouse']=$warehouse->id;
		}


		if ($state['object']=='product' or $state['object']=='customer') {


			if ($state['parent']=='store' and $_object->get('Store Key')!=$state['parent_key']) {
				$state=array('old_state'=>$state, 'module'=>'utils', 'section'=>'not_found', 'tab'=>'not_found', 'subtab'=>'', 'parent'=>$state['object'], 'parent_key'=>'', 'object'=>'',
					'store'=>'',
					'website'=>'',
					'warehouse'=>'',
					'key'=>'',
					'request'=>$state['request']
				);

			}
		}


		if (!$_object->id  and $modules[$state['module']]['sections'][$state['section']]['type']=='object') {


			$state=array('old_state'=>$state, 'module'=>'utils', 'section'=>'not_found', 'tab'=>'not_found', 'subtab'=>'', 'parent'=>$state['object'], 'parent_key'=>'', 'object'=>'',
				'store'=>$store,
				'website'=>$website,
				'warehouse'=>$warehouse,
				'key'=>'',
				'request'=>$state['request']
			);


		}else {

			$state['_object']=$_object;

		}

	}



	if (is_object($_parent) and !$_parent->id) {
		$state=array('old_state'=>$state, 'module'=>'utils', 'section'=>'not_found', 'tab'=>'not_found', 'subtab'=>'', 'parent'=>$state['parent'], 'parent_key'=>'', 'object'=>'',
			'store'=>$store,
			'website'=>$website,
			'warehouse'=>$warehouse
		);
	}


	if ($state['module']=='hr') {
		if (!$user->can_view('staff') ) {
			$state=array('old_state'=>$state, 'module'=>'utils', 'section'=>'forbidden', 'tab'=>'forbidden', 'subtab'=>'', 'parent'=>$state['parent'], 'parent_key'=>$state['parent_key'], '_object'=>'', 'object'=>'', 'key'=>'',
				'store'=>$store,
				'website'=>$website,
				'warehouse'=>$warehouse);
		}
	}

	$state['store']=$store;
	$state['website']=$website;
	$state['warehouse']=$warehouse;



	$sql=sprintf('insert into `User System View Fact`  (`User Key`,`Date`,`Module`,`Section`,`Tab`,`Parent`,`Parent Key`,`Object`,`Object Key`)  values (%d,%s,%s,%s,%s,%s,%s,%s,%s)',
		$user->id,
		prepare_mysql(gmdate('Y-m-d H:i:s')),
		prepare_mysql($state['module']),
		prepare_mysql($state['section']),
		prepare_mysql(($state['subtab']!=''?$state['subtab']:$state['tab'])),
		prepare_mysql($state['parent']),
		prepare_mysql($state['parent_key']),
		prepare_mysql($state['object']),
		prepare_mysql($state['key'])

	);
	$db->exec($sql);

	$_SESSION['request']=$state['request'];


	if (isset($state['current_store'])) {
		$_SESSION['current_store']=$state['current_store'];
	}
	if (isset($state['current_website'])) {
		$_SESSION['current_website']=$state['current_website'];
	}
	if (isset($state['current_warehouse'])) {
		$_SESSION['current_warehouse']=$state['current_warehouse'];
	}

	$response=array('state'=>array());

	list($state, $response['view_position'])=get_view_position($state);


	if ($data['old_state']['module']!=$state['module']  or $reload ) {
		$response['menu']=get_menu($state, $user, $smarty);

	}


	if (
		$data['old_state']['module']!=$state['module'] or
		$data['old_state']['section']!=$state['section'] or
		$data['old_state']['parent_key']!=$state['parent_key'] or
		$data['old_state']['key']!=$state['key'] or  $reload

	) {
		$response['navigation']=get_navigation($user, $smarty, $state, $db, $account);

	}
	if ($reload) {
		$response['logout_label']=_('Logout');
	}


	//special dynamic tabs
	if ($state['section']=='timesheets') {

		if ($state['parent']=='day') {

			unset($modules[$state['module']]['sections'][$state['section']]['tabs']['timesheets.days']);
			unset($modules[$state['module']]['sections'][$state['section']]['tabs']['timesheets.weeks']);
			unset($modules[$state['module']]['sections'][$state['section']]['tabs']['timesheets.months']);

			if ($state['tab']=='timesheets.days' or $state['tab']=='timesheets.weeks' or $state['tab']=='timesheets.months' )
				$state['tab']='timesheets.employees';

		}elseif ($state['parent']=='week') {

			unset($modules[$state['module']]['sections'][$state['section']]['tabs']['timesheets.weeks']);
			unset($modules[$state['module']]['sections'][$state['section']]['tabs']['timesheets.months']);

			if ( $state['tab']=='timesheets.weeks' or $state['tab']=='timesheets.months' )
				$state['tab']='timesheets.days';

		}elseif ($state['parent']=='month') {

			unset($modules[$state['module']]['sections'][$state['section']]['tabs']['timesheets.months']);

			if ( $state['tab']=='timesheets.months' )
				$state['tab']='timesheets.weeks';

		}
	}

	list($state, $response['tabs'])=get_tabs($state, $modules, $user, $smarty);// todo only calculate when is subtabs in the section


	if ($state['object']!=''
		and ($modules[$state['module']]['sections'][$state['section']]['type']=='object'  or isset($modules[$state['module']]['sections'][$state['section']]['showcase'])  )   ) {
		$response['object_showcase']=get_object_showcase(
			(isset($modules[$state['module']]['sections'][$state['section']]['showcase'])?$modules[$state['module']]['sections'][$state['section']]['showcase']:$state['object']),
			$state, $smarty, $user, $db);
	}else {
		$response['object_showcase']='';
	}


	$response['tab']=get_tab($db, $smarty, $user, $account, $state['tab'], $state['subtab'], $state, $data['metadata']);

	unset($state['_object']);
	unset($state['_parent']);
	unset($state['store']);
	unset($state['website']);
	unset($state['warehouse']);
	$response['state']=$state;





	echo json_encode($response);

	break;
case 'tab':
	$data=prepare_values($_REQUEST, array(
			'tab'=>array('type'=>'string'),
			'subtab'=>array('type'=>'string'),
			'state'=>array('type'=>'json array'),
		));


	$response=array(
		'tab'=>get_tab($db, $smarty, $user, $account, $data['tab'], $data['subtab'], $data['state'])
	);




	echo json_encode($response);
	break;


default:
	$response=array('state'=>404, 'resp'=>'Operation not found 2');
	echo json_encode($response);

}

function get_tab($db, $smarty, $user, $account, $tab, $subtab, $state=false, $metadata=false) {


	$_tab=$tab;
	$_subtab=$subtab;

	$actual_tab=($subtab!=''?$subtab:$tab);
	$state['tab']=$actual_tab;

	$smarty->assign('data', $state);


	if (file_exists('tabs/'.$actual_tab . '.tab.php')) {
		include_once 'tabs/'.$actual_tab . '.tab.php';
	}else {
		$html='Tab Not found: >'.$actual_tab.'<';

	}


	if (is_array($state)) {


		$_SESSION['state'][$state['module']][$state['section']]['tab']=$_tab;
		if ($_subtab!='') {
			$_SESSION['tab_state'][$_tab]=$_subtab;
		}

	}
	return $html;

}



function get_object_showcase($showcase, $data, $smarty, $user, $db) {



	switch ($showcase) {

	case 'website':
	case 'dashboard':
		$html='';
		break;
	case 'store':
		include_once 'showcase/store.show.php';
		$html=get_store_showcase($data, $smarty, $user, $db);
		break;
	case 'products_special_categories':
		include_once 'showcase/products_special_categories.show.php';
		$html=get_products_special_categories_showcase($data, $smarty, $user, $db);
		break;
	case 'account':
		include_once 'showcase/account.show.php';
		$html=get_account_showcase($data, $smarty, $user, $db);
		break;
	case 'product':
		include_once 'showcase/product.show.php';
		$html=get_product_showcase($data, $smarty, $user, $db);
		break;
	case 'part':
		include_once 'showcase/part.show.php';
		$html=get_part_showcase($data, $smarty, $user, $db);
		break;
	case 'employee':
		include_once 'showcase/employee.show.php';
		$html=get_employee_showcase($data, $smarty, $user, $db);
		break;
	case 'contractor':
		include_once 'showcase/contractor.show.php';
		$html=get_contractor_showcase($data, $smarty, $user, $db);
		break;
	case 'customer':
		include_once 'showcase/customer.show.php';
		$html=get_customer_showcase($data, $smarty, $user, $db);
		break;
	case 'supplier':
		include_once 'showcase/supplier.show.php';
		$html=get_supplier_showcase($data, $smarty, $user, $db);
		break;
	case 'order':
		include_once 'showcase/order.show.php';
		$html=get_order_showcase($data, $smarty, $user, $db);
		break;
	case 'invoice':
		include_once 'showcase/invoice.show.php';
		$html=get_invoice_showcase($data, $smarty, $user, $db);
		break;
	case 'delivery_note':
		include_once 'showcase/delivery_note.show.php';
		$html=get_delivery_note_showcase($data, $smarty, $user, $db);
		break;
	case 'user':
		include_once 'showcase/user.show.php';
		$html=get_user_showcase($data, $smarty, $user, $db);
		break;
	case 'warehouse':
		include_once 'showcase/warehouse.show.php';
		$html=get_warehouse_showcase($data, $smarty, $user, $db);
		break;
	case 'timesheet':
		include_once 'showcase/timesheet.show.php';
		$html=get_timesheet_showcase($data, $smarty, $user, $db);
		break;
	case 'attachment':
		include_once 'showcase/attachment.show.php';
		$html=get_attachment_showcase($data, $smarty, $user, $db);
		break;
	case 'manufacture_task':
		include_once 'showcase/manufacture_task.show.php';
		$html=get_manufacture_task_showcase($data, $smarty, $user, $db);
		break;
	case 'category':

		if ($data['_object']->id==$data['store']->get('Store Family Category Key') or
			$data['_object']->id==$data['store']->get('Store Department Category Key')
		) {
			$html='';
		}elseif ($data['_object']->get('Root Key')==$data['store']->get('Store Family Category Key')) {
			include_once 'showcase/family.show.php';
			$html=get_family_showcase($data, $smarty, $user, $db);
		}elseif ($data['_object']->get('Root Key')==$data['store']->get('Store Department Category Key')) {
			include_once 'showcase/department.show.php';
			$html=get_department_showcase($data, $smarty, $user, $db);
		}else {

			include_once 'showcase/category.show.php';
			$html=get_category_showcase($data, $smarty, $user, $db);
		}
		break;
	default:
		$html=$data['object'].' -> '.$data['key'];
		break;
	}
	return $html;

}


function get_menu($data, $user, $smarty) {

	include_once 'navigation/menu.php';

	return $html;


}


function get_navigation($user, $smarty, $data, $db, $account) {


	switch ($data['module']) {

	case ('dashboard'):
		require_once 'navigation/dashboard.nav.php';
		return get_dashboard_navigation($data, $smarty, $user, $db, $account);
		break;
	case ('products_server'):
		require_once 'navigation/products.nav.php';
		switch ($data['section']) {
		case 'stores':
			return get_stores_navigation($data, $smarty, $user, $db , $account);
			break;

		case 'products':
			return get_products_all_stores_navigation($data, $smarty, $user, $db, $account);
			break;

		}
	case ('products'):
		require_once 'navigation/products.nav.php';
		switch ($data['section']) {

		case 'store':
			return get_store_navigation($data, $smarty, $user, $db, $account);
			break;
		case 'products':
			return get_products_navigation($data, $smarty, $user, $db, $account);
			break;
		case 'dashboard':
			return get_store_dashboard_navigation($data, $smarty, $user, $db, $account);
			break;
		case 'product':



			return get_product_navigation($data, $smarty, $user, $db, $account);
			break;

		case ('categories'):
			return get_products_categories_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('category'):
			return get_products_category_navigation($data, $smarty, $user, $db, $account);
			break;
		}
	case ('customers'):
		require_once 'navigation/customers.nav.php';
		switch ($data['section']) {

		case ('customer'):
			return get_customer_navigation($data, $smarty, $user, $db, $account);
			break;

		case ('customers'):
			return get_customers_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('categories'):

			return get_customers_categories_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('category'):

			return get_customers_category_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('lists'):
			return get_customers_lists_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('list'):
			return get_customers_list_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('dashboard'):
			return get_customers_dashboard_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('statistics'):

			return get_customers_statistics_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('pending_orders'):
			return get_customers_pending_orders_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('customer.new'):
			return get_new_customer_navigation($data, $smarty, $user, $db, $account, $account);
			break;
		}

		break;
	case ('customers_server'):
		require_once 'navigation/customers.nav.php';
		switch ($data['section']) {
		case ('customers'):
		case('pending_orders'):
			return get_customers_server_navigation($data, $smarty, $user, $db);
			break;
		}

		break;
	case ('orders_server'):
		require_once 'navigation/orders.nav.php';
		switch ($data['section']) {
		case ('orders'):
		case ('payments'):
			return get_orders_server_navigation($data);
			break;
		}

		break;
	case ('invoices_server'):
		require_once 'navigation/orders.nav.php';
		switch ($data['section']) {

		case ('invoices'):
		case ('payments'):
			return get_invoices_server_navigation($data);
			break;

		case ('categories'):
			return get_invoices_categories_server_navigation($data);
			break;
		case ('category'):
			return get_invoices_category_server_navigation($data);
			break;
		}



		break;
	case ('delivery_notes_server'):
		require_once 'navigation/orders.nav.php';
		switch ($data['section']) {
		case ('delivery_notes'):

			return get_delivery_notes_server_navigation($data);
			break;
		}

		break;

	case ('orders'):
		require_once 'navigation/orders.nav.php';
		switch ($data['section']) {
		case ('orders'):
		case ('payments'):
			return get_orders_navigation($data);
			break;
		case ('order'):
			return get_order_navigation($data);
			break;
		case ('delivery_note'):
			return get_delivery_note_navigation($data);
			break;
		case ('invoice'):
			return get_invoice_navigation($data);
			break;
		default:
			return 'View not found';

		}
		break;
	case ('invoices'):
		require_once 'navigation/orders.nav.php';
		switch ($data['section']) {

		case ('invoices'):
		case ('payments'):
			return get_invoices_navigation($data);
			break;

		case ('invoice'):
			return get_invoice_navigation($data);
			break;
		case ('delivery_note'):
			return get_delivery_note_navigation($data);
			break;
		case ('order'):
			return get_order_navigation($data);
			break;
		default:
			return 'View not found';

		}
		break;
	case ('delivery_notes'):
		require_once 'navigation/orders.nav.php';
		switch ($data['section']) {
		case ('delivery_notes'):
			return get_delivery_notes_navigation($data);
			break;
		case ('delivery_note'):
			return get_delivery_note_navigation($data);
			break;
		case ('invoice'):
			return get_invoice_navigation($data);
			break;
		case ('order'):
			return get_order_navigation($data);
			break;
		case ('pick_aid'):
			return get_pick_aid_navigation($data);
			break;
		case ('pack_aid'):
			return get_pack_aid_navigation($data);
			break;
		default:
			return 'View not found';

		}
		break;

	case ('websites'):

		require_once 'navigation/websites.nav.php';
		switch ($data['section']) {
		case ('websites'):

			return get_websites_navigation($data);
			break;
		case ('website'):


			return get_website_navigation($data);
			break;
		case ('page'):
			return get_page_navigation($data);
			break;
		case ('website.user'):
			return get_user_navigation($data);
			break;
		default:
			return 'View not found';

		}
		break;
	case ('marketing_server'):
		require_once 'navigation/marketing.nav.php';
		switch ($data['section']) {
		case ('marketing'):

			return get_marketing_server_navigation($data);
			break;
		}

		break;

	case ('marketing'):
		require_once 'navigation/marketing.nav.php';
		switch ($data['section']) {

		case ('deals'):
			return get_deals_navigation($data);
			break;

		case ('enewsletters'):
			return get_enewsletters_navigation($data);
			break;
		case ('mailshots'):

			return get_mailshots_navigation($data);
			break;
		case ('marketing_post'):

			return get_marketing_post_navigation($data);
			break;
		case ('lists'):
			return get_customers_lists_navigation($data);
			break;
		case ('list'):
			return get_customers_list_navigation($data);
			break;
		case ('dashboard'):
			return get_customers_dashboard_navigation($data);
			break;
		case ('statistics'):

			return get_customers_statistics_navigation($data);
			break;
		case ('pending_orders'):
			return get_customers_pending_orders_navigation($data);
			break;
		}



	case ('reports'):

		require_once 'navigation/reports.nav.php';
		switch ($data['section']) {
		case ('reports'):
			return get_reports_navigation($user, $smarty, $data);
			break;
		case ('performance'):
			return get_performance_navigation($user, $smarty, $data);
			break;
		case ('sales'):
			return get_sales_navigation($user, $smarty, $data);
			break;
		case ('tax'):
			return get_tax_navigation($user, $smarty, $data);
			break;
		case ('billingregion_taxcategory'):
			return get_georegion_taxcategory_navigation($user, $smarty, $data);
			break;
		case ('billingregion_taxcategory.invoices'):
			return get_invoices_georegion_taxcategory_navigation($user, $smarty, $data, 'invoices');
			break;
		case ('billingregion_taxcategory.refunds'):
			return get_invoices_georegion_taxcategory_navigation($user, $smarty, $data, 'refunds');
			break;
		}

	case ('production'):
		require_once 'navigation/production.nav.php';
		switch ($data['section']) {
		case ('dashboard'):
			return get_dashboard_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('manufacture_tasks'):
			return get_manufacture_tasks_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('manufacture_task.new'):
			return get_new_manufacture_task_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('operatives'):
			return get_operatives_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('batches'):
			return get_batches_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('manufacture_task'):
			return get_manufacture_task_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('operative'):
			return get_operative_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('batch'):
			return get_batch_navigation($data, $smarty, $user, $db, $account);
			break;

		}
		break;
	case ('suppliers'):
		require_once 'navigation/suppliers.nav.php';
		switch ($data['section']) {

		case ('supplier'):
			return get_supplier_navigation($data);
			break;

		case ('suppliers'):
			return get_suppliers_navigation($data);
			break;
		case ('categories'):

			return get_suppliers_categories_navigation($data);
			break;
		case ('category'):

			return get_suppliers_category_navigation($data);
			break;
		case ('lists'):
			return get_suppliers_lists_navigation($data);
			break;
		case ('list'):
			return get_suppliers_list_navigation($data);
			break;
		case ('dashboard'):
			return get_suppliers_dashboard_navigation($data);
			break;
		case ('supplier.new'):
			return get_new_supplier_navigation($data, $smarty, $user, $db, $account);
			break;
		}

		break;
	case ('inventory_server'):
		require_once 'navigation/inventory.nav.php';
		switch ($data['section']) {
		case ('inventory'):
			return get_inventory_all_warehouses_navigation($data, $smarty, $user, $db, $account);
			break;


		}
	case ('inventory'):
		require_once 'navigation/inventory.nav.php';
		switch ($data['section']) {


		case ('inventory'):
			return get_inventory_navigation($data, $smarty, $user, $db, $account);
			break;

		case ('part'):
			return get_part_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('part.new'):
			return get_new_part_navigation($data, $smarty, $user, $db, $account, $account);
			break;
		case ('part.image'):
			return get_part_image_navigation($data, $smarty, $user, $db, $account);
			break;

		case ('transactions'):
			return get_transactions_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('stock_history'):
			return get_stock_history_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('categories'):
			return get_categories_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('category'):
			return get_category_navigation($data, $smarty, $user, $db, $account);
			break;
		}

		break;
	case ('warehouses'):
	case ('warehouses_server'):
		require_once 'navigation/warehouses.nav.php';
		switch ($data['section']) {

		case ('warehouses'):
			return get_warehouses_navigation($data);
			break;
		case ('warehouse'):
			return get_warehouse_navigation($data);
			break;

		case ('locations'):
			return get_locations_navigation($data);
			break;

		}

		break;

	case ('hr'):
		require_once 'navigation/hr.nav.php';

		switch ($data['section']) {

		case ('employees'):
		case ('new_timesheet_record'):

			return get_employees_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('contractors'):
			return get_contractors_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('organization'):
			return get_organization_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('employee'):

			return get_employee_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('employee.new'):
			return get_new_employee_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('contractor'):
			return get_contractor_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('contractor.new'):
			return get_new_contractor_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('timesheet'):
			return get_timesheet_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('timesheets'):
			return get_timesheets_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('employee.attachment.new'):
			return get_new_employee_attachment_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('employee.user.new'):
			return get_new_employee_user_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('employee.attachment'):
			return get_employee_attachment_navigation($data, $smarty, $user, $db, $account);
			break;
		}

		break;



	case ('utils'):
		require_once 'navigation/utils.nav.php';
		switch ($data['section']) {
		case ('forbidden'):
		case ('not_found'):
			return get_utils_navigation($data);
			break;
		case ('fire'):
			return get_fire_navigation($data);
			break;
		}

		break;
	case ('profile'):
		require_once 'navigation/account.nav.php';
		return  get_profile_navigation($data, $smarty, $user, $db, $account);
		break;

	case ('payments'):
		require_once 'navigation/payments.nav.php';
		switch ($data['section']) {
		case ('payment_service_provider'):
			return get_payment_service_provider_navigation($data, $user, $smarty);
			break;
		case ('payment_service_providers'):
			return get_payment_service_providers_navigation($data, $user, $smarty);
			break;
		case ('payment_account'):
			return get_payment_account_navigation($data, $user, $smarty);
			break;
		case ('payment_accounts'):
			return get_payment_accounts_navigation($data, $user, $smarty);
			break;
		case ('payment'):
			return get_payment_navigation($data, $user, $smarty);
			break;
		case ('payments'):
			return get_payments_navigation($data, $user, $smarty);
			break;
		}
		break;
	case ('account'):

		require_once 'navigation/account.nav.php';

		switch ($data['section']) {
		case ('account'):
			return get_account_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('users'):
			return get_users_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('data_sets'):
			return get_data_sets_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('timeseries'):
			return get_timeseries_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('timeserie'):
			return get_timeserie_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('images'):
			return get_images_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('attachments'):
			return get_attachments_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('osf'):
			return get_osf_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('isf'):
			return get_isf_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('orders_index'):
			return get_orders_index_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('staff'):
			return get_staff_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('suppliers'):
			return get_suppliers_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('warehouse'):
			return get_warehouse_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('root'):
			return get_root_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('staff.user'):
			return get_staff_user_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('suppliers.user'):
			return get_supplierss_user_navigation($data, $smarty, $user, $db, $account);
			break;

		case ('warehouse.user'):
			return get_warehouse_user_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('root.user'):
			return get_root_user_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('settings'):
			return get_settings_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('staff.user.api_key') :
			return get_api_key_navigation($data, $smarty, $user, $db, $account);
			break;
		case ('staff.user.api_key.new') :
			return get_new_api_key_navigation($data, $smarty, $user, $db, $account);
			break;



		}



		break;
	case ('settings'):
		require_once 'navigation/account.nav.php';
		return get_settings_navigation($data);
		break;
	default:
		return 'Module not found';
	}

}




function get_tabs($data, $modules, $user, $smarty) {



	if (isset($modules[$data['module']]['sections'][$data['section']]['tabs'])) {
		$tabs=$modules[$data['module']]['sections'][$data['section']]['tabs'];
	}else {
		$tabs=array();
	}



	if (isset($modules[$data['module']]['sections'][$data['section']]['tabs'][$data['tab']] ['subtabs'])) {

		$subtabs=$modules[$data['module']]['sections'][$data['section']]['tabs'][$data['tab']]['subtabs'];
	}else {
		$subtabs=array();
	}


	if (isset($tabs[$data['tab']]) ) {
		$tabs[$data['tab']]['selected']=true;
	}


	if (isset($subtabs[$data['subtab']]) )$subtabs[$data['subtab']]['selected']=true;

	$_content=array(
		'tabs'=>$tabs,
		'subtabs'=>$subtabs



	);

	if ($data['section']=='category') {

		if ($data['_object']->get('Category Scope')=='Product') {
			if ($data['_object']->get('Category Subject')=='Product') {
				$_content['tabs']['category.subjects']['label']=_('Products');


				if ($data['_object']->get('Root Key')==$data['store']->get('Store Family Category Key')) {
					$_content['tabs']['category.categories']['label']=_('Families');
				}

			}else {


				if ($data['_object']->get('Root Key')==$data['store']->get('Store Department Category Key')) {
					$_content['tabs']['category.subjects']['label']=_('Families');
					$_content['tabs']['category.categories']['label']=_('Departments');
				}elseif ($data['_object']->get('Root Key')==$data['store']->get('Store Family Category Key')) {
					$_content['tabs']['category.categories']['label']=_('Families');
				}else {

					$_content['tabs']['category.subjects']['label']=_('Categories');
				}

			}
		}



		if ($data['_object']->get('Category Branch Type')=='Head') {
			unset($_content['tabs']['category.categories']);
			if ($data['tab']=='category.categories') {
				$_content['tabs']['category.subjects']['selected']=true;
				$data['tab']='category.subjects';
			}

		}else {
			unset($_content['tabs']['category.subjects']);
			if ($data['tab']=='category.subjects') {
				$_content['tabs']['category.categories']['selected']=true;
				$data['tab']='category.categories';
			}
		}

		//print_r($data['_object']);
		//print_r($_content);


	}

	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('tabs.tpl');

	return array($data, $html);
}


function get_view_position($state) {
	global $user, $smarty, $account;


	$branch=array();
	//$branch=array(array('label'=>'<span >'._('Home').'</span>', 'icon'=>'home', 'reference'=>'/dashboard'));
	//print_r($state);
	switch ($state['module']) {
	case 'dashboard':


		$branch=array(array('label'=>'<span >'._('Dashboard').'</span>', 'icon'=>'dashboard', 'reference'=>'/dashboard'));

		break;

	case 'products_server':
		if ($state['section']=='stores') {
			$branch[]=array('label'=>_('Stores'), 'icon'=>'shopping-bag', 'reference'=>'');

		}elseif ($state['section']=='products') {

			$branch[]=array('label'=>_('Products (All stores)'), 'icon'=>'cube', 'reference'=>'');
		}


		break;

	case 'products':


		if ($state['section']=='store') {
			$branch[]=array('label'=>_('Store').' <span class="id">'.$state['_object']->get('Store Code').'</span>', 'icon'=>'shopping-bag', 'reference'=>'store/'.$state['_object']->id);
			$state['current_store']=$state['_object']->id;

		}elseif ($state['section']=='dashboard') {
			$branch[]=array('label'=>_("Store's dashboard").' <span class="id">'.$state['_object']->get('Store Code').'</span>', 'icon'=>'', 'reference'=>'store/'.$state['_object']->id);
			$state['current_store']=$state['_object']->id;

		}elseif ($state['section']=='product') {

			if ($state['parent']=='store') {
				if ( $user->get_number_stores()>1) {


					$branch[]=array('label'=>_('(All stores)'), 'icon'=>'', 'reference'=>'products/all');
					$branch[]=array('label'=>_('Products').' <span class="id">'.$state['store']->get('Code').'</span>', 'icon'=>'cube', 'reference'=>'products/'.$state['store']->id);


				}
			}elseif ($state['parent']=='order') {
				$order=new Order($state['parent_key']);
				$store=new Store($order->get('Order Store Key'));
				$branch=array(array('label'=>_('Home'), 'icon'=>'home', 'reference'=>''));

				if ( $user->get_number_stores()>1) {
					$branch[]=array('label'=>_('Orders').' ('._('All stores').')', 'icon'=>'bars', 'reference'=>'orders/all');
				}

				$branch[]=array('label'=>_('Orders').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'orders/'.$store->id);

				$branch[]=array('label'=>_('Order').' '.$order->get('Order Public ID'), 'icon'=>'shopping-cart', 'reference'=>'orders/'.$store->id.'/'.$state['parent_key']);


			}
			$state['current_store']=$state['store']->id;
			$_ref=$state['parent'].'/'.$state['parent_key'].'/product/'.$state['_object']->id;
			if (isset($state['otf'])) {
				$_ref=$state['parent'].'/'.$state['parent_key'].'/item/'.$state['otf'];
			}

			$branch[]=array('label'=>'<span class="id Store_Product_Code">'.$state['_object']->get('Code').'</span>', 'icon'=>'cube', 'reference'=>$_ref);

		}elseif ($state['section']=='products') {
			if ( $user->get_number_stores()>1) {


				$branch[]=array('label'=>_('(All stores)'), 'icon'=>'', 'reference'=>'products/all');

			}

			$branch[]=array('label'=>_('Products').' <span class="id">'.$state['store']->get('Code').'</span>', 'icon'=>'cube', 'reference'=>'');


		}elseif ($state['section']=='categories') {
			$branch[]=array('label'=>_("Products's categories"), 'icon'=>'sitemap', 'reference'=>'');
		}elseif ($state['section']=='category') {
			$category=$state['_object'];
			$branch[]=array('label'=>_("Products's categories"), 'icon'=>'sitemap', 'reference'=>'products/'.$category->get('Store Key').'/categories');


			if (isset($state['metadata'])) {
				$parent_category_keys=$state['metadata'];
			}else {

				$parent_category_keys=preg_split('/\>/', $category->get('Category Position'));
			}


			foreach ( $parent_category_keys as $category_key) {
				if (!is_numeric($category_key)) {
					continue;
				}
				if ($category_key==$state['key']) {
					$branch[]=array('label'=>$category->get('Label'), 'icon'=>'', 'reference'=>'');
					break;
				}else {

					$parent_category=new Category($category_key);
					if ($parent_category->id) {

						$branch[]=array('label'=>$parent_category->get('Label'), 'icon'=>'', 'reference'=>'products/'.$category->get('Store Key').'/category/'.$parent_category->id);

					}
				}
			}
		}


		break;
	case 'customers_server':
		if ($state['section']=='customers')
			$branch[]=array('label'=>_('Customers (All stores)'), 'icon'=>'', 'reference'=>'');

		break;


	case 'customers':


		switch ($state['parent']) {
		case 'store':
			$store=new Store($state['parent_key']);
			$state['current_store']=$store->id;

			break;


		}

		if ( $user->get_number_stores()>1) {


			$branch[]=array('label'=>_('(All stores)'), 'icon'=>'', 'reference'=>'customers/all');

		}

		switch ($state['section']) {
		case 'list':
			$list=new SubjectList($state['key']);
			$store=new Store($list->data['List Parent Key']);


			$branch[]=array('label'=>_("Customer's lists").' '.$store->data['Store Code'], 'icon'=>'list', 'reference'=>'customers/'.$store->id.'/lists');
			$branch[]=array('label'=>$list->get('List Name'), 'icon'=>'', 'reference'=>'customers/list/'.$list->id);

			break;

		case 'customer':

			if ($state['parent']=='store') {
				$customer=new Customer($state['key']);
				if ($customer->id) {


					$store=new Store($customer->data['Customer Store Key']);


					$branch[]=array('label'=>_('Customers').' '.$store->data['Store Code'], 'icon'=>'users', 'reference'=>'customers/'.$store->id);
					$branch[]=array('label'=>$customer->get_formatted_id(), 'icon'=>'user', 'reference'=>'customer/'.$customer->id);
				}
			}elseif ($state['parent']=='list') {
				$customer=new Customer($state['key']);
				$store=new Store($customer->data['Customer Store Key']);

				$list=new SubjectList($state['parent_key']);

				$branch[]=array('label'=>_("Customer's lists").' '.$store->data['Store Code'], 'icon'=>'list', 'reference'=>'customers/'.$store->id.'/lists');
				$branch[]=array('label'=>$list->get('List Name'), 'icon'=>'', 'reference'=>'customers/list/'.$list->id);


				$branch[]=array('label'=>_('Customer').' '.$customer->get_formatted_id(), 'icon'=>'user', 'reference'=>'customer/'.$customer->id);
			}
			break;
		case 'dashboard':
			$branch[]=array('label'=>_("Customer's dashboard").' '.$store->data['Store Code'], 'icon'=>'dashboard', 'reference'=>'customers/dashboard/'.$store->id);
			break;
		case 'customers':
			$branch[]=array('label'=>_('Customers').' '.$store->data['Store Code'], 'icon'=>'users', 'reference'=>'customers/'.$store->id);
			break;

		case 'categories':
			$branch[]=array('label'=>_("Customer's categories").' '.$store->data['Store Code'], 'icon'=>'sitemap', 'reference'=>'customers/categories/'.$store->id);
			break;
		case 'lists':
			$branch[]=array('label'=>_("Customer's lists").' '.$store->data['Store Code'], 'icon'=>'list', 'reference'=>'customers/'.$store->id.'/lists');
			break;
		case 'statistics':
			$branch[]=array('label'=>_("Customer's stats").' '.$store->data['Store Code'], 'icon'=>'line-chart', 'reference'=>'customers/statistics/'.$store->id);
			break;
		case 'pending_orders':
			$branch[]=array('label'=>_("Pending orders").' '.$store->data['Store Code'], 'icon'=>'clock-o', 'reference'=>'customers/pending_orders/'.$store->id);
			break;
		}
		break;
	case 'suppliers':
		if ($state['section']=='suppliers') {
			$branch[]=array('label'=>_('Suppiers'), 'icon'=>'industry', 'reference'=>'suppliers');
		}elseif ($state['section']=='supplier') {
			$branch[]=array('label'=>_('Suppiers'), 'icon'=>'industry', 'reference'=>'suppliers');
			$branch[]=array('label'=>$state['_object']->get('Code'), 'icon'=>'', 'reference'=>'supplier/'.$state['key']);

		}elseif ($state['section']=='supplier.new') {
			$branch[]=array('label'=>_('Suppiers'), 'icon'=>'industry', 'reference'=>'suppliers');
			$branch[]=array('label'=>_('New supplier'), 'icon'=>'', 'reference'=>'');

		}
		break;
	case 'orders_server':
		$branch[]=array('label'=>'', 'icon'=>'bars', 'reference'=>'account/orders');

		if ( $user->get_number_stores()>1) {
			$branch[]=array('label'=>_('Orders').' ('._('All stores').')', 'icon'=>'', 'reference'=>'orders/all');
		}
		break;
	case 'invoices_server':

		if ($state['section']=='categories') {
			$branch[]=array('label'=>_("Invoice's categories").' ('._('All stores').')', 'icon'=>'', 'reference'=>'');

		}if ($state['section']=='category') {
			$branch[]=array('label'=>_("Invoice's categories").' ('._('All stores').')', 'icon'=>'', 'reference'=>'');
			$branch[]=array('label'=>'<span class="Category_Code">'.$state['_object']->get('Code').'</span>', 'icon'=>'sitemap', 'reference'=>'');

		}else {


			$branch[]=array('label'=>'', 'icon'=>'bars', 'reference'=>'account/orders');

			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>_('Invoices').' ('._('All stores').')', 'icon'=>'', 'reference'=>'');
			}
		}
		break;
	case 'delivery_notes_server':
		$branch[]=array('label'=>'', 'icon'=>'bars', 'reference'=>'account/orders');

		if ( $user->get_number_stores()>1) {
			$branch[]=array('label'=>_('Delivery Notes').' ('._('All stores').')', 'icon'=>'', 'reference'=>'delivery_notes/all');
		}
		break;
	case 'orders':
		$branch[]=array('label'=>'', 'icon'=>'bars', 'reference'=>'account/orders');

		switch ($state['section']) {
		case 'orders':

			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'orders/all');
			}
			$store=new Store($state['parent_key']);

			$branch[]=array('label'=>_('Orders').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'orders/'.$store->id);


			break;

		case 'payments':
			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>_('Payments').' ('._('All stores').')', 'icon'=>'bars', 'url'=>'payments/all');
			}
			break;

		case 'order':

			if ($state['parent']=='customer') {

				$customer=new Customer($state['parent_key']);
				if ($customer->id) {
					if ( $user->get_number_stores()>1) {


						$branch[]=array('label'=>_('Customers (All stores)'), 'icon'=>'bars', 'reference'=>'customers/all');

					}

					$store=new Store($customer->data['Customer Store Key']);


					$branch[]=array('label'=>_('Customers').' '.$store->data['Store Code'], 'icon'=>'users', 'reference'=>'customers/'.$store->id);
					$branch[]=array('label'=>_('Customer').' '.$customer->get_formatted_id(), 'icon'=>'user', 'reference'=>'customer/'.$customer->id);
				}



			}
			else {
				$store=new Store($state['_object']->data['Order Store Key']);

				if ( $user->get_number_stores()>1) {
					$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'orders/all');
				}
				$branch[]=array('label'=>_('Orders').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'orders/'.$store->id);


			}
			$branch[]=array('label'=>$state['_object']->get('Order Public ID'), 'icon'=>'shopping-cart', 'reference'=>'');

			break;

		case 'delivery_note':

			$store=new Store($state['_object']->data['Delivery Note Store Key']);

			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'orders/all');
			}
			$branch[]=array('label'=>_('Orders').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'orders/'.$store->id);

			$parent=new Order($state['parent_key']);
			$branch[]=array('label'=>$parent->get('Order Public ID'), 'icon'=>'shopping-cart', 'reference'=>'orders/'.$store->id.'/'.$state['parent_key']);





			$branch[]=array('label'=>$state['_object']->get('Delivery Note ID'), 'icon'=>'truck fa-flip-horizontal', 'reference'=>'');
			break;

		case 'invoice':

			$store=new Store($state['_object']->data['Invoice Store Key']);

			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'orders/all');
			}
			$branch[]=array('label'=>_('Orders').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'orders/'.$store->id);

			$parent=new Order($state['parent_key']);
			$branch[]=array('label'=>$parent->get('Order Public ID'), 'icon'=>'shopping-cart', 'reference'=>'orders/'.$store->id.'/'.$state['parent_key']);



			$branch[]=array('label'=>$state['_object']->get('Invoice Public ID'), 'icon'=>'usd', 'reference'=>'');
			break;

		}

		break;
	case 'delivery_notes':
		$branch[]=array('label'=>'', 'icon'=>'bars', 'reference'=>'account/orders');

		switch ($state['section']) {
		case 'delivery_notes':

			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'delivery_notes/all');
			}
			$store=new Store($state['parent_key']);

			$branch[]=array('label'=>_('Delivery Notes').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'delivery_notes/'.$store->id);


			break;


		case 'delivery_note':

			if ($state['parent']=='customer') {

				$customer=new Customer($state['parent_key']);
				if ($customer->id) {
					if ( $user->get_number_stores()>1) {


						$branch[]=array('label'=>_('Customers (All stores)'), 'icon'=>'', 'reference'=>'customers/all');

					}

					$store=new Store($customer->data['Customer Store Key']);


					$branch[]=array('label'=>_('Customers').' '.$store->data['Store Code'], 'icon'=>'users', 'reference'=>'customers/'.$store->id);
					$branch[]=array('label'=>_('Customer').' '.$customer->get_formatted_id(), 'icon'=>'user', 'reference'=>'customer/'.$customer->id);
				}



			}
			else {
				$store=new Store($state['_object']->data['Delivery Note Store Key']);

				if ( $user->get_number_stores()>1) {
					$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'delivery_notes/all');
				}
				$branch[]=array('label'=>_('Delivery notes').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'delivery_notes/'.$store->id);


			}
			$branch[]=array('label'=>$state['_object']->get('Delivery Note ID'), 'icon'=>'truck fa-flip-horizontal', 'reference'=>'');

			break;

		case 'order':

			$store=new Store($state['_object']->data['Order Store Key']);

			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'delivery_notes/all');
			}
			$branch[]=array('label'=>_('Delivery notes').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'delivery_notes/'.$store->id);

			$parent=new DeliveryNote($state['parent_key']);
			$branch[]=array('label'=>$parent->get('Delivery Note ID'), 'icon'=>'truck fa-flip-horizontal', 'reference'=>'delivery_notes/'.$store->id.'/'.$state['parent_key']);



			$branch[]=array('label'=>$state['_object']->get('Order Public ID'), 'icon'=>'shopping-cart', 'reference'=>'');
			$branch[]=array('label'=>$state['_object']->get('Delivery Note ID'), 'icon'=>'truck fa-flip-horizontal', 'reference'=>'');

			break;

		case 'invoice':

			$store=new Store($state['_object']->data['Invoice Store Key']);

			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'delivery_notes/all');
			}
			$branch[]=array('label'=>_('Delivery notes').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'delivery_notes/'.$store->id);

			$parent=new DeliveryNote($state['parent_key']);
			$branch[]=array('label'=>$parent->get('Delivery Note ID'), 'icon'=>'truck fa-flip-horizontal', 'reference'=>'delivery_notes/'.$store->id.'/'.$state['parent_key']);



			$branch[]=array('label'=>$state['_object']->get('Invoice Public ID'), 'icon'=>'usd', 'reference'=>'');
			$branch[]=array('label'=>$state['_object']->get('Delivery Note ID'), 'icon'=>'truck fa-flip-horizontal', 'reference'=>'');

			break;


		}

		break;
	case 'invoices':
		$branch[]=array('label'=>'', 'icon'=>'bars', 'reference'=>'account/orders');

		switch ($state['section']) {

		case 'invoices':
			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'url'=>'invoices/all');
			}
			$store=new Store($state['parent_key']);

			$branch[]=array('label'=>_('Invoices').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'invoices/'.$store->id);


			break;

		case 'payments':
			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>_('Payments').' ('._('All stores').')', 'icon'=>'', 'url'=>'invoices/payments/all');
			}
			break;

		case 'invoice':

			if ($state['parent']=='customer') {

				$customer=new Customer($state['parent_key']);
				if ($customer->id) {
					if ( $user->get_number_stores()>1) {


						$branch[]=array('label'=>_('Customers (All stores)'), 'icon'=>'', 'reference'=>'customers/all');

					}

					$store=new Store($customer->data['Customer Store Key']);


					$branch[]=array('label'=>_('Customers').' '.$store->data['Store Code'], 'icon'=>'users', 'reference'=>'customers/'.$store->id);
					$branch[]=array('label'=>_('Customer').' '.$customer->get_formatted_id(), 'icon'=>'user', 'reference'=>'customer/'.$customer->id);
				}



			}
			else {
				$store=new Store($state['_object']->data['Invoice Store Key']);

				if ( $user->get_number_stores()>1) {
					$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'invoices/all');
				}
				$branch[]=array('label'=>_('Invoices').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'invoices/'.$store->id);


			}
			$branch[]=array('label'=>$state['_object']->get('Invoice Public ID'), 'icon'=>'usd', 'reference'=>'');

			break;

		case 'delivery_note':

			$store=new Store($state['_object']->data['Delivery Note Store Key']);


			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'invoices/all');
			}
			$branch[]=array('label'=>_('Invoices').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'invoices/'.$store->id);

			$parent=new Invoice($state['parent_key']);
			$branch[]=array('label'=>$parent->get('Invoice Public ID'), 'icon'=>'usd', 'reference'=>'invoices/'.$store->id.'/'.$state['parent_key']);





			$branch[]=array('label'=>$state['_object']->get('Delivery Note ID'), 'icon'=>'truck fa-flip-horizontal', 'reference'=>'');
			break;


		case 'order':

			$store=new Store($state['_object']->data['Order Store Key']);


			if ( $user->get_number_stores()>1) {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'invoices/all');
			}
			$branch[]=array('label'=>_('Invoices').' '.$store->data['Store Code'], 'icon'=>'', 'reference'=>'invoices/'.$store->id);

			$parent=new Invoice($state['parent_key']);
			$branch[]=array('label'=>$parent->get('Invoice Public ID'), 'icon'=>'usd', 'reference'=>'invoices/'.$store->id.'/'.$state['parent_key']);





			$branch[]=array('label'=>$state['_object']->get('Order Public ID'), 'icon'=>'shopping-cart', 'reference'=>'');
			break;


		}



		break;
	case 'help':
		switch ($state['section']) {
		case 'help':
			$branch[]=array('label'=>_('Help'), 'icon'=>'', 'reference'=>'help');
			break;


		}
		break;
	case 'hr':
		switch ($state['section']) {
		case 'employees':
			$branch[]=array('label'=>_('Employees'), 'icon'=>'', 'reference'=>'hr');
			break;

		case 'employee':
			$branch[]=array('label'=>_('Employees'), 'icon'=>'', 'reference'=>'hr');

			$branch[]=array('label'=>_('Employee').' <span class="id Staff_Alias">'.$state['_object']->get('Staff Alias').'</span>', 'icon'=>'', 'reference'=>'employee/'.$state['_object']->id);

			break;
		case 'employee.attachment':
			include_once 'class.Staff.php';
			$employee=new Staff($state['parent_key']);
			$branch[]=array('label'=>_('Employees'), 'icon'=>'', 'reference'=>'hr');

			$branch[]=array('label'=>_('Employee').' <span class="id Staff_Alias">'.$employee->get('Staff Alias').'</span>', 'icon'=>'', 'reference'=>'employee/'.$employee->id);
			$branch[]=array('label'=>_('Attachment').' <span class="id Attachment_Caption">'.$state['_object']->get('Caption').'</span>', 'icon'=>'', 'reference'=>'employee/'.$employee->id.'/attachment/'.$state['_object']->id);

		case 'timesheets':
			$branch[]=array('label'=>_("Employees' calendar"), 'icon'=>'', 'reference'=>'timesheets/day/'.date('Ymd'));
			if ($state['parent']=='year') {
				$branch[]=array('label'=>$state['parent_key'], 'icon'=>'', 'reference'=>'timesheets/year/'.$state['parent_key']);

			}elseif ($state['parent']=='month') {
				$year=substr($state['parent_key'], 0, 4);
				$month=substr($state['parent_key'], 4, 2);
				$branch[]=array('label'=>$year, 'icon'=>'', 'reference'=>'timesheets/year/'.$year);

				$date=strtotime("$year-$month-01");
				$branch[]=array('label'=>strftime('%B', $date), 'icon'=>'', 'reference'=>'timesheets/month/'.$state['parent_key']);

			}elseif ($state['parent']=='week') {
				$year=substr($state['parent_key'], 0, 4);
				$week=substr($state['parent_key'], 4, 2);
				$branch[]=array('label'=>$year, 'icon'=>'', 'reference'=>'timesheets/year/'.$year);

				$date=strtotime("$year".'W'.$week);
				$branch[]=array('label'=>  sprintf(_('%s week (starting %s %s)'), get_ordinal_suffix($week), strftime('%a', $date), get_ordinal_suffix(strftime('%d', $date)))               , 'icon'=>'', 'reference'=>'timesheets/week/'.$year.$week);

			}elseif ($state['parent']=='day') {

				$year=substr($state['parent_key'], 0, 4);
				$month=substr($state['parent_key'], 4, 2);
				$day=substr($state['parent_key'], 6, 2);

				$date=strtotime("$year-$month-$day");

				$branch[]=array('label'=>$year, 'icon'=>'', 'reference'=>'timesheets/year/'.$year);

				$branch[]=array('label'=> strftime('%B', $date) , 'icon'=>'', 'reference'=>'timesheets/month/'.$year.$month);
				$branch[]=array('label'=> strftime('%a', $date).' '. get_ordinal_suffix(strftime('%d', $date))   , 'icon'=>'', 'reference'=>'timesheets/month/'.$year.$month.$day);

			}

		}
		break;
	case 'inventory_server':
		if ($state['section']=='inventory') {

			$branch[]=array('label'=>_('Inventory (All warehouses)'), 'icon'=>'cube', 'reference'=>'');
		}


		break;


	case 'inventory':


		switch ($state['section']) {
		case 'inventory':
			if ( $user->get_number_warehouses()>1 or $user->can_create('warehouses') ) {

				$branch[]=array('label'=>'('._('All warehouses').')', 'icon'=>'', 'reference'=>'inventory/all');

			}
			$branch[]=array('label'=>_('Inventory').' <span class="id">'.$state['warehouse']->get('Code').'</span>', 'icon'=>'th-large', 'reference'=>'');

			break;
		case 'part':
			if ( $user->get_number_warehouses()>1 or $user->can_create('warehouses') ) {

				$branch[]=array('label'=>'('._('All warehouses').')', 'icon'=>'', 'reference'=>'inventory/all');

			}
			$branch[]=array('label'=>_('Inventory').' <span class="id">'.$state['warehouse']->get('Code').'</span>', 'icon'=>'th-large', 'reference'=>'inventory/'.$state['warehouse']->id);
			$branch[]=array('label'=>'<span class="id Part_Reference">'.$state['_object']->get('Reference').'</span> (<span class="id">'.$state['_object']->get('SKU').'</span>)' , 'icon'=>'square', 'reference'=>'');

			break;

		case 'part.image':
			if ( $user->get_number_warehouses()>1 or $user->can_create('warehouses') ) {

				$branch[]=array('label'=>'('._('All warehouses').')', 'icon'=>'', 'reference'=>'inventory/all');

			}
			$branch[]=array('label'=>_('Inventory').' <span class="id">'.$state['warehouse']->get('Code').'</span>', 'icon'=>'th-large', 'reference'=>'inventory/'.$state['warehouse']->id);
			$branch[]=array('label'=>'<span class="id Part_Reference">'.$state['_parent']->get('Reference').'</span> (<span class="id">'.$state['_parent']->get('SKU').'</span>)' , 'icon'=>'square', 'reference'=>'inventory/'.$state['warehouse']->id.'/part/'.$state['_parent']->sku);
			$branch[]=array('label'=>_('Image'), 'icon'=>'camera-retro', 'reference'=>'');

			break;

		case 'part.new':
			if ( $user->get_number_warehouses()>1 or $user->can_create('warehouses') ) {

				$branch[]=array('label'=>'('._('All warehouses').')', 'icon'=>'', 'reference'=>'inventory/all');

			}
			$branch[]=array('label'=>_('Inventory').' <span class="id">'.$state['warehouse']->get('Code').'</span>', 'icon'=>'th-large', 'reference'=>'inventory/'.$state['warehouse']->id);
			$branch[]=array('label'=>_('New part') , 'icon'=>'square', 'reference'=>'');

			break;
		}


		break;

	case 'warehouses':


		switch ($state['section']) {
		case 'warehouse':
			if ( $user->get_number_warehouses()>1 or $user->can_create('warehouses') ) {

				$branch[]=array('label'=>'('._('All warehouses').')', 'icon'=>'', 'reference'=>'warehouses');

			}
			$branch[]=array('label'=>_('warehouse').' <span class="id Warehouse_Code">'.$state['warehouse']->get('Code').'</span>', 'icon'=>'map', 'reference'=>'');

			break;
		case 'part':
			if ( $user->get_number_warehouses()>1 or $user->can_create('warehouses') ) {

				$branch[]=array('label'=>'('._('All warehouses').')', 'icon'=>'', 'reference'=>'inventory/all');

			}
			$branch[]=array('label'=>_('Inventory').' <span class="id">'.$state['warehouse']->get('Code').'</span>', 'icon'=>'th-large', 'reference'=>'inventory/'.$state['warehouse']->id);
			$branch[]=array('label'=>'<span class="id Part_Reference">'.$state['_object']->get('Reference').'</span> (<span class="id">'.$state['_object']->get('SKU').'</span>)' , 'icon'=>'square', 'reference'=>'');

			break;
		}


		break;


	case 'websites':


		if ( $user->get_number_websites()>1) {

			$branch[]=array('label'=>_('Websites'), 'icon'=>'bars', 'reference'=>'websites');

		}
		switch ($state['section']) {
		case 'website':

			$website=$state['_object'];

			$branch[]=array('label'=>_('Website').' '.$website->data['Site Code'], 'icon'=>'globe', 'reference'=>'website/'.$website->id);
			break;
		case 'page':
			$page=$state['_object'];
			$website=new Site($page->get('Page Site Key'));
			$branch[]=array('label'=>_('Website').' '.$website->data['Site Code'], 'icon'=>'globe', 'reference'=>'website/'.$website->id);
			$branch[]=array('label'=>_('Page').' '.$page->data['Page Code'], 'icon'=>'file', 'reference'=>'website/'.$website->id.'/page/'.$website->id);

			break;
		case 'website.user':

			if ($state['parent']=='website') {
				$website=new Site($state['parent_key']);
			}elseif ($state['parent']=='page') {
				$page=new Page($state['parent_key']);

				$website=new Site($page->get('Page Site Key'));

			}

			$branch[]=array('label'=>_('Website').' '.$website->data['Site Code'], 'icon'=>'globe', 'reference'=>'website/'.$website->id);

			if ($state['parent']=='page') {

				$branch[]=array('label'=>_('Page').' '.$page->data['Page Code'], 'icon'=>'file', 'reference'=>'website/'.$website->id.'/page/'.$page->id);

			}

			$branch[]=array('label'=>_('User').' '.$state['_object']->data['User Handle'], 'icon'=>'user', 'reference'=>'website/'.$website->id.'/user/'.$state['_object']->id);

			break;
		}

		break;

	case 'profile':
		$branch[]=array('label'=>_('My profile').' <span class="id">'.$user->get('User Alias').'</span>', 'icon'=>'', 'reference'=>'profile');


		break;
	case 'payments':

		if ($state['section']=='payment_account') {


			/*

			include_once 'class.Payment_Service_Provider.php';

			$psp=new Payment_Service_Provider($state['_object']->get('Payment Service Provider Key'));

			$branch[]=array('label'=>_('Payment service provider').'  <span id="id">'.$psp->get('Payment Service Provider Code').'</span>', 'icon'=>'', 'reference'=>'account/payment_service_provider/'.$psp->id);

			$branch[]=array('label'=>_('Payment account').'  <span id="id">'.$state['_object']->get('Payment Account Code').'</span>', 'icon'=>'', 'reference'=>'account/payment_service_provider/'.$state['_object']->id);
*/


			if ($state['parent']=='account') {
				$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'cc', 'reference'=>'payment_accounts/all');

			}

			$branch[]=array('label'=>_('Payment account').'  <span id="id">'.$state['_object']->get('Payment Account Code').'</span>', 'icon'=>'', 'reference'=>'account/payment_service_provider/'.$state['_object']->id);


		}
		elseif ($state['section']=='payment_accounts') {
			if ($state['parent']=='store') {
				$store=new Store($state['parent_key']);
				if ( $user->get_number_stores()>1) {
					$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'payment_accounts/all');
				}

				$branch[]=array('label'=>_('Payment accounts').'  <span id="id">('.$store->get('Code').')</span>', 'icon'=>'', 'reference'=>'payment_accounts/'.$store->id);
			}
			elseif ($state['parent']=='account') {

				$branch[]=array('label'=>_('Payment accounts').' ('._('All stores').')', 'icon'=>'', 'reference'=>'payment_accounts/all');

			}
			elseif ($state['parent']=='payment_service_provider') {

				include_once 'class.Payment_Service_Provider.php';

				$psp=new Payment_Service_Provider($state['parent_key']);

				$branch[]=array('label'=>_('Payment service provider').'  <span id="id">'.$psp->get('Payment Service Provider Code').'</span>', 'icon'=>'', 'reference'=>'payment_service_provider/'.$psp->id);
				$branch[]=array('label'=>_('Payment accounts'), 'icon'=>'', 'reference'=>'');

			}



		}
		elseif ($state['section']=='payment_service_providers') {


			$branch[]=array('label'=>_('Payment service providers'), 'icon'=>'bank', 'reference'=>'');


		}
		elseif ($state['section']=='payment_service_provider') {

			$branch[]=array('label'=>_('Payment service providers'), 'icon'=>'bank', 'reference'=>'');
			$psp=new Payment_Service_Provider($state['_object']->get('Payment Service Provider Key'));

			$branch[]=array('label'=>_('Payment service provider').'  <span id="id">'.$psp->get('Code').'</span>', 'icon'=>'', 'reference'=>'');


		}
		elseif ($state['section']=='payments') {

			if ($state['parent']=='account') {
				$branch[]=array('label'=>_('Payments').' ('._('All stores').')', 'icon'=>'', 'reference'=>'payments/all');

			}
			elseif ($state['parent']=='store') {
				$store=new Store($state['parent_key']);




				if ( $user->get_number_stores()>1) {
					$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'payments/all');
				}

				$branch[]=array('label'=>_('Payments').'  <span id="id">('.$store->get('Code').')</span>', 'icon'=>'', 'reference'=>'payments/'.$store->id);



			}
			elseif ($state['parent']=='payment_service_provider') {
				include_once 'class.Payment_Service_Provider.php';
				$branch[]=array('label'=>_('Payment service provider').'  <span id="id">'.$psp->get('Payment Service Provider Code').'</span>', 'icon'=>'', 'reference'=>'account/payment_service_provider/'.$psp->id);



			}elseif ($state['parent']=='payment_account') {
				include_once 'class.Payment_Account.php';
				$payment_account=new Payment_Account($state['_object']->get('Payment Account Key'));
				$branch[]=array('label'=>_('Payment account').'  <span id="id">'.$payment_account->get('Payment Account Code').'</span>', 'icon'=>'', 'reference'=>'payment_service_provider/'.$psp->id.'/payment_account/'.$payment_account->id);


			}



		}
		elseif ($state['section']=='payment') {

			if ($state['parent']=='account') {

			}
			elseif ($state['parent']=='store') {
				$store=new Store($state['parent_key']);




				if ( $user->get_number_stores()>1) {
					$branch[]=array('label'=>'('._('All stores').')', 'icon'=>'', 'reference'=>'payments/all');
				}

				$branch[]=array('label'=>_('Payments').'  <span id="id">('.$store->get('Code').')</span>', 'icon'=>'', 'reference'=>'payments/'.$store->id);


				$branch[]=array('label'=>_('Payment').'  <span id="id">'.$state['_object']->get('Payment Key').'</span>', 'icon'=>'', 'reference'=>'');

			}
			elseif ($state['parent']=='payment_service_provider') {
				include_once 'class.Payment_Service_Provider.php';
				$branch[]=array('label'=>_('Payment service provider').'  <span id="id">'.$psp->get('Payment Service Provider Code').'</span>', 'icon'=>'', 'reference'=>'account/payment_service_provider/'.$psp->id);



			}elseif ($state['parent']=='payment_account') {
				include_once 'class.Payment_Account.php';
				$payment_account=new Payment_Account($state['_object']->get('Payment Account Key'));
				$branch[]=array('label'=>_('Payment account').'  <span id="id">'.$payment_account->get('Payment Account Code').'</span>', 'icon'=>'', 'reference'=>'payment_service_provider/'.$psp->id.'/payment_account/'.$payment_account->id);

				$branch[]=array('label'=>_('Payment').'  <span id="id">'.$state['_object']->get('Payment Key').'</span>', 'icon'=>'', 'reference'=>'payment_account/'.$payment_account->id.'/payment/'.$state['_object']->id);

			}





		}


		break;
	case 'account':


		if ($state['section']=='orders_index') {
			$branch[]=array('label'=>_("Order's index"), 'icon'=>'bars', 'reference'=>'');
			break;
		}

		$branch[]=array('label'=>_('Account').' <span class="id">'.$account->get('Account Code').'</span>', 'icon'=>'', 'reference'=>'account');
		if ($state['section']=='users') {
			$branch[]=array('label'=>_('Users'), 'icon'=>'', 'reference'=>'account/users');

		}elseif ($state['section']=='staff') {
			$branch[]=array('label'=>_('Users'), 'icon'=>'', 'reference'=>'account/users');

			$branch[]=array('label'=>_('Staff users'), 'icon'=>'', 'reference'=>'account/users/staff');

		}elseif ($state['section']=='staff.user') {
			$branch[]=array('label'=>_('Users'), 'icon'=>'', 'reference'=>'account/users');

			$branch[]=array('label'=>_('Staff users'), 'icon'=>'', 'reference'=>'account/users/staff');
			$branch[]=array('label'=>_('User').' <span id="id">'.$state['_object']->data['User Alias'].'</span>', 'icon'=>'male', 'reference'=>'account/user/'.$state['_object']->id);

		}elseif ($state['section']=='settings') {
			$branch[]=array('label'=>_('Settings'), 'icon'=>'cog', 'reference'=>'account/settings');

		}elseif ($state['section']=='payment_service_provider') {
			$branch[]=array('label'=>_('Payment service provider').'  <span id="id">'.$state['_object']->get('Payment Service Provider Code').'</span>', 'icon'=>'', 'reference'=>'account/payment_service_provider/'.$state['_object']->id);

		}elseif ($state['section']=='data_sets') {
			$branch[]=array('label'=>_('Data sets'), 'icon'=>'align-left', 'reference'=>'account/data_sets');

		}elseif ($state['section']=='timeseries') {
			$branch[]=array('label'=>_('Data sets'), 'icon'=>'align-left', 'reference'=>'account/data_sets');
			$branch[]=array('label'=>_('Time series'), 'icon'=>'line-chart', 'reference'=>'account/data_sets/timeseries');

		}elseif ($state['section']=='timeserie') {
			$branch[]=array('label'=>_('Data sets'), 'icon'=>'align-left', 'reference'=>'account/data_sets');
			$branch[]=array('label'=>_('Time series'), 'icon'=>'line-chart', 'reference'=>'account/data_sets/timeseries');
			$branch[]=array('label'=>$state['_object']->get('Name'), 'icon'=>'', 'reference'=>'');

		}elseif ($state['section']=='images') {
			$branch[]=array('label'=>_('Data sets'), 'icon'=>'align-left', 'reference'=>'account/data_sets');
			$branch[]=array('label'=>_('Images'), 'icon'=>'image', 'reference'=>'account/data_sets/images');

		}elseif ($state['section']=='attachments') {
			$branch[]=array('label'=>_('Data sets'), 'icon'=>'align-left', 'reference'=>'account/data_sets');
			$branch[]=array('label'=>_('Attachments'), 'icon'=>'paperclip', 'reference'=>'account/data_sets/attachments');

		}elseif ($state['section']=='osf') {
			$branch[]=array('label'=>_('Data sets'), 'icon'=>'align-left', 'reference'=>'account/data_sets');
			$branch[]=array('label'=>_('Transactions timeseries'), 'icon'=>'', 'reference'=>'account/data_sets/osf');

		}elseif ($state['section']=='isf') {
			$branch[]=array('label'=>_('Data sets'), 'icon'=>'align-left', 'reference'=>'account/data_sets');
			$branch[]=array('label'=>_('Inventory timeseries'), 'icon'=>'', 'reference'=>'account/data_sets/isf');

		}

		/*
		case ('data_sets'):
			return get_data_sets_navigation($data, $smarty, $user, $db,$account);
			break;
		case ('timeseries'):
			return get_timeseries_navigation($data, $smarty, $user, $db,$account);
			break;
		case ('images'):
			return get_images_navigation($data, $smarty, $user, $db,$account);
			break;
		case ('attachments'):
			return get_attachments_navigation($data, $smarty, $user, $db,$account);
			break;
		case ('osf'):
			return get_osf_navigation($data, $smarty, $user, $db,$account);
			break;
		case ('isf'):
			return get_isf_navigation($data, $smarty, $user, $db,$account);
			break;
*/


		break;
	case 'production':
		if ($state['section']=='manufacture_tasks') {
			$branch[]=array('label'=>_("Manufacture Tasks"), 'icon'=>'tasks', 'reference'=>'production/manufacture_tasks');
		}elseif ($state['section']=='manufacture_task') {
			$branch[]=array('label'=>_("Manufacture Tasks"), 'icon'=>'tasks', 'reference'=>'production/manufacture_tasks');
			$branch[]=array('label'=>'<span class="Manufacture_Task_Code">'.$state['_object']->get('Code').'</span>', 'icon'=>'', 'reference'=>'');

		}

		break;
	case 'reports':
		$branch[]=array('label'=>_('Reports'), 'icon'=>'', 'reference'=>'reports');

		if ($state['section']=='billingregion_taxcategory') {
			$branch[]=array('label'=>_('Billing region & Tax code report'), 'icon'=>'', 'reference'=>'report/billingregion_taxcategory');

		}else if ($state['section']=='billingregion_taxcategory.invoices') {
			$branch[]=array('label'=>_('Billing region & Tax code report'), 'icon'=>'', 'reference'=>'report/billingregion_taxcategory');


			$parents=preg_split('/_/', $state['parent_key']);

			switch ($parents[0]) {
			case 'EU':
				$billing_region=_('European Union');
				break;
			case 'NOEU':
				$billing_region=_('Outside European Union');
				break;
			case 'GBIM':
				$billing_region='GB+IM';
				break;
			case 'Unknown':
				$billing_region=_('Unknown');
				break;
			default:
				$billing_region=$parents[0];
				break;
			}

			$label=_('Invoices')." $billing_region & ".$parents[1];
			$branch[]=array('label'=>$label, 'icon'=>'', 'reference'=>'');

		}else if ($state['section']=='billingregion_taxcategory.refunds') {
			$branch[]=array('label'=>_('Billing region & Tax code report'), 'icon'=>'', 'reference'=>'report/billingregion_taxcategory');
			$parents=preg_split('/_/', $state['parent_key']);

			switch ($parents[0]) {
			case 'EU':
				$billing_region=_('European Union');
				break;
			case 'Unknown':
				$billing_region=_('Unknown');
				break;
			case 'NOEU':
				$billing_region=_('Outside European Union');
				break;
			case 'GBIM':
				$billing_region='GB+IM';
				break;
			default:
				$billing_region=$state[0];
				break;
			}

			$label=_('Refunds')." $billing_region & ".$parents[1];
			$branch[]=array('label'=>$label, 'icon'=>'', 'reference'=>'');
		}

		break;

	}

	$_content=array(
		'branch'=>$branch,

	);
	$smarty->assign('_content', $_content);

	$html=$smarty->fetch('view_position.tpl');
	return array($state, $html);



}









?>
