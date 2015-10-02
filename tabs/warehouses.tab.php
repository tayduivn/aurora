<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 2 October 2015 at 12:16:14 BST, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


$tab='warehouses';
$ar_file='ar_warehouse_tables.php';
$tipo='warehouses';

$default=$user->get_tab_defaults($tab);


$table_views=array();

$table_filters=array(
	'code'=>array('label'=>_('Code'),'title'=>_('Store code')),
	'name'=>array('label'=>_('Name'),'title'=>_('Store name')),

);

$parameters=array(
		'parent'=>'',
		'parent_key'=>'',
);


include('utils/get_table_html.php');


?>
