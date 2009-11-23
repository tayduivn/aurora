<?php
require_once 'common.php';

require_once 'class.Warehouse.php';
require_once 'class.WarehouseArea.php';
require_once 'class.PartLocation.php';
require_once 'class.ShelfType.php';


if(!isset($_REQUEST['tipo']))
  {
    $response=array('state'=>405,'resp'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
  }
$editor=array(
	      'Author Name'=>$user->data['User Alias'],
	      'Author Type'=>$user->data['User Type'],
	      'Author Key'=>$user->data['User Parent Key'],
	      'User Key'=>$user->id
	      ,'Date'=>date('Y-m-d H:i:s')
	      );


$tipo=$_REQUEST['tipo'];
switch($tipo){
case('add_part_to_location'):
  add_part_to_location();
  break;
case('new_area'):
  new_warehouse_area();
  break;
case('new_shelf_type'):
  new_shelf_type();
  break;
case('new_location'):
  new_location();
  break;
case('warehouse_areas'):
  list_warehouse_areas_for_edition();
  break;
case('edit_locations'):
  list_locations_for_edition();
  break;
case('shelf_types'):
  list_shelf_types_for_edition();
  break;

case('edit_warehouse_area'):
  update_warehouse_area();
  break;
case('edit_part_location'):
  update_part_location();
  break;
case('edit_location'):
  update_location();
  break;
case('edit_shelf_type'):
  update_shelf_type();
  break;
case('edit_shelf'):
  update_shelf();
  break;

case('edit_shelf_location_type'):
  update_shelf_location_type();
  break;


case('delete_area'):
  delete_warehouse_area();
  break;
case('delete_location'):
  delete_location();
  break;
case('delete_part_location'):
  delete_part_location();
  break;
case('delete_warehouse'):
  delete_warehouse();
  break;
case('lost_stock'):
  lost_stock();
  break;
case('move_stock'):
  move_stock();
  break;
case('locations'):
  list_locations();
  break;


 default:

   $response=array('state'=>404,'resp'=>_('Operation not found'));
   echo json_encode($response);
   
 }

function update_part_location(){
  global $editor;

   if(
     !isset($_REQUEST['location_key'])
     or !isset($_REQUEST['part_sku'])
     or !isset($_REQUEST['key'])
     or !isset($_REQUEST['newvalue'])
     ){
    $response=array('state'=>400,'action'=>'error','msg'=>'');
    echo json_encode($response);
     return;
  }
  

  $part_sku=$_REQUEST['part_sku'];
  $location_key=$_REQUEST['location_key'];
  $new_value=stripslashes(urldecode($_REQUEST['newvalue']));
  $traslator=array(
		   'qty'=>'Quantity On Hand',
		   'can_pick'=>'Can Pick',
		   );
  if(array_key_exists($_REQUEST['key'],$traslator)){
    $key=$traslator[$_REQUEST['key']];
  }else{
    $response=array('state'=>400,'action'=>'error','msg'=>'Unknown key '.$_REQUEST['key']);
    echo json_encode($response);
    return;
  }    
  


  $part_location=new PartLocation($part_sku,$location_key);
  if(!$part_location->ok){
    $response=array('state'=>400,'action'=>'error','msg'=>'');
    echo json_encode($response);
    return;
  }
  $data=array($key=>$new_value);
  $part_location->editor=$editor;
  $part_location->update($data);
  
  if($part_location->updated){
    $response=array('state'=>200,'action'=>'updates','msg'=>$part_location->msg,'newvalue'=>$part_location->data[$key],'stock'=>$part_location->part->get('Part Current Stock'));
     echo json_encode($response);
     return;
  }else{
    $response=array('state'=>400,'action'=>'nochange','msg'=>$part_location->msg);
    echo json_encode($response);
     return;

  }
  

}

function update_warehouse_area(){
  
  if(
     !isset($_REQUEST['wa_key'])
     or !isset($_REQUEST['key'])
     or !isset($_REQUEST['newvalue'])
     ){
    $response=array('state'=>400,'action'=>'error','msg'=>'');
    echo json_encode($response);
     return;
  }
    
  
  $wa_key=$_REQUEST['wa_key'];
 
  $new_value=stripslashes(urldecode($_REQUEST['newvalue']));

  $traslator=array(
		   'code'=>'Warehouse Area Code',
		   'name'=>'Warehouse Area Name',
		   'description'=>'Warehouse Area Description'
		   );
  if(array_key_exists($_REQUEST['key'],$traslator)){
    $key=$traslator[$_REQUEST['key']];
  }else{
    $response=array('state'=>400,'action'=>'error','msg'=>'Unknown key '.$_REQUEST['key']);
    echo json_encode($response);
    return;
  }    


  $wa=new WarehouseArea($wa_key);
  if(!$wa->id){
    $response=array('state'=>400,'action'=>'error','msg'=>'');
    echo json_encode($response);
     return;
  }
  
  $wa->update(array($key=>$new_value));
  if($wa->updated){
    $response=array('state'=>200,'action'=>'updates','msg'=>$wa->msg,'newvalue'=>$wa->data[$key]);
     echo json_encode($response);
     return;
  }else{
    $response=array('state'=>400,'action'=>'nochange','msg'=>$wa->msg);
    echo json_encode($response);
     return;

  }
}


function update_shelf_type(){
 
  if(
      !isset($_REQUEST['id'])
     or !isset($_REQUEST['newvalue'])
     ){
    $response=array('state'=>400,'action'=>'error','msg'=>'no data');
    echo json_encode($response);
     return;
  }
    
  $id=$_REQUEST['id'];
  $key=$_REQUEST['key'];
  $new_value=stripslashes(urldecode($_REQUEST['newvalue']));

  $traslator=array(
		   'name'=>'Shelf Type Name',
		   'description'=>'Shelf Type Description',
		   'rows'=>'Shelf Type Rows',
		   'columns'=>'Shelf Type Columns',
		   'deep'=>'Shelf Type Location Deep',
		   'length'=>'Shelf Type Location Length',
		   'height'=>'Shelf Type Location Height',
		   'max_weight'=>'Shelf Type Location Max Weight',
		   'max_volume'=>'Shelf Type Location Max Volume',
		   'type'=>'Shelf Type Type',
		   
		   );
  if(array_key_exists($_REQUEST['key'],$traslator)){
    $key=$traslator[$_REQUEST['key']];
  }else{
    $response=array('state'=>400,'action'=>'error','msg'=>'Unknown key '.$_REQUEST['key']);
    echo json_encode($response);
    return;
  }    


  $wa=new ShelfType($id);
  if(!$wa->id){
    $response=array('state'=>400,'action'=>'error','msg'=>'object not found');
    echo json_encode($response);
     return;
  }
  
  $wa->update(array($key=>$new_value));
  if($wa->updated){
    $response=array('state'=>200,'action'=>'updates','msg'=>$wa->msg,'newvalue'=>$wa->data[$key]);
     echo json_encode($response);
     return;
  }else{
    $response=array('state'=>400,'action'=>'nochange','msg'=>$wa->msg);
    echo json_encode($response);
     return;

  }
}


function list_warehouse_areas_for_edition(){
    if(isset( $_REQUEST['warehouse']) and  is_numeric( $_REQUEST['warehouse']))
	$warehouse_id=$_REQUEST['warehouse'];
      else
	$warehouse_id=$_SESSION['state']['warehouse']['id'];
   

      $conf=$_SESSION['state']['warehouse_area']['table'];
      $conf2=$_SESSION['state']['warehouse_area'];
      $conf_table='warehouse_area';

      if(isset( $_REQUEST['parent']))
	$parent=$_REQUEST['parent'];
      else
	$parent=$conf['parent'];


      if(isset( $_REQUEST['sf'])){
	$start_from=$_REQUEST['sf'];
	
	
      }else
	$start_from=$conf['sf'];
      if(isset( $_REQUEST['nr'])){
	$number_results=$_REQUEST['nr'];
	if($start_from>0){
	  $page=floor($start_from/$number_results);
	  $start_from=$start_from-$page;
	}
	
      }else
	$number_results=$conf['nr'];
      if(isset( $_REQUEST['o']))
	$order=$_REQUEST['o'];
      else
	$order=$conf['order'];
      if(isset( $_REQUEST['od']))
	$order_dir=$_REQUEST['od'];
      else
	$order_dir=$conf['order_dir'];
      $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
      if(isset( $_REQUEST['where']))
	$where=addslashes($_REQUEST['where']);
      else
     $where=$conf['where'];
      
    
      if(isset( $_REQUEST['f_field']))
	$f_field=$_REQUEST['f_field'];
      else
	$f_field=$conf['f_field'];
      
      if(isset( $_REQUEST['f_value']))
	$f_value=$_REQUEST['f_value'];
      else
	$f_value=$conf['f_value'];
      
      
      if(isset( $_REQUEST['tableid']))
	$tableid=$_REQUEST['tableid'];
      else
    $tableid=0;
      
      
    

      
      
      $_SESSION['state'][$conf_table]['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value,'parent'=>$parent);
     
      
      
      switch($parent){
      case('warehouse'):
	$where=sprintf("where  `Warehouse Key`=%d",$warehouse_id);
	break;
      default:
	$where='where true';
	  
	  }   

      $filter_msg='';
      $wheref='';
      if($f_field=='name' and $f_value!='')
	$wheref.=" and  `Warehouse Area Name` like '".addslashes($f_value)."%'";
      if($f_field=='code' and $f_value!='')
	$wheref.=" and  `Warehouse Area Code` like '".addslashes($f_value)."%'";
   
   

   
   
   $sql="select count(*) as total from `Warehouse Area Dimension`   $where $wheref";

   $res = mysql_query($sql); 
   if($row=mysql_fetch_array($res)) {
     $total=$row['total'];
   }
   mysql_free_result($res);
   if($wheref==''){
     $filtered=0; $total_records=$total;
   }else{
     $sql="select count(*) as total `Warehouse Area Dimension`   $where ";
     
     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      	$total_records=$row['total'];
	$filtered=$total_records-$total;
	mysql_free_result($result);
     }

   }

 $rtext=$total_records." ".ngettext('area','areas',$total_records);
  if($total_records>$number_results)
    $rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
  else
    $rtext_rpp=' ('._('Showing all').')';
   $_dir=$order_direction;
   $_order=$order;
   
   $order='`Warehouse Area Code`';
   if($order=='name')
     $order='`Warehouse Area Name`';
   elseif($order=='code')
     $order='`Warehouse Area Code`';
 

 
   $sql="select *  from `Warehouse Area Dimension` $where $wheref order by $order $order_direction limit $start_from,$number_results    ";
   // print $sql;
   $res = mysql_query($sql);
   $adata=array();
   
   $sum_active=0;
   
   
   while($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
    
    $adata[]=array(
		   'wa_key'=>$row['Warehouse Area Key'],
		   'code'=>$row['Warehouse Area Code'],
		   'name'=>$row['Warehouse Area Name'],
		   'description'=>$row['Warehouse Area Description'],

		   
		   );
   }
  mysql_free_result($res);

  





  
  
  $response=array('resultset'=>
		  array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
			)
		   );
   echo json_encode($response);

   }

