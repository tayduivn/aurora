<?php
/*
File: export_wizard.php

UI customer page

About:
Autor: Raul Perusquia <rulovico@gmail.com>

Copyright (c) 2009, Kaktus

Version 2.0
*/

include_once('common.php');
include_once('class.Customer.php');

$css_files=array(
         $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
         $yui_path.'menu/assets/skins/sam/menu.css',
         $yui_path.'calendar/assets/skins/sam/calendar.css',
         $yui_path.'button/assets/skins/sam/button.css',
         $yui_path.'editor/assets/skins/sam/editor.css',
         $yui_path.'assets/skins/sam/autocomplete.css',

         'text_editor.css',
         'common.css',
         'button.css',
         'container.css',
         'table.css',
         'css/customer.css'

         );
$js_files=array(
        $yui_path.'utilities/utilities.js',
        $yui_path.'json/json-min.js',
        $yui_path.'paginator/paginator-min.js',
        $yui_path.'datasource/datasource-min.js',
        $yui_path.'autocomplete/autocomplete-min.js',
        $yui_path.'datatable/datatable-min.js',
        $yui_path.'container/container-min.js',
        $yui_path.'editor/editor-min.js',
        $yui_path.'menu/menu-min.js',
        $yui_path.'calendar/calendar-min.js',
        'external_libs/ampie/ampie/swfobject.js',
        'common.js.php',
        'table_common.js.php',
        'js/search.js',
        'js/edit_common.js',
        'customer.js.php'
        );
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

if(!$_REQUEST['id']){
	header('Location: index.php');
	exit;
}


if(!$user->can_view('customers')){
  exit();
}

if(isset($_REQUEST['id']) and is_numeric($_REQUEST['id']) ){
  $_SESSION['state']['customer']['id']=$_REQUEST['id'];
  $customer_id=$_REQUEST['id'];
}else{
  $customer_id=$_SESSION['state']['customer']['id'];
}

$customer=new customer($customer_id);
$customer_id = $customer->data['Customer Key'];

$list=$customer->data;
if(isset($_SESSION['list'])){
	unset($_SESSION['list']);
}
//print_r($list);
$smarty->assign('customer_id',$customer_id);
$smarty->assign('list',$list);

$smarty->display('export_wizard.tpl');
?>
