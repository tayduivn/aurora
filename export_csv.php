<?php
/*
 File: customer_csv.php 

 Customer CSV data for export

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2010, Kaktus 
 
 Version 2.0
*/

include_once('common.php');
include_once('ar_common.php');

if(!isset($_REQUEST['tipo']))
exit("unknown operation");

$tipo=$_REQUEST['tipo'];

list($filename,$adata)=get_data($tipo);

if(!$filename){
exit("unknown operation 2");
}

//print_r($adata);
//exit;

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"".$filename."\"");
$out = fopen('php://output', 'w');
foreach ($adata as $data) {
    fputcsv($out, $data);
}

fclose($out);

function get_data($tipo){
$filename='';
$data=array();


switch ($tipo) {
    case 'customers':
        $filename=_('customers').'.csv';

        $f_field=$_SESSION['state']['customers']['table']['f_field'];
        $f_value=$_SESSION['state']['customers']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('customers').'.csv';
	$where=sprintf(' `Customer Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_customerslist_data($wheref,$where);
        break;
    case 'stores':
        $filename=_('stores').'.csv';
        $f_field=$_SESSION['state']['stores']['table']['f_field'];
        $f_value=$_SESSION['state']['stores']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('stores').'.csv';
        $data=get_stores_data($wheref);break;
    case 'families':
        $filename=_('families').'.csv';
        $f_field=$_SESSION['state']['families']['table']['f_field'];
        $f_value=$_SESSION['state']['families']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('families').'.csv';
        $data=get_families_data($wheref);
        break;
  case 'families_in_department':
    
        $f_field=$_SESSION['state']['family']['table']['f_field'];
        $f_value=$_SESSION['state']['family']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('families').'.csv';
        $where=sprintf(' `Product Main Department Key`=%d ',$_SESSION['state']['department']['id']);
        $data=get_families_data($wheref,$where);
        break;       
    case 'products':
        $filename=_('products').'.csv';
        $f_field=$_SESSION['state']['products']['table']['f_field'];
        $f_value=$_SESSION['state']['products']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('products').'.csv';
        $data=get_products_data($wheref);
        break;
    case 'departments':
        $filename=_('departments').'.csv';
        $f_field=$_SESSION['state']['departments']['table']['f_field'];
        $f_value=$_SESSION['state']['departments']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('departments').'.csv';
        $where=sprintf(' `Product Department Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_departments_data($wheref,$where);
        break;
   case 'products_in_family':
    
        $f_field=$_SESSION['state']['family']['table']['f_field'];
        $f_value=$_SESSION['state']['family']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('products').'.csv';
        $where=sprintf(' `Product Family Key`=%d ',$_SESSION['state']['family']['id']);
        $data=get_products_data($wheref,$where);
        break;
   case 'company_departments':
        $filename=_('company_departments').'.csv';
        $f_field=$_SESSION['state']['company_departments']['table']['f_field'];
        $f_value=$_SESSION['state']['company_departments']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('company_departments').'.csv';
        $data=get_company_departments_data($wheref);
        break;    
   case 'orders_per_store':
        $filename=_('orders_per_store').'.csv';
        $f_field=$_SESSION['state']['stores']['orders']['f_field'];
        $f_value=$_SESSION['state']['stores']['orders']['f_value'];

        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('orders_per_store').'.csv';
	 $where=sprintf(' `Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_orders_data($wheref);
        break; 
   case 'invoices_per_store':
        $filename=_('invoices_per_store').'.csv';
        $f_field=$_SESSION['state']['stores']['invoices']['f_field'];
        $f_value=$_SESSION['state']['stores']['invoices']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('invoices_per_store').'.csv';
	 $where=sprintf(' `Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_invoices_data($wheref);
        break; 
   case 'delivery_notes_per_store':
        $filename=_('delivery_notes_per_store').'.csv';
        $f_field=$_SESSION['state']['stores']['delivery_notes']['f_field'];
        $f_value=$_SESSION['state']['stores']['delivery_notes']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('delivery_notes_per_store').'.csv';
	 $where=sprintf(' `Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_delivery_notes_data($wheref);
        break;
   case 'orders':
        $filename=_('orders').'.csv';
        $f_field=$_SESSION['state']['orders']['table']['f_field'];
        $f_value=$_SESSION['state']['orders']['table']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('orders').'.csv';
        $where=sprintf(' `Order Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_orders_in_order_data($wheref,$where);
        break;  
   case 'invoices':
        $filename=_('invoices').'.csv';
        $f_field=$_SESSION['state']['orders']['invoices']['f_field'];
        $f_value=$_SESSION['state']['orders']['invoices']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('invoices').'.csv';
        $where=sprintf(' `Invoice Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_orders_invoices_data($wheref,$where);
        break;  
   case 'dn':
        $filename=_('delivery_notes').'.csv';
        $f_field=$_SESSION['state']['orders']['dn']['f_field'];
        $f_value=$_SESSION['state']['orders']['dn']['f_value'];
        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('delivery_notes').'.csv';
        $where=sprintf(' `Delivery Note Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_orders_delivery_notes_data($wheref,$where);
        break;  
   case 'customers_per_store':
        $filename=_('customers_per_store').'.csv';
        $f_field=$_SESSION['state']['stores']['customers']['f_field'];
        $f_value=$_SESSION['state']['stores']['customers']['f_value'];

        $wheref=wheref_stores($f_field,$f_value);
        $filename=_('customers_per_store').'.csv';
	 $where=sprintf(' `Store Key`=%d ',$_SESSION['state']['store']['id']);
        $data=get_customers_data($wheref);
        break; 
                     
    default:
        
        break;
}
return array($filename,$data);
}

function get_stores_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['stores']['table']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Store Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Store Name'),
'departments'=>array('title'=>_('Departments'),'db_name'=>'Store Departments'),
'families'=>array('title'=>_('Departments'),'db_name'=>'Store Families'),
'products'=>array('title'=>_('Products'),'db_name'=>'Store For Public Sale Products'),
'discontinued'=>array('title'=>_('Discontinued'),'db_name'=>'Store Discontinued Products'),
'new'=>array('title'=>_('New'),'db_name'=>'Store New Products'),
'surplus'=>array('title'=>_('Surplus'),'db_name'=>'Store Surplus Availability Products'),
'ok'=>array('title'=>_('Ok'),'db_name'=>'Store Optimal Availability Products'),
'gone'=>array('title'=>_('Gone'),'db_name'=>'Store Out Of Stock Products'),
'low'=>array('title'=>_('Gone'),'db_name'=>'Store Low Availability Products'),
'critical'=>array('title'=>_('Gone'),'db_name'=>'Store Critical Availability Products'),
'unknown'=>array('title'=>_('Unknown'),'db_name'=>'Store Unknown Stock Products'),
'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Store Total Invoiced Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Store Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Store 1 Year Acc Invoiced Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Store 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Store 1 Quarter Acc Invoiced Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Store 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Store 1 Month Acc Invoiced Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Store 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Store 1 Week Acc Invoiced Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Store 1 Week Acc Profit'),
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Store Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}

function get_families_data($wheref){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['families']['table']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Product Family Code'),
'stores'=>array('title'=>_('Stores'),'db_name'=>'Product Family Store Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Product Family Name'),
'products'=>array('title'=>_('Products'),'db_name'=>'Product Family For Public Sale Products'),

'surplus'=>array('title'=>_('Surplus'),'db_name'=>'Product Family Surplus Availability Products'),
'ok'=>array('title'=>_('Ok'),'db_name'=>'Product Family Optimal Availability Products'),
'low'=>array('title'=>_('Gone'),'db_name'=>'Product Family Low Availability Products'),
'critical'=>array('title'=>_('Gone'),'db_name'=>'Product Family Critical Availability Products'),
'gone'=>array('title'=>_('Unknown'),'db_name'=>'Product Family Out Of Stock Products'),
'unknown'=>array('title'=>_('Unknown'),'db_name'=>'Product Family Unknown Stock Products'),

'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Product Family Total Acc Invoiced Gross Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Product Family Total Acc Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Product Family 1 Year Acc Invoiced Gross Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Product Family 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Product Family 1 Quarter Acc Invoiced Gross Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Product Family 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Product Family 1 Month Acc Invoiced Gross Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Product Family 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Product Family 1 Week Acc Invoiced Gross Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Product Family 1 Week Acc Profit'),
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Product Family Dimension` where true $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}



function get_products_data($wheref,$where=' true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['products']['table']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Product Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Product Short Description'),
'status'=>array('title'=>_('Status'),'db_name'=>'Product Sales Type'),
'web'=>array('title'=>_('Web'),'db_name'=>'Product Web State'),


'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Product Total Invoiced Gross Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Product Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Product 1 Year Acc Invoiced Gross Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Product 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Product 1 Quarter Acc Invoiced Gross Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Product 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Product 1 Month Acc Invoiced Gross Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Product 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Product 1 Week Acc Invoiced Gross Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Product 1 Week Acc Profit'),


);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Product Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}

function get_departments_data($wheref,$where){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['departments']['table']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Product Department Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Product Department Name'),
'families'=>array('title'=>_('Families'),'db_name'=>'Product Department Families'),
'products'=>array('title'=>_('Products'),'db_name'=>'Product Department For Public Sale Products'),
'discontinued'=>array('title'=>_('Discontinued'),'db_name'=>'Product Department Discontinued Products'),


'surplus'=>array('title'=>_('Surplus'),'db_name'=>'Product Department Surplus Availability Products'),
'ok'=>array('title'=>_('Ok'),'db_name'=>'Product Department Optimal Availability Products'),
'gone'=>array('title'=>_('Gone'),'db_name'=>'Product Department Out Of Stock Products'),
'low'=>array('title'=>_('Gone'),'db_name'=>'Product Department Low Availability Products'),
'critical'=>array('title'=>_('Gone'),'db_name'=>'Product Department Critical Availability Products'),
'unknown'=>array('title'=>_('Unknown'),'db_name'=>'Product Department Unknown Sales State Products'),
'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Product Department Total Invoiced Gross Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Product Department Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Product Department 1 Year Acc Invoiced Gross Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Product Department 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Product Department 1 Quarter Acc Invoiced Gross Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Product Department 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Product Department 1 Month Acc Invoiced Gross Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Product Department 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Product Department 1 Week Acc Invoiced Gross Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Product Department 1 Week Acc Profit'),
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Product Department Dimension` where $where $wheref";

$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}






function get_company_departments_data($wheref){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['company_departments']['table']['csv_export'];
}


$fields=array(
'area'=>array('title'=>_('Area'),'db_name'=>'Company Area Code'),
'code'=>array('title'=>_('Code'),'db_name'=>'Company Department Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Company Department Name'),

'department_description'=>array('title'=>_('Departments Description'),'db_name'=>'Company Department Description'),
'no_of_department_employee'=>array('title'=>_('No. Of Department Employee'),'db_name'=>'Company Department Number Employees'),
'company_area_name'=>array('title'=>_('Company Area Name'),'db_name'=>'Company Area Name'),
'company_area_description'=>array('title'=>_('Company Area Description'),'db_name'=>'Company Area Description'),

);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;

 
$sql="select * from `Company Department Dimension` CDS left join `Company Area Dimension` CAS on CDS.`Company Key`=CAS.`Company Key` where true $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}


function get_orders_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['stores']['orders']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Store Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Store Name'),
'orders'=>array('title'=>_('Orders'),'db_name'=>'Store Total Orders'),
'cancelled'=>array('title'=>_('Cancelled'),'db_name'=>'Store Cancelled Orders'),
'suspended'=>array('title'=>_('Suspended'),'db_name'=>'Store Suspended Orders'),
'pending'=>array('title'=>_('Pending'),'db_name'=>'Store Orders In Process'),
'dispatched'=>array('title'=>_('Dispatched'),'db_name'=>'Store Dispatched Orders'),

'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Store Total Invoiced Gross Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Store Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Store 1 Year Acc Invoiced Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Store 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Store 1 Quarter Acc Invoiced Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Store 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Store 1 Month Acc Invoiced Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Store 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Store 1 Week Acc Invoiced Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Store 1 Week Acc Profit'),
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Store Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}

function get_invoices_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['stores']['invoices']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Store Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Store Name'),
'invoices'=>array('title'=>_('Invoices'),'db_name'=>'Store Invoices'),
'invpaid'=>array('title'=>_('Inv Paid'),'db_name'=>'Store Paid Invoices'),
'invtopay'=>array('title'=>_('Inv To Pay'),'db_name'=>'Store Partially Paid Invoices'),
'refunds'=>array('title'=>_('Refunds'),'db_name'=>'Store Refunds '),
'refpaid'=>array('title'=>_('Ref Paid'),'db_name'=>'Store Paid Refunds'),
'reftopay'=>array('title'=>_('Ref To Pay'),'db_name'=>'Store Partially Paid Refunds'),

'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Store Total Invoiced Gross Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Store Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Store 1 Year Acc Invoiced Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Store 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Store 1 Quarter Acc Invoiced Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Store 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Store 1 Month Acc Invoiced Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Store 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Store 1 Week Acc Invoiced Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Store 1 Week Acc Profit'),
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Store Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}
function get_delivery_notes_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['stores']['delivery_notes']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Store Code'),
'name'=>array('title'=>_('Name'),'db_name'=>'Store Name'),
'total'=>array('title'=>_('Total'),'db_name'=>'Store Total Delivery Notes'),
'topick'=>array('title'=>_('To Pick'),'db_name'=>'Store Ready to Pick Delivery Notes'),
'picking'=>array('title'=>_('Picking'),'db_name'=>'Store Picking Delivery Notes'),
'packing'=>array('title'=>_('Packing'),'db_name'=>'Store Packing Delivery Notes'),
'ready'=>array('title'=>_('Ready'),'db_name'=>'Store Ready to Dispatch Delivery Notes'),
'send'=>array('title'=>_('Send'),'db_name'=>'Store Dispatched Delivery Notes'),
'returned'=>array('title'=>_('Returned'),'db_name'=>'Store Returned Delivery Notes'),

'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Store Total Invoiced Gross Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Store Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Store 1 Year Acc Invoiced Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Store 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Store 1 Quarter Acc Invoiced Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Store 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Store 1 Month Acc Invoiced Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Store 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Store 1 Week Acc Invoiced Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Store 1 Week Acc Profit'),
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Store Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
//print_r($row);
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}


