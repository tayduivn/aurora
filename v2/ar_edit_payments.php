<?php
require_once 'common.php';
require_once 'ar_edit_common.php';
require_once 'class.Order.php';
require_once 'class.Staff.php';
include_once 'class.Payment.php';
include_once 'class.Payment_Account.php';
include_once 'class.Payment_Service_Provider.php';

require_once 'class.User.php';
include_once 'class.PartLocation.php';
require_once 'utils/order_functions.php';



if (!isset($_REQUEST['tipo'])) {
	$response=array('state'=>407,'resp'=>'Non acceptable request (t)');
	echo json_encode($response);
	exit;
}


$tipo=$_REQUEST['tipo'];

switch ($tipo) {


case ('refund_payment'):
	$data=prepare_values($_REQUEST,array(
			'parent'=>array('type'=>'string'),

			'parent_key'=>array('type'=>'key'),
			'refund_reference'=>array('type'=>'string'),
			'refund_payment_method'=>array('type'=>'string'),
			'refund_amount'=>array('type'=>'numeric'),
			'payment_key'=>array('type'=>'key')




		));


	refund_payment($data);

	break;

case ('credit_payment'):
	$data=prepare_values($_REQUEST,array(
			'parent'=>array('type'=>'string'),

			'parent_key'=>array('type'=>'key'),
			'credit_reference'=>array('type'=>'string'),
			'credit_amount'=>array('type'=>'numeric'),
			'payment_key'=>array('type'=>'key')
		));


	credit_payment($data);

	break;

case('add_payment'):
	$data=prepare_values($_REQUEST,array(
			'parent'=>array('type'=>'string'),

			'parent_key'=>array('type'=>'key'),
			'payment_reference'=>array('type'=>'string'),
			'payment_method'=>array('type'=>'string'),
			'payment_amount'=>array('type'=>'numeric'),
			'payment_account_key'=>array('type'=>'key')




		));

	if ($data['parent']=='order') {
		add_payment_to_order($data);
	}elseif ($data['parent']=='invoice') {

		add_payment_to_invoice($data);
	}
	break;


case('set_payment_as_completed'):
	$data=prepare_values($_REQUEST,array(
			'payment_key'=>array('type'=>'key'),
			'payment_transaction_id'=>array('type'=>'string')

		));
	set_payment_as_completed($data);
	break;

case('cancel_pending_payment'):
	$data=prepare_values($_REQUEST,array(
			'payment_key'=>array('type'=>'key'),
			'order_key'=>array('type'=>'key')
		));
	cancel_pending_payment($data);
	break;
case('cancel_payment'):
	$data=prepare_values($_REQUEST,array(
			'payment_key'=>array('type'=>'key'),
			'order_key'=>array('type'=>'key'),
			'status_info'=>array('type'=>'string')
		));
	cancel_payment($data);
	break;


default:
	$response=array('state'=>404,'resp'=>'Operation not found');
	echo json_encode($response);

}


