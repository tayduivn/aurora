<?
include_once('common.php');
include_once('stock_functions.php');

$view_sales=$LU->checkRight(PROD_SALES_VIEW);
$view_stock=$LU->checkRight(PROD_STK_VIEW);
$create=$LU->checkRight(PROD_CREATE);
$modify=$LU->checkRight(PROD_MODIFY);
$view_sales=true;
$view_stock=true;
$create=true;
$modify=true;


$smarty->assign('view_sales',$view_sales);
$smarty->assign('view_stock',$view_stock);
$smarty->assign('create',$create);
$smarty->assign('modify',$modify);


if(isset($_REQUEST['edit']))
  $edit=$_REQUEST['edit'];
else
  $edit=$_SESSION['state']['departments']['edit'];



$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 'common.css',
		 'container.css',
		 'button.css',
		 'table.css'
		 );
$js_files=array(
		$yui_path.'yahoo-dom-event/yahoo-dom-event.js',
		$yui_path.'connection/connection-min.js',
		$yui_path.'json/json-min.js',
		$yui_path.'element/element-beta-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'dragdrop/dragdrop-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable-min.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		'js/common.js.php',
		'js/table_common.js.php',
		);

if($edit)
  $js_files[]='js/edit_departments.js.php';
 else{
   $js_files[]='js/search.js';
   $js_files[]='js/departments.js.php';
 }


$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);



$_SESSION['state']['assets']['page']='departments';
if(isset($_REQUEST['view'])){
  $valid_views=array('sales','general','stoke');
  if (in_array($_REQUEST['view'], $valid_views)) 
    $_SESSION['state']['departments']['view']=$_REQUEST['view'];

 }
$smarty->assign('view',$_SESSION['state']['departments']['view']);
$smarty->assign('show_details',$_SESSION['state']['departments']['details']);
$smarty->assign('show_percentages',$_SESSION['state']['departments']['percentages']);
$smarty->assign('avg',$_SESSION['state']['departments']['avg']);
$smarty->assign('period',$_SESSION['state']['departments']['period']);


//$sql="select id from product";
//$result =& $db->query($sql);

// include_once('classes/product.php');
// while($row=$result->fetchRow()){
//   $product= new product($row['id']);
//   $product->set_stock();
// }


 $table_title=_('Department List');
  $sql="select count(*) as numberof ,sum(`Product Department Total Invoiced Gross Amount`-`Product Department Total Invoiced Discount Amount`) as total_sales  from `Product Department Dimension`  ";
$result =mysql_query($sql);
if(!$departments=mysql_fetch_array($result, MYSQL_ASSOC))
  exit("Internal Error DEPS");



// //$smarty->assign('table_info',$departments['numberof'].' '.ngettext('Department','Departments',$departments['numberof']));
// $sql="select count(*) as numberof from product_group";
// $result =& $db->query($sql);
// $families=$result->fetchRow();
// $sql="select count(*) as numberof from product";
// $result =& $db->query($sql);
// $products=$result->fetchRow();





// $smarty->assign('stock_value',money($departments['stock_value']));
$smarty->assign('total_sales',money($departments['total_sales']));
$smarty->assign('departments',number($departments['numberof']));
// $smarty->assign('families',number($families['numberof']));
// $smarty->assign('products',number($products['numberof']));

$smarty->assign('parent','departments.php');
$smarty->assign('title', _('Product Departments'));
//$smarty->assign('total_departments',$departments['numberof']);
$smarty->assign('table_title',$table_title);

if($edit)
$smarty->display('edit_departments.tpl');
else
$smarty->display('departments.tpl');

?>