<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Family.php');
include_once('../../class.Product.php');
include_once('../../class.Supplier.php');
include_once('../../class.Page.php');
include_once('../../class.Store.php');
error_reporting(E_ALL);

date_default_timezone_set('Europe/London');


$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if(!$con){print "Error can not connect with database server\n";exit;}
//$dns_db='dw_avant2';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
  

require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           

global $myconf;


$store_code='UK';
$store_key=1;

include_once('../../sites/UK/create_main_pages.php');
include_once('../../sites/DE/create_main_pages.php');
include_once('../../sites/FR/create_main_pages.php');
include_once('../../sites/PL/create_main_pages.php');




//$sql=sprintf("select P.`Page Key` from `Page Dimension` P  left join `Page Store Dimension` PS on (P.`Page Key`=PS.`Page Key`)  where `Page Type`='Store'  and `Page Store Function`='Information' ");
$sql=sprintf("select P.`Page Key` from `Page Dimension` P  left join `Page Store Dimension` PS on (P.`Page Key`=PS.`Page Key`)  where `Page Type`='Store'  ");
$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
  $sql=sprintf("delete from `Page Dimension` where `Page Key`=%d",$row['Page Key']);
  // print "$sql\n";
  mysql_query($sql);
  $sql=sprintf("delete from `Page Store Dimension` where `Page Key`=%d",$row['Page Key']);
  mysql_query($sql);
  //print "$sql\n";
  
}


foreach($page_data as $store_code=>$data){
  $store=new Store('code',$store_code);

foreach($data as $page_data){
  $page_data['Page Store Order Template']='No Applicable';
  $page_data['Page Store Function']='Information';
  $page_data['Page Store Creation Date']=date('Y-m-d H:i:s');
  $page_data['Page Store Last Update Date']=date('Y-m-d H:i:s');
  $page_data['Page Store Last Structural Change Date']=date('Y-m-d H:i:s');
  $page_data['Page Type']='Store';
  $page_data['Page Store Source Type'] ='Static';
  $page_data['Page Store Code']=$store_code;
  $page_data['Page Parent Key']=$store->id;
 
  $page=new Page('find',$page_data,'create');
  //  print_r($page_data);
  // print_r($page);
}
}


//print_r($store_data);
foreach($store_data as $store_code=>$xdata){
  $store=new Store('code',$store_code);

  $data=array();
  $data['Page Store Slogan']=$xdata['Slogan'];
  $data['Page Store Resume']=$xdata['Resume'];
  $data['Showcases Layout']='Splited';
  $data['Page Store Function']='Store Catalogue';

 
  $store->create_page($data);
}


$sql=sprintf("select * from `Product Department Dimension` left join  `Store Dimension` on (`Product Department Store Key`=`Store Key`)  where `Product Department Sales Type`='Public Sale'  ");

$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
$department=new Department($row['Product Department Key']);
  $data=array();
  $data['Page Store Slogan']=(isset($department_data[$row['Store Code'].'_'.$row['Product Department Code']]['Slogan'])?$department_data[$row['Store Code'].'_'.$row['Product Department Code']]['Slogan']:'');
  $data['Page Store Resume']=(isset($department_data[$row['Store Code'].'_'.$row['Product Department Code']]['Resume'])?$department_data[$row['Store Code'].'_'.$row['Product Department Code']]['Resume']:'');
$data['Page Store Function']='Department Catalogue';
  $data['Showcases Layout']='Splited';
  $department->create_page($data);
}


$sql=sprintf("select * from `Product Family Dimension` left join  `Store Dimension` on (`Product Family Store Key`=`Store Key`)  where `Product Family Sales Type`='Public Sale'  ");

$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
$family=new Family($row['Product Family Key']);
  $data=array();
  $data['Page Store Slogan']=(isset($family_data[$row['Store Code'].'_'.$row['Product Family Code']]['Slogan'])?$family_data[$row['Store Code'].'_'.$row['Product Family Code']]['Slogan']:'');
  $data['Page Store Resume']=(isset($family_data[$row['Store Code'].'_'.$row['Product Family Code']]['Resume'])?$family_data[$row['Store Code'].'_'.$row['Product Family Code']]['Resume']:'');
$data['Page Store Function']='Family Catalogue';
  $data['Showcases Layout']='Splited';
  $family->create_page($data);
}

?>