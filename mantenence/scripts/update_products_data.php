<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once '../../conf/dns.php';
include_once '../../class.Department.php';
include_once '../../class.Family.php';
include_once '../../class.Product.php';
include_once '../../class.Supplier.php';
include_once '../../class.Part.php';
include_once '../../class.PartLocation.php';

include_once '../../class.SupplierProduct.php';
error_reporting(E_ALL);

date_default_timezone_set('UTC');
include_once '../../set_locales.php';
require '../../locale.php';
$_SESSION['locale_info'] = localeconv();

$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if (!$con) {print "Error can not connect with database server\n";exit;}

$db=@mysql_select_db($dns_db, $con);
if (!$db) {print "Error can not access the database\n";exit;}


require_once '../../common_functions.php';

mysql_set_charset('utf8');
require_once '../../conf/conf.php';
date_default_timezone_set('UTC');


//$sql="select `Product ID` from `Product Dimension` where `Product ID`=1663 order by  `Product ID` ";
$sql="select `Product ID` from `Product Dimension` order by  `Product ID` ";

$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
$product=new Product('pid',$row['Product ID']);
//$product->update_parts();
//$product->update_availability();
//$product->update_sales_averages();

$sql=sprintf("update `Product Dimension` set `Product Short Description`=%s,`Product XHTML Short Description`=%s where `Product ID`=%d "
,prepare_mysql($product->get('Short Description'))
			,prepare_mysql($product->get('XHTML Short Description'))
			,$product->pid
		);
		//print "$sql\n";
		mysql_query($sql);
		
//$product->update_part_ratio();
//$product->update_weight_from_parts();
//$product->update_cost();

print $product->pid."\r";
}


?>
