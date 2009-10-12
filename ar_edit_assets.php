<?php
require_once 'common.php';

require_once 'class.Product.php';
require_once 'class.Department.php';
require_once 'class.Family.php';

require_once 'class.Order.php';
require_once 'class.Location.php';
require_once 'class.PartLocation.php';

if(!isset($_REQUEST['tipo']))
  {
    $response=array('state'=>405,'resp'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
  }



$tipo=$_REQUEST['tipo'];
switch($tipo){

case('delete_image'):

  delete_image();
  

  break;
case('upload_product_image'):
upload_image('product');
break;
 case('delete_family'):
delete_family();
     break;
case('delete_store'):
delete_store();
   break;
case('delete_department'):
delete_department();
   break;
 case('edit_family'):
  edit_family(); 
   break;

case('edit_product_advanced'):
  edit_product_multi();
   break;
case('edit_product'):
  edit_product();
   break;
 case('edit_department'):
 edit_department();
   break;
 case('edit_store'):
 edit_store();

   break;

 case('new_store'):
   
   create_store();
   break;
 case('new_department'):
  create_department();
   break;
 case('new_family'):
create_family();
   break;
case('edit_departments'):
  list_departments_for_edition();
 
   break;

case('edit_stores'):
   list_stores_for_edition();
   
   break;
case('edit_families'):
list_families_for_edition();
 
  break;
case('edit_products'):
  list_products_for_edition();
  break;


 default:

   $response=array('state'=>404,'resp'=>_('Operation not found'));
   echo json_encode($response);
   
 }
function create_store(){
  if(isset($_REQUEST['name'])  and  isset($_REQUEST['code'])   ){
    $store=new Store('find',array(
					      'Store Code'=>$_REQUEST['code']
					      ,'Store Name'=>$_REQUEST['name']
					      
				  ),'create');
    if(!$store->new){
       $state='400';
    }else{
      
      $state='200';
    }
     $response=array('state'=>$state,'msg'=>$store->msg);
  }
  
  else
    $response=array('state'=>400,'resp'=>_('Error'));
  echo json_encode($response);
}
function create_department(){
 if(isset($_REQUEST['name'])  and  isset($_REQUEST['code'])   ){
     $store_key=$_SESSION['state']['store']['id'];
     $department=new Department('find',array(
					      'Product Department Code'=>$_REQUEST['code']
					      ,'Product Department Name'=>$_REQUEST['name']
					      ,'Product Department Store Key'=>$store_key
					       ),'create');
     if(!$department->new){
       $state='400';
     }else{
       $state='200';
     }
     $response=array('state'=>$state,'msg'=>$department->msg);
   }
   else
     $response=array('state'=>400,'resp'=>_('Error'));
   echo json_encode($response);
}
function create_family(){
 if(isset($_REQUEST['name'])  and  isset($_REQUEST['code'])   ){
     $department_key=$_SESSION['state']['department']['id'];
     
     $family=new Family('create',array(
					      
				       'Product Family Code'=>$_REQUEST['code']
				       ,'Product Family Name'=>$_REQUEST['name']
				       ,'Product Family Description'=>$_REQUEST['description']
				       ,'Product Family Special Characteristic'=>$_REQUEST['special_char']
				       ,'Product Family Main Department Key'=>$department_key

				       ));
     if(!$family->new){
       $state='401';
     }else{
       $state='200';
     }

     $response=array('state'=>$state,'msg'=>$family->msg);


 }
 else
     $response=array('state'=>400,'msg'=>_('Error'));
   echo json_encode($response);
}
function delete_family(){
 if(!isset($_REQUEST['id']))
     return 'Error: no family specificated';
   if(!is_numeric($_REQUEST['id']) or $_REQUEST['id']<=0 )
     return 'Error: wrong family id';
   if(!isset($_REQUEST['delete_type'])  or !($_REQUEST['delete_type']=='delete' or $_REQUEST['delete_type']=='discontinue'  )  )
     return 'Error: delete type no supplied';

   $id=$_REQUEST['id'];
   $family=new Family($id);

   if($_REQUEST['delete_type']=='delete'){

     $family->delete();
   }else if($_REQUEST['delete_type']=='discontinue'){
     $family->discontinue();
   }
   if($family->deleted){
     print 'Ok';
   }else{
     print $family->msg;
   }
   
}
function delete_store(){
  if(!isset($_REQUEST['id']))
     return 'Error: no store key';
   if(!is_numeric($_REQUEST['id']) or $_REQUEST['id']<=0 )
     return 'Error: wrong store id';
   if(!isset($_REQUEST['delete_type'])  or !($_REQUEST['delete_type']=='delete' or $_REQUEST['delete_type']=='close'  )  )
     return 'Error: delete type no supplied';

   $id=$_REQUEST['id'];
   $store=new Store($id);

   if($_REQUEST['delete_type']=='delete'){

     $store->delete();
   }else if($_REQUEST['delete_type']=='close'){
     $store->close();
   }
   if($store->deleted){
     print 'Ok';
   }else{
     print $store->msg;
   }
   
}
function delete_department(){
  if(!isset($_REQUEST['id']))
     return 'Error: no department key';
   if(!is_numeric($_REQUEST['id']) or $_REQUEST['id']<=0 )
     return 'Error: wrong department id';
   if(!isset($_REQUEST['delete_type'])  or !($_REQUEST['delete_type']=='delete' or $_REQUEST['delete_type']=='discontinue'  )  )
     return 'Error: delete type no supplied';

   $id=$_REQUEST['id'];
   $department=new Department($id);

   if($_REQUEST['delete_type']=='delete'){

     $department->delete();
   }else if($_REQUEST['delete_type']=='discontinue'){
     $department->close();
   }
   if($department->deleted){
     print 'Ok';
   }else{
     print $department->msg;
   }
   
   
}
function edit_store(){
  $store=new Store($_REQUEST['id']);
   $store->update($_REQUEST['key'],stripslashes(urldecode($_REQUEST['newvalue'])),stripslashes(urldecode($_REQUEST['oldvalue'])));
     
   if($store->updated){
     $response= array('state'=>200,'newvalue'=>$store->newvalue,'key'=>$_REQUEST['key']);
	  
   }else{
     $response= array('state'=>400,'msg'=>$store->msg,'key'=>$_REQUEST['key']);
   }
   echo json_encode($response);  
}
function edit_department(){
  $department=new Department($_REQUEST['id']);
   $department->update($_REQUEST['key'],stripslashes(urldecode($_REQUEST['newvalue'])),stripslashes(urldecode($_REQUEST['oldvalue'])));
   
   //   $response= array('state'=>400,'msg'=>print_r($_REQUEST);
   //echo json_encode($response);  
   // exit;
   if($department->updated){
     $response= array('state'=>200,'newvalue'=>$department->newvalue,'key'=>$_REQUEST['key']);
	  
   }else{
     $response= array('state'=>400,'msg'=>$department->msg,'key'=>$_REQUEST['key']);
   }
   echo json_encode($response);  

}
function edit_product(){
  $product=new product('pid',$_REQUEST['id']);
 
   $translator=array(
		     'name'=>'Product Name',
		     'sdescription'=>'Product Special Characteristic',
		     'price'=>'Product Price',
		     'unit_price'=>'Product Unit Price',
		     'margin'=>'Product Margin',
		     'unit_rrp'=>'Product RRP Per Unit',
		     );
    
    if(array_key_exists($_REQUEST['key'],$translator))
      $key=$translator[$_REQUEST['key']];
    else
      $key=$_REQUEST['key'];

    $product->update($key,stripslashes(urldecode($_REQUEST['newvalue'])));
   

   if($product->updated){
     $response= array('state'=>200,'newvalue'=>$product->new_value,'key'=>$_REQUEST['key']);
	  
   }else{
     $response= array('state'=>400,'msg'=>$product->msg,'key'=>$_REQUEST['key']);
   }
   echo json_encode($response); 
}


function edit_family(){
 $family=new family($_REQUEST['id']);
   $family->update($_REQUEST['key'],stripslashes(urldecode($_REQUEST['newvalue'])),stripslashes(urldecode($_REQUEST['oldvalue'])));
   

   if($family->updated){
     $response= array('state'=>200,'newvalue'=>$family->newvalue,'key'=>$_REQUEST['key']);
	  
   }else{
     $response= array('state'=>400,'msg'=>$family->msg,'key'=>$_REQUEST['key']);
   }
   echo json_encode($response); 
}

function upload_image($subject='product'){
  
  print_r($_REQUEST);
  $target_path = "uploads/".'pimg_'.date('U');
  if(move_uploaded_file($_FILES['testFile']['tmp_name'],$target_path )) {
    if($subject=='product')
      $subject=new product('pid',$_REQUEST['id']);
    
    $subject->add_image($target_path);
      
      
    }

  }


function edit_product_multi(){

  if(!isset($_REQUEST['value'])  and isset($_REQUEST['newvalue']) )
    $_REQUEST['value']=$_REQUEST['newvalue'];
  if(!isset($_REQUEST['id']) or !isset($_REQUEST['key']) or  !isset($_REQUEST['value'])       ){
    $response= array('state'=>400,'msg'=>'error','key'=>$_REQUEST['key']);
    echo json_encode($response); 
    return;
  }

  $product=new product('pid',$_REQUEST['id']);
  $result=array();
  $updated=false;
  if($_REQUEST['key']=='array'){
    $tmp=preg_replace('/\\\"/','"',$_REQUEST['value']);
    $tmp=preg_replace('/\\\\\"/','"',$tmp);
    $raw_data=json_decode($tmp, true);
   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }
   
   $result=array();
   //print_r($raw_data);
   foreach($raw_data as $key=>$value){
     $product->update($key,$value);
   }
  }else{

    $translator=array('name'=>'Product Name');
    
    if(array_key_exists($_REQUEST['key'],$translator))
      $key=$translator[$_REQUEST['key']];
    else
      $key=$_REQUEST['key'];
    $value=stripslashes(urldecode($_REQUEST['value']));  
    $product->update($key,$value);
  }
  

  $response= array('state'=>200,'updated_fields'=>$product->updated_fields,'errors_while_updating'=>$product->errors_while_updating);
  echo json_encode($response);  
}

