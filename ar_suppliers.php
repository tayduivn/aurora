<?
require_once 'common.php';
require_once 'stock_functions.php';


if (!$LU or !$LU->isLoggedIn()) {
  $response=array('state'=>402,'resp'=>_('Forbidden'));
  echo json_encode($response);
  exit;
 }


if(!isset($_REQUEST['tipo']))
  {
    $response=array('state'=>405,'resp'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
  }

$tipo=$_REQUEST['tipo'];
switch($tipo){

 case('po_go'):

   if(isset( $_REQUEST['po_id']) and is_numeric( $_REQUEST['po_id']) ){
     // get tipo of po
     $sql=sprintf("select id,supplier_id,tipo,(select count(*) from porden_item where porden_id=porden.id) as items from porden where id=%d",$_REQUEST['po_id']);

     $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
     if($row=$res->fetchRow()) {
       $po_id=$row['id'];
       $supplier_id=$row['supplier_id'];
       $tipo=$row['tipo'];
       $items=$row['items'];
       switch($tipo){
       case(0):

	 if($items>0){
	   list($date,$error) =prepare_mysql_date($_REQUEST['expected_date']);
	   if($error){$response=array('state'=>400,'resp'=>_('Wrong date format, must be dd-mm-yyyy'));echo json_encode($response);break;}
	   
	   $date=$date.date(' H:i:s');
	   $sql="update  porden set date_submited=NOW(),date_expected='$date' , tipo=1 where id=".$po_id;
	   $db->exec($sql);
	   header('Location: porder.php?id='. $po_id);
	   exit;
	 }else{
	   $response=array('state'=>400,'resp'=>_('There are no items in this purchase order!'),);echo json_encode($response);break;
	 }

	 break;
       case(3):
	 $sql="delete from porden where id=".$po_id;
	 $db->exec($sql);

	  $sql="delete from porden_item where porden_id=".$po_id;
	 $db->exec($sql);
	 header('Location: supplier.php?id='. $supplier_id);
	 exit;
	 break;
       case(1):
	 // Ok baby let introduce all this products to the stock chain

	  $sql=sprintf("select date_received,id,supplier_id,tipo,(select count(*) from porden_item where porden_id=porden.id) as items from porden where id=%d",$_REQUEST['po_id']);

	  $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
	  if($row=$res->fetchRow()) {
	    $po_id=$row['id'];
	    $date_received=$row['date_received'];
	    if($row['tipo']!==1){$response=array('state'=>400,'resp'=>_('You can not do this operation'),);echo json_encode($response);break;}

	    $sql="update  porden set tipo=2 where id=".$po_id;
	    $db->exec($sql);

	 
	    $sql=sprintf("select (qty-damage) as qty   product_id,group_id from porden_item left join product as p (product_id=p.id) where porden_id=%d",$_REQUEST['po_id']);
	    $res2 = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
	    while($row2=$res2->fetchRow()) {
	      $product_id=$row2['product_id'];
	      $qty=$row2['qty'];
	      
	      update_stockhistory($product_id,$qty,2,$po_id,$date_received);
	    }

	  }





       case(2):
	 $sql="update  porden set tipo=1 where id=".$po_id;
	 $db->exec($sql);

	 $sql="delete from  stock_history  where  op_tipo=2  and op_id=".$po_id;
	 $db->exec($sql);
	   $sql=sprintf("select product_id,group_id from porden_item left join product as p (product_id=p.id) where porden_id=%d",$_REQUEST['po_id']);
	   $res2 = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
	   $fams=array();
	   while($row2=$res2->fetchRow()) {
	     $fams[]=$row2['group_id'];
	     update_stockhistoryline($row2['product_id']);
	   }
	   $fams=array_unique($fams);
	   foreach($fams as $fam)
	     update_family($fam);
       }


     }

   }


   break;
case('po_goback'):

   if(isset( $_REQUEST['po_id']) and is_numeric( $_REQUEST['po_id']) ){
     // get tipo of po
     $sql=sprintf("select id,supplier_id,tipo from porden where id=%d",$_REQUEST['po_id']);

     $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
     if($row=$res->fetchRow()) {
       $po_id=$row['id'];
       $supplier_id=$row['supplier_id'];
       $tipo=$row['tipo'];

       switch($tipo){
       case(0):
       case(3):
	 $sql="delete from porden where id=".$po_id;
	 $db->exec($sql);

	  $sql="delete from porden_item where porden_id=".$po_id;
	 $db->exec($sql);
	 header('Location: supplier.php?id='. $supplier_id);
	 exit;
	 break;
       case(1):
	 $sql="update  porden set tipo=3 where id=".$po_id;
	 $db->exec($sql);
	 header('Location: supplier.php?id='. $supplier_id);
	  exit;
	 break;
       case(2):
	 $sql="update  porden set tipo=1 where id=".$po_id;
	 $db->exec($sql);

	 $sql="delete from  stock_history  where  op_tipo=2  and op_id=".$po_id;
	 $db->exec($sql);
	   $sql=sprintf("select product_id,group_id from porden_item left join product as gpon (product_id=p.id) where porden_id=%d",$_REQUEST['po_id']);
	   $res2 = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
	   $fams=array();
	   while($row2=$res2->fetchRow()) {
	     $fams[]=$row2['group_id'];
	     update_stockhistoryline($row2['product_id']);
	   }
	   $fams=array_unique($fams);
	   foreach($fams as $fam)
	     update_family($fam);
       }


     }

   }


   break;
case('po'):
   if(isset( $_REQUEST['sf']))
     $start_from=$_REQUEST['sf'];
   else
    $start_from=$_SESSION['tables']['po_item'][3];
  if(isset( $_REQUEST['nr']))
    $number_results=$_REQUEST['nr'];
  else
    $number_results=$_SESSION['tables']['po_item'][2];
  if(isset( $_REQUEST['o']))
    $order=$_REQUEST['o'];
  else
    $order=$_SESSION['tables']['po_item'][0];
  if(isset( $_REQUEST['od']))
    $order_dir=$_REQUEST['od'];
  else
    $order_dir=$_SESSION['tables']['po_item'][1];
  

  $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
  
  

   if(isset( $_REQUEST['supplier_id'])  and  is_numeric($_REQUEST['supplier_id'])  )
     $supplier_id= $_REQUEST['supplier_id'];
   else
     $supplier_id=$_SESSION['tables']['po_item'][4][1];
  
   if(isset( $_REQUEST['po_id'])  and  is_numeric($_REQUEST['po_id'])  )
     $po_id= $_REQUEST['po_id'];
   else
     $po_id=$_SESSION['tables']['po_item'][4][0];

  if(isset( $_REQUEST['view_all'])  and  is_numeric($_REQUEST['view_all'])  )
     $view_all= $_REQUEST['view_all'];
   else
     $view_all=$_SESSION['tables']['po_item'][4][2];


   if(isset( $_REQUEST['where']))
     $where=addslashes($_REQUEST['where']);
   else
     $where=$_SESSION['tables']['po_item'][5];


  if(isset( $_REQUEST['f_field']))
     $f_field=$_REQUEST['f_field'];
   else
     $f_field=$_SESSION['tables']['po_item'][6];

  if(isset( $_REQUEST['f_value']))
     $f_value=$_REQUEST['f_value'];
  else
    $f_value=$_SESSION['tables']['po_item'][7];
  



  $_SESSION['tables']['po_item']=array($order,$order_direction,$number_results,$start_from,array($po_id,$supplier_id,$view_all,$_SESSION['tables']['po_item'][4][3]),$where,$f_field,$f_value);
  //  print_r($_SESSION['tables']['po_item']);

  $wheref='';

    // if( ($f_field=='public_id'   or  $f_field=='customer_name')  and $f_value=!'' )
  //   $wheref.=" and   $f_field like '".addslashes($f_value)."%'   ";
  $wheref='';
  if(($f_field=='p.code' or $f_field=='sup_code') and $f_value!='')
    $wheref.=" and  ".$f_field." like '".addslashes($f_value)."%'";

  



  if($view_all){

    $where=$where.' and ps.supplier_id='.$supplier_id;

  $sql="select count(*) as total from product  as p left join product_group as g on (g.id=group_id) left join product_department as d on (d.id=department_id) left join product2supplier as ps on (product_id=p.id) $where $wheref ";

  $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
  if($row=$res->fetchRow()) {
    $total=$row['total'];
  }
    if($wheref==''){
      $filtered=0;
    }else{
      
      $sql="select count(*) as total from product  as p left join product_group as g on (g.id=group_id) left join product_department as d on (d.id=department_id) left join product2supplier as ps on (product_id=p.id)  $where  ";
      $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
      if($row=$res->fetchRow()) {
	$filtered=$row['total']-$total;
      }
      
    }

  //   if($_SESSION['tables']['po_item'][4][3]==0 or $_SESSION['tables']['po_item'][4][3]==3)
//       $sql="select  
// (select sum(expected_qty) from porden_item where porden_id=$po_id and p2s_id=ps.id) as ordered,
// 0  as received,
// 0  as damage,
// ps.price as expected_price_unit,

//     p.units_tipo,sup_code,ps.id as p2s_id,(p.units*ps.price) as price_outer,ps.price as price_unit,stock,p.condicion as condicion, p.code as code, p.id as id,p.description as description , group_id,g.name as fam
// from product as p left join product_group as g on (g.id=group_id)  left join product2supplier as ps on (product_id=p.id)  $where $wheref  order by $order $order_direction limit $start_from,$number_results ";
//     elseif($_SESSION['tables']['po_item'][4][3]==1)  
//       $sql="select  
// (select sum(expected_qty) from porden_item where porden_id=$po_id and p2s_id=ps.id) as ordered,
// (select sum(expected_price) from porden_item where porden_id=$po_id and p2s_id=ps.id) as expected_price_unit,
// 0  as received,
// 0  as damage,
//     p.units_tipo,sup_code,ps.id as p2s_id,(p.units*ps.price) as price_outer,ps.price as price_unit,stock,p.condicion as condicion, p.code as code, p.id as id,p.description as description , group_id,g.name as fam
// from product as p left join product_group as g on (g.id=group_id)  left join product2supplier as ps on (product_id=p.id)  $where $wheref  order by $order $order_direction limit $start_from,$number_results ";
//     elseif($_SESSION['tables']['po_item'][4][3]==2)  
      $sql="select  
(select sum(expected_qty) from porden_item where porden_id=$po_id and p2s_id=ps.id) as ordered,
(select sum(qty) from porden_item where porden_id=$po_id and p2s_id=ps.id) as received,
(select sum(damage) from porden_item where porden_id=$po_id and p2s_id=ps.id) as damage,
(select sum(price) from porden_item where porden_id=$po_id and p2s_id=ps.id) as price_unit,
ps.price as expected_price_unit,

    p.units_tipo,sup_code,ps.id as p2s_id,stock,p.condicion as condicion, p.code as code, p.id as id,p.description as description , group_id,g.name as fam
from product as p left join product_group as g on (g.id=group_id)  left join product2supplier as ps on (product_id=p.id)  $where $wheref  order by $order $order_direction limit $start_from,$number_results ";
    

    
  }else{
    $where=$where.' and porden_id='.$po_id;
    
    $sql="select count(*) as total from porden_item $where $wheref ";

  $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
  if($row=$res->fetchRow()) {
    $total=$row['total'];
  }

  if($wheref==''){
      $filtered=0;
    }else{
      
      $sql="select count(*) as total from porden_item  $where  ";
      $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
      if($row=$res->fetchRow()) {
	$filtered=$row['total']-$total;
      }
      
    }
  $sql="select  qty as received,damage,expected_qty as ordered ,    p.units_tipo,sup_code,ps.id as p2s_id,porden_item.price as price_unit,porden_item.expected_price as expected_price_unit,stock,p.condicion as condicion, p.code as code, p.id as id,p.description as description , group_id,g.name as fam
from porden_item left join product2supplier as ps on (p2s_id=ps.id)  left join  product as p on (ps.product_id=p.id) left join product_group as g on (g.id=group_id)   $where $wheref  order by $order $order_direction limit $start_from,$number_results ";


  }  
 // print $sql;
   $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
 $data=array();
   while($row=$res->fetchRow()) {

     if($view_all)
       $code=$row['code'].' ('.$row['fam'].')';
     else
       $code=$row['code'];

     if($_SESSION['tables']['po_item'][4][3]==2)
       $price=$row['price_unit'];
     else{
       
       $price=$row['expected_price_unit'];
       if($_SESSION['tables']['po_item'][4][3]==1 and $row['price_unit']!=''){
	 $price=$row['price_unit'];
       }

     }
     

     $ordered=($row['ordered']==''?0:$row['ordered']);
     $eprice=$ordered*$price;


     



     $received=$row['received'];
     if($_SESSION['tables']['po_item'][4][3]==1 and $row['received']==''){
       $received=$ordered;
       
     }

     if($_SESSION['tables']['po_item'][4][3]==2 or $_SESSION['tables']['po_item'][4][3]==1 ){
       $eprice=$received*$price;
	 
     }
     


     $data[]=array(
		   'id'=>$row['id'],
		   'p2s_id'=>$row['p2s_id'],


		   'price'=>money($price),

		   'stock'=>($row['stock']==''?'':number($row['stock'])),
		   'code'=>$code,
		   'sup_code'=>$row['sup_code'],
		   'description'=>$row['description'],
		   'units_tipo'=>$_units_tipo[$row['units_tipo']],
		   'units_tipo_id'=>$row['units_tipo'],
		   'ordered'=>number($ordered),
		   'eprice'=>money($eprice),
		   'damage'=>number($row['damage']),
		   'received'=>number($received),

		   );
   }
   
   if($total<$number_results)
     $rtext=$total.' '.ngettext('item returned','items returned',$total);
   else
     $rtext='';
   $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$data,
			 'total_records'=>$total,
			 'records_offset'=>$start_from,
			 'records_returned'=>$start_from+$res->numRows(),
			 'records_perpage'=>$number_results,
			 'records_text'=>$rtext,
			 'records_order'=>$order,
			 'records_order_dir'=>$order_dir,
			 'filtered'=>$filtered
			 )
		   );
   echo json_encode($response);
   break;

 case('changesupplierblock'):
   if(isset($_REQUEST['value']) and isset($_REQUEST['block'])){
     $value=$_REQUEST['value'];
     $block=$_REQUEST['block'];
	  

     if(is_numeric($value) and ($value==0 or $value==1)    and is_numeric($block) and $value>=0 and $value<2      )
       $_SESSION['views']['supplier_blocks'][$block]=$value;
   }
   break;
   

   case('pos'):
     
     if(isset( $_REQUEST['id']) and is_numeric($_REQUEST['id']))     $supplier_id=$_REQUEST['id'];
   else
     $supplier_id=$_SESSION['tables']['po_list'][4];

   if(isset( $_REQUEST['sf']))
     $start_from=$_REQUEST['sf'];
   else
     $start_from=$_SESSION['tables']['po_list'][3];
   if(isset( $_REQUEST['nr']))
     $number_results=$_REQUEST['nr'];
   else
     $number_results=$_SESSION['tables']['po_list'][2];
   if(isset( $_REQUEST['o']))
     $order=$_REQUEST['o'];
   else
     $order=$_SESSION['tables']['po_list'][0];
   if(isset( $_REQUEST['od']))
     $order_dir=$_REQUEST['od'];
   else
     $order_dir=$_SESSION['tables']['po_list'][1];
   
   
   


   $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
   


   

    if(isset( $_REQUEST['where']))
     $where=addslashes($_REQUEST['where']);
   else
     $where=$_SESSION['tables']['po_list'][5];

    
   if(isset( $_REQUEST['f_field']))
     $f_field=$_REQUEST['f_field'];
   else
     $f_field=$_SESSION['tables']['po_list'][6];

  if(isset( $_REQUEST['f_value']))
     $f_value=$_REQUEST['f_value'];
   else
     $f_value=$_SESSION['tables']['po_list'][7];

  if(isset($_REQUEST['tview'])){
    $tview=$_REQUEST['tview'];
    $_SESSION['views']['pos_table_options'][$tview]=($_SESSION['views']['pos_table_options'][$tview]?0:1);
  }

  $_SESSION['tables']['po_list']=array($order,$order_direction,$number_results,$start_from,$supplier_id,$where,$f_field,$f_value);
  
  $view='';
  foreach($_SESSION['views']['pos_table_options'] as $key=>$val){
    if(!$val)
      $view.=' and tipo!='.$key;
  }


  

  $where =$where.$view.' and supplier_id='.$supplier_id;
  $wheref='';

  // if($f_field=='max' and is_numeric($f_value) )
  // $wheref.=" and  (TO_DAYS(NOW())-TO_DAYS(date_index))<=".$f_value."    ";
  //else if($f_field=='min' and is_numeric($f_value) )
  // $wheref.=" and  (TO_DAYS(NOW())-TO_DAYS(date_index))>=".$f_value."    ";
  if($f_field=='public_id' and $f_value!='')
    $wheref.=" and  ".$f_field." like '".addslashes($f_value)."%'";


   

   
   $sql="select count(*) as total from porden   $where $wheref ";
   // print "$sql";
   $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
  if($row=$res->fetchRow()) {
    $total=$row['total'];
  }
  if($where==''){
    $filtered=0;
  }else{
    
      $sql="select count(*) as total from porden  $where";
      $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
      if($row=$res->fetchRow()) {
	$filtered=$row['total']-$total;
      }
      
  }
  
  

  $sql="select tipo,public_id,UNIX_TIMESTAMP(date_index) as date_index ,id,total from porden  $where $wheref  order by $order $order_direction limit $start_from,$number_results ";
  //print $sql;
   $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
   $data=array();
   while($row=$res->fetchRow()) {
     $data[]=array(
		   'id'=>sprintf("%05d",$row['id']),
		   'public_id'=>$row['public_id'],
		   //		   'customer_name'=>$row['customer_name'],
		   //'customer_id'=>$row['customer_id'],
		   //		   'date_index'=>$row['date_index'],
		   // 'date_index'=> strftime("%A %e %B %Y", strtotime('@'.$row['date_index'])),
		   'date_index'=> strftime("%A %e %B %Y", strtotime('@'.$row['date_index'])),
		   //		   'date_invoice'=> strftime("%A %e %B %Y", strtotime('@'.$row['date_invoice'])),
		   'tipo'=>$_porder_tipo[$row['tipo']],
		   'total'=>money($row['total'])
		   //'titulo'=>$_order_tipo[$row['tipo']],
		   //'tipo'=>$row['tipo'],
		   //		   'desde'=>$row['desde'],
		   //'file'=>$row['original_file']
		   );
   }

   

   if($total<$number_results)
     $rtext=$total.' '.ngettext('delivery note','delivery notes',$total);
   else
     $rtext='';
   $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$data,
			 'total_records'=>$total,
			 'records_offset'=>$start_from,
			 'records_returned'=>$start_from+$res->numRows(),
			 'records_perpage'=>$number_results,
			 'records_text'=>$rtext,
			 'records_order'=>$order,
			 'records_order_dir'=>$order_dir,
			 'filtered'=>$filtered
			 )
		   );
   echo json_encode($response);
   break;

 case('dn_items'):
   $sum_qty=0;
   $sum_qty2=0;
   $sum_price=0;
   $total_diff=0;
   //   print_r($_SESSION['tables']['dn_item'][0]);
   $dn_id=$_SESSION['tables']['dn_item'][0];
   //   print_r($_SESSION['tables']['dn_item'][0]);
   $sql=sprintf("select p2s.product_id,p.code,sup_code,p.units,p.description,p.units_tipo,po.qty as qty,po.expected_qty,expected_price,po.price as unit_cost from porden_item as po left join product2supplier as p2s on (p2s.id=p2s_id) left join product as p on  (p.id=p2s.product_id) where po.porden_id=%d",$dn_id);
   
   // print "$sql";
   $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
   $data=array();
   while($row=$res->fetchRow()) {

     $qty=$row['expected_qty'];
     $product_id=$row['product_id'];
     
     $qty2=$row['qty'];
     $cost=$row['qty']*$row['unit_cost'];
     
     $old_price=$row['expected_price'];
     $price=$row['unit_cost'];
     
     $sum_qty+=$qty;
     $sum_qty2+=$qty2;
     $sum_price+=$cost;
     //  $total_diff+=$num_diff;
     $diff='';
     
     $data[]=array(
		   'product_id'=>$product_id,
		   'code'=>$row['code'],
		   'sup_code'=>$row['sup_code'],
		   'description'=>number($row['units']).' ('.$_units_tipo[$row['units_tipo']].')x '.$row['description'],
		   'units'=>number($row['units']),
		   'units_tipo'=>$row['units_tipo'],
		   'units_tipof'=>$_units_tipo[$row['units_tipo']],
		   'qty'=>number($qty),
		   'qty2'=>number($qty2),
		   'cost'=>money($cost),
		   'price'=>($diff!=''?' ('.$diff.') ':'').($cost==0?'':money($price)),
		   'old_price'=>money($row['expected_price']),
		   'dif'=>$diff
		   
		   
		   
		   );

     
   }
   
 

   $data[]=array(
		 'product_id'=>0,
		'code'=>_('Subtotals'),
		 'sup_code'=>'',
		 'description'=>'',
		'units'=>number($row['units']),
		'units_tipo'=>'',
		'units_tipof'=>'',
		'qty'=>number($sum_qty),
		'qty2'=>number($sum_qty2),
		'cost'=>money($sum_price),
		'price'=>'',
		'old_price'=>'',
		'dif'=>''
		
		 );
   

  //  print_r($data);
   
   $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$data,
			 )
		   );
