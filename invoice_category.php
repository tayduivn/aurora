<?php
/*
 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2013, Inikoo

 Version 2.0
*/
include_once 'class.Category.php';
include_once 'class.Store.php';
include_once 'common.php';
include_once('common_date_functions.php');




if (!$user->can_view('orders')  ) {
	header('Location: index.php');
	exit;
}

if (isset($_REQUEST['id'])) {
	$category_key=$_REQUEST['id'];
} else {
	$category_key=0;
}

if (!$category_key) {
	header('Location: index.php?error_no_category_id');
	exit;
}
$category=new Category($category_key);
if (!$category->id) {
	header('Location: invoice_category_deleted.php?id='.$category_key);
	exit;
}


if ($category->data['Category Subject']!='Invoice') {
	header('Location: index.php?error_no_wrong_category_id');
	exit;
}

$modify=$user->can_edit('orders');


$smarty->assign('view',$_SESSION['state']['invoice_categories']['view']);

$css_files=array(
	$yui_path.'reset-fonts-grids/reset-fonts-grids.css',
	$yui_path.'menu/assets/skins/sam/menu.css',
	$yui_path.'assets/skins/sam/autocomplete.css',
	$yui_path.'calendar/assets/skins/sam/calendar.css',
	'css/common.css',
	'css/container.css',
	'css/button.css',
	'css/calendar.css',
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
	'js/jquery.min.js',
'js/common.js',
	'js/search.js',
	'js/table_common.js',
	'js/export_common.js',
	
	'external_libs/ammap/ammap/swfobject.js',
	'js/invoices_common.js',
	'js/localize_calendar.js',
	'js/calendar_interval.js',
	'js/reports_calendar.js',
	'invoice_category.js.php',

);





$smarty->assign('search_label',_('Orders'));
$smarty->assign('search_scope','orders');

$smarty->assign('subcategories_view',$_SESSION['state']['invoice_categories']['view']);
$smarty->assign('subcategories_period',$_SESSION['state']['invoice_categories']['period']);
$smarty->assign('subcategories_avg',$_SESSION['state']['invoice_categories']['avg']);
$smarty->assign('category_period',$_SESSION['state']['invoice_categories']['period']);




$category_key=  $category->id;
$store=new Store($category->data['Category Store Key']);

$currency=$store->data['Store Currency Code'];
$currency_symbol=currency_symbol($currency);

$smarty->assign('category',$category);

if (isset($_REQUEST['block_view']) and in_array($_REQUEST['block_view'],array('subcategories','subjects','overview','history'))) {
	$_SESSION['state']['invoice_categories'][$state_type.'_block_view']=$_REQUEST['block_view'];
}

$state_type=($category->data['Category Branch Type']=='Head'?'head':'node');

$block_view=$_SESSION['state']['invoice_categories'][$state_type.'_block_view'];


$smarty->assign('state_type',$state_type);

$show_subcategories=true;
$show_subjects=true;
$show_subjects_data=true;



if ($category->data['Category Branch Type']!='Head') {
	$show_subjects=false;
	$show_subjects_data=false;
}

if ($category->data['Category Max Deep']<=$category->data['Category Deep']) {
	$show_subcategories=false;

}

//print $block_view;
if (!$show_subcategories and $block_view=='subcategories') {
	$block_view='overview';
}
if (!$show_subjects and $block_view=='subjects') {
	$block_view='overview';
}
if (!$show_subjects_data and $block_view=='sales') {
	$block_view='overview';
}

//print " $block_view";
$smarty->assign('show_subcategories',$show_subcategories);
$smarty->assign('show_subjects',$show_subjects);
$smarty->assign('show_subjects_data',$show_subjects_data);
$smarty->assign('block_view',$block_view);


$tipo_filter=$_SESSION['state']['invoice_categories']['invoices']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['invoice_categories']['invoices']['f_value']);
$filter_menu=array(
	'public_id'=>array('db_key'=>'public_id','menu_label'=>_('Invoice Number starting with <i>x</i>'),'label'=>_('Invoice Number')),
	'customer_name'=>array('db_key'=>'customer_name','menu_label'=>_('Customer Name starting with <i>x</i>'),'label'=>_('Customer')),
	'minvalue'=>array('db_key'=>'minvalue','menu_label'=>_('Invoice with a minimum value of').' <i>'.$corporate_currency_symbol.'n</i>','label'=>'Min Value ('.$corporate_currency_symbol.')'),
	'maxvalue'=>array('db_key'=>'maxvalue','menu_label'=>_('Invoice with a maximum value of').' <i>'.$corporate_currency_symbol.'n</i>','label'=>'Max Value ('.$corporate_currency_symbol.')'),
	'country'=>array('db_key'=>'country','menu_label'=>_('Invoice billed to country code <i>xxx</i>'),'label'=>_('Country Code'))

);
$smarty->assign('filter_menu0',$filter_menu);

