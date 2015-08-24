<?php
/*
 File: customers.php

 UI customers page

 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Inikoo

 Version 2.0
*/
include_once 'common.php';
include_once 'class.Store.php';




if (!$user->can_view('customers')) {
	header('Location: index.php');
	exit();
}

if (isset($_REQUEST['store']) and is_numeric($_REQUEST['store']) ) {
	$store_key=$_REQUEST['store'];

} else {
	exit("no store key");

}

if (!($user->can_view('stores') and in_array($store_key,$user->stores)   ) ) {
	header('Location: index.php');
	exit;
}

$store=new Store($store_key);
if ($store->id) {
	$_SESSION['state']['customers']['store']=$store->id;
} else {
	header('Location: index.php?error=store_not_found');
	exit();
}

//print_r($_SESSION['state']['customers']);

$currency=$store->data['Store Currency Code'];
$currency_symbol=currency_symbol($currency);



$smarty->assign('store',$store);

$smarty->assign('store_key',$store->id);
$modify=$user->can_edit('customers');

if (preg_match('/es_es/i',  $lc_messages_locale)) {

	$overview_all_contacts_text=sprintf("Tenemos %s contactos de los cuales %s son contactos activos. La semana pasada adquirimos %s nuevos clientes. Ellos representan un %s de todos los contactos activos."
		,$store->get('Contacts')
		,$store->get('Active Contacts')
		,percentage($store->data['Store Active Contacts'],$store->data['Store Contacts'])
		,percentage($store->data['Store New Contacts'],$store->data['Store Active Contacts'])
	);
} else {
	$s='';
	if ($store->data['Store Total Users'])
		$s=sprintf("%s (%s) customers have visited the website.", number($store->data['Store Contacts Who Visit Website']) ,percentage($store->data['Store Contacts Who Visit Website'],$store->data['Store Contacts']));

	$overview_all_contacts_text=sprintf("We have had %s contacts so far, %s of them still active (%s). Over the last week we acquired  %s new %s representing  %s of the total active customer base. %s",
		$store->get('Contacts')
		,$store->get('Active Contacts')
		,percentage($store->data['Store Active Contacts'],$store->data['Store Contacts'])

		,$store->get('New Contacts')
		,ngettext('customer','customers',$store->data['Store New Contacts'])
		,percentage($store->data['Store New Contacts'],$store->data['Store Active Contacts'])
		,$s
	);
}
$smarty->assign('overview_all_contacts_text',$overview_all_contacts_text);


if (preg_match('/es_es/i',  $lc_messages_locale)) {

	$overview_contacts_with_orders_text=sprintf("Tenemos %s contactos de los cuales %s son contactos activos. La semana pasada adquirimos %s nuevos clientes. Ellos representan un %s de todos los contactos activos."
		,$store->get('Contacts')
		,$store->get('Active Contacts')
		,percentage($store->data['Store Active Contacts'],$store->data['Store Contacts'])
		,percentage($store->data['Store New Contacts'],$store->data['Store Active Contacts'])
	);
} else {
	$s='';
	//    if ($store->data['Store Total Users'])
	//      $s=sprintf("%d (%s) customer are registered in the website.", $store->data['Store Total Users'] ,percentage($store->data['Store Total Users'],$store->data['Store Active Contacts']));

	$overview_contacts_with_orders_text=sprintf("We have had %s contacts with orders so far, %s of them active (%s). Over the last week we acquired  %s new %s representing  %s of the total active customer base. %s",
		$store->get('Contacts With Orders')
		,$store->get('Active Contacts With Orders')
		,percentage($store->data['Store Active Contacts With Orders'],$store->data['Store Contacts With Orders'])

		,$store->get('New Contacts With Orders')
		,ngettext('customer','customers',$store->data['Store New Contacts With Orders'])
		,percentage($store->data['Store New Contacts With Orders'],$store->data['Store Active Contacts With Orders'])
		,$s
	);
}

$smarty->assign('overview_contacts_with_orders_text',$overview_contacts_with_orders_text);

$smarty->assign('modify',$modify);


$smarty->assign('search_label',_('Customers'));
$smarty->assign('search_scope','customers');



$css_files=array(
	$yui_path.'reset-fonts-grids/reset-fonts-grids.css',
	$yui_path.'menu/assets/skins/sam/menu.css',
	$yui_path.'assets/skins/sam/autocomplete.css',
	'css/common.css',
	'css/container.css',
	'css/edit.css',
	'css/button.css',
	'css/table.css',
	//'theme.css.php'

);

