<?php
include_once 'class.Category.php';
include_once 'class.Warehouse.php';

include_once 'common.php';
include_once 'assets_header_functions.php';



if (!$user->can_view('warehouses')  ) {
	header('Location: index.php');
	exit;
}


$modify=$user->can_edit('warehouses');
if (!$modify) {
	header('Location: part_categories.php');
}



get_header_info($user,$smarty);
$smarty->assign('search_label',_('Parts'));
$smarty->assign('search_scope','parts');

$css_files=array(

	$yui_path.'reset-fonts-grids/reset-fonts-grids.css',
	$yui_path.'menu/assets/skins/sam/menu.css',
	$yui_path.'assets/skins/sam/autocomplete.css',
	$yui_path.'calendar/assets/skins/sam/calendar.css',
	'common.css',
	'css/container.css',
	'button.css',
	'table.css',
	'css/edit.css',
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
	'js/common.js',
	'js/table_common.js',
	'js/search.js',
	'js/edit_common.js',
	'js/edit_category_common.js'
);
$smarty->assign('css_files',$css_files);



if (isset($_REQUEST['id'])) {
	$category_key=$_REQUEST['id'];


} else {
	$category_key=$_SESSION['state']['part_categories']['category_key'];
}
$_SESSION['state']['part_categories']['category_key']=$category_key;
$_SESSION['state']['part_categories']['no_assigned_parts']['checked_all']=0;

if (!$category_key) {
	$category_key=0;

	$create_subcategory=true;

	$view='subcategory';
	$_SESSION['state']['part_categories']['edit']=$view;


	if (isset($_REQUEST['warehouse_id']) and is_numeric($_REQUEST['warehouse_id']) ) {
		$warehouse_id=$_REQUEST['warehouse_id'];

	} else {
		$warehouse_id=$_SESSION['state']['store']['id'];
	}


	$smarty->assign('category_key',false);

	//$general_options_list[]=array('tipo'=>'url','url'=>'part_categories.php?warehouse_id='.$warehouse_id.'&id=0','label'=>_('Exit Edit'));
	//$general_options_list[]=array('tipo'=>'js','id'=>'new_category','label'=>_('Add Category'));



}
else {


	$category=new Category($category_key);
	if (!$category->id) {
		header('Location: part_categories.php?id=0&error=cat_not_found');
		exit;

	}
	$category_key=$category->id;

	$view=$_SESSION['state']['part_categories']['edit'];
	if ($category->data['Category Max Deep']<=$category->data['Category Deep'] ) {
		$create_subcategory=false;
		if ( $_SESSION['state']['part_categories']['edit']=='subcategory') {
			$view='parts';
			$_SESSION['state']['part_categories']['edit']=$view;
		}

	}else {
		$create_subcategory=true;


	}



	$smarty->assign('category',$category);
	$smarty->assign('category_key',$category->id);

	// $tpl_file='part_category.tpl';
	$warehouse_id=$category->data['Category Warehouse Key'];


}

$warehouse=new Warehouse($warehouse_id);

if (!$warehouse->id) {

	//print_r($category);

	header('Location: index.php?error=warehouse_not_found');
	exit;

}


$smarty->assign('warehouse_id',$warehouse_id);

//$_SESSION['state']['categories']['subject']='Part';

//$_SESSION['state']['categories']['parent_key']=$category_key;
//$_SESSION['state']['categories']['subject_key']=false;
//$_SESSION['state']['categories']['store_key']=$store->id;


$js_files[]='edit_part_category.js.php?key='.$category_key;
$smarty->assign('js_files',$js_files);
$smarty->assign('category_key',$category_key);
$smarty->assign('create_subcategory',$create_subcategory);



$smarty->assign('edit',$view);
$smarty->assign('warehouse',$warehouse);
$smarty->assign('subject','Part');

$smarty->assign('parent','parts');
$smarty->assign('title', _('Edit Part Categories'));






$tipo_filter=$_SESSION['state']['part_categories']['subcategories']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['part_categories']['subcategories']['f_value']);

