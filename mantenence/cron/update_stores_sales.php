<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once '../../conf/dns.php';
include_once '../../class.Department.php';
include_once '../../class.Family.php';
include_once '../../class.Product.php';
include_once '../../class.Supplier.php';
include_once '../../class.Part.php';
include_once '../../class.Store.php';
include_once '../../class.Category.php';

error_reporting(E_ALL);

date_default_timezone_set('UTC');


$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if (!$con) {
	print "Error can not connect with database server\n";
	exit;
}
//$dns_db='dw_avant';
$db=@mysql_select_db($dns_db, $con);
if (!$db) {
	print "Error can not access the database\n";
	exit;
}


require_once '../../common_functions.php';

mysql_set_charset('utf8');
require_once '../../conf/conf.php';
setlocale(LC_MONETARY, 'en_GB.UTF-8');

global $myconf;


$start_time=date('U');

print date('r')." Start Store\n";



$contador=0;
$lap_time0=date('U');
$sql="select * from `Store Dimension` ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {


	$store=new Store($row['Store Key']);
	$store->update_up_today_sales();
	$store->update_customer_activity_interval();
	$store->update_interval_sales();
	$store->update_last_period_sales();
	$store->update_orders();
	
	$store->update_customers_data();
	
	
	unset($store);
	$contador++;
}
//$lap_time1=date('U');
print date('r')." End Store\n";







?>
