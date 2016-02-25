<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 9 October 2015 at 12:43:25 CEST, Malaga Spain
 Copyright (c) 2015, Inikoo

 Version 3

*/

include_once 'utils/invalid_messages.php';
include_once 'utils/country_functions.php';


$part=$state['_object'];

$options_Packing_Group=array(
	'None'=>_('None'), 'I'=>'I', 'II'=>'II', 'III'=>'III'
);


$object_fields=array(
	array(
		'label'=>_('Id'),
		'show_title'=>true,
		'fields'=>array(

			array(
				'id'=>'Part_Reference',
				'edit'=>'string',
				'value'=>htmlspecialchars($part->get('Part Reference')),
				'formatted_value'=>$part->get('Reference'),
				'label'=>ucfirst($part->get_field_label('Part Reference')),
				'required'=>true,
				'server_validation'=>'check_for_duplicates'
			),




		)
	),
	array(
		'label'=>_('Stock unit'),
		'show_title'=>true,
		'fields'=>array(
			array(
				'id'=>'Part_Unit_Description',
				'edit'=>'string',
				'value'=>htmlspecialchars($part->get('Part Unit Description')),
				'formatted_value'=>$part->get('Unit Description'),
				'label'=>ucfirst($part->get_field_label('Part Unit Description')),
				'required'=>true,



			),

			array(
				'id'=>'Part_Package_Weight',
				'edit'=>'numeric',
				'value'=>$part->get('Part Package Weight') ,
				'formatted_value'=>$part->get('Package Weight') ,
				'label'=>ucfirst($part->get_field_label('Part Package Weight')),
				'invalid_msg'=>get_invalid_message('numeric'),
				'required'=>true,
			),
			array(
				'id'=>'Part_Package_Dimensions',
				'edit'=>'dimensions',
				'value'=>$part->get('Part Package Dimensions') ,
				'formatted_value'=>$part->get('Package Dimensions') ,
				'label'=>ucfirst($part->get_field_label('Part Package Dimensions')),
				'invalid_msg'=>get_invalid_message('string'),
				'required'=>true,
				'placeholder'=>_('L x W x H (in cm)')
			),


			array(
				'id'=>'Part_Tariff_Code',
				'edit'=>'numeric',
				'value'=>$part->get('Part Tariff Code') ,
				'formatted_value'=>$part->get('Tariff Code') ,
				'label'=>ucfirst($part->get_field_label('Part Tariff Code')),
				'invalid_msg'=>get_invalid_message('string'),
				'required'=>true,


			),
			array(
				'id'=>'Part_Duty_Rate',
				'edit'=>'numeric',
				'value'=>$part->get('Part Duty Rate') ,
				'formatted_value'=>$part->get('Duty Rate') ,
				'label'=>ucfirst($part->get_field_label('Part Duty Rate')),
				'invalid_msg'=>get_invalid_message('string'),
				'required'=>true,

			)


		)
	),
	array(
		'label'=>_('Health & Safety'),
		'show_title'=>true,
		'fields'=>array(

			array(
				'id'=>'Part_UN_Number',
				'edit'=>'string',
				'value'=>htmlspecialchars($part->get('Part UN Number')),
				'formatted_value'=>$part->get('UN Number'),
				'label'=>ucfirst($part->get_field_label('Part UN Number')),
				'required'=>false
			),
			array(
				'id'=>'Part_UN_Class',
				'edit'=>'string',
				'value'=>htmlspecialchars($part->get('Part UN Class')),
				'formatted_value'=>$part->get('UN Class'),
				'label'=>ucfirst($part->get_field_label('Part UN Class')),
				'required'=>false
			),
			array(
				'id'=>'Part_Packing_Group',
				'edit'=>'option',
				'options'=>$options_Packing_Group,
				'value'=>htmlspecialchars($part->get('Part Packing Group')),
				'formatted_value'=>$part->get('Packing Group'),
				'label'=>ucfirst($part->get_field_label('Part Packing Group')),
				'required'=>false
			),
			array(
				'id'=>'Part_Proper_Shipping_Name',
				'edit'=>'string',
				'value'=>htmlspecialchars($part->get('Part Proper Shipping Name')),
				'formatted_value'=>$part->get('Proper Shipping Name'),
				'label'=>ucfirst($part->get_field_label('Part Proper Shipping Name')),
				'required'=>false
			),
			array(
				'id'=>'Part_Hazard_Indentification_Number',
				'edit'=>'string',
				'value'=>htmlspecialchars($part->get('Part Hazard Indentification Number')),
				'formatted_value'=>$part->get('Hazard Indentification Number'),
				'label'=>ucfirst($part->get_field_label('Part Hazard Indentification Number')),
				'required'=>false
			)
		)
			
			




		),
	
	array(
		'label'=>_('Components'),
		'show_title'=>true,
		'fields'=>array(

			array(
				'id'=>'Part_Materials',
				'edit'=>'textarea',
				'value'=>htmlspecialchars($part->get('Part Materials')),
				'formatted_value'=>$part->get('Materials'),
				'label'=>ucfirst($part->get_field_label('Part Materials')),
				'required'=>false
			),
			
			array(
				'id'=>'Part_Origin_Country_Code',
				'edit'=>'country',
				'value'=>htmlspecialchars($part->get('Part Origin Country Code')),
				'formatted_value'=>$part->get('Origin Country Code'),
				'label'=>ucfirst($part->get_field_label('Part Origin Country Code')),
				'required'=>false
			),
			
		)
			
			



		)	
	


);
$smarty->assign('object_fields', $object_fields);
$smarty->assign('state', $state);
$smarty->assign('preferred_countries', '"'.join('", "', preferred_countries(
($part->get('Part Origin Country Code')==''?$account->get('Account Country 2 Alpha Code'):$part->get('Part Origin Country Code'))
)).'"');

$html=$smarty->fetch('edit_object.tpl');

?>
