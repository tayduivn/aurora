<?php
/*
 File: users.php 

 UI user managment page

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/
include_once('common.php');
if(!$user->can_view('users'))
  exit();

$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 //		 $yui_path.'datatable/assets/skins/sam/datatable.css',
		 // $yui_path.'container/assets/skins/sam/container.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 $yui_path.'build/assets/skins/sam/skin.css',
		 'common.css',
		 'css/edit.css',
		 'table.css'
		 );
$js_files=array(
		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container-min.js',
		$yui_path.'button/button-min.js',
		$yui_path.'menu/menu-min.js',
		'common.js.php',
		'table_common.js.php',
		'js/edit_common.js',
		'sha256.js.php',
		'passwordmeter.js.php',
		'edit_users_customer.js.php'
		);
		
	
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);


$_SESSION['state']['users']['supplier']['type']='Supplier';

$sql="select (select count(*) from `User Group Dimension`) as number_groups ,( select count(*) from `User Dimension`) as number_users ";
$result = mysql_query($sql);
if(!$user=mysql_fetch_array($result, MYSQL_ASSOC))
  exit;
mysql_free_result($result);
$smarty->assign('box_layout','yui-t4');



$smarty->assign('parent','users');
$smarty->assign('title', _('Users'));


$sql="select `Language Code` as  id from `Language Dimension`";
$newuser_langs=array();
$result=mysql_query($sql);
 while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
  $newuser_langs[$row['id']]=$_lang[$row['id']];
 }
 mysql_free_result($result);
$smarty->assign('newuser_langs',$newuser_langs);

$sql="select `User Group Key` as id from `User Group Dimension`";
$newuser_groups=array();
$res=mysql_query($sql);
while($row=mysql_fetch_array($res, MYSQL_ASSOC)){
  $newuser_groups[$row['id']]=$_group[$row['id']];
 }
 mysql_free_result($res);
$smarty->assign('newuser_groups',$newuser_groups);

//create user list
/* $sql=sprintf("select `Staff ID` as id,`Staff Alias` as alias,(select count(*) from liveuser_users where tipo=1 and id_in_table=`Staff Dimension`.`Staff Key`) as is_user from `Staff Dimension` where `Staff Currently Working`='Yes' and `Staff Most Recent`='Yes' order by `Staff Alias`"); */
/* $result=mysql_query($sql); */
/* $num_cols=5; */
/* $staff=array(); */
/* while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){ */
/*   $staff[]=array('alias'=>$row['alias'],'id'=>$row['id'],'is_user'=>$row['is_user']); */
/*  } */
/* foreach($staff as $key=>$_staff){ */
/*   $staff[$key]['mod']=fmod($key,$num_cols); */
/* } */
/* $smarty->assign('staff',$staff); */
/* $smarty->assign('staff_cols',$num_cols); */



/* $sql=sprintf("select `Supplier Key` as id,`Supplier Code` as alias,(select count(*) from liveuser_users where tipo=2 and id_in_table=`Supplier Dimension`.`Supplier Key`) as is_user from `Supplier Dimension`  order by `Supplier Code`"); */
/* $result=mysql_query($sql); */
/* $num_cols=4; */
/* $supplier=array(); */
/* while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){ */
/*   $supplier[]=array('alias'=>$row['alias'],'id'=>$row['id'],'is_user'=>$row['is_user']); */
/*  } */
/*  mysql_free_result($result); */
/* foreach($supplier as $key=>$_supplier){ */
/*   $supplier[$key]['mod']=fmod($key,$num_cols); */
/* } */
/* $smarty->assign('suppliers',$supplier); */
/* $smarty->assign('supplier_cols',$num_cols); */



$tipo_filter=$_SESSION['state']['users']['supplier']['f_field'];

$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['users']['supplier']['f_value']);
$filter_menu=array(
		   'alias'=>array('db_key'=>'alias','menu_label'=>'Alias like  <i>x</i>','label'=>'Alias'),
		   'name'=>array('db_key'=>'name','menu_label'=>'Name Like <i>x</i>','label'=>'Name'),
		   );
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);

$smarty->display('edit_users_customer.tpl');
?>
