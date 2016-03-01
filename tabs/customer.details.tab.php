<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 13 September 2015 16:58:29 GMT+8, Kuala Lumpur
 Copyright (c) 2015, Inikoo

 Version 3

*/



include_once 'utils/country_functions.php';

include_once 'utils/invalid_messages.php';




$customer=$state['_object'];


$options_valid_tax_number=array(
	'Yes'=>_('Valid'), 'No'=>_('Not Valid'), 'Unknown'=>_('Unknown'), 'Auto'=>_('Check online'),
);

$company_field=array();

$object_fields=array(
	array(
		'label'=>_('Name, Ids'),
		'show_title'=>true,
		'fields'=>array(
			array(
				'id'=>'Customer_Company_Name',
				'edit'=>'string',
				'value'=>htmlspecialchars($customer->get('Customer Company Name')),
				'formatted_value'=>$customer->get('Company Name'),
				'label'=>ucfirst($customer->get_field_label('Customer Company Name')),
				'required'=>false
			),

			array(

				'id'=>'Customer_Main_Contact_Name',
				'edit'=>'string',
				'value'=>htmlspecialchars($customer->get('Customer Main Contact Name')),
				'formatted_value'=>$customer->get('Main Contact Name'),
				'label'=>ucfirst($customer->get_field_label('Customer Main Contact Name')),
				'required'=>true
			),
			array(
				'id'=>'Customer_Registration_Number',
				'edit'=>'string',
				'value'=>$customer->get('Customer Registration Number'),
				'formatted_value'=>$customer->get('Registration Number'),
				'label'=>ucfirst($customer->get_field_label('Customer Registration Number')),
				'required'=>false
			),
			array(
				'id'=>'Customer_Tax_Number',
				'edit'=>'string',
				'value'=>$customer->get('Customer Tax Number'),
				'formatted_value'=>$customer->get('Tax Number'),
				'label'=>ucfirst($customer->get_field_label('Customer Tax Number')),
				'required'=>false

			),
			array(
				'render'=>($customer->get('Customer Tax Number')==''?false:true),
				'id'=>'Customer_Tax_Number_Valid',
				'edit'=>'option',
				'options'=>$options_valid_tax_number,
				'value'=>$customer->get('Customer Tax Number Valid'),
				'formatted_value'=>$customer->get('Tax Number Valid'),
				'label'=>ucfirst($customer->get_field_label('Customer Tax Number Valid')),
			),

		)
	),
	array(
		'label'=>_('Email'),
		'show_title'=>false,
		'fields'=>array(

			array(
				'id'=>'Customer_Main_Plain_Email',
				'edit'=>'email',
				'value'=>$customer->get('Customer Main Plain Email'),
				'formatted_value'=>$customer->get('Main Plain Email'),
				'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates')),
				'label'=>ucfirst($customer->get_field_label('Customer Main Plain Email')),
				'invalid_msg'=>get_invalid_message('email'),
				'required'=>true
			), array(
				'id'=>'new_email',
				'render'=>false,
				'edit'=>'new_email',
				'value'=>'',
				'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates')),
				'formatted_value'=>'',
				'label'=>ucfirst($customer->get_field_label('Customer Other Email')),
				'invalid_msg'=>get_invalid_message('email'),

				'required'=>false
			),

			array(
				'id'=>'Customer_Other_Email',
				'render'=>false,
				'edit'=>'email',
				'value'=>'',
				'formatted_value'=>'',
				'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates')),
				'label'=>ucfirst($customer->get_field_label('Customer Other Email')).' <i onClick="set_this_as_main(this)" title="'._('Set as main email').'" class="fa fa-star-o very_discret button"></i>',
				'invalid_msg'=>get_invalid_message('email'),
				'required'=>false
			),

			array(
				'render'=>($customer->get('Customer Main Plain Email')==''?false:true),
				'id'=>'show_new_email',
				'class'=>'new',
				'value'=>'',
				'label'=>_('Add email').' <i class="fa fa-plus new_button button"></i>',
				'reference'=>''
			),

		)
	),

	array(
		'label'=>_('Telephones'),
		'show_title'=>false,
		'fields'=>array(

			array(
				'id'=>'Customer_Main_Plain_Mobile',
				'edit'=>'telephone',
				'value'=>$customer->get('Customer Main Plain Mobile'),
				'formatted_value'=>$customer->get('Main Plain Mobile'),
				'label'=>ucfirst($customer->get_field_label('Customer Main Plain Mobile')). ($customer->get('Customer Main Plain Mobile')!=''?($customer->get('Customer Preferred Contact Number')=='Mobile'?'':' <i onClick="set_this_as_main(this)" title="'._('Set as main contact number').'" class="fa fa-star-o discret button"></i>'):'')    ,
				'invalid_msg'=>get_invalid_message('telephone'),
				'required'=>false
			),
			array(



				'id'=>'Customer_Main_Plain_Telephone',
				'edit'=>'telephone',
				'value'=>$customer->get('Customer Main Plain Telephone'),
				'formatted_value'=>$customer->get('Main Plain Telephone'),
				'label'=>ucfirst($customer->get_field_label('Customer Main Plain Telephone')).($customer->get('Customer Main Plain Telephone')!=''?($customer->get('Customer Preferred Contact Number')=='Telephone'?'':' <i onClick="set_this_as_main(this)" title="'._('Set as main contact number').'" class="fa fa-star-o discret button"></i>'):'')    ,
				'invalid_msg'=>get_invalid_message('telephone'),
				'required'=>false

			), array(
				'id'=>'new_telephone',
				'render'=>false,
				'edit'=>'new_telephone',
				'value'=>'',
				'formatted_value'=>'',
				'label'=>ucfirst($customer->get_field_label('Customer Other Telephone')).' <i onClick="set_this_as_main(this)" title="'._('Set as main telephone').'" class="fa fa-star-o very_discret button"></i>',
				'required'=>false
			),

			array(
				'id'=>'Customer_Other_Telephone',
				'render'=>false,
				'edit'=>'telephone',
				'value'=>'',
				'formatted_value'=>'',
				'label'=>ucfirst($customer->get_field_label('Customer Other Telephone')).' <i onClick="set_this_as_main(this)" title="'._('Set as main telephone').'" class="fa fa-star-o very_discret button"></i>',
				'required'=>false
			),

			array(
				'render'=>($customer->get('Customer Main Plain Telephone')==''?false:true),
				'id'=>'show_new_telephone',
				'class'=>'new',
				'value'=>'',
				'label'=>_('Add telephone').' <i class="fa fa-plus new_button button"></i>',
				'required'=>false,
				'reference'=>''
			),

			array(
				'id'=>'Customer_Main_Plain_FAX',
				'edit'=>'telephone',
				'value'=>$customer->get('Customer Main Plain FAX'),
				'formatted_value'=>$customer->get('Main Plain FAX'),
				'label'=>ucfirst($customer->get_field_label('Customer Main Plain FAX')),
				'invalid_msg'=>get_invalid_message('telephone'),
				'required'=>false
			),

		)
	),

	array(
		'label'=>_('Address'),
		'show_title'=>false,
		'fields'=>array(

			array(
				'id'=>'Customer_Contact_Address',
				'edit'=>'address',
				'value'=>htmlspecialchars($customer->get('Customer Contact Address')),
				'formatted_value'=>$customer->get('Contact Address'),
				'label'=>ucfirst($customer->get_field_label('Customer Contact Address')),
				'invalid_msg'=>get_invalid_message('address'),
				'required'=>false
			),


			array(
				'id'=>'Customer_Invoice_Address',
				'edit'=>'address',
				'value'=>htmlspecialchars($customer->get('Customer Invoice Address')),
				'formatted_value'=>$customer->get('Invoice Address'),
				'label'=>ucfirst($customer->get_field_label('Customer Invoice Address')),
				'required'=>false
			),
			array(
				'id'=>'Customer_Delivery_Address',
				'edit'=>'address',
				'value'=>htmlspecialchars($customer->get('Customer Delivery Address')),
				'formatted_value'=>$customer->get('Delivery Address'),
				'label'=>ucfirst($customer->get_field_label('Customer Delivery Address')),
				'invalid_msg'=>get_invalid_message('address'),
				'required'=>false
			),
			array(
				'id'=>'Customer_Other_Delivery_Address',
				'render'=>false,
				'edit'=>'address_to_clone',
				'field_type'=>'other_delivery_address',
				'value'=>'',
				'formatted_value'=>'',
				'invalid_msg'=>get_invalid_message('address'),
				'label'=>'',
				'required'=>false
			),

			array(
				'id'=>'new_delivery_address',
				'render'=>false,
				'edit'=>'new_delivery_address',
				'value'=>'',
				'formatted_value'=>'',
				'label'=>_('New delivery address'),
				'required'=>false
			),
			array(
				'id'=>'show_new_delivery_address',
				'class'=>'new',
				'value'=>'',
				'label'=>_('Add delivery address').' <i class="fa fa-plus new_button button"></i>',
				'reference'=>''
			),

		)
	),

array(
		'label'=>_('Operations'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(
			array(

				'id'=>'delete_customer',
				'class'=>'new',
				'value'=>'',
				'label'=>'<i class="fa fa-lock button" style="margin-right:20px"></i> <span class="disabled">'._('Delete customer').' <i class="fa fa-trash new_button link"></i></span>',
				'reference'=>''
			),

		)
		
	),

);

$other_emails=$customer->get_other_emails_data();
if (count($other_emails)>0) {
	$other_emails_fields=array();
	foreach ($other_emails as $other_email_data_key=>$other_email_data) {
		$other_emails_fields[]=array(
			'id'=>'Customer_Other_Email_'.$other_email_data_key,
			'edit'=>'email',
			'value'=>$other_email_data['email'],
			'formatted_value'=>$other_email_data['email'],
			'server_validation'=>json_encode(array('tipo'=>'check_for_duplicates')),
			'label'=>ucfirst($customer->get_field_label('Customer Other Email')).' <i onClick="set_this_as_main(this)" title="'._('Set as main email').'" class="fa fa-star-o very_discret button"></i>',
			'required'=>false
		);
	}
	array_splice( $object_fields[1]['fields'], 1, 0, $other_emails_fields);
}

$other_telephones=$customer->get_other_telephones_data();
if (count($other_telephones)>0) {
	$other_telephones_fields=array();
	foreach ($other_telephones as $other_telephone_data_key=>$other_telephone_data) {
		$other_telephones_fields[]=array(
			'id'=>'Customer_Other_Telephone_'.$other_telephone_data_key,
			'edit'=>'telephone',
			'value'=>$other_telephone_data['telephone'],
			'formatted_value'=>$other_telephone_data['formatted_telephone'],
			'label'=>ucfirst($customer->get_field_label('Customer Other Telephone')).' <i onClick="set_this_as_main(this)" title="'._('Set as main telephone').'" class="fa fa-star-o very_discret button"></i>',
			'required'=>false
		);
	}
	array_splice( $object_fields[2]['fields'], 2, 0, $other_telephones_fields);
}


$other_delivery_addresses=$customer->get_other_delivery_addresses_data();
if (count($other_delivery_addresses)>0) {
	$other_delivery_addresses_fields=array();
	foreach ($other_delivery_addresses as $other_telephone_data_key=>$other_telephone_data) {
		$other_delivery_addresses_fields[]=array(
			'id'=>'Customer_Other_Delivery_Address_'.$other_telephone_data_key,
			'edit'=>'address',
			'render'=>false,
			'value'=>htmlspecialchars($other_telephone_data['value']),
			'field_type'=>'other_delivery_address',
			'formatted_value'=>$other_telephone_data['formatted_value'],
			'invalid_msg'=>get_invalid_message('address'),
			'label'=>'',
			'required'=>false
		);
	}




}

$other_delivery_addresses_fields[]=array(
	'id'=>'other_delivery_addresses',
	'render'=>(count($other_delivery_addresses)>0?true:false),
	'class'=>'directory',
	'value'=>'',
	'label'=>_('Other delivery addresses'),
	'formatted_value'=>$smarty->fetch('delivery_addresses_directory.tpl'),
	'reference'=>''
);

array_splice( $object_fields[3]['fields'], 3, 0, $other_delivery_addresses_fields);


$store=new Store($state['_object']->get('Customer Store Key'));

$smarty->assign('default_country', $store->get('Store Home Country Code 2 Alpha'));
$smarty->assign('preferred_countries', '"'.join('", "', preferred_countries($store->get('Store Home Country Code 2 Alpha'))).'"');

$smarty->assign('object', $state['_object']);
$smarty->assign('key', $state['key']);

$smarty->assign('object_fields', $object_fields);
$smarty->assign('state', $state);
$smarty->assign('js_code', file_get_contents('js/customer.details.js'));

//print_r($customer->get('Customer Contact Address'));

$html=$smarty->fetch('edit_object.tpl');

?>