function get_orders_in_order_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['orders']['table']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Order Id'),'db_name'=>'Order Public ID'),
'last_date'=>array('title'=>_('Last Updated'),'db_name'=>'Order Last Updated Date'),
'customer'=>array('title'=>_('Customer'),'db_name'=>'Order Customer Name'),
'status'=>array('title'=>_('Status'),'db_name'=>'Order Current Dispatch State'),


'totaltax'=>array('title'=>_('Total Tax'),'db_name'=>'Order Total Tax Amount'),
'totalnet'=>array('title'=>_('Total Net'),'db_name'=>'Order Total Net Amount'),
'total'=>array('title'=>_('Total'),'db_name'=>'Order Total Net Amount'),

'balancenet'=>array('title'=>_('Balance Net'),'db_name'=>'Order Balance Net Amount'),
'balancetax'=>array('title'=>_('Balance Tax'),'db_name'=>'Order Balance Tax Amount'),
'balancetotal'=>array('title'=>_('Balance Total'),'db_name'=>'Order Balance Total Amount'),

'outstandingbalancenet'=>array('title'=>_('Outstanding Balance Net'),'db_name'=>'Order Outstanding Balance Net Amount'),
'outstandingbalancetax'=>array('title'=>_('Outstanding Balance Tax'),'db_name'=>'Order Outstanding Balance Tax Amount'),
'outstandingbalancetotal'=>array('title'=>_('Outstanding Balance Total'),'db_name'=>'Order Outstanding Balance Total Amount'),

