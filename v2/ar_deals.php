<?php

/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>

 Copyright (c) 2013, Inikoo


*/

require_once 'common.php';

require_once 'ar_common.php';

if (!isset($_REQUEST['tipo'])) {
	$response=array('state'=>405,'msg'=>'Non acceptable request (t)');
	echo json_encode($response);
	exit;
}

if (count($user->stores)==0) return;


$tipo=$_REQUEST['tipo'];
switch ($tipo) {
case('is_voucher_code_in_store'):
	$data=prepare_values($_REQUEST,array(
			'store_key'=>array('type'=>'key'),
			'query'=>array('type'=>'string'),
		));
	is_voucher_code_in_store($data);
	break;

case('marketing_per_store'):
	list_marketing_per_store();
	break;
case('get_offer_elements_numbers'):
	$data=prepare_values($_REQUEST,array(
			'parent'=>array('type'=>'string'),
			'parent_key'=>array('type'=>'key'),
		));
	get_offer_elements_numbers($data) ;

	break;
case('get_campaign_elements_numbers'):
	$data=prepare_values($_REQUEST,array(
			'parent'=>array('type'=>'string'),
			'parent_key'=>array('type'=>'key'),
		));
	get_campaign_elements_numbers($data) ;

	break;
case('get_deal_component_elements_numbers'):
	$data=prepare_values($_REQUEST,array(
			'parent'=>array('type'=>'string'),
			'parent_key'=>array('type'=>'key'),
		));
	get_deal_component_elements_numbers($data) ;

	break;

case('campaigns'):
	list_campaigns();
	break;
case('deals'):
	list_deals();
	break;
case('deal_components'):
	list_deal_components();
	break;

case('deal_data'):
	$data=prepare_values($_REQUEST,array(
			'deal_key'=>array('type'=>'key')
		));
	deal_data($data);
	break;
case('is_campaign_name_in_store'):
	$data=prepare_values($_REQUEST,array(
			'store_key'=>array('type'=>'key'),
			'query'=>array('type'=>'string'),
		));
	is_campaign_name_in_store($data);
	break;
case('is_deal_name_in_store'):
	$data=prepare_values($_REQUEST,array(
			'store_key'=>array('type'=>'key'),
			'query'=>array('type'=>'string'),
		));
	is_deal_name_in_store($data);
	break;
case('orders'):
	$can_see_customers=$user->can_view('customers');
	list_orders($can_see_customers);
	break;

case('orders_with_voucher'):
	$can_see_customers=$user->can_view('customers');
	list_orders_with_voucher($can_see_customers);
	break;
case('customers'):
	list_customers();
	break;
case('name_in_other_deal'):
	$data=prepare_values($_REQUEST,array(
			'query'=>array('type'=>'string'),
			'store_key'=>array('type'=>'key'),
			'deal_key'=>array('type'=>'key')
		));
	name_in_other_deal($data) ;
	break;
default:
	$response=array('state'=>404,'resp'=>'Operation not found');
	echo json_encode($response);
}



function deal_data($data) {

	require_once 'class.Deal.php';

	$deal_key=$data['deal_key'];
	$deal=new Deal($deal_key);

	$deal_data=array(
		'name'=>$deal->data['Deal Name'],
		'description'=>$deal->data['Deal Description'],
		'key'=>$deal->id,
	);

	$response=
		array('state'=>200,
		'data'=>$deal_data


	);
	echo json_encode($response);

}


function name_in_other_deal($data) {
	$query=_trim($data['query']);
	$sql=sprintf('select `Deal Name`,`Deal Key`,`Deal Name` from `Deal Dimension` where `Deal Name`=%s and `Deal Key`!=%d and `Deal Store Key`=%d ',
		prepare_mysql($query),
		$data['deal_key'],
		$data['store_key']
	);
	$result=mysql_query($sql);
	//print $sql;
	$num_rows = mysql_num_rows($result);
	if ($num_rows==0) {
		$response=array('state'=>200,'found'=>0,'msg'=>'');
		echo json_encode($response);
	} else {
		$deals='';

		while ($row=mysql_fetch_assoc($result)) {
			$deals.=sprintf(', <a href="deal.php?id=%d">%s (%d)</a>',$row['Deal Key'],$row['Deal Name'],$row['Deal Key']);
		}
		$deals=preg_replace('/^, /','',$deals);



		$response=array('state'=>200,'found'=>1,'msg'=>_('Same name found in another').' '.ngettext('offer','offers',$num_rows).'. '.$deals);
		echo json_encode($response);
	}
}

