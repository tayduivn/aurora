<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 28 December 2015 at 15:57:09 GMT+8, Kuala Lumpur, Mlaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

$tab='category.history';
$ar_file='ar_history_tables.php';
$tipo='object_history';

$default=$user->get_tab_defaults($tab);

$table_views=array(
);

$table_filters=array(
//	'note'=>array('label'=>_('Notes'),'title'=>_('Notes')),
);

$parameters=array(
		'parent'=>$state['object'],
		'parent_key'=>$state['key'],
		
);

include('utils/get_table_html.php');

?>
