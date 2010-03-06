<?php
include_once('common.php');
include_once('class.Warehouse.php');
include_once('location_header_functions.php');



if(isset($_REQUEST['id']) and is_numeric($_REQUEST['id']) ){
  $warehouse_id=$_REQUEST['id'];

}else{
  $warehouse_id=$_SESSION['state']['warehouse']['id'];
}
$warehouse=new warehouse($warehouse_id);
if(!($user->can_view('warehouses') and in_array($warehouse_id,$user->warehouses)   ) ){
  header('Location: index.php');
   exit;
}
$modify=$user->can_edit('warehouses');
$smarty->assign('view_parts',$user->can_view('parts'));
$show_details=$_SESSION['state']['store']['details'];
$smarty->assign('show_details',$show_details);
get_header_info($user,$smarty);

$general_options_list=array();
if($modify)
  $general_options_list[]=array('tipo'=>'url','url'=>'edit_warehouse.php','label'=>_('Edit Warehouse'));
$general_options_list[]=array('tipo'=>'js','state'=>$show_details,'id'=>'details','label'=>($show_details?_('Hide Details'):_('Show Details')));
$general_options_list[]=array('tipo'=>'js','state'=>$show_details,'id'=>'details','label'=>($show_details?_('Hide Map'):_('Show Map')));


$smarty->assign('search_label',_('Locations'));
$smarty->assign('search_scope','locations');


$smarty->assign('general_options_list',$general_options_list);




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
		$yui_path.'datatable/datatable-min.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'common.js.php',
		'table_common.js.php',
		'calendar_common.js.php',
		'js/dropdown.js',
		'warehouse.js.php'
		);




$smarty->assign('parent','warehouses');
$smarty->assign('title', _('Warehouse'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

$smarty->assign('table_title',_('Location List'));


if($_SESSION['state']['locations']['table']['order']=='warehouse'){
$_SESSION['state']['locations']['table']['order']='code';
}

$tipo_filter=$_SESSION['state']['locations']['table']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['locations']['table']['f_value']);
$filter_menu=array(
		   'code'=>array('db_key'=>_('code'),'menu_label'=>'Location Code','label'=>'Code'),
		   );
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);

$tipo_filter=$_SESSION['state']['warehouse_areas']['table']['f_field'];
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['warehouse_areas']['table']['f_value']);
$filter_menu=array(
		   'code'=>array('db_key'=>_('code'),'menu_label'=>'Area Code','label'=>'Code'),
		   );
$smarty->assign('filter_menu1',$filter_menu);
$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);



$smarty->assign('warehouse',$warehouse);
//print_r($warehouse->get('areas'));

$smarty->assign('paginator_menu',$paginator_menu);


$smarty->display('warehouse.tpl');
?>