$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);

$invoices_view=$_SESSION['state']['invoice_categories']['invoices']['view'];

if ($invoices_view=='other_value' and $category->data['Is Category Field Other']=='No') {
	$invoices_view='general';
}


$smarty->assign('invoices_view',$invoices_view);



$smarty->assign('invoices_period',$_SESSION['state']['invoice_categories']['invoices']['period']);
$smarty->assign('invoices_avg',$_SESSION['state']['invoice_categories']['invoices']['avg']);





$smarty->assign('store_key',$store->id);

$smarty->assign('store_id',$store->id);
$smarty->assign('store',$store);



$order=$_SESSION['state']['invoice_categories']['subcategories']['order'];
if ($order=='code') {
	$order='`Category Code`';
	$order_label=_('Code');
} else {
	$order='`Category Label`';
	$order_label=_('Label');
}
$_order=preg_replace('/`/','',$order);
$sql=sprintf("select `Category Key` as id , `Category Code` as name from `Category Dimension`  where  `Category Parent Key`=%d and `Category Root Key`=%d  and %s < %s  order by %s desc  limit 1",
	$category->data['Category Parent Key'],
	$category->data['Category Root Key'],
	$order,
	prepare_mysql($category->get($_order)),
	$order
);
//print $sql;
$result=mysql_query($sql);
if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$prev['link']='invoice_category.php?id='.$row['id'];
	$prev['title']=$row['name'];
	$smarty->assign('prev',$prev);
}

//print_r($prev);

mysql_free_result($result);


$sql=sprintf(" select`Category Key` as id , `Category Code` as name from `Category Dimension`  where  `Category Parent Key`=%d  and `Category Root Key`=%d    and  %s>%s  order by %s   ",
	$category->data['Category Parent Key'],
	$category->data['Category Root Key'],
	$order,
	prepare_mysql($category->get($_order)),
	$order
);

$result=mysql_query($sql);
if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$next['link']='invoice_category.php?id='.$row['id'];
	$next['title']=$row['name'];
	$smarty->assign('next',$next);
}
mysql_free_result($result);




$tipo_filter=$_SESSION['state']['invoice_categories']['subcategories']['f_field'];
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['invoice_categories']['subcategories']['f_value']);

$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Category Code'),'label'=>_('Code')),
	'label'=>array('db_key'=>'code','menu_label'=>_('Category Label'),'label'=>_('Label')),

);


$smarty->assign('filter_menu1',$filter_menu);
$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);


$tipo_filter=$_SESSION['state']['store']['history']['f_field'];
$smarty->assign('filter2',$tipo_filter);
$smarty->assign('filter_value2',$_SESSION['state']['site']['history']['f_value']);
$filter_menu=array(
	'notes'=>array('db_key'=>'notes','menu_label'=>_('Records with  notes *<i>x</i>*'),'label'=>_('Notes')),
	'author'=>array('db_key'=>'author','menu_label'=>_('Done by <i>x</i>*'),'label'=>_('Notes')),
	'upto'=>array('db_key'=>'upto','menu_label'=>_('Records up to <i>n</i> days'),'label'=>_('Up to (days)')),
	'older'=>array('db_key'=>'older','menu_label'=>_('Records older than  <i>n</i> days'),'label'=>_('Older than (days)')),
	'abstract'=>array('db_key'=>'abstract','menu_label'=>_('Records with abstract'),'label'=>_('Abstract'))

);
$smarty->assign('filter_name2',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu2',$filter_menu);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu2',$paginator_menu);