function cancel_payment($data) {

	$payment_key=$data['payment_key'];
	$payment=new Payment($payment_key);
	$order=new Order($payment->data['Payment Order Key']);
	$invoice=new Invoice($payment->data['Payment Invoice Key']);



	if (!$payment->id) {

		$pending_payments=count($order->get_payment_keys('Pending'));


		$response=array(
			'state'=>201,
			'msg'=>'error: payment dont exists',
			'type_error'=>'invalid_payment_key',
			'payment_key'=>$data['payment_key'],
			'pending_payments'=>$pending_payments,
			'status'=>'Deleted',
			'created_time_interval'=>0,
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']


		);
		echo json_encode($response);
		return;
	}

	if ($payment->data['Payment Transaction Status']!='Completed') {
		$response=array(
			'state'=>201,
			'msg'=>'error: payment not completed. '.$payment->data['Payment Transaction Status'],
			'type_error'=>'invalid_payment_status',
			'payment_key'=>$payment->id,
			'status'=>$payment->data['Payment Transaction Status'],
			'created_time_interval'=>0,
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']

		);
		echo json_encode($response);
		return;
	}
	if ($payment->data['Payment Submit Type']!='Manual') {
		$response=array(
			'state'=>201,
			'msg'=>'error: payment submit type is not Manual. ('.$payment->data['Payment Transaction Status'].')',
			'type_error'=>'invalid_submit_type_status',
			'payment_key'=>$payment->id,
			'status'=>$payment->data['Payment Submit Type'],
			'created_time_interval'=>0,
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']

		);
		echo json_encode($response);
		return;
	}


	$customer=new Customer($payment->data['Payment Customer Key']);
	$customer->update_field_switcher('Customer Account Balance',$customer->data['Customer Account Balance']+$payment->data['Payment Amount'],'no_history');


	$data_to_update=array(

		'Payment Completed Date'=>'',
		'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Cancelled Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Transaction Status'=>'Cancelled',
		'Payment Transaction Status Info'=>$data['status_info'],


	);
	$payment->update($data_to_update);
	$payment->update_balance();







	$sql=sprintf("delete from `Invoice Payment Bridge` where `Payment Key`=%d ",$payment->id);
	mysql_query($sql);

	$sql=sprintf("select `Payment Key` from  `Payment Dimension` where `Payment Key`=%d  ",$payment->data['Payment Related Payment Key']);
	$res=mysql_query($sql);
	if ($row=mysql_fetch_assoc($res)) {
		$_payment=new Payment($row['Payment Key']);
		$_payment->update_balance();
	}


	$order->update_payment_state();
	if ($invoice->id) {
		$invoice->update_payment_state();
	}
	$response=array(
		'state'=>200,
		'payment_key'=>$payment->id,
		'status'=>$payment->data['Payment Transaction Status'],
		'created_time_interval'=>$payment->get_formated_time_lapse('Created Date'),
		'order_dispatch_status'=>$order->data['Order Current Dispatch State']
	);









	echo json_encode($response);
	return;




}

function cancel_pending_payment($data) {

	$payment_key=$data['payment_key'];
	$payment=new Payment($payment_key);
	$order=new Order($data['order_key']);



	if (!$payment->id) {

		$pending_payments=count($order->get_payment_keys('Pending'));


		$response=array(
			'state'=>201,
			'msg'=>'error: payment dont exists',
			'type_error'=>'invalid_payment_key',
			'payment_key'=>$data['payment_key'],
			'pending_payments'=>$pending_payments,
			'status'=>'Deleted',
			'created_time_interval'=>0,
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']


		);
		echo json_encode($response);
		return;
	}

	if ($payment->data['Payment Transaction Status']!='Pending') {
		$pending_payments=count($order->get_payment_keys('Pending'));
		$response=array(
			'state'=>201,
			'msg'=>'error: payment not pending. '.$payment->data['Payment Transaction Status'],
			'type_error'=>'invalid_payment_status',
			'payment_key'=>$payment->id,
			'pending_payments'=>$pending_payments,
			'status'=>$payment->data['Payment Transaction Status'],
			'created_time_interval'=>0,
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']

		);
		echo json_encode($response);
		return;
	}

	$data_to_update=array(

		'Payment Completed Date'=>'',
		'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Cancelled Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Transaction Status'=>'Cancelled',
		'Payment Transaction Status Info'=>_('Cancelled by user'),


	);
	$payment->update($data_to_update);

	if ($payment->data['Payment Method']=='Account') {
		$customer = new Customer($payment->data['Payment Customer Key']);
		$customer->update_field_switcher('Customer Account Balance',round($customer->data['Customer Account Balance']+$payment->data['Payment Amount'],2));
		$order->update_field_switcher('Order Apply Auto Customer Account Payment','No');


	}


	$pending_payments=count($order->get_payment_keys('Pending'));

	if ($pending_payments==0) {

		if (  count($order->get_payment_keys('Completed'))) {

			$order->set_as_in_process();
		}else {

			$order->checkout_cancel_payment();
		}
	}

	if (!$payment->id) {
		$response=array(
			'state'=>200,
			'payment_key'=>$data['payment_key'],
			'pending_payments'=>$pending_payments,
			'status'=>'Deleted',
			'created_time_interval'=>0,
			'msg'=>'error: payment dont exists',
			'type_error'=>'invalid_payment_key',
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']

		);
	}else {

		$response=array(
			'state'=>200,
			'payment_key'=>$payment->id,
			'pending_payments'=>$pending_payments,
			'status'=>$payment->data['Payment Transaction Status'],
			'created_time_interval'=>$payment->get_formated_time_lapse('Created Date'),
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']
		);
	}








	echo json_encode($response);
	return;




}