echo json_encode($response);
break;




case('dn_items_new'):
$sum_qty=0;
$sum_qty2=0;
$sum_price=0;
$total_diff=0;

$supplier_id=$_SESSION['deliver_note']['supplier_id'];

   $sql=sprintf("select p2s.id as p2s_id,p.units_tipo,p2s.price as price,  p.id as product_id,p.code ,p2s.sup_code,p.description as description ,p.units    from product as p left join product2supplier as p2s on (p.id=product_id) where p2s.supplier_id=%d",$supplier_id);
   
   
   $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
   $data=array();
   while($row=$res->fetchRow()) {
     $product_id=$row['product_id'];
     $p2s_id=$row['p2s_id'];
     if(isset($_SESSION['deliver_note']['items'][$p2s_id])  and $_SESSION['deliver_note']['items'][$p2s_id][0]>0  ){
       $_SESSION['deliver_note']['items'][$p2s_id][3]=$row['price'];
       
       $qty=$_SESSION['deliver_note']['items'][$p2s_id][0];
       $qty2=$_SESSION['deliver_note']['items'][$p2s_id][1];
       //    $price=$_SESSION['deliver_note']['items'][$p2s_id][2];

       if($qty2==0)
	 $cost=0;
       else
	 $cost=$_SESSION['deliver_note']['items'][$p2s_id][2];
       
       if($qty2>0)
	 $price=$cost/$qty2;
       else
	 $price='';
	 
       if($price!='')
	 $_SESSION['deliver_note']['items'][$p2s_id][4]=$price;
       else
	 $_SESSION['deliver_note']['items'][$p2s_id][4]=$row['price'];
       
       
       $num_diff=0;

       if($row['price']=='' or $row['price']==0 or $cost==0 or $price==0)
	 $diff='';
       else{
	 
	 $diff=$price-$row['price'];
	 $num_diff=$diff;
	 if($diff!=0)
	   $diff=number(100*$diff/$row['price'],1).'%';
	 else
	   $diff='';
	 
       }
       


       $sum_qty+=$qty;
       $sum_qty2+=$qty2;
       $sum_price+=$cost;
       $total_diff+=$num_diff;
       

       $data[]=array(
		   'product_id'=>$product_id,
		   'code'=>$row['code'],
		   'sup_code'=>$row['sup_code'],
		   'description'=>number($row['units']).' ('.$_units_tipo[$row['units_tipo']].')x '.$row['description'],
		   'units'=>number($row['units']),
		   'units_tipo'=>$row['units_tipo'],
		   'units_tipof'=>$_units_tipo[$row['units_tipo']],
		   'qty'=>number($qty),
		   'qty2'=>number($qty2),
		   'cost'=>money($cost),
		   'price'=>($diff!=''?' ('.$diff.') ':'').($cost==0?'':money($price)),
		   'old_price'=>money($row['price']),
		   'dif'=>$diff



		   );


     }

   }
   
  $data[]=array(
		'product_id'=>0,
		'code'=>_('Subtotals'),
		'sup_code'=>'',
		'description'=>'',
		'units'=>number($row['units']),
		'units_tipo'=>'',
		'units_tipof'=>'',
		'qty'=>number($sum_qty),
		'qty2'=>number($sum_qty2),
		'cost'=>money($sum_price),
		'price'=>'',
		'old_price'=>'',
		'dif'=>''
		
		 );
   

  //  print_r($data);
   
   $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$data,
			 )
		   );
   echo json_encode($response);
   break;





 case('index'):
   
 if(!$LU->checkRight(SUP_VIEW))
    exit;

    $conf=$_SESSION['state']['suppliers']['table'];
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
    if(isset( $_REQUEST['f_field']))
     $f_field=$_REQUEST['f_field'];
   else
     $f_field=$conf['f_field'];

  if(isset( $_REQUEST['f_value']))
     $f_value=$_REQUEST['f_value'];
   else
     $f_value=$conf['f_value'];
