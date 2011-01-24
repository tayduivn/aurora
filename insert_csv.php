<?php
/*
 File: store.php 

 UI store page

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/
include_once('common.php');



$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 $yui_path.'assets/skins/sam/autocomplete.css',

		 //	 $yui_path.'assets/skins/sam/autocomplete.css',
		 'common.css',
		 'container.css',
		 'button.css',
		 'table.css',
		 'css/dropdown.css',
		 'css/import_data.css'
		 );
$js_files=array(
		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'dragdrop/dragdrop-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'uploader/uploader-debug.js',
		'js/php.default.min.js',
		'common.js.php',
		'table_common.js.php',
		'js/dropdown.js',
        	);


	//value of the assigned field
	$assign = $_REQUEST['assign_field'];


	//value of the right column
	$values = $_REQUEST['values'];

	
	//code to generate the final array		
	for($i = 0; $i < count($_REQUEST['assign_field']);  $i++) 
	{

		//restrict whether any ignore field is there 
		if($_REQUEST['assign_field'][$i] != '0')
		{

		 	$rows[$_REQUEST['assign_field'][$i]] = $_REQUEST['values'][$i];

		}		
		
	}
	
	
	

$smarty->assign('js_files',$js_files);
$smarty->assign('css_files',$css_files);
$smarty->assign('assign',$assign);
$smarty->assign('values',$values);

 
$smarty->display('insert_csv.tpl');
echo '<pre>'; print_r($rows);

?>