function set_payment_as_completed($data) {

	include_once 'send_confirmation_email_function.php';


	$payment_transaction_id=$data['payment_transaction_id'];
	$payment_key=$data['payment_key'];

	$payment=new Payment($payment_key);
	$payment_account=new Payment_Account($payment->data['Payment Account Key']);
	$order_key=$payment->data['Payment Order Key'];
	$order=new Order($order_key);

	if (!$payment->id) {

		$pending_payments=count($order->get_payment_keys('Pending'));


		$response=array(
			'state'=>201,
			'msg'=>'error: payment dont exists',
			'type_error'=>'invalid_payment_key',
			'payment_key'=>$data['payment_key'],
			'pending_payments'=>$pending_payments,
			'status'=>'Deleted',
			'created_time_interval'=>0,
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']


		);
		echo json_encode($response);
		return;
	}

	if ($payment->data['Payment Transaction Status']!='Pending') {
		$pending_payments=count($order->get_payment_keys('Pending'));
		$response=array(
			'state'=>201,
			'msg'=>'error: payment not pending. '.$payment->data['Payment Transaction Status'],
			'type_error'=>'invalid_payment_status',
			'payment_key'=>$payment->id,
			'pending_payments'=>$pending_payments,
			'status'=>$payment->data['Payment Transaction Status'],
			'created_time_interval'=>0,
			'order_dispatch_status'=>$order->data['Order Current Dispatch State']

		);
		echo json_encode($response);
		return;
	}





	$data_to_update=array(

		'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Transaction Status'=>'Completed',
		'Payment Transaction ID'=>$payment_transaction_id,

	);


	$payment->update($data_to_update);
	$order=new Order($payment->data['Payment Order Key']);

	$order->update(
		array(
			'Order Payment Account Key'=>$payment_account->id,
			'Order Payment Account Code'=>$payment_account->data['Payment Account Code'],
			'Order Payment Method'=>$payment_account->data['Payment Type'],
			'Order Payment Key'=>$payment->id,
			'Order Checkout Completed Payment Date'=>gmdate('Y-m-d H:i:s')
		));

	$order->checkout_submit_order();

	send_confirmation_email($order);

	$updated_data=array(
		'order_items_gross'=>$order->get('Items Gross Amount'),
		'order_items_discount'=>$order->get('Items Discount Amount'),
		'order_items_net'=>$order->get('Items Net Amount'),
		'order_net'=>$order->get('Total Net Amount'),
		'order_tax'=>$order->get('Total Tax Amount'),
		'order_charges'=>$order->get('Charges Net Amount'),
		'order_credits'=>$order->get('Net Credited Amount'),
		'order_shipping'=>$order->get('Shipping Net Amount'),
		'order_total'=>$order->get('Total Amount'),
		'order_total_paid'=>$order->get('Payments Amount'),
		'order_total_to_pay'=>$order->get('To Pay Amount')

	);

	$payments_data=array();
	foreach ($order->get_payment_objects('',true,true) as $payment) {
		$payments_data[$payment->id]=array(
			'date'=>$payment->get('Created Date'),
			'amount'=>$payment->get('Amount'),
			'status'=>$payment->get('Payment Transaction Status')
		);
	}

	$response=array('state'=>200,
		'result'=>'updated',
		'order_shipping_method'=>$order->data['Order Shipping Method'],
		'data'=>$updated_data,

		'tax_info'=>$order->get_formated_tax_info_with_operations(),
		'payments_data'=>$payments_data,
		'order_total_paid'=>$order->data['Order Payments Amount'],
		'order_total_to_pay'=>$order->data['Order To Pay Amount']
	);

	echo json_encode($response);


}



