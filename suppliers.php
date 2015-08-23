<?php
/*
 File: suppliers.php

 UI suppliers page

 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/


include_once 'common.php';
include_once 'class.Warehouse.php';


$css_files=array(
	$yui_path.'reset-fonts-grids/reset-fonts-grids.css',
	$yui_path.'menu/assets/skins/sam/menu.css',
	$yui_path.'assets/skins/sam/autocomplete.css',
	$yui_path.'calendar/assets/skins/sam/calendar.css',
	'css/common.css',
	'css/container.css',
	'css/button.css',
	'css/table.css',
	'theme.css.php'
);

$js_files=array(

	$yui_path.'utilities/utilities.js',
	$yui_path.'json/json-min.js',
	$yui_path.'paginator/paginator-min.js',
	$yui_path.'datasource/datasource-min.js',
	$yui_path.'autocomplete/autocomplete-min.js',
	$yui_path.'datatable/datatable-min.js',
	$yui_path.'container/container-min.js',
	$yui_path.'menu/menu-min.js',
	$yui_path.'calendar/calendar-min.js',
	'js/php.default.min.js',

	'js/jquery.min.js',
'js/common.js',
	'js/table_common.js',
	'js/search.js',
	'js/suppliers_common.js',
	'js/supplier_products_common.js',
	'js/suppliers.js',
	'js/edit_common.js',


);





$smarty->assign('parent','suppliers');
$smarty->assign('title', _('Suppliers'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);



if ($user->data['User Type']=='Supplier') {
	if (count($user->suppliers)==0) {
		$smarty->display('forbidden.tpl');
		exit();
	}

	if (count($user->suppliers)==1) {
		header('Location: supplier.php?id='.$user->suppliers[0]);
		exit;
	}
}else {


	if (!($user->can_view('suppliers')     )) {
		header('Location: index.php');
		exit;
	}
}


$warehouse=new Warehouse(1);

$smarty->assign('warehouse',$warehouse);


$q='';
$sql="select count(*) as numberof from `Supplier Dimension`";
$result=mysql_query($sql);
if (!$suppliers=mysql_fetch_array($result, MYSQL_ASSOC))
	exit;


$create=$user->can_create('suppliers');

$modify=$user->can_edit('suppliers');
$view_sales=$user->can_view('supplier sales');



$view_stock=$user->can_view('supplier stock');

$smarty->assign('view_sales',$view_sales);
$smarty->assign('view_stock',$view_stock);


$smarty->assign('create',$create);
$smarty->assign('modify',$modify);

$smarty->assign('suppliers_view',$_SESSION['state']['suppliers']['suppliers']['view']);
$smarty->assign('suppliers_period',$_SESSION['state']['suppliers']['suppliers']['period']);

$smarty->assign('supplier_products_view',$_SESSION['state']['suppliers']['supplier_products']['view']);
$smarty->assign('supplier_products_period',$_SESSION['state']['suppliers']['supplier_products']['period']);
$smarty->assign('supplier_products_avg',$_SESSION['state']['suppliers']['supplier_products']['avg']);


$smarty->assign('options_box_width','400px');
$smarty->assign('block_view',$_SESSION['state']['suppliers']['block_view']);

$general_options_list=array();


if ($modify) {
	$general_options_list[]=array('tipo'=>'url','url'=>'edit_suppliers.php','label'=>_('Edit Suppliers'));
	$general_options_list[]=array('tipo'=>'url','url'=>'new_supplier.php','label'=>_('Add Supplier'));
}
//$general_options_list[]=array('tipo'=>'js','state'=>$show_details,'id'=>'details','label'=>($show_details?_('Hide Details'):_('Show Details')));
$general_options_list[]=array('tipo'=>'url','url'=>'suppliers_lists.php','label'=>_('Lists'));
$general_options_list[]=array('tipo'=>'url','url'=>'supplier_categories.php','label'=>_('Categories'));

//$smarty->assign('general_options_list',$general_options_list);


$smarty->assign('search_label',_('Suppliers'));
$smarty->assign('search_scope','supplier_products');

//$smarty->assign('box_layout','yui-t4');
//print_r($_SESSION['state']['suppliers']);



$smarty->assign('total_suppliers',$suppliers['numberof']);


$tipo_filter=$_SESSION['state']['suppliers']['suppliers']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['suppliers']['suppliers']['f_value']);


$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Suppliers with code starting with  <i>x</i>'),'label'=>_('Code')),
	'name'=>array('db_key'=>'name','menu_label'=>_('Suppliers which name starting with <i>x</i>'),'label'=>_('Name')),
	'low'=>array('db_key'=>'low','menu_label'=>_('Suppliers with more than <i>n</i> low stock products'),'label'=>_('Low')),
	'outofstock'=>array('db_key'=>'outofstock','menu_label'=>_('Suppliers with more than <i>n</i> products out of stock'),'label'=>_('Out of Stock')),
);
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);

//$smarty->assign('table_info',$orders.'  '.ngettext('Order','Orders',$orders));
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);



$tipo_filter=$_SESSION['state']['suppliers']['supplier_products']['f_field'];
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['suppliers']['supplier_products']['f_value']);
$filter_menu=array(
	'sup_code'=>array('db_key'=>'code','menu_label'=>_('Suppliers products with code starting with <i>x</i>'),'label'=>_('Code')),
);
$smarty->assign('filter_menu1',$filter_menu);
$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);

$tipo_filter=$_SESSION['state']['suppliers']['porders']['f_field'];
$smarty->assign('filter2',$tipo_filter);
$smarty->assign('filter_value2',$_SESSION['state']['suppliers']['porders']['f_value']);
$filter_menu=array(
	'public_id'=>array('db_key'=>'public_id','menu_label'=>_('Public ID>'),'label'=>_('Public ID')),
);
$smarty->assign('filter_menu2',$filter_menu);
$smarty->assign('filter_name2',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu2',$paginator_menu);

$tipo_filter=$_SESSION['state']['suppliers']['supplier_invoices']['f_field'];
$smarty->assign('filter3',$tipo_filter);
$smarty->assign('filter_value3',$_SESSION['state']['suppliers']['supplier_invoices']['f_value']);
$filter_menu=array(
	'public_id'=>array('db_key'=>'public_id','menu_label'=>_('Public ID>'),'label'=>_('Public ID')),
);
$smarty->assign('filter_menu3',$filter_menu);
$smarty->assign('filter_name3',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu3',$paginator_menu);

$tipo_filter=$_SESSION['state']['suppliers']['supplier_dns']['f_field'];
$smarty->assign('filter4',$tipo_filter);
$smarty->assign('filter_value4',$_SESSION['state']['suppliers']['supplier_dns']['f_value']);
$filter_menu=array(
	'public_id'=>array('db_key'=>'public_id','menu_label'=>_('Public ID>'),'label'=>_('Public ID')),
);
$smarty->assign('filter_menu4',$filter_menu);
$smarty->assign('filter_name4',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu4',$paginator_menu);

$smarty->assign('elements_sp_state',$_SESSION['state']['suppliers']['supplier_products']['elements']['state']);


$smarty->assign('supplier_id',false);

$session_data=base64_encode(json_encode(array(
			'label'=>array(
				'y'=>_('1y'),
				'Code'=>_('Code'),
				'Name'=>_('Name'),
				'Contact'=>_('Contact'),
				'Email'=>_('Email'),
				'Tel'=>_('Tel'),
				'Products_Origin'=>_('Products Origin'),
				'P_POs'=>_('P POs'),
				'Products'=>_('Products'),
				'Discontinued'=>_('Discontinued'),
				'Stock_Value'=>_('Stock Value'),
				'Delivery_Time'=>_('Delivery Time'),
				'High'=>_('High'),
				'Normal'=>_('Normal'),
				'Critical'=>_('Critical'),
				'Low'=>_('Low'),
				'Out_of_Stock'=>_('Out of Stock'),
				'Required'=>_('Required'),
				'Sold'=>_('Sold'),
				'Sales'=>_('Sales'),
				'Profit'=>_('Profit'),
				'PaS'=>_('PaS'),
				'Cost'=>_('Cost'),
				'Margin'=>_('Margin'),
				'Year0'=>date('Y',strtotime('now')),
				'Year1'=>date('Y',strtotime('-1 year')),
				'Year2'=>date('Y',strtotime('-2 year')),
				'Year3'=>date('Y',strtotime('-3 year')),
				'year0'=>date('y',strtotime('now')),
				'year1'=>date('y',strtotime('-1 year')),
				'year2'=>date('y',strtotime('-2 year')),
				'year3'=>date('y',strtotime('-3 year')),
				'year4'=>date('y',strtotime('-4 year')),
				'Supplier'=>_('Supplier'),
				'Description'=>_('Description'),
				'Used_In'=>_('Used In'),
				'Stock'=>_('Stock'),
				'W_Until_OO'=>_('w until OO'),
				'Purchase_Order_ID'=>_('Purchase Order ID'),
				'Last_Updated'=>_('Last Updated'),
				'Buyer'=>_('Buyer'),
				'Items'=>_('Items'),
				'Status'=>_('Status'),


				'Page'=>_('Page'),
				'of'=>_('of')
			),
			'state'=>$_SESSION['state']['suppliers']
		)));
$smarty->assign('session_data',$session_data);

$smarty->display('suppliers.tpl');
?>
