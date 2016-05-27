<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 28 December 2015 at 16:34:36 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/



$tab='category.categories';
$ar_file='ar_categories_tables.php';
$tipo='categories';

$default=$user->get_tab_defaults($tab);



$table_views=array(

);

$table_filters=array(
	'label'=>array('label'=>_('Label'), 'title'=>_('Category label')),
	'code'=>array('label'=>_('Code'), 'title'=>_('Category code')),

);

$parameters=array(
	'parent'=>$state['object'],
	'parent_key'=>$state['key'],

);


$table_buttons[]=array(
	'icon'=>'plus',
	'title'=>_('New category'),
	'id'=>'new_record',
	'inline_new_object'=>
	array(
		'field_id'=>'Category_Code',
		'field_label'=>_('Add category').':',
		'field_edit'=>'string',
		'object'=>'Category',
		'parent'=>$state['object'],
		'parent_key'=>$state['key'],
		'placeholder'=>_("Category's code")
	)

);

$smarty->assign('table_buttons', $table_buttons);


include 'utils/get_table_html.php';


?>
