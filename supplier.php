<?php
/*
 File: supplier.php 

 UI supplier page

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/
include_once('common.php');
include_once('class.Supplier.php');

if(!$user->can_view('suppliers'))
  exit();
$modify=$user->can_edit('suppliers');



$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 //$yui_path.'autocomplete/assets/skins/sam/autocomplete.css',

		 'common.css',
		 'button.css',
		 'container.css',
		 'table.css'
		 );
$js_files=array(

		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'animation/animation-min.js',

		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'common.js.php',
		'table_common.js.php',
	
		);



$edit=false;
if(isset($_REQUEST['edit']) and $_REQUEST['edit']){
  $edit=true;
  $_REQUEST['id']=$_REQUEST['edit'];
 }
if(!$modify)
  $edit=false;


if(isset($_REQUEST['id']) and is_numeric($_REQUEST['id']))
  $supplier_id=$_REQUEST['id'];
else
  $supplier_id=$_SESSION['state']['supplier']['id'];

$_SESSION['state']['supplier']['id']=$supplier_id;

$smarty->assign('supplier_id',$supplier_id);

$supplier=new Supplier($supplier_id);

$company=new Company($supplier->data['Supplier Company Key']);
//$supplier->load('contacts');
$smarty->assign('supplier',$supplier);
$smarty->assign('company',$company);

$address=new address($company->data['Company Main Address Key']);
$smarty->assign('address',$address);



$smarty->assign('parent','suppliers.php');
$smarty->assign('title','Supplier: '.$supplier->get('Supplier Code'));


$tipo_filter=$_SESSION['state']['supplier']['products']['f_field'];
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['supplier']['products']['f_value']);

$filter_menu=array( 
		   'p.code'=>array('db_key'=>_('p.code'),'menu_label'=>'Our Product Code','label'=>'Code'),
		   'sup_code'=>array('db_key'=>_('sup_code'),'menu_label'=>'Supplier Product Code','label'=>'Supplier Code'),
		   );
$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);


if($edit){
 $sql=sprintf("select * from kbase.`Salutation Dimension` S left join kbase.`Language Dimension` L on S.`Language Code`=L.`Language ISO 639-1 Code`  where `Language Code`=%s limit 1000",prepare_mysql($myconf['lang']));
  $result=mysql_query($sql);
  $salutations=array();
  while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
    $salutations[]=array('txt'=>$row['Salutation'],'relevance'=>$row['Relevance'],'id'=>$row['Salutation Key']);
  }
  mysql_free_result($result);
  $smarty->assign('prefix',$salutations);
  $editing_block=$_SESSION['state']['supplier']['edit'];
  $smarty->assign('edit',$editing_block);

  $addresses=$company->get_addresses();
  $smarty->assign('addresses',$addresses);
  $number_of_addresses=count($addresses);
  $smarty->assign('number_of_addresses',$number_of_addresses);

  $contacts=$company->get_contacts();
  $smarty->assign('contacts',$contacts);
  $number_of_contacts=count($contacts);
  $smarty->assign('number_of_contacts',$number_of_contacts);

  $smarty->assign('scope','Supplier');

  $js_files[]='js/edit_common.js';
  $js_files[]='js/validate_telecom.js';
 
  $js_files[]='edit_address.js.php';
  $js_files[]='edit_contact_from_parent.js.php';
  $js_files[]='edit_contact_telecom.js.php';
  $js_files[]='edit_contact_name.js.php';
  $js_files[]='edit_contact_email.js.php';

  $js_files[]=sprintf('edit_company.js.php?edit=%s&id=%d',$editing_block,$company->id);
  $js_files[]=sprintf('edit_supplier.js.php');

  $css_files[]=$yui_path.'assets/skins/sam/autocomplete.css';
  $css_files[]='css/edit_address.css';
  $css_files[]='css/edit.css';

  $smarty->assign('from','supplier');
  $smarty->assign('css_files',$css_files);
  $smarty->assign('js_files',$js_files);
  
  

  $smarty->display('edit_supplier.tpl');

  


}else{
$js_files[]=sprintf('supplier.js.php');
$smarty->assign('display',$_SESSION['state']['supplier']['display']);
$smarty->assign('products_view',$_SESSION['state']['supplier']['products']['view']);
$smarty->assign('products_percentage',$_SESSION['state']['supplier']['products']['percentage']);
$smarty->assign('products_period',$_SESSION['state']['supplier']['products']['period']);










$tipo_filter=$_SESSION['state']['supplier']['po']['f_field'];
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value1',$_SESSION['state']['supplier']['po']['f_value']);

$filter_menu=array( 
		   'id'=>array('db_key'=>_('p.code'),'menu_label'=>'Purchase order','label'=>'Id'),
		   'minvalue'=>array('db_key'=>'minvalue','menu_label'=>'Orders with a minimum value of <i>'.$myconf['currency_symbol'].'n</i>','label'=>'Min Value ('.$myconf['currency_symbol'].')'),
		   'maxvalue'=>array('db_key'=>'maxvalue','menu_label'=>'Orders with a maximum value of <i>'.$myconf['currency_symbol'].'n</i>','label'=>'Max Value ('.$myconf['currency_symbol'].')'),
		   'max'=>array('db_key'=>'max','menu_label'=>'Orders from the last <i>n</i> days','label'=>'Last (days)')
		   );
$smarty->assign('filter_menu1',$filter_menu);
$smarty->assign('filter_name1',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu1',$paginator_menu);

 $smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

$smarty->display('supplier.tpl');
}
?>