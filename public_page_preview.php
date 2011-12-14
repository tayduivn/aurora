<?php

include_once('app_files/db/dns.php');
include_once('class.Image.php');

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
date_default_timezone_set('UTC');

require_once 'common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once 'conf/conf.php';
setlocale(LC_MONETARY, 'en_GB.UTF-8');



include_once('class.Customer.php');
include_once('class.Store.php');
include_once('class.Page.php');
include_once('class.Site.php');

if (!isset($_REQUEST['id'])  or  !is_numeric($_REQUEST['id']) ) {
     header('Location: index.php');
    exit;
} 


$page_key=$_REQUEST['id'];
$page=new Page($page_key);

$site=new Site($page->data['Page Site Key']);
$store=new Store($page->data['Page Store Key']);


$css_files=array(
        $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
           );
           
           
           
           
//include_once('Theme.php');
$js_files=array(
              $yui_path.'utilities/utilities.js',
              $yui_path.'json/json-min.js',
              $yui_path.'paginator/paginator-min.js',
           
			'js/page_preview.js'
			
          );
          
   $sql=sprintf("select `External File Type`,`Page Store External File Key` as external_file_key from `Site External File Bridge` where `Site Key`=%d",$site->id);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
    if ($row['External File Type']=='CSS')
        $css_files[]='public_external_file.php?id='.$row['external_file_key'];
    else
        $js_files[]='public_external_file.php?id='.$row['external_file_key'];

}
$sql=sprintf("select `External File Type`,`Page Store External File Key` as external_file_key from `Page Store External File Bridge` where `Page Key`=%d",$page->id);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
    if ($row['External File Type']=='CSS')
        $css_files[]='public_external_file.php?id='.$row['external_file_key'];
    else
        $js_files[]='public_external_file.php?id='.$row['external_file_key'];

}

$sql=sprintf("select `External File Type`,`Page Store External File Key` as external_file_key from `Page Header External File Bridge` where `Page Header Key`=%d",$page->data['Page Header Key']);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
    if ($row['External File Type']=='CSS')
        $css_files[]='public_external_file.php?id='.$row['external_file_key'];
    else
        $js_files[]='public_external_file.php?id='.$row['external_file_key'];

}
/*
$sql=sprintf("select `External File Type`,`Page Store External File Key` as external_file_key from `Page Footer External File Bridge` where `Page Footer Key`=%d",$page->data['Page Footer Key']);
$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {
    if ($row['External File Type']=='CSS')
        $css_files[]='public_external_file.php?id='.$row['external_file_key'];
    else
        $js_files[]='public_external_file.php?id='.$row['external_file_key'];

}
*/
       
 
    
          
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);




$smarty->assign('title',_('Preview').' '.$page->data['Page Title']);
$smarty->assign('store',$store);
$smarty->assign('page',$page);
$smarty->assign('site',$site);

$smarty->assign('template_string',$page->data['Page Store Source']);
$smarty->display('page_preview.tpl');
?>