$tipo_filter=$_SESSION['state']['invoice_categories']['no_assigned_invoices']['f_field'];
$smarty->assign('filter3',$tipo_filter);
$smarty->assign('filter_value3',$_SESSION['state']['invoice_categories']['no_assigned_invoices']['f_value']);
$filter_menu=array(
	'public_id'=>array('db_key'=>'public_id','menu_label'=>_('Invoice Number starting with <i>x</i>'),'label'=>_('Invoice Number')),
	'customer_name'=>array('db_key'=>'customer_name','menu_label'=>_('Customer Name starting with <i>x</i>'),'label'=>_('Customer')),
	'minvalue'=>array('db_key'=>'minvalue','menu_label'=>_('Invoice with a minimum value of').' <i>'.$corporate_currency_symbol.'n</i>','label'=>'Min Value ('.$corporate_currency_symbol.')'),
	'maxvalue'=>array('db_key'=>'maxvalue','menu_label'=>_('Invoice with a maximum value of').' <i>'.$corporate_currency_symbol.'n</i>','label'=>'Max Value ('.$corporate_currency_symbol.')'),
	'country'=>array('db_key'=>'country','menu_label'=>_('Invoice billed to country code <i>xxx</i>'),'label'=>_('Country Code'))

);
$smarty->assign('filter_menu3',$filter_menu);

$smarty->assign('filter_name3',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu3',$paginator_menu);



$smarty->assign('parent','invoices');
$smarty->assign('title', _('Invoice Category').' '.$category->data['Category Code']);

$smarty->assign('subject','Invoice');
$smarty->assign('category_key',$category_key);
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

include_once 'conf/period_tags.php';
unset($period_tags['hour']);
$smarty->assign('period_tags',$period_tags);

$plot_data=array('pie'=>array('forecast'=>3,'interval'=>''));
$smarty->assign('plot_tipo','store');
$smarty->assign('plot_data',$plot_data);

$elements_number=array('Changes'=>0,'Assign'=>0
);
$sql=sprintf("select count(*) as num ,`Type` from  `Invoice Category History Bridge` where  `Category Key`=%d group by  `Type`",$category->id);
//print_r($sql);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
	$elements_number[$row['Type']]=number($row['num']);
}
$smarty->assign('history_elements_number',$elements_number);
$smarty->assign('history_elements',$_SESSION['state']['invoice_categories']['history']['elements']);

// 'elements'=>array(
//    'payment'=>array('Yes'=>1,'No'=>1,'Partially'=>1),
//    'type'=>array('Invoice'=>1,'Refund'=>1)


$smarty->assign('elements_invoice_elements_type',$_SESSION['state']['invoice_categories']['invoices']['elements_type']);
$smarty->assign('elements_invoice_category_elements_type',$_SESSION['state']['invoice_categories']['elements_type']);
$smarty->assign('elements_invoice_category_type',$_SESSION['state']['invoice_categories']['elements']['type']);
$smarty->assign('elements_invoice_category_payment',$_SESSION['state']['invoice_categories']['elements']['payment']);

$smarty->assign('elements_invoice_type',$_SESSION['state']['invoice_categories']['invoices']['elements']['type']);
$smarty->assign('elements_invoice_payment',$_SESSION['state']['invoice_categories']['invoices']['elements']['payment']);

if (isset($_REQUEST['period'])) {
	$period=$_REQUEST['period'];

}else {
	$period=$_SESSION['state']['invoice_categories']['period'];
}
if (isset($_REQUEST['from'])) {
	$from=$_REQUEST['from'];
}else {
	$from=$_SESSION['state']['invoice_categories']['from'];
}

if (isset($_REQUEST['to'])) {
	$to=$_REQUEST['to'];
}else {
	$to=$_SESSION['state']['invoice_categories']['to'];
}

list($period_label,$from,$to)=get_period_data($period,$from,$to);
$_SESSION['state']['invoice_categories']['period']=$period;
$_SESSION['state']['invoice_categories']['from']=$from;
$_SESSION['state']['invoice_categories']['to']=$to;




$smarty->assign('from',$from);
$smarty->assign('to',$to);
$smarty->assign('period',get_interval_db_name($period));
$smarty->assign('period_label',$period_label);
$to_little_edian=($to==''?'':date("d-m-Y",strtotime($to)));
$from_little_edian=($from==''?'':date("d-m-Y",strtotime($from)));
$smarty->assign('to_little_edian',$to_little_edian);
$smarty->assign('from_little_edian',$from_little_edian);
$smarty->assign('calendar_id','sales');


$root_category=new Category($category->data['Category Root Key']);
$smarty->assign('root_category_store_key',$root_category->data['Category Store Key']);

include_once 'invoices_export_common.php';

$smarty->display('invoice_category.tpl');
?>
