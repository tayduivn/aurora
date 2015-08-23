<?php
/*
 File: store.php

 UI store page

 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2010, Inikoo

 Version 2.0
*/
include_once 'common.php';
include_once 'class.Store.php';


$page='store';
$smarty->assign('page',$page);
if (isset($_REQUEST['id']) and is_numeric($_REQUEST['id']) ) {
	$store_id=$_REQUEST['id'];

} else {
	$store_id=$_SESSION['state'][$page]['id'];
}


if (!($user->can_view('stores') and in_array($store_id,$user->stores)   ) ) {
	header('Location: index.php');
	exit;
}
if (!$user->can_edit('stores') ) {
	header('Location: store.php?error=cannot_edit');
	exit;
}


$store=new Store($store_id);
$_SESSION['state'][$page]['id']=$store->id;

$view_sales=$user->can_view('product sales');
$view_stock=$user->can_view('product stock');
$create=$user->can_create('product departments');

$smarty->assign('store',$store);

$smarty->assign('pages_view',$_SESSION['state']['store']['edit_pages']['view']);


$smarty->assign('view_parts',$user->can_view('parts'));

$smarty->assign('view_sales',$view_sales);
$smarty->assign('view_stock',$view_stock);
$smarty->assign('create',$create);





//$smarty->assign('general_options_list',$general_options_list);
$smarty->assign('search_label',_('Products'));
$smarty->assign('search_scope','products');


$css_files=array(
	$yui_path.'reset-fonts-grids/reset-fonts-grids.css',
	$yui_path.'menu/assets/skins/sam/menu.css',
	$yui_path.'button/assets/skins/sam/button.css',
	$yui_path.'assets/skins/sam/autocomplete.css',
	'css/common.css',
	'css/container.css',
	'css/button.css',
	'css/table.css',
	'css/edit.css',
//	'css/upload_files.css',
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
	$yui_path.'container/container-min.js',
	$yui_path.'menu/menu-min.js',
	'js/php.default.min.js',
	'js/jquery.min.js',
'js/common.js',
	'js/table_common.js',
	'js/search.js',
	'email_credential.js.php',
	'js/pages_common.js',
	'js/edit_common.js',
	'country_select.js.php',
	'js/edit_store.js',

);


$smarty->assign('edit',$_SESSION['state'][$page]['edit']);



$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);




$smarty->assign('store_key',$store->id);



$subject_id=$store_id;


$smarty->assign($page,$store);

$smarty->assign('parent','products');
$smarty->assign('title', $store->data['Store Name']);


$stores=array();
$sql=sprintf("select * from `Store Dimension` CD order by `Store Key`");

$res=mysql_query($sql);
$first=true;
while ($row=mysql_fetch_array($res)) {
	$stores[$row['Store Key']]=array('code'=>$row['Store Code'],'selected'=>0);
	if ($first) {
		$stores[$row['Store Key']]['selected']=1;
		$first=FALSE;
	}
}
mysql_free_result($res);





$smarty->assign('stores',$stores);





$tipo_filter=$_SESSION['state']['store']['edit_departments']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['store']['edit_departments']['f_value']);
$filter_menu=array(
	'name'=>array('db_key'=>'name','menu_label'=>_('Departments with name like *<i>x</i>*'),'label'=>_('Name')),
	'code'=>array('db_key'=>'code','menu_label'=>_('Departments with code like x</i>*'),'label'=>_('Code')),

);
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);


$tipo_filter=$_SESSION['state']['store']['history']['f_field'];
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['store']['history']['f_value']);
$filter_menu=array(
	'notes'=>array('db_key'=>'notes','menu_label'=>_('Records with  notes *<i>x</i>*'),'label'=>_('Notes')),
	'author'=>array('db_key'=>'author','menu_label'=>_('Done by <i>x</i>*'),'label'=>_('Notes')),
	'upto'=>array('db_key'=>'upto','menu_label'=>_('Records up to <i>n</i> days'),'label'=>_('Up to (days)')),
	'older'=>array('db_key'=>'older','menu_label'=>_('Records older than  <i>n</i> days'),'label'=>_('Older than (days)')),
	'abstract'=>array('db_key'=>'abstract','menu_label'=>_('Records with abstract'),'label'=>_('Abstract'))

);
$smarty->assign('filter_menu1',$filter_menu);

$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);