'contactname'=>array('title'=>_('Customer Contact Name'),'db_name'=>'Order Customer Contact Name'),
'sourcetype'=>array('title'=>_('Source Type'),'db_name'=>'Order Main Source Type'),
'paymentstate'=>array('title'=>_('Payment State'),'db_name'=>'Order Current Payment State'),
'actiontaken'=>array('title'=>_('Actions Taken'),'db_name'=>'Order Actions Taken'),
'ordertype'=>array('title'=>_('Type'),'db_name'=>'Order Type'),
'shippingmethod'=>array('title'=>_('Shipping Method'),'db_name'=>'Order Shipping Method'),

);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Order Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}
function get_orders_invoices_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['orders']['invoices']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Invoice Public ID'),
'date'=>array('title'=>_('date'),'db_name'=>'Invoice Date'),
'name'=>array('title'=>_('Customer'),'db_name'=>'Invoice Customer Name'),

'paymentmethod'=>array('title'=>_('Payment Method'),'db_name'=>'Invoice Main Payment Method'),
'invoicefor'=>array('title'=>_('Invoice For'),'db_name'=>'Invoice For'),
'invoicepaid'=>array('title'=>_('Invoice Paid'),'db_name'=>'Invoice Paid'),

'invoice_total_amount'=>array('title'=>_('Invoice Total Amount'),'db_name'=>'Invoice Total Amount'),
'invoice_total_profit'=>array('title'=>_('Invoice Total Profit'),'db_name'=>'Invoice Total Profit'),
'invoice_total_tax_amount'=>array('title'=>_('Invoice Total Tax Amount'),'db_name'=>'Invoice Total Tax Amount'),
'invoice_total_tax_adjust_amount'=>array('title'=>_('Invoice Total Tax Adjust Amount'),'db_name'=>'Invoice Total Tax Adjust Amount'),
'invoice_total_adjust_amount'=>array('title'=>_('Invoice Total Adjust Amount'),'db_name'=>'Invoice Total Adjust Amount')
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Invoice Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}
function get_orders_delivery_notes_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['orders']['dn']['csv_export'];
}


