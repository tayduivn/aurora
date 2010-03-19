<?php
/*
 File: products.php 

 UI products page

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/
include_once('common.php');
include_once('assets_header_functions.php');

if(!$user->can_view('products'))
  exit();
$view_sales=$user->can_view('product sales');
$view_stock=$user->can_view('product stock');
$create=$user->can_create('products');
$modify=$user->can_edit('products');

$view_sales=$user->can_view('product sales');
$view_stock=$user->can_view('product stock');
$create=$user->can_create('products');
$modify=$user->can_edit('products');


$smarty->assign('view_parts',$user->can_view('parts'));
$smarty->assign('view_sales',$view_sales);
$smarty->assign('view_stock',$view_stock);
$smarty->assign('create',$create);
$smarty->assign('modify',$modify);

get_header_info($user,$smarty);


$q='';
if(isset($_REQUEST['search']) and $_REQUEST['search']!=''  ){
  // SEARCH!!!!!!!!!!!!
  $q=$_REQUEST['search'];
  //  print "$q";
  $sql=sprintf("select `Product ID` from `Product Dimension` where `Product Code`=%s ",prepare_mysql($q));
  $result=mysql_query($sql);
  if($found=mysql_fetch_array($result, MYSQL_ASSOC)){
    header('Location: product.php?pid='. $found['Product ID']);
    exit;
  }
  mysql_free_result($result);
  $sql=sprintf("select `Product Family Key`  from `Product family Dimension`  where `Product Family Code`='%s' ",prepare_mysql($q));
  $result=mysql_query($sql);
  if($found=mysql_fetch_array($result, MYSQL_ASSOC)){
    header('Location: family.php?id='. $found['product family key']);
    exit;
  }
 mysql_free_result($result);

  //do you mean
  $from_url=$_REQUEST['from_url'];
  
    
//   $_SESSION['tables']['pindex_list'][5]='p.code';
//   $_SESSION['tables']['pindex_list'][6]=$q;
  
 }


if(isset($_REQUEST['parent'])   ){
  // print_r($_REQUEST);
  $_SESSION['state']['products']['table']['parent']=$_REQUEST['parent'];
  
 }



$sql="select count(*) as total_products,sum(if(`Product Sales Type`!='Not For Sale',1,0)) as for_sale  from `Product Dimension` ";
$result=mysql_query($sql);
if(!$products=mysql_fetch_array($result, MYSQL_ASSOC))
  exit;
mysql_free_result($result);

$smarty->assign('box_layout','yui-t0');
$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
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
		'js/search.js',
		'products.js.php',
		'js/dropdown.js'
		);






$smarty->assign('parent','products');
$smarty->assign('title', _('Product Index'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

$product_home="Products Home";
$smarty->assign('home',$product_home);


$number_products=$products['for_sale'];
$smarty->assign('total_products',$number_products);
$tipo_filter=($q==''?$_SESSION['state']['products']['table']['f_field']:'code');
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value',($q==''?$_SESSION['state']['products']['table']['f_value']:addslashes($q)));
$filter_menu=array(
		   'code'=>array('db_key'=>'code','menu_label'=>'Product starting with  <i>x</i>','label'=>'Code'),
		   'description'=>array('db_key'=>'description','menu_label'=>'Product Description with <i>x</i>','label'=>'Description'),
		   );
$smarty->assign('filter_menu',$filter_menu);

$smarty->assign('filter_name',$filter_menu[$tipo_filter]['label']);
$smarty->assign('table_info',$number_products.'  '.ngettext('Product','Products',$number_products));
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu',$paginator_menu);
$smarty->assign('view',$_SESSION['state']['products']['view']);
$smarty->assign('table_title',_('Product List'));

$smarty->assign('currency',$myconf['currency_symbol']);

$smarty->assign('show_details',$_SESSION['state']['products']['details']);
$smarty->assign('show_percentages',$_SESSION['state']['products']['percentages']);
$smarty->assign('avg',$_SESSION['state']['products']['avg']);
$smarty->assign('period',$_SESSION['state']['products']['period']);


$smarty->display('products.tpl');
?>