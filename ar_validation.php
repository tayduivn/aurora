<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created:8 November 2015 at 13:37:41 GMT, Sheffield UK
 Copyright (c) 2015, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/ar_common.php';
require_once 'utils/object_functions.php';



if (!isset($_REQUEST['tipo'])) {
	$response=array('state'=>405, 'resp'=>'Non acceptable request (t)');
	echo json_encode($response);
	exit;
}


$tipo=$_REQUEST['tipo'];

switch ($tipo) {
case 'check_for_duplicates':

	$data=prepare_values($_REQUEST, array(
			'object'=>array('type'=>'string'),
			'parent'=>array('type'=>'string'),
			'parent_key'=>array('type'=>'string'),
			'key'=>array('type'=>'string'),
			'field'=>array('type'=>'string'),
			'value'=>array('type'=>'string'),

		));

	check_for_duplicates($data, $db, $user, $account);
	break;

default:
	$response=array('state'=>405, 'resp'=>'Tipo not found '.$tipo);
	echo json_encode($response);
	exit;
	break;
}


function check_for_duplicates($data, $db, $user, $account) {



	$field=preg_replace('/_/', ' ', $data['field']);


	$validation_sql_queries=array();

	switch ($data['object']) {
	case 'User':
		switch ($field) {
		case 'Staff User Handle':
			$invalid_msg=_('Another user is using this login');
			$sql=sprintf("select `User Key`as `key` ,`User Handle` as field from `User Dimension` where `User Type`='Staff' and `User Handle`=%s",
				prepare_mysql($data['value'])
			);

			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);
			break;


		default:

			break;
		}
		break;

		break;
	case 'Contractor':
	
		switch ($field) {
		case 'Staff ID':
			$invalid_msg=_('Another contractor is using this payroll Id');
			$sql=sprintf("select `Staff Key` as `key` ,`Staff Alias` as field from `Staff Dimension` where `Staff ID`=%s",
			prepare_mysql($data['value'])
			);
						$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);

			break;
		case 'Staff Alias':
			$invalid_msg=_('Another contractor is using this code');
			$sql=sprintf("select `Staff Key` as `key` ,`Staff Alias` as field from `Staff Dimension` where `Staff Alias`=%s",
			prepare_mysql($data['value'])
			);
						$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);

			break;
		case 'Staff User Handle':
			$invalid_msg=_('Another user is using this login handle');
			$sql=sprintf("select `User Key`as `key` ,`User Handle` as field from `User Dimension` where `User Type`='Staff' and `User Handle`=%s",
				prepare_mysql($data['value'])
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);
			break;
		default:

			break;
		}


		break;	
		
	case 'Staff':
		switch ($field) {
		case 'Staff ID':
			$invalid_msg=_('Another employee is using this payroll Id');
			break;
		case 'Staff Alias':
			$invalid_msg=_('Another employee is using this code');
			break;
		case 'Staff User Handle':
			$invalid_msg=_('Another user is using this login handle');
			$sql=sprintf("select `User Key`as `key` ,`User Handle` as field from `User Dimension` where `User Type`='Staff' and `User Handle`=%s",
				prepare_mysql($data['value'])
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);
			break;
		default:

			break;
		}


		break;
	case 'Store':

		switch ($field) {
		case 'Store Code':
			$invalid_msg=_('Another store is using this code');
			break;

		default:

			break;
		}


		break;

	case 'Category':

		switch ($field) {
		case 'Category Code':
			$invalid_msg=_('Another category is using this code');
			break;

		default:

			break;
		}


		break;

	case 'Customer':



		switch ($field) {
		case 'Customer Main Plain Email':
			$invalid_msg=_('Another customer have this email');
			$sql=sprintf("select `Customer Key` as `key` ,`Customer Main Plain Email` as field from `Customer Dimension` where `Customer Main Plain Email`=%s and `Customer Store Key`=%d ",
				prepare_mysql($data['value']),
				$data['parent_key']
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);

			$invalid_msg=_('Another customer have this email');
			$sql=sprintf("select `Customer Other Email Customer Key` as `key` ,`Customer Other Email Email` as field from `Customer Other Email Dimension` where `Customer Other Email Email`=%s and `Customer Other Email Store Key`=%d  and `Customer Other Email Customer Key`!=%d ",
				prepare_mysql($data['value']),
				$data['parent_key'],
				$data['key']
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);


			$invalid_msg=_('Email already set up in this customer');
			$sql=sprintf("select `Customer Other Email Customer Key` as `key` ,`Customer Other Email Email` as field from `Customer Other Email Dimension` where `Customer Other Email Email`=%s and  `Customer Other Email Customer Key`=%d ",
				prepare_mysql($data['value']),
				$data['key']
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);


			break;
		case 'new email':
			$invalid_msg=_('Another customer have this email');
			$sql=sprintf("select `Customer Key` as `key` ,`Customer Main Plain Email` as field from `Customer Dimension` where `Customer Main Plain Email`=%s and `Customer Store Key`=%d ",
				prepare_mysql($data['value']),
				$data['parent_key']
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);

			$invalid_msg=_('Another customer have this email');
			$sql=sprintf("select `Customer Other Email Customer Key` as `key` ,`Customer Other Email Email` as field from `Customer Other Email Dimension` where `Customer Other Email Email`=%s and `Customer Other Email Store Key`=%d  and `Customer Other Email Customer Key`!=%d ",
				prepare_mysql($data['value']),
				$data['parent_key'],
				$data['key']
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);

			$invalid_msg=_('Email already set up in this customer');
			$sql=sprintf("select `Customer Other Email Customer Key` as `key` ,`Customer Other Email Email` as field from `Customer Other Email Dimension` where `Customer Other Email Email`=%s and  `Customer Other Email Customer Key`=%d ",
				prepare_mysql($data['value']),
				$data['key']
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);


			$invalid_msg=_('Email already set up in this customer');
			$sql=sprintf("select `Customer Key` as `key` ,`Customer Main Plain Email` as field from `Customer Dimension` where `Customer Key`=%d ",
				prepare_mysql($data['value']),
				$data['key']
			);
			$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);



		default:

			if (preg_match('/^Customer Other Email (\d+)/i', $field, $matches)) {
				$customer_email_key=$matches[1];



				$invalid_msg=_('Email already set up in this customer');
				$sql=sprintf("select `Customer Key` as `key` ,`Customer Main Plain Email` as field from `Customer Dimension` where `Customer Main Plain Email`=%s  and `Customer Key`=%d ",
					prepare_mysql($data['value']),
					$data['key']
				);
				$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);


				$invalid_msg=_('Email already set up in this customer');
				$sql=sprintf("select `Customer Other Email Customer Key` as `key` ,`Customer Other Email Email` as field from `Customer Other Email Dimension` where `Customer Other Email Email`=%s and  `Customer Other Email Customer Key`=%d  and  `Customer Other Email Key`!=%d  ",
					prepare_mysql($data['value']),
					$data['key'],
					$customer_email_key
				);
				$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);



				$invalid_msg=_('Another customer have this email');
				$sql=sprintf("select `Customer Key` as `key` ,`Customer Main Plain Email` as field from `Customer Dimension` where `Customer Main Plain Email`=%s and `Customer Store Key`=%d ",
					prepare_mysql($data['value']),
					$data['parent_key']
				);
				$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);



				$invalid_msg=_('Another customer have this email');
				$sql=sprintf("select `Customer Other Email Customer Key` as `key` ,`Customer Other Email Email` as field from `Customer Other Email Dimension` where `Customer Other Email Email`=%s and `Customer Other Email Store Key`=%d  ",
					prepare_mysql($data['value']),
					$data['parent_key']
				);
				$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);






			}
		}


		break;

	default:


		break;
	}





	if (count($validation_sql_queries)==0) {
		switch ($data['parent']) {
		case 'store':
			$parent_where=sprintf(' and `%s Store Key`=%d ', $data['object'], $data['parent_key']);
			break;
		case 'category':
			$parent_where=sprintf(' and `%s Parent Key`=%d ', $data['object'], $data['parent_key']);
			break;
		default:
			$parent_where='';
		}

		$sql=sprintf('select `%s Key` as `key` ,`%s` as field from `%s Dimension` where `%s`=%s %s ',
			addslashes(preg_replace('/_/', ' ', $data['object'])),
			addslashes($field),

			addslashes(preg_replace('/_/', ' ', $data['object'])),
			addslashes($field),
			prepare_mysql($data['value']),
			$parent_where

		);




		if (!isset($invalid_msg)) {
			$invalid_msg=_('Another object with same value found');
		}
		$validation_sql_queries[]=array('sql'=>$sql, 'invalid_msg'=>$invalid_msg);
	}

	$validation='valid';
	$msg='';


	foreach ($validation_sql_queries as $validation_query) {
		$sql=$validation_query['sql'];
		$invalid_msg=$validation_query['invalid_msg'];

		//print "$sql\n";

		if ($result=$db->query($sql)) {
			if ($row = $result->fetch()) {
				$validation='invalid';
				$msg=$invalid_msg;
				break;
			}
		}else {
			print_r($error_info=$db->errorInfo());
			print "$sql";
			exit;
		}


	}

	$response=array(
		'state'=>200,
		'validation'=>$validation,
		'msg'=>$msg,
	);
	echo json_encode($response);



}




?>
