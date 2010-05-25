<?php
include_once('common.php');
include_once('class.Supplier.php');

$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 //$yui_path.'autocomplete/assets/skins/sam/autocomplete.css',

		 'common.css',
		 'button.css',
		 'container.css',
		 'table.css'
		 );
$js_files=array(

		$yui_path.'utilities/utilities.js',
		$yui_path.'connection/connection-debug.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'animation/animation-min.js',

		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'common.js.php',
		'table_common.js.php',
	    'supplier_products.js.php'
		);




$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);


$sql="select count(*) as total_products  from `Supplier Product Dimension` ";
$result =mysql_query($sql);
if(!$products=mysql_fetch_array($result, MYSQL_ASSOC))
  exit("Internal Error DEPS");



$smarty->assign('products',$products['total_products']);

$smarty->assign('view',$_SESSION['state']['supplier_products']['view']);
$smarty->assign('percentage',$_SESSION['state']['supplier_products']['percentage']);
$smarty->assign('period',$_SESSION['state']['supplier_products']['period']);


$smarty->assign('box_layout','yui-t0');
$smarty->assign('parent','suppliers');
$smarty->assign('title','Supplier Products List');


$tipo_filter=$_SESSION['state']['supplier_products']['table']['f_field'];
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value',$_SESSION['state']['supplier_products']['table']['f_value']);

$filter_menu=array( 
		   'code'=>array('db_key'=>_('code'),'menu_label'=>'Our Product Code','label'=>'Code'),
		   'sup_code'=>array('db_key'=>_('sup_code'),'menu_label'=>'Supplier Product Code','label'=>'Supplier Code'),
		   );
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);
$smarty->display('supplier_products.tpl');
?>