<?php
/*
 File: site.php

 UI site page

 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/
include_once 'common.php';
include_once 'class.Store.php';
include_once 'common_date_functions.php';

include_once 'class.Site.php';



$smarty->assign('page','site');
if (isset($_REQUEST['id']) and is_numeric($_REQUEST['id']) ) {
	$site_id=$_REQUEST['id'];

} else {
	$site_id=$_SESSION['state']['site']['id'];
}




if (!($user->can_view('sites')    ) ) {
	header('Location: index.php');
	exit;
}



$site=new Site($site_id);
if (!$site->id) {
	header('Location: sites.php?site_not_found');
	exit;
}



$_SESSION['state']['site']['id']=$site->id;

$store=new Store($site->data['Site Store Key']);
$smarty->assign('store',$store);
$smarty->assign('store_key',$store->id);

$create=$user->can_create('sites');

$modify=$user->can_edit('sites');


$smarty->assign('create',$create);
$smarty->assign('modify',$modify);



$smarty->assign('search_label',_('Website'));
$smarty->assign('search_scope','site');

$css_files=array(
	$yui_path.'reset-fonts-grids/reset-fonts-grids.css',
	$yui_path.'menu/assets/skins/sam/menu.css',
	$yui_path.'assets/skins/sam/autocomplete.css',
	$yui_path.'calendar/assets/skins/sam/calendar.css',
	'css/common.css',
	'css/container.css',
	'css/button.css',
	'css/table.css',
	'css/calendar.css',
	'theme.css.php'
);
$js_files=array(
	$yui_path.'utilities/utilities.js',
	$yui_path.'json/json-min.js',
	$yui_path.'paginator/paginator-min.js',
	$yui_path.'dragdrop/dragdrop-min.js',
	$yui_path.'datasource/datasource-min.js',
	$yui_path.'autocomplete/autocomplete-min.js',
	$yui_path.'datatable/datatable.js',
		$yui_path.'calendar/calendar-min.js',

	$yui_path.'container/container-min.js',
	$yui_path.'menu/menu-min.js',
	'js/php.default.min.js',
	'js/common.js',
	'js/table_common.js',
	'js/edit_common.js',
	'js/search.js',
	'js/localize_calendar.js',
	'js/calendar_interval.js',
	'js/reports_calendar.js',
	'site.js.php'

);





$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);




if (isset($_REQUEST['view'])) {
	$valid_views=array('details','pages','hits','visitors');
	if (in_array($_REQUEST['view'], $valid_views))
		$_SESSION['state']['site']['view']=$_REQUEST['view'];

}
$smarty->assign('block_view',$_SESSION['state']['site']['view']);


if (isset($_REQUEST['pages_view'])) {
	$valid_views=array('general','hits','visitors');
	if (in_array($_REQUEST['view'], $valid_views))
		$_SESSION['state']['site']['pages']['view']=$_REQUEST['view'];

}

$smarty->assign('email_reminders_block_view',$_SESSION['state']['site']['email_reminders_block']);
$smarty->assign('favorites_block_view',$_SESSION['state']['site']['favorites_block']);





$smarty->assign('pages_block_view',$_SESSION['state']['site']['pages_block']);
$smarty->assign('hits_block_view',$_SESSION['state']['site']['hits_block']);




$smarty->assign('pages_view',$_SESSION['state']['site']['pages']['view']);
$smarty->assign('page_period',$_SESSION['state']['site']['pages']['period']);



$subject_id=$site_id;


$smarty->assign('site',$site);

$smarty->assign('parent','websites');
$smarty->assign('title', _('Website').': '.$site->data['Site Code']);


$tipo_filter=$_SESSION['state']['site']['pages']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['site']['pages']['f_value']);
$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>'Page code starting with  <i>x</i>','label'=>'Code'),
	'title'=>array('db_key'=>'code','menu_label'=>'Page title like  <i>x</i>','label'=>'Code'),

);
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);
$smarty->assign('table_type',$_SESSION['state']['site']['pages']['type']);

$elements_number=array('System'=>0, 'Info'=>0, 'Department'=>0, 'Family'=>0, 'Product'=>0, 'FamilyCategory'=>0, 'ProductCategory'=>0 );
$sql=sprintf("select count(*) as num,`Page Store Section Type` from  `Page Store Dimension` where `Page Site Key`=%d group by `Page Store Section Type`",$site->id);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
	$elements_number[$row['Page Store Section Type']]=number($row['num']);
}
$smarty->assign('page_section_elements_number',$elements_number);
$smarty->assign('page_section_elements',$_SESSION['state']['site']['pages']['elements']['section']);


$elements_number=array('Online'=>0, 'Offline'=>0 );
$sql=sprintf("select count(*) as num,`Page State` from  `Page Store Dimension` where `Page Site Key`=%d group by `Page State`",$site->id);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
	$elements_number[$row['Page State']]=number($row['num']);
}
$smarty->assign('page_state_elements_number',$elements_number);
$smarty->assign('page_state_elements',$_SESSION['state']['site']['pages']['elements']['state']);

$table_type_options=array(
	'list'=>array('mode'=>'list','label'=>_('List')),
	'thumbnails'=>array('mode'=>'thumbnails','label'=>_('Thumbnails')),
);
$smarty->assign('pages_table_type',$_SESSION['state']['site']['pages']['table_type']);
$smarty->assign('pages_table_type_label',$table_type_options[$_SESSION['state']['site']['pages']['table_type']]['label']);
$smarty->assign('pages_table_type_menu',$table_type_options);



$tipo_filter=$_SESSION['state']['site']['users']['f_field'];
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['site']['users']['f_value']);
$filter_menu=array(
	'customer'=>array('db_key'=>'user','menu_label'=>_('Customer'),'label'=>_('Customer')),
	'handle'=>array('db_key'=>'handle','menu_label'=>_('Handle starting with'),'label'=>_('Handle')),

);
$smarty->assign('filter_menu1',$filter_menu);
$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);


$tipo_filter=$_SESSION['state']['site']['queries']['f_field'];
$smarty->assign('filter2',$tipo_filter);
$smarty->assign('filter_value2',$_SESSION['state']['site']['queries']['f_value']);
$filter_menu=array(
	'query'=>array('db_key'=>'query','menu_label'=>_('Query'),'label'=>_('Query')),


);
$smarty->assign('filter_menu2',$filter_menu);
$smarty->assign('filter_name2',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu2',$paginator_menu);

$tipo_filter=$_SESSION['state']['site']['query_history']['f_field'];
$smarty->assign('filter3',$tipo_filter);
$smarty->assign('filter_value3',$_SESSION['state']['site']['query_history']['f_value']);
$filter_menu=array(
	'customer'=>array('db_key'=>'user','menu_label'=>_('Customer'),'label'=>_('Customer')),
	'handle'=>array('db_key'=>'handle','menu_label'=>_('Handle starting with'),'label'=>_('Handle')),
	'query'=>array('db_key'=>'query','menu_label'=>_('Query'),'label'=>_('Query'))


);
$smarty->assign('filter_menu3',$filter_menu);
$smarty->assign('filter_name3',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu3',$paginator_menu);


$tipo_filter=$_SESSION['state']['site']['history']['f_field'];
$smarty->assign('filter4',$tipo_filter);
$smarty->assign('filter_value4',$_SESSION['state']['site']['history']['f_value']);
$filter_menu=array(
		'notes'=>array('db_key'=>'abstract','menu_label'=>_('Records with abstract *<i>x</i>*'),'label'=>_('Abstract')),
	'author'=>array('db_key'=>'author','menu_label'=>_('Done by <i>x</i>*'),'label'=>_('Notes')),
//	'upto'=>array('db_key'=>'upto','menu_label'=>_('Records up to <i>n</i> days'),'label'=>_('Up to (days)')),
//	'older'=>array('db_key'=>'older','menu_label'=>_('Records older than  <i>n</i> days'),'label'=>_('Older than (days)')),

);
$smarty->assign('filter_name4',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu4',$filter_menu);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu4',$paginator_menu);


$tipo_filter=$_SESSION['state']['site']['email_reminders']['f_field'];
$smarty->assign('filter5',$tipo_filter);
$smarty->assign('filter_value5',$_SESSION['state']['site']['email_reminders']['f_value']);
$filter_menu=array(
	'subject_name'=>array('db_key'=>'subject_name','menu_label'=>_('Customer Name'),'label'=>_('Name')),

);
$smarty->assign('filter_name5',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu5',$filter_menu);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu5',$paginator_menu);


$tipo_filter=$_SESSION['state']['site']['email_reminders_customers']['f_field'];
$smarty->assign('filter5',$tipo_filter);
$smarty->assign('filter_value6',$_SESSION['state']['site']['email_reminders_customers']['f_value']);
$filter_menu=array(
	'name'=>array('db_key'=>'subject_name','menu_label'=>_('Customer Name'),'label'=>_('Name')),

);
$smarty->assign('filter_name6',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu6',$filter_menu);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu6',$paginator_menu);

$tipo_filter=$_SESSION['state']['site']['email_reminders_products']['f_field'];
$smarty->assign('filter7',$tipo_filter);
$smarty->assign('filter_value7',$_SESSION['state']['site']['email_reminders_products']['f_value']);
$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Product code'),'label'=>_('Code')),
);
$smarty->assign('filter_name7',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu7',$filter_menu);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu7',$paginator_menu);

$tipo_filter=$_SESSION['state']['site']['deleted_pages']['f_field'];
$smarty->assign('filter8',$tipo_filter);
$smarty->assign('filter_value8',$_SESSION['state']['site']['deleted_pages']['f_value']);
$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Page code'),'label'=>_('Code')),
);
$smarty->assign('filter_name8',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu8',$filter_menu);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu8',$paginator_menu);

$tipo_filter=$_SESSION['state']['site']['page_changelog']['f_field'];
$smarty->assign('filter9',$tipo_filter);
$smarty->assign('filter_value9',$_SESSION['state']['site']['page_changelog']['f_value']);
$filter_menu=array(
	'code'=>array('db_key'=>'page','menu_label'=>_('Page code'),'label'=>_('Code')),
);
$smarty->assign('filter_name9',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu9',$filter_menu);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu9',$paginator_menu);

$tipo_filter=$_SESSION['state']['site']['product_changelog']['f_field'];
$smarty->assign('filter10',$tipo_filter);
$smarty->assign('filter_value10',$_SESSION['state']['site']['product_changelog']['f_value']);
$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Product code'),'label'=>_('Code')),
);
$smarty->assign('filter_name10',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu10',$filter_menu);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu10',$paginator_menu);


$tipo_filter=$_SESSION['state']['site']['requests']['f_field'];
$smarty->assign('filter11',$tipo_filter);
$smarty->assign('filter_value11',$_SESSION['state']['site']['requests']['f_value']);
$filter_menu=array(
	'handle'=>array('db_key'=>'handle','menu_label'=>_('Handle starting with  <i>x</i>'),'label'=>_('Handle')),

);
$smarty->assign('filter_menu11',$filter_menu);
$smarty->assign('filter_name11',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu11',$paginator_menu);



$tipo_filter=$_SESSION['state']['site']['favorites_products']['f_field'];
$smarty->assign('filter12',$tipo_filter);
$smarty->assign('filter_value12',$_SESSION['state']['site']['favorites_products']['f_value']);
$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Product code'),'label'=>_('Code')),

);
$smarty->assign('filter_menu12',$filter_menu);
$smarty->assign('filter_name12',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu12',$paginator_menu);




$tipo_filter=$_SESSION['state']['site']['favorites_customers']['f_field'];
$smarty->assign('filter14',$tipo_filter);
$smarty->assign('filter_value14',$_SESSION['state']['site']['favorites_customers']['f_value']);
$filter_menu=array(
	'name'=>array('db_key'=>'name','menu_label'=>_('Customer name'),'label'=>_('Name')),

);
$smarty->assign('filter_menu14',$filter_menu);
$smarty->assign('filter_name14',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu14',$paginator_menu);





$report_index['products']['title']=_('Products');
$report_index['products']['reports']['back_to_stock']=array('title'=>_('Back to stock'),'url'=>'site_report_back_to_stock.php','snapshot'=>'');
$report_index['products']['reports']['out_of_stock']=array('title'=>_('Recently out of stock'),'url'=>'site_report_out_of_stock.php','snapshot'=>'');


$search_queries_block_view=$_SESSION['state']['site']['search_queries_block'];
$smarty->assign('search_queries_block_view',$search_queries_block_view);

$smarty->assign('report_index',$report_index);


$smarty->assign('back_in_stock_elements_email_reminders',$_SESSION['state']['site']['email_reminders']['elements']['back_in_stock']);
$smarty->assign('customers_back_in_stock_elements_email_reminders',$_SESSION['state']['site']['email_reminders_customers']['elements']['back_in_stock']);
$smarty->assign('products_back_in_stock_elements_email_reminders',$_SESSION['state']['site']['email_reminders_products']['elements']['back_in_stock']);


$page_flags_elements_data=array();
$sql=sprintf("select * from  `Site Flag Dimension` where `Site Key`=%d and `Site Flag Active`='Yes' ",$site->id);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {

	$page_flags_elements_data[$row['Site Flag Key']]=
		array(
		'key'=>$row['Site Flag Key'],
		'number'=>number($row['Site Flag Number Pages']),
		'label'=>$row['Site Flag Label'],
		'color'=>$row['Site Flag Color'],
		'img'=>'flag_'.strtolower($row['Site Flag Color']).'.png',

	);
}

$smarty->assign('page_flags_elements_data',$page_flags_elements_data);
$smarty->assign('page_flags_elements',$_SESSION['state']['site']['pages']['elements']['flags']);




$smarty->assign('page_elements_type',$_SESSION['state']['site']['pages']['elements_type']);


$smarty->assign('requests_elements',$_SESSION['state']['site']['requests']['elements']);
if (isset($_REQUEST['period'])) {
	$period=$_REQUEST['period'];

}else {
	$period=$_SESSION['state']['site']['period'];
}
if (isset($_REQUEST['from'])) {
	$from=$_REQUEST['from'];
}else {
	$from=$_SESSION['state']['site']['from'];
}

if (isset($_REQUEST['to'])) {
	$to=$_REQUEST['to'];
}else {
	$to=$_SESSION['state']['site']['to'];
}

list($period_label,$from,$to)=get_period_data($period,$from,$to);
$_SESSION['state']['page']['period']=$period;
$_SESSION['state']['page']['from']=$from;
$_SESSION['state']['page']['to']=$to;

$smarty->assign('from',$from);
$smarty->assign('to',$to);
$smarty->assign('period',$period);
$smarty->assign('period_label',$period_label);
$to_little_edian=($to==''?'':date("d-m-Y",strtotime($to)));
$from_little_edian=($from==''?'':date("d-m-Y",strtotime($from)));
$smarty->assign('to_little_edian',$to_little_edian);
$smarty->assign('from_little_edian',$from_little_edian);
$smarty->assign('calendar_id','sales');

$smarty->display('site.tpl');

?>
