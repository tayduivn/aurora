<?
include_once('common.php');
include_once('classes/Order.php');
include_once('classes/Supplier.php');



if(isset($_REQUEST['id']) and is_numeric($_REQUEST['id'])){
   
   $po=new Order('po',$_REQUEST['id']);
   $po->load('supplier');

}else
   exit(_('Error the Purchese Order do not exist'));




$po_id = $po->id;
$_SESSION['state']['po']['id']=$po->id;

$_SESSION['state']['supplier']['id']=$po->data['supplier_id'];



//print_r($po->data);
$smarty->assign('po',$po->data);


$smarty->assign('title',$po->supplier->data['code']."<br/>"._('Purchase Order').' '.$po->data['id']." (".$po->data['status'].")");


if($po->data['items']==0)
  $_SESSION['state']['po']['items']['all_products']=true;
 else
   $_SESSION['state']['po']['items']['all_products']=false;


$_SESSION['state']['po']['status']=floor($po->data['status_id']*.1);



 $smarty->assign('show_all',$_SESSION['state']['po']['items']['all_products']);


$smarty->assign('parent','suppliers.php');
$smarty->assign('currency',$myconf['currency_symbol']);
$smarty->assign('decimal_point',$myconf['decimal_point']);


$tipo_filter=$_SESSION['state']['po']['items']['f_field'];
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['po']['items']['f_value']);

$filter_menu=array( 
		   'p.code'=>array('db_key'=>_('p.code'),'menu_label'=>'Our Product Code','label'=>'Code'),
		   'sup_code'=>array('db_key'=>_('sup_code'),'menu_label'=>'Supplier Product Code','label'=>'Supplier Code'),
		    );
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu',$paginator_menu);

$smarty->assign('date',date("d-m-Y"));
$smarty->assign('time',date("H:i"));


//create user list
$sql=sprintf("select id,alias,position_id from staff where active=1 order by alias ");

$res = $db->query($sql);
$i=0;
$num_cols=5;
$staff=array();
while($row=$res->fetchrow()){
  $staff[]=array('mod'=>fmod($i,$num_cols),'alias'=>$row['alias'],'id'=>$row['id'],'position_id'=>$row['position_id']);
  $i++;
 }
$smarty->assign('staff',$staff);
$smarty->assign('staff_cols',$num_cols-1);


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
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'js/common.js.php',
		'js/table_common.js.php',
	       	'js/porder.js.php'
		);
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);


$smarty->display('porder.tpl');
?>