function add_payment_to_invoice($data) {

	global $user;

	$invoice=new Invoice($data['parent_key']);
	$payment_account=new Payment_Account($data['payment_account_key']);

	$orders_invoice=$invoice->get_orders_ids();

	$order_key=array_pop($orders_invoice);

	$order=new Order($order_key);


	if (!$invoice->id) {
		$response=array('state'=>400,'msg'=>'error: order dont exists','type_error'=>'invalid_invoice_key');
		echo json_encode($response);
		return;
	}



	if (!$payment_account->id) {
		$response=array('state'=>400,'msg'=>'error: payment account dont exists','type_error'=>'invalid_payment_account_keyy');
		echo json_encode($response);
		return;
	}

	if (!$payment_account->in_store($order->data['Order Store Key'])) {
		$response=array('state'=>400,'msg'=>'error: payment account not in this site','type_error'=>'payment_account_not_in_store');
		echo json_encode($response);
		return;
	}


	$payment_service_provider=new Payment_Service_Provider($payment_account->data['Payment Service Provider Key']);




	$payment_data=array(
		'Payment Account Key'=>$payment_account->id,
		'Payment Account Code'=>$payment_account->data['Payment Account Code'],
		'Payment Service Provider Key'=>$payment_account->data['Payment Service Provider Key'],
		'Payment Order Key'=>$order_key,
		'Payment Store Key'=>$invoice->data['Invoice Store Key'],
		'Payment Customer Key'=>$invoice->data['Invoice Customer Key'],
		'Payment Submit Type'=>'Manual',
		'Payment User Key'=>$user->id,
		'Payment Balance'=>$data['payment_amount'],
		'Payment Amount'=>$data['payment_amount'],
		'Payment Refund'=>0,
		'Payment Currency Code'=>$invoice->data['Invoice Currency'],
		'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Created Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Transaction Status'=>'Completed',
		'Payment Transaction ID'=>$data['payment_reference'],
		'Payment Method'=>$data['payment_method'],

	);

	$payment=new Payment('create',$payment_data);

	$payment_balance=$invoice->apply_payment($payment);



	$sql=sprintf("insert into `Order Payment Bridge` values (%d,%d,%d,%d,%.2f,'No') ON DUPLICATE KEY UPDATE `Amount`=%.2f ",
		$order_key,
		$payment->id,
		$payment_account->id,
		$payment_account->data['Payment Service Provider Key'],
		$payment->data['Payment Amount'],
		$payment->data['Payment Amount']
	);
	mysql_query($sql);



	$order->update_payment_state();

	$updated_data=array(

		'invoice_total_paid'=>$invoice->get('Paid Amount'),
		'invoice_total_to_pay'=>$invoice->get('Outstanding Total Amount')

	);

	$payments_data=array();
	foreach ($invoice->get_payment_objects('',true,true) as $payment) {
		$payments_data[$payment->id]=array(
			'date'=>$payment->get('Created Date'),
			'amount'=>$payment->get('Amount'),
			'status'=>$payment->get('Payment Transaction Status')
		);
	}



	$response=array('state'=>200,
		'result'=>'updated',
		'data'=>$updated_data,
		'payments_data'=>$payments_data,
		'invoice_total_paid'=>$invoice->data['Invoice Paid Amount'],
		'invoice_total_to_pay'=>$invoice->data['Invoice Outstanding Total Amount']
	);

	echo json_encode($response);

}