function list_products_for_edition(){
 $conf=$_SESSION['state']['products']['table'];
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
  
  if(isset( $_REQUEST['where']))
    $where=$_REQUEST['where'];
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



  if(isset( $_REQUEST['percentages'])){
    $percentages=$_REQUEST['percentages'];
    $_SESSION['state']['products']['percentages']=$percentages;
  }else
    $percentages=$_SESSION['state']['products']['percentages'];
  
  

   if(isset( $_REQUEST['period'])){
    $period=$_REQUEST['period'];
    $_SESSION['state']['products']['period']=$period;
  }else
    $period=$_SESSION['state']['products']['period'];

 if(isset( $_REQUEST['avg'])){
    $avg=$_REQUEST['avg'];
    $_SESSION['state']['products']['avg']=$avg;
  }else
    $avg=$_SESSION['state']['products']['avg'];

  
   if(isset( $_REQUEST['tableid']))
    $tableid=$_REQUEST['tableid'];
  else
    $tableid=0;


   if(isset( $_REQUEST['parent']))
     $parent=$_REQUEST['parent'];
   else
     $parent=$conf['parent'];

   if(isset( $_REQUEST['mode']))
     $mode=$_REQUEST['mode'];
   else
     $mode=$conf['mode'];
   
    if(isset( $_REQUEST['restrictions']))
     $restrictions=$_REQUEST['restrictions'];
   else
     $restrictions=$conf['restrictions'];



     switch($parent){
     case('store'):
       $where=sprintf(' where `Product Family Store Key`=%d',$_SESSION['state']['store']['id']);
       break;
     case('department'):
       $where=sprintf(' left join `Product Department Bridge` B on (P.`Product Key`=B.`Product Key`) where `Product Department Key`=%d',$_SESSION['state']['department']['id']);
       break;
     case('family'):
       $where=sprintf(' where `Product Family Key`=%d',$_SESSION['state']['family']['id']);
       break;
     case('none'):
       $where=sprintf(' where true ');
       break;
     }
     $group='';
//      switch($mode){
//      case('same_code'):
//        $where.=sprintf(" and `Product Most Recent`='Yes' ");
//        break;
//      case('same_id'):
//        $group=' group by `Product ID`';
//        break;
//      }
//    
     switch($restrictions){
     case('forsale'):
       $where.=sprintf(" and `Product Sales State`='For Sale'  ");
       break;
     case('editable'):
       $where.=sprintf(" and `Product Sales State` in ('For Sale','In process','Unknown')  ");
       break;
     case('notforsale'):
       $where.=sprintf(" and `Product Sales State` in ('Not For Sale')  ");
       break;
     case('discontinued'):
       $where.=sprintf(" and `Product Sales State` in ('Discontinued')  ");
       break;
     case('all'):

       break;
     }


   $filter_msg='';



  $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
  
  
  $_SESSION['state']['products']['table']=array(
						'order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value,'mode'=>$mode,'restrictions'=>'','parent'=>$parent
);
  
  
  //  $where.=" and `Product Department Key`=".$id;

  
  
  $filter_msg='';
  $wheref='';
  if($f_field=='name' and $f_value!='')
    $wheref.=" and  ".$f_field." like '".addslashes($f_value)."%'";
  
    $sql="select count(*) as total from `Product Dimension`   P   $where $wheref";

   $result=mysql_query($sql);
   if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
     $total=$row['total'];
   }
   mysql_free_result($result);
   if($wheref==''){
       $filtered=0; $total_records=$total;
   }else{
     $sql="select count(*) as total  from `Product Dimension`  P  $where ";

     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       $filtered=$row['total']-$total; $total_records=$row['total'];
     }
     mysql_free_result($result);

   }
   $rtext=$total_records." ".ngettext('product','products',$total_records);
   if($total_records>$number_results)
     $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
   else
     $rtext_rpp='';
   
   $_order=$order;
   $_dir=$order_direction;
   
  if($order=='code')
    $order='`Product Code`';
  elseif($order=='name')
    $order='`Product Name`';
  else
    $order='`Product Code`';

  $sql="select *  from `Product Dimension` P  $where $wheref  order by $order $order_direction limit $start_from,$number_results    ";
  
  $res = mysql_query($sql);
  $adata=array();
  while($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
    if($row['Product Total Quantity Ordered']==0 and  $row['Product Total Quantity Invoiced']==0 and  $row['Product Total Quantity Delivered']==0  ){
      $delete='<img src="art/icons/delete.png" /> <span>'._('Delete').'<span>';
      $delete_type='delete';
    }else{
  $delete='<img src="art/icons/discontinue.png" /> <span>'._('Discontinue').'<span>';
      $delete_type='discontinue';
    }

    if($row['Product RRP']!=0 and is_numeric($row['Product RRP']))
      $customer_margin=_('CM').' '.number(100*($row['Product RRP']-$row['Product Price'])/$row['Product RRP'],1).'%';
    else
      $customer_margin=_('Not for resale');
    
    if($row['Product Price']!=0 and is_numeric($row['Product Cost']))
      $margin=number(100*($row['Product Price']-$row['Product Cost'])/$row['Product Price'],1).'%';
    else
      $margin=_('ND');
    global $myconf;
    $in_common_currency=$myconf['currency_code'];
    $in_common_currency_price='';
    if($row['Product Currency']!= $in_common_currency){
      if(!isset($exchange[$row['Product Currency']])){
	$exchange[$row['Product Currency']]=currency_conversion($row['Product Currency'],$in_common_currency);

      }
      $in_common_currency_price='('.money($exchange[$row['Product Currency']]*$row['Product Price']).') ';
      
    }


    if($row['Product Record Type']=='In Process'){

      if($row['Product Editing Price']!=0 and is_numeric($row['Product Cost']))
	$margin=number(100*($row['Product Editing Price']-$row['Product Cost'])/$row['Product Editing Price'],1).'%';
      else
	$margin=_('ND');
      global $myconf;
      $in_common_currency=$myconf['currency_code'];
      $in_common_currency_price='';
      if($row['Product Currency']!= $in_common_currency){
	if(!isset($exchange[$row['Product Currency']])){
	  $exchange[$row['Product Currency']]=currency_conversion($row['Product Currency'],$in_common_currency);
	  
	}
	$in_common_currency_price='('.money($exchange[$row['Product Currency']]*$row['Product Editing Price']).') ';
	
      }
      


      $processing=_('Editing');
      $name=$row['Product Editing Name'];
      $sdescription=$row['Product Editing Special Characteristic'];
      $famsdescription=$row['Product Editing Family Special Characteristic'];
      $price=money($row['Product Editing Price'],$row['Product Currency']);
      if(is_numeric($row['Product Editing Units Per Case']) and $row['Product Editing Units Per Case']!=1){
	$unit_price=money($row['Product Editing Price']/$row['Product Editing Units Per Case'],$row['Product Currency']);
      }else
	$unit_price='?';
      $units=$row['Product Editing Units Per Case'];
      $unit_type=$row['Product Editing Unit Type'];
      $units_info='';
    }else{

       if($row['Product Price']!=0 and is_numeric($row['Product Cost']))
	 $margin=number(100*($row['Product Price']-$row['Product Cost'])/$row['Product Price'],1).'%';
    else
      $margin=_('ND');
    global $myconf;
    $in_common_currency=$myconf['currency_code'];
    $in_common_currency_price='';
    if($row['Product Currency']!= $in_common_currency){
      if(!isset($exchange[$row['Product Currency']])){
	$exchange[$row['Product Currency']]=currency_conversion($row['Product Currency'],$in_common_currency);

      }
      $in_common_currency_price='('.money($exchange[$row['Product Currency']]*$row['Product Price']).') ';
      
    }


      $processing=_('Live');
      $name=$row['Product Name'];
      $sdescription=$row['Product Special Characteristic'];

      $price=money($row['Product Price'],$row['Product Currency']);
      $unit_price=money($row['Product Price']/$row['Product Units Per Case'],$row['Product Currency']);
      $units=$row['Product Units Per Case'];
      $unit_type=$row['Product Unit Type'];
      $units_info=number($row['Product Units Per Case']);
    }


    if($row['Product Record Type']=='New')
      $processing=_('Editing');

    switch($row['Product Sales State']){
    case('For sale'):
      case('Out of Stock'):
	$sales_state=_('For Sale');
	break;
    case('Discontinued'):
      $sales_state=_('Discontinue');
      break;
    case('Unknown'):
      $sales_state=_('Unknown');
      break;
    case('No Applicable'):
    case('Not for Sale'):
      $sales_state=_('Not For Sale');	
      break;
    }
    switch($row['Product Web State']){
    case('Online Force Out of Stock'):
      $web_state=_('Out of Stock');
	break;
    case('Online Auto'):
      $web_state=_('Auto');
      break;
    case('Unknown'):
      $web_state=_('Unknown');
    case('Offline'):
      $web_state=_('Offline');
      break;
    case('Online Force Hide'):
      $web_state=_('Hide');	
      break;
    case('Online Force For Sale'):
      $web_state=_('Sale');	
      break;

    }


    

    $state_info='';

$adata[]=array(
	       'id'=>$row['Product ID'],
	       'code'=>$row['Product Code'],
	       'code_price'=>sprintf('%s <a href="edit_product.php?pid=%d&edit=prices"><img src="art/icons/external.png"/></a>',$row['Product Code'],$row['Product ID']),
	       

	       'name'=>$row['Product Name'],
	       'processing'=>$processing,
	       'sales_state'=>$sales_state,
	       'web_state'=>$web_state,
	       'state_info'=>$state_info,
	       'sdescription'=>$sdescription,

	       'units'=>$units,
	       'units_info'=>$units_info,

	       'unit_type'=>$unit_type,
	       'price'=>$price,
	       'unit_price'=>$unit_price,
	       'margin'=>$margin,

	       'price_info'=>$in_common_currency_price,

	       'unit_rrp'=>money(($row['Product RRP']/$row['Product Units Per Case']),$row['Product Currency']),
	       'rrp_info'=>$customer_margin,

	       'delete'=>$delete,
	       'delete_type'=>$delete_type

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
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$start_from+$total,
			'records_perpage'=>$number_results,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'filtered'=>$filtered
			)
		  );

  echo json_encode($response);
}
function list_families_for_edition(){  
$conf=$_SESSION['state']['families']['table'];
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
  
  if(isset( $_REQUEST['where']))
    $where=$_REQUEST['where'];
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



  if(isset( $_REQUEST['percentages'])){
    $percentages=$_REQUEST['percentages'];
    $_SESSION['state']['families']['percentages']=$percentages;
  }else
    $percentages=$_SESSION['state']['families']['percentages'];
  
  

   if(isset( $_REQUEST['period'])){
    $period=$_REQUEST['period'];
    $_SESSION['state']['families']['period']=$period;
  }else
    $period=$_SESSION['state']['families']['period'];

 if(isset( $_REQUEST['avg'])){
    $avg=$_REQUEST['avg'];
    $_SESSION['state']['families']['avg']=$avg;
  }else
    $avg=$_SESSION['state']['families']['avg'];

  
   if(isset( $_REQUEST['tableid']))
    $tableid=$_REQUEST['tableid'];
  else
    $tableid=0;

   if(isset( $_REQUEST['parent'])){
     switch($_REQUEST['parent']){
     case('store'):
       $where=sprintf(' where `Product Family Store Key`=%d',$_SESSION['state']['store']['id']);
       break;
     case('department'):
       $where=sprintf('  where `Product Family Main Department Key`=%d',$_SESSION['state']['department']['id']);
       break;
     case('none'):
         $where=sprintf(' where true ');
       break;
     }
   }
   


   $filter_msg='';



  $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
  
  
  $_SESSION['state']['families']['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);
  
  
  //  $where.=" and `Product Department Key`=".$id;

  
  
  $filter_msg='';
  $wheref='';
  if($f_field=='name' and $f_value!='')
    $wheref.=" and  ".$f_field." like '".addslashes($f_value)."%'";
  
    $sql="select count(*) as total from `Product Family Dimension`   F   $where $wheref";

   $result=mysql_query($sql);
   if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
     $total=$row['total'];
   }
   mysql_free_result($result);
   if($wheref==''){
       $filtered=0; $total_records=$total;
   }else{
     $sql="select count(*) as total  from `Product Family Dimension`  F  $where ";

     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       $filtered=$row['total']-$total; $total_records=$row['total'];
     }
mysql_free_result($result);
   }
  
   $rtext=sprintf(ngettext("%d family", "%d families", $total_records), $total_records);
   if($total_records>$number_results)
     $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
   else
     $rtext_rpp='';
   
   $_order=$order;
   $_dir=$order_direction;
   
  if($order=='code')
    $order='`Product Family Code`';
  elseif($order=='name')
    $order='`Product Family Name`';
  
  $sql="select F.`Product Family Key`,`Product Family Code`,`Product Family Name`,`Product Family For Sale Products`+`Product Family In Process Products`+`Product Family Not For Sale Products`+`Product Family Discontinued Products`+`Product Family Unknown Sales State Products` as Products  from `Product Family Dimension` F  $where $wheref  order by $order $order_direction limit $start_from,$number_results    ";

  $res = mysql_query($sql);
  $adata=array();
  while($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
    if($row['Products']>0){
      $delete='<img src="art/icons/discontinue.png" /> <span xonclick="discontinue_family('.$row['Product Family Key'].')"  id="del_'.$row['Product Family Key'].'" style="cursor:pointer">'._('Discontinue').'<span>';
      $delete_type='discontinue';
    }else{
      $delete='<img src="art/icons/delete.png" /> <span xonclick="delete_family('.$row['Product Family Key'].')"  id="del_'.$row['Product Family Key'].'" style="cursor:pointer">'._('Delete').'<span>';
      $delete_type='delete';
    }
$adata[]=array(
	       'id'=>$row['Product Family Key'],
	       'edit'=>sprintf('<a href="family.php?id=%d&edit=1">%03d<a>',$row['Product Family Key'],$row['Product Family Key']),
	       'code'=>$row['Product Family Code'],
	       'name'=>$row['Product Family Name'],
	       'delete'=>$delete,
	       'delete_type'=>$delete_type

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
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$start_from+$total,
			'records_perpage'=>$number_results,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'filtered'=>$filtered
			)
		  );

  echo json_encode($response);
  }
