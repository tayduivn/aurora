<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 16 October 2015 at 14:28:16 BST, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/

$tab='website.search.queries';
$ar_file='ar_websites_tables.php';
$tipo='queries';

$default=$user->get_tab_defaults($tab);


$table_views=array(
	
);

$table_filters=array(
	'query'=>array('label'=>_('Query'),'title'=>_('Search query'))

);

$parameters=array(
		'parent'=>$state['key'],
		'parent_key'=>$state['key'],
		
);


include('utils/get_table_html.php');


?>