$fields=array(
'id'=>array('title'=>_('Delivery Note ID'),'db_name'=>'Delivery Note ID'),
'date'=>array('title'=>_('Date'),'db_name'=>'Delivery Note Date'),
'type'=>array('title'=>_('Type'),'db_name'=>'Delivery Note Type'),
'customer_name'=>array('title'=>_('Customer'),'db_name'=>'Delivery Note Customer Name'),
'weight'=>array('title'=>_('Weight(in kilograms)'),'db_name'=>'Delivery Note Weight'),
'parcels_no'=>array('title'=>_('Parcels'),'db_name'=>'Delivery Note Number Parcels'),

'start_picking_date'=>array('title'=>_('Start Picking Date'),'db_name'=>'Delivery Note Date Start Picking'),
'finish_picking_date'=>array('title'=>_('Finish Picking Date'),'db_name'=>'Delivery Note Date Finish Picking'),

'start_packing_date'=>array('title'=>_('Start Packing Date'),'db_name'=>'Delivery Note Date Start Packing'),
'finish_packing_date'=>array('title'=>_('Finish Packing Date'),'db_name'=>'Delivery Note Date Finish Packing'),

'state'=>array('title'=>_('State'),'db_name'=>'Delivery Note State'),
'dispatched_method'=>array('title'=>('Dispatch Method'),'db_name'=>'Delivery Note Dispatch Method'),
'parcel_type'=>array('title'=>_('Parcel Type'),'db_name'=>'Delivery Note Parcel Type'),
'boxes_no'=>array('title'=>_('Number Of Boxes'),'db_name'=>'Delivery Note Number Boxes')
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Delivery Note Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}
function get_customers_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['stores']['customers']['csv_export'];
}


