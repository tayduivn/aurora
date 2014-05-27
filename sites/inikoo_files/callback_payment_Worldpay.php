<?php
/*

 About:
 Autor: Jonathan Hardi, supervied by is mastrer Raul Perusquia <raul@inikoo.com>

 Copyright (c) 2014, Inikoo
 Created: 27 May 2014 18:55:21 CEST, Malaga , Spain

 Version 2.0
*/

require_once 'common.php';
include_once 'class.Payment.php';

 

if (!isset($_POST['MC_Payment_Key']) or !isset($_POST['MC_PaymentAccountKey']) or !isset($_POST['transId']) or !isset($_POST['cartId']) or !isset($_POST['amount']) or !isset($_POST['currency'])  ) {
@mail("raul@inikoo.com", "WP testx1", var_export($_POST, true));
//@mail("raul@inikoo.com", "WP test2", var_export($_REQUEST, true));
	exit();
}

$rep_PaymentAccountKey = $_POST['MC_PaymentAccountKey'];
$payment_key = $_POST['MC_Payment_Key'];




$rep_transId = $_POST['transId'];
$rep_cartId = $_POST['cartId'];
$rep_amount = $_POST['amount'];
$rep_currency = $_POST['currency'];
$rep_description = $_POST['desc'];
$rep_name = $_POST['name'];
$rep_town = $_POST['town'];
$rep_postcode = $_POST['postcode'];
$rep_country = $_POST['country'];
$rep_email = $_POST['email'];
$rep_environment = $_POST['environment'];
$rep_password = $_POST['callbackPW'];
$rep_transaction_id = $_POST['transaction_id'];
$rep_card_type = $_POST['cardType'];
$rep_ip_address = $_POST['ip_address'];
$rep_company_name = $_POST['company_name'];
$rep_address_line_1 = $_POST['address_line_1'];
$rep_telephone = $_POST['telephone'];
$rep_fax = $_POST['fax'];
$rep_country_string = $_POST['country_string'];
$rep_timestamp = $_POST['timestamp'];
$rep_MC_uId = $_POST['MC_uId'];
$rep_MC_PaymentKey = $_POST['MC_orderId'];
$rep_transStatus = $_POST['transStatus'];


$payment=new Payment($payment_key);
$payment_account=new Payment_Account($payment->data['Payment Account Key']);

if ($payment->data['Payment Transaction ID'] == $rep_transId) {
	exit();
}

list ($valid,$error,$error_info)=check_if_valid($rep_password,$rep_transStatus,$rep_amount,$payment,$payment_account);

 @mail("raul@inikoo.com", "WP test", "$valid,$error,$error_info");

if ($valid) {



	if ($payment->data['Payment Transaction Status']=='Pending') {





		

		$data_to_update=array(
			'Payment Sender'=>$rep_name,
			'Payment Sender Country 2 Alpha Code'=>$rep_country,
			'Payment Sender Email'=>$rep_email,
			'Payment Sender Card Type'=>$rep_card_type,
			'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
			'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
			'Payment Transaction Status'=>'Completed',
			'Payment Transaction ID'=>$rep_transId,


		);
		
		
		

		
		
		$payment->update($data_to_update);
		$order=new Order($payment->data['Payment Order Key']);
		$order->checkout_submit_order();
	
	@mail("raul@inikoo.com", "is_valid", $order->msg);
	
}


}else {



}



function check_if_valid($rep_password,$rep_transStatus,$rep_amount,$payment,$payment_account) {

$valid=true;
$error='';
$error_info='';

if (!$payment->id) {
	$valid=false;
	$error_type='no_payment_found';
	$error_info=$payment->id;
	return array($valid,$error,$error_info);
}

if ($rep_password != md5($payment_account->data['Payment Account Response']) ) {
	$valid=false;
	$error_type='wrong_signature';
	$error_info=$payment->data['Payment Account Response'].'<<-->>'.$rep_password;

	return array($valid,$error,$error_info);

}

if ($rep_transStatus != 'Y' ) {
	$valid=false;
	$error_type='wrong_transStatus';
	$error_info='<<-->>'.$rep_transStatus;

	return array($valid,$error,$error_info);

}


if ($payment->data['Payment Balance'] != $rep_amount) {
	$valid=false;
	$error_type='payment_amount_not_match';
	$error_info=$payment->data['Payment Balance'].'<->'.$rep_amount;
	return array($valid,$error,$error_info);

}






return array($valid,$error,$error_info);

}



?>