function list_stores_for_edition(){
$conf=$_SESSION['state']['stores']['table'];

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
   

  if(isset( $_REQUEST['percentages'])){
    $percentages=$_REQUEST['percentages'];
    $_SESSION['state']['stores']['percentages']=$percentages;
  }else
    $percentages=$_SESSION['state']['stores']['percentages'];
  
  

   if(isset( $_REQUEST['period'])){
    $period=$_REQUEST['period'];
    $_SESSION['state']['stores']['period']=$period;
  }else
    $period=$_SESSION['state']['stores']['period'];

 if(isset( $_REQUEST['avg'])){
    $avg=$_REQUEST['avg'];
    $_SESSION['state']['stores']['avg']=$avg;
  }else
    $avg=$_SESSION['state']['stores']['avg'];

    $_SESSION['state']['stores']['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);
  // print_r($_SESSION['tables']['families_list']);

  //  print_r($_SESSION['tables']['families_list']);
$where=" ";
   
 $filter_msg='';
  $wheref='';
  if($f_field=='name' and $f_value!='')
    $wheref.=" and  ".$f_field." like '".addslashes($f_value)."%'";


  



   $sql="select count(*) as total from `Store Dimension`   $where $wheref";

   $result=mysql_query($sql);
   if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
     $total=$row['total'];
   }
   mysql_free_result($result);
   if($wheref==''){
       $filtered=0; $total_records=$total;
   }else{
     $sql="select count(*) as total `Store Dimension`   $where ";

     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       $filtered=$row['total']-$total;$total_records=$row['total'];
     }
mysql_free_result($result);
   }

    $rtext=$total_records." ".ngettext('store','stores',$total_records);
   if($total_records>$number_results)
     $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
   else
     $rtext_rpp='';


   $_dir=$order_direction;
   $_order=$order;
   

   if($order=='name')
     $order='`Store Name`';
   else if($order=='code')
     $order='`Store Code`';
   else
     $order='`Store Code`';



 
   $sql="select *  from `Store Dimension`  order by $order $order_direction limit $start_from,$number_results    ";
   
   $res = mysql_query($sql);
   $adata=array();
   //   print "$sql";
   while($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
     if($row['Store For Sale Products']>0){
       $delete='<img src="art/icons/discontinue.png" /> <span conclick="close_store('.$row['Store Key'].')"  id="del_'.$row['Store Key'].'" style="cursor:pointer">'._('Close').'<span>';
       $delete_type='close';
     }else{
       $delete='<img src="art/icons/delete.png" /> <span conclick="delete_store('.$row['Store Key'].')"  id="del_'.$row['Store Key'].'" style="cursor:pointer">'._('Delete').'<span>';
       $delete_type='delete';
     }
     $adata[]=array(
		    'id'=>$row['Store Key']
		    ,'code'=>$row['Store Code']
		    ,'name'=>$row['Store Name']
		    ,'delete'=>$delete
		    ,'delete_type'=>$delete_type
		  );
  }


   $total=mysql_num_rows($res);
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
function list_departments_for_edition(){
 if(!isset($_REQUEST['parent']))
     $parent='store';
  else
    $parent=$_REQUEST['parent'];


  if($parent=='store')  
    $conf=$_SESSION['state']['store']['table'];
  else
    $conf=$_SESSION['state']['departments']['table'];
  
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
   

   if(isset( $_REQUEST['percentages'])){
     $percentages=$_REQUEST['percentages'];
     $_SESSION['state']['store']['percentages']=$percentages;
   }else
     $percentages=$_SESSION['state']['store']['percentages'];
   
  

   if(isset( $_REQUEST['period'])){
     $period=$_REQUEST['period'];
     $_SESSION['state']['store']['period']=$period;
   }else
     $period=$_SESSION['state']['store']['period'];

   if(isset( $_REQUEST['avg'])){
     $avg=$_REQUEST['avg'];
     $_SESSION['state']['store']['avg']=$avg;
   }else
     $avg=$_SESSION['state']['store']['avg'];
   
   
   $store_id=$_SESSION['state']['store']['id'];



   $_SESSION['state']['store']['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);
   
   //$where=$where.' '.sprintf(" and `Product Department Store Key`=%d",$store_id);
   
   $filter_msg='';
   $wheref='';
   if($f_field=='name' and $f_value!='')
     $wheref.=" and  ".$f_field." like '".addslashes($f_value)."%'";
   
   
   switch($parent){
   case('store'):
     $where=sprintf(' where `Product Department Store Key`=%d',$_SESSION['state']['store']['id']);
     break;
   case('none'):
     $where=sprintf(' where true ');
     break;
   }
  

   $sql="select count(*) as total from `Product Department Dimension`   $where $wheref";
   // print $sql;
   $res = mysql_query($sql); 
   if($row=mysql_fetch_array($res)) {
     $total=$row['total'];
   }
   mysql_free_result($res);
   if($wheref==''){
       $filtered=0; $total_records=$total;
   }else{
     $sql="select count(*) as total `Product Department Dimension`   $where ";

     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      	$total_records=$row['total'];
	$filtered=$total_records-$total;
     }
     mysql_free_result($result);

   }

   $rtext=$total_records." ".ngettext('department','departments',$total_records);
   if($total_records>$number_results)
     $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
   else
     $rtext_rpp='';
   
   
   $_dir=$order_direction;
   $_order=$order;
   
  if($order=='name')
    $order='`Product Department Name`';
   elseif($order=='code')
    $order='`Product Department Code`';
  

    $sql="select D.`Product Department Key`,`Product Department Code`,`Product Department Name`,`Product Department For Sale Products`+`Product Department In Process Products`+`Product Department Not For Sale Products`+`Product Department Discontinued Products`+`Product Department Unknown Sales State Products` as Products  from `Product Department Dimension` D  $where $wheref  order by $order $order_direction limit $start_from,$number_results    ";
    $res = mysql_query($sql);
    $adata=array();
    //print "$sql";
    while($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
      if($row['Products']>0){
	$delete='<img src="art/icons/discontinue.png" /> <span  style="cursor:pointer">'._('Discontinue').'<span>';
	$delete_type='discontinue';
      }else{
	$delete='<img src="art/icons/delete.png" /> <span  style="cursor:pointer">'._('Delete').'<span>';
      $delete_type='delete';
    }


      $adata[]=array(
		     'id'=>$row['Product Department Key'],
		     'name'=>$row['Product Department Name'],
		     'code'=>$row['Product Department Code'],
		     'delete'=>$delete,
		     'delete_type'=>$delete_type
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
			 'total_records'=>$total,
			 'records_offset'=>$start_from,
			 'records_returned'=>$start_from+$total,
			 'records_perpage'=>$number_results,
			 'rtext'=>$rtext,
			 'rtext_rpp'=>$rtext_rpp,
			 'records_order'=>$order,
			 'records_order_dir'=>$order_dir,
			 'filtered'=>$filtered
			 )
		   );
   echo json_encode($response);
}


function delete_image(){
  $scope=$_REQUEST['scope'];
  $scope_key=$_REQUEST['scope_key'];
  $image_key=$_REQUEST['image_key'];

}

?>