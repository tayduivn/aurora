<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 2 November 2015 at 11:32:44 CET, Lido (Venice) , Italy

 Copyright (c) 2015, Inikoo

 Version 3.0
*/




function get_payment_service_provider_navigation($data) {

	global $smarty;

	$object=$data['_object'];
	$left_buttons=array();
	$right_buttons=array();

	if ($data['parent']) {

		switch ($data['parent']) {
		case 'account':
			$tab='payment_service_providers';
			$_section='account';
			break;


		}


		$number_results=$_SESSION['table_state'][$tab]['nr'];
		$start_from=0;
		$order=$_SESSION['table_state'][$tab]['o'];
		$order_direction=($_SESSION['table_state'][$tab]['od']==1 ?'desc':'');
		$f_value=$_SESSION['table_state'][$tab]['f_value'];
		$parameters=$_SESSION['table_state'][$tab];





		include_once 'prepare_table/'.$tab.'.ptble.php';

		$_order_field=$order;
		$order=preg_replace('/^.*\.`/', '', $order);
		$order=preg_replace('/^`/', '', $order);
		$order=preg_replace('/`$/', '', $order);
		$_order_field_value=$object->get($order);


		$prev_title='';
		$next_title='';
		$prev_key=0;
		$next_key=0;
		$sql=trim($sql_totals." $wheref");

		$res2=mysql_query($sql);
		if ($row2=mysql_fetch_assoc($res2) and $row2['num']>1 ) {

			$sql=sprintf("select `Payment Service Provider Name` object_name,PSP.`Payment Service Provider Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND PSP.`Payment Service Provider Key` < %d))  order by $_order_field desc , PSP.`Payment Service Provider Key` desc limit 1",

				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$object->id
			);


			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$prev_key=$row['object_key'];
				$prev_title=_("Payment option").' '.$row['object_name'].' ('.$row['object_key'].')';

			}

			$sql=sprintf("select `Payment Service Provider Name` object_name,PSP.`Payment Service Provider Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND PSP.`Payment Service Provider Key` > %d))  order by $_order_field   , PSP.`Payment Service Provider Key`  limit 1",
				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$object->id
			);


			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$next_key=$row['object_key'];
				$next_title=_("Payment option").' '.$row['object_name'].' ('.$row['object_key'].')';

			}


			if ($order_direction=='desc') {
				$_tmp1=$prev_key;
				$_tmp2=$prev_title;
				$prev_key=$next_key;
				$prev_title=$next_title;
				$next_key=$_tmp1;
				$next_title=$_tmp2;
			}


		}

		if ($data['parent']=='account') {



			$up_button=array('icon'=>'arrow-up', 'title'=>_("Account payment options"), 'reference'=>'account');

			if ($prev_key) {
				$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'account/payment_service_provider/'.$prev_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'', 'url'=>'');

			}
			$left_buttons[]=$up_button;


			if ($next_key) {
				$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'account/payment_service_provider/'.$next_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

			}






		}
	}
	else {
		exit('');

	}

	$sections=get_sections('account', '');


	$sections['account']['selected']=true;



	$title=_('Payment option').' <span class="id">'.$data['_object']->get('Payment Service Provider Name').'</span>';


	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,
		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search account'))

	);
	$smarty->assign('_content', $_content);


	$html=$smarty->fetch('navigation.tpl');

	return $html;

}


