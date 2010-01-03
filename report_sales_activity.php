<?php
include_once('common.php');
include_once('report_functions.php');
include_once('class.Store.php');

$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 //		 $yui_path.'datatable/assets/skins/sam/datatable.css',
		 'common.css',
		 'button.css',
		 'container.css',
		 'table.css'
		 );
$js_files=array(

		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'common.js.php',
		'table_common.js.php',
		'calendar_common.js.php',

		'report_sales.js.php'
		);



$smarty->assign('parent','reports');
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);



$to=date('d-m-Y');
$from=date('d-m-Y',strtotime('now -7 day'));

$compare_from=date('d-m-Y',strtotime("$from -1 year"));
$compare_to=date('d-m-Y',strtotime("$to -1 year"));


$int=prepare_mysql_dates($from,$to,'`Invoice Date`','date start end');
$compare_int=prepare_mysql_dates($compare_from,$compare_to,'`Invoice Date`','date start end');


list($store_data,$activity_data,$total)=report_data($int);
$compare_data=report_data($compare_int);

$title=_('Sales Activity Report');



foreach($store_data as $key=>$val){
  //get_color_in_gradient($factor,$HexFrom, $HexTo)
  
  $store_data[$key]['compare_invoices_color']=background_color($val['_invoices'],$compare_data[0][$key]['_invoices']);
  $store_data[$key]['compare_customers_color']=background_color($val['_customers'],$compare_data[0][$key]['_customers']);
  $store_data[$key]['compare_net_color']=background_color($val['_eq_net'],$compare_data[0][$key]['_eq_net']);

 


  $store_data[$key]['compare_invoices']=percentage($val['_invoices']-$compare_data[0][$key]['_invoices'],$compare_data[0][$key]['_invoices']);
 $store_data[$key]['compare_customers']=percentage($val['_customers']-$compare_data[0][$key]['_customers'],$compare_data[0][$key]['_customers']);
 $store_data[$key]['compare_net']=percentage($val['_eq_net']-$compare_data[0][$key]['_eq_net'],$compare_data[0][$key]['_eq_net']);

  if($val['store']!=''){
  if($total['stores']>1){
   $store_data[$key]['per_invoices']=percentage($val['_invoices'],$total['invoices']);
   $store_data[$key]['per_customers']=percentage($val['_customers'],$total['customers']);
  
   
  if($val['currency_code']!=$myconf['currency_code'])
    $store_data[$key]['per_eq_net']='<span class="mix_currency">'.percentage($val['_eq_net'],$total['net']).'</span>';
  else
    $store_data[$key]['per_eq_net']=percentage($val['_eq_net'],$total['net']);
  }
 
 }else{
 

     $store_data[$key]['sub_per_invoices']=percentage($val['_invoices'],$total['invoices']);
     $store_data[$key]['sub_per_customers']=percentage($val['_customers'],$total['customers']);

     if($val['currency_code']!=$myconf['currency_code']){
       $store_data[$key]['sub_per_eq_net']='<span class="mix_currency">'.percentage($val['_eq_net'],$total['net']).'</span>';

     }else{
       $store_data[$key]['sub_per_eq_net']=percentage($val['_eq_net'],$total['net']);
     }
  }

}

if($total['stores']>1){
if($mixed_currencies){
  $store_data[]=array(
		   'store'=>_('Total')
		   ,'invoices'=>number($total['invoices'])
		   ,'net'=>'<span class="mix_currency">'.money($total['net']).'</span>'

		   
		      );
}else{
   $store_data[]=array(
		   'store'=>_('Total')
		   ,'invoices'=>number($total['invoices'])
		   ,'net'=>money($total['net'])

		   
		      );

}
}  


$smarty->assign('store_data',$store_data);
$smarty->assign('activity_data',$activity_data);





$day_interval=get_time_interval(strtotime($from),(strtotime($to)))+1;

$smarty->assign('title',$title);

$smarty->assign('currency',$myconf['currency_symbol']);




$smarty->display('report_sales_activity.tpl');