function add_payment_to_order($data) {

	global $user;

	$order=new Order($data['parent_key']);
	$payment_account=new Payment_Account($data['payment_account_key']);

	if (!$order->id) {
		$response=array('state'=>400,'msg'=>'error: order dont exists','type_error'=>'invalid_order_key');
		echo json_encode($response);
		return;
	}



	if (!$payment_account->id) {
		$response=array('state'=>400,'msg'=>'error: payment account dont exists','type_error'=>'invalid_payment_account_keyy');
		echo json_encode($response);
		return;
	}

	if (!$payment_account->in_store($order->data['Order Store Key'])) {
		$response=array('state'=>400,'msg'=>'error: payment account not in this site','type_error'=>'payment_account_not_in_store');
		echo json_encode($response);
		return;
	}


	$payment_service_provider=new Payment_Service_Provider($payment_account->data['Payment Service Provider Key']);






	$payment_data=array(
		'Payment Account Key'=>$payment_account->id,
		'Payment Account Code'=>$payment_account->data['Payment Account Code'],
		'Payment Service Provider Key'=>$payment_account->data['Payment Service Provider Key'],
		'Payment Order Key'=>$order->id,
		'Payment Store Key'=>$order->data['Order Store Key'],
		'Payment Customer Key'=>$order->data['Order Customer Key'],
		'Payment Submit Type'=>'Manual',
		'Payment User Key'=>$user->id,
		'Payment Balance'=>$data['payment_amount'],
		'Payment Amount'=>$data['payment_amount'],
		'Payment Refund'=>0,
		'Payment Currency Code'=>$order->data['Order Currency'],
		'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Created Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Transaction Status'=>'Completed',
		'Payment Transaction ID'=>$data['payment_reference'],
		'Payment Method'=>$data['payment_method'],

	);

	$payment=new Payment('create',$payment_data);

	$sql=sprintf("insert into `Order Payment Bridge` values (%d,%d,%d,%d,%.2f,'No') ON DUPLICATE KEY UPDATE `Amount`=%.2f ",
		$order->id,
		$payment->id,
		$payment_account->id,
		$payment_account->data['Payment Service Provider Key'],
		$payment->data['Payment Amount'],
		$payment->data['Payment Amount']
	);
	mysql_query($sql);


	$order->update_payment_state();


	$updated_data=array(
		'order_items_gross'=>$order->get('Items Gross Amount'),
		'order_items_discount'=>$order->get('Items Discount Amount'),
		'order_items_net'=>$order->get('Items Net Amount'),
		'order_net'=>$order->get('Total Net Amount'),
		'order_tax'=>$order->get('Total Tax Amount'),
		'order_charges'=>$order->get('Charges Net Amount'),
		'order_credits'=>$order->get('Net Credited Amount'),
		'order_shipping'=>$order->get('Shipping Net Amount'),
		'order_total'=>$order->get('Total Amount'),
		'order_total_paid'=>$order->get('Payments Amount'),
		'order_total_to_pay'=>$order->get('To Pay Amount')

	);

	$payments_data=array();
	foreach ($order->get_payment_objects('',true,true) as $payment) {
		$payments_data[$payment->id]=array(
			'date'=>$payment->get('Created Date'),
			'amount'=>$payment->get('Amount'),
			'status'=>$payment->get('Payment Transaction Status')
		);
	}



	$response=array('state'=>200,
		'result'=>'updated',
		'order_shipping_method'=>$order->data['Order Shipping Method'],
		'data'=>$updated_data,
		'shipping'=>money($order->new_value),
		'shipping_amount'=>$order->data['Order Shipping Net Amount'],
		'ship_to'=>$order->get('Order XHTML Ship Tos'),
		'tax_info'=>$order->get_formated_tax_info_with_operations(),
		'payments_data'=>$payments_data,
		'order_total_paid'=>$order->data['Order Payments Amount'],
		'order_total_to_pay'=>$order->data['Order To Pay Amount']
	);

	echo json_encode($response);

}