if(isset( $_REQUEST['where']))
     $where=$_REQUEST['where'];
   else
     $where=$conf['where'];
  
   if(isset( $_REQUEST['tableid']))
    $tableid=$_REQUEST['tableid'];
  else
    $tableid=0;
   $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
  $_SESSION['state']['suppliers']['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);
  $_order=$order;
  $_dir=$order_direction;
  $filter_msg='';


   $wheref='';
  if(($f_field=='code' or $f_field=='name') and $f_value!='')
    $wheref.=" and  ".$f_field." like '".addslashes($f_value)."%'";
  elseif($f_field=='low' and is_numeric($f_value))
    $wheref.=" and lowstock>=$f_value  ";
   elseif($f_field=='outofstock' and is_numeric($f_value))
    $wheref.=" and outofstock>=$f_value  ";


   $sql="select count(*) as total from supplier    $where $wheref";
   $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
   if($row=$res->fetchRow()) {
    $total=$row['total'];
   }
   if($wheref==''){
     $filtered=0;
   }else{
     $sql="select count(*) as total from supplier $where      ";
     $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
     if($row=$res->fetchRow()) {
       $filtered=$row['total']-$total;
     }
     
   }
   

   $sql="select id,code,name,contact_id,products,active,outofstock,lowstock from supplier $where $wheref order by $order $order_direction limit $start_from,$number_results";
   
   
   $res = $db->query($sql); if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
   $data=array();
   while($row=$res->fetchRow()) {

     $id="<a href='supplier.php?id=".$row['id']."'>".$myconf['supplier_id_prefix'].sprintf("%05d",$row['id']).'</a>';
     $data[]=array(
		   'id'=>$id,
		   'code'=>$row['code'],
		   'name'=>$row['name'],
		   'products'=>number($row['products']),
		   'active'=>number($row['active']),
		   'outofstock'=>number($row['outofstock']),
		   'lowstock'=>number($row['lowstock']),
		   'delete'=>'<img src="art/icons/status_busy.png"/>'

		   );
   }
   
   if($total==0){
     $rtext=_('No suppliers have been registered yet').'.';
   }else if($total<$number_results)
     $rtext=$total.' '.ngettext('supplier returned','suppliers returned',$total);
   else
     $rtext='';
   $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$data,
			 'sort_key'=>$_order,
			 'sort_dir'=>$_dir,
			 'tableid'=>$tableid,
			 'filter_msg'=>$filter_msg,
			 'total_records'=>$total,
			 'records_offset'=>$start_from,
			 'records_returned'=>$start_from+$res->numRows(),
			 'records_perpage'=>$number_results,
			 'records_text'=>$rtext,
			 'records_order'=>$order,
			 'records_order_dir'=>$order_dir,
			 'filtered'=>$filtered
			 )
		   );
   echo json_encode($response);
   break;

