<?php
/*
 File: departments.php 

 UI department page

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0

 Created: 20-04-2009 17:38

*/
include_once('common.php');
include_once('assets_header_functions.php');

if(!$user->can_view('product departments'))
  exit();


$view_sales=$user->can_view('product sales');
$view_stock=$user->can_view('product stock');
$create=$user->can_create('product departments');
$modify=$user->can_edit('product departments');



$smarty->assign('view_sales',$view_sales);
$smarty->assign('view_stock',$view_stock);
$smarty->assign('create',$create);
$smarty->assign('modify',$modify);


if(isset($_REQUEST['edit']))
  $edit=$_REQUEST['edit'];
else
  $edit=$_SESSION['state']['departments']['edit'];

get_header_info($user,$smarty);

$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 'common.css',
		 'container.css',
		 'button.css',
		 'table.css',
		  'css/dropdown.css'
		 );
$js_files=array(
		
		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'dragdrop/dragdrop-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable-min.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		'common.js.php',
		'table_common.js.php',
		 'js/dropdown.js'
		);

if($edit){
  $js_files[]='js/edit_common.js';
  $js_files[]='edit_departments.js.php';
 } else{
   $js_files[]='js/search.js';
   $js_files[]='departments.js.php';
 }


$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);



$_SESSION['state']['assets']['page']='departments';
//if(isset($_REQUEST['view'])){
//  $valid_views=array('sales','general','stoke');
//  if (in_array($_REQUEST['view'], $valid_views)) 
//    $_SESSION['state']['departments']['view']=$_REQUEST['view'];
//
// }
$smarty->assign('view',$_SESSION['state']['departments']['view']);
$smarty->assign('show_details',$_SESSION['state']['departments']['details']);
$smarty->assign('show_percentages',$_SESSION['state']['departments']['percentages']);
$smarty->assign('avg',$_SESSION['state']['departments']['avg']);
$smarty->assign('period',$_SESSION['state']['departments']['period']);


//$sql="select id from product";
//$result=mysql_query($sql);

// include_once('class.product.php');
// while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
//   $product= new product($row['id']);
//   $product->set_stock();
// }




$smarty->assign('parent','products');
$smarty->assign('title', _('Departments'));
//$smarty->assign('total_stores',$stores['numberof']);
//$smarty->assign('table_title',$table_title);



$departments=array();
$sql=sprintf("select count(*) as num from `Product Department Dimension` ");

$res=mysql_query($sql);
if($row=mysql_fetch_array($res)){
  $departments=$row['num'];
 }
 
$smarty->assign('departments',$departments);

$q='';
$tipo_filter=($q==''?$_SESSION['state']['departments']['table']['f_field']:'code');
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value',($q==''?$_SESSION['state']['departments']['table']['f_value']:addslashes($q)));
$filter_menu=array(
		   'code'=>array('db_key'=>'code','menu_label'=>'Store starting with  <i>x</i>','label'=>'Code'),
		   'description'=>array('db_key'=>'description','menu_label'=>'Store Description with <i>x</i>','label'=>'Description'),
		   );
$smarty->assign('filter_menu0',$filter_menu);

$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);


if($edit){
$smarty->display('edit_departments.tpl');
 }else
$smarty->display('departments.tpl');

?>