function credit_payment($data) {




	global $user;

	$credit_amount=round($data['credit_amount'],2);




	$payment=new Payment($data['payment_key']);
	$payment->load_payment_account();
	$payment->load_payment_service_provider();





	$payment->update(array(
			'Payment Refund'=>round($payment->data['Payment Refund']+$credit_amount,2)
		));


	$store=new Store($payment->data['Payment Store Key']);

	$payment_account=new Payment_Account($store->get_payment_account_key());


	$payment_data=array(
		'Payment Account Key'=>$payment_account->data['Payment Account Key'],
		'Payment Account Code'=>$payment_account->data['Payment Account Code'],
		'Payment Type'=>'Credit',

		'Payment Service Provider Key'=>$payment_account->data['Payment Service Provider Key'],
		'Payment Order Key'=>$payment->data['Payment Order Key'],
		'Payment Store Key'=>$payment->data['Payment Store Key'],
		'Payment Customer Key'=>$payment->data['Payment Customer Key'],

		'Payment Balance'=>$credit_amount,
		'Payment Amount'=>$credit_amount,
		'Payment Refund'=>0,
		'Payment Currency Code'=>$payment->data['Payment Currency Code'],
		'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Created Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Transaction Status'=>'Completed',
		'Payment Transaction ID'=>'',
		'Payment Method'=>'Account',
		'Payment Related Payment Key'=>$payment->id,
		'Payment Related Payment Transaction ID'=>'',
		'Payment Submit Type'=>'Manual',
		'Payment User Key'=>$user->id,

	);

	$refund_payment=new Payment('create',$payment_data);
	$customer=new Customer($payment->data['Payment Customer Key']);
	$customer->update_field_switcher('Customer Account Balance',$customer->data['Customer Account Balance']-$credit_amount,'no_history');

	$refund_payment->load_payment_account();


	$order=new Order($payment->data['Payment Order Key']);
	if ($order->id) {

		$sql=sprintf("insert into `Order Payment Bridge` values (%d,%d,%d,%d,%.2f,'No') ON DUPLICATE KEY UPDATE `Amount`=%.2f ",
			$order->id,
			$refund_payment->id,
			$refund_payment->payment_account->id,
			$refund_payment->payment_account->data['Payment Service Provider Key'],
			$refund_payment->data['Payment Amount'],
			$refund_payment->data['Payment Amount']
		);
		mysql_query($sql);




		$order->update_totals();
		$order->update_payment_state();

		$updated_data=array(
			'order_items_gross'=>$order->get('Items Gross Amount'),
			'order_items_discount'=>$order->get('Items Discount Amount'),
			'order_items_net'=>$order->get('Items Net Amount'),
			'order_net'=>$order->get('Total Net Amount'),
			'order_tax'=>$order->get('Total Tax Amount'),
			'order_charges'=>$order->get('Charges Net Amount'),
			'order_credits'=>$order->get('Net Credited Amount'),
			'order_shipping'=>$order->get('Shipping Net Amount'),
			'order_total'=>$order->get('Total Amount'),
			'order_total_paid'=>$order->get('Payments Amount'),
			'order_total_to_pay'=>$order->get('To Pay Amount')

		);

		$payments_data=array();
		foreach ($order->get_payment_objects('',true,true) as $payment) {
			$payments_data[$payment->id]=array(
				'date'=>$payment->get('Created Date'),
				'amount'=>$payment->get('Amount'),
				'status'=>$payment->get('Payment Transaction Status')
			);
		}


		$response=array('state'=>200,
			'result'=>'updated',
			'order_shipping_method'=>$order->data['Order Shipping Method'],
			'data'=>$updated_data,
			'shipping'=>money($order->new_value),
			'shipping_amount'=>$order->data['Order Shipping Net Amount'],
			'ship_to'=>$order->get('Order XHTML Ship Tos'),
			'tax_info'=>$order->get_formated_tax_info_with_operations(),
			'payments_data'=>$payments_data,
			'order_total_paid'=>$order->data['Order Payments Amount'],
			'order_total_to_pay'=>$order->data['Order To Pay Amount']
		);

		echo json_encode($response);
	}


	if ($data['parent']=='invoice') {
		$invoice=new Invoice($data['parent_key']);
	}else {
		$invoice=new Invoice($payment->data['Payment Invoice Key']);
	}


	if ($invoice->id) {
		$invoice->apply_payment($refund_payment);
	}


}



