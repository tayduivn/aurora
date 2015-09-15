<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require_once 'conf/dns.php';
require_once 'conf/key.php';

require_once 'common_functions.php';
require_once 'common_detect_agent.php';

//require_once "class.Session.php";
require_once "aes.php";
require_once "class.Account.php";

require_once "class.Auth.php";
require_once "class.User.php";

$mem = new Memcached();
$mem->addServer($memcache_ip, 11211);

$external_DB_link=false;

if (isset($connect_to_external) and isset($external_dns_user)) {
	$external_DB_link=mysql_connect($external_dns_host,$external_dns_user,$external_dns_pwd );

	if (!$external_DB_link) {
		print "Error can not connect with external database server\n";
	}
	$external_db_selected=mysql_select_db($external_dns_db, $external_DB_link);
	if (!$external_db_selected) {
		print "Error can not access the external database\n";
	}
	//print $external_DB_link;
}


$default_DB_link=mysql_connect($dns_host,$dns_user,$dns_pwd );
if (!$default_DB_link) {
	print "Error can not connect with database server\n";
}
$db_selected=mysql_select_db($dns_db, $default_DB_link);
if (!$db_selected) {
	print "Error can not access the database\n";
	exit;
}


mysql_set_charset('utf8');

mysql_query("SET time_zone='+0:00'");
require_once 'conf/modules.php';
require_once 'conf/conf.php';

$inikoo_account=new Account();
date_default_timezone_set($inikoo_account->data['Account Timezone']) ;
define("TIMEZONE",$inikoo_account->data['Account Timezone']);

//$max_session_time=$myconf['max_session_time'];
//$max_session_time_in_milliseconds=1000*$max_session_time;

//$session = new Session($max_session_time);
session_save_path('server_files/tmp');
ini_set('session.gc_maxlifetime', 57600); // 16 hours
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
session_start();

//print date("%c",1349421365);

//print_r($session);
//print '//'.session_id( );
//print_r($_SESSION['state']);

require 'external_libs/Smarty/Smarty.class.php';
$smarty = new Smarty();
$smarty->template_dir = 'templates';
$smarty->compile_dir = 'server_files/smarty/templates_c';
$smarty->cache_dir = 'server_files/smarty/cache';
$smarty->config_dir = 'server_files/smarty/configs';
//$smarty->error_reporting = E_STRICT;

$smarty->assign('inikoo_account',$inikoo_account);




if (isset($_REQUEST['log_as']) and $_REQUEST['log_as']=='supplier')
	$log_as="supplier";
else
	$log_as="staff";



$is_already_logged_in=(isset($_SESSION['logged_in']) and $_SESSION['logged_in']? true : false);

if (!$is_already_logged_in) {
	$target = $_SERVER['PHP_SELF'];
	if (!preg_match('/(js|js\.php)$/',$target)) {

		header('Location: login.php?log_as='.$log_as);
		exit;
	}
	exit;
}

if ($_SESSION['logged_in_page']!=0) {


	$sql=sprintf("update `User Log Dimension` set `Logout Date`=NOW()  where `Session ID`=%s", prepare_mysql(session_id()));
	mysql_query($sql);

	session_regenerate_id();
	session_destroy();
	unset($_SESSION);

	header('Location: login.php?log_as='.$log_as);
	exit;

}
$user=new User($_SESSION['user_key']);

$_client_locale='en_GB.UTF-8';
include_once 'set_locales.php';
require 'locale.php';

$_SESSION['locale_info'] = localeconv();
if ($_SESSION['locale_info']['currency_symbol']=='EU')
	$_SESSION['locale_info']['currency_symbol']='�';

$smarty->assign('lang_code',$_SESSION['text_locale_code']);
$smarty->assign('lang_country_code',strtolower($_SESSION['text_locale_country_code']));
$smarty->assign('locale',$_SESSION['text_locale_code'].'_'.$_SESSION['text_locale_country_code']);

$args="?";

foreach ($_GET as $key => $value) {
	if ($key!='_locale')$args.=$key.'='.$value.'&';
}


$smarty->assign('decimal_point',$_SESSION['locale_info']['decimal_point']);
$smarty->assign('thousands_sep',$_SESSION['locale_info']['thousands_sep']);
$smarty->assign('currency_symbol',$_SESSION['locale_info']['currency_symbol']);


$smarty->assign('user',$user);

$user->read_groups();
$user->read_rights();
$user->read_stores();
$user->read_websites();
$user->read_warehouses();
if ($user->data['User Type']=='Supplier') {
	$user->read_suppliers();

}


$corporate_currency=$inikoo_account->data['Account Currency'];
$corporate_currency_symbol=$inikoo_account->data['Account Currency Symbol'];
$corporate_country_code=$inikoo_account->data['Account Country Code'];
$corporate_country_2alpha_code=$inikoo_account->data['Account Country 2 Alpha Code'];
$inikoo_public_url=$inikoo_account->data['Inikoo Public URL'];


$smarty->assign('top_navigation_message',$inikoo_account->data['Short Message']);
$smarty->assign('account_name',$inikoo_account->data['Account Name']);
$account_code=$inikoo_account->data['Account Code'];

$smarty->assign('inikoo_version',$inikoo_account->data['Inikoo Version']);
$smarty->assign('top_navigation_message',$inikoo_account->data['Short Message']);
$smarty->assign('account_name',$inikoo_account->data['Account Name']);
$account_label=($inikoo_account->data['Account Menu Label']==''?_('Company'):$inikoo_account->data['Account Menu Label']);
$smarty->assign('account_label',$account_label);


/*
$lang_menu=$mem->get('EPRLANG'.$account_code.$inikoo_account->data['Inikoo Version']);
if (!$lang_menu) {
	$lang_menu=array();
	$sql=sprintf("select `Language Code`,`Country 2 Alpha Code`,`Language Original Name` from `Language Dimension` order by `Language Original Name`");
	$res=mysql_query($sql);

	while ($row=mysql_fetch_assoc($res) ) {
		$_locale=$row['Language Code'].'_'.$row['Country 2 Alpha Code'].'.UTF-8';
		$lang_menu[]=array($_SERVER['PHP_SELF'].$args.'_locale='.$_locale,strtolower($row['Country 2 Alpha Code']),$row['Language Original Name']);
	}
	$mem->set('EPRLANG'.$account_code.$inikoo_account->data['Inikoo Version'], $lang_menu, 1728000);
}



$smarty->assign('lang_menu',$lang_menu);
*/

$common='';

$smarty->assign('page_name',basename($_SERVER["PHP_SELF"], ".php"));
$smarty->assign('analyticstracking',( file_exists('templates/analyticstracking.tpl')?true:false));

?>
