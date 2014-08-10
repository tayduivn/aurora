<?php




require_once 'common.php';
include_once 'class.Payment.php';
include_once 'class.Payment_Account.php';
include_once 'send_confirmation_email_function.php';




$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql(var_export($_REQUEST, true)));mysql_query($sql);

if (!isset($_POST['custom']) or !isset($_POST['mc_gross'])  ) {
	exit();
}

$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate

//@mail("raul@inikoo.com", "paypalshit req", "$req");

$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql($req));mysql_query($sql);


$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";


$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Host: www.paypal.com\r\n";  // www.paypal.com for a live site
$header .= "Content-Length: " . strlen($req) . "\r\n";
$header .= "Connection: close\r\n\r\n";

$fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);
//$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
// assign posted variables to local variables
$item_name = (isset($_POST['item_name'])?$_POST['item_name']:'');
$item_number = (isset($_POST['item_number'])?$_POST['item_number']:'');


$payment_status = (isset($_POST['payment_status'])?$_POST['payment_status']:'');

$payment_shipping = (isset($_POST['mc_shipping'])?$_POST['mc_shipping']:'');

$payment_amountG1 = (isset($_POST['mc_gross1'])?$_POST['mc_gross1']:'');



$payment_amount = (isset($_POST['mc_gross'])?$_POST['mc_gross']:'');


$payment_currency = (isset($_POST['mc_currency'])?$_POST['mc_currency']:'');



$payment_transaction_id = (isset($_POST['txn_id'])?$_POST['txn_id']:'');


$receiver_email = (isset($_POST['receiver_email'])?$_POST['receiver_email']:'');


$payer_email = (isset($_POST['payer_email'])?$_POST['payer_email']:'');



$This_invoice = (isset($_POST['invoice'])?$_POST['invoice']:'');
$payment_key = $_POST['custom'];
$This_txn_type =  (isset($_POST['txn_type'])?$_POST['txn_type']:'');


$txn_type = (isset($_POST['txn_type'])?$_POST['txn_type']:'');



$residence_country = (isset($_POST['residence_country'])?$_POST['residence_country']:'');


$payment_cycle = (isset($_POST['payment_cycle'])?$_POST['payment_cycle']:'');

$mc_fee = (isset($_POST['mc_fee'])?$_POST['mc_fee']:'');


$reason_code = (isset($_POST['reason_code'])?$_POST['reason_code']:'');



$payment_type = (isset($_POST['payment_type'])?$_POST['payment_type']:'');

$payment_memo = (isset($_POST['mc_gross1'])?$_POST['mc_gross1']:'');


$payment_address_country_code = (isset($_POST['residence_country'])?$_POST['residence_country']:'');



$payment_address_status = (isset($_POST['payer_status'])?$_POST['payer_status']:'');


$payment_first_name = (isset($_POST['first_name'])?$_POST['first_name']:'');

$payment_last_name =(isset($_POST['first_name'])?$_POST['first_name']:'');

$payment_payer_business_name = (isset($_POST['payer_business_name'])?$_POST['payer_business_name']:'');


$payment_payer_payment_status = (isset($_POST['payment_status'])?$_POST['payment_status']:'');





$payment_fullName = $payment_first_name.' '.$payment_last_name;


//$emailBack = $payment_payer_business_name."\n Amount=".$payment_amount."\n Email=".$receiver_email." PayPal ID=".$payment_transaction_id." Add State=".$payment_address_status;




if (!$fp) {
	// HTTP ERROR
	// @mail("raul@inikoo.com", "paypalshit res", "HTTP ERROR");
	$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql("HTTP ERROR"));mysql_query($sql);

} else {
	fputs($fp, $header . $req);


	while (!feof($fp)) {
		$res = fgets($fp, 1024);



		//@mail("raul@inikoo.com", "paypalshit res", "$res");
		$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql("Paypay:res= ->".$res."<-  ($payment_key) "));mysql_query($sql);


		if ( preg_match('/VERIFIED/',$res) ) {
			// check the payment_status is Completed
			$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql("Payment status: $payment_status"));mysql_query($sql);

			if ($payment_status == 'Completed' ) {

				$payment=new Payment($payment_key);
				$payment_account=new Payment_Account($payment->data['Payment Account Key']);

				//@mail("raul@inikoo.com", "paypalshit", "$receiver_email,$payment_amount,$payment_currency,$payment,$payment_account");
				list ($valid,$error,$error_info)=check_if_valid($receiver_email,$payment_amount,$payment_currency,$payment,$payment_account);

				$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql("Paypal chack validation: $receiver_email,$payment_amount,$payment_currency ||| $valid,$error,$error_info"));mysql_query($sql);

				if ($valid) {

					if ($payment_transaction_id!= $payment->data['Payment Transaction ID']) {


						$data_to_update=array(
							'Payment Sender'=>$payment_fullName,
							'Payment Sender Email'=>$receiver_email,
							'Payment Sender Payment Paypal Type'=>$payment_type,
							'Payment Sender Message'=>$payment_memo,
							'Payment Transaction Address Status'=>$payment_payer_payment_status,
							'Payment Fees'=>$mc_fee,
							'Payment Transaction Status Info'=>$payment_status,
							'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
							'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
							'Payment Transaction Status'=>'Completed',
							'Payment Transaction ID'=>$payment_transaction_id,
							'Payment Method'=>'Paypal'

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




						//======

						$account_payment_key=0;
						$sql=sprintf("select `Payment Key` from `Order Payment Bridge` where `Is Account Payment`='Yes' and `Order Key`=%d ",
							$order->id

						);
						$res=mysql_query($sql);
						if ($row=mysql_fetch_assoc($res)) {
							$account_payment_key=$row['Payment Key'];

						}



						if ($account_payment_key) {
							$account_payment=new Payment($account_payment_key);

							$data_to_update=array(
								'Payment Completed Date'=>gmdate('Y-m-d H:i:s'),
								'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
								'Payment Transaction Status'=>'Completed'

							);



							$account_payment->update($data_to_update);

						}
						//====








						$order->checkout_submit_order();






						send_confirmation_email($order);


					}
					else {
						$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql("paypal error $payment_transaction_id  diferent to DB one ".$payment->data['Payment Transaction ID']));mysql_query($sql);

					}

				}else {
					$sql=sprintf("insert into debugtable (`text`,`date`) values (%s,NOW())",prepare_mysql("Paypal: error it say not valid"));mysql_query($sql);



				}









			}


		}
		else if (strcmp($res, "INVALID") == 0) {
				// log for manual investigation
			}
	}
	fclose($fp);
}