function get_payment_account_navigation($data) {

	global $smarty;

	$object=$data['_object'];
	$left_buttons=array();
	$right_buttons=array();

	if ($data['parent']) {

		switch ($data['parent']) {
		case 'account':
			$tab='payment_service_providers';
			$_section='account';
			break;
		case 'payment_service_provider':
			$tab='payment_service_provider.accounts';
			$_section='account';
			break;


		}


		$number_results=$_SESSION['table_state'][$tab]['nr'];
		$start_from=0;
		$order=$_SESSION['table_state'][$tab]['o'];
		$order_direction=($_SESSION['table_state'][$tab]['od']==1 ?'desc':'');
		$f_value=$_SESSION['table_state'][$tab]['f_value'];
		$parameters=$_SESSION['table_state'][$tab];


		//print_r($_SESSION['table_state'][$tab]);


		include_once 'prepare_table/'.$tab.'.ptble.php';


		$_order_field=$order;
		$order=preg_replace('/^.*\.`/', '', $order);
		$order=preg_replace('/^`/', '', $order);
		$order=preg_replace('/`$/', '', $order);


		$_order_field_value=$object->get($order);


		$prev_title='';
		$next_title='';
		$prev_key=0;
		$next_key=0;
		$sql=trim($sql_totals." $wheref");

		$res2=mysql_query($sql);
		if ($row2=mysql_fetch_assoc($res2) and $row2['num']>1 ) {

			$sql=sprintf("select `Payment Account Name` object_name,PA.`Payment Account Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND PA.`Payment Account Key` < %d))  order by $_order_field desc , PA.`Payment Account Key` desc limit 1",

				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$object->id
			);


			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$prev_key=$row['object_key'];
				$prev_title=_("Payment option").' '.$row['object_name'].' ('.$row['object_key'].')';

			}

			$sql=sprintf("select `Payment Account Name` object_name,PA.`Payment Account Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND PA.`Payment Account Key` > %d))  order by $_order_field   , PA.`Payment Account Key`  limit 1",
				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$object->id
			);


			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$next_key=$row['object_key'];
				$next_title=_("Payment option").' '.$row['object_name'].' ('.$row['object_key'].')';

			}


			if ($order_direction=='desc') {
				$_tmp1=$prev_key;
				$_tmp2=$prev_title;
				$prev_key=$next_key;
				$prev_title=$next_title;
				$next_key=$_tmp1;
				$next_title=$_tmp2;
			}


		}

		if ($data['parent']=='account') {



			$up_button=array('icon'=>'arrow-up', 'title'=>_("Account payment options"), 'reference'=>'account');

			if ($prev_key) {
				$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'account/payment_service_provider/'.$prev_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'', 'url'=>'');

			}
			$left_buttons[]=$up_button;


			if ($next_key) {
				$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'account/payment_service_provider/'.$next_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

			}






		}
		elseif ($data['parent']=='payment_service_provider') {
			include_once 'class.Payment_Service_Provider.php';
			$psp=new Payment_Service_Provider($data['parent_key']);

			$up_button=array('icon'=>'arrow-up', 'title'=>_("Payment option").' '.$psp->get('Payment Service Provider Name'), 'reference'=>'account/payment_service_provider/'.$data['parent_key']);

			if ($prev_key) {
				$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'payment_service_provider/'.$data['parent_key'].'/payment_account/'.$prev_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'', 'url'=>'');

			}
			$left_buttons[]=$up_button;


			if ($next_key) {
				$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'payment_service_provider/'.$data['parent_key'].'/payment_account/'.$next_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

			}






		}
	}
	else {
		exit('');

	}

	$sections=get_sections('account', '');


	$sections['account']['selected']=true;



	$title=_('Payment account').' <span class="id">'.$data['_object']->get('Payment Account Name').'</span>';


	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,
		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search account'))

	);
	$smarty->assign('_content', $_content);


	$html=$smarty->fetch('navigation.tpl');

	return $html;

}