$filter_menu=array(
	'name'=>array('db_key'=>_('name'),'menu_label'=>_('Category Code'),'label'=>_('Name')),
);


$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);

$tipo_filter=$_SESSION['state']['part_categories']['no_assigned_parts']['f_field'];
$smarty->assign('filter3',$tipo_filter);
$smarty->assign('filter_value3',$_SESSION['state']['part_categories']['no_assigned_parts']['f_value']);
$filter_menu=array(
	'sku'=>array('db_key'=>'sku','menu_label'=>_("SKU"),'label'=>_("SKU")),

	'used_in'=>array('db_key'=>'used_in','menu_label'=>_('Used in <i>x</i>'),'label'=>_('Used in')),
	'supplied_by'=>array('db_key'=>'supplied_by','menu_label'=>_('Supplied by <i>x</i>'),'label'=>_('Supplied by')),
	'description'=>array('db_key'=>'description','menu_label'=>_('Part Description like <i>x</i>'),'label'=>_('Description')),

);
$smarty->assign('filter_menu3',$filter_menu);

$smarty->assign('filter_name3',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu3',$paginator_menu);



$tipo_filter=$_SESSION['state']['part_categories']['history']['f_field'];
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['part_categories']['history']['f_value']);
$filter_menu=array(
	'notes'=>array('db_key'=>'notes','menu_label'=>'Records with  notes *<i>x</i>*','label'=>_('Notes')),
	'author'=>array('db_key'=>'author','menu_label'=>'Done by <i>x</i>*','label'=>_('Notes')),
	'uptu'=>array('db_key'=>'upto','menu_label'=>'Records up to <i>n</i> days','label'=>_('Up to (days)')),
	'older'=>array('db_key'=>'older','menu_label'=>'Records older than  <i>n</i> days','label'=>_('Older than (days)')),
	'abstract'=>array('db_key'=>'abstract','menu_label'=>'Records with abstract','label'=>_('Abstract'))

);

$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu1',$filter_menu);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);


$tipo_filter=$_SESSION['state']['part_categories']['edit_parts']['f_field'];
$smarty->assign('filter2',$tipo_filter);
$smarty->assign('filter_value2',$_SESSION['state']['part_categories']['edit_parts']['f_value']);
$filter_menu=array(
	'sku'=>array('db_key'=>'sku','menu_label'=>_("SKU"),'label'=>_("SKU")),

	'used_in'=>array('db_key'=>'used_in','menu_label'=>_('Used in <i>x</i>'),'label'=>_('Used in')),
	'supplied_by'=>array('db_key'=>'supplied_by','menu_label'=>_('Supplied by <i>x</i>'),'label'=>_('Supplied by')),
	'description'=>array('db_key'=>'description','menu_label'=>_('Part Description like <i>x</i>'),'label'=>_('Description')),

);
$smarty->assign('filter_menu2',$filter_menu);

$smarty->assign('filter_name2',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu2',$paginator_menu);


$smarty->assign('filter4','used_in');
$smarty->assign('filter_value4','');
$filter_menu=array(
	'sku'=>array('db_key'=>'sku','menu_label'=>_('Part SKU'),'label'=>_('SKU')),
	'used_in'=>array('db_key'=>'used_in','menu_label'=>_('Used in'),'label'=>_('Used in')),
);
$smarty->assign('filter_menu4',$filter_menu);
$smarty->assign('filter_name4',$filter_menu['used_in']['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu4',$paginator_menu);

$smarty->assign('filter5','code');
$smarty->assign('filter_value5','');
$filter_menu=array(
	'code'=>array('db_key'=>'sku','menu_label'=>_('Category Code'),'label'=>_('Code')),
	'label'=>array('db_key'=>'used_in','menu_label'=>_('Category Label'),'label'=>_('Label')),
);
$smarty->assign('filter_menu5',$filter_menu);
$smarty->assign('filter_name5',$filter_menu['code']['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu5',$paginator_menu);






$smarty->display('edit_part_category.tpl');
?>
