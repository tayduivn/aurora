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


print "Start ".date("r")."\n";



$sql="select count(*) as total from `Product Family Dimension`  where  `Product Family Stealth`='No' ";
$result=mysql_query($sql);
if ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
	$total=$row['total'];
}
$contador=0;
$lap_time0=date('U');
$sql="select F.`Product Family Key` from `Product Family Dimension`   F  left join `Product Family Data Dimension` D on (F.`Product Family Key`=D.`Product Family Key`) where  `Product Family Stealth`='No'   order by `Product Family 1 Year Acc Customers`  desc  ";

$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

	$family=new Family($row['Product Family Key']);
	$family->update_sales_correlations('Same Department',40);
	foreach ($family->get_pages_keys() as $page_key) {
		$page=new Page($page_key);
		$page->update_see_also();
	}
	$contador++;

	$lap_time1=date('U');

	print 'Sales Corr Time  '.$family->data['Product Family Code'].' '.percentage($contador,$total,3)."  time  ".sprintf("%.2f",($lap_time1-$lap_time0))." lap  ".sprintf("%.2f",($lap_time1-$lap_time0)/$contador)." EST  ".sprintf("%.1f", (($lap_time1-$lap_time0)/$contador)*($total-$contador)/3600)  ."h \r";
	unset($family);
}
print "End ".date("r")."\n";


$contador=0;
$lap_time0=date('U');
$sql="select F.`Product Family Key` from `Product Family Dimension`   F  left join `Product Family Data Dimension` D on (F.`Product Family Key`=D.`Product Family Key`) where  `Product Family Stealth`='No'   order by `Product Family 1 Year Acc Customers`  desc ";

$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

	$family=new Family($row['Product Family Key']);
	$family->update_sales_correlations('All',1000);
	foreach ($family->get_pages_keys() as $page_key) {
		$page=new Page($page_key);
		$page->update_see_also();
	}
	$contador++;

	$lap_time1=date('U');

	print 'Sales Corr Time  '.$family->data['Product Family Code'].' '.percentage($contador,$total,3)."  time  ".sprintf("%.2f",($lap_time1-$lap_time0))." lap  ".sprintf("%.2f",($lap_time1-$lap_time0)/$contador)." EST  ".sprintf("%.1f", (($lap_time1-$lap_time0)/$contador)*($total-$contador)/3600)  ."h \r";
	unset($family);
}
print "End ".date("r")."\n";


$sql="select count(*) as total from `Product Family Dimension`  ";
$result=mysql_query($sql);
if ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
	$total=$row['total'];
}
mysql_free_result($result);
$contador=0;
$lap_time0=date('U');
$sql="select `Product Family Key` from `Product Family Dimension` ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

	$family=new Family($row['Product Family Key']);
	$family->update_similar_families();
	unset($family);
	$contador++;

	$lap_time1=date('U');
	//  print 'Sim Time '.percentage($contador,$total,3)."  time  ".sprintf("%.2f",($lap_time1-$lap_time0))." lap  ".sprintf("%.2f",($lap_time1-$lap_time0)/$contador)." EST  ".sprintf("%.1f", (($lap_time1-$lap_time0)/$contador)*($total-$contador)/3600)  ."h \r";
}



?>
