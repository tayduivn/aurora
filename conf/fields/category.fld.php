<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 26 May 2016 at 14:29:11 CEST, Mijas Costa, Spain

 Copyright (c) 2016, Inikoo

 Version 3.0
*/
if (isset($options['new']) and  $options['new'] ) {
	$new=true;
}else {
	$new=false;
}

switch ($options['Category Scope']) {
    case 'Product':
        $subject_options=array('Product'=>_('Products'),'Category'=>_('Categories'));
        $subject_value='Product';
          $subject_formatted_value=_('Products');
        break;
    default:
        $subject_options=array();
           $subject_value='';
          $subject_formatted_value='';
        break;
}



$object_fields=array(
	array(
		'label'=>_('Id'),
		'show_title'=>true,
		'fields'=>array(
			
			array(
				'edit'=>'option',
				'render'=>($new?true:false),
				'id'=>'Category_Subject',
				'options'=>$subject_options,
				'value'=> $subject_value,
				'formatted_value'=> $subject_formatted_value,
				'label'=>_('Subject type'),
				'type'=>'value'
			),
			
			array(
				'edit'=>($edit?'string':''),
				'id'=>'Category_Code',
				'value'=>$object->get('Category Code'),
				'formatted_value'=>$object->get('Code'),
				'label'=>ucfirst($object->get_field_label('Category Code')),
				'invalid_msg'=>get_invalid_message('string'),
				'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates')),
				'type'=>'value'
			),
				array(
				'edit'=>($edit?'string':''),
				'id'=>'Category_Label',
				'value'=>$object->get('Category Label'),
				'formatted_value'=>$object->get('Label'),
				'label'=>ucfirst($object->get_field_label('Category Label')),
				'invalid_msg'=>get_invalid_message('string'),
				'type'=>'value'
			),

		)
	),
	
	
	
);


if(!$new){
$operations=array(
	'label'=>_('Operations'),
	'show_title'=>true,
	'class'=>'operations',
	'fields'=>array(
		array(
			'id'=>'delete_category',
			'class'=>'operation',
			'value'=>'',
			'label'=>'<i class="fa fa-fw fa-lock button" onClick="toggle_unlock_delete_object(this)" style="margin-right:20px"></i> <span onClick="delete_object(this)" class="delete_object disabled">'._('Delete category').' <i class="fa fa-trash new_button link"></i></span>',
			'reference'=>'',
			'type'=>'operation'
		),

	)

);
$object_fields[]=$operations;

}



?>
