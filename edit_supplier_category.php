<?php
include_once 'class.Category.php';

include_once 'common.php';



if (!$user->can_view('suppliers')  ) {
	header('Location: index.php');
	exit;
}

if (isset($_REQUEST['id'])) {
	$category_key=$_REQUEST['id'];


} else {
	header('Location: supplier_categories.php?e=no_cate_key');
}


$category=new Category($category_key);
if (!$category->id) {
	header('Location: supplier_categories.php?id=0&error=cat_not_found');
	exit;
}
if ($category->data['Category Subject']!='Supplier') {
	header('Location: index.php?error_no_wrong_category_id');
	exit;
}


$modify=$user->can_edit('suppliers');
if (!$modify) {
	header('Location: supplier_categories.php');
}



$smarty->assign('search_label',_('Suppliers'));
$smarty->assign('search_scope','suppliers');



$css_files=array(

	$yui_path.'reset-fonts-grids/reset-fonts-grids.css',
	$yui_path.'menu/assets/skins/sam/menu.css',
	$yui_path.'assets/skins/sam/autocomplete.css',
	$yui_path.'calendar/assets/skins/sam/calendar.css',
	'css/common.css',
	'css/container.css',
	'css/button.css',
	'css/table.css',
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
	$yui_path.'animation/animation-min.js',
	'js/jquery.min.js',
'js/common.js',
	'js/table_common.js',
	'js/search.js',
	'js/edit_common.js',
	'js/edit_category_common.js',
	'edit_supplier_category.js.php?key='.$category_key

);
$smarty->assign('css_files',$css_files);





$_SESSION['state']['supplier_categories']['no_assigned_suppliers']['checked_all']=0;








$category_key=$category->id;

$view=$_SESSION['state']['supplier_categories']['edit'];


if ($category->data['Category Max Deep']<=$category->data['Category Deep'] ) {
	$create_subcategory=false;
	if ( $_SESSION['state']['supplier_categories']['edit']=='subcategory') {
		$view='suppliers';
		$_SESSION['state']['supplier_categories']['edit']=$view;
	}

}else {
	$create_subcategory=true;


}



$smarty->assign('category',$category);
$smarty->assign('category_key',$category->id);


$order=$_SESSION['state']['supplier_categories']['subcategories']['order'];
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
	$prev['link']='edit_supplier_category.php?id='.$row['id'];
	$prev['title']=$row['name'];
	$smarty->assign('prev',$prev);
}
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
	$next['link']='edit_supplier_category.php?id='.$row['id'];
	$next['title']=$row['name'];
	$smarty->assign('next',$next);
}
mysql_free_result($result);


$smarty->assign('show_history',$_SESSION['state']['supplier_categories']['show_history']);


$smarty->assign('supplier_id',0);
$smarty->assign('js_files',$js_files);
$smarty->assign('category_key',$category_key);
$smarty->assign('create_subcategory',$create_subcategory);



$smarty->assign('edit',$view);
$smarty->assign('subject','Supplier');

$smarty->assign('parent','suppliers');
$smarty->assign('title', _('Supplier Category').' '.$category->data['Category Code'].' ('._('Editing').')');






$tipo_filter=$_SESSION['state']['supplier_categories']['subcategories']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['supplier_categories']['subcategories']['f_value']);

$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Category Code'),'label'=>_('Name')),
);


$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);

$tipo_filter=$_SESSION['state']['supplier_categories']['no_assigned_suppliers']['f_field'];
$smarty->assign('filter3',$tipo_filter);
$smarty->assign('filter_value3',$_SESSION['state']['supplier_categories']['no_assigned_suppliers']['f_value']);
$filter_menu=array(
		   'code'=>array('db_key'=>'code','menu_label'=>_('Suppliers with code starting with  <i>x</i>'),'label'=>_('Code')),
		   'name'=>array('db_key'=>'name','menu_label'=>_('Suppliers which name starting with <i>x</i>'),'label'=>_('Name')),
		   'low'=>array('db_key'=>'low','menu_label'=>_('Suppliers with more than <i>n</i> low stock products'),'label'=>_('Low')),
		   'outofstock'=>array('db_key'=>'outofstock','menu_label'=>_('Suppliers with more than <i>n</i> products out of stock'),'label'=>_('Out of Stock')),

);
$smarty->assign('filter_menu3',$filter_menu);