function get_payment_navigation($data) {

	global $smarty,$user;

	$object=$data['_object'];
	$left_buttons=array();
	$right_buttons=array();

	if ($data['parent']) {

		switch ($data['parent']) {
		case 'account':
			$tab='payments';
			$_section='account';
			break;
		case 'payment_service_provider':
			$tab='payment_service_provider.payments';
			$_section='account';
			break;
		case 'payment_account':
			$tab='payment_account.payments';
			$_section='account';
			break;

		}




		if (isset($_SESSION['table_state'][$tab])) {
			$number_results=$_SESSION['table_state'][$tab]['nr'];
			$start_from=0;
			$order=$_SESSION['table_state'][$tab]['o'];
			$order_direction=($_SESSION['table_state'][$tab]['od']==1 ?'desc':'');
			$f_value=$_SESSION['table_state'][$tab]['f_value'];
			$parameters=$_SESSION['table_state'][$tab];
		}else {

			$default=$user->get_tab_defaults($tab);

			$number_results=$default['rpp'];
			$start_from=0;
			$order=$default['sort_key'];
			$order_direction=($default['sort_order']==1 ?'desc':'');
			$f_value='';
			$parameters=$default;
			$parameters['parent']=$data['parent'];
			$parameters['parent_key']=$data['parent_key'];

		}









		include_once 'prepare_table/'.$tab.'.ptble.php';


		$_order_field=$order;
		$order=preg_replace('/^.*\.`/', '', $order);
		$order=preg_replace('/^`/', '', $order);
		$order=preg_replace('/`$/', '', $order);


		$_order_field_value=$object->get($order);


		$prev_title='';
		$next_title='';
		$prev_key=0;
		$next_key=0;
		$sql=trim($sql_totals." $wheref");
		$res2=mysql_query($sql);
		
		if ($row2=mysql_fetch_assoc($res2) and $row2['num']>1 ) {

			$sql=sprintf("select `Payment Key` object_name,P.`Payment Key` as object_key from $table   $where $wheref
	                and ($_order_field < %s OR ($_order_field = %s AND P.`Payment Key` < %d))  order by $_order_field desc , P.`Payment Key` desc limit 1",

				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$object->id
			);

			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$prev_key=$row['object_key'];
				$prev_title=_("Payment option").' '.$row['object_name'].' ('.$row['object_key'].')';

			}

			$sql=sprintf("select `Payment Key` object_name,P.`Payment Key` as object_key from $table   $where $wheref
	                and ($_order_field  > %s OR ($_order_field  = %s AND P.`Payment Key` > %d))  order by $_order_field   , P.`Payment Key`  limit 1",
				prepare_mysql($_order_field_value),
				prepare_mysql($_order_field_value),
				$object->id
			);


			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$next_key=$row['object_key'];
				$next_title=_("Payment option").' '.$row['object_name'].' ('.$row['object_key'].')';

			}


			if ($order_direction=='desc') {
				$_tmp1=$prev_key;
				$_tmp2=$prev_title;
				$prev_key=$next_key;
				$prev_title=$next_title;
				$next_key=$_tmp1;
				$next_title=$_tmp2;
			}


		}

		if ($data['parent']=='account') {



			$up_button=array('icon'=>'arrow-up', 'title'=>_("Account payments"), 'reference'=>'account');

			if ($prev_key) {
				$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'account/payment/'.$prev_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'', 'url'=>'');

			}
			$left_buttons[]=$up_button;


			if ($next_key) {
				$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'account/payment/'.$next_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

			}






		}
		elseif ($data['parent']=='payment_service_provider') {
			include_once 'class.Payment_Service_Provider.php';
			$psp=new Payment_Service_Provider($data['parent_key']);

			$up_button=array('icon'=>'arrow-up', 'title'=>_("Payment option").' '.$psp->get('Payment Service Provider Name'), 'reference'=>'account/payment_service_provider/'.$data['parent_key']);

			if ($prev_key) {
				$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'payment_service_provider/'.$data['parent_key'].'/payment/'.$prev_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'', 'url'=>'');

			}
			$left_buttons[]=$up_button;


			if ($next_key) {
				$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'payment_service_provider/'.$data['parent_key'].'/payment/'.$next_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

			}






		}
		elseif ($data['parent']=='payment_account') {
			include_once 'class.Payment_Account.php';
			$payment_account=new Payment_Account($data['parent_key']);

			$up_button=array('icon'=>'arrow-up', 'title'=>_("Payment account").' '.$payment_account->get('Payment Account Name'), 'reference'=>'account/payment_account/'.$data['parent_key']);

			if ($prev_key) {
				$left_buttons[]=array('icon'=>'arrow-left', 'title'=>$prev_title, 'reference'=>'payment_account/'.$data['parent_key'].'/payment/'.$prev_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-left disabled', 'title'=>'', 'url'=>'');

			}
			$left_buttons[]=$up_button;


			if ($next_key) {
				$left_buttons[]=array('icon'=>'arrow-right', 'title'=>$next_title, 'reference'=>'payment_account/'.$data['parent_key'].'/payment/'.$next_key);

			}else {
				$left_buttons[]=array('icon'=>'arrow-right disabled', 'title'=>'', 'url'=>'');

			}






		}
	}
	else {
		exit('');

	}

	$sections=get_sections('account', '');


	$sections['account']['selected']=true;



	$title=_('Payment').' <span class="id">'.$data['_object']->get('Payment Key').' '.($data['_object']->get('Payment Transaction ID')!=''?'('.$data['_object']->get('Payment Transaction ID').')':'').' </span>';


	$_content=array(
		'sections_class'=>'',
		'sections'=>$sections,
		'left_buttons'=>$left_buttons,
		'right_buttons'=>$right_buttons,
		'title'=>$title,
		'search'=>array('show'=>true, 'placeholder'=>_('Search account'))

	);
	$smarty->assign('_content', $_content);


	$html=$smarty->fetch('navigation.tpl');

	return $html;

}


?>
