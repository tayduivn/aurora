<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2012 Inikoo Ltd
include_once '../../app_files/db/dns.php';
include_once '../../class.Department.php';
include_once '../../class.Family.php';
include_once '../../class.Product.php';
include_once '../../class.Supplier.php';
include_once '../../class.Part.php';
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
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';
date_default_timezone_set('UTC');


//$sql="select * from `Product Dimension` where `Product Code`='FO-A1'";
$sql="select * from `Order Dimension`  ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
	$order=new Order($row['Order Key']);
	$delivery_notes=$order->get_delivery_notes_objects();
	foreach ($delivery_notes as $delivery_note) {

	

			if (!in_array($delivery_note->data['Delivery Note Type'],array('Replacement & Shortages','Replacement','Shortages')) and $delivery_note->data['Delivery Note Date']!='') {



				$sql=sprintf("update `Order Dimension` set `Order Send to Warehouse Date`=%s where `Order Key`=%d   ",
				prepare_mysql($delivery_note->data['Delivery Note Date']),
				$order->id
				);
				mysql_query($sql);
				//print "$sql\n";

			}

		

	}




}


?>