$smarty->assign('filter_name3',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu3',$paginator_menu);



$tipo_filter=$_SESSION['state']['supplier_categories']['history']['f_field'];
$smarty->assign('filter1',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['supplier_categories']['history']['f_value']);
$filter_menu=array(
		'notes'=>array('db_key'=>'abstract','menu_label'=>_('Records with abstract *<i>x</i>*'),'label'=>_('Abstract')),
	'author'=>array('db_key'=>'author','menu_label'=>_('Done by <i>x</i>*'),'label'=>_('Notes')),
//	'upto'=>array('db_key'=>'upto','menu_label'=>_('Records up to <i>n</i> days'),'label'=>_('Up to (days)')),
//	'older'=>array('db_key'=>'older','menu_label'=>_('Records older than  <i>n</i> days'),'label'=>_('Older than (days)')),

);

$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$smarty->assign('filter_menu1',$filter_menu);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);


$tipo_filter=$_SESSION['state']['supplier_categories']['edit_suppliers']['f_field'];
$smarty->assign('filter2',$tipo_filter);
$smarty->assign('filter_value2',$_SESSION['state']['supplier_categories']['edit_suppliers']['f_value']);
$filter_menu=array(
		   'code'=>array('db_key'=>'code','menu_label'=>_('Suppliers with code starting with  <i>x</i>'),'label'=>_('Code')),
		   'name'=>array('db_key'=>'name','menu_label'=>_('Suppliers which name starting with <i>x</i>'),'label'=>_('Name')),
		   'low'=>array('db_key'=>'low','menu_label'=>_('Suppliers with more than <i>n</i> low stock products'),'label'=>_('Low')),
		   'outofstock'=>array('db_key'=>'outofstock','menu_label'=>_('Suppliers with more than <i>n</i> products out of stock'),'label'=>_('Out of Stock')),

);
$smarty->assign('filter_menu2',$filter_menu);

$smarty->assign('filter_name2',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu2',$paginator_menu);


$smarty->assign('filter4','used_in');
$smarty->assign('filter_value4','');
$filter_menu=array(
		   'code'=>array('db_key'=>'code','menu_label'=>_('Suppliers with code starting with  <i>x</i>'),'label'=>_('Code')),
		   'name'=>array('db_key'=>'name','menu_label'=>_('Suppliers which name starting with <i>x</i>'),'label'=>_('Name'))
		 
);
$smarty->assign('filter_menu4',$filter_menu);
$smarty->assign('filter_name4',$filter_menu['code']['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu4',$paginator_menu);

$smarty->assign('filter5','code');
$smarty->assign('filter_value5','');
$filter_menu=array(
	'code'=>array('db_key'=>'code','menu_label'=>_('Category Code'),'label'=>_('Code')),
	'label'=>array('db_key'=>'label','menu_label'=>_('Category Label'),'label'=>_('Label')),
);
$smarty->assign('filter_menu5',$filter_menu);
$smarty->assign('filter_name5',$filter_menu['code']['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu5',$paginator_menu);

$elements_number=array('Changes'=>0,'Assign'=>0);
$sql=sprintf("select count(*) as num ,`Type` from  `Supplier Category History Bridge` where  `Category Key`=%d group by  `Type`",$category->id);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
	$elements_number[$row['Type']]=number($row['num']);
}


$smarty->assign('history_elements_number',$elements_number);
$smarty->assign('history_elements',$_SESSION['state']['supplier_categories']['history']['elements']);

$smarty->display('edit_supplier_category.tpl');
?>