function refund_payment($data) {

	global $user;

	$refund_amount=round($data['refund_amount'],2);

	$payment=new Payment($data['payment_key']);
	$payment->load_payment_account();
	$payment->load_payment_service_provider();
	if ($data['refund_payment_method']=='online') {

		if ($payment->payment_account->data['Payment Account Online Refund']=='Yes') {

			switch ($payment->payment_service_provider->data['Payment Service Provider Code']) {
			case 'Paypal':
				$refunded_data=online_paypal_refund($refund_amount,$payment);
				break;
			case 'Worldpay':
				$refunded_data=online_worldpay_refund($refund_amount,$payment);
				break;
			case 'BTree':
				$refunded_data=online_braintree_refund($refund_amount,$payment);
				break;
			default:
				$response=array('state'=>400,'msg'=>"Error 2. Payment account can't do online refunds");
				echo json_encode($response);
				return;
			}

		}else {
			$response=array('state'=>400,'msg'=>"Payment account can't do online refunds");
			echo json_encode($response);
			return;
		}

	}else {

		$refunded_data=array(
			'status'=>'Completed',
			'reference'=>$data['refund_reference'],
			'submit_type'=>'Manual'
		);

	}

	$order=new Order($payment->data['Payment Order Key']);



	$payment->update(array(
			'Payment Refund'=>round($payment->data['Payment Refund']+$refund_amount,2)
		));

	$payment_data=array(
		'Payment Account Key'=>$payment->data['Payment Account Key'],
		'Payment Account Code'=>$payment->data['Payment Account Code'],
		'Payment Type'=>'Refund',

		'Payment Service Provider Key'=>$payment->data['Payment Service Provider Key'],
		'Payment Order Key'=>$order->id,
		'Payment Store Key'=>$payment->data['Payment Store Key'],
		'Payment Customer Key'=>$payment->data['Payment Customer Key'],

		'Payment Balance'=>$refund_amount,
		'Payment Amount'=>$refund_amount,
		'Payment Refund'=>0,
		'Payment Currency Code'=>$payment->data['Payment Currency Code'],
		'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Created Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
		'Payment Transaction Status'=>$refunded_data['status'],
		'Payment Transaction ID'=>$refunded_data['reference'],
		'Payment Method'=>$payment->data['Payment Method'],
		'Payment Related Payment Key'=>$payment->id,
		'Payment Related Payment Transaction ID'=>$payment->data['Payment Transaction ID'],
		'Payment Submit Type'=>$refunded_data['submit_type'],
		'Payment User Key'=>$user->id,
	);

	$refund_payment=new Payment('create',$payment_data);

	$refund_payment->load_payment_account();


	if ($order->id) {

		$sql=sprintf("insert into `Order Payment Bridge` values (%d,%d,%d,%d,%.2f,'No') ON DUPLICATE KEY UPDATE `Amount`=%.2f ",
			$order->id,
			$refund_payment->id,
			$refund_payment->payment_account->id,
			$refund_payment->payment_account->data['Payment Service Provider Key'],
			$refund_payment->data['Payment Amount'],
			$refund_payment->data['Payment Amount']
		);
		mysql_query($sql);
		$order->update_payment_state();


		$updated_data=array(
			'order_items_gross'=>$order->get('Items Gross Amount'),
			'order_items_discount'=>$order->get('Items Discount Amount'),
			'order_items_net'=>$order->get('Items Net Amount'),
			'order_net'=>$order->get('Total Net Amount'),
			'order_tax'=>$order->get('Total Tax Amount'),
			'order_charges'=>$order->get('Charges Net Amount'),
			'order_credits'=>$order->get('Net Credited Amount'),
			'order_shipping'=>$order->get('Shipping Net Amount'),
			'order_total'=>$order->get('Total Amount'),
			'order_total_paid'=>$order->get('Payments Amount'),
			'order_total_to_pay'=>$order->get('To Pay Amount')

		);

		$payments_data=array();
		foreach ($order->get_payment_objects('',true,true) as $payment) {
			$payments_data[$payment->id]=array(
				'date'=>$payment->get('Created Date'),
				'amount'=>$payment->get('Amount'),
				'status'=>$payment->get('Payment Transaction Status')
			);
		}


		$order->apply_payment_from_customer_account();
		$response=array('state'=>200,
			'result'=>'updated',
			'order_shipping_method'=>$order->data['Order Shipping Method'],
			'data'=>$updated_data,
			'shipping'=>money($order->new_value),
			'shipping_amount'=>$order->data['Order Shipping Net Amount'],
			'ship_to'=>$order->get('Order XHTML Ship Tos'),
			'tax_info'=>$order->get_formated_tax_info_with_operations(),
			'payments_data'=>$payments_data,
			'order_total_paid'=>$order->data['Order Payments Amount'],
			'order_total_to_pay'=>$order->data['Order To Pay Amount']
		);

		echo json_encode($response);
	}

	if ($data['parent']=='invoice') {
		$invoice=new Invoice($data['parent_key']);
	}else {
		$invoice=new Invoice($payment->data['Payment Invoice Key']);
	}


	if ($invoice->id) {
		$invoice->apply_payment($payment);
	}


}


function online_paypal_refund($refund_amount,$payment) {
	require_once 'class.PaypalRefund.php';

	$aryData['transactionID'] = $payment->data['Payment Transaction ID'];   //Payment Transaction ID   1JR99805457778808
	$aryData['refundType'] = "Partial"; //Partial or Full   can do full one as Partial if we want still works
	$aryData['currencyCode'] =$payment->data['Payment Currency Code'];    //Payment Currency Code
	$aryData['amount'] = round(-1.0*$refund_amount,2);
	$aryData['memo'] = _("Refund");  //what ever we want to say back to the customer about the refunds
	//  $aryData['invoiceID'] = "Order:00053";

	$ref = new PayPalRefund(
		$payment->payment_account->data['Payment Account Refund Login'],
		$payment->payment_account->data['Payment Account Refund Password'],
		$payment->payment_account->data['Payment Account Refund Signature'],
		$payment->payment_account->data['Payment Account Refund URL Link']
	);


	$aryRes = $ref->refundAmount($aryData);


	if ($aryRes['ACK'] == "Success") {

		$refunded_data=array(
			'status'=>'Completed',
			'reference'=>$aryRes['REFUNDTRANSACTIONID'],
			'submit_type'=>'EPS'
		);

	}else {
		$refunded_data=array(
			'status'=>'Error',
			'reference'=>$aryRes['L_LONGMESSAGE0'],
			'submit_type'=>'EPS'
		);
	}

	return $refunded_data;

}