function new_location(){
global $editor;

if( !isset($_REQUEST['values']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
   
   $tmp=preg_replace('/\\\"/','"',$_REQUEST['values']);
   $tmp=preg_replace('/\\\\\"/','"',$tmp);
   
   $raw_data=json_decode($tmp, true);
   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }

   if(!isset($raw_data['Location Warehouse Area Key'])  or !is_numeric($raw_data['Location Warehouse Area Key']) ){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }



   // print_r($raw_data);

   $warehouse_area=new WarehouseArea($raw_data['Location Warehouse Area Key']);
   
   if(!$warehouse_area->id){
     $response=array('state'=>400,'msg'=>'Wrong Warehouse Area');
     echo json_encode($response);
     return;
   }
	

   $raw_data['editor']=$editor;
 
   $warehouse_area->add_location($raw_data);
   if($warehouse_area->new_area){
     $response=array(
		     'state'=>200
		     ,'action'=>'created'
		     ,'msg'=>_('Location added to Warehouse Area')
		     );
     echo json_encode($response);
     return;
     
   }else{
     $response=array('state'=>200,'action'=>'nochange','msg'=>$warehouse_area->new_location_msg);
     echo json_encode($response);
     return;

   }
}

function new_warehouse_area(){
global $editor;

if( !isset($_REQUEST['values']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
   
   $tmp=preg_replace('/\\\"/','"',$_REQUEST['values']);
   $tmp=preg_replace('/\\\\\"/','"',$tmp);
   
   $raw_data=json_decode($tmp, true);
   // print_r($raw_data);
   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }

   if(!isset($raw_data['Warehouse Key'])  or !is_numeric($raw_data['Warehouse Key']) ){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }
   $warehouse=new warehouse($raw_data['Warehouse Key']);
   
   if(!$warehouse->id){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }
	

   $raw_data['editor']=$editor;
 
   $warehouse->add_area($raw_data);
   if($warehouse->new_area){
     $response=array(
		     'state'=>200
		     ,'action'=>'created'
		     ,'msg'=>_('Area added to Warehouse')
		     );
     echo json_encode($response);
     return;
     
   }else{
     $response=array('state'=>200,'action'=>'nochange','msg'=>$warehouse->new_area_msg);
     echo json_encode($response);
     return;

   }
}

function new_shelf_type(){
global $editor;

if( !isset($_REQUEST['values']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
   
   $tmp=preg_replace('/\\\"/','"',$_REQUEST['values']);
   $tmp=preg_replace('/\\\\\"/','"',$tmp);
   
   $raw_data=json_decode($tmp, true);
   // print_r($raw_data);
   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }

  
	

   $raw_data['editor']=$editor;
   
   $shelf_type=new ShelfType('find',$raw_data,'create');
   if($shelf_type->new){
     $response=array(
		     'state'=>200
		     ,'action'=>'created'
		     ,'msg'=>_('Shelf Type Created')
		     );
     echo json_encode($response);
     return;
     
   }else{
     $response=array('state'=>200,'action'=>'nochange','msg'=>$shelf_type->msg);
     echo json_encode($response);
     return;

   }
}


function list_locations_for_edition(){
  if(isset( $_REQUEST['warehouse']) and  is_numeric( $_REQUEST['warehouse']))
    $warehouse_id=$_REQUEST['warehouse'];
  else
    $warehouse_id=$_SESSION['state']['warehouse']['id'];
  
   if(isset( $_REQUEST['warehouse_area']) and  is_numeric( $_REQUEST['warehouse_area']))
    $warehouse_area_id=$_REQUEST['warehouse_area'];
  else
    $warehouse_area_id=$_SESSION['state']['warehouse_area']['id'];




      $conf=$_SESSION['state']['warehouse_area']['table'];
      $conf2=$_SESSION['state']['warehouse_area'];
      $conf_table='warehouse_area';

      if(isset( $_REQUEST['parent']))
	$parent=$_REQUEST['parent'];
      else
	$parent=$conf['parent'];





      if(isset( $_REQUEST['sf'])){
	$start_from=$_REQUEST['sf'];
	
	
      }else
	$start_from=$conf['sf'];
      if(isset( $_REQUEST['nr'])){
	$number_results=$_REQUEST['nr'];
	if($start_from>0){
	  $page=floor($start_from/$number_results);
	  $start_from=$start_from-$page;
	}
	
      }else
	$number_results=$conf['nr'];
      if(isset( $_REQUEST['o']))
	$order=$_REQUEST['o'];
      else
	$order=$conf['order'];
      if(isset( $_REQUEST['od']))
	$order_dir=$_REQUEST['od'];
      else
	$order_dir=$conf['order_dir'];
      $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
      if(isset( $_REQUEST['where']))
	$where=addslashes($_REQUEST['where']);
      else
     $where=$conf['where'];
      
    
      if(isset( $_REQUEST['f_field']))
	$f_field=$_REQUEST['f_field'];
      else
	$f_field=$conf['f_field'];
      
      if(isset( $_REQUEST['f_value']))
	$f_value=$_REQUEST['f_value'];
      else
	$f_value=$conf['f_value'];
      
      
      if(isset( $_REQUEST['tableid']))
	$tableid=$_REQUEST['tableid'];
      else
    $tableid=0;
      
      
    

      
      
      $_SESSION['state'][$conf_table]['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value,'parent'=>$parent);
     
      
      
      switch($parent){
      case('warehouse'):
	$where=sprintf("where  `Location Warehouse Key`=%d",$warehouse_id);
	break;
      default:
	$where='where true';
	  
	  }   

      $filter_msg='';
      $wheref='';
      if($f_field=='area' and $f_value!='')
	$wheref.=" and  `Location Area Code` like '".addslashes($f_value)."%'";
      if($f_field=='code' and $f_value!='')
	$wheref.=" and  `Location Code` like '".addslashes($f_value)."%'";
   
   

   
   
   $sql="select count(*) as total from `Location Dimension`   $where $wheref";
   //  print $sql;
   $res = mysql_query($sql); 
   if($row=mysql_fetch_array($res)) {
     $total=$row['total'];
   }
   mysql_free_result($res);
   if($wheref==''){
     $filtered=0; $total_records=$total;
   }else{
     $sql="select count(*) as total `Location`   $where ";
     
     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      	$total_records=$row['total'];
	$filtered=$total_records-$total;
	mysql_free_result($result);
     }

   }

 $rtext=$total_records." ".ngettext('location','locations',$total_records);
  if($total_records>$number_results)
    $rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
  else
    $rtext_rpp=' ('._('Showing all').')';
   $_dir=$order_direction;
   $_order=$order;
   
   $order='`Location Code`';
   if($order=='used_for')
     $order='`Location Mainly Used For`';
   elseif($order=='code')
     $order='`Location Code`';
   elseif($order=='area')
     $order='`Location Area Code`';
   elseif($order=='max_slots')
     $order='`Location Max Slots`';
   elseif($order=='max_weight')
     $order='`Location Max Weight`';
  elseif($order=='max_vol')
     $order='`Location Max Volume`';

   $sql="select *  from `Location Dimension` left join `Warehouse Area Dimension` WAD on (`Location Warehouse Area Key`=WAD.`Warehouse Area Key`)  $where $wheref order by $order $order_direction limit $start_from,$number_results    ";

   

   // print $sql;
   $res = mysql_query($sql);
   $adata=array();
   
   $sum_active=0;
   
   
   while($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
     $user_for=$row['Location Mainly Used For'];
     $area=$row['Warehouse Area Code'];
     $max_vol='';
     if($row['Location Max Volume']!='')
       $max_vol=number($row['Location Max Volume']).'L';
     $max_weight='';
     if($row['Location Max Weight']!='')
       $max_weight=number($row['Location Max Weight']).'Kg';
     
     $adata[]=array(
		    'location_key'=>$row['Location Key']
		    ,'code'=>$row['Location Code']
		    ,'used_for'=>$user_for
		    ,'max_slots'=>$row['Location Max Slots']
		    ,'area'=>$area
		    ,'max_vol'=>$max_vol
		    ,'max_weight'=>$max_weight
		    ,'delete'=>'<img alt="'._('Delete').'" src="art/icons/cross.png" />'
		    );
   }
  mysql_free_result($res);

  





  
  
  $response=array('resultset'=>
		  array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
			)
		   );
   echo json_encode($response);

   }


function delete_location(){
  if(!isset($_REQUEST['location_key'])){
    $response=array('state'=>400,'msg'=>'Error');
     echo json_encode($response);
     return;
  }
    
  $location_key=$_REQUEST['location_key'];
  $location=new Location($location_key);

  $location->delete();
  
  if($location->deleted){
    echo 'Ok';
    return;
    
  }else{
    echo $location->deleted_msg;
    return;
  }
  

}

function delete_part_location(){
  global $editor;
  if(!isset($_REQUEST['location_key'])
     or !isset($_REQUEST['part_sku'])
     ){
    $response=array('state'=>400,'msg'=>'Error 1');
     echo json_encode($response);
     return;
  }
    
  $location_key=$_REQUEST['location_key'];
  $part_sku=$_REQUEST['part_sku'];
  
  $part_location=new PartLocation($part_sku,$location_key);
  $part_location->editor=$editor;
  $part_location->delete();
  
  if($part_location->deleted){
    echo 'Ok';
    return;
    
  }else{
    echo $part_location->deleted_msg;
    return;
  }
  

}
function add_part_to_location(){
  global $editor;
  
  if(!isset($_REQUEST['location_key'])
     or !isset($_REQUEST['part_sku'])
     ){
    $response=array('state'=>400,'msg'=>'Error');
    echo json_encode($response);
    return;
  }
  
  $location_key=$_REQUEST['location_key'];
  $part_sku=$_REQUEST['part_sku'];
  $data=array(
	      'Location Key'=>$location_key
	      ,'Part SKU'=>$part_sku
	      ,'editor'=>$editor
	      );
  
  $part_location=new PartLocation('find',$data,'create');
  if($part_location->new){
    $response=array('state'=>200,'action'=>'added','msg'=>$part_location->msg);
     echo json_encode($response);
     return;
  }else{
    $response=array('state'=>400,'action'=>'nochange','msg'=>$part_location->msg);
    echo json_encode($response);
    return;
  }
  


}


function lost_stock(){
  global $editor;
  if( !isset($_REQUEST['values']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
  
  $tmp=preg_replace('/\\\"/','"',$_REQUEST['values']);
  $tmp=preg_replace('/\\\\\"/','"',$tmp);
   
   $raw_data=json_decode($tmp, true);
   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }


  if(
     !isset($raw_data['location_key'])
     or !isset($raw_data['part_sku'])
     or !isset($raw_data['qty'])
     or !isset($raw_data['why'])
     or !isset($raw_data['action'])
     ){
    $response=array('state'=>400,'action'=>'error','msg'=>'wp');
    echo json_encode($response);
    return;
  }
  
  $traslator=array(
		   'qty'=>'Lost Quantity',
		   'why'=>'Reason',
		   'action'=>'Action'
		   );
  
  foreach($raw_data as $key =>$value){
    if(array_key_exists($key,$traslator)){
      $data[$traslator[$key]]=$value;
    }    
  }    
  
  $part_location=new PartLocation($raw_data['part_sku'],$raw_data['location_key']);
  
  
  if(!$part_location->ok){
    $response=array('state'=>400,'action'=>'error','msg'=>'errr n pl');
    echo json_encode($response);
    return;
  }
   $part_location->editor=$editor;
   $part_location->set_stock_as_lost($data);
   if($part_location->error){
     $response=array('state'=>400,'action'=>'nochange','msg'=>$part_location->msg);
    echo json_encode($response);
     return;

   }else{
     $response=array('state'=>200,'action'=>'ok','msg'=>$part_location->msg,'qty'=>$part_location->data['Quantity On Hand'],'stock'=>$part_location->part->get('Part Current Stock'));
     echo json_encode($response);
     return;

   }
  }

function move_stock(){
  global $editor;
  if( !isset($_REQUEST['values']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
  }
  
  $tmp=preg_replace('/\\\"/','"',$_REQUEST['values']);
  $tmp=preg_replace('/\\\\\"/','"',$tmp);
   
  $raw_data=json_decode($tmp, true);
  if(!is_array($raw_data)){
    $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
  }
  
  if(
     !isset($raw_data['from_key'])
     or !isset($raw_data['part_sku'])
     or !isset($raw_data['qty'])
     or !isset($raw_data['to_key'])
     ){
    $response=array('state'=>400,'action'=>'error','msg'=>'wp');
    echo json_encode($response);
    return;
  }

  
  $traslator=array(
		   'qty'=>'Quantity To Move',
		   'to_key'=>'Destination Key',
		   );
  $data=array();
  foreach($raw_data as $key =>$value){
    if(array_key_exists($key,$traslator)){
      $data[$traslator[$key]]=$value;
    }    
  }
  
  $part_location=new PartLocation($raw_data['part_sku'],$raw_data['from_key']);
  
 
  if(!$part_location->ok){
    $response=array('state'=>400,'action'=>'error','msg'=>'');
    echo json_encode($response);
    return;
  }
   $part_location->editor=$editor;
   //  print_r($data);
   $part_location->move_stock($data);
   if($part_location->error){
     $response=array('state'=>400,'action'=>'nochange','msg'=>$part_location->msg);
     echo json_encode($response);
     return;

   }else{
     $response=array('state'=>200,'action'=>'ok','msg'=>$part_location->msg,'qty_from'=>$part_location->data['Quantity On Hand'],'qty_to'=>$part_location->data['Quantity On Hand'],'stock'=>$part_location->part->get('Part Current Stock'));
     echo json_encode($response);
     return;

   }
  }

function list_locations(){
$conf=$_SESSION['state']['warehouse']['locations'];
   
   if(isset( $_REQUEST['sf']))
     $start_from=$_REQUEST['sf'];
   else
     $start_from=$conf['sf'];
   if(isset( $_REQUEST['nr']))
     $number_results=$_REQUEST['nr'];
   else
     $number_results=$conf['nr'];
   if(isset( $_REQUEST['o']))
     $order=$_REQUEST['o'];
   else
    $order=$conf['order'];
   if(isset( $_REQUEST['od']))
     $order_dir=$_REQUEST['od'];
   else
     $order_dir=$conf['order_dir'];
   $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
   if(isset( $_REQUEST['where']))
     $where=addslashes($_REQUEST['where']);
   else
     $where=$conf['where'];

    
   if(isset( $_REQUEST['f_field']))
     $f_field=$_REQUEST['f_field'];
   else
     $f_field=$conf['f_field'];
   
   if(isset( $_REQUEST['f_value']))
     $f_value=$_REQUEST['f_value'];
   else
     $f_value=$conf['f_value'];

   
   if(isset( $_REQUEST['tableid']))
     $tableid=$_REQUEST['tableid'];
   else
    $tableid=0;
   

   
   
 
   $wheref='';
   if($f_field=='code' and $f_value!='')
     $wheref.=" and  `Location Code` like '".addslashes($f_value)."%'";
   

  
   $_SESSION['state']['warehouse']['locations']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);
   
   
   $sql="select count(*) as total from `Location Dimension`    $where $wheref";
   // print $sql;
   $result=mysql_query($sql);
   if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
     $total=$row['total'];
   }
   if($wheref==''){
       $filtered=0;
       $total_records=$total;
   }else{
     $sql="select count(*) as total from `Location Dimension`  $where ";

     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       $total_records=$row['total'];
       $filtered=$row['total']-$total;
     }

   }

   




   if($total==0 and $filtered>0){
     switch($f_field){
     case('code'):
       $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any location name starting with")." <b>$f_value</b> ";
       break;
     }
   }elseif($filtered>0){
     switch($f_field){
     case('code'):
       $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('only locations starting with')." <b>$f_value</b> <span onclick=\"remove_filter($tableid)\" id='remove_filter$tableid' class='remove_filter'>"._('Show All')."</span>";
       break;
     }
   }else
      $filter_msg='';
   
   
   $rtext=$total_records." ".ngettext('location','locations',$total_records);
   if($total_records>$number_results)
     $rtext.=sprintf(" <span class='rtext_rpp'>(%d%s)</span>",$number_results,_('rpp'));
  $_order=$order;
  $_dir=$order_direction;

  if($order=='code')
    $order='`Location Code`';
  elseif($order=='parts')
    $order='`Location Distinct Parts`';
 elseif($order=='max_volumen')
    $order='`Location Max Volume`';
  elseif($order=='max_weight')
    $order='`Location Max Weight`';
  elseif($order=='tipo')
    $order='`Location Mainly Used For`';
 elseif($order=='area')
    $order='`Warehouse Area Code`';

  $data=array();
  $sql="select * from `Location Dimension` left join `Warehouse Area Dimension` WAD on (`Location Warehouse Area Key`=WAD.`Warehouse Area Key`)  $where $wheref   order by $order $order_direction limit $start_from,$number_results    ";
  //  print $sql;
  $result=mysql_query($sql);
  while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
    $code=sprintf('<a href="location.php?id=%d" >%s</a>',$row['Location Key'],$row['Location Code']);
    $tipo=$row['Location Mainly Used For'];

    if($row['Location Max Weight']=='' or $row['Location Max Weight']<=0)
      $max_weight=_('Unknown');
    else
      $max_weight=number($row['Location Max Weight'])._('Kg');
    if($row['Location Max Volume']==''  or $row['Location Max Volume']<=0)
      $max_vol=_('Unknown');
    else
      $max_vol=number($row['Location Max Volume'])._('L');

    if($row['Warehouse Area Code']=='')
      $area=_('Unknown');
    else
      $area=$row['Warehouse Area Code'];
    $data[]=array(
		 'id'=>$row['Location Key']
		 ,'tipo'=>$tipo
		 ,'code'=>$code
		 ,'area'=>$area
		 ,'parts'=>number($row['Location Distinct Parts'])
		 ,'max_weight'=>$max_weight
		 ,'max_volumen'=>$max_vol
		 );
  }
  $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$data,
			 'sort_key'=>$_order,
			 'sort_dir'=>$_dir,
			 'tableid'=>$tableid,
			 'filter_msg'=>$filter_msg,
			 'rtext'=>$rtext,
			 'total_records'=>$total,
			 'records_offset'=>$start_from,
			 'records_returned'=>$start_from+$total,
			'records_perpage'=>$number_results,
			 
			 'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'filtered'=>$filtered
			 )
		   );
   echo json_encode($response);
}


