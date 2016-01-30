<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 18 November 2015 at 22:09:21 GMT, Sheffied UK
 Copyright (c) 2015, Inikoo

 Version 3

*/


include_once 'utils/invalid_messages.php';

$employee=$state['_object'];
$employee->get_user_data();


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

foreach (preg_split('/,/', $employee->get('Staff Position')) as $current_position_key) {
	if ( array_key_exists($current_position_key, $options_Staff_Position ) ) {

	$options_Staff_Position[$current_position_key]['selected']=true;
	}
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

foreach (preg_split('/,/', $employee->get('Staff Supervisor')) as $current_supervisor_key) {
	if ( array_key_exists($current_supervisor_key, $options_Staff_Supervisor ) ) {
		$options_Staff_Supervisor[$current_supervisor_key]['selected']=true;
	}
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

				'id'=>'Staff_Key',
				'value'=>$employee->get('Staff Key'),
				'label'=>ucfirst($employee->get_field_label('Staff Key')),
			),
			array(

				'id'=>'Staff_ID',
				'edit'=>'string',
				'value'=>$employee->get('Staff ID'),
				'label'=>ucfirst($employee->get_field_label('Staff ID')),
				'invalid_msg'=>get_invalid_message('string'),
				'server_validation'=>'check_for_duplicates',
				'required'=>false
			),
			array(

				'id'=>'Staff_Alias',
				'edit'=>'string',
				'value'=>$employee->get('Staff Alias'),
				'label'=>ucfirst($employee->get_field_label('Staff Alias')),
				'server_validation'=>'check_for_duplicates',
				'invalid_msg'=>get_invalid_message('string'),
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
				'required'=>true

			),
			
			
			array(

				'id'=>'Staff_Email',
				'edit'=>'email',
				'value'=>$employee->get('Staff Email'),
				'formatted_value'=>$employee->get('Email'),
				'label'=>ucfirst($employee->get_field_label('Staff Email')),
				'server_validation'=>'check_for_duplicates',
				'invalid_msg'=>get_invalid_message('email'),
			),
			array(

				'id'=>'Staff_Telephone',
				'edit'=>'telephone',
				'value'=>$employee->get('Staff Telephone'),
				'formatted_value'=>$employee->get('Telephone'),
				'label'=>ucfirst($employee->get_field_label('Staff Telephone')),
				'invalid_msg'=>get_invalid_message('telephone'),
			),
		

		)
	),
	array(
		'label'=>_('Contract'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(
			
			array(

				'id'=>'Staff_Currently_Working',
				'edit'=>'option',
				'value'=>$employee->get('Staff Currently Working'),
				'formatted_value'=>$employee->get('Currently Working'),
				'options'=>$options_yn,
				'label'=>ucfirst($employee->get_field_label('Staff Currently Working')),
			),
			array(

				'id'=>'Staff_Valid_From',
				'edit'=>'date',
				'time'=>'09:00:00',
				'value'=>$employee->get('Staff Valid From'),
				'formatted_value'=>$employee->get('Valid From'),
				'label'=>ucfirst($employee->get_field_label('Staff Valid From')),
				'invalid_msg'=>get_invalid_message('date'),
			),
			array(
				'render'=>($employee->get('Staff Currently Working')=='Yes'?false:true),
				'id'=>'Staff_Valid_To',
				'edit'=>'date',
				'time'=>'17:00:00',
				'value'=>$employee->get('Staff Valid To'),
				'formatted_value'=>$employee->get('Valid To'),
				'label'=>ucfirst($employee->get_field_label('Staff Valid To')),
				'invalid_msg'=>get_invalid_message('date'),
			),
			
			array(

				'id'=>'Staff_Job_Title',
				'edit'=>'string',
				'value'=>$employee->get('Staff Job Title'),
				'label'=>ucfirst($employee->get_field_label('Staff Job Title')),
			),
			array(
				//   'render'=>($employee->get('Staff Currently Working')=='Yes'?true:false),
				'id'=>'Staff_Supervisor',
				'edit'=>'radio_option',
				'value'=>$employee->get('Staff Supervisor'),
				'formatted_value'=>$employee->get('Supervisor'),
				'options'=>$options_Staff_Supervisor,
				'label'=>ucfirst($employee->get_field_label('Staff Supervisor')),
				'required'=>false

			),

		)
	),
	array(
		'label'=>_('System roles'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(
			
			array(
				'render'=>($employee->get('Staff Currently Working')=='Yes'?true:false),
				'id'=>'Staff_Position',
				'edit'=>'radio_option',
				'value'=>$employee->get('Staff Position'),
				'formatted_value'=>$employee->get('Position'),
				'options'=>$options_Staff_Position,
				'label'=>ucfirst($employee->get_field_label('Staff Position')),
			)

		)
	),

);

if ($employee->get('Staff User Key')) {


	$object_fields[]=array(
		'label'=>_('System user').' <i  onClick="change_view(\'account/user/'.$employee->get('Staff User Key').'\')" class="fa fa-link link"></i>',
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(

			array(

				'id'=>'Staff_User_Active',
				'edit'=>'option',
				'value'=>$employee->get('Staff User Active'),
				'formatted_value'=>$employee->get('User Active'),
				'options'=>$options_yn,
				'label'=>ucfirst($employee->get_field_label('Staff Active')),
			),
			array(

				'id'=>'Staff_User_Handle',
				'edit'=>'string',
				'value'=>$employee->get('Staff User Handle'),
				'formatted_value'=>$employee->get('User Handle'),
				'label'=>ucfirst($employee->get_field_label('Staff User Handle')),
				'server_validation'=>'check_for_duplicates'
			),

			array(
				'render'=>($employee->get('Staff User Active')=='Yes'?true:false),

				'id'=>'Staff_User_Password',
				'edit'=>'password',
				'value'=>'',
				'formatted_value'=>'******',
				'label'=>ucfirst($employee->get_field_label('Staff User Password')),
				'invalid_msg'=>get_invalid_message('password'),
			),
			array(
				'render'=>($employee->get('Staff User Active')=='Yes'?true:false),

				'id'=>'Staff_User_PIN',
				'edit'=>'pin',
				'value'=>'',
				'formatted_value'=>'****',
				'label'=>ucfirst($employee->get_field_label('Staff User PIN')),
				'invalid_msg'=>get_invalid_message('pin'),
			),



		)
	);

}else {
	$object_fields[]=array(
		'label'=>_('System user'),
		'show_title'=>true,
		'class'=>'edit_fields',
		'fields'=>array(
			array(

				'id'=>'new_user',
				'class'=>'new',
				'value'=>'',
				'label'=>_('Set up system user').' <i class="fa fa-plus new_button link"></i>',
				'reference'=>'employee/'.$employee->id.'/new/user'
			),

		)
	);

}

$smarty->assign('state', $state);
$smarty->assign('object_fields', $object_fields);

$html=$smarty->fetch('edit_object.tpl');

?>
