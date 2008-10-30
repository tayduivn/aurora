<?
include_once('common.php');

if(!$LU->checkRight(SUP_VIEW))
  exit;

include('string.php');
include('_contact.php');
include('telecom.php');
include('email.php');
include('address.php');
include('_supplier.php');




$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 'common.css',
		 'button.css',
		 'container.css',
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
		$yui_path.'calendar/calendar-min.js',
		'js/common.js.php',
		'js/table_common.js.php',
		'js/supplier.js.php'
		);
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

if(!isset($_REQUEST['id']) and is_numeric($_REQUEST['id']))
  $supplier_id=1;
else
  $supplier_id=$_REQUEST['id'];


$_SESSION['state']['supplier']['id']=$supplier_id;
$smarty->assign('supplier_id',$supplier_id);



$supplier_data= get_supplier_data($supplier_id);
$contact_data= get_contact_data($supplier_data['contact_id']);
$telecoms=get_telecoms($contact_data['id']);
$num_children=count($contact_data['child']);
if($num_children==1){
  $smarty->assign('contact',$contact_data['child'][0]['name']);
 }
elseif($num_children==2){
  $smarty->assign('contact',$contact_data['child'][1]['name'].' & '.$contact_data['child'][0]['name']);
 }






// $sql=sprintf("select count(*) as number from porden where tipo=0 and supplier_id=".$supplier_id);
// $result = $db->query($sql);
// if($tmp=$result->fetchRow())
//   $supplier['po_todo']=$tmp['number'];
// $sql=sprintf("select count(*) as number from porden where tipo=1 and supplier_id=".$supplier_id);
// $result = $db->query($sql);
// if($tmp=$result->fetchRow())
//   $supplier['po_submited']=$tmp['number'];
// $sql=sprintf("select count(*) as number from porden where tipo=2 and supplier_id=".$supplier_id);
// $result =& $db->query($sql);
// if($tmp=$result->fetchRow())
//   $supplier['po_received']=$tmp['number'];
// $sql=sprintf("select count(*) as number from porden where tipo=3 and supplier_id=".$supplier_id);
// $result =& $db->query($sql);
// if($tmp=$result->fetchRow())
//   $supplier['po_cancelled']=$tmp['number'];
// $supplier['pos']=$supplier['po_todo']+$supplier['po_submited']+$supplier['po_received']+$supplier['po_cancelled'];

// $sql=sprintf("select count(*) as number from product2supplier where supplier_id=".$supplier_id);
// $result =& $db->query($sql);
// if($tmp=$result->fetchRow())
//   $supplier['products']=$tmp['number'];

// $_SESSION['tables']['products_withsupplier'][4]=$supplier_id;
// $_SESSION['tables']['dn_list'][4]=$supplier_id;
// $_SESSION['tables']['po_list'][4]=$supplier_id;

//$_SESSION['tables']['purchase_orders'][4]=$supplier_id;


$smarty->assign('display',$_SESSION['state']['supplier']['display']);
$smarty->assign('box_layout','yui-t0');
$smarty->assign('parent','suppliers.php');
$smarty->assign('title','Supplier: '.$supplier_data['code']);

$smarty->assign('name',$supplier_data['name']);

$smarty->assign('id',$myconf['supplier_id_prefix'].sprintf("%05d",$supplier_data['id']));
 $smarty->assign('principal_address',display_full_address($contact_data['main_address']) );
$smarty->assign('telecoms',$telecoms);


$smarty->display('supplier.tpl');
?>