$js_files=array(
	$yui_path.'utilities/utilities.js',
	$yui_path.'json/json-min.js',
	$yui_path.'paginator/paginator-min.js',
	$yui_path.'datasource/datasource-min.js',
	$yui_path.'autocomplete/autocomplete-min.js',
	$yui_path.'datatable/datatable.js',
	$yui_path.'container/container-min.js',
	$yui_path.'menu/menu-min.js',
	'js/jquery.min.js',
	'js/common.js',
	'js/table_common.js',
	'js/search.js',
	'js/edit_common.js',

	'js/customers_common.js',
	'js/export_common.js',
	'customers.js.php?store_key='.$store->id

);


//$smarty->assign('advanced_search',$_SESSION['state']['customers']['advanced_search']);


$smarty->assign('parent','customers');
$smarty->assign('title', _('Customers').' ('.$store->data['Store Code'].')');
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);




$smarty->assign('view',$_SESSION['state']['customers']['customers']['view']);

$tipo_filter=$_SESSION['state']['customers']['customers']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['customers']['customers']['f_value']);

$filter_menu=array(
	'customer name'=>array('db_key'=>'customer name','menu_label'=>_('Customer Name'),'label'=>_('Name')),
	'postcode'=>array('db_key'=>'postcode','menu_label'=>_('Customer Postcode'),'label'=>_('Postcode')),
	'country'=>array('db_key'=>'country','menu_label'=>_('Customer Country'),'label'=>_('Country')),
	'min'=>array('db_key'=>'min','menu_label'=>_('Mininum Number of Orders'),'label'=>_('Min No Orders')),
	'max'=>array('db_key'=>'min','menu_label'=>_('Maximum Number of Orders'),'label'=>_('Max No Orders')),
	'last_more'=>array('db_key'=>'last_more','menu_label'=>_('Last order more than (days)'),'label'=>_('Last Order >(Days)')),
	'last_less'=>array('db_key'=>'last_more','menu_label'=>_('Last order less than (days)'),'label'=>_('Last Order <(Days)')),
	'maxvalue'=>array('db_key'=>'maxvalue','menu_label'=>_('Balance less than').' '.$currency_symbol  ,'label'=>_('Balance')." <($currency_symbol)"),
	'minvalue'=>array('db_key'=>'minvalue','menu_label'=>_('Balance more than').' '.$currency_symbol  ,'label'=>_('Balance')." >($currency_symbol)"),
);


$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);



$tipo_filter1=$_SESSION['state']['customers']['pending_orders']['f_field'];
$smarty->assign('filter1',$tipo_filter1);
$smarty->assign('filter_value1',($_SESSION['state']['customers']['pending_orders']['f_value']));
$filter_menu1=array(
	'public_id'=>array('db_key'=>'public_id','menu_label'=>_('Order Number starting with <i>x</i>'),'label'=>_('Order Number')),
);
$smarty->assign('filter_menu1',$filter_menu1);
$smarty->assign('filter_name1',$filter_menu1[$tipo_filter1]['label']);
$paginator_menu1=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu1);


$elements_number=array('InProcessbyCustomer'=>0,'InProcess'=>0,'SubmittedbyCustomer'=>0,'InWarehouse'=>0,'Packed'=>0);
$sql=sprintf("select count(*) as num,`Order Current Dispatch State` from  `Order Dimension` where  `Order Store Key`=%d  group by `Order Current Dispatch State` ",$store_key);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
	$elements_number[preg_replace('/\s/','',$row['Order Current Dispatch State'])]=$row['num'];
}

$sql=sprintf("select count(*) as num  from  `Order Dimension` where  `Order Store Key`=%d  and `Order Current Dispatch State` in ('Ready to Pick','Picking & Packing','Ready to Ship') ",$store_key);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
	$elements_number['InWarehouse']=$row['num'];
}


$smarty->assign('elements_number',$elements_number);
$smarty->assign('elements',$_SESSION['state']['customers']['pending_orders']['elements']);



$elements_number=array('Send'=>0,'ToSend'=>0);
$sql=sprintf("select count(*) as num,`Send Post Status` from  `Customer Send Post` group by `Send Post Status`");
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
	$_key=preg_replace('/ /','',$row['Send Post Status']);

	if (in_array($_key,array('Send','ToSend')))
		$elements_number[$_key]=$row['num'];
}

