<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 14 September 2015 19:11:44 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/


$tab='customers_server';
$ar_file='ar_customers_tables.php';
$tipo='customers_server';

$default=$user->get_tab_defaults($tab);



$table_views=array();

$table_filters=array(
	'code'=>array('label'=>_('Code'),'title'=>_('Store code')),
	'name'=>array('label'=>_('Name'),'title'=>_('Store name')),

);

$parameters=array(
		'parent'=>'',
		'parent_key'=>'',
		'percentages'=>0,
);


include('utils/get_table_html.php');


?>