$tipo_filter=$_SESSION['state']['store']['edit_charges']['f_field'];
$smarty->assign('filter2',$tipo_filter);
$smarty->assign('filter_value2',$_SESSION['state']['store']['edit_charges']['f_value']);
$filter_menu=array(
	'description'=>array('db_key'=>'code','menu_label'=>_('Description'),'label'=>_('Description')),

);
$smarty->assign('filter_menu2',$filter_menu);
$smarty->assign('filter_name2',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu2',$paginator_menu);



$tipo_filter=$_SESSION['state']['store']['offers']['f_field'];
$smarty->assign('filter4',$tipo_filter);
$smarty->assign('filter_value4',$_SESSION['state']['store']['offers']['f_value']);
$filter_menu=array(
	'name'=>array('db_key'=>'name','menu_label'=>_('Offers with name like *<i>x</i>*'),'label'=>_('Name')),
	'code'=>array('db_key'=>'code','menu_label'=>_('Offers with code like x</i>*'),'label'=>_('Code')),
);
$smarty->assign('filter_menu4',$filter_menu);

$smarty->assign('filter_name4',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu4',$paginator_menu);

$tipo_filter=$_SESSION['state']['store']['edit_pages']['f_field'];
$smarty->assign('filter6',$tipo_filter);
$smarty->assign('filter_value6',$_SESSION['state']['store']['edit_pages']['f_value']);
$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Code'),'label'=>_('Code')),
	'header'=>array('db_key'=>'header','menu_label'=>_('Header'),'label'=>_('Header')),

);
$smarty->assign('filter_menu6',$filter_menu);
$smarty->assign('filter_name6',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu6',$paginator_menu);


$smarty->assign('show_history',$_SESSION['state']['store']['show_history']);


$number_of_sites=0;
$site_key=0;


$sql=sprintf("select count(*) as num, `Site Key` from `Site Dimension` where `Site Store Key`=%d ",
	$store->id);

$res=mysql_query($sql);
if ($row=mysql_fetch_assoc($res)) {
	$number_of_sites=$row['num'];
	if ($number_of_sites==1)
		$site_key=$row['Site Key'];

}

$smarty->assign('number_of_sites',$number_of_sites);
$smarty->assign('site_key',$site_key);


$credentials=array();
if ($store->get_email_credentials_data('Newsletters')) {
	foreach ($store->get_email_credentials_data('Newsletters') as $_key=>$_value) {
		foreach ($_value as $key=>$value) {
			$key=preg_replace('/\s/', '_', $key);
			$credentials[$key]=$value;
		}
	}
}
else {
	$credentials['Email_Address_Gmail']='';
	$credentials['Password_Gmail']='';
	$credentials['Email_Address_Other']='';
	$credentials['Login_Other']='';
	$credentials['Password_Other']='';
	$credentials['Incoming_Mail_Server']='';
	$credentials['Outgoing_Mail_Server']='';
	$credentials['Email_Address_Direct_Mail']='';
	$credentials['Email_Address_Amazon_Mail']='';

}


$smarty->assign('email_credentials',$credentials);


$session_data=base64_encode(json_encode(array(
			'label'=>array(

				'Last_Order'=>_('Last Order'),
				'State'=>_('State'),
				'Id'=>_('Id'),
				'Total'=>_('Total'),
				'Number'=>_('Number'),
				'Customer'=>_('Customer'),
				'Customers'=>_('Customers'),
				'Orders'=>_('Orders'),
				'Code'=>_('Code'),
				'Description'=>_('Description'),
				'Date'=>_('Date'),
				'Number'=>_('Number'),
				'ID'=>_('ID'),
				'Name'=>_('Name'),
				'Location'=>_('Location'),
				'Duration'=>_('Duration'),
				'Page'=>_('Page'),
				'of'=>_('of')
			),
			'state'=>array(
				'edit_departments'=>$_SESSION['state']['store']['edit_departments'],
				'edit_pages'=>$_SESSION['state']['store']['edit_pages'],
				'campaigns'=>$_SESSION['state']['store']['campaigns'],
				'edit_charges'=>$_SESSION['state']['store']['edit_charges'],
				'edit_offers'=>$_SESSION['state']['store']['edit_offers'],
							'history'=>$_SESSION['state']['store']['history']

			)
		)));
$smarty->assign('session_data',$session_data);


$smarty->display('edit_store.tpl');

?>