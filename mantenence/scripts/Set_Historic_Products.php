<?php
include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Family.php');
include_once('../../class.Product.php');
include_once('../../class.Supplier.php');
include_once('../../class.Part.php');
include_once('../../class.SupplierProduct.php');
date_default_timezone_set('GMT');

error_reporting(E_ALL);
$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );
if(!$con){print "Error can not connect with database server\n";exit;}
//$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           

$sql=sprintf("select `Product Code` from `Product Dimension` group by `Product Code`");
$res_code=mysql_query($sql);
while($row_c=mysql_fetch_array($res_code)){
  $code=$row_c['Product Code'];
  $sql=sprintf("select * from `Product Dimension` where `Product Store Key`=1 and `Product Code`=%s order by  `Product Valid To` desc",prepare_mysql($code));
  $res=mysql_query($sql);
  $number=mysql_num_rows($res);
  if($number>1){
    $count=0;  
    while($row=mysql_fetch_array($res)){
      $pid=$row['Product ID'];
    $to=$row['Product Valid To'];
    //print "$code $pid $to ".$row['Product Short Description']."\n";
    if($count>0){
      $sql=sprintf("update `Product Dimension` set `Product Record Type`='Historic',`Product Sales Type`='Not for Sale',`Product To Be Discontinued`='No Applicable',`Product Web State`='Offline' where `Product ID`=%d",$pid);
      //exit($sql);
      mysql_query($sql);
    }

    $count++;
  }
  }
}

 
$sql=sprintf("select id,code  from aw_old.product  where   condicion=2 and stock=0  ");
	$result2a=mysql_query($sql);
	while($row2a=mysql_fetch_array($result2a, MYSQL_ASSOC)   ){
	  $sql=sprintf("update `Product Dimension` set `Product Record Type`='Discontinued',`Product Sales Type`='Public Sale',`Product To Be Discontinued`='No Applicable',`Product Web State`='Offline' where `Product Code`=%s and `Product Record Type`!='Historic' "
		       ,prepare_mysql($row2a['code']));

      mysql_query($sql);
           	  

	}


$sql=sprintf("select id,code  from aw_old.product  where   condicion=2 and stock>0  ");
	$result2a=mysql_query($sql);
	while($row2a=mysql_fetch_array($result2a, MYSQL_ASSOC)   ){
	  $sql=sprintf("update `Product Dimension` set `Product Record Type`='Discontinuing',`Product Sales Type`='Public Sale',`Product To Be Discontinued`='Yes',`Product Web State`='Offline' where `Product Code`=%s and `Product Record Type`!='Historic' "
		       ,prepare_mysql($row2a['code']));

      mysql_query($sql);
           	  

	}


$sql="select * from `Product Dimension` where `Product Record Type`='Normal' and `Product Valid From`<'2009-01-01' ";
$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
  $product=new Product('pid',$row['Product ID']);
  if($product->data['Product 1 Year Acc Quantity Ordered']==0){
 $sql=sprintf("update `Product Dimension` set `Product Record Type`='Discontinued' ,`Product Sales Type`='Public Sale',`Product To Be Discontinued`='No Applicable',`Product Web State`='Offline' where   `Product ID`=%d "
	      ,$row['Product ID']);
 // print "$sql\n";
      mysql_query($sql);
  }
}




?>