function report_data($int){
  global $myconf;



  $link='';
  $store_data=array();
  $activity_data=array();
  $sql="select `Store Name`,`Store Key`,`Store Currency Code` from `Store Dimension`";
  $result=mysql_query($sql);
  $mixed_currencies=false;
  while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
    if($row['Store Currency Code']!=$myconf['currency_code'])
      $mixed_currencies=true;
    $store_data[$row['Store Key']]=array(
					 'store'=>sprintf('<a href="report_sales.php?store_key=%d%s">%s</a>',$row['Store Key'],$link,$row['Store Name'])
					 ,'currency_code'=>$row['Store Currency Code']
					 ,'net'=>'<b>'.money(0,$row['Store Currency Code']).'</b>'
					 ,'tax'=>'<b>'.money(0,$row['Store Currency Code']).'</b>'
					 ,'eq_tax'=>money(0)
					 ,'eq_net'=>money(0)
					 ,'_eq_tax'=>0
					 ,'_eq_net'=>0
					 ,'invoices'=>0
					 );

     $activity_data[$row['Store Key']]=array(
					 'store'=>sprintf('<a href="report_sales.php?store_key=%d%s">%s</a>',$row['Store Key'],$link,$row['Store Name'])
					 ,'received'=>0
					 ,'in_process'=>0

					 );

    
    $sql=sprintf("select  count(DISTINCT `Invoice Customer Key`) as customers, `Invoice Category`,`Store Name`,`Store Key`,`Store Currency Code`,sum(if(`Invoice Title`='Invoice',1,0)) as invoices,sum(`Invoice Total Net Amount`) as net,sum(`Invoice Total Tax Amount`) as tax ,sum(`Invoice Total Net Amount`*`Invoice Currency Exchange`) as eq_net,sum(`Invoice Total Tax Amount`*`Invoice Currency Exchange`) as eq_tax from `Invoice Dimension` I left join `Store Dimension` S on (S.`Store Key`=`Invoice Store Key`) where `Invoice Store Key`=%d %s group by `Invoice Category`"
		 ,$row['Store Key'],$int[0]);
    $result2=mysql_query($sql);
    if(mysql_num_rows($result2) >1 ){
      while($row2=mysql_fetch_array($result2, MYSQL_ASSOC)){
	$store_data[$row['Store Key'].'.'.$row2['Invoice Category']]=array(
									   'store'=>''
									   ,'substore'=>sprintf("%s",$row2['Invoice Category'])
									 ,'invoices'=>number($row2['invoices'])
									 ,'customers'=>number($row2['customers'])
									 ,'net'=>money($row2['net'],$row['Store Currency Code'])
									 ,'tax'=>money($row2['tax'],$row['Store Currency Code'])
									 ,'eq_net'=>money($row2['eq_net'])
									 ,'eq_tax'=>money($row2['eq_tax'])
									 ,'_eq_net'=>$row2['eq_net']
									 ,'_eq_tax'=>$row2['eq_tax']
									 ,'currency_code'=>$row['Store Currency Code']
									 ,'_invoices'=>$row2['invoices']
									 ,'_customers'=>$row2['customers']
									 );
      }
      
    }




     $sql=sprintf("select `Order Category`,count(*) as received , sum(IF(`Order Current Dispatch State`='In Process',1,0)) as in_process from  `Order Dimension` I  where `Order Store Key`=%d %s group by  `Order Category`"
		  ,$row['Store Key'],preg_replace('/Invoice/','Order',$int[0]));
    $result2=mysql_query($sql);
    if(mysql_num_rows($result2) >1 ){
      while($row2=mysql_fetch_array($result2, MYSQL_ASSOC)){
	$activity_data[$row['Store Key'].'.'.$row2['Order Category']]=array(
									 'store'=>''
									 ,'substore'=>sprintf("%s",$row2['Order Category'])
									 ,'received'=>number($row2['received'])
									 ,'in_process'=>number($row2['in_process'])
									 );
      }
      
    }



  }


 


