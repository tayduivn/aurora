<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once('../../conf/dns.php');
include_once('../../class.Store.php');

include_once('../../class.Deal.php');
include_once('../../class.DealCampaign.php');

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


$sql="select `Deal Campaign Key` from `Deal Campaign Dimension`";
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {

	$campaign=new DealCampaign($row['Deal Campaign Key']);
	$campaign->update_status();
	
}

?>