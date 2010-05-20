<?php
/*
 File: customer_csv.php 

 Customer CSV data for export proprces

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2010, Kaktus 
 
 Version 2.0
*/

include_once('common.php');
if(!$user->can_view('customers')){
  exit();
}

 


header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"customers_export_".date("ymd_Hi").".csv\"");
$out = fopen('php://output', 'w');


 $conf=$_SESSION['state']['customers']['table'];
 $where=$conf['where'];
 $f_field=$conf['f_field'];
$f_value=$conf['f_value'];
     $store=$_SESSION['state']['customers']['store'];

if(is_numeric($store)){
     $where.=sprintf(' and `Customer Store Key`=%d ',$store);
   }


 $wheref='';
  
  if(($f_field=='customer name'     )  and $f_value!=''){
    $wheref="  and  `Customer Name` like '%".addslashes($f_value)."%'";
  }elseif(($f_field=='postcode'     )  and $f_value!=''){
    $wheref="  and  `Customer Main Postal Code` like '%".addslashes($f_value)."%'";
  }else if($f_field=='id'  )
     $wheref.=" and  `Customer Key` like '".addslashes(preg_replace('/\s*|\,|\./','',$f_value))."%' ";
  else if($f_field=='last_more' and is_numeric($f_value) )
    $wheref.=" and  (TO_DAYS(NOW())-TO_DAYS(`Customer Last Order Date`))>=".$f_value."    ";
  else if($f_field=='last_less' and is_numeric($f_value) )
    $wheref.=" and  (TO_DAYS(NOW())-TO_DAYS(`Customer Last Order Date`))<=".$f_value."    ";
  else if($f_field=='max' and is_numeric($f_value) )
    $wheref.=" and  `Customer Orders`<=".$f_value."    ";
  else if($f_field=='min' and is_numeric($f_value) )
    $wheref.=" and  `Customer Orders`>=".$f_value."    ";
  else if($f_field=='maxvalue' and is_numeric($f_value) )
    $wheref.=" and  `Customer Net Balance`<=".$f_value."    ";
  else if($f_field=='minvalue' and is_numeric($f_value) )
    $wheref.=" and  `Customer Net Balance`>=".$f_value."    ";

$sql="select   *,`Customer Net Refunds`+`Customer Tax Refunds` as `Customer Total Refunds` from  `Customer Dimension` $where $wheref";
  
  $adata=array(
	     'id'=>_('ID')
	     ,'type'=>_('Type')
	     ,'name'=>_('Name')
	     ,'contact_name'=>_('Conatact')
	     ,'email'=>_('Email')
	     ,'orders'=>_('Orders')
	     ,'last_order'=>_('Last Order')
	       
	       
	       );
 fputcsv($out, $adata);

  
  $result=mysql_query($sql);
  while($data=mysql_fetch_array($result, MYSQL_ASSOC)){


  
$type=$data['Customer Type'];
  if($data['Customer Orders']==0)
      $last_order_date='';
    else
      $last_order_date=strftime("%d-%m-%Y", strtotime($data['Customer Last Order Date']));
  $adata=array(
	       'id'=>$myconf['customer_id_prefix'].sprintf("%05d",$data['Customer Key'])
	       ,'type'=>$type
	       ,'name'=>$data['Customer Name']
	       ,'contact_name'=>$data['Customer Main Contact Name']
	       ,'email'=>$data['Customer Main Plain Email']
	       ,'orders'=>number($data['Customer Orders'])
	       ,'last_order'=>$last_order_date
	       
	       
	       );
  fputcsv($out, $adata);
  }
mysql_free_result($result);









fclose($out);






?>