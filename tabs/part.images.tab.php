<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 2 March 2016 at 10:06:02 GMT+8, Yiwu, China
 Copyright (c) 2015, Inikoo

 Version 3

*/

$tab='part.images';
$ar_file='ar_images_tables.php';
$tipo='images';

$default=$user->get_tab_defaults($tab);

$table_views=array(
);

$table_filters=array(
	'caption'=>array('label'=>_('Caption')),
);

$parameters=array(
	'parent'=>$state['object'],
	'parent_key'=>$state['key'],

);


$smarty->assign('upload_file', array(
		'tipo'=>'upload_images',
		'object'=>$state['object'],
		'key'=>$state['key'],
		'label'=>_('Upload image')
	));

$smarty->assign('js_code', file_get_contents('build/js/injections/edit_images.min.js'));
include 'utils/get_table_html.php';

?>
