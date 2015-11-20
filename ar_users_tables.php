<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 1 October 2015 at 11:45:16 BST, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/ar_common.php';
require_once 'utils/table_functions.php';


if (!$user->can_view('users')) {
	echo json_encode(array('state'=>405, 'resp'=>'Forbidden'));
	exit;
}


if (!isset($_REQUEST['tipo'])) {
	$response=array('state'=>405, 'resp'=>'Non acceptable request (t)');
	echo json_encode($response);
	exit;
}


$tipo=$_REQUEST['tipo'];

switch ($tipo) {
case 'users':
	users(get_table_parameters(), $db, $user);
	break;
case 'staff':
	staff(get_table_parameters(), $db, $user);
	break;
case 'groups':
	groups(get_table_parameters(), $db, $user);
	break;
case 'login_history':
	login_history(get_table_parameters(), $db, $user);
	break;
case 'api_keys':
	api_keys(get_table_parameters(), $db, $user);
	break;
default:
	$response=array('state'=>405, 'resp'=>'Tipo not found '.$tipo);
	echo json_encode($response);
	exit;
	break;
}


function staff($_data, $db, $user) {
	global $db;
	$rtext_label='user';
	include_once 'prepare_table/init.php';

	$sql="select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
	$adata=array();

	foreach ($db->query($sql) as $data) {
		if ($data['User Active']=='Yes')
			$is_active=_('Yes');
		else
			$is_active=_('No');

		$groups=preg_split('/,/', $data['Groups']);
		$stores=preg_split('/,/', $data['Stores']);
		$warehouses=preg_split('/,/', $data['Warehouses']);
		$sites=preg_split('/,/', $data['Sites']);

		$adata[]=array(
			'id'=>(integer) $data['User Key'],
			'handle'=>$data['User Handle'],
			'name'=>$data['User Alias'],
			'active'=>$is_active,
			'logins'=>number($data['User Login Count']),
			'last_login'=>($data ['User Last Login']==''?'':strftime( "%e %b %Y %H:%M %Z", strtotime( $data ['User Last Login']." +00:00" ) )),
			'fail_logins'=>number($data['User Failed Login Count']),
			'fail_last_login'=>($data ['User Last Failed Login']==''?'':strftime( "%e %b %Y %H:%M %Z", strtotime( $data ['User Last Failed Login']." +00:00" ) )),

			'groups'=>$data['Groups'],
			'stores'=>$stores,
			'warehouses'=>$warehouses,
			'websites'=>$data['Sites'],
		);

	}

	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$adata,
			'rtext'=>$rtext,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'total_records'=> $total

		)
	);
	echo json_encode($response);
}


function login_history($_data, $db, $user) {
	global $db;
	$rtext_label='session';
	include_once 'prepare_table/init.php';

	$sql="select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";

	$adata=array();

	foreach ($db->query($sql) as $data) {

		$adata[]=array(
			'id'=>(integer) $data['User Log Key'],
			'user_key'=>(integer) $data['User Key'],
			'handle'=>$data['User Handle'],
			'user'=>$data['User Alias'],
			'parent_key'=>$data['User Parent Key'],
			'ip'=>$data['IP'],
			'login_date'=>strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Start Date'])),
			'logout_date'=>($data['Logout Date']!=''?strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Logout Date'])):''),
		);

	}

	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$adata,
			'rtext'=>$rtext,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'total_records'=> $total

		)
	);
	echo json_encode($response);
}


function users($_data, $db, $user) {
	global $db;
	$rtext_label='user category';
	include_once 'prepare_table/init.php';

	$sql="select $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";

	//print $sql;
	$base_data=array(
		'Staff'=>array('User Type'=>'Staff', 'active_users'=>0),
		'Warehouse'=>array('User Type'=>'Warehouse', 'active_users'=>0),
		'Administrator'=>array('User Type'=>'Administrator', 'active_users'=>0),
		'Supplier'=>array('User Type'=>'Supplier', 'active_users'=>0),
	);

	foreach ($db->query($sql) as $data) {

		$base_data[$data['User Type']]=$data;
	}

	foreach ($base_data as $key=>$data) {

		switch ($data['User Type']) {
		case 'Staff':
			$type=_('Staff');
			$request='account/users/staff';
			break;
		case 'Warehouse':
			$type=_('Warehouse');
			$request='account/users/warehouse';
			break;
		case 'Administrator':
			$type=_('Adminstrator');
			$request='account/users/root';
			break;
		case 'Supplier':
			$type=_('Supplier');
			$request='account/users/suppliers';
			break;
		default:
			$type=$data['User Type'];
			break;
		}

		$adata[]=array(
			'request'=>$request,
			'type'=>$type,
			'active_users'=>number($data['active_users']),
		);

	}

	$rtext=_('Users');

	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$adata,
			'rtext'=>$rtext,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'total_records'=> $total

		)
	);
	echo json_encode($response);
}


function api_keys($_data, $db, $user) {
	global $db;
	$rtext_label='api_key';
	include_once 'prepare_table/init.php';

	$sql="select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";

	$adata=array();

	foreach ($db->query($sql) as $data) {




		switch ($data['API Key Scope']) {
		case 'Timesheet':
			$scope=_('Timesheet');
			break;
		default:
			$scope=$data['API Key Scope'];
			break;
		}

		switch ($data['API Key Active']) {
		case 'Yes':
			$active=_('Active');
			break;
		case 'No':
			$active=_('Suspended');
			break;
		default:
			$active=$data['API Key Active'];
			break;
		}





		$adata[]=array(
			'id'=>(integer) $data['API Key Key'],
			'user_key'=>(integer) $data['API Key User Key'],
			'handle'=>$data['User Handle'],
			'scope'=>$scope,
			'active'=>$active,
			'formated_id'=>sprintf('%04d', $data['API Key Key']),
			'user'=>$data['User Alias'],
			'from'=>($data['API Key Valid From']!=''?strftime("%a %e %b %Y %H:%M %Z", strtotime($data['API Key Valid From'])):''),
			'to'=>($data['API Key Valid To']!=''?strftime("%a %e %b %Y %H:%M %Z", strtotime($data['API Key Valid To'])):''),
			'valid_ip'=>$data['API Key Allowed IP'],
			'ok_requests'=>number($data['API Key Successful Requests']),
			'fail_ip'=>number($data['API Key Successful Requests']),
			'fail_limit'=>number($data['API Key Successful Requests']),
			'last_request_date'=>($data['API Key Last Request Date']!=''?strftime("%a %e %b %Y %H:%M %Z", strtotime($data['API Key Last Request Date'])):''),

		);

	}

	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$adata,
			'rtext'=>$rtext,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'total_records'=> $total

		)
	);
	echo json_encode($response);
}


?>