$sql="select count(DISTINCT `Invoice Customer Key`) as customers, `Invoice Store Key`,sum(if(`Invoice Title`='Invoice',1,0)) as invoices,sum(`Invoice Total Net Amount`) as net,sum(`Invoice Total Tax Amount`) as tax ,sum(`Invoice Total Net Amount`*`Invoice Currency Exchange`) as eq_net,sum(`Invoice Total Tax Amount`*`Invoice Currency Exchange`) as eq_tax from `Invoice Dimension` where true ".$int[0]." group by `Invoice Store Key`";
//print $sql;
$result=mysql_query($sql);
$sum_net_eq=0;
$sum_tax_eq=0;
$sum_inv=0;
$sum_customers=0;
$mixed_currencies=false;
$number_stores=0;
while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
$number_stores++;
  $sum_net_eq+=$row['eq_net'];
  $sum_tax_eq+=$row['eq_tax'];
  $sum_inv+=$row['invoices'];
  $sum_customers+=$row['customers'];
  $store_data[$row['Invoice Store Key']]['customers']='<b>'.number($row['customers']).'</b>';
  $store_data[$row['Invoice Store Key']]['invoices']='<b>'.number($row['invoices']).'</b>';
  $store_data[$row['Invoice Store Key']]['net']='<b>'.money($row['net'],$store_data[$row['Invoice Store Key']]['currency_code']).'</b>';
  $store_data[$row['Invoice Store Key']]['tax']='<b>'.money($row['tax'],$store_data[$row['Invoice Store Key']]['currency_code']).'</b>';
  $store_data[$row['Invoice Store Key']]['eq_net']=money($row['eq_net']);
  $store_data[$row['Invoice Store Key']]['eq_tax']=money($row['eq_tax']);
  $store_data[$row['Invoice Store Key']]['_eq_net']=$row['eq_net'];
  $store_data[$row['Invoice Store Key']]['_eq_ta']=$row['eq_tax'];
  $store_data[$row['Invoice Store Key']]['_invoices']= $row['invoices'];
  $store_data[$row['Invoice Store Key']]['_customers']= $row['customers'];

 
}
mysql_free_result($result);

 $sql=sprintf("select `Order Store Key`,count(*) as received , sum(IF(`Order Current Dispatch State`='In Process',1,0)) as in_process, sum(IF(`Order Current Dispatch State`='Ready to Pick',1,0)) as ready_to_pick, sum(IF(`Order Current Dispatch State`='Picking',1,0)) as picking, sum(IF(`Order Current Dispatch State`='Ready to Pack',1,0)) as ready_to_pack , sum(IF(`Order Current Dispatch State`='Ready to Ship',1,0)) as ready_to_ship,  sum(IF(`Order Current Dispatch State`='Dispached',1,0)) as dispached, sum(IF(`Order Current Dispatch State`='Cancelled',1,0)) as cancelled  from  `Order Dimension` I  where  true %s group by  `Order Store Key`"
	      ,preg_replace('/Invoice/','Order',$int[0]));
$result=mysql_query($sql);
//print $sql;
$sum_received=0;
$sum_in_process=0;
while($row=mysql_fetch_array($result, MYSQL_ASSOC)){

 
  $sum_received+=$row['received'];
  $sum_in_process+=$row['in_process'];
  $activity_data[$row['Order Store Key']]['received']='<b>'.number($row['received']).'</b>';
  $activity_data[$row['Order Store Key']]['in_process']='<b>'.number($row['in_process']).'</b>';
  $activity_data[$row['Order Store Key']]['in_warehouse']='<b>'.number($row['ready_to_pick']+$row['picking']+$row['ready_to_pack']).'</b>';
  $activity_data[$row['Order Store Key']]['ready']='<b>'.number($row['ready_to_ship']).'</b>';
  $activity_data[$row['Order Store Key']]['dispached']='<b>'.number($row['dispached']).'</b>';
  $activity_data[$row['Order Store Key']]['cancelled']='<b>'.number($row['dispached']).'</b>';


 
}
mysql_free_result($result);


$totals=array(
	      'invoices'=>$sum_inv
	      ,'customers'=>$sum_customers
	      ,'net'=>$sum_net_eq
	      ,'stores'=>$number_stores
	      ,'received'=>$sum_received
	      ,'in_process'=>$sum_in_process
	      );
return array($store_data,$activity_data,$totals);

}

function background_color($v1,$v2){
  
  if($v2==0)
    return '#ddd';
  
  $factor=($v1-$v2)/$v2;
 
  if($factor<0){

    return '#'.get_color_in_gradient((-1*$factor),'ffffff','ff8f8f');
  }else{
    return '#'.get_color_in_gradient($factor,'ffffff','94ff8f');

  }

}



?>