//   $sql="select s.id as id ,s.code as code, 
//  ( select count(*) from product2supplier left join product as p on (p.id=product_id) where condicion!=0 and stock=0  and product2supplier.supplier_id=s.id ) as discontinued,
//  ( select count(*) from product2supplier left join product as p on (p.id=product_id) where (condicion=0 or (condicion!=0 and stock>0) ) and product2supplier.supplier_id=s.id ) as active,
//   ( select count(*) from product2supplier  where supplier_id=s.id ) as total, ( select count(*) from product2supplier left join product as p on (p.id=product_id) where condicion=0 and stock=0  and product2supplier.supplier_id=s.id ) as outstock 
// , ( select count(*) from product2supplier left join product as p on (p.id=product_id) where (isnull(stock) or stock<0)  and product2supplier.supplier_id=s.id ) as error
// from supplier as s  order by $order $order_direction limit $start_from,$number_results  ";
  //  print $sql;
// $result=mysql_query($sql);
//   $adata=array();
//   while($data=mysql_fetch_array($result, MYSQL_ASSOC)){
//     $adata[]=array(
// 		  'id'=>$data['id'],
// 		  'code'=>$data['code'],
// 		  //'discontinued'=>$data['discontinued'],
// 		  //'total'=>$data['total'],
// 		  //'error'=>$data['error'],
// 		  //'active'=>$data['active'],
// 		  //'outstock'=>$data['outstock']
// 		   );
//   }
//    $response=array('resultset'=>
// 		   array('state'=>200,
// 			 'data'=>$adata
// 			 )
// 		   );
//    echo json_encode($response);
//    break; 
 case('supplier'):
   
   $id=$_REQUEST['id'];
   $start_from=$_REQUEST['sf'];
   $number_results=$_REQUEST['nr'];
   $order=$_REQUEST['o'];
   $order_direction=(preg_match('/desc/',$_REQUEST['od'])?'desc':'');
   










   $sql="select p.id as id ,p.code as code,p.description as description,p.stock as stock from product as p  left join product2supplier as p2s on (product_id=p.id) where p2s.supplier_id=$id order by $order $order_direction limit $start_from,$number_results ";
   $result=mysql_query($sql);
   $adata=array();
   while($data=mysql_fetch_array($result, MYSQL_ASSOC)){
    $adata[]=array(
		  'id'=>$data['id'],
		  'code'=>$data['code'],
		  'description'=>$data['description'],
		  'stock'=>$data['stock'],
		  'delete'=>'<img src="art/icons/status_busy.png"/>'

		   );
  }
   $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$adata
			 )
		   );
   echo json_encode($response);
   break; 


 case('update_po'):
   $key=$_REQUEST['key'];
   switch($key){
   case('shipping'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       $total=$qty+$row['goods']+$row['vat']+$row['charges'];
       $sql=sprintf("update porden set shipping='%s', total='%s' where id=%d",$qty,$total,$po_id);
       $db->exec($sql);
       $total_int=number($total,0);
       $total_decimal=money_cents($total);
       $response=array('state'=>200,'total_int'=>$total_int,'total_decimal'=>$total_decimal,'total'=>money($total),'value'=>money($qty));
        echo json_encode($response);
     }
     break;
     case('vat'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       $total=$qty+$row['goods']+$row['shipping']+$row['charges'];
       $sql=sprintf("update porden set vat='%s', total='%s' where id=%d",$qty,$total,$po_id);
       $db->exec($sql);
       $total_int=number($total,0);
       $total_decimal=money_cents($total);
       $response=array('state'=>200,'total_int'=>$total_int,'total_decimal'=>$total_decimal,'total'=>money($total),'value'=>money($qty));
        echo json_encode($response);
     }
     break;
   case('other'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       $total=$qty+$row['goods']+$row['shipping']+$row['vat'];
       $sql=sprintf("update porden set charges='%s', total='%s' where id=%d",$qty,$total,$po_id);
       $db->exec($sql);
       $total_int=number($total,0);
       $total_decimal=money_cents($total);
       $response=array('state'=>200,'total_int'=>$total_int,'total_decimal'=>$total_decimal,'total'=>money($total),'value'=>money($qty));
       echo json_encode($response);
   }
     break;
 case('invoice_number'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {

       $sql=sprintf("update porden set public_id='%s' where id=%d",addslashes($qty),$po_id);
       $db->exec($sql);
       $response=array('state'=>200);
       echo json_encode($response);
   }
     break;
 case('checked_by'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     if(!is_numeric($qty)){$response=array('state'=>400);echo json_encode($response);break;}
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {

       if($qty>0){
	 $sql=sprintf("select alias  from staff  where id='%d' ",$qty);
	 $res2 = $db->query($sql); 
	 if($row2=$res2->fetchRow())
	   $alias=$row2['alias'];
       }else if($qty==0)
	 $alias=_('Nobody');
       else
	 $alias='NULL';
	   
       $sql=sprintf("update porden set checked_by=%s where id=%d",$qty,$po_id);
       $db->exec($sql);
       $response=array('state'=>200,'alias'=>$row2['alias']);
       echo json_encode($response);
   }
     break;
 case('received_by'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     if(!is_numeric($qty)){$response=array('state'=>400);echo json_encode($response);break;}
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {

       if($qty>0){
	 $sql=sprintf("select alias  from staff  where id='%d' ",$qty);
	 $res2 = $db->query($sql); 
	 if($row2=$res2->fetchRow())
	   $alias=$row2['alias'];
       }else if($qty==0)
	 $alias=_('Nobody');
       else
	 $alias='NULL';
	   
       $sql=sprintf("update porden set received_by=%s where id=%d",$qty,$po_id);
       $db->exec($sql);
       $response=array('state'=>200,'alias'=>$row2['alias']);
       echo json_encode($response);
   }
     break;

 case('invoice_date'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       list($date,$error)=prepare_mysql_date($qty);
       if($error){$response=array('state'=>400,'resp'=>_('Wrong date format, must be dd-mm-yyyy'));echo json_encode($response);break;}
       
       $sql=sprintf("update porden set date_invoice='%s' where id=%d",$date,$po_id);
       $db->exec($sql);
       $response=array('state'=>$sql);
       echo json_encode($response);
   }
     break;
 case('time_received'):
     $po_id=$_REQUEST['po_id'];
     $qty=$_REQUEST['qty'];
     $sql=sprintf("select *  from porden  where id='%d' ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       list($date,$error)=prepare_mysql_datetime($qty);
       if($error){$response=array('state'=>400,'resp'=>_('Wrong date format, must be dd-mm-yyyy'));echo json_encode($response);break;}
       
       $sql=sprintf("update porden set date_received='%s' where id=%d",$date,$po_id);
       $db->exec($sql);
       $response=array('state'=>200);
       echo json_encode($response);
   }
     break;
   }
   $response=array('state'=>400);
   break;
 case('update_poitem'):

   $key=$_REQUEST['key'];
   switch($key){
   case('ordered'):
     $qty=$_REQUEST['qty'];
     $units_tipo=$_REQUEST['units_tipo'];

     $po_id=$_SESSION['tables']['po_item'][4][0];
     $p2s_id=$_REQUEST['p2s_id'];

     $sql=sprintf("select price from product2supplier  where id='%d' ",$p2s_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       $price=$row['price'];
       
     }
     
     
     
     $expected_price=$price;
     
     $sql=sprintf("select id from porden_item  where porden_id='%d' and  p2s_id='%d' ",$po_id,$p2s_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       if($qty==0)
	 $sql=sprintf("delete from porden_item where id=%d",$row['id']);
       else
	 $sql=sprintf("update porden_item set expected_qty='%s'   where id=%d",$qty,$row['id']);
       $db->exec($sql);
       
     }else{
       $sql=sprintf("insert into porden_item (porden_id,p2s_id,expected_qty,units_tipo,expected_price) value (%d,%d,'%s',%d,'%s')",$po_id,$p2s_id,$qty,$units_tipo,$expected_price);
       $db->exec($sql);
     }

     $expected_price=$expected_price*$qty;

     $total_expected_price=0;
     $items=0;
     $sql=sprintf("select sum(expected_qty*expected_price) as ep ,count(*) as items from porden_item where  porden_id='%d'  ",$po_id);
     $res = $db->query($sql); 
      if($row=$res->fetchRow()) {
	$total_expected_price=$row['ep'];
	$items=$row['items'];
	$sql=sprintf("update porden set total='%s',goods='%s' ,items=%d  where id=%d",$row['ep'],$row['ep'],$items,$po_id);
       $db->exec($sql);
      }


      $response=array('state'=>200,'eprice'=>money($expected_price),'gprice'=>money($total_expected_price),'tprice'=>money($total_expected_price),'items'=>$items);
     echo json_encode($response);
     break;
   case('received'):
     $qty=$_REQUEST['qty'];
     $units_tipo=$_REQUEST['units_tipo'];
     $po_id=$_SESSION['tables']['po_item'][4][0];
     $p2s_id=$_REQUEST['p2s_id'];

     $sql=sprintf("select price from product2supplier  where id='%d' ",$p2s_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       $price=$row['price'];
       
     }


     $sql=sprintf("select id from porden_item  where porden_id='%d' and  p2s_id='%d' ",$po_id,$p2s_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       

       $sql=sprintf("update porden_item set qty='%s'  where id=%d",$qty,$row['id']);
       $db->exec($sql);
     }else{
       
       $sql=sprintf("insert into porden_item (porden_id,p2s_id,expected_qty,qty,units_tipo,expected_price,price) value (%d,%d,0,'%s',%d,'%s','%s')",$po_id,$p2s_id,$qty,$units_tipo,$price,$price);
       $db->exec($sql);


     }

     $total_expected_price=0;
     $items=0;
     $sql=sprintf("select sum(qty*price) as ep ,count(*) as items from porden_item where  porden_id='%d'  ",$po_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       $total_expected_price=$row['ep'];
       $items=$row['items'];
       $sql=sprintf("update porden set total='%s',goods='%s' ,items=%d  where id=%d",$row['ep'],$row['ep'],$items,$po_id);
       $db->exec($sql);
     }




       $tprice=$qty*$price;


       $response=array('state'=>200,'eprice'=>money($tprice),'gprice'=>money($total_expected_price),'tprice'=>money($total_expected_price),'items'=>$items,'v_goods'=>$total_expected_price);
     echo json_encode($response);
     break;
   case('damage'):
     $qty=$_REQUEST['qty'];
     $units_tipo=$_REQUEST['units_tipo'];
     $po_id=$_SESSION['tables']['po_item'][4][0];
     $p2s_id=$_REQUEST['p2s_id'];
     $sql=sprintf("select id,qty,expected_qty from porden_item  where porden_id='%d' and  p2s_id='%d' ",$po_id,$p2s_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       
       if($row['qty']=='')
	 $qty=$row['expected_qty'];
	       
       if($qty<=$row['qty']){
	 $sql=sprintf("update porden_item set damage='%s'  where id=%d",$qty,$row['id']);
	 $db->exec($sql);
       }else{
	 $response=array('state'=>400,'qty'=>0);echo json_encode($response);break;
       }
     }
     $response=array('state'=>200,'qty'=>$qty);
     echo json_encode($response);
     break;
   case('eprice'):
     $qty=$_REQUEST['qty'];
     $units_tipo=$_REQUEST['units_tipo'];
     $po_id=$_SESSION['tables']['po_item'][4][0];
     $p2s_id=$_REQUEST['p2s_id'];
     $sql=sprintf("select id from porden_item  where porden_id='%d' and  p2s_id='%d' ",$po_id,$p2s_id);
     $res = $db->query($sql); 
     if($row=$res->fetchRow()) {
       if($row['qty']>0)
	 $qty=$qty/$row['qty'];
       else
	 $qty=0;
       $sql=sprintf("update porden_item set price='%s'  where id=%d",$qty,$units_tipo,$expected_price,$row['id']);
       $db->exec($sql);
     }
     $response=array('state'=>200);
     echo json_encode($response);
     break;
   default:
     $response=array('state'=>404,'resp'=>_('Suboperation not found'));
     echo json_encode($response);
   }
   break;



 case('updateone_p2s'):

   $key=$_REQUEST['key'];
   switch($key){
   case('delete'):



     $sql=sprintf("delete from product2supplier where id=%d",$_REQUEST['id'] );
     $db->exec($sql);
     $response=array('state'=>200);
     echo json_encode($response);
     break;

   case('sup_code'):
     $code=addslashes($_REQUEST['value']);
	  
     $sql=sprintf("update product2supplier set sup_code='%s' where id=%d",$code,$_REQUEST['id'] );
     //     print "$sql";

      $db->exec($sql);
     $response=array('state'=>200);
     echo json_encode($response);
     break;
  case('price_unit'):

    $price=str_replace($myconf['currency_symbol'],"",$_REQUEST['value']);
    $price=str_replace($myconf['decimal_point'],"",$price);
    //    print $_REQUEST['value'];
    $price=number_format($price,3,'.','');
    $sql=sprintf("update product2supplier set price='%s' where id=%d",$price,$_REQUEST['id'] );
    //print "$sql";
    $db->exec($sql);
     $response=array('state'=>200);
     echo json_encode($response);
     break;
   default:
     $response=array('state'=>404,'resp'=>_('Suboperation not found'));
     echo json_encode($response);
   }
   break;
 case('updateone_s'):

   $key=$_REQUEST['key'];
   switch($key){
   case('delete'):


     $sql=sprintf("select  count(*) as num from product2supplier where supplier_id=%d",$_REQUEST['id'] );
     $res = $db->query($sql); 
     if ($row=$res->fetchRow()) {
       $num_products=$row['num'];
       if($num_products==0){
	 $sql=sprintf("delete from supplier where id=%d",$_REQUEST['id'] );
	 $db->exec($sql);
	 $response=array('state'=>200);
       }else{
	 $response=array('state'=>400);
       }
     }
     echo json_encode($response);
     break;

   case('code'):
     $code=addslashes($_REQUEST['value']);
	  
     $sql=sprintf("update supplier set code='%s' where id=%d",$code,$_REQUEST['id'] );
     $db->exec($sql);
     $response=array('state'=>200);
     echo json_encode($response);
     break;
  case('name'):
     $name=addslashes($_REQUEST['value']);
	  
     $sql=sprintf("update supplier set name='%s' where id=%d",$name,$_REQUEST['id'] );
     $db->exec($sql);
     $response=array('state'=>200);
     echo json_encode($response);
     break;
   default:
     $response=array('state'=>404,'resp'=>_('Suboperation not found'));
     echo json_encode($response);
   }
   break;


case('new_supplier'):
   
   if(isset($_REQUEST['name'])  and  isset($_REQUEST['code'])    and $_REQUEST['code']!='' and $_REQUEST['name']!=''){
     $name=addslashes($_REQUEST['name']);
     $code=addslashes($_REQUEST['code']);


     $sql=sprintf("insert into contact (tipo,order_name,name,date_creation,alias) values (1,'%s','%s',NOW(),'%s')",$name,$name,$code);
     $db->exec($sql);
     $contact_id = $db->lastInsertID();

     $sql=sprintf("insert into  supplier (code,name,contact_id) values ('%s','%s',%d)",$code,$name,$contact_id);
     $affected=& $db->exec($sql);

     if (PEAR::isError($affected)) {
       if(preg_match('/^MDB2 Error: constraint violation$/',$affected->getMessage())){
	 $response=array('state'=>400,'resp'=>_('Error: Another supplier has the same code'));echo json_encode($response);break;}
       else{
	 $response=array('state'=>400,'resp'=>_('Fatal Error'));echo json_encode($response);break;}
     }else{
       $supplier_id = $db->lastInsertID();
       $response=array('state'=>200,'supplier_id'=>$supplier_id);echo json_encode($response);break;
     }
   }
   $response=array('state'=>400,'resp'=>_('Fatal Error'));echo json_encode($response);break;

 default:

   
   $response=array('state'=>404,'resp'=>_('Operation not found'));
   echo json_encode($response);
   
 }


?>