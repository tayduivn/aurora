<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 18 November 2015 at 23:15:45 GMT Sheffied UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


include_once 'utils/invalid_messages.php';
include_once 'class.Staff.php';


$employee=new Staff(0);
$employee->data['Staff Type']='Contractor';

$options_yn=array(
	'Yes'=>_('Yes'), 'No'=>_('No')
);

$options_Staff_Position=array();
$sql=sprintf('select `Company Position Key`,`Company Position Code`,`Company Position Title` from `Company Position Dimension`  ');
foreach ($db->query($sql) as $row) {
	$options_Staff_Position[$row['Company Position Key']]=array(
		'label'=>$row['Company Position Title'],
		'selected'=>false
	);
}



$options_Staff_Supervisor=array();
$sql=sprintf('select `Staff Name`,`Staff Key`,`Staff Alias` from `Staff Dimension` where `Staff Currently Working`="Yes" ');
foreach ($db->query($sql) as $row) {
	$options_Staff_Supervisor[$row['Staff Key']]=array(
		'label'=>$row['Staff Alias'],

		'label2'=>$row['Staff Name'].' ('.sprintf('%03d', $row['Staff Key']).')',
		'selected'=>false
	);
}



asort($options_Staff_Position);
asort($options_Staff_Supervisor);


asort($options_yn);

$object_fields=array(
	array(
		'label'=>_('Id'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(



			array(

				'id'=>'Staff_ID',
				'edit'=>'string',
				'value'=>'',
				'label'=>ucfirst($employee->get_field_label('Staff ID')),
				'invalid_msg'=>get_invalid_message('smallint_unsigned'),
				'server_validation'=>'check_for_duplicates',
				'required'=>false,
				'type'=>'value'
			),
			array(

				'id'=>'Staff_Alias',
				'edit'=>'string',
				'value'=>$employee->get('Staff Alias'),
				'label'=>ucfirst($employee->get_field_label('Staff Alias')),
				'server_validation'=>'check_for_duplicates',
				'invalid_msg'=>get_invalid_message('string'),
				'type'=>'value'
			),


		)
	),

	array(
		'label'=>_('Personal information'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(

			array(

				'id'=>'Staff_Name',
				'edit'=>'string',
				'value'=>$employee->get('Staff Name'),
				'label'=>ucfirst($employee->get_field_label('Staff Name')),
				'invalid_msg'=>get_invalid_message('string'),
				'required'=>true,
				'type'=>'value'

			),
			
			
			array(

				'id'=>'Staff_Email',
				'edit'=>'email',
				'value'=>$employee->get('Staff Email'),
				'formatted_value'=>$employee->get('Email'),
				'label'=>ucfirst($employee->get_field_label('Staff Email')),
				'server_validation'=>'check_for_duplicates',
				'invalid_msg'=>get_invalid_message('email'),
				'required'=>false,
				'type'=>'value'
			),
			array(

				'id'=>'Staff_Telephone',
				'edit'=>'telephone',
				'value'=>$employee->get('Staff Telephone'),
				'formatted_value'=>$employee->get('Telephone'),
				'label'=>ucfirst($employee->get_field_label('Staff Telephone')),
				'invalid_msg'=>get_invalid_message('telephone'),
				'required'=>false,
				'type'=>'value'
			),
		

		)
	),
	array(
		'label'=>_('Contract'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(
			

			array(
				'render'=>false,
				'edit'=>'option',
				'id'=>'Staff_Currently_Working',
				'value'=>'Yes',
				'formatted_value'=>_('Yes'),
				'options'=>$options_yn,
				'label'=>ucfirst($employee->get_field_label('Staff Currently Working')),
				'type'=>'value'
			),
			array(
				'render'=>false,
				'edit'=>'hidden',
				'id'=>'Staff_Valid_From',

				'time'=>'09:00:00',
				'value'=>$employee->get('Staff Valid From'),
				'formatted_value'=>$employee->get('Valid From'),
				'label'=>ucfirst($employee->get_field_label('Staff Valid From')),
				'invalid_msg'=>get_invalid_message('date'),
				'type'=>'value',
				'required'=>false,
			),
			
			array(

				'id'=>'Staff_Job_Title',
				'edit'=>'string',
				'value'=>$employee->get('Staff Job Title'),
				'label'=>ucfirst($employee->get_field_label('Staff Job Title')),
				'required'=>false,
				'type'=>'value'
			),
			array(
				//   'render'=>($employee->get('Staff Currently Working')=='Yes'?true:false),
				'id'=>'Staff_Supervisor',
				'edit'=>'radio_option',
				'value'=>$employee->get('Staff Supervisor'),
				'formatted_value'=>$employee->get('Supervisor'),
				'options'=>$options_Staff_Supervisor,
				'label'=>ucfirst($employee->get_field_label('Staff Supervisor')),
				'required'=>false,
				'type'=>'value'

			),

		)
	),
	
	array(
		'label'=>_('System roles'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(
			
			array(
				'id'=>'Staff_Position',
				'edit'=>'radio_option',
				'value'=>'',
				'formatted_value'=>'',
				'options'=>$options_Staff_Position,
				'label'=>ucfirst($employee->get_field_label('Staff Position')),
				'required'=>false,
				'type'=>'value'
			)

		)
	),

);



$object_fields[]=array(
	'label'=>_('System user'),
	'show_title'=>true,
	'class'=>'edit_fields',
	'fields'=>array(


		array(

			'id'=>'add_new_user',
			'class'=>'',
			'value'=>'',
			'label'=>_('Set up system user').' <i onClick="show_user_fields()" class="fa fa-plus new_button link"></i>',
			'required'=>false,
			'type'=>'util'
		),

		array(
			'render'=>false,
			'id'=>'dont_add_new_user',
			'class'=>'',
			'value'=>'',
			'label'=>_("Don't set up system user").' <i onClick="hide_user_fields()" class="fa fa-minus new_button link"></i>',
			'required'=>false,
			'type'=>'util'
		),


		array(
			'render'=>false,
			'id'=>'Staff_User_Active',
			'edit'=>'option',
			'value'=>'Yes',
			'formatted_value'=>_('Yes'),
			'options'=>$options_yn,
			'label'=>ucfirst($employee->get_field_label('Staff User Active')),
			'type'=>'user_value'
		),
		array(
			'render'=>false,
			'id'=>'Staff_User_Handle',
			'edit'=>'string',
			'value'=>$employee->get('Staff User Handle'),
			'formatted_value'=>$employee->get('User Handle'),
			'label'=>ucfirst($employee->get_field_label('Staff User Handle')),
			'server_validation'=>'check_for_duplicates',
			'type'=>'user_value'

		),

		array(
			'render'=>false,

			'id'=>'Staff_User_Password',
			'edit'=>'password',
			'value'=>'',
			'formatted_value'=>'******',
			'label'=>ucfirst($employee->get_field_label('Staff User Password')),
			'invalid_msg'=>get_invalid_message('password'),
			'type'=>'user_value'

		),
		array(
			'render'=>false,
			'id'=>'Staff_User_PIN',
			'edit'=>'pin',
			'value'=>'',
			'formatted_value'=>'****',
			'label'=>ucfirst($employee->get_field_label('Staff User PIN')),
			'invalid_msg'=>get_invalid_message('pin'),
			'type'=>'user_value'

		),



	)
);



$smarty->assign('state', $state);
$smarty->assign('object', $employee);


$smarty->assign('object_name','Contractor');


$smarty->assign('object_fields', $object_fields);
$smarty->assign('new_object_label', _('View new contractor'));
$smarty->assign('new_object_request','contractor/__key__');




$smarty->assign('js_code', file_get_contents('js/employee.new.js'));

$html=$smarty->fetch('new_object.tpl');

?>