function list_customers() {


	if (isset($_REQUEST['parent'])) {
		$parent=$_REQUEST['parent'];
	}else {
		exit('no parent');
	}

	if (isset($_REQUEST['parent_key'])) {
		$parent_key=$_REQUEST['parent_key'];
	}else {
		exit('no parent_key');
	}

	switch ($parent) {
	case 'campaign':
		$_conf='campaign';
		break;
	case 'deal':
		$_conf='deal';
		break;

	default:
		exit('unknown parent');
		break;
	}

	$conf=$_SESSION['state'][$_conf]['customers'];

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];
	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=$conf['nr'];
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');

	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];
	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');

	$_SESSION['state'][$_conf]['customers']['order']=$order;
	$_SESSION['state'][$_conf]['customers']['order_dir']=$order_direction;
	$_SESSION['state'][$_conf]['customers']['nr']=$number_results;
	$_SESSION['state'][$_conf]['customers']['sf']=$start_from;
	$_SESSION['state'][$_conf]['customers']['f_field']=$f_field;
	$_SESSION['state'][$_conf]['customers']['f_value']=$f_value;

	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';

	$table=' `Order Deal Bridge` B left join  `Order Dimension` O on (O.`Order Key`=B.`Order Key`) left join `Customer Dimension` CD on (`Order Customer Key`=CD.`Customer Key`)';


	switch ($parent) {
	case 'deal':
		$where=sprintf(" where `Deal Key`=%d and `Used`='Yes' ",$parent_key);
		break;
	case 'campaign':
		$where=sprintf(" where `Deal Campaign Key`=%d and `Used`='Yes' ",$parent_key);
		break;
	default:
		exit('unknown parent');
		break;
	}





	$wheref="";


	if ($f_field=='name'  and $f_value!='')
		$wheref.=" and `Customer Name` like '".addslashes($f_value)."%'";
	elseif ($f_field=='country' and  $f_value!='') {
		if ($f_value=='UNK') {
			$wheref.=" and  `Customer Main Country Code`='".$f_value."'    ";
			$find_data=' '._('a unknown country');
		} else {

			$f_value=Address::parse_country($f_value);
			if ($f_value!='UNK') {
				$wheref.=" and  `Customer Main Country Code`='".$f_value."'    ";
				$country=new Country('code',$f_value);
				$find_data=' '.$country->data['Country Name'].' <img src="art/flags/'.$country->data['Country 2 Alpha Code'].'.png" alt="'.$country->data['Country Code'].'"/>';
			}

		}
	}

	$sql="select count(distinct `Customer Key`) as total from  $table  $where $wheref";
	//   print $mode.' '.$sql;
	$res = mysql_query($sql);
	if ($row=mysql_fetch_array($res)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(distinct `Customer Key`) as total from  $table  $where      ";

		$res = mysql_query($sql);
		if ($row=mysql_fetch_array($res)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('customer','customers',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
	elseif ($total_records>10)
		$rtext_rpp=' ('._("Showing all").')';
	else
		$rtext_rpp='';



	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any customer like")." <b>$f_value</b> ";
			break;
		case('postcode'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any customer with postcode like")." <b>$f_value</b> ";
			break;
		case('country'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any customer based in").$find_data;
			break;
		case('id'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any customer with ID like")." <b>$f_value</b> ";
			break;
		case('last_more'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No customer with last order")."> <b>".number($f_value)."</b> ".ngettext('day','days',$f_value);
			break;
		case('last_more'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No customer with last order")."< <b>".number($f_value)."</b> ".ngettext('day','days',$f_value);
			break;
		case('maxvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No customer with balance")."< <b>".money($f_value,$currency)."</b> ";
			break;
		case('minvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No customer with balance")."> <b>".money($f_value,$currency)."</b> ";
			break;


		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('with name like')." <b>*".$f_value."*</b>";
			break;
		case('id'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('with ID  like')." <b>".$f_value."*</b>";
			break;
		case('postcode'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('with postcode like')." <b>".$f_value."*</b>";
			break;
		case('country'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('based in').$find_data;
			break;
		case('last_more'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('which last order')."> ".number($f_value)."  ".ngettext('day','days',$f_value);
			break;
		case('last_less'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('which last order')."< ".number($f_value)."  ".ngettext('day','days',$f_value);
			break;
		case('maxvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('which balance')."< ".money($f_value,$currency);
			break;
		case('minvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('customer','customers',$total)." "._('which balance')."> ".money($f_value,$currency);
			break;
		}
	}
	else
		$filter_msg='';


	$_order=$order;
	$_dir=$order_direction;

	if ($order=='orders')
		$order='orders';
	else if ($order=='id')
			$order='customer_id';
		elseif ($order=='location')
			$order='`Customer Main Location`';
		elseif ($order=='last_order')
			$order='last_order';

		else
			$order='`Customer Name`';


		$sql="select   CD.`Customer Key` as customer_id,`Customer Name`,`Customer Main Location`,
	count(distinct O.`Order Key`) as orders ,
	max(O.`Order Date`) as last_order from
	$table $where $wheref group by `Customer Key`    order by $order $order_direction  limit $start_from,$number_results ";


	$data=array();
	//print $sql;
	$res = mysql_query($sql);
	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

		$id="<a href='customer.php?p=cs&id=".$row['customer_id']."'>".sprintf("%05d",$row['customer_id']).'</a>';

		$data[]=array(
			'id'=>$id,
			'name'=>sprintf('<a href="customer.php?id=%d">%s</a>',$row['customer_id'],$row['Customer Name']),
			'location'=>$row['Customer Main Location'],
			'orders'=>number($row['orders']),
			'last_order'=> strftime("%e %b %y", strtotime($row['last_order'].' +0:00')),



		);
	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$data,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,

			'records_perpage'=>$number_results,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'filtered'=>$filtered
		)
	);
	echo json_encode($response);
}

function list_orders($can_see_customers=false) {

	if (isset($_REQUEST['parent'])) {
		$parent=$_REQUEST['parent'];
	}else {
		exit('no parent');
	}

	if (isset($_REQUEST['parent_key'])) {
		$parent_key=$_REQUEST['parent_key'];
	}else {
		exit('no parent_key');
	}

	switch ($parent) {
	case 'campaign':
		$_conf='campaign';
		break;
	case 'deal':
		$_conf='deal';
		break;

	default:
		exit('unknown parent');
		break;
	}


	$conf=$_SESSION['state'][$_conf]['orders'];


	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];

	if (!is_numeric($start_from))
		$start_from=0;

	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=$conf['nr'];
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];
	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;



	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	$_SESSION['state'][$_conf]['order']['order']=$order;
	$_SESSION['state'][$_conf]['order']['order_dir']=$order_direction;
	$_SESSION['state'][$_conf]['order']['nr']=$number_results;
	$_SESSION['state'][$_conf]['order']['sf']=$start_from;
	$_SESSION['state'][$_conf]['order']['f_field']=$f_field;
	$_SESSION['state'][$_conf]['order']['f_value']=$f_value;


	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	switch ($parent) {
	case 'deal':
		$where=sprintf(" where `Deal Key`=%d and `Used`='Yes' ",$parent_key);
		break;
	case 'campaign':
		$where=sprintf(" where `Deal Campaign Key`=%d and `Used`='Yes' ",$parent_key);
		break;
	default:
		exit('unknown parent');
		break;

	}


	$wheref='';

	if (($f_field=='customer_name')  and $f_value!='') {
		$wheref="  and  `Order Customer Name` like '%".addslashes($f_value)."%'";
	}
	elseif (($f_field=='postcode')  and $f_value!='') {
		$wheref="  and  `Customer Main Postal Code` like '%".addslashes($f_value)."%'";
	}
	elseif ($f_field=='public_id')
		$wheref.=" and  `Order Public ID`  like '".addslashes($f_value)."%'";

	elseif ($f_field=='maxvalue' and is_numeric($f_value) )
		$wheref.=" and  `Order Invoiced Balance Total Amount`<=".$f_value."    ";
	elseif ($f_field=='minvalue' and is_numeric($f_value) )
		$wheref.=" and  `Order Invoiced Balance Total Amount`>=".$f_value."    ";
	elseif ($f_field=='country' and  $f_value!='') {
		if ($f_value=='UNK') {
			$wheref.=" and  `Order Billing To Country Code`='".$f_value."'    ";
			$find_data=' '._('a unknown country');
		} else {
			$f_value=Address::parse_country($f_value);
			if ($f_value!='UNK') {
				$wheref.=" and  `Order Billing To Country Code`='".$f_value."'    ";
				$country=new Country('code',$f_value);
				$find_data=' '.$country->data['Country Name'].' <img style="vertical-align: text-bottom;position:relative;bottom:2px" src="art/flags/'.strtolower($country->data['Country 2 Alpha Code']).'.gif" alt="'.$country->data['Country Code'].'"/>';
			}
		}
	}



	$sql="select count(DISTINCT B.`Order Key`) as total from `Order Deal Bridge` B left join  `Order Dimension` O on (O.`Order Key`=B.`Order Key`)    $where $wheref";
	// print $sql;
	$res = mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT B.`Order Key`) as total from  `Order Deal Bridge` B left join  `Order Dimension` O on (O.`Order Key`=B.`Order Key`)  $where      ";
		$res = mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}

	}

	$rtext=number($total_records)." ".ngettext('order','orders',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
	elseif ($total_records>10)
		$rtext_rpp=' ('._("Showing all").')';
	else
		$rtext_rpp='';


	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('customer_name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with customer like")." <b>$f_value</b> ";
			break;
		case('public_id'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with number like")." <b>$f_value</b> ";
			break;
		case('maxvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with balance")."< <b>".money($f_value,$currency)."</b> ";
			break;
		case('minvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with balance")."> <b>".money($f_value,$currency)."</b> ";
			break;
		case('country'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order from")." <b>".$find_data."</b> ";
			break;

		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('customer_name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('with name like')." <b>*".$f_value."*</b>";
			break;
		case('public_id'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('with Number like')." <b>".$f_value."*</b>";
			break;
		case('maxvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('which balance')."< ".money($f_value,$currency);
			break;
		case('minvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('which balance')."> ".money($f_value,$currency);
			break;
		case('country'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('from')." ".$find_data;
			break;
		}
	}
	else
		$filter_msg='';



	if ($order=='order') {
		$order='`Order Public ID`';
	}elseif ($order=='customer_name') {
		if ($can_see_customers) {
			$order='`Order Customer Name`';
		}else {
			$order='`Order Public ID`';
		}
	}elseif ($order=='dispatch_state') {
		$order='`Order Current Dispatch State`';
	}elseif ($order=='total_amount') {
		$order='`Order Total Amount`';
	}elseif ($order=='deal_used') {
		$order='deal_used';
	} else {
		$order='`Order Date`';
	}



	$sql=sprintf("select `Order Customer Key`,`Order Customer Name`,O.`Order Key`,`Order Public ID`,`Order Date`,`Order Current Dispatch State`,`Order Total Amount`,`Order Currency` from `Order Deal Bridge` B left join  `Order Dimension` O on (O.`Order Key`=B.`Order Key`)    %s %s  group by   B.`Order Key` order by  $order $order_direction  limit $start_from,$number_results"
		,$where
		,$wheref
	);
	//  print $sql;

	$res=mysql_query($sql);
	$data=array();

	while ($row= mysql_fetch_array($res, MYSQL_ASSOC) ) {
		if ($can_see_customers)
			$customer='<a href="customer.php?id='.$row['Order Customer Key'].'">'.$row['Order Customer Name'].'</a>';
		else
			$customer=sprintf("%05d",$row['Order Customer Key']);



		$data[]=array(
			'order'=>sprintf("<a href='order.php?id=%d'>%s</a>",$row['Order Key'],$row['Order Public ID']),
			'customer_name'=>$customer,
			'date'=> strftime("%e %b %y", strtotime($row['Order Date'].' +0:00')),
			'dispatch_state'=>get_order_formated_dispatch_state($row['Order Current Dispatch State'],$row['Order Key']),// function in: utils/order_functions.php

			'total_amount'=>money($row['Order Total Amount'],$row['Order Currency'])


		);
	}

	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$data,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'filtered'=>$filtered
		)
	);
	echo json_encode($response);
}

function list_orders_with_voucher($can_see_customers=false) {

	if (isset($_REQUEST['parent'])) {
		$parent=$_REQUEST['parent'];
	}else {
		exit('no parent');
	}



	if (isset($_REQUEST['voucher_key'])) {
		$voucher_key=$_REQUEST['voucher_key'];
	}else {
		exit('no voucher');
	}

	if (isset($_REQUEST['deal_key'])) {
		$deal_key=$_REQUEST['deal_key'];
	}else {
		exit('no deal_key');
	}


	switch ($parent) {

	case 'voucher':
		$_conf='voucher';
		break;

	case 'deal':
		$_conf='deal';
		break;

	default:
		exit('unknown parent');
		break;
	}


	$conf=$_SESSION['state'][$_conf]['orders_with_voucher'];


	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];

	if (!is_numeric($start_from))
		$start_from=0;

	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=$conf['nr'];
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];
	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;



	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	$_SESSION['state'][$_conf]['orders_with_voucher']['order']=$order;
	$_SESSION['state'][$_conf]['orders_with_voucher']['order_dir']=$order_direction;
	$_SESSION['state'][$_conf]['orders_with_voucher']['nr']=$number_results;
	$_SESSION['state'][$_conf]['orders_with_voucher']['sf']=$start_from;
	$_SESSION['state'][$_conf]['orders_with_voucher']['f_field']=$f_field;
	$_SESSION['state'][$_conf]['orders_with_voucher']['f_value']=$f_value;


	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	$where=sprintf(" where `Voucher Key`=%d  ",$voucher_key);



	$wheref='';

	if (($f_field=='customer_name')  and $f_value!='') {
		$wheref="  and  `Order Customer Name` like '%".addslashes($f_value)."%'";
	}
	elseif (($f_field=='postcode')  and $f_value!='') {
		$wheref="  and  `Customer Main Postal Code` like '%".addslashes($f_value)."%'";
	}
	elseif ($f_field=='public_id')
		$wheref.=" and  `Order Public ID`  like '".addslashes($f_value)."%'";

	elseif ($f_field=='maxvalue' and is_numeric($f_value) )
		$wheref.=" and  `Order Invoiced Balance Total Amount`<=".$f_value."    ";
	elseif ($f_field=='minvalue' and is_numeric($f_value) )
		$wheref.=" and  `Order Invoiced Balance Total Amount`>=".$f_value."    ";
	elseif ($f_field=='country' and  $f_value!='') {
		if ($f_value=='UNK') {
			$wheref.=" and  `Order Billing To Country Code`='".$f_value."'    ";
			$find_data=' '._('a unknown country');
		} else {
			$f_value=Address::parse_country($f_value);
			if ($f_value!='UNK') {
				$wheref.=" and  `Order Billing To Country Code`='".$f_value."'    ";
				$country=new Country('code',$f_value);
				$find_data=' '.$country->data['Country Name'].' <img style="vertical-align: text-bottom;position:relative;bottom:2px" src="art/flags/'.strtolower($country->data['Country 2 Alpha Code']).'.gif" alt="'.$country->data['Country Code'].'"/>';
			}
		}
	}



	$sql="select count(DISTINCT B.`Order Key`) as total from `Voucher Order Bridge` B left join  `Order Dimension` O on (O.`Order Key`=B.`Order Key`)    $where $wheref";
	// print $sql;
	$res = mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT B.`Order Key`) as total from  `Voucher Order Bridge` B left join  `Order Dimension` O on (O.`Order Key`=B.`Order Key`)  $where      ";
		$res = mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}

	}

	$rtext=number($total_records)." ".ngettext('order','orders',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
	elseif ($total_records>10)
		$rtext_rpp=' ('._("Showing all").')';
	else
		$rtext_rpp='';


	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('customer_name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with customer like")." <b>$f_value</b> ";
			break;
		case('public_id'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with number like")." <b>$f_value</b> ";
			break;
		case('maxvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with balance")."< <b>".money($f_value,$currency)."</b> ";
			break;
		case('minvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order with balance")."> <b>".money($f_value,$currency)."</b> ";
			break;
		case('country'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("No order from")." <b>".$find_data."</b> ";
			break;

		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('customer_name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('with name like')." <b>*".$f_value."*</b>";
			break;
		case('public_id'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('with Number like')." <b>".$f_value."*</b>";
			break;
		case('maxvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('which balance')."< ".money($f_value,$currency);
			break;
		case('minvalue'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('which balance')."> ".money($f_value,$currency);
			break;
		case('country'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ".ngettext('order','orders',$total)." "._('from')." ".$find_data;
			break;
		}
	}
	else
		$filter_msg='';







	if ($order=='order') {
		$order='`Order Public ID`';
	}elseif ($order=='customer_name') {
		if ($can_see_customers) {
			$order='`Order Customer Name`';
		}else {
			$order='`Order Public ID`';
		}
	}elseif ($order=='dispatch_state') {
		$order='`Order Current Dispatch State`';
	}elseif ($order=='total_amount') {
		$order='`Order Total Amount`';
	}elseif ($order=='deal_used') {
		$order='deal_used';
	} else {
		$order='`Order Date`';
	}




	$sql=sprintf("select  (select count(*) from `Order Deal Bridge` ODB where ODB.`Order Key`=B.`Order Key` and ODB.`Deal Key`=%d) as deal_used , `Order Customer Key`,`Order Customer Name`,O.`Order Key`,`Order Public ID`,`Order Date`,`Order Current Dispatch State`,`Order Total Amount`,`Order Currency` from `Voucher Order Bridge` B left join `Order Dimension` O on (O.`Order Key`=B.`Order Key`)    %s %s  group by   B.`Order Key` order by  $order $order_direction  limit $start_from,$number_results",
		$deal_key,
		$where,
		$wheref
	);
	//  print $sql;

	$res=mysql_query($sql);
	$data=array();

	while ($row= mysql_fetch_array($res, MYSQL_ASSOC) ) {
		if ($can_see_customers)
			$customer='<a href="customer.php?id='.$row['Order Customer Key'].'">'.$row['Order Customer Name'].'</a>';
		else
			$customer=sprintf("%05d",$row['Order Customer Key']);



		$data[]=array(
			'order'=>sprintf("<a href='order.php?id=%d'>%s</a>",$row['Order Key'],$row['Order Public ID']),
			'customer_name'=>$customer,
			'date'=> strftime("%e %b %y", strtotime($row['Order Date'].' +0:00')),
			'dispatch_state'=>get_order_formated_dispatch_state($row['Order Current Dispatch State'],$row['Order Key']),// function in: utils/order_functions.php

			'total_amount'=>money($row['Order Total Amount'],$row['Order Currency']),
			'deal_used'=>($row['deal_used']?'<img src="art/icons/accept.png">':'<img src="art/icons/accept_bw_hidden.png">')


		);
	}

	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$data,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'filtered'=>$filtered
		)
	);
	echo json_encode($response);
}


function list_campaigns() {



	if (!isset($_REQUEST['parent']) or !isset($_REQUEST['parent_key'])) {

		exit("no parent");
	}

	$parent= $_REQUEST['parent'];
	$parent_key=$_REQUEST['parent_key'];

	$conf=$_SESSION['state'][$parent]['campaigns'];


	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];


	if (isset( $_REQUEST['nr'])) {
		$number_results=$_REQUEST['nr'];
		if ($start_from>0) {
			$page=floor($start_from/$number_results);
			$start_from=$start_from-$page;
		}

	} else
		$number_results=$conf['nr'];


	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;


	$elements=$conf['elements'];



	if (isset( $_REQUEST['campaign_elements_Active'])) {
		$elements['Active']=$_REQUEST['campaign_elements_Active'];
	}
	if (isset( $_REQUEST['campaign_elements_Waiting'])) {
		$elements['Waiting']=$_REQUEST['campaign_elements_Waiting'];
	}
	if (isset( $_REQUEST['campaign_elements_Suspended'])) {
		$elements['Suspended']=$_REQUEST['campaign_elements_Suspended'];
	}
	if (isset( $_REQUEST['campaign_elements_Finish'])) {
		$elements['Finish']=$_REQUEST['campaign_elements_Finish'];
	}



	$_SESSION['state'][$parent]['campaigns']['order']=$order;
	$_SESSION['state'][$parent]['campaigns']['order_dir']=$order_direction;
	$_SESSION['state'][$parent]['campaigns']['nr']=$number_results;
	$_SESSION['state'][$parent]['campaigns']['sf']=$start_from;
	$_SESSION['state'][$parent]['campaigns']['f_field']=$f_field;
	$_SESSION['state'][$parent]['campaigns']['f_value']=$f_value;
	$_SESSION['state'][$parent]['campaigns']['elements']=$elements;



	if ($parent=='store')
		$where=sprintf("where  `Deal Campaign Store Key`=%d    ",$parent_key);
	elseif ($parent=='marketing')
		$where=sprintf("where  `Deal Campaign Store Key`=%d    ",$parent_key);
	else
		$where=sprintf("where true ");;


	$_elements='';
	$count_elements=0;
	foreach ($elements as $_key=>$_value) {
		if ($_value) {
			$count_elements++;
			$_elements.=','.prepare_mysql($_key);

		}
	}
	$_elements=preg_replace('/^\,/','',$_elements);
	if ($_elements=='') {
		$where.=' and false' ;
	} elseif ($count_elements<4) {
		$where.=' and `Deal Campaign Status` in ('.$_elements.')' ;
	}






	$filter_msg='';
	$wheref='';
	if ($f_field=='description' and $f_value!='')
		$wheref.=" and  `Deal Campaign Description` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Deal Campaign Name` like '".addslashes($f_value)."%'";









	$sql="select count(*) as total from `Deal Campaign Dimension` $where $wheref";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

		$total=$row['total'];
	}
	if ($wheref!='') {
		$sql="select count(*) as total_without_filters from `Deal Campaign Dimension`   $where ";


		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

			$total_records=$row['total_without_filters'];
			$filtered=$row['total_without_filters']-$total;
		}

	} else {
		$filtered=0;
		$filter_total=0;
		$total_records=$total;
	}
	mysql_free_result($res);



	$rtext=number($total_records)." ".ngettext('campaign','campaigns',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	elseif ($total_records>0)
		$rtext_rpp=' ('._('Showing all').')';
	else
		$rtext_rpp='';


	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any campaign with this name ")." <b>".$f_value."*</b> ";
			break;
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any campaign with this code ")." <b>".$f_value."*</b> ";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any campaign with description like ")." <b>".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('campaigns with name like')." <b>".$f_value."*</b>";
			break;
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('campaigns with code like')." <b>".$f_value."*</b>";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('campaigns with description like')." <b>".$f_value."*</b>";
			break;
		}
	}
	else
		$filter_msg='';

	$_dir=$order_direction;
	$_order=$order;

	if ($order=='name')
		$order='`Deal Campaign Name`';
	elseif ($order=='description')
		$order='`Deal Campaign Description`';
	elseif ($order=='orders')
		$order='`Deal Campaign Total Acc Used Orders`';
	elseif ($order=='customers')
		$order='`Deal Campaign Total Acc Used Customers`';
	elseif ($order=='store')
		$order='`Store Code`';
	elseif ($order=='deals')
		$order='`Deal Campaign Number Current Deals`';
	elseif ($order=='from')
		$order='`Deal Campaign Valid From`';
	elseif ($order=='to')
		$order='`Deal Campaign Valid To`';


	else
		$order='`Deal Campaign Name`';


	$sql="select *  from `Deal Campaign Dimension` left join `Store Dimension` S on (S.`Store Key`=`Deal Campaign Store Key`)  $where $wheref order by $order $order_direction limit $start_from,$number_results    ";

	$res = mysql_query($sql);

	$total=mysql_num_rows($res);
	$adata=array();
	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

		$orders=number($row['Deal Campaign Total Acc Used Orders']);
		$customers=number($row['Deal Campaign Total Acc Used Customers']);
		$deals=number($row['Deal Campaign Number Current Deals']);




		if (!$row['Deal Campaign Valid To'] ) {
			$duration=_('Permanent');
			$to=_('Permanent');
		} else {
			if ($row['Deal Campaign Valid From']) {
				$duration=strftime("%a %e %b %Y", strtotime($row['Deal Campaign Valid From']." +00:00")).' - ';
				$from=strftime("%a %e %b %Y", strtotime($row['Deal Campaign Valid From']." +00:00"));
			}else {
				$duration='? -';
				$from='';
			}
			$duration.=strftime("%a %e %b %Y", strtotime($row['Deal Campaign Valid To']." +00:00"));
			$to=strftime("%a %e %b %Y", strtotime($row['Deal Campaign Valid To']." +00:00"));
		}

		if ($row['Deal Campaign Valid From']) {
			$from=strftime("%a %e %b %Y", strtotime($row['Deal Campaign Valid From']." +00:00"));
		}else {
			$from='';
		}


		$name=sprintf("<a href='campaign.php?id=%d'>%s</a>",$row['Deal Campaign Key'],$row['Deal Campaign Name']);
		$store=sprintf("<a href='marketing.php?store=%d'>%s</a>",$row['Deal Campaign Store Key'],$row['Store Code']);


		$adata[]=array(
			'store'=>$store,
			'name'=>$name,
			'description'=>$row['Deal Campaign Description'].$deals,
			'duration'=>$duration,
			'orders'=>$orders,
			'customers'=>$customers,
			'deals'=>$deals,
			'from'=>$from,
			'to'=>$to,

		);
	}
	mysql_free_result($res);


	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
		)
	);
	echo json_encode($response);
}

function list_deals() {

	global $user;

	if ( isset($_REQUEST['parent']))
		$parent= $_REQUEST['parent'];
	else {
		exit("no parent arg");
	}

	if ( isset($_REQUEST['parent_key']))
		$parent_key= $_REQUEST['parent_key'];
	else {
		exit("no parent key arg");
	}


	if ( isset($_REQUEST['referrer']))
		$referrer= $_REQUEST['referrer'];
	else {
		$referrer='marketing';
	}


	$conf=$_SESSION['state'][$parent]['offers'];



	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];


	if (isset( $_REQUEST['nr'])) {
		$number_results=$_REQUEST['nr'];

	} else
		$number_results=$conf['nr'];


	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');



	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;




	$elements=$conf['elements'];



	if (isset( $_REQUEST['elements_type']))
		$elements_type=$_REQUEST['elements_type'];
	else
		$elements_type=$conf['elements_type'];


	if (isset( $_REQUEST['offer_status_elements_Finish'])) {
		$elements['status']['Finish']=$_REQUEST['offer_status_elements_Finish'];
	}
	if (isset( $_REQUEST['offer_status_elements_Active'])) {
		$elements['status']['Active']=$_REQUEST['offer_status_elements_Active'];
	}
	if (isset( $_REQUEST['offer_status_elements_Waiting'])) {
		$elements['status']['Waiting']=$_REQUEST['offer_status_elements_Waiting'];
	}
	if (isset( $_REQUEST['offer_status_elements_Suspended'])) {
		$elements['status']['Suspended']=$_REQUEST['offer_status_elements_Suspended'];
	}



	if (isset( $_REQUEST['offer_trigger_elements_order'])) {
		$elements['trigger']['Order']=$_REQUEST['offer_trigger_elements_order'];
	}
	if (isset( $_REQUEST['offer_trigger_elements_department'])) {
		$elements['trigger']['Department']=$_REQUEST['offer_trigger_elements_department'];
	}
	if (isset( $_REQUEST['offer_trigger_elements_family'])) {
		$elements['trigger']['Family']=$_REQUEST['offer_trigger_elements_family'];
	}
	if (isset( $_REQUEST['offer_trigger_elements_product'])) {
		$elements['trigger']['Product']=$_REQUEST['offer_trigger_elements_product'];
	}


	$_SESSION['state'][$parent]['offers']['order']=$order;
	$_SESSION['state'][$parent]['offers']['order_dir']=$order_direction;
	$_SESSION['state'][$parent]['offers']['nr']=$number_results;
	$_SESSION['state'][$parent]['offers']['sf']=$start_from;
	$_SESSION['state'][$parent]['offers']['f_field']=$f_field;
	$_SESSION['state'][$parent]['offers']['f_value']=$f_value;
	$_SESSION['state'][$parent]['offers']['elements']=$elements;
	$_SESSION['state'][$parent]['offers']['elements_type']=$elements_type;





	if ($parent=='store') {
		$where=sprintf("where  `Deal Trigger`='Order' and  `Deal Store Key`=%d     ",$parent_key);



	}elseif ($parent=='campaign') {
		$where=sprintf("where  `Deal Campaign Key`=%d     ",$parent_key);
	}
	elseif ($parent=='department')
		$where=sprintf("where    `Deal Trigger`='Department' and  `Deal Trigger Key`=%d     ",$parent_key);
	elseif ($parent=='family')
		$where=sprintf("where    `Deal Trigger`='Family' and  `Deal Trigger Key`=%d   ",$parent_key);
	elseif ($parent=='product')
		$where=sprintf("where    `Deal Trigger`='Product' and  `Deal Trigger Key`=%d   ",$parent_key);
	elseif ($parent=='customer')
		$where=sprintf("where  `Deal Trigger`='Customer' and  `Deal Trigger Key`=%d   ",$parent_key);
	elseif ($parent=='customer_categories')
		$where=sprintf("where    `Deal Trigger`='Customer Category' and  `Deal Trigger Key`=%d   ",$parent_key);
	elseif ($parent=='customers_list')
		$where=sprintf("where    `Deal Trigger`='Customer List' and  `Deal Trigger Key`=%d   ",$parent_key);
	elseif ($parent=='marketing')
		$where=sprintf("where   `Deal Store Key`=%d     ",$parent_key);
	else
		$where=sprintf("where `Deal Store Key` in (%s)",join(',',$user->stores));




	switch ($elements_type) {
	case 'trigger':
		$_elements='';
		$count_elements=0;
		foreach ($elements['trigger'] as $_key=>$_value) {
			if ($_value) {
				$count_elements++;
				$_elements.=','.prepare_mysql($_key);

			}
		}
		$_elements=preg_replace('/^\,/','',$_elements);
		if ($_elements=='') {
			$where.=' and false' ;
		} elseif ($count_elements<4) {
			$where.=' and `Deal Trigger` in ('.$_elements.')' ;
		}
		break;
	case 'status':
		$_elements='';
		$count_elements=0;
		foreach ($elements['status'] as $_key=>$_value) {
			if ($_value) {
				$count_elements++;
				$_elements.=','.prepare_mysql($_key);

			}
		}
		$_elements=preg_replace('/^\,/','',$_elements);
		if ($_elements=='') {
			$where.=' and false' ;
		} elseif ($count_elements<4) {
			$where.=' and `Deal Status` in ('.$_elements.')' ;
		}
		break;




	}


	// print "$parent $where";
	$filter_msg='';
	$wheref='';
	if ($f_field=='description' and $f_value!='')
		$wheref.=" and `Deal Terms Description` like '%".addslashes($f_value)."%'   ";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Deal Name` like '".addslashes($f_value)."%'";







	$sql="select count( distinct `Deal Key`) as total from `Deal Dimension` left join `Deal Component Dimension` on (`Deal Component Deal Key`=`Deal Key`)  $where $wheref";
	//print $sql;
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

		$total=$row['total'];
	}
	if ($wheref!='') {
		$sql="select count( distinct `Deal Key`) as total_without_filters from `Deal Dimension` left join `Deal Component Dimension` on (`Deal Component Deal Key`=`Deal Key`)  $where ";


		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

			$total_records=$row['total_without_filters'];
			$filtered=$row['total_without_filters']-$total;
		}

	} else {
		$filtered=0;
		$filter_total=0;
		$total_records=$total;
	}
	mysql_free_result($res);


	$rtext=number($total_records)." ".ngettext('offer','offers',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
	elseif ($total_records>=10)
		$rtext_rpp='('._("Showing all").')';
	else
		$rtext_rpp='';





	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any deal with this name ")." <b>".$f_value."*</b> ";
			break;
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any deal with this code ")." <b>*".$f_value."*</b> ";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any deal with description like ")." <b>".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('deals with name like')." <b>".$f_value."*</b>";
			break;
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('deals with code like')." <b>*".$f_value."*</b>";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('deals with description like')." <b>".$f_value."*</b>";
			break;
		}
	}
	else
		$filter_msg='';

	$_dir=$order_direction;
	$_order=$order;


	if ($order=='description')
		$order='`Deal Name`,`Deal Description`';
	elseif ($order=='orders')
		$order='`Deal Total Acc Used Orders`';
	elseif ($order=='customers')
		$order='`Deal Total Acc Used Customers`';
	elseif ($order=='store')
		$order='`Store Code`';
	elseif ($order=='state')
		$order='`Deal Status`';
	elseif ($order=='from')
		$order='`Deal Begin Date`';
	elseif ($order=='to')
		$order='`Deal Expiration Date`';
	else
		$order='`Deal Name`';


	$sql="select *  from `Deal Dimension` left join `Deal Component Dimension` on (`Deal Component Deal Key`=`Deal Key`)  left join `Store Dimension` S on (S.`Store Key`=`Deal Store Key`)  $where  $wheref  group by `Deal Key` order by $order $order_direction limit $start_from,$number_results    ";
	//print $sql;
	$res = mysql_query($sql);

	$total=mysql_num_rows($res);
	$adata=array();
	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {


		$name=sprintf('<a href="deal.php?id=%d&referrer=%s">%s</a>',$row['Deal Key'],$referrer,$row['Deal Name']);

		$orders=number($row['Deal Total Acc Used Orders']);
		$customers=number($row['Deal Total Acc Used Customers']);


		switch ($row['Deal Status']) {
		case 'Waiting':
			$state=sprintf('<img src="art/icons/bullet_orange.png" alt="%s" title="%s">',_('Waiting'),_('Offer waiting'));
			break;
		case 'Active':
			$state=sprintf('<img src="art/icons/bullet_green.png" alt="%s" title="%s">',_('Active'),_('Offer active'));
			break;
		case 'Suspended':
			$state=sprintf('<img src="art/icons/bullet_red.png" alt="%s" title="%s">',_('Suspended'),_('Offer suspended'));
			break;
		case 'Finish':
			$state=sprintf('<img src="art/icons/bullet_grey.png" alt="%s" title="%s">',_('Finished'),_('Offer finished'));
			break;
		default:
			$state=$row['Deal Status'];
		}



		$duration='';
		if ($row['Deal Expiration Date']=='' and $row['Deal Begin Date']=='') {
			$duration=_('Permanent');
		}else {

			if ($row['Deal Begin Date']!='') {
				$duration=strftime("%x", strtotime($row['Deal Begin Date']." +00:00"));

			}
			$duration.=' - ';
			if ($row['Deal Expiration Date']!='') {
				$duration.=strftime("%x", strtotime($row['Deal Expiration Date']." +00:00"));

			}else {
				$duration.=_('Present');
			}

		}

		if ($row['Deal Expiration Date']!='') {
			$to=strftime("%x", strtotime($row['Deal Expiration Date']." +00:00"));
		}else {
			$to=_('Permanent');
		}


		if ($row['Deal Begin Date']!='') {
			$from=strftime("%x", strtotime($row['Deal Begin Date']." +00:00"));
		}else {
			$from='';
		}


		$store=sprintf("<a href='marketing.php?store=%d'>%s</a>",$row['Deal Store Key'],$row['Store Code']);


		$adata[]=array(
		
			'store'=>$store,
			'name'=>$name,
			'description'=>sprintf('<span title="%s">%s</span>',strip_tags($row['Deal Term Allowances']),$row['Deal Term Allowances Label']),
			'orders'=>$orders,
			'customers'=>$customers,
			'duration'=>$duration,
			'state'=>$state,
			'from'=>$from,
			'to'=>$to,



		);
	}
	mysql_free_result($res);


	$response=array('resultset'=>
		array(

			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,


		)
	);
	echo json_encode($response);

}

function list_deal_components() {

	if ( isset($_REQUEST['parent']))
		$parent= $_REQUEST['parent'];
	else {
		exit("no parent arg");
	}

	if ( isset($_REQUEST['parent_key']))
		$parent_key=$_REQUEST['parent_key'];
	else
		exit("no parent key arg");


	$conf=$_SESSION['state']['deal']['components'];

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];


	if (isset( $_REQUEST['nr'])) {
		$number_results=$_REQUEST['nr'];

	} else
		$number_results=$conf['nr'];


	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');



	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;


	$elements=$conf['elements'];

	if (isset( $_REQUEST['deal_component_status_elements_Active'])) {
		$elements['Active']=$_REQUEST['deal_component_status_elements_Active'];
	}
	if (isset( $_REQUEST['deal_component_status_elements_Waiting'])) {
		$elements['Waiting']=$_REQUEST['deal_component_status_elements_Waiting'];
	}
	if (isset( $_REQUEST['deal_component_status_elements_Suspended'])) {
		$elements['Suspended']=$_REQUEST['deal_component_status_elements_Suspended'];
	}
	if (isset( $_REQUEST['deal_component_status_elements_Finish'])) {
		$elements['Finish']=$_REQUEST['deal_component_status_elements_Finish'];
	}



	$_SESSION['state']['deal']['components']['order']=$order;
	$_SESSION['state']['deal']['components']['order_dir']=$order_direction;
	$_SESSION['state']['deal']['components']['nr']=$number_results;
	$_SESSION['state']['deal']['components']['sf']=$start_from;
	$_SESSION['state']['deal']['components']['f_field']=$f_field;
	$_SESSION['state']['deal']['components']['f_value']=$f_value;
	$_SESSION['state']['deal']['components']['elements']=$elements;

	



	if ($parent=='deal')
		$where=sprintf("where  `Deal Component Deal Key`=%d   ",$parent_key);
	else
		$where=sprintf("where false ");;


	$_elements='';
	$count_elements=0;
	foreach ($elements as $_key=>$_value) {
		if ($_value) {
			$count_elements++;
			$_elements.=','.prepare_mysql($_key);

		}
	}
	$_elements=preg_replace('/^\,/','',$_elements);
	if ($_elements=='') {
		$where.=' and false' ;
	} elseif ($count_elements<4) {
		$where.=' and `Deal Component Status` in ('.$_elements.')' ;
	}





	
	$filter_msg='';
	$wheref='';
	if ($f_field=='allowances' and $f_value!='')
		$wheref.=" and  `Deal Component Allowance Plain Description` regexp '[[:<:]]".addslashes($f_value)."' ";





	$sql="select count(*) as total from `Deal Component Dimension`   $where $wheref";
	
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

		$total=$row['total'];
	}
	if ($wheref!='') {
		$sql="select count(*) as total_without_filters from `Deal Component Dimension`   $where ";

		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

			$total_records=$row['total_without_filters'];
			$filtered=$row['total_without_filters']-$total;
		}

	} else {
		$filtered=0;
		$filter_total=0;
		$total_records=$total;
	}
	mysql_free_result($res);


	$rtext=number($total_records)." ".ngettext(_('allowance'),_('allowances'),$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
	elseif ($total_records>10)
		$rtext_rpp=' ('._("Showing all").')';
	else {
		$rtext_rpp='';
	}






	if ($total==0 and $filtered>0) {
		switch ($f_field) {

		case('allowances'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any offer with allowances like ")." <b>".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {

		case('allowances'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('offer with allowances like')." <b>".$f_value."*</b>";
			break;
		}
	}
	else
		$filter_msg='';

	$_dir=$order_direction;
	$_order=$order;

	if ($order=='orders')
		$order='`Deal Component Total Acc Used Orders`';
	elseif ($order=='customers')
		$order='`Deal Component Total Acc Used Customers`';
	elseif ($order=='allowance')
		$order='`Deal Component Allowance Description`';
	elseif ($order=='from')
		$order='`Deal Component Begin Date`';
	elseif ($order=='to')
		$order='`Deal Component Expiration Date`';
	elseif ($order=='status')
		$order='`Deal Component Status`';
	elseif ($order=='allowances')
		$order='`Deal Component Allowance Plain Description`';	
	elseif ($order=='label')
		$order='`Deal Component XHTML Allowance Description Label`';		
	

	$sql="select *  from `Deal Component Dimension` $where  $wheref  order by $order $order_direction limit $start_from,$number_results    ";
	//print $sql;
	$res = mysql_query($sql);

	$total=mysql_num_rows($res);
	$adata=array();
	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {


		$orders=number($row['Deal Component Total Acc Used Orders']);
		$customers=number($row['Deal Component Total Acc Used Customers']);

		$duration='';
		if ($row['Deal Component Expiration Date']=='' and $row['Deal Component Begin Date']=='') {
			$duration=_('Permanent');
		}else {

			if ($row['Deal Component Begin Date']!='') {
				$duration=strftime("%x", strtotime($row['Deal Component Begin Date']." +00:00"));

			}
			$duration.=' - ';
			if ($row['Deal Component Expiration Date']!='') {
				$duration.=strftime("%x", strtotime($row['Deal Component Expiration Date']." +00:00"));

			}else {
				$duration.=_('Present');
			}

		}

		if ($row['Deal Component Begin Date']=='') {
			$from='';
		}else {
			$from=strftime("%x", strtotime($row['Deal Component Begin Date']." +00:00"));
		}


		if ($row['Deal Component Expiration Date']=='') {
			$to=_('permenent');
		}else {
			$to=strftime("%x", strtotime($row['Deal Component Expiration Date']." +00:00"));
		}



		switch ($row['Deal Component Allowance Target']) {
		case 'Department':
			$allowance_target=_('FamDepartmentily');
			break;
		case 'Family':
			$allowance_target=_('Family');
			break;
		case 'Product':
			$allowance_target=_('Product');
			break;
		default:
			$allowance_target=$row['Deal Component Allowance Target'];
		}




		
		switch ($row['Deal Component Status']) {
		case 'Waiting':
			$status=sprintf('<img src="art/icons/bullet_orange.png" alt="%s" title="%s">',_('Waiting'),_('Offer waiting'));
			break;
		case 'Active':
			$status=sprintf('<img src="art/icons/bullet_green.png" alt="%s" title="%s">',_('Active'),_('Offer active'));
			break;
		case 'Suspended':
			$status=sprintf('<img src="art/icons/bullet_red.png" alt="%s" title="%s">',_('Suspended'),_('Offer suspended'));
			break;
		case 'Finish':
			$status=sprintf('<img src="art/icons/bullet_grey.png" alt="%s" title="%s">',_('Finished'),_('Offer finished'));
			break;
		default:
			$status=$row['Deal Component Status'];
		}

		$adata[]=array(
		'deal_component_key'=>$row['Deal Component Key'],
			'terms'=>$row['Deal Component Terms Description'],
			'allowances'=>$row['Deal Component Allowance XHTML Description'],
			'label'=>$row['Deal Component XHTML Allowance Description Label'],
			'target'=>$row['Deal Component Allowance Target'],
			'orders'=>$orders,
			'customers'=>$customers,
			'duration'=>$duration,
			'from'=>$from,
			'to'=>$to,
			'status'=>$status


		);
	}
	mysql_free_result($res);



	// if($total<$number_results)
	//  $rtext=$total.' '.ngettext('store','stores',$total);
	//else
	//  $rtext='';

	//   $total_records=ceil($total_records/$number_results)+$total_records;

	$response=array('resultset'=>
		array(

			'state'=>200,
			'data'=>$adata,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,

			'records_perpage'=>$number_results,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'filtered'=>$filtered


		)
	);
	echo json_encode($response);

}

function get_offer_elements_numbers($data) {
	$elements_number=
		array(
		'status'=>array('Waiting'=>0,'Active'=>0,'Suspended'=>0,'Finish'=>0),

		'trigger'=>array('Order'=>0,'Department'=>0,'Family'=>0,'Product'=>0)
	);

	switch ($data['parent']) {
	case 'store':
		$where=sprintf("where `Deal Store Key`=%d",$data['parent_key']);
		break;
	case 'campaign':
		$where=sprintf("where `Deal Campaign Key`=%d",$data['parent_key']);
		break;
	default:
		$where=' where false';
	}

	$sql=sprintf("select count(*) as num,`Deal Trigger`  from  `Deal Dimension`  $where group by `Deal Trigger`");
	$res=mysql_query($sql);
	while ($row=mysql_fetch_assoc($res)) {
		$elements_number['trigger'][$row['Deal Trigger']]=$row['num'];
	}
	$sql=sprintf("select count(*) as num,`Deal Status` from  `Deal Dimension` $where  group by `Deal Status`");
	$res=mysql_query($sql);
	while ($row=mysql_fetch_assoc($res)) {
		$elements_number['status'][$row['Deal Status']]=$row['num'];
	}
	echo json_encode(array('state'=>200,'elements_numbers'=>$elements_number));


}

function get_campaign_elements_numbers($data) {
	$elements_number=array('Waiting'=>0,'Active'=>0,'Suspended'=>0,'Finish'=>0);
	$sql=sprintf("select count(*) as num,`Deal Campaign Status` from  `Deal Campaign Dimension` where `Deal Campaign Store Key`=%d group by `Deal Campaign Status`",$data['parent_key']);
	$res=mysql_query($sql);
	while ($row=mysql_fetch_assoc($res)) {
		$elements_number[$row['Deal Campaign Status']]=$row['num'];
	}
	echo json_encode(array('state'=>200,'elements_numbers'=>$elements_number));


}

function get_deal_component_elements_numbers($data) {
	$elements_number=array('Waiting'=>0,'Active'=>0,'Suspended'=>0,'Finish'=>0);

	switch ($data['parent']) {
	case 'deal':
		$where=sprintf('`Deal Component Deal Key`=%d',$data['parent_key']);

		break;
	default:
		$where=sprintf('`Deal Component Store Key`=%d',$data['parent_key']);
	}

	$sql=sprintf("select count(*) as num,`Deal Component Status` from  `Deal Component Dimension` where $where group by `Deal Component Status`");
	$res=mysql_query($sql);
	while ($row=mysql_fetch_assoc($res)) {
		$elements_number[$row['Deal Component Status']]=$row['num'];
	}
	echo json_encode(array('state'=>200,'elements_numbers'=>$elements_number));


}

function list_marketing_per_store() {

	global $user;

	$conf=$_SESSION['state']['stores']['marketing'];

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];

	if (isset( $_REQUEST['nr'])) {
		$number_results=$_REQUEST['nr'];
		if ($start_from>0) {
			$page=floor($start_from/$number_results);
			$start_from=$start_from-$page;
		}

	} else
		$number_results=$conf['nr'];





	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	if (isset( $_REQUEST['where']))
		$where=addslashes($_REQUEST['where']);
	else
		$where=$conf['where'];



	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;


	if (isset( $_REQUEST['percentages'])) {
		$percentages=$_REQUEST['percentages'];

	} else
		$percentages=$_SESSION['state']['stores']['marketing']['percentages'];



	if (isset( $_REQUEST['period'])) {
		$period=$_REQUEST['period'];

	} else
		$period=$_SESSION['state']['stores']['marketing']['period'];

	if (isset( $_REQUEST['avg'])) {
		$avg=$_REQUEST['avg'];

	} else
		$avg=$_SESSION['state']['stores']['marketing']['avg'];



	$_SESSION['state']['stores']['marketing']['percentage']=$percentages;
	$_SESSION['state']['stores']['marketing']['period']=$period;
	$_SESSION['state']['stores']['marketing']['avg']=$avg;
	$_SESSION['state']['stores']['marketing']['order']=$order;
	$_SESSION['state']['stores']['marketing']['order_dir']=$order_dir;
	$_SESSION['state']['stores']['marketing']['nr']=$number_results;
	$_SESSION['state']['stores']['marketing']['sf']=$start_from;
	$_SESSION['state']['stores']['marketing']['where']=$where;
	$_SESSION['state']['stores']['marketing']['f_field']=$f_field;
	$_SESSION['state']['stores']['marketing']['f_value']=$f_value;
	// print_r($_SESSION['tables']['families_list']);

	//  print_r($_SESSION['tables']['families_list']);

	if (count($user->stores)==0)
		$where="where false";
	else {
		$where=sprintf("where `Store Key` in (%s)",join(',',$user->stores));
	}


	$filter_msg='';
	$wheref='';
	if ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Store Name` like '%".addslashes($f_value)."%'";
	if ($f_field=='code'  and $f_value!='')
		$wheref.=" and  `Store Code` like '".addslashes($f_value)."%'";




	$sql="select count(*) as total from `Store Dimension`   $where $wheref";
	//print $sql;
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($result);

	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from `Store Dimension`   $where ";

		$result=mysql_query($sql);
		if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($result);

	}


	$rtext=number($total_records)." ".ngettext('store','stores',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp=' ('._('Showing all').')';

	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any store with code like ")." <b>".$f_value."*</b> ";
			break;
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any store with name like ")." <b>*".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('stores with code like')." <b>".$f_value."*</b>";
			break;
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('stores with name like')." <b>*".$f_value."*</b>";
			break;
		}
	}
	else
		$filter_msg='';

	$_dir=$order_direction;
	$_order=$order;


	if ($order=='code')
		$order='`Store Code`';
	elseif ($order=='name')
		$order='`Store Name`';

	elseif ($order=='ecampaigns')
		$order='`Store Email Campaigns`';

	elseif ($order=='newsletters')
		$order='`Store Newsletter`s';

	elseif ($order=='reminders')
		$order='`Store Active Email Reminders`';

	elseif ($order=='campaigns')
		$order='`Store Active Deal Campaigns`';
	elseif ($order=='deals')
		$order='`Store Active Deals`';
	else
		$order='`Store Code`';

	$total_customers=0;





	$sql="select `Store Active Deals`,`Store Active Deal Campaigns`,`Store Active Email Reminders`,`Store Newsletters`,`Store Email Campaigns`,`Store Name`,`Store Code`,`Store Key` from  `Store Dimension`    $where $wheref  order by $order $order_direction limit $start_from,$number_results    ";
	$res = mysql_query($sql);
	//print $sql;
	$total=mysql_num_rows($res);

	$total_ecampaigns=0;
	$total_newsletters=0;
	$total_reminders=0;
	$total_campaigns=0;
	$total_deals=0;

	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$name=sprintf('<a href="marketing.php?store=%d">%s</a>',$row['Store Key'],$row['Store Name']);
		$code=sprintf('<a href="marketing.php?store=%d">%s</a>',$row['Store Key'],$row['Store Code']);

		$total_ecampaigns+=$row['Store Email Campaigns'];
		$total_newsletters+=$row['Store Newsletters'];
		$total_reminders+=$row['Store Active Email Reminders'];
		$total_campaigns+=$row['Store Active Deal Campaigns'];
		$total_deals+=$row['Store Active Deals'];


		$adata[]=array(
			'code'=>$code,
			'name'=>$name,
			'ecampaigns'=>number($row['Store Email Campaigns']) ,
			'newsletters'=>number($row['Store Newsletters']),
			'reminders'=>number($row['Store Active Email Reminders']),
			'campaigns'=>number($row['Store Active Deal Campaigns']),
			'deals'=>number($row['Store Active Deals'])


		);
	}
	mysql_free_result($res);


	if ($total_records<=$number_results) {

		$adata[]=array(
			'name'=>'',
			'code'=>_('Total'),
			'name'=>'',
			'ecampaigns'=>number($total_ecampaigns) ,
			'newsletters'=>number($total_newsletters),
			'reminders'=>number($total_reminders),
			'campaigns'=>number($total_campaigns),
			'deals'=>number($total_deals)


		);
	}else {
		$adata[]=array();
	}

	// if($total<$number_results)
	//  $rtext=$total.' '.ngettext('store','stores',$total);
	//else
	//  $rtext='';

	$total_records=ceil($total_records/$number_results)+$total_records;

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
		)
	);
	echo json_encode($response);
}


function is_campaign_name_in_store($data) {
	$store_key=$data['store_key'];
	$sql=sprintf("select `Deal Campaign Key`,`Deal Campaign Name`  from `Deal Campaign Dimension` where `Deal Campaign Store Key`=%d and `Deal Campaign Name`=%s  ",
		$store_key,
		prepare_mysql($data['query'])

	);

	$res=mysql_query($sql);
	if ($row=mysql_fetch_assoc($res)) {

		$msg=sprintf('%s <a href="campaign.php?id=%d">%s</a>',
			_('Another campaign already has this name'),
			$row['Deal Campaign Key'],
			$row['Deal Campaign Name']
		);

		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	}else {
		$response= array(
			'state'=>200,
			'found'=>0,

		);
		echo json_encode($response);
		return;

	}
}

function is_deal_name_in_store($data) {
	$store_key=$data['store_key'];
	$sql=sprintf("select `Deal Key`,`Deal Name`  from `Deal Dimension` where `Deal Store Key`=%d and `Deal Name`=%s  ",
		$store_key,
		prepare_mysql($data['query'])

	);

	$res=mysql_query($sql);
	if ($row=mysql_fetch_assoc($res)) {

		$msg=sprintf('%s <a href="deal.php?id=%d">%s</a>',
			_('Another deal already has this name'),
			$row['Deal Key'],
			$row['Deal Name']
		);

		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	}else {
		$response= array(
			'state'=>200,
			'found'=>0,

		);
		echo json_encode($response);
		return;

	}
}


function is_voucher_code_in_store($data) {
	$store_key=$data['store_key'];
	$sql=sprintf("select `Voucher Key`,`Voucher Code`  from `Voucher Dimension` where `Voucher Store Key`=%d and `Voucher Code`=%s and `Voucher Status`='Active' ",
		$store_key,
		prepare_mysql($data['query'])

	);

	$res=mysql_query($sql);
	if ($row=mysql_fetch_assoc($res)) {

		$msg=sprintf('%s <a href="voucher.php?id=%d">%s</a>',
			_('Another voucher already has this code'),
			$row['Voucher Key'],
			$row['Voucher Code']
		);

		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	}else {
		$response= array(
			'state'=>200,
			'found'=>0,

		);
		echo json_encode($response);
		return;

	}


}



?>