function online_worldpay_refund($refund_amount,$payment) {


	$url =$payment->payment_account->data['Payment Account Refund URL Link'];

	$authPW = $payment->payment_account->data['Payment Account Refund Password'];

	$instId =$payment->payment_account->data['Payment Account Refund Login'];

	$cartId = "Refund";  // always the same
	$testMode = "0";   // 0 when live

	$amount = round(-1.0*$refund_amount,2); // amount of refund
	$normalAmount = $amount;
	$op= "refund-partial";  // for full refund change to 'refund-full' and  amount  ='';
	$transId = $payment->data['Payment Transaction ID']; //  Payment Transaction ID
	$Currency = $payment->data['Payment Currency Code'];  //Payment Currency Code
	$startDelayUnit = 4;  // always the same
	$startDelayMult = 1; // always the same
	$intervalMult = 1;   // always the same
	$intervalUnit = 4;  // always the same
	$option = 0;        // always the same


	$sigNotMd5 = $payment->payment_account->data['Payment Account Refund Signature'];



	$signature = $sigNotMd5 . ":".$instId.":" . $Currency . ":" . $amount;
	$signature = md5($signature);

	$request=sprintf("https://%s?authPW=%s&instId=%s&cartId=%s&testMode=%s&signature=%s&normalAmount=%s&op=%s&transId=%s&amount=%s&currency=%s&startDelayUnit=%s&startDelayMult=%s&intervalMult=%s&intervalUnit=%s&option=%s",
		$url,
		$authPW,
		$instId,
		$cartId,
		$testMode,
		$signature,
		$normalAmount,
		$op,
		$transId,
		$amount,
		$Currency,
		$startDelayUnit,
		$startDelayMult,
		$intervalMult,
		$intervalUnit,
		$option
	);

	//$request=urlencode($request);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $request);
	curl_setopt($ch, CURLOPT_REFERER, $request);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch);
	curl_close($ch);



	$respond_array=preg_split('/\,/',$response);
	if (count($respond_array)==3) {

		if ($respond_array[0]=="A") {

			if ($respond_array[1]!=$payment->data['Payment Transaction ID']) {
				$_ref=$respond_array[1];
			}else {
				$_ref='';
			}

			$refunded_data=array(
				'status'=>'Completed',
				'reference'=>$_ref,
				'submit_type'=>'EPS'
			);
		}elseif ($respond_array[0]=="N") {
			$refunded_data=array(
				'status'=>'Error',
				'reference'=>$respond_array[2],
				'submit_type'=>'EPS'
			);
		}else {
			$refunded_data=array(
				'status'=>'Error',
				'reference'=>'Unknown response:'.$response,
				'submit_type'=>'EPS'
			);

		}
	}else {
		$refunded_data=array(
			'status'=>'Error',
			'reference'=>'Wrong response:'.$response,
			'submit_type'=>'EPS'
		);

	}

	return $refunded_data;


}

function online_braintree_refund($refund_amount,$payment) {


	$refund_amount=-1*$refund_amount;
	require_once 'external_libs/braintree-php-3.2.0/lib/Braintree.php';

	Braintree_Configuration::environment('production');
	Braintree_Configuration::merchantId($payment->payment_account->get('Payment Account ID'));
	Braintree_Configuration::publicKey($payment->payment_account->get('Payment Account Login'));
	Braintree_Configuration::privateKey($payment->payment_account->get('Payment Account Password'));


	$result = Braintree_Transaction::refund($payment->data['Payment Transaction ID'], $refund_amount);

	//print_r($result);
	if ($result->success) {
		$refunded_data=array(
			'status'=>'Completed',
			'reference'=>$result->transaction->id,
			'submit_type'=>'EPS'
		);

	}else {

		if (isset($result->transaction->processorSettlementResponseText)) {
			$msg=$result->transaction->processorSettlementResponseText.' ('.$result->transaction->processorSettlementResponseCode.')';

		}else {
			$msg=$result->message;

		}
		$refunded_data=array(
			'status'=>'Error',
			'reference'=>$msg,
			'submit_type'=>'EPS'
		);

	}

	return $refunded_data;



}




?>
