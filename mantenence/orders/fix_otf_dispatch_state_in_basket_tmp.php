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
include_once '../../class.Order.php';

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

$inikoo_account=new Account(1);

$sql="select `Order Key` from `Order Dimension` where  `Order Current Dispatch State` in  ('In Process by Customer')  order by `Order Key` desc";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
	$sql=sprintf("update `Order Transaction Fact` set `Current Dispatching State`='In Process by Customer' where `Current Dispatching State`='In Process' and `Order Key`=%d   ",
	$row['Order Key']
	);
	print "$sql\n";
    //mysql_query($sql);

}


?>
