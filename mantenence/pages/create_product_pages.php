<?php
include_once '../../conf/dns.php';
include_once '../../class.Department.php';
include_once '../../class.Deal.php';
include_once '../../class.Charge.php';
include_once '../../class.Family.php';
include_once '../../class.Product.php';
include_once '../../class.Supplier.php';
include_once '../../class.Part.php';
include_once '../../class.Warehouse.php';
include_once '../../class.Node.php';
include_once '../../class.Shipping.php';
include_once '../../class.SupplierProduct.php';
include_once '../../class.Image.php';

error_reporting(E_ALL);
date_default_timezone_set('UTC');
include_once '../../set_locales.php';
require '../../locale.php';
$_SESSION['locale_info'] = localeconv();


$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if (!$con) {
	print "Error can not connect with database server\n";
	exit;
}
//$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db) {
	print "Error can not access the database\n";
	exit;
}
$codigos=array();


require_once '../../common_functions.php';

mysql_set_charset('utf8');
require_once '../../conf/conf.php';
date_default_timezone_set('UTC');

$site_key=1;


$site = new Site($site_key);


$sql=sprintf("select `Product Department Key` from  `Product Department Dimension` where `Product Department Store Key`=%d and `Product Department Sales Type`='Public Sale'",
	$site->data['Site Store Key']);

$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {

	$sql=sprintf("select `Page Key` from `Page Store Dimension` where `Page Store Section Type`='Department' and `Page Parent Key`=%d and `Page Site Key`=%d",
		$row['Product Department Key'],
		$site->id
	);
	$res2=mysql_query($sql);
	if ($row2=mysql_fetch_assoc($res2)) {
		continue;
	}


	$page_data=array(
		'Page Store Content Display Type'=>'Template',
		'Page Store Content Template Filename'=>'department',
		'Page State'=>'Online'
	);

	$site->add_department_page($row['Product Department Key'],$page_data);
}

$sql=sprintf("select `Product Family Key` from  `Product Family Dimension` where `Product Family Store Key`=%d  and `Product Family Sales Type`='Public Sale'",
	$site->data['Site Store Key']);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {

	$sql=sprintf("select `Page Key` from `Page Store Dimension` where `Page Store Section Type`='Family' and `Page Parent Key`=%d and `Page Site Key`=%d",
		$row['Product Family Key'],
		$site->id
	);
	$res2=mysql_query($sql);
	if ($row2=mysql_fetch_assoc($res2)) {
		$family_page=new Page($row2['Page Key']);
		$family_page->update_button_products('Parent');
		$family_page->update_list_products();
		continue;
	}



	$page_data=array(
		'Page Store Content Display Type'=>'Template',
		'Page Store Content Template Filename'=>'family_buttons',
		'Page State'=>'Online'
	);
	$family_page_key=$site->add_family_page($row['Product Family Key'],$page_data);
	$family_page=new Page($family_page_key);
	$family_page->update_button_products('Parent');
	$family_page->update_list_products();
}





?>
