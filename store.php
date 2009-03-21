<?

include_once('common.php');
include_once('stock_functions.php');
include_once('classes/Store.php');

$view_sales=$LU->checkRight(PROD_SALES_VIEW);
$view_stock=$LU->checkRight(PROD_STK_VIEW);
$create=$LU->checkRight(PROD_CREATE);
$modify=$LU->checkRight(PROD_MODIFY);



$smarty->assign('view_sales',$view_sales);
$smarty->assign('view_stock',$view_stock);
$smarty->assign('create',$create);
$smarty->assign('modify',$modify);


if(isset($_REQUEST['edit']))
  $edit=$_REQUEST['edit'];
else
  $edit=$_SESSION['state']['store']['edit'];



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
		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
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
  $js_files[]='js/edit_store.js.php';
 else{
   $js_files[]='js/search.js';
   $js_files[]='js/store.js.php';
 }


$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

if(isset($_REQUEST['id'])){
  $_SESSION['state']['store']['store_id']=$_REQUEST['id'];
 }

$store=new Store($_SESSION['state']['store']['store_id']);
  

$_SESSION['state']['assets']['page']='store';
if(isset($_REQUEST['view'])){
  $valid_views=array('sales','general','stoke');
  if (in_array($_REQUEST['view'], $valid_views)) 
    $_SESSION['state']['store']['view']=$_REQUEST['view'];

 }
$smarty->assign('view',$_SESSION['state']['store']['view']);
$smarty->assign('show_details',$_SESSION['state']['store']['details']);
$smarty->assign('show_percentages',$_SESSION['state']['store']['percentages']);
$smarty->assign('avg',$_SESSION['state']['store']['avg']);
$smarty->assign('period',$_SESSION['state']['store']['period']);


//$sql="select id from product";
//$result =& $db->query($sql);

// include_once('classes/product.php');
// while($row=$result->fetchRow()){
//   $product= new product($row['id']);
//   $product->set_stock();
// }




// //$smarty->assign('table_info',$store['numberof'].' '.ngettext('Department','Store',$store['numberof']));
// $sql="select count(*) as numberof from product_group";
// $result =& $db->query($sql);
// $families=$result->fetchRow();
// $sql="select count(*) as numberof from product";
// $result =& $db->query($sql);
// $products=$result->fetchRow();





// $smarty->assign('stock_value',money($store['stock_value']));
//$smarty->assign('total_sales',money($store['total_sales']));
$smarty->assign('store',$store);
// $smarty->assign('families',number($families['numberof']));
// $smarty->assign('products',number($products['numberof']));

$smarty->assign('parent','store.php');
$smarty->assign('title', $store->data['Store Name']);
//$smarty->assign('total_store',$store['numberof']);
//$smarty->assign('table_title',$table_title);

if($edit){

$stores=array();
$sql=sprintf("select * from `Store Dimension` CD order by `Store Key`");

$res=mysql_query($sql);
 $first=true;
while($row=mysql_fetch_array($res)){
    $stores[$row['Store Key']]=array('code'=>$row['Store Code'],'selected'=>0);
    if($first){
      $stores[$row['Store Key']]['selected']=1;
      $first=FALSE;
    }
}
 $smarty->assign('stores',$stores);
$smarty->display('edit_store.tpl');
 }else
$smarty->display('store.tpl');

?>