<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 9 April 2016 at 18:43:17 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2016, Inikoo

 Version 3.0
*/

$account=new Account();

if (isset($options['new']) and  $options['new'] ) {
	$new=true;
}else {
	$new=false;
}


if (isset($options['part_scope']) and  $options['part_scope'] ) {
	$part_scope=true;
}else {
	$part_scope=false;
}


if (isset($options['show_full_label']) and  $options['show_full_label'] ) {
	$show_full_label=true;
	$field_prefix='Part ';
}else {
	$show_full_label=false;
	$field_prefix='';
}



$options_status=array('Available'=>_('Available'), 'NoAvailable'=>_('No stock'), 'Discontinued'=>_('Discontinued'));
$supplier_part_fields=array(
	array(
		'label'=>($show_full_label?_("Supplier's part Id"):_('Id')),

		'show_title'=>true,
		'fields'=>array(
			array(
				'id'=>'Supplier_Part_Supplier_Key',
				'render'=>(($new and $part_scope)?true:false ),
				'edit'=>'dropdown_select',
				'scope'=>'suppliers',
				'parent'=>'account',
				'parent_key'=>1,
				'value'=>htmlspecialchars($object->get('Supplier Part Supplier Key')),
				'formatted_value'=>$object->get('Supplier Key'),
				'stripped_formatted_value'=>$object->get('Supplier Key'),
				'label'=>("Supplier's code"),
				'placeholder'=>_("Supplier's code"),
				'required'=>($part_scope?true:false ),
				'type'=>'value'
			),
			array(
				'id'=>'Supplier_Part_Reference',
				'edit'=>($edit?'string':''),

				'value'=>htmlspecialchars($object->get('Supplier Part Reference')),
				'formatted_value'=>$object->get('Reference'),
				'label'=>ucfirst($object->get_field_label('Supplier Part Reference')),
				'required'=>true,
				'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates')),
				'type'=>'value'
			),

			array(
				'id'=>'Part_Reference',
				'edit'=>($edit?'string':''),

				'value'=>htmlspecialchars($object->get('Part Part Reference')),
				'formatted_value'=>$object->get('Part Reference'),
				'label'=>ucfirst($object->get_field_label('Part Reference')),
				'required'=>true,
				'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates', 'field'=>'Part_Reference', 'parent'=>'account', 'parent_key'=>1, 'object'=>'Part', 'key'=>(isset($object->part->id)?$object->part->id:false))),
				'type'=>'value'
			),
			array(
				'id'=>'Part_Barcode_Number',
				'edit'=>($edit?'barcode':''),

				'value'=>htmlspecialchars($object->get('Part Part Barcode Number')),
				'formatted_value'=>$object->get('Part Barcode Number'),
				'label'=>ucfirst($object->get_field_label('Part Barcode Number')),
				'required'=>false,
				'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates', 'parent'=>'account', 'parent_key'=>1, 'object'=>'Part', 'key'=>(isset($object->part)?$object->part->id:0))),
				'type'=>'value'
			),
			array(
				'id'=>'Part_Barcode_Number_Next_Available',
				'edit'=>($edit?'string':''),
				'render'=>false,
				'value'=>'No',
				'formatted_value'=>'No',
				'label'=>'Next barcode',
				'required'=>false,
				'type'=>'value'
			)

		)
	),



	array(
		'label'=>($show_full_label?_("Supplier's part ordering"):_('Ordering')),

		'show_title'=>true,
		'fields'=>array(
			array(
				'render'=>($new?false:true),
				'id'=>'Supplier_Part_Status',
				'edit'=>($edit?'option':''),

				'options'=>$options_status,
				'value'=>htmlspecialchars($object->get('Supplier Part Status')),
				'formatted_value'=>$object->get('Status'),
				'label'=>ucfirst($object->get_field_label('Supplier Part Status')),
				'required'=>($new?false:true),
				'type'=>'skip'
			),




			array(
				'id'=>'Supplier_Part_Minimum_Carton_Order',
				'edit'=>'smallint_unsigned',
				'value'=>($new?1:htmlspecialchars($object->get('Supplier Part Minimum Carton Order'))),
				'formatted_value'=>($new?1:$object->get('Minimum Carton Order')),
				'label'=>ucfirst($object->get_field_label('Supplier Part Minimum Carton Order')),
				'placeholder'=>_('cartons'),

				'required'=>true,
				'type'=>'value'
			),
			array(
				'id'=>'Supplier_Part_Average_Delivery_Days',
				'edit'=>($edit?'numeric':''),

				'value'=>($new?1:htmlspecialchars($object->get('Supplier Part Average Delivery Days'))),
				'formatted_value'=>($new?7:$object->get('Average Delivery Days')),
				'label'=>ucfirst($object->get_field_label('Supplier Part Average Delivery Days')),
				'placeholder'=>_('days'),

				'required'=>false,
				'type'=>'value'
			),
			array(
				'id'=>'Supplier_Part_Carton_CBM',
				'edit'=>($edit?'numeric':''),

				'value'=>htmlspecialchars($object->get('Supplier Part Carton CBM')),
				'formatted_value'=>$object->get('Carton CBM'),
				'label'=>ucfirst($object->get_field_label('Supplier Part Carton CBM')),
				'placeholder'=>_('cubic meters'),
				'required'=>false,
				'type'=>'value'
			),


		)
	),

	array(
		'label'=>($show_full_label?_("Supplier's part cost/price"):_('Cost/price')),

		'show_title'=>true,
		'fields'=>array(
			array(
				'render'=>false,
				'id'=>'Supplier_Part_Currency_Code',
				'edit'=>($edit?'string':''),

				'value'=>($new? ($part_scope? '':  $options['supplier']->get('Supplier Default Currency Code')):htmlspecialchars($object->get('Supplier Part Currency Code'))),
				'formatted_value'=>($new? ($part_scope? '':$options['supplier']->get('Default Currency Code ')):htmlspecialchars($object->get('Currency Code'))),
				'label'=>ucfirst($object->get_field_label('Supplier Part Currency Code')),
				'required'=>false,
				'type'=>'value'
			),

			array(
				'id'=>'Supplier_Part_Unit_Cost',
				'edit'=>($edit?'amount':''),
				'locked'=>($part_scope?1:0),
				'value'=>htmlspecialchars($object->get('Supplier Part Unit Cost')),
				'formatted_value'=>$object->get('Unit Cost'),
				'label'=>ucfirst($object->get_field_label('Supplier Part Unit Cost')),
				'required'=>true,
				'placeholder'=>($part_scope? '':sprintf(_('amount in %s '), $options['supplier']->get('Default Currency Code'))),
				'type'=>'value'
			),

			array(
				'id'=>'Supplier_Part_Unit_Extra_Cost',
				'edit'=>'amount_margin',
				'locked'=>($part_scope?1:0),
				'value'=>htmlspecialchars($object->get('Supplier Part Unit Extra Cost')),
				'formatted_value'=>$object->get('Unit Extra Cost'),
				'label'=>ucfirst($object->get_field_label('Supplier Part Unit Extra Cost')),
				'required'=>false,
				'placeholder'=>($part_scope?'': sprintf(_('amount in %s or %%'), $options['supplier']->get('Default Currency Code'))),
				'type'=>'value'
			),
			array(
				'id'=>'Part_Part_Unit_Price',
				'edit'=>'amount_margin',

				'value'=>htmlspecialchars($object->get('Part Part Unit Price')),
				'formatted_value'=>$object->get('Part Unit Price'),
				'label'=>ucfirst($object->get_field_label('Part Unit Price')),
				'required'=>true,
				'placeholder'=>sprintf(_('amount in %s or margin (%%)'), $account->get('Currency')),
				'type'=>'value'
			),
			array(
				'id'=>'Part_Part_Unit_RRP',
				'edit'=>'amount_margin',
				'value'=>htmlspecialchars($object->get('Part Part Unit RRP')),
				'formatted_value'=>$object->get('Part Unit RRP'),
				'label'=>ucfirst($object->get_field_label('Part Unit RRP')),
				'required'=>true,
				'placeholder'=>sprintf(_('amount in %s or margin (%%)'), $account->get('Currency')),
				'type'=>'value'
			),

		)
	),

	array(
		'label'=>($show_full_label?_("Supplier's part packing"):_('Packing')),

		'show_title'=>true,
		'fields'=>array(


			array(
				'id'=>'Supplier_Part_Units_Per_Package',
				'edit'=>'smallint_unsigned',
				'value'=>($new?1:htmlspecialchars($object->get('Supplier Part Units Per Package'))),
				'formatted_value'=>($new?1:$object->get('Units Per Package')),
				'label'=>ucfirst($object->get_field_label('Supplier Part Units Per Package')),
				'required'=>true,
				'type'=>'value'
			),
			array(
				'id'=>'Supplier_Part_Packages_Per_Carton',
				'edit'=>'smallint_unsigned',
				'value'=>($new?1:htmlspecialchars($object->get('Supplier Part Packages Per Carton'))),
				'formatted_value'=>($new?1:$object->get('Packages Per Carton')),
				'label'=>ucfirst($object->get_field_label('Supplier Part Packages Per Carton')),
				'required'=>true,
				'type'=>'value'
			),



		)
	),

);




?>