function check_if_valid($login,$amount,$currency,$payment,$payment_account) {

	$valid=true;
	$error='';
	$error_info='';

	if (!$payment->id) {
		$valid=false;
		$error_type='no_payment_found';
		$error_info=$payment->id;
		return array($valid,$error,$error_info);
	}


	if ($login != $payment_account->data['Payment Account Login'] ) {
		$valid=false;
		$error_type='wrong_login';
		$error_info=$payment_account->data['Payment Account Login'].'<<-->>'.$login;

		return array($valid,$error,$error_info);

	}

	/*
	if ($payment->data['Payment Balance'] != $amount) {
		$valid=false;
		$error_type='payment_amount_not_match';
		$error_info=$payment->data['Payment Balance'].'<->'.$amount;
		return array($valid,$error,$error_info);

	}
	*/
	/*
	if (strcmp($payment->data['Payment Currency Code'],trim($currency))) {
		$valid=false;
		$error_type='payment_currency_not_match';
		$error_info='->'.$payment->data['Payment Currency Code'].'<->'.$currency.'<-';
		return array($valid,$error,$error_info);

	}

*/
	return array($valid,$error,$error_info);

}


INSERT INTO `dw`.`Shipping Dimension` (`Shipping Key`, `Store Key`, `Shipping Type`, `Shipping Destination Type`, `Shipping Destination Code`, `Shipping Destination Metadata`, `Shipping Price Method`, `Shipping Secondary Destination Check`, `Shipping Metadata`, `Shipping Begin Date`, `Shipping Expiration Date`) VALUES 
(NULL, '1', 'Normal', 'Country', 'GBR', 'PO35%', 'Step Order Items Net Amount', 'Post Code', '0,495,15;495,,0', NULL, NULL);
INSERT INTO `dw`.`Shipping Dimension` (`Shipping Key`, `Store Key`, `Shipping Type`, `Shipping Destination Type`, `Shipping Destination Code`, `Shipping Destination Metadata`, `Shipping Price Method`, `Shipping Secondary Destination Check`, `Shipping Metadata`, `Shipping Begin Date`, `Shipping Expiration Date`) VALUES 
(NULL, '1', 'Normal', 'Country', 'GBR', 'PO36%', 'Step Order Items Net Amount', 'Post Code', '0,495,15;495,,0', NULL, NULL);

INSERT INTO `dw`.`Shipping Dimension` (`Shipping Key`, `Store Key`, `Shipping Type`, `Shipping Destination Type`, `Shipping Destination Code`, `Shipping Destination Metadata`, `Shipping Price Method`, `Shipping Secondary Destination Check`, `Shipping Metadata`, `Shipping Begin Date`, `Shipping Expiration Date`) VALUES 
(NULL, '1', 'Normal', 'Country', 'GBR', 'PO37%', 'Step Order Items Net Amount', 'Post Code', '0,495,15;495,,0', NULL, NULL);

INSERT INTO `dw`.`Shipping Dimension` (`Shipping Key`, `Store Key`, `Shipping Type`, `Shipping Destination Type`, `Shipping Destination Code`, `Shipping Destination Metadata`, `Shipping Price Method`, `Shipping Secondary Destination Check`, `Shipping Metadata`, `Shipping Begin Date`, `Shipping Expiration Date`) VALUES 
(NULL, '1', 'Normal', 'Country', 'GBR', 'PO38%', 'Step Order Items Net Amount', 'Post Code', '0,495,15;495,,0', NULL, NULL);

INSERT INTO `dw`.`Shipping Dimension` (`Shipping Key`, `Store Key`, `Shipping Type`, `Shipping Destination Type`, `Shipping Destination Code`, `Shipping Destination Metadata`, `Shipping Price Method`, `Shipping Secondary Destination Check`, `Shipping Metadata`, `Shipping Begin Date`, `Shipping Expiration Date`) VALUES 
(NULL, '1', 'Normal', 'Country', 'GBR', 'PO39%', 'Step Order Items Net Amount', 'Post Code', '0,495,15;495,,0', NULL, NULL);


?>