//print_r($elements_number);
$smarty->assign('pending_post_elements_number',$elements_number);
$smarty->assign('pending_post_elements',$_SESSION['state']['customers']['pending_post']['elements']);


$smarty->assign('block_view',$_SESSION['state']['customers']['block_view']);

$smarty->assign('orders_type',$_SESSION['state']['customers']['customers']['orders_type']);
$smarty->assign('elements_activity',$_SESSION['state']['customers']['customers']['elements']['activity']);
$smarty->assign('elements_level_type',$_SESSION['state']['customers']['customers']['elements']['level_type']);
$smarty->assign('elements_location',$_SESSION['state']['customers']['customers']['elements']['location']);
$smarty->assign('elements_customers_elements_type',$_SESSION['state']['customers']['customers']['elements_type']);

$tipo_filter=$_SESSION['state']['customers']['pending_post']['f_field'];
$smarty->assign('filter2',$tipo_filter);
$smarty->assign('filter_value2',$_SESSION['state']['customers']['pending_post']['f_value']);
$filter_menu=array(
	'name'=>array('db_key'=>'name','menu_label'=>_('Customer Name'),'label'=>_('Name')),
	'postcode'=>array('db_key'=>'postcode','menu_label'=>_('Customer Postcode'),'label'=>_('Postcode')),
	'country'=>array('db_key'=>'country','menu_label'=>_('Customer Country'),'label'=>_('Country')),

);
$smarty->assign('filter_menu2',$filter_menu);
$smarty->assign('filter_name2',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu2',$paginator_menu);
include 'customers_export_common.php';




$branch=array(array('label'=>'','icon'=>'home','url'=>'index.php'));
if ( $user->get_number_stores()>1) {
	$branch[]=array('label'=>_('Customers'),'icon'=>'dot-circle-o','url'=>'customers_server.php');
}


$left_buttons=array();
if ($user->stores>1) {

	
	

	list($prev_key,$next_key)=get_prev_next($store->id,$user->stores);
	
	$sql=sprintf("select `Store Code` from `Store Dimension` where `Store Key`=%d",$prev_key);
	$res=mysql_query($sql);
	if($row=mysql_fetch_assoc($res)){
	    $prev_title=_('Customers').' '.$row['Store Code'];
	}else{$prev_title='';}
	$sql=sprintf("select `Store Code` from `Store Dimension` where `Store Key`=%d",$next_key);
	$res=mysql_query($sql);
	if($row=mysql_fetch_assoc($res)){
	    $next_title=_('Customers').' '.$row['Store Code'];
	}else{$next_title='';}
	

	$left_buttons[]=array('icon'=>'arrow-left','title'=>$prev_title,'url'=>'customers.php?store='.$prev_key);
	$left_buttons[]=array('icon'=>'arrow-up','title'=>_('Customers per store'),'url'=>'customers_server.php');

	$left_buttons[]=array('icon'=>'arrow-right','title'=>$next_title,'url'=>'customers.php?store='.$next_key);
}


$right_buttons=array();
$right_buttons[]=array('icon'=>'cog','title'=>_('Settings'),'url'=>'customer_store_configuration.php?store=',$store->id);
$right_buttons[]=array('icon'=>'edit','title'=>_('Edit customers'),'url'=>'edit_customers.php?store=',$store->id);
$right_buttons[]=array('icon'=>'plus','title'=>_('New customer'),'id'=>"new_customer");

$_content=array(
	'branch'=>$branch
	,

	'section_links'=>array(
		array('label'=>_('Statistics'),'icon'=>'line-chart','title'=>'','url'=>'customers_stats.php?store='.$store->id),
		array('label'=>_('Categories'),'icon'=>'cubes','title'=>'','url'=>'customer_categories.php?id=0&store_id='.$store->id),
		array('label'=>_('Lists'),'icon'=>'list','title'=>'','url'=>'customers_lists.php?store='.$store->id),
		array('label'=>_('Pending orders'),'icon'=>'clock-o','title'=>'','url'=>'store_pending_orders.php?id='.$store->id),
	),
	'left_buttons'=>$left_buttons,
	'right_buttons'=>$right_buttons,
	'title'=>_('Customers').' '.$store->get('Store Code'),
	'search'=>array('show'=>true,'placeholder'=>_('Search customers'))

);


$smarty->assign('content',$_content);

$smarty->display('customers.tpl');
?>
