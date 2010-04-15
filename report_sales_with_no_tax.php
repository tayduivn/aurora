<?php
include_once('common.php');
include_once('report_functions.php');
include_once('class.Store.php');

$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 //		 $yui_path.'datatable/assets/skins/sam/datatable.css',
		 'common.css',
		 'button.css',
		 'container.css',
		 'table.css',
		 'css/dropdown.css'

		 );
$js_files=array(

		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'js/php.default.min.js',
		'common.js.php',
		'table_common.js.php',
		'calendar_common.js.php',
		'report_sales_with_no_tax.js.php',


		'js/dropdown.js'

		);

$root_title=_('Sales Report');




$smarty->assign('parent','reports');
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

$tipo='all_invoices';
$store_keys=join(',',$user->stores);


include_once('report_dates.php');
$_SESSION['state']['report_sales_with_no_tax']['stores']=$store_keys;
$_SESSION['state']['report_sales_with_no_tax']['invoices']['from']=$from;
$_SESSION['state']['report_sales_with_no_tax']['invoices']['to']=$to;
$smarty->assign('period',$period);


$tipo_filter=$_SESSION['state']['report_sales_with_no_tax']['invoices']['f_field'];
$smarty->assign('filter_show0',$_SESSION['state']['report_sales_with_no_tax']['invoices']['f_show']);
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['report_sales_with_no_tax']['invoices']['f_value']);
$filter_menu=array(
		   'public_id'=>array('db_key'=>'public_id','menu_label'=>_('Invoice Number'),'label'=>_('Inv No')),
		   'customer'=>array('db_key'=>'customer','menu_label'=>_('Customer'),'label'=>_('Customer')),
		   'tax_number'=>array('db_key'=>'tax_number','menu_label'=>_('Tax Number'),'label'=>_('Tax No.')),
		   'send_to'=>array('db_key'=>'send_to','menu_label'=>_('Send to'),'label'=>_('Send to')),
		   
		   );
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);

$tipo_filter=$_SESSION['state']['report_sales_with_no_tax']['customers']['f_field'];
$smarty->assign('filter_show1',$_SESSION['state']['report_sales_with_no_tax']['customers']['f_show']);
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['report_sales_with_no_tax']['customers']['f_value']);
$filter_menu=array(
		   'customer'=>array('db_key'=>'customer','menu_label'=>_('Customer'),'label'=>_('Customer')),
		   'tax_number'=>array('db_key'=>'tax_number','menu_label'=>_('Tax Number'),'label'=>_('Tax Number')),
		   
		   );
$smarty->assign('filter_menu1',$filter_menu);
$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);

$smarty->display('report_sales_with_no_tax.tpl');
?>

