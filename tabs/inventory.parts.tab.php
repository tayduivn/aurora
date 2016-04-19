<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 30 September 2015 18:22:51 BST, Sheffield, UK
 Copyright (c) 2015, Inikoo

 Version 3

*/



$tab='inventory.parts';
$ar_file='ar_inventory_tables.php';
$tipo='parts';

$default=$user->get_tab_defaults($tab);



$table_views=array(
	'overview'=>array('label'=>_('Overview')),
	'sales'=>array('label'=>_('Sales')),

);

$table_filters=array(
	'reference'=>array('label'=>_('Reference'), 'title'=>_('Part reference')),

);


$parameters=array(
	'parent'=>$state['parent'],
	'parent_key'=>$state['parent_key'],

);

include 'utils/get_table_html.php';


?>
