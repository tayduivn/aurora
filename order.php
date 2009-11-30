<?php
include_once('common.php');
include_once('class.Order.php');
if(!$user->can_view('orders')){
  header('Location: index.php');
   exit;
}
  
$new=false;
if(isset($_REQUEST['new']))
  $new=true;

if($new){
  
  if(isset($_REQUEST['customer_key']) and is_numeric($_REQUEST['customer_key']) ){
    $customer=new Customer($_REQUEST['customer_key']);
    if(!$customer->id)
      $customer=new Customer('create anonymous');
  }else
    $customer=new Customer('create anonymous');
$editor=array(
	      'Author Name'=>$user->data['User Alias'],
	      'Author Type'=>$user->data['User Type'],
	      'Author Key'=>$user->data['User Parent Key'],
	      'User Key'=>$user->id
	      );

  $order_data=array('type'=>'system'
		    ,'Customer Key'=>$customer->id
		    ,'Order Type'=>'Order'
		    ,'editor'=>$editor
		    );

  $order=new Order('new',$order_data);
  if($order->error){
    exit('error');
  }else{
    $js_file='order_in_process.js.php';
    $template='order_in_process.tpl';
  }


}else{

if(!isset($_REQUEST['id']) or !is_numeric($_REQUEST['id'])){
    header('Location: orders_server.php?msg=wrong_id');
   exit;
}


$order_id=$_REQUEST['id'];


$_SESSION['state']['order']['id']=$order_id;
if(!$order=new Order($order_id)){
   header('Location: orders_server.php?msg=order_not_found');
   exit;

}
if(!($user->can_view('stores') and in_array($order->data['Order Store Key'],$user->stores)   ) ){
  header('Location: orders_server.php');
   exit;
}

$customer=new Customer($order->get('order customer key'));

if(isset($_REQUEST['pick_aid'])){
    $js_file='order_pick_aid.js.php';
   $template='order_pick_aid.tpl';
}else{


switch($order->get('Order Current Dispatch State')){
  
 case('in process'):
   $js_file='order_in_process.js.php';
   $template='order_in_process.tpl';
   break;
  case('Dispached'):

    
    $js_file='order_dispached.js.php';
    $template='order_dispached.tpl';
  break; 

 default:
   $js_file='order_in_process.js.php';
  $template='order_in_process.tpl';
  break;
  
 }
}

}


$smarty->assign('order',$order);
$smarty->assign('customer',$customer);

$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 'common.css',
		 'container.css',
		 'table.css'
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
		$js_file
		);

$smarty->assign('parent','order.php');
$smarty->assign('title',_('Order').' '.$order->get('Order Public ID') );
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);
$smarty->display($template);
?>