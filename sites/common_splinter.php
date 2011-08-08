<?php

//$path = 'classes/';
//set_include_path(get_include_path() . $path);
//print get_include_path() . PATH_SEPARATOR . $path;
  include_once('app_files/key.php');
                include_once('aes.php');
 
require_once 'app_files/db/dns.php';
require_once("conf/checkout.php");
require_once 'common_functions.php';
require_once 'ar_show_products.php';

require_once "classes/class.Session.php";

require_once "classes/class.Auth.php";
require_once "classes/class.User.php";
require_once "classes/class.Site.php";
require_once "classes/class.LightCustomer.php";

require_once "classes/class.LightProduct.php";
require_once "classes/class.LightFamily.php";


$secret_key='FDK/S5GRkZFXi47zvs4pTezyfEr5nWFthsFbG6j1CzCPYPX5';

$default_DB_link=mysql_connect($dns_host,$dns_user,$dns_pwd );
if (!$default_DB_link) {
    print "Error can not connect with database server\n";
}
$db_selected=mysql_select_db($dns_db, $default_DB_link);
if (!$db_selected) {
    print "Error can not access the database\n";
    exit;
}
mysql_query("SET NAMES 'utf8'");
require_once 'conf/timezone.php';
date_default_timezone_set(TIMEZONE) ;
mysql_query("SET time_zone='+0:00'");
require_once 'conf/conf.php';

$yui_path="external_libs/yui/2.9/build/";
$pics_path='http://tunder/';

$max_session_time=36000;
$session = new Session($max_session_time,1,100);
//require('external_libs/Smarty/Smarty.class.php');
//$smarty = new Smarty();

$public_url=$myconf['public_url'];
if(!isset($_SESSION['basket'])){
$_SESSION['basket']=array('items'=>0,'total'=>0);

}

if(isset($_REQUEST['qty']) and is_numeric($_REQUEST['qty'])){
$_SESSION['basket']['items']=$_REQUEST['qty'];
}
if(isset($_REQUEST['tot']) and is_numeric($_REQUEST['tot'])){
$_SESSION['basket']['total']=$_REQUEST['tot'];
}

$site=new Site($myconf['site_key']);
if(!$site->id){

exit ("Site data not found");
}



$store_key=$site->data['Site Store Key'];
$store=new Store($store_key);
$store_code=$store->data['Store Code'];
//$smarty->assign('store_code',$store_code);
//$smarty->assign('store_key',$store_key);

$_client_locale=$store->data['Store Locale'].'.UTF-8';
setlocale(LC_MONETARY, $_client_locale);


$traslated_labels=array();

if (file_exists($store_code.'/labels.php')) {
    require_once $store_code.'/labels.php';
} else {
    require_once 'conf/labels.php';
}



$_SESSION ['lang']='';



$logout = (array_key_exists('logout', $_REQUEST)) ? $_REQUEST['logout'] : false;
 
if ($logout) {
 
 //    $sql=sprintf("update `User Log Dimension` set `Logout Date`=NOW()  where `Session ID`=%s", prepare_mysql(session_id()));
  //  mysql_query($sql);
 
    session_regenerate_id();
    session_destroy();
    unset($_SESSION);
 
   // include_once 'login.php';
   // exit;
   
   $_SESSION['logged_in']=0;
$logged_in=false;
$St=get_sk();
   
}



if(isset($_REQUEST['p'])){


$dencrypted_secret_data=AESDecryptCtr(base64_decode($_REQUEST['p']),$secret_key,256);
// print "$dencrypted_secret_data\n";
 $auth=new Auth(IKEY,SKEY);

    $auth->authenticate_from_masterkey($dencrypted_secret_data);

if ($auth->is_authenticated()) {
    $_SESSION['logged_in']=true;
    $_SESSION['store_key']=$store_key;
    $_SESSION['site_key']=$site->id;

    $_SESSION['user_key']=$auth->get_user_key();
    $_SESSION['customer_key']=$auth->get_user_parent_key();
    
    
    }


}



$logged_in=(isset($_SESSION['logged_in']) and $_SESSION['logged_in']? true : false);
if(!isset($_SESSION['site_key']) or !isset($_SESSION['user_key'])){
$_SESSION['logged_in']=0;
$logged_in=false;
$St=get_sk();
}

if ($logged_in ) {

    if ($_SESSION['site_key']!=$site->id) {
        $_SESSION['logged_in']=0;
        $logged_in=false;
        $St=get_sk();
    } else {

        $user=new User($_SESSION['user_key']);
        $customer=new LightCustomer($_SESSION['customer_key']);

    }

} 
else {

$_SESSION['logged_in']=0;
$logged_in=false;
$St=get_sk();


 
}

log_visit($session->id);



function get_sk(){
      

   $Sk="skstart|".(date('U')+300)."|".ip()."|".IKEY."|".sha1(mt_rand()).sha1(mt_rand());
        $St=AESEncryptCtr($Sk,SKEY, 256);
return $St;
}



function show_product($code){
	global $logged_in, $ecommerce_url, $username, $method,$store_key;
	$product=new LightProduct($code, $store_key);

	if(!$product->match)
		return;

	
	$data=array('ecommerce_url'=>$ecommerce_url,'username'=>$username,'method'=>$method);
	
	if($logged_in){
				print $product->get_full_order_form('ecommerce', $data);

	}else{

	print $product->get_info();

	}

}



function show_products($code){
	
	global $logged_in,$ecommerce_url_multi, $username, $method;
	
	$conf= array('ecommerce_url_multi'=>$ecommerce_url_multi
				,'username'=>$username
				,'method'=>$method
				,'secure'=>(empty($secure) ? '' : $_SERVER["HTTPS"])
				,'_port'=>$_SERVER["SERVER_PORT"]
				,'_protocol'=>$_SERVER["SERVER_PROTOCOL"]
				,'url'=>$_SERVER['REQUEST_URI']
				,'server'=>$_SERVER['SERVER_NAME']
				);

	
	$code_list=array();
	$data=array();

	
	if(preg_match('/,/', $code)){
		$code_list=explode(',', $code);
		
		foreach($code_list as $code){
			$product=new LightProduct($code, 1);
			if($product->match){
				$data[]=$product->data;
			}
		}
		if($logged_in){
			echo show_products_in_family('ecommerce', $data, $conf);
			return;
		}
		else{
			echo show_products_in_family_info($data);
			return;
		}
	}
	else{
	}

	
	$product=new LightFamily($code, 1);
	if(!$product->match)
		return;
	
	
	
	$s = empty($secure) ? '' : $_SERVER["HTTPS"];
	if($logged_in){
		echo $product->get_order_list('ecommerce', $s, $_SERVER["SERVER_PORT"], $_SERVER["SERVER_PROTOCOL"], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_NAME'], $ecommerce_url_multi, $username, $method);
	}
	else{
		echo $product->get_order_list_info();
		return;
	}
}





?>
