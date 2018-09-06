<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 29 August 2018 at 20:47:47 GMT+8, Kuala Lumpur, Malaysis
 Copyright (c) 2018, Inikoo

 Version 3

*/

include_once 'common.php';
include_once 'utils/object_functions.php';

include_once 'class.Public_Website.php';
include_once 'class.Public_Webpage.php';
include_once 'class.Public_Store.php';

if(!isset($_REQUEST['website_key']) or !is_numeric($_REQUEST['website_key'])){
    exit;
}

if (!isset($_REQUEST['theme']) or !preg_match('/^theme\_\d+$/', $_REQUEST['theme'])) {
    print 'no theme set up:->'.$_REQUEST['theme'].'<';
    return;
}


$website_key=$_REQUEST['website_key'];
$theme=$_REQUEST['theme'];

$website=get_object('Website',$website_key);
$store=new Public_Store($website->get('Website Store Key'));




$header_data = $website->get('Header Data');
$header_key=$website->get('Website Header Key');
$smarty->assign('header_data', $header_data);
$smarty->assign('header_key', $header_key);





$header_data = $website->get('Footer Data');
$header_key=$website->get('Website Footer Key');
$smarty->assign('footer_data', $header_data);
$smarty->assign('footer_key', $header_key);


$webpage_key=$website->get_system_webpage_key('home.sys');

$webpage=new Public_Webpage($webpage_key);

$smarty->assign('webpage', $webpage);
$smarty->assign('website', $website);
$smarty->assign('store', $store);
$smarty->assign('logged_in', 'false');
$smarty->assign('zero_money', money(0,$store->get('Store Currency Code')));
$smarty->assign('price', money(1.99,$store->get('Store Currency Code')));
$smarty->assign('rrp', money(3.99,$store->get('Store Currency Code')));


$smarty->assign('navigation', $webpage->get('Navigation Data'));
$smarty->assign('discounts', $webpage->get('Discounts'));

$smarty->assign('content', $webpage->get('Content Data'));


$settings=$website->settings;
$smarty->assign('settings',$settings);


$template = $theme.'/website.header.mobile.'.$theme.'.tpl';

if (file_exists('templates/'.$template)) {
    $smarty->display($template);
} else {
    printf("template %s not found",$template);
}

?>