$fields=array(
'code'=>array('title'=>_('Code'),'db_name'=>'Store Code'),
'name'=>array('title'=>_('Store Name'),'db_name'=>'Store Name'),
'total_customer_contacts'=>array('title'=>_('Total Customer Contacts'),'db_name'=>'Store Total Customer Contacts'),
'new_customer_contacts'=>array('title'=>_('New Customer Contacts'),'db_name'=>'Store New Customer Contacts'),
'total_customer'=>array('title'=>_('Store Total Customers'),'db_name'=>'Store Total Customers'),
'active_customer'=>array('title'=>_('Active Customers'),'db_name'=>'Store Active Customers'),
'new_customer'=>array('title'=>_('New Customers'),'db_name'=>'Store New Customers'),
'lost_customer'=>array('title'=>_('Lost Customers'),'db_name'=>'Store Lost Customers'),

'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Store Total Invoiced Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Store Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Store 1 Year Acc Invoiced Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Store 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Store 1 Quarter Acc Invoiced Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Store 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Store 1 Month Acc Invoiced Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Store 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Store 1 Week Acc Invoiced Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Store 1 Week Acc Profit')
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Store Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}
function get_customerslist_data($wheref,$where='true'){

$data=prepare_values($_REQUEST,array('fields'=>array('type'=>'json array','optional'=>true)));
if(isset($data['fields'])){
$fields_to_export=$data['fields'];
}else{
$fields_to_export=$_SESSION['state']['customers']['table']['csv_export'];
}


$fields=array(
'id'=>array('title'=>_('Customer Id'),'db_name'=>'Customer Key'),
'name'=>array('title'=>_('Customer Name'),'db_name'=>'Customer Name'),
'location'=>array('title'=>_('Location'),'db_name'=>'Customer Main Delivery Address Town'),
'last_orders'=>array('title'=>_('Last Order'),'db_name'=>'Customer Last Order Date'),
'orders'=>array('title'=>_('Orders'),'db_name'=>'Customer Orders'),
'status'=>array('title'=>_('Status'),'db_name'=>'Customer Type by Activity ')
/*'new_customer'=>array('title'=>_('New Customers'),'db_name'=>'Store New Customers'),
'lost_customer'=>array('title'=>_('Lost Customers'),'db_name'=>'Store Lost Customers'),

'sales_all'=>array('title'=>_('Total Sales'),'db_name'=>'Store Total Invoiced Amount'),
'profit_all'=>array('title'=>_('Total Profit'),'db_name'=>'Store Total Profit'),
'sales_1y'=>array('title'=>_('Sales 1Y'),'db_name'=>'Store 1 Year Acc Invoiced Amount'),
'profit_1y'=>array('title'=>_('Profit 1Y'),'db_name'=>'Store 1 Year Acc Profit'),
'sales_1q'=>array('title'=>_('Sales 1Q'),'db_name'=>'Store 1 Quarter Acc Invoiced Amount'),
'profit_1q'=>array('title'=>_('Profit 1Q'),'db_name'=>'Store 1 Quarter Acc Profit'),
'sales_1m'=>array('title'=>_('Sales 1M'),'db_name'=>'Store 1 Month Acc Invoiced Amount'),
'profit_1m'=>array('title'=>_('Profit 1M'),'db_name'=>'Store 1 Month Acc Profit'),
'sales_1w'=>array('title'=>_('Sales 1W'),'db_name'=>'Store 1 Week Acc Invoiced Amount'),
'profit_1w'=>array('title'=>_('Profit 1W'),'db_name'=>'Store 1 Week Acc Profit')*/
);


foreach($fields as $key=>$value){
if(!isset($fields_to_export[$key]) or  !$fields_to_export[$key]  )
unset($fields[$key]);
}



$data=array();
$_data=array();
foreach($fields as $key=>$options){
$_data[]=$options['title'];
}
$data[]=$_data;
$sql="select * from `Customer Dimension` where $where $wheref";
$res=mysql_query($sql);

while($row=mysql_fetch_assoc($res)){
$_data=array();
foreach($fields as $key=>$options){

$_data[]=$row[$options['db_name']];
}
$data[]=$_data;
}
//print_r($data);exit;

return $data;

}


?>