function list_shelf_types_for_edition(){


  $conf_table='shelf_types';
      $conf=$_SESSION['state']['shelf_types']['table'];

      if(isset( $_REQUEST['sf'])){
	$start_from=$_REQUEST['sf'];
	
	
      }else
	$start_from=$conf['sf'];
      if(isset( $_REQUEST['nr'])){
	$number_results=$_REQUEST['nr'];
	if($start_from>0){
	  $page=floor($start_from/$number_results);
	  $start_from=$start_from-$page;
	}
	
      }else
	$number_results=$conf['nr'];
      if(isset( $_REQUEST['o']))
	$order=$_REQUEST['o'];
      else
	$order=$conf['order'];
      if(isset( $_REQUEST['od']))
	$order_dir=$_REQUEST['od'];
      else
	$order_dir=$conf['order_dir'];
      $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
      if(isset( $_REQUEST['where']))
	$where=addslashes($_REQUEST['where']);
      else
     $where=$conf['where'];
      
    
      if(isset( $_REQUEST['f_field']))
	$f_field=$_REQUEST['f_field'];
      else
	$f_field=$conf['f_field'];
      
      if(isset( $_REQUEST['f_value']))
	$f_value=$_REQUEST['f_value'];
      else
	$f_value=$conf['f_value'];
      
      
      if(isset( $_REQUEST['tableid']))
	$tableid=$_REQUEST['tableid'];
      else
    $tableid=0;
      
      
    

      
      
      $_SESSION['state'][$conf_table]['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);
     
 

      $filter_msg='';
      $wheref='';
      if($f_field=='name' and $f_value!='')
	$wheref.=" and  `Shelf Type Name` like '".addslashes($f_value)."%'";

   
   

   
   
   $sql="select count(*) as total from `Shelf Type Dimension`   $where $wheref";
   //  print $sql;
   $res = mysql_query($sql); 
   if($row=mysql_fetch_array($res)) {
     $total=$row['total'];
   }
   mysql_free_result($res);
   if($wheref==''){
     $filtered=0; $total_records=$total;
   }else{
     $sql="select count(*) as total from `Shelf Type Dimension`   $where ";
     
     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      	$total_records=$row['total'];
	$filtered=$total_records-$total;
	mysql_free_result($result);
     }

   }

 $rtext=$total_records." ".ngettext('shelf type','shelf types',$total_records);
  if($total_records>$number_results)
    $rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
  else
    $rtext_rpp=' ('._('Showing all').')';
   $_dir=$order_direction;
   $_order=$order;
   
   $order='`Shelf Type Name`';
   if($order=='name')
     $order='`Shelf Type Name`';
   elseif($order=='rows')
     $order='`Shelf Type Rows`';
   elseif($order=='columns')
     $order='`Shelf Type Columns`';
   elseif($order=='deep')
     $order='`Shelf Type Location Deep`';
   elseif($order=='length')
     $order='`Shelf Type Location Length`';
   elseif($order=='height')
     $order='`Shelf Type Location Height`';
   elseif($order=='type')
     $order='`Shelf Type Type`';
   elseif($order=='max_weight')
     $order='`Shelf Type Location Max Weight`';
  elseif($order=='max_vol')
     $order='`Shelf Type Location Max Volume`';

   $sql="select *  from `Shelf Type Dimension` $where $wheref order by $order $order_direction limit $start_from,$number_results    ";
   
   $res = mysql_query($sql);
   $adata=array();   
   $sum_active=0;   
   while($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
     
     $max_vol='';
     $max_weight='';
     $deep='';
     $length='';
     $height='';
     
     if($row['Shelf Type Location Max Volume']!='')
       $max_vol=number($row['Location Max Volume']);

     if($row['Shelf Type Location Max Weight']!='')
       $max_weight=number($row['Location Max Weight']);
     if($row['Shelf Type Location Deep']!='')
       $deep=number($row['Shelf Type Location Deep']);

     if($row['Shelf Type Location Length']!='')
       $length=number($row['Shelf Type Location Length']);

     if($row['Shelf Type Location Height']!='')
       $height=number($row['Shelf Type Location Height']);

     $type=$row['Shelf Type Type'];
     
     $adata[]=array(
		    'id'=>$row['Shelf Type Key']
		    ,'name'=>$row['Shelf Type Name']
		    ,'description'=>$row['Shelf Type Description']
		    ,'rows'=>number($row['Shelf Type Rows'])
		    ,'columns'=>number($row['Shelf Type Columns'])
		    ,'deep'=>$deep
		    ,'length'=>$length
		    ,'height'=>$height
		    ,'max_vol'=>$max_vol
		    ,'max_weight'=>$max_weight
		    ,'type'=>$type
		    ,'delete'=>'<img alt="'._('Delete').'" src="art/icons/cross.png" />'
		    );
   }
  mysql_free_result($res);

  





  
  
  $response=array('resultset'=>
		  array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
			)
		   );
   echo json_encode($response);

   }





?>