<?php
/*
  File: Ordes.php

  This file contains the Order Class

  About:
  Autor: Raul Perusquia <rulovico@gmail.com>

  Copyright (c) 2009, Inikoo

  Version 2.0
*/
include_once 'class.DB_Table.php';


include_once 'class.Customer.php';
include_once 'class.Store.php';
include_once 'class.Ship_To.php';
include_once 'class.Billing_To.php';

include_once 'class.Invoice.php';

include_once 'class.DeliveryNote.php';
include_once 'class.TaxCategory.php';
include_once 'class.CurrencyExchange.php';
require_once 'order_common_functions.php';


class Order extends DB_Table {
	//Public $data = array ();
	// Public $items = array ();
	// Public $status_names = array ();
	// Public $id = false;
	// Public $tipo;
	// Public $staus = 'new';


	var $ghost_order=false;
	var $update_stock=true;
	public $skip_update_product_sales=false;
	var $skip_update_after_individual_transaction=true;
	function __construct($arg1 = false, $arg2 = false) {

		$this->table_name='Order';
		$this->ignore_fields=array('Order Key');
		$this->update_customer=true;

		$this->status_names = array (0 => 'new' );
		if (preg_match( '/new/i', $arg1 )) {
			$this->create_order ( $arg2 );

			return;
		}
		if (is_numeric( $arg1 )) {
			$this->get_data ( 'id', $arg1 );
			return;
		}
		$this->get_data ( $arg1, $arg2 );

	}

	/*   function set_adjust_amounts($tipo,$amount){ */
	/*     $this->adjusts[$tipo]=$amount; */
	/*   } */
	/*   function get_adjust_amounts($tipo,$amount){ */
	/*     if(array_key_exists($tipo,$this->adjusts)) */
	/*       return $this->adjusts[$tipo]; */
	/*     else */
	/*       false; */
	/*   } */


	function add_credit_no_product_transaction($credit_transaction_data) {

		$order_date=$this->data['Order Date'];

		$sql=sprintf("insert into `Order No Product Transaction Fact` (
		`Transaction Gross Amount`,`Transaction Net Amount`,`Transaction Tax Amount`,
		`Affected Order Key`,`Order Key`,`Order Date`,`Transaction Type`,`Transaction Description`,`Tax Category Code`,`Currency Code`)
		values (%f,%f,%s,%d,%s,%s,%s,%s,%s) ",

			$credit_transaction_data['Transaction Net Amount'],
			$credit_transaction_data['Transaction Net Amount'],
			$credit_transaction_data['Transaction Tax Amount'],
			prepare_mysql($credit_transaction_data['Affected Order Key']),
			$this->id,
			prepare_mysql($order_date),
			prepare_mysql('Credit'),
			prepare_mysql($credit_transaction_data['Transaction Description']),

			prepare_mysql($credit_transaction_data['Tax Category Code']),

			prepare_mysql($this->data['Order Currency'])

		);
		//print $sql;
		mysql_query($sql);
		$this->update_no_normal_totals();
	}

	function update_credit_no_product_transaction($credit_transaction_data) {


		$sql=sprintf("update `Order No Product Transaction Fact` set `Transaction Outstanding Net Amount Balance`=%f,`Transaction Outstanding Tax Amount Balance`=%f,`Transaction Net Amount`=%f,`Transaction Tax Amount`=%f,`Transaction Description`=%s,`Tax Category Code`=%s where `Order No Product Transaction Fact Key`=%d and `Order Key`=%d ",
			$credit_transaction_data['Transaction Net Amount'],
			$credit_transaction_data['Transaction Tax Amount'],
			$credit_transaction_data['Transaction Net Amount'],
			$credit_transaction_data['Transaction Tax Amount'],
			prepare_mysql($credit_transaction_data['Transaction Description']),

			prepare_mysql($credit_transaction_data['Tax Category Code']),
			$credit_transaction_data['Order No Product Transaction Fact Key'],
			$this->id


		);
		//print $sql;
		mysql_query($sql);
		$this->update_no_normal_totals();
	}


	function delete_credit_transaction($transaction_key) {
		$sql=sprintf("delete from `Order No Product Transaction Fact`  where `Order No Product Transaction Fact Key`=%d and `Order Key`=%d ",
			$transaction_key,
			$this->id


		);
		//print $sql;
		mysql_query($sql);
		$this->update_no_normal_totals();

	}

	function create_refund($data=false) {
		$refund_tag='ref';
		$refund_data=array(
			'Invoice Customer Key'=>$this->data['Order Customer Key'],
			'Invoice Store Key'=>$this->data['Order Store Key'],
			'Order Key'=>$this->id,

			'Invoice Public ID'=>$this->data['Order Public ID'].$refund_tag
		);
		if (!$data)$data=array();

		if (array_key_exists('Invoice Metadata',$data))$refund_data['Invoice Metadata']=$data['Invoice Metadata'];
		if (array_key_exists('Invoice Date',$data))$refund_data['Invoice Date']=$data['Invoice Date'];
		if (array_key_exists('Invoice Tax Code',$data))$refund_data['Invoice Tax Code']=$data['Invoice Tax Code'];

		$refund=new Invoice('create refund',$refund_data);
		return $refund;
	}

	function create_order($data) {



		global $myconf;


		if (isset($data['editor'])) {
			foreach ($data['editor'] as $key=>$value) {
				if (array_key_exists($key,$this->editor))
					$this->editor[$key]=$value;

			}
		}
		$this->editor=$data ['editor'];


		$this->data ['Order Type'] = $data ['Order Type'];
		if (isset($data['Order Date'])) {
			$this->data ['Order Date'] =$data['Order Date'];

		}else {
			$this->data ['Order Date'] = gmdate('Y-m-d H:i:s');

		}
		$this->data ['Order Created Date']=$this->data ['Order Date'];


		$this->data['Order Tax Code']='';
		$this->data['Order Tax Rate']=0;
		$this->data['Order Tax Name']='';
		$this->data['Order Tax Operations']='';
		$this->data['Order Tax Selection Type']='';


		if (isset($data['Order Tax Code'])) {

			$tax_cat=new TaxCategory('code',$data['Order Tax Code']);
			if ($tax_cat->id) {
				$this->data['Order Tax Code']=$tax_cat->data['Tax Category Code'];
				$this->data['Order Tax Rate']=$tax_cat->data['Tax Category Rate'];
				$this->data['Order Tax Name']=$tax_cat->data['Tax Category Name'];
				$this->data['Order Tax Operations']='';
				$this->data['Order Tax Selection Type']='set';



			}
		}



		$this->set_data_from_customer($data['Customer Key']);

		if (isset($data['Order Current Dispatch State']) and $data['Order Current Dispatch State']=='In Process by Customer') {
			$this->data ['Order Current Dispatch State'] = 'In Process by Customer';
			$this->data ['Order Current XHTML Payment State'] = _('Waiting for payment');
		}else {
			$this->data ['Order Current Dispatch State'] = 'In Process';
			$this->data ['Order Current XHTML Payment State'] = _('Waiting for payment');
		}




		if (isset($data['Order Apply Auto Customer Account Payment'])) {
			$this->data ['Order Apply Auto Customer Account Payment'] =$data['Order Apply Auto Customer Account Payment'];
		}else {
			$this->data ['Order Apply Auto Customer Account Payment']='Yes';

		}



		if (isset($data['Order Payment Method'])) {
			$this->data ['Order Payment Method'] =$data['Order Payment Method'];
		}else {
			$this->data ['Order Payment Method'] ='Unknown';

		}


		$this->data ['Order Current Payment State'] = 'Not Invoiced';

		if (array_key_exists('Order Sales Representative Keys',$data)) {
			$this->data ['Order Sales Representative Keys']=$data['Order Sales Representative Keys'];
		}else {
			$this->data ['Order Sales Representative Keys'] =array($this->editor['User Key']);
		}

		$this->data ['Order For'] = 'Customer';

		$this->data ['Order Customer Message']='';


		if (isset($data['Order Original Data MIME Type']))
			$this->data ['Order Original Data MIME Type']=$data['Order Original Data MIME Type'];
		else
			$this->data ['Order Original Data MIME Type']='none';




		if (isset($data['Order Original Metadata']))
			$this->data ['Order Original Metadata']=$data['Order Original Metadata'];
		else
			$this->data ['Order Original Metadata']='';


		if (isset($data['Order Original Data Source']))
			$this->data ['Order Original Data Source']=$data['Order Original Data Source'];
		else
			$this->data ['Order Original Data Source']='Other';


		if (isset($data['Order Original Data Filename']))
			$this->data ['Order Original Data Filename']=$data['Order Original Data Filename'];
		else
			$this->data ['Order Original Data Filename']='Other';



		$this->data ['Order Currency Exchange']=1;
		$sql=sprintf("select `Account Currency` from `Account Dimension`");


		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res)) {
			$corporation_currency_code=$row['Account Currency'];
		} else {
			$corporation_currency_code='GBP';

		}
		//print_r($this->data);
		//print "xxx $corporation_currency_code xx";
		//print "xx -> corporation_currency_code ".this->data ['Order Currency']." <-  xxx\n";








		if ($this->data ['Order Currency']!=$corporation_currency_code) {


			//take off this and only use curret exchenge whan get rid off excel
			$date_difference=date('U')-strtotime($this->data['Order Date'].' +0:00');
			if ($date_difference>3600) {
				$currency_exchange = new CurrencyExchange($this->data ['Order Currency'].$corporation_currency_code,$this->data['Order Date']);
				$exchange= $currency_exchange->get_exchange();
			}else {
				$exchange=currency_conversion($this->data ['Order Currency'],$corporation_currency_code,'now');
			}
			$this->data ['Order Currency Exchange']=$exchange;
		}

		$this->data ['Order Main Source Type']='Call';
		if (isset($data['Order Main Source Type']) and preg_match('/^(Internet|Call|Store|Unknown|Email|Fax)$/i'))
			$this->data ['Order Main Source Type']=$data['Order Main Source Type'];

		if (isset($data ['Order Public ID'])) {
			$this->data ['Order Public ID'] = $data ['Order Public ID'];
			$this->data ['Order File As'] = $this->prepare_file_as($data ['Order Public ID']);
		} else {
			$this->next_public_id();
		}



		$this->create_order_header ();



		if (count( $this->data ['Order Sales Representative Keys'])==0) {
			$sql = sprintf( "insert into `Order Sales Representative Bridge` values (%d,0,1)", $this->id);
			mysql_query($sql);
		}else {
			$share=1/count( $this->data ['Order Sales Representative Keys']);
			foreach ( $this->data ['Order Sales Representative Keys'] as $sale_rep_key ) {
				$sql = sprintf( "insert into `Order Sales Representative Bridge` values (%d,%d,%f)", $this->id, $sale_rep_key ,$share);
				mysql_query($sql);
			}
		}


		$this->get_data('id',$this->id);
		$this->update_xhtml_sale_representatives();
		$this->update_charges();

		if ($this->data['Order Shipping Method']=='Calculated') {
			$this->update_shipping();

		}
		$customer=new Customer($data['Customer Key']);
		$customer->editor=$this->editor;

		$customer->add_history_new_order($this);

		$customer->update_orders();
		$customer->update_no_normal_data();

		$store=new Store($this->data['Order Store Key']);
		$store->update_orders();


		$this->update_full_search();
		if (!$this->ghost_order) {
			$this->get_data('id',$this->id);
			$this->update_item_totals_from_order_transactions();
			$this->update_totals_from_order_transactions();
			$this->apply_payment_from_customer_account();
		}
	}

	function get_sales_representative_keys() {
		$sales_representative_keys=array();
		$sql=sprintf("select `Staff Key` from `Order Sales Representative Bridge` where `Order Key`=%s",
			$this->id
		);
		$result = mysql_query($sql) or die('aa0 Query failed: ' . mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$sales_representative_keys[]=$row['Staff Key'];
		}
		return $sales_representative_keys;
	}

	function update_xhtml_sale_representatives() {

		$xhtml_sale_representatives='';
		$tag='&view=csr';
		$sql=sprintf("select S.`Staff Key`,`Staff Alias` from `Order Sales Representative Bridge` B  left join `Staff Dimension` S on (B.`Staff Key`=S.`Staff Key`) where `Order Key`=%s",
			$this->id
		);
		//print $sql;
		$result = mysql_query($sql) or die('aa1 Query failed: ' . mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$id=$row['Staff Key'];
			$ids[$id]=$id;

			$xhtml_sale_representatives.=sprintf(', <a href="staff.php?id=%d%s">%s</a>',$id,$tag,mb_ucwords($row['Staff Alias']));

		}
		$xhtml_sale_representatives=preg_replace("/^\,\s*/","",$xhtml_sale_representatives);
		if ($xhtml_sale_representatives=='')
			$xhtml_sale_representatives=_('Unknown');

		$sql=sprintf("update `Order Dimension` set `Order XHTML Sales Representative`=%s where `Order Key`=%d",
			prepare_mysql($xhtml_sale_representatives),
			$this->id
		);
		//print $sql;
		mysql_query($sql);
	}




	function checkout_cancel_payment() {

		$date=gmdate("Y-m-d H:i:s");

		if (!($this->data['Order Current Dispatch State']=='In Process by Customer' or $this->data['Order Current Dispatch State']=='Waiting for Payment Confirmation')) {
			$this->error=true;
			$this->msg='Order is not in process by customer xx';
			return;

		}

		$this->data['Order Current Dispatch State']='In Process by Customer';
		//TODO make it using $this->calculate_state();
		$this->data['Order Current XHTML Dispatch State']=_('In Process by Customer');
		//TODO make it using $this->calculate_state(); or calculate_payments new functuon

		// 'Not Invoiced','Waiting Payment','Paid','Partially Paid','Unknown','No Applicable'



		$sql=sprintf("update `Order Dimension` set `Order Submitted by Customer Date`=NULL,`Order Current Dispatch State`=%s,`Order Current XHTML Dispatch State`=%s where `Order Key`=%d"

			,prepare_mysql($this->data['Order Current Dispatch State'])
			,prepare_mysql($this->data['Order Current XHTML Dispatch State'])


			,$this->id
		);

		mysql_query($sql);

		$this->update_payment_state();

	}


	function checkout_submit_payment() {

		$date=gmdate("Y-m-d H:i:s");

		if (!($this->data['Order Current Dispatch State']=='In Process by Customer'  or $this->data['Waiting for Payment Confirmation'] ) ) {
			$this->error=true;
			$this->msg='Order is not in process by customer';


			return;

		}

		$this->data['Order Current Dispatch State']='Waiting for Payment Confirmation';
		//TODO make it using $this->calculate_state();
		$this->data['Order Current XHTML Dispatch State']=_('Waiting for Payment Confirmation');
		//TODO make it using $this->calculate_state(); or calculate_payments new functuon

		// 'Not Invoiced','Waiting Payment','Paid','Partially Paid','Unknown','No Applicable'


		$sql=sprintf("update `Order Dimension` set `Order Checkout Submitted Payment Date`=%s,`Order Date`=%s,`Order Current Dispatch State`=%s,`Order Current XHTML Dispatch State`=%s where `Order Key`=%d"
			,prepare_mysql($date)
			,prepare_mysql($date)
			,prepare_mysql($this->data['Order Current Dispatch State'])
			,prepare_mysql($this->data['Order Current XHTML Dispatch State'])


			,$this->id
		);

		mysql_query($sql);
		$this->update_payment_state();
	}


	function checkout_submit_order() {

		$date=gmdate("Y-m-d H:i:s");

		if (!($this->data['Order Current Dispatch State']=='In Process by Customer' or $this->data['Order Current Dispatch State']=='Waiting for Payment Confirmation')) {
			$this->error=true;
			$this->msg='Order is not in process by customer: xx  '.$this->id.' '.$this->data['Order Current Dispatch State'];
			return;

		}
		$this->data['Order Current Dispatch State']='Submitted by Customer';
		$this->data['Order Current XHTML Dispatch State']='Submitted by Customer';




		$sql=sprintf("update `Order Dimension` set `Order Submitted by Customer Date`=%s,`Order Date`=%s,`Order Current Dispatch State`=%s,`Order Current XHTML Dispatch State`=%s  where `Order Key`=%d"
			,prepare_mysql($date)
			,prepare_mysql($date)
			,prepare_mysql($this->data['Order Current Dispatch State'])
			,prepare_mysql($this->data['Order Current XHTML Dispatch State'])

			,$this->id
		);



		mysql_query($sql);
		$this->update_payment_state();
	}

	function send_to_warehouse($date=false,$extra_data=false) {


		if (!$date)
			$date=gmdate('Y-m-d H:i:s');

		if (!($this->data['Order Current Dispatch State']=='In Process' or $this->data['Order Current Dispatch State']=='Submitted by Customer'    or $this->data['Order Current Dispatch State']=='In Process by Customer' )) {
			$this->error=true;
			$this->msg='Order is not in process';
			return;

		}

		if ($this->data['Order Current Dispatch State']=='Submitted by Customer'    or $this->data['Order Current Dispatch State']=='In Process by Customer') {
			$this->update_field_switcher('Order Date',$date,'no_history');

		}


		if ($this->data['Order For Collection']=='Yes') {
			$dispatch_method='Collection';
		} else {
			$dispatch_method='Dispatch';
		}
		$data_dn=array(
			'Delivery Note Date Created'=>$date,
			'Delivery Note Order Date Placed'=>$this->data['Order Date'],
			'Delivery Note ID'=>$this->data['Order Public ID'],
			'Delivery Note File As'=>$this->data['Order File As'],
			'Delivery Note Type'=>$this->data['Order Type'],
			'Delivery Note Dispatch Method'=>$dispatch_method,
			'Delivery Note Title'=>_('Delivery Note for').' '.$this->data['Order Type'].' <a class="id" href="order.php?id='.$this->id.'">'.$this->data['Order Public ID'].'</a>',
			'Delivery Note Customer Key'=>$this->data['Order Customer Key'],
			'Delivery Note Metadata'=>$this->data['Order Original Metadata']

		);


		$dn=new DeliveryNote('create',$data_dn,$this);
		$dn->update_stock=$this->update_stock;

		if (isset($this->date_create_inventory_transaction_fact)) {
			$date=$this->date_create_inventory_transaction_fact;
		}



		$dn->create_inventory_transaction_fact($this->id,$date,$extra_data);
		$this->update_delivery_notes('save');
		$this->data['Order Current Dispatch State']='Ready to Pick';
		$this->data['Order Current XHTML Dispatch State']=_('Ready to Pick');
		$sql=sprintf("update `Order Dimension` set `Order Current Dispatch State`=%s,`Order Current XHTML Dispatch State`=%s  where `Order Key`=%d"
			,prepare_mysql($this->data['Order Current Dispatch State'])
			,prepare_mysql($this->data['Order Current XHTML Dispatch State'])
			,$this->id
		);

		mysql_query($sql);

		$this->update_delivery_notes();
		$this->update_full_search();

		return $dn;
	}




	function send_post_action_to_warehouse($date=false,$type=false,$metadata='') {
		if (!$date)
			$date=gmdate('Y-m-d H:i:s');

		if (!$this->data['Order Current Dispatch State']=='Dispatched') {
			$this->error=true;
			$this->msg='Order is not already dispatched';
			return;

		}
		if (!$type) {
			$type='Replacement & Shortages';
		}


		$type_formated=$type;
		$title="Delivery Note for $type of ".$this->data['Order Type'].' <a href="order.php?id='.$this->id.'">'.$this->data['Order Public ID'].'</a>';

		if ($this->data['Order For Collection']=='Yes')
			$dispatch_method='Collection';
		else
			$dispatch_method='Dispatch';
		if ($type=='Replacement')
			$suffix='rpl';
		elseif ($type=='Missing') {
			$suffix='sh';
			$type='Shortages';
		}else
			$suffix='r';
		$data_dn=array(
			'Delivery Note Date Created'=>$date,
			'Delivery Note ID'=>$this->data['Order Public ID']."$suffix",
			'Delivery Note File As'=>$this->data['Order File As']."$suffix",
			'Delivery Note Type'=>$type,
			'Delivery Note Title'=>$title,
			'Delivery Note Dispatch Method'=>$dispatch_method,
			'Delivery Note Metadata'=>$metadata,
			'Delivery Note Customer Key'=>$this->data['Order Customer Key']

		);






		$dn=new DeliveryNote('create',$data_dn,$this);
		$dn->create_post_order_inventory_transaction_fact($this->id,$date);
		$this->update_delivery_notes('save');
		//TODO!!!


		//$this->update_post_dispatch_state();

		$this->update_full_search();

		$customer=new Customer($this->data['Order Customer Key']);
		$customer->add_history_post_order_in_warehouse($dn,$type);
		return $dn;
	}

	function cancel_by_customer($note) {
		$this->cancel($note,false,false,$by_customer=true);
	}

	function cancel($note='',$date=false,$force=false,$by_customer=false) {

		$this->cancelled=false;
		if (preg_match('/Dispatched/',$this->data ['Order Current Dispatch State'])) {
			$this->msg=_('Order can not be cancelled, because has already been dispatched');

		}
		if (preg_match('/Cancelled/',$this->data ['Order Current Dispatch State'])) {
			$this->msg=_('Order is already cancelled');

		}
		else {

			if ($by_customer) {
				$state = 'Cancelled by Customer';

			}else {
				$state  = 'Cancelled';
			}

			if (!$date)
				$date=gmdate('Y-m-d H:i:s');
			$this->data ['Order Cancelled Date'] = $date;

			$this->data ['Order Cancel Note'] = $note;

			$this->data ['Order Current Payment State'] = 'No Applicable';


			$this->data ['Order Current Dispatch State'] = $state;

			$this->data ['Order Current XHTML Dispatch State'] = _('Cancelled');
			$this->data ['Order Current XHTML Payment State'] = _ ( 'Order cancelled' );
			$this->data ['Order XHTML Invoices'] = '';
			$this->data ['Order XHTML Delivery Notes'] = '';
			$this->data ['Order Invoiced Balance Total Amount'] = 0;
			$this->data ['Order Invoiced Balance Net Amount'] = 0;
			$this->data ['Order Invoiced Balance Tax Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Total Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Net Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Tax Amount'] = 0;



			$sql = sprintf( "update `Order Dimension` set    `Order Cancelled Date`=%s, `Order Current Payment State`=%s,`Order Current Dispatch State`=%s,`Order Current XHTML Dispatch State`=%s,`Order Current XHTML Payment State`=%s,`Order XHTML Invoices`='',`Order XHTML Delivery Notes`='' ,`Order Invoiced Balance Net Amount`=0,`Order Invoiced Balance Tax Amount`=0,`Order Invoiced Balance Total Amount`=0 ,`Order Invoiced Outstanding Balance Net Amount`=0,`Order Invoiced Outstanding Balance Tax Amount`=0,`Order Invoiced Outstanding Balance Total Amount`=0,`Order Invoiced Profit Amount`=0,`Order Cancel Note`=%s  where `Order Key`=%d"
				//     ,$no_shipped
				, prepare_mysql ( $this->data ['Order Cancelled Date'] )
				, prepare_mysql ( $this->data ['Order Current Payment State'] )
				, prepare_mysql ( $this->data ['Order Current Dispatch State'] )
				, prepare_mysql ( $this->data ['Order Current XHTML Dispatch State'] )
				, prepare_mysql ( $this->data ['Order Current XHTML Payment State'] )
				, prepare_mysql ( $this->data ['Order Cancel Note'] )

				, $this->id );
			if (! mysql_query( $sql ))
				exit ( "$sql error can not update cancel\n" );

			$sql = sprintf( "update `Order Transaction Fact` set `Consolidated`='Yes',`Current Dispatching State`=%s,`Current Payment State`=%s where `Order Key`=%d ",
				prepare_mysql($state),
				prepare_mysql($state),
				$this->id );
			mysql_query( $sql );



			$sql = sprintf( "update `Order No Product Transaction Fact` set `State`=%s  where `Order Key`=%d ",
				prepare_mysql($state),
				$this->id );
			mysql_query( $sql );



			foreach ($this->get_delivery_notes_objects() as $dn) {
				$dn->cancel($note,$date,$force);
			}

			$customer=new Customer($this->data['Order Customer Key']);
			$customer->editor=$this->editor;
			$customer->add_history_order_cancelled($this);
			$store=new Store($this->data['Order Store Key']);
			$store->update_orders();

			$this->update_deals_usage();
			$this->cancelled=true;

		}



	}


	function activate($date=false) {


		if (!preg_match('/Suspended/',$this->data ['Order Current Dispatch State'])) {
			$this->msg=_('Order is not suspended');

		}
		else {

			if (!$date)
				$date=gmdate('Y-m-d H:i:s');
			$this->data ['Order Suspended Date'] = $date;

			$this->data ['Order Suspend Note'] = $note;

			$this->data ['Order Current Payment State'] = 'No Applicable';
			$this->data ['Order Current Dispatch State'] = 'Suspended';
			$this->data ['Order Current XHTML Dispatch State'] = _('Suspended');
			$this->data ['Order Current XHTML Payment State'] = _( 'Order Suspended' );
			$this->data ['Order XHTML Invoices'] = '';
			$this->data ['Order XHTML Delivery Notes'] = '';
			$this->data ['Order Invoiced Balance Total Amount'] = 0;
			$this->data ['Order Invoiced Balance Net Amount'] = 0;
			$this->data ['Order Invoiced Balance Tax Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Total Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Net Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Tax Amount'] = 0;



			$sql = sprintf( "update `Order Dimension` set `Order Suspended Date`=%s, `Order Current Payment State`=%s,`Order Current Dispatch State`=%s,`Order Current XHTML Dispatch State`=%s,`Order Current XHTML Payment State`=%s,`Order XHTML Invoices`='',`Order XHTML Delivery Notes`='' ,`Order Invoiced Balance Net Amount`=0,`Order Invoiced Balance Tax Amount`=0,`Order Invoiced Balance Total Amount`=0 ,`Order Invoiced Outstanding Balance Net Amount`=0,`Order Invoiced Outstanding Balance Tax Amount`=0,`Order Invoiced Outstanding Balance Total Amount`=0,`Order Invoiced Profit Amount`=0,`Order Suspend Note`=%s  where `Order Key`=%d"
				, prepare_mysql ( $this->data ['Order Suspended Date'] )
				, prepare_mysql ( $this->data ['Order Current Payment State'] )
				, prepare_mysql ( $this->data ['Order Current Dispatch State'] )
				, prepare_mysql ( $this->data ['Order Current XHTML Dispatch State'] )
				, prepare_mysql ( $this->data ['Order Current XHTML Payment State'] )
				, prepare_mysql ( $this->data ['Order Suspend Note'] )

				, $this->id );
			mysql_query( $sql );

			$sql = sprintf( "update `Order Transaction Fact` set `Current Dispatching State`='Suspended',`Current Payment State`='No Applicable' where `Order Key`=%d ", $this->id );
			mysql_query( $sql );
			$sql = sprintf( "update `Order No Product Transaction Fact` set `State`='Suspended'  where `Order Key`=%d ", $this->id );
			mysql_query( $sql );

			foreach ($this->get_delivery_notes_objects() as $dn) {
				$dn->suspend($note,$date);
			}

			$customer=new Customer($this->data['Order Customer Key']);
			$customer->editor=$this->editor;
			$customer->add_history_order_activate($this);//<--- Not done yet
			$this->suspended=true;

		}



	}


	function suspend($note='',$date=false) {

		$this->suspended=false;
		if (preg_match('/Dispatched/',$this->data ['Order Current Dispatch State'])) {
			$this->msg=_('Order can not be suspended, because has already been dispatched');

		}
		elseif (preg_match('/Suspended/',$this->data ['Order Current Dispatch State'])) {
			$this->msg=_('Order is cancelled');

		}
		elseif (preg_match('/Suspended/',$this->data ['Order Current Dispatch State'])) {
			$this->msg=_('Order is already suspended');

		}
		else {

			if (!$date)
				$date=gmdate('Y-m-d H:i:s');
			$this->data ['Order Suspended Date'] = $date;

			$this->data ['Order Suspend Note'] = $note;

			$this->data ['Order Current Payment State'] = 'No Applicable';
			$this->data ['Order Current Dispatch State'] = 'Suspended';
			$this->data ['Order Current XHTML Dispatch State'] = _('Suspended');
			$this->data ['Order Current XHTML Payment State'] = _( 'Order Suspended' );
			$this->data ['Order XHTML Invoices'] = '';
			$this->data ['Order XHTML Delivery Notes'] = '';
			$this->data ['Order Invoiced Balance Total Amount'] = 0;
			$this->data ['Order Invoiced Balance Net Amount'] = 0;
			$this->data ['Order Invoiced Balance Tax Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Total Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Net Amount'] = 0;
			$this->data ['Order Invoiced Outstanding Balance Tax Amount'] = 0;



			$sql = sprintf( "update `Order Dimension` set `Order Suspended Date`=%s, `Order Current Payment State`=%s,`Order Current Dispatch State`=%s,`Order Current XHTML Dispatch State`=%s,`Order Current XHTML Payment State`=%s,`Order XHTML Invoices`='',`Order XHTML Delivery Notes`='' ,`Order Invoiced Balance Net Amount`=0,`Order Invoiced Balance Tax Amount`=0,`Order Invoiced Balance Total Amount`=0 ,`Order Invoiced Outstanding Balance Net Amount`=0,`Order Invoiced Outstanding Balance Tax Amount`=0,`Order Invoiced Outstanding Balance Total Amount`=0,`Order Invoiced Profit Amount`=0,`Order Suspend Note`=%s  where `Order Key`=%d"
				, prepare_mysql ( $this->data ['Order Suspended Date'] )
				, prepare_mysql ( $this->data ['Order Current Payment State'] )
				, prepare_mysql ( $this->data ['Order Current Dispatch State'] )
				, prepare_mysql ( $this->data ['Order Current XHTML Dispatch State'] )
				, prepare_mysql ( $this->data ['Order Current XHTML Payment State'] )
				, prepare_mysql ( $this->data ['Order Suspend Note'] )

				, $this->id );
			mysql_query( $sql );

			$sql = sprintf( "update `Order Transaction Fact` set `Current Dispatching State`='Suspended',`Current Payment State`='No Applicable' where `Order Key`=%d ", $this->id );
			mysql_query( $sql );
			$sql = sprintf( "update `Order No Product Transaction Fact` set `State`='Suspended'  where `Order Key`=%d ", $this->id );
			mysql_query( $sql );

			foreach ($this->get_delivery_notes_objects() as $dn) {
				$dn->suspend($note,$date);
			}

			$customer=new Customer($this->data['Order Customer Key']);
			$customer->editor=$this->editor;
			$customer->add_history_order_suspended($this);
			$store=new Store($this->data['Order Store Key']);
			$store->update_orders();
			$this->suspended=true;

		}



	}


	function create_invoice($date=false) {
		// intended to be used in services

		if (!$date)
			$date=gmdate("Y-m-d H:i:s");

		$tax_code='UNK';
		$orders_ids='';

		$tax_code=$this->data['Order Tax Code'];





		$delivery_note_keys='';
		foreach ($this->get_delivery_notes_ids()as $dn_key) {

			$delivery_note_keys=$dn_key.',';

		}
		$delivery_note_keys=preg_replace('/\,$/','',$delivery_note_keys);



		$data_invoice=array(
			'Invoice Date'=>$date,
			'Invoice Type'=>'Invoice',
			'Invoice Public ID'=>$this->data['Order Public ID'],
			'Delivery Note Keys'=>$delivery_note_keys,
			'Orders Keys'=>$this->id,
			'Invoice Store Key'=>$this->data['Order Store Key'],
			'Invoice Customer Key'=>$this->data['Order Customer Key'],
			'Invoice Tax Code'=>$tax_code,
			'Invoice Tax Shipping Code'=>$tax_code,
			'Invoice Tax Charges Code'=>$tax_code,
			'Invoice Sales Representative Keys'=>$this->get_sales_representative_keys(),
			'Invoice Metadata'=>$this->data['Order Original Metadata'],
			'Invoice Billing To Key'=>$this->data['Order Billing To Key To Bill'],

		);




		$invoice=new Invoice ('create',$data_invoice);







		$invoice->update_totals();

		$this->update_xhtml_invoices();
		$this->update_customer_history();
		$this->update_xhtml_state();






		return $invoice;
	}

	function no_payment_applicable() {




		$this->data ['Order Current Payment State'] = 'No Applicable';
		$this->data ['Order Current Dispatch State'] = 'Dispatched';

		$dn_txt=_('Dispatched');
		if ($this->data ['Order Type'] == 'Order') {
			$dn_txt = _("No value order, Dispatched");
		}



		$sql = sprintf( "update `Order Dimension` set `Order Current XHTML Payment State`=%s where `Order Key`=%d", prepare_mysql ( $dn_txt ), $this->id );
		if (! mysql_query( $sql ))
			exit ( "arror can not update no_payment_applicable\n" );


		$sql = sprintf( "update `Order Dimension` set `Order Current Payment State`=%s ,`Order Current Dispatch State`=%s where `Order Key`=%d", prepare_mysql ( $this->data ['Order Current Payment State'] ), prepare_mysql ( $this->data ['Order Current Dispatch State'] ), $this->id );
		if (! mysql_query( $sql ))
			exit ( "arror can not update no_payment_applicable\n" );

		$sql = sprintf( "update `Order Transaction Fact` set `Consolidated`='Yes',`Current Payment State`=%s ,`Current Dispatching State`=%s where `Order Key`=%d", prepare_mysql ( $this->data ['Order Current Payment State'] ), prepare_mysql ( $this->data ['Order Current Dispatch State'] ), $this->id );
		if (! mysql_query( $sql ))
			exit ( "arror can not update no_payment_applicabl 3e\n" );

	}

	function delete_transaction($otf_key) {
		$sql=sprintf("delete from `Order Transaction Fact` where `Order Transaction Fact Key`=%d",$otf_key);
		mysql_query($sql);


		$sql=sprintf("delete from `Inventory Transaction Fact` where `Map To Order Transaction Fact Key`=%d",$otf_key);
		mysql_query($sql);

	}


	function authorize_all() {
		$sql=sprintf("update  `Order Transaction Fact` set `Current Autorized to Sell Quantity`=`Order Quantity`  where `Order Key`=%d",$this->id);
		mysql_query($sql);

	}


	function add_order_transaction($data,$historic=false) {




		if (!isset($data ['ship to key'])) {
			$ship_to_keys=preg_split('/,/',$this->data['Order Ship To Keys']);
			$ship_to_key=$ship_to_keys[0];

		} else {
			$ship_to_key=$data ['ship to key'];
		}

		if (!isset($data ['billing to key'])) {
			$billing_to_keys=preg_split('/,/',$this->data['Order Billing To Keys']);
			$billing_to_key=$billing_to_keys[0];

		} else {
			$billing_to_key=$data ['billing to key'];
		}




		$tax_code=$this->data['Order Tax Code'];
		$tax_rate=$this->data['Order Tax Rate'];

		if (array_key_exists('tax_code',$data))
			$tax_code=$data['tax_code'];
		if (array_key_exists('tax_rate',$data))
			$tax_rate=$data['tax_rate'];


		//print "xx->$tax_code<-xx" ;

		if (isset($data['Order Type']))
			$order_type=$data['Order Type'];
		else
			$order_type=$this->data['Order Type'];

		if (array_key_exists('qty',$data)) {
			$quantity=$data ['qty'];
			$quantity_set=true;

		} else {
			$quantity=0;
			$quantity_set=false;
		}

		if (array_key_exists('qty',$data)) {
			$quantity=$data ['qty'];
			$quantity_set=true;

		} else {
			$quantity=0;
			$quantity_set=false;
		}



		if (array_key_exists('bonus qty',$data)) {
			$bonus_quantity=$data ['bonus qty'];
			$bonus_quantity_set=true;
		} else {
			$bonus_quantity=0;
			$bonus_quantity_set=false;

		}

		$gross_discounts=0;


		if ($historic) {

			$old_quantity=0;
			$old_bonus_quantity=0;
			$old_net_amount=0;

			$total_quantity=$quantity+$bonus_quantity;
			if ($total_quantity==0) {
				return array(
					'updated'=>false
				);

			}


			if ($quantity==0) {
				$data ['Current Payment State']='No Applicable';

			}


			$product=new Product('id',$data['Product Key']);
			$gross=$quantity*$product->data['Product History Price'];
			$estimated_weight=$total_quantity*$product->data['Product Package Weight'];
			$gross_discounts=0;
			$sql = sprintf( "insert into `Order Transaction Fact` (`Order Bonus Quantity`,`Order Transaction Type`,`Transaction Tax Rate`,`Transaction Tax Code`,`Order Currency Code`,`Estimated Weight`,`Order Date`,`Backlog Date`,`Order Last Updated Date`,
                             `Product Key`,`Product ID`,`Product Code`,`Product Family Key`,`Product Department Key`,
                             `Current Dispatching State`,`Current Payment State`,`Customer Key`,`Order Key`,`Order Public ID`,`Order Quantity`,`Ship To Key`,`Billing To Key`,
                             `Order Transaction Gross Amount`,`Order Transaction Total Discount Amount`,`Order Transaction Amount`,`Metadata`,`Store Key`,`Units Per Case`,`Customer Message`)
                             values (%f,%s,%f,%s,%s,%s,%s,%s,%s,
                             %d,%d,%s,%d,%d,
                             %s,%s,%s,%s,%s,%s,%s,%s,
                             %.2f,%.2f,%.2f,%s,%s,%f,'') ",
				$bonus_quantity,
				prepare_mysql($order_type),
				$tax_rate,
				prepare_mysql ($tax_code),
				prepare_mysql ( $this->data ['Order Currency'] ),
				$estimated_weight,
				prepare_mysql ( $data ['date'] ),
				prepare_mysql ( $data ['date'] ),
				prepare_mysql ( $data ['date'] ),
				$product->id,
				$product->data['Product ID'],
				prepare_mysql($product->data['Product Code']),
				$product->data['Product Family Key'],
				$product->data['Product Main Department Key'],
				prepare_mysql ( $data ['Current Dispatching State'] ),
				prepare_mysql ( $data ['Current Payment State'] ),
				prepare_mysql ( $this->data['Order Customer Key' ] ),
				prepare_mysql ( $this->data ['Order Key'] ),
				prepare_mysql ( $this->data ['Order Public ID'] ),
				$quantity,
				prepare_mysql ( $ship_to_key ),
				prepare_mysql ( $billing_to_key ),
				$gross,
				$gross_discounts,
				$gross-$gross_discounts,
				prepare_mysql ( $data ['Metadata'] ,false),
				prepare_mysql ( $this->data ['Order Store Key'] ),
				(isset($data ['units_per_case'])?$data ['units_per_case']:'')

			);
			mysql_query( $sql );

			$otf_key=mysql_insert_id();

			//print "Otf $otf_key \n";

		}
		else {


			if (!in_array($this->data['Order Current Dispatch State'],array('In Process by Customer','In Process','Submitted by Customer','Ready to Pick','Picking & Packing','Packed')) ) {
				return array(
					'updated'=>false,

				);
			}



			if (in_array($this->data['Order Current Dispatch State'],array('Ready to Pick','Picking & Packing','Packed')) ) {


				$dn_keys=$this->get_delivery_notes_ids();
				$dn_key=array_pop($dn_keys);
				$dn=new DeliveryNote($dn_key);


			}else {
				$dn_key=0;
			}




			$sql=sprintf("select `Order Bonus Quantity`,`Order Quantity`,`Order Transaction Gross Amount`,`Order Transaction Total Discount Amount`,`Order Transaction Fact Key` from `Order Transaction Fact` OTF where `Order Key`=%d and `Product Key`=%d ",
				$this->id,
				$data ['Product Key']);


			if ($dn_key) {
				$sql.=sprintf(' and `Delivery Note Key`=%d',$dn_key);
			}

			$res=mysql_query($sql);

			if ($row=mysql_fetch_array($res)) {

				$old_quantity=$row['Order Quantity'];
				$old_bonus_quantity=$row['Order Bonus Quantity'];
				$old_net_amount=$row['Order Transaction Gross Amount']-$row['Order Transaction Total Discount Amount'];



				if (!$quantity_set) {
					$quantity=$old_quantity;
				}
				if (!$bonus_quantity_set) {
					$bonus_quantity=$old_bonus_quantity;
				}
				$total_quantity=$quantity+$bonus_quantity;


				//    print "\n**** $old_quantity $old_bonus_quantity   ;  ($quantity_set,$bonus_quantity_set) ; QTY    $quantity ==     $total_quantity\n";

				if ($total_quantity==0) {

					$this->delete_transaction($row['Order Transaction Fact Key']);
					$otf_key=0;
					$gross=0;
					$gross_discounts=0;

				}
				else {



					$product=new Product('id',$data['Product Key']);
					$estimated_weight=$total_quantity*$product->data['Product Package Weight'];
					$gross=$quantity*$product->data['Product History Price'];




					$sql = sprintf( "update`Order Transaction Fact` set  `Estimated Weight`=%s,`Order Quantity`=%f,`Current Autorized to Sell Quantity`=%f,`Order Bonus Quantity`=%f,`Order Last Updated Date`=%s,`Order Transaction Gross Amount`=%.2f ,`Order Transaction Total Discount Amount`=%.2f,`Order Transaction Amount`=%.2f  where `Order Transaction Fact Key`=%d ",
						$estimated_weight ,
						$quantity,
						$quantity,
						$bonus_quantity,
						prepare_mysql ( $data ['date'] ),
						$gross,
						$gross_discounts,
						$gross-$gross_discounts,
						$row['Order Transaction Fact Key']

					);
					mysql_query($sql);
					if (mysql_affected_rows()) {
						$this->update_field('Order Last Updated Date',gmdate('Y-m-d H:i:s'),'no_history');
					}
					if ($dn_key) {

						$sql = sprintf("update  `Order Transaction Fact` set `Current Autorized to Sell Quantity`=%f,`Delivery Note ID`=%s,`Delivery Note Key`=%d ,`Destination Country 2 Alpha Code`=%s where `Order Transaction Fact Key`=%d"
							,$quantity
							,prepare_mysql ($dn->data ['Delivery Note ID'])
							,$dn_key
							,prepare_mysql($dn->data ['Delivery Note Country 2 Alpha Code'])
							,$row['Order Transaction Fact Key']

						);
						mysql_query($sql);
					}


					$otf_key=$row['Order Transaction Fact Key'];





					//   print "$sql  $otf_key  \n";
					//    exit;
				}

			}
			else {

				$old_quantity=0;
				$old_bonus_quantity=0;
				$old_net_amount=0;


				$total_quantity=$quantity+$bonus_quantity;

				if ($total_quantity==0) {
					return array(
						'updated'=>false,
						'qty'=>$quantity,
						'bonus qty'=>$bonus_quantity,
						'otf_key'=>0,
						'delta_qty'=>0,
						'delta_net_amount'=>0,
						'net_amount'=>0

					);
				}

				$product=new Product('id',$data['Product Key']);
				$gross=$quantity*$product->data['Product History Price'];
				$estimated_weight=$total_quantity*$product->data['Product Package Weight'];

				$sql = sprintf( "insert into `Order Transaction Fact` (`Order Bonus Quantity`,`Order Transaction Type`,`Transaction Tax Rate`,`Transaction Tax Code`,`Order Currency Code`,`Estimated Weight`,`Order Date`,`Backlog Date`,`Order Last Updated Date`,
                                 `Product Key`,`Product ID`,`Product Code`,`Product Family Key`,`Product Department Key`,
                                 `Current Dispatching State`,`Current Payment State`,`Customer Key`,`Order Key`,`Order Public ID`,`Order Quantity`,`Ship To Key`,`Billing To Key`,
                                 `Order Transaction Gross Amount`,`Order Transaction Total Discount Amount`,`Order Transaction Amount`,`Metadata`,`Store Key`,`Units Per Case`,`Customer Message`,`Delivery Note Key`)
                                 values (%f,%s,%f,%s,%s,%s,%s,%s,%s,
                                 %d,%d,%s,%d,%d,
                                 %s,%s,%s,%s,%s,%s,%s,%s,
                                 %.2f,%.2f,%.2f,%s,%s,%f,'',%s)   ",

					$bonus_quantity,
					prepare_mysql($order_type),
					$tax_rate,
					prepare_mysql ($tax_code),
					prepare_mysql ( $this->data ['Order Currency'] ),
					$estimated_weight ,
					prepare_mysql ( $data ['date'] ),
					prepare_mysql ( $data ['date'] ),
					prepare_mysql ( $data ['date'] ),
					$product->id,
					$product->data['Product ID'],
					prepare_mysql($product->data['Product Code']),
					$product->data['Product Family Key'],
					$product->data['Product Main Department Key'],
					prepare_mysql ( $data ['Current Dispatching State'] ),
					prepare_mysql ( $data ['Current Payment State'] ),
					prepare_mysql ( $this->data['Order Customer Key' ] ),
					prepare_mysql ( $this->data ['Order Key'] ),
					prepare_mysql ( $this->data ['Order Public ID'] ),
					$quantity,
					prepare_mysql ( $ship_to_key ),
					prepare_mysql ( $billing_to_key ),
					$gross,
					$gross_discounts,
					$gross-$gross_discounts,
					prepare_mysql ( $data ['Metadata'] ,false),
					prepare_mysql ( $this->data ['Order Store Key'] ),
					$product->data['Product Units Per Case'],
					prepare_mysql($dn_key)
				);

				mysql_query( $sql );

				$otf_key=mysql_insert_id();
				//print $sql;
				if (!$otf_key) {
					print "Error xxx";
				}



				if ($dn_key) {

					$sql = sprintf("update  `Order Transaction Fact` set `Current Autorized to Sell Quantity`=%f,`Estimated Weight`=%f,`Delivery Note ID`=%s,`Delivery Note Key`=%d ,`Destination Country 2 Alpha Code`=%s where `Order Transaction Fact Key`=%d"
						,$quantity
						,$estimated_weight
						,prepare_mysql ($dn->data ['Delivery Note ID'])
						,$dn_key
						,prepare_mysql($dn->data ['Delivery Note Country 2 Alpha Code'])
						,$otf_key

					);
					mysql_query($sql);
				}




			}

			if ($dn_key) {
				$dn->update_inventory_transaction_fact($otf_key,$quantity);

				$dn->update_item_totals();
			}

				$this->update_field('Order Last Updated Date',gmdate('Y-m-d H:i:s'),'no_history');
			
			if(
			in_array($this->data['Order Current Dispatch State'],array('In Process by Customer','In Process'))
			){
			
							$this->update_field('Order Date',gmdate('Y-m-d H:i:s'),'no_history');

			}
			

		}


		if (array_key_exists('Supplier Metadata', $data)) {

			$sql = sprintf( "update`Order Transaction Fact` set  `Supplier Metadata`=%s  where `Order Transaction Fact Key`=%d ",
				prepare_mysql($data['Supplier Metadata']),
				$otf_key

			);
			//        print "$sql\n";
			mysql_query($sql);
		}


		if (!$this->skip_update_after_individual_transaction) {

			$this->update_number_items();
			$this->update_number_products();
			$this->update_insurance();

			$this->update_discounts_items();
			$this->update_item_totals_from_order_transactions();



			$this->update_shipping($dn_key,false);
			$this->update_charges($dn_key,false);
			$this->update_discounts_no_items();



			$this->update_no_normal_totals();
			$this->update_totals_from_order_transactions();
			$this->update_number_items();
			$this->update_number_products();

			$this->apply_payment_from_customer_account();

		}

		//print "xx $gross $gross_discounts ";



		$net_amount=$gross-$gross_discounts;
		return array(
			'updated'=>true,
			'otf_key'=>$otf_key,
			'to_charge'=>money($net_amount,$this->data['Order Currency']),
			'net_amount'=>$net_amount,
			'delta_net_amount'=>$net_amount-$old_net_amount,
			'qty'=>$quantity,
			'delta_qty'=>$quantity-$old_quantity,
			'bonus qty'=>$bonus_quantity,
			'discount_percentage'=>($gross_discounts>0?percentage($gross_discounts,$gross,$fixed=1,$error_txt='NA',$psign=''):'')
		);

		//  print "$sql\n";


	}





	function update_discounts() {
		$this->update_discounts_items();
		$this->update_discounts_no_items();
	}



	function create_order_header() {




		//calculate the order total
		$this->data ['Order Items Gross Amount'] = 0;
		$this->data ['Order Items Discount Amount'] = 0;




		$sql = sprintf( "insert into `Order Dimension` (

		`Order Apply Auto Customer Account Payment`,

		`Order Tax Number`,`Order Tax Number Valid`,`Order Created Date`,
		`Order Payment Method`,
		`Order Customer Order Number`,`Order Tax Code`,`Order Tax Rate`,


                         `Order Customer Contact Name`,`Order For`,`Order File As`,`Order Date`,`Order Last Updated Date`,`Order Public ID`,`Order Store Key`,`Order Store Code`,`Order Main Source Type`,`Order Customer Key`,`Order Customer Name`,`Order Current Dispatch State`,`Order Current Payment State`,`Order Current XHTML Payment State`,`Order Customer Message`,`Order Original Data MIME Type`,`Order Items Gross Amount`,`Order Items Discount Amount`,`Order Original Metadata`,`Order XHTML Store`,`Order Type`,`Order Currency`,`Order Currency Exchange`,`Order Original Data Filename`,`Order Original Data Source`,
                         `Order Tax Name`,`Order Tax Operations`,`Order Tax Selection Type`

                         ) values
                         (%s,%s,%s,%s,%s,%d,%s,%f,

                         %s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s ,%.2f,%.2f,%s,%s,%s,%s,   %f,%s,%s,%s,%s,%s)",
			prepare_mysql ( $this->data ['Order Apply Auto Customer Account Payment'] ),
			prepare_mysql ( $this->data ['Order Tax Number'] ),
			prepare_mysql ( $this->data ['Order Tax Number Valid'] ),
			prepare_mysql ( $this->data ['Order Created Date'] ),
			prepare_mysql ($this->data ['Order Payment Method'] ),

			$this->data ['Order Customer Order Number'],
			prepare_mysql ($this->data ['Order Tax Code'],false ),
			$this->data ['Order Tax Rate'],




			prepare_mysql ( $this->data ['Order Customer Contact Name'],false ),
			prepare_mysql ( $this->data ['Order For'] ),
			prepare_mysql ( $this->data ['Order File As'] ),
			prepare_mysql ( $this->data ['Order Date'] ),
			prepare_mysql ( $this->data ['Order Date'] ),
			prepare_mysql ( $this->data ['Order Public ID'] ),
			prepare_mysql ( $this->data ['Order Store Key'] ),
			prepare_mysql ( $this->data ['Order Store Code'] ),

			prepare_mysql ( $this->data ['Order Main Source Type'] ),
			prepare_mysql ( $this->data ['Order Customer Key'] ),
			prepare_mysql ( $this->data ['Order Customer Name'] ,false),
			prepare_mysql ( $this->data ['Order Current Dispatch State'] ),
			prepare_mysql ( $this->data ['Order Current Payment State'] ),
			prepare_mysql ( $this->data ['Order Current XHTML Payment State'] ),
			prepare_mysql ( $this->data ['Order Customer Message'] ),
			prepare_mysql ( $this->data ['Order Original Data MIME Type'] ),


			$this->data ['Order Items Gross Amount'],
			$this->data ['Order Items Discount Amount'],
			prepare_mysql ( $this->data ['Order Original Metadata'] ),
			prepare_mysql ( $this->data ['Order XHTML Store'] ),
			prepare_mysql ( $this->data ['Order Type'] ),
			prepare_mysql( $this->data ['Order Currency'] ),
			$this->data ['Order Currency Exchange'],
			prepare_mysql( $this->data ['Order Original Data Filename'] ),
			prepare_mysql( $this->data ['Order Original Data Source'] ),
			prepare_mysql( $this->data ['Order Tax Name'] ),
			prepare_mysql( $this->data ['Order Tax Operations'] ),
			prepare_mysql( $this->data ['Order Tax Selection Type'] )




		)

		;

		if (mysql_query( $sql )) {
			$this->id = mysql_insert_id();
			$this->data ['Order Key'] = $this->id;
		}
		else {
			exit ( "$sql  Error coan not create order header");
		}

	}



	function get_data($key, $id) {
		if ($key == 'id') {
			$sql = sprintf( "select * from `Order Dimension` where `Order Key`=%d", $id );
			$result = mysql_query( $sql );
			if ($this->data = mysql_fetch_array( $result, MYSQL_ASSOC )) {
				$this->id = $this->data ['Order Key'];
			}


		}
		elseif ($key == 'public id' or $key == 'public_id') {
			$sql = sprintf( "select * from `Order Dimension` where `Order Public ID`=%s", prepare_mysql ( $id ) );
			$result = mysql_query( $sql );
			//print "$sql\n";
			if ($this->data = mysql_fetch_array( $result, MYSQL_ASSOC )) {
				$this->id = $this->data ['Order Key'];
			}




		}


		if ($this->id) {
			$this->set_display_currency($this->data['Order Currency'],1.0);
		}

	}
	function update_xhtml_state() {
		$xhtml_state=$this->calculate_state();
		$this->data['Order Current XHTML State']=$xhtml_state;

		$sql=sprintf("update `Order Dimension` set `Order Current XHTML State`=%s where `Order Key`=%d",
			prepare_mysql($xhtml_state,false),
			$this->id
		);

		mysql_query($sql);

	}

	function set_display_currency($currency_code,$exchange) {
		$this->currency_code=$currency_code;
		$this->exchange=$exchange;

	}


	function formated_net() {
		return money($this->data['Order Total Net Amount']-$this->data['Order Out of Stock Net Amount']-$this->data['Order No Authorized Net Amount']-$this->data['Order Not Found Net Amount']-$this->data['Order Not Due Other Net Amount'],$this->data['Order Currency']);
	}
	function formated_tax() {
		return money($this->data['Order Total Tax Amount']-$this->data['Order Out of Stock Tax Amount']-$this->data['Order No Authorized Tax Amount']-$this->data['Order Not Found Tax Amount']-$this->data['Order Not Due Other Tax Amount'],$this->data['Order Currency']);

	}
	function formated_total() {
		return money($this->data['Order Total Net Amount']+$this->data['Order Total Tax Amount']-$this->data['Order Out of Stock Net Amount']-$this->data['Order No Authorized Net Amount']-$this->data['Order Not Found Net Amount']-$this->data['Order Not Due Other Net Amount']-$this->data['Order Out of Stock Tax Amount']-$this->data['Order No Authorized Tax Amount']-$this->data['Order Not Found Tax Amount']-$this->data['Order Not Due Other Tax Amount'],$this->data['Order Currency']);

	}


	function get($key = '') {


		if (array_key_exists( $key, $this->data ))
			return $this->data [$key];


		if ($key=='Shipping Net Amount' and $this->data['Order Shipping Method']=='TBC') {
			return _('TBC');
		}

		if (preg_match('/^(Balance (Total|Net|Tax)|Invoiced Total Net Adjust|Invoiced Total Tax Adjust|Invoiced Refund Net|Invoiced Refund Tax|Total|Items|Invoiced Items|Invoiced Tax|Invoiced Net|Invoiced Charges|Payments|To Pay|Invoiced Shipping|(Shipping |Charges |Insurance )?Net).*(Amount)$/',$key)) {
			$amount='Order '.$key;
			return money($this->exchange*$this->data[$amount],$this->currency_code);
		}
		if (preg_match('/^Number (Items|Products)$/',$key)) {

			$amount='Order '.$key;

			return number($this->data[$amount]);
		}


		switch ($key) {
		case('Order Out of Stock Amount'):
			return $this->data['Order Out of Stock Net Amount']+$this->data['Order Out of Stock Tax Amount'];
		case('Out of Stock Amount'):
			return money(-1*($this->data['Order Out of Stock Net Amount']+$this->data['Order Out of Stock Tax Amount']),$this->data['Order Currency']);
		case('Invoiced Total Tax Amount'):
			return money($this->data['Order Invoiced Tax Amount']+$this->data['Order Invoiced Refund Tax Amount'],$this->data['Order Currency']);
			break;
		case('Out of Stock Net Amount'):
			return money(-1*$this->data['Order Out of Stock Net Amount'],$this->data['Order Currency']);
			break;
		case('Not Found Net Amount'):
			return money(-1*$this->data['Order Not Found Net Amount'],$this->data['Order Currency']);
			break;
		case('Not Due Other Net Amount'):
			return money(-1*$this->data['Order Not Due Other Net Amount'],$this->data['Order Currency']);
			break;
		case('No Authorized Net Amount'):
			return money(-1*$this->data['Order No Authorized Net Amount'],$this->data['Order Currency']);
			break;
		case('Invoiced Total Net Amount'):
			return money($this->data['Order Invoiced Net Amount']+$this->data['Order Invoiced Refund Net Amount'],$this->data['Order Currency']);
			break;
		case('Invoiced Total Amount'):
			return money($this->data['Order Invoiced Net Amount']+$this->data['Order Invoiced Tax Amount']+$this->data['Order Invoiced Refund Net Amount']+$this->data['Order Invoiced Refund Tax Amount'],$this->data['Order Currency']);
			break;
		case('Shipping And Handing Net Amount'):
			return money($this->data['Order Shipping Net Amount']+$this->data['Order Charges Net Amount']);
			break;
		case('Date'):
		case('Last Updated Date'):
		case('Cancelled Date'):
		case('Created Date'):

		case('Suspended Date'):
		case('Checkout Submitted Payment Date'):
		case('Checkout Completed Payment Date'):
		case('Submitted by Customer Date'):
		case('Dispatched Date'):
		case('Post Transactions Dispatched Date'):

			return strftime("%a %e %b %Y %H:%M %Z",strtotime($this->data['Order '.$key].' +0:00'));
			break;

		case('Interval Last Updated Date'):
			include_once 'common_natural_language.php';
			return seconds_to_string(gmdate('U')-gmdate('U',strtotime($this->data['Order Last Updated Date'].' +0:00')));
			break;



		case ('Order Main Ship To Key') :
			$sql = sprintf( "select `Ship To Key`,count(*) as  num from `Order Transaction Fact` where `Order Key`=%d group by `Ship To Key` order by num desc limit 1", $this->id );
			$res = mysql_query( $sql );
			if ($row2 = mysql_fetch_array( $res, MYSQL_ASSOC )) {
				return $row2 ['Ship To Key'];
			} else
				return '';

			break;
		case ('Order Main Billing To Key') :
			$sql = sprintf( "select `Billing To Key`,count(*) as  num from `Order Transaction Fact` where `Order Key`=%d group by `Billing To Key` order by num desc limit 1", $this->id );
			$res = mysql_query( $sql );
			if ($row2 = mysql_fetch_array( $res, MYSQL_ASSOC )) {
				return $row2 ['Billing To Key'];
			} else
				return '';

			break;

		case ('Weight'):
			if ($this->data['Order Current Dispatch State']=='Dispatched') {
				if ($this->data['Order Weight']=='')
					return weight($this->data['Order Dispatched Estimated Weight']);
				else
					return weight($this->data['Order Weight']);
			} else {
				return weight($this->data['Order Estimated Weight']);
			}
			break;


		case ('Current Dispatch State'):
			//'In Process by Customer','In Process','Submitted by Customer','Ready to Pick','Picking & Packing','Ready to Ship','Dispatched','Unknown','Packing','Packed','Cancelled','Suspended'  case('Current Dispatch State'):
			switch ($key) {
			case 'In Process':
				return _('In Process');
				break;
			case 'In Process by Customer':
				return _('In Process by Customer');
				break;
			case 'Submitted by Customer':
				return _('Submitted by Customer');
				break;
			case 'Ready to Pick':
				return _('Ready to Pick');
				break;
			case 'Picking & Packing':
				return _('Picking & Packing');
				break;
			case 'Packed Done':
				return _('Packed & Checked');
				break;
			case 'Ready to Ship':
				return _('Ready to Ship');
				break;
			case 'Dispatched':
				return _('Dispatched');
				break;
			case 'Unknown':
				return _('Unknown');
				break;
			case 'Packing':
				return _('Packing');
				break;
			case 'Cancelled':
				return _('Cancelled');
				break;
			case 'Suspended':
				return _('Suspended');
				break;

			default:
				return $this->data['Order Current Dispatch State'];
				break;
			}

			break;
		}
		$_key = ucwords( $key );
		if (array_key_exists( $_key, $this->data ))
			return $this->data [$_key];

		return false;
	}





	function get_delivery_notes_ids() {
		$sql=sprintf("select `Delivery Note Key` from `Order Transaction Fact` where `Order Key`=%d group by `Delivery Note Key`",$this->id);
		//print "$sql\n";
		$res = mysql_query( $sql );
		$delivery_notes=array();
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {
			if ($row['Delivery Note Key']) {
				$delivery_notes[$row['Delivery Note Key']]=$row['Delivery Note Key'];
			}

		}
		return $delivery_notes;

	}
	function get_delivery_notes_objects() {
		$delivery_notes=array();
		$delivery_notes_ids=$this->get_delivery_notes_ids();
		foreach ($delivery_notes_ids as $delivery_notes_id) {
			$delivery_notes[$delivery_notes_id]=new DeliveryNote($delivery_notes_id);
		}
		return $delivery_notes;
	}


	function get_invoices_to_pay_abs_amount() {
		$invoices_to_pay_abs_amount=0;
		$sql=sprintf("select sum(abs(`Invoice Outstanding Total Amount`)) as amount from `Order Invoice Bridge` B left join `Invoice Dimension` I on (I.`Invoice Key`=B.`Invoice Key`) where `Order Key`=%d",
			$this->id
		);
		$res = mysql_query( $sql );
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {
			$invoices_to_pay_abs_amount=$row['amount'];
		}
		return $invoices_to_pay_abs_amount;
	}

	function get_invoices_ids() {
		$invoices=array();


		$sql=sprintf("select `Invoice Key` from `Order Transaction Fact` where `Order Key`=%d group by `Invoice Key`",$this->id);
		$res = mysql_query( $sql );
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {
			if ($row['Invoice Key']) {
				$invoices[$row['Invoice Key']]=$row['Invoice Key'];
			}
		}
		$sql=sprintf("select `Refund Key` from `Order Transaction Fact` where `Order Key`=%d group by `Refund Key`",$this->id);
		$res = mysql_query( $sql );
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {
			if ($row['Refund Key']) {
				$invoices[$row['Refund Key']]=$row['Refund Key'];
			}
		}

		$sql=sprintf("select `Invoice Key` from `Order No Product Transaction Fact` where `Order Key`=%d group by `Invoice Key`",$this->id);
		$res = mysql_query( $sql );
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {
			if ($row['Invoice Key']) {
				$invoices[$row['Invoice Key']]=$row['Invoice Key'];
			}
		}
		$sql=sprintf("select `Refund Key` from `Order No Product Transaction Fact` where `Order Key`=%d group by `Refund Key`",$this->id);
		$res = mysql_query( $sql );
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {
			if ($row['Refund Key']) {
				$invoices[$row['Refund Key']]=$row['Refund Key'];
			}
		}


		return $invoices;

	}

	function get_number_invoices() {

		return count($this->get_invoices_ids());
	}


	function get_invoices_objects() {
		$invoices=array();
		$invoices_ids=$this->get_invoices_ids();
		foreach ($invoices_ids as $order_id) {
			$invoices[$order_id]=new Invoice($order_id);
		}
		return $invoices;
	}









	function update_field_switcher($field,$value,$options='') {

		switch ($field) {
		case('Order Tax Number'):
			$this->update_field($field,$value,$options);

			$this->update_tax();

			break;

		case('Order XHTML Invoices'):
			$this->update_xhtml_invoices();
			break;
		case('Order XHTML Delivery Notes'):
			$this->update_xhtml_delivery_notes();
			break;
		default:
			$base_data=$this->base_data();


			if (array_key_exists($field,$base_data)) {
				// print "xxx-> $field : $value -> ".$this->data[$field]." \n";

				if ($value!=$this->data[$field]) {

					$this->update_field($field,$value,$options);
				}
			}
		}

	}



	function update_xhtml_invoices() {
		$prefix='';
		$this->data ['Order XHTML Invoices'] ='';
		foreach ($this->get_invoices_objects() as $invoice) {

			if ($invoice->get('Invoice Paid')=='Yes')
				$state='<img src="art/icons/money.png" style="height:14px">';

			else

				$state='<img src="art/icons/money_bw.png" style="width:14px">';

			$this->data ['Order XHTML Invoices'] .= sprintf( ' %s <a href="invoice.php?id=%d">%s%s</a> <a href="invoice.pdf.php?id=%d" target="_blank"><img style="height:10px;position:relative;bottom:2.5px" src="art/pdf.gif" alt=""></a><br/>',
				$state,
				$invoice->data ['Invoice Key'],
				$prefix,
				$invoice->data ['Invoice Public ID'],
				$invoice->data ['Invoice Key'] );
		}
		$this->data ['Order XHTML Invoices'] =_trim(preg_replace('/\<br\/\>$/','',$this->data ['Order XHTML Invoices']));
		$sql=sprintf("update `Order Dimension` set `Order XHTML Invoices`=%s where `Order Key`=%d "
			,prepare_mysql($this->data['Order XHTML Invoices'])
			,$this->id
		);
		mysql_query($sql);
	}

	function update_xhtml_delivery_notes() {
		$prefix='';
		$this->data ['Order XHTML Delivery Notes'] ='';
		foreach ($this->get_delivery_notes_objects() as $delivery_note) {
			//'Picker & Packer Assigned','Picking & Packing','Packer Assigned','Ready to be Picked','Picker Assigned','Picking','Picked','Packing','Packed','Approved','Dispatched','Cancelled','Cancelled to Restock','Packed Done'

			//print $delivery_note->get('Delivery Note State');

			if ($delivery_note->get('Delivery Note State')=='Dispatched')
				$state='<img src="art/icons/lorry.png" style="height:14px">';
			else if ($delivery_note->get('Delivery Note State')=='Packed Done')
					$state='<img src="art/icons/package.png" style="height:14px">';
				else if ($delivery_note->get('Delivery Note State')=='Approved')
						$state='<img src="art/icons/package_green.png" style="height:14px">';
					else

						$state='<img src="art/icons/cart.png" style="width:14px">';

					$this->data ['Order XHTML Delivery Notes'] .= sprintf( '%s <a href="dn.php?id=%d">%s%s</a> <a href="dn.pdf.php?id=%d" target="_blank"><img style="height:10px;position:relative;bottom:2.5px" src="art/pdf.gif" alt=""></a><br/>',
						$state,
						$delivery_note->data ['Delivery Note Key'],
						$prefix,
						$delivery_note->data ['Delivery Note ID'], $delivery_note->data ['Delivery Note Key'] );
		}
		$this->data ['Order XHTML Delivery Notes'] =_trim(preg_replace('/\<br\/\>$/','',$this->data ['Order XHTML Delivery Notes']));

		$sql=sprintf("update `Order Dimension` set `Order XHTML Delivery Notes`=%s where `Order Key`=%d "
			,prepare_mysql($this->data['Order XHTML Delivery Notes'])
			,$this->id
		);
		mysql_query($sql);
	}


	function cutomer_rankings() {
		$sql = sprintf( "select `Customer Key` as id,`Customer Orders` as orders, (select count(*) from `Customer Dimension` as TC where TC.`Customer Orders`<C.`Customer Orders`) as better,(select count(DISTINCT `Customer Key` ) from `Customer Dimension`) total  from `Customer Dimension` as C order by `Customer Orders` desc ;" );

		$orders = - 99999;
		$position = 0;

		$result = mysql_query( $sql );
		while ( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) ) {

			if ($row ['orders'] != $orders) {
				$position ++;
				$orders = $row ['orders'];
			}
			$better_than = $row ['better'];
			$total = $row ['total'];
			if ($total > 0)
				$top = sprintf( "%f", 100 * (1.0 - ($better_than / $total)) );
			else
				$top = 'null';
			$id = $row ['id'];
			$sql = sprintf( "update `Customer Dimension` set `Customer Orders Top Percentage`=%s,`Customer Orders Position`=%d,`Customer Has More Orders Than`=%d where `Customer Key`=%d", $top, $position, $better_than, $id );
			// print "$sql\n";
			mysql_query( $sql );
		}
	}

	function compare_addresses($cdata) {

		//check if the addresses are the same:
		$diff_result = array_diff( $cdata ['address_data'], $cdata ['shipping_data'] );

		if (count( $diff_result ) == 0) {

			$this->same_address = true;
			$this->same_contact = true;
			$this->same_company = true;

			$this->same_telephone = true;

		} else {


			$percentage = array ('address1' => 1, 'town' => 1, 'country' => 1, 'country_d1' => 1, 'postcode' => 1 );
			$percentage_address = array ();

			foreach ( $diff_result as $key => $value ) {
				similar_text( $cdata ['shipping_data'] [$key], $cdata ['address_data'] [$key], $p );
				$percentage [$key] = $p / 100;
				if (preg_match( '/address1|town|^country$|postcode|country_d1/i', $key ))
					$percentage_address [$key] = $p / 100;
			}
			if (count( $percentage ) == 0)
				$avg_percentage = 1;
			else
				$avg_percentage = average ( $percentage );

			if (count( $percentage_address ) == 0)
				$avg_percentage_address = 1;
			else
				$avg_percentage_address = average ( $percentage_address );

			//   print "AVG DIFF O:$avg_percentage A:$avg_percentage_address \n";


			if ($cdata ['shipping_data'] ['name'] == '' or ! array_key_exists( 'name', $diff_result ))
				$this->same_contact = true;
			else {
				$_max = 1000000;
				$irand = mt_rand( 0, 1000000 );
				$rand = $irand / $_max;
				if ($rand < $percentage ['name'] and $percentage ['name'] > .90) {
					$this->same_contact = true;

				} else
					$this->same_contact = false;
			}
			if ($cdata ['shipping_data'] ['company'] == '' or ! array_key_exists( 'company', $diff_result ))
				$this->same_company = true;
			else {
				$_max = 1000000;
				$irand = mt_rand( 0, 1000000 );
				$rand = $irand / $_max;

				if ($rand < $percentage ['company'] and $percentage ['company'] > .90) {
					$this->same_company = true;
				} else
					$this->same_company = false;
			}

			if (array_key_exists( 'telephone', $diff_result ))
				$this->same_telephone = false;
			else
				$this->same_telephone = true;

			if ($avg_percentage_address == 1)
				$this->same_address = true;
			else
				$this->same_address = false;


		}

	}


	function update_product_sales() {
		return;
		if ($this->skip_update_product_sales)
			return;




		$stores=array();
		$family=array();
		$departments=array();
		$sql = "select OTF.`Product Key` ,`Product Family Key`,`Product Store Key` from `Order Transaction Fact` OTF left join `Product Dimension` PD on (PD.`Product Key`=OTF.`Product Key`)where `Order Key`=" . $this->data ['Order Key']." group by OTF.`Product Key`";
		$result = mysql_query( $sql );
		//   print $sql;
		if ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {
			$product=new Product($row['Product Key']);
			$product->update_sales();
			$family[$row['Product Family Key']]=true;
			$store[$row['Product Store Key']]=true;
		}
		foreach ($family as $key=>$val) {
			$family=new Family($key);
			$family->update_sales_data();
			$sql = sprintf("select `Product Department Key`  from `Product Family Department Bridge` where `Product Family Key`=%d" ,$key);
			$result = mysql_query( $sql );
			while ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {
				$departments[$row['Product Department Key']]=true;
			}

		}
		foreach ($departments as $key=>$val) {
			$department=new Department($key);
			$department->update_sales_data();
		}


		foreach ($store as $key=>$val) {
			$store=new Store($key);
			$store->update_sales();
		}

	}



	function get_items_totals_by_adding_transactions() {



		$sql = sprintf("select sum(`Estimated Dispatched Weight`) as disp_estimated_weight,sum(`Estimated Weight`) as estimated_weight,sum(`Weight`) as weight,
		sum(`Transaction Tax Rate`*(`Order Transaction Amount`)) as tax,
		sum(`Order Transaction Gross Amount`) as gross,sum(`Order Transaction Total Discount Amount`) as discount, sum(`Order Transaction Amount`) as total_items_net,sum(`Invoice Transaction Shipping Amount`) as shipping,sum(`Invoice Transaction Charges Amount`) as charges    from `Order Transaction Fact` where  `Order Key`=%d" ,
			$this->id);
		// print "$sql\n";
		$result = mysql_query( $sql );
		if ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {

			$this->data ['Order Items Gross Amount'] = round($row ['gross'],2);
			$this->data ['Order Items Discount Amount'] =  round($row ['discount'],2);
			$this->data ['Order Items Net Amount'] =  round($row ['total_items_net'],2);
			$this->data ['Order Items Tax Amount']=  round($row ['tax'],2);
			$this->data ['Order Items Total Amount']= $this->data ['Order Items Net Amount'] +$this->data ['Order Items Tax Amount'];
			$this->data ['Order Estimated Weight']= $row ['estimated_weight'];
			$this->data ['Order Dispatched Estimated Weight']= $row ['disp_estimated_weight'];



		}

	}



	function accept() {
		$this->data['Order Invoiced Balance Net Amount']=$this->data ['Order Items Net Amount'];
		$this->data['Order Invoiced Balance Tax Amount']=$this->data ['Order Items Tax Amount'];
		$this->data['Order Invoiced Balance Total Amount']=$this->data ['Order Items Total Amount'];

	}


	function update_no_normal_totals($args='') {

		$this->data['Order Balance Net Amount']=0;
		$this->data['Order Balance Tax Amount']=0;
		$this->data['Order Balance Total Amount']=0;
		$this->data['Order Outstanding Balance Net Amount']=0;
		$this->data['Order Outstanding Balance Tax Amount']=0;
		$this->data['Order Outstanding Balance Total Amount']=0;

		$this->data['Order Invoiced Balance Net Amount']=0;
		$this->data['Order Invoiced Balance Tax Amount']=0;
		$this->data['Order Invoiced Balance Total Amount']=0;
		$this->data['Order Invoiced Outstanding Balance Net Amount']=0;
		$this->data['Order Invoiced Outstanding Balance Tax Amount']=0;
		$this->data['Order Invoiced Outstanding Balance Total Amount']=0;
		$this->data['Order Invoiced Refund Net Amount']=0;
		$this->data['Order Invoiced Refund Tax Amount']=0;
		$this->data['Order Invoiced Refund Notes']='';

		$this->data['Order Tax Credited Amount']=0;
		$this->data['Order Net Credited Amount']=0;
		$this->data['Order Tax Refund Amount']=0;
		$this->data['Order Net Refund Amount']=0;

		$sql = "select count(*) as number_otfs,
               sum(IFNULL(`Cost Supplier`,0)+IFNULL(`Cost Storing`,0)+IFNULL(`Cost Handing`,0)+IFNULL(`Cost Shipping`,0))as costs,
               sum(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`) as net,
               sum(`Invoice Transaction Item Tax Amount`) as tax,
                sum(`Order Transaction Amount`) as original_net,
               sum(`Order Transaction Amount`*`Transaction Tax Rate`) as original_tax,
               sum(`Invoice Transaction Net Refund Amount`) as ref_net,
               sum(`Invoice Transaction Tax Refund Amount`) as ref_tax,
               sum(`Invoice Transaction Outstanding Net Balance`) as ob_net ,
               sum(`Invoice Transaction Outstanding Tax Balance`) as ob_tax ,
               sum(`Invoice Transaction Outstanding Refund Net Balance`) as ref_ob_net ,
               sum(`Invoice Transaction Outstanding Refund Tax Balance`) as ref_ob_tax ,

               sum(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`) as inv_items,
               sum(`Invoice Transaction Shipping Amount`) as inv_shp,
               sum(`Invoice Transaction Charges Amount`) as inv_charges,
               sum(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`+`Invoice Transaction Shipping Amount`+`Invoice Transaction Charges Amount`+`Invoice Transaction Net Adjust`) as inv_net,
               sum(`Invoice Transaction Item Tax Amount`+`Invoice Transaction Shipping Tax Amount`+`Invoice Transaction Charges Tax Amount`+`Invoice Transaction Tax Adjust`) as inv_tax,


               sum(`Order Out of Stock Lost Amount`) as out_of_stock_net,
               sum(`Order Out of Stock Lost Amount`*`Transaction Tax Rate`) as out_of_stock_tax,
              sum(if(`Order Quantity`>0, `No Shipped Due No Authorized`*(`Order Transaction Amount`)/`Order Quantity`,0)) as not_authorized_net,
               sum(if(`Order Quantity`>0, `No Shipped Due No Authorized`*`Transaction Tax Rate`*(`Order Transaction Amount`)/`Order Quantity`,0)) as not_authorized_tax,
              sum(if(`Order Quantity`>0, `No Shipped Due Not Found`*(`Order Transaction Amount`)/`Order Quantity`,0)) as not_found_net,
               sum(if(`Order Quantity`>0, `No Shipped Due Not Found`*`Transaction Tax Rate`*(`Order Transaction Amount`)/`Order Quantity`,0)) as not_found_tax,
              sum(if(`Order Quantity`>0, `No Shipped Due Other`*(`Order Transaction Amount`)/`Order Quantity`,0)) as not_due_other_net,
               sum(if(`Order Quantity`>0, `No Shipped Due Other`*`Transaction Tax Rate`*(`Order Transaction Amount`)/`Order Quantity`,0)) as not_due_other_tax



               from `Order Transaction Fact`    where  `Order Key`=" . $this->data ['Order Key'];

		$result = mysql_query( $sql );
		//print "\n$sql\n";
		if ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {
			$number_otfs=$row['number_otfs'];
			//print_r($row);
			$this->data['Order Invoiced Balance Net Amount']=$row['net']+$row['ref_net'];
			$this->data['Order Invoiced Balance Tax Amount']=$row['tax']+$row['ref_tax'];
			$this->data['Order Invoiced Balance Total Amount']=$this->data['Order Invoiced Balance Net Amount']+$this->data['Order Invoiced Balance Tax Amount'];
			$this->data['Order Invoiced Outstanding Balance Net Amount']=$row['ob_net']+$row['ref_ob_net'];
			$this->data['Order Invoiced Outstanding Balance Tax Amount']=$row['ob_tax']+$row['ref_ob_tax'];
			$this->data['Order Invoiced Outstanding Balance Total Amount']=$this->data['Order Invoiced Outstanding Balance Net Amount']+$this->data['Order Invoiced Outstanding Balance Tax Amount'];

			$total_not_dispatch_net=$row['out_of_stock_net']+$row['not_authorized_net']+$row['not_found_net']+$row['not_due_other_net'];
			$total_not_dispatch_tax=$row['out_of_stock_tax']+$row['not_authorized_tax']+$row['not_found_tax']+$row['not_due_other_tax'];

			//print $row['net'].'xx';


			$this->data['Order Balance Net Amount']=round($row['original_net']+$row['ref_net']-$total_not_dispatch_net,2);
			$this->data['Order Balance Tax Amount']=round($row['original_tax']+$row['ref_tax']-$total_not_dispatch_tax,2);
			$this->data['Order Balance Total Amount']=$this->data['Order Balance Net Amount']+$this->data['Order Balance Tax Amount'];
			$this->data['Order Outstanding Balance Net Amount']=$this->data['Order Balance Net Amount']-$this->data['Order Invoiced Balance Net Amount']+$this->data['Order Invoiced Outstanding Balance Net Amount'];
			$this->data['Order Outstanding Balance Tax Amount']=$this->data['Order Balance Tax Amount']-$this->data['Order Invoiced Balance Tax Amount']+$this->data['Order Invoiced Outstanding Balance Tax Amount'];
			$this->data['Order Outstanding Balance Total Amount']=$this->data['Order Balance Total Amount']-$this->data['Order Invoiced Balance Total Amount']+$this->data['Order Invoiced Outstanding Balance Total Amount'];



			//  $this->data['Order Tax Refund Invoiced Amount']=$row['ref_tax'];
			//  $this->data['Order Net Refund Invoiced Amount']=$row['ref_net'];
			//print_r($row);

			$this->data['Order Invoiced Items Amount']=$row['inv_items'];
			$this->data['Order Invoiced Shipping Amount']=$row['inv_shp'];
			$this->data['Order Invoiced Charges Amount']=$row['inv_charges'];
			$this->data['Order Invoiced Net Amount']=$row['inv_net'];
			$this->data['Order Invoiced Tax Amount']=$row['inv_tax'];
			$this->data['Order Invoiced Refund Net Amount']=$row['ref_net'];
			$this->data['Order Invoiced Refund Tax Amount']=$row['ref_tax'];


			$this->data['Order Out of Stock Net Amount']=$row['out_of_stock_net'];
			$this->data['Order Out of Stock Tax Amount']=$row['out_of_stock_tax'];

			$this->data['Order No Authorized Net Amount']=$row['not_authorized_net'];
			$this->data['Order No Authorized Tax Amount']=$row['not_authorized_tax'];

			$this->data['Order Not Found Net Amount']=$row['not_found_net'];
			$this->data['Order Not Found Tax Amount']=$row['not_found_tax'];

			$this->data['Order Not Due Other Net Amount']=$row['not_due_other_net'];
			$this->data['Order Not Due Other Tax Amount']=$row['not_due_other_tax'];

			$this->data['Order Invoiced Profit Amount']= $this->data['Order Invoiced Balance Net Amount']-$this->data['Order Invoiced Outstanding Balance Net Amount']- $row['costs'];
			$this->data['Order Profit Amount']= $this->data['Order Balance Net Amount']-$this->data['Order Outstanding Balance Net Amount']- $row['costs'];

		}



		$sql = sprintf("select * from `Order No Product Transaction Fact` where `Order Key`=%d" , $this->data ['Order Key']);
		//print "$sql\n";
		$result = mysql_query( $sql );
		while ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {

			$this->data['Order Invoiced Balance Net Amount']+=$row['Transaction Invoice Net Amount'];
			$this->data['Order Invoiced Balance Tax Amount']+=$row['Transaction Invoice Tax Amount'];
			$this->data['Order Invoiced Balance Total Amount']+=$row['Transaction Invoice Net Amount']+$row['Transaction Invoice Tax Amount'];
			$this->data['Order Invoiced Outstanding Balance Net Amount']+=$row['Transaction Outstanding Net Amount Balance'];
			$this->data['Order Invoiced Outstanding Balance Tax Amount']+=$row['Transaction Outstanding Tax Amount Balance'];
			$this->data['Order Invoiced Outstanding Balance Total Amount']+=$row['Transaction Outstanding Net Amount Balance']+$row['Transaction Outstanding Tax Amount Balance'];

			// print "xx ".$row['Transaction Net Amount']." \n";
			$this->data['Order Balance Net Amount']+=$row['Transaction Net Amount'];
			$this->data['Order Balance Tax Amount']+=$row['Transaction Tax Amount'];
			$this->data['Order Balance Total Amount']+=$row['Transaction Net Amount']+$row['Transaction Tax Amount'];
			$this->data['Order Outstanding Balance Net Amount']+=$row['Transaction Net Amount']-$row['Transaction Invoice Net Amount']+$row['Transaction Outstanding Net Amount Balance'];
			$this->data['Order Outstanding Balance Tax Amount']+=$row['Transaction Tax Amount']-$row['Transaction Invoice Tax Amount']+$row['Transaction Outstanding Tax Amount Balance'];
			$this->data['Order Outstanding Balance Total Amount']+=$row['Transaction Net Amount']-$row['Transaction Invoice Net Amount']+$row['Transaction Outstanding Net Amount Balance']+$row['Transaction Tax Amount']-$row['Transaction Invoice Tax Amount']+$row['Transaction Outstanding Tax Amount Balance'];


			if ( $row['Transaction Type']=='Adjust') {

				$this->data['Order Invoiced Net Amount']+=$row['Transaction Invoice Net Amount'];
				$this->data['Order Invoiced Tax Amount']+=$row['Transaction Invoice Tax Amount'];

			}


			if ( $row['Transaction Type']=='Credit') {
				$this->data['Order Tax Credited Amount']+=$row['Transaction Tax Amount'];
				$this->data['Order Net Credited Amount']+=$row['Transaction Net Amount'];
				$this->data['Order Invoiced Refund Net Amount']+=$row['Transaction Invoice Net Amount'];
				$this->data['Order Invoiced Refund Tax Amount']+=$row['Transaction Invoice Tax Amount'];

			}else if ($row['Transaction Type']=='Refund') {
					$this->data['Order Tax Refund Amount']+=$row['Transaction Tax Amount'];
					$this->data['Order Net Refund Amount']+=$row['Transaction Net Amount'];
					$this->data['Order Invoiced Refund Net Amount']+=$row['Transaction Invoice Net Amount'];
					$this->data['Order Invoiced Refund Tax Amount']+=$row['Transaction Invoice Tax Amount'];
				}  else if ($row['Transaction Type']=='Adjust') {
					$this->data['Order Invoiced Total Net Adjust Amount']+=$row['Transaction Invoice Net Amount'];
					$this->data['Order Invoiced Total Tax Adjust Amount']+=$row['Transaction Invoice Tax Amount'];
				}

		}

		//print_r($this->data);


		$oustanding_invoiced_refund_net=0;
		$oustanding_invoiced_refund_tax=0;

		$sql = sprintf("select * from `Order No Product Transaction Fact` where `Transaction Type` in ('Refund','Credit') and `Affected Order Key`=%d and `Order Key`!=%d" , $this->id,$this->id);

		$result = mysql_query( $sql );
		while ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {

			$this->data['Order Invoiced Refund Net Amount']+=$row['Transaction Invoice Net Amount'];
			$this->data['Order Invoiced Refund Tax Amount']+=$row['Transaction Invoice Tax Amount'];
			$oustanding_invoiced_refund_net+=$row['Transaction Outstanding Net Amount Balance'];
			$oustanding_invoiced_refund_tax+=$row['Transaction Outstanding Tax Amount Balance'];
			if ($row['Transaction Description']!='')
				$this->data['Order Invoiced Refund Notes'].='<br/>'.$row['Transaction Description'];
		}


		if ($number_otfs==0) {
			$net=0;$tax=0;
			$sql = sprintf("select sum(`Transaction Net Amount`) as amount,sum(`Transaction Tax Amount`) as tax from `Order No Product Transaction Fact` where `Transaction Type`='Shipping'  and `Order Key`=%d" , $this->data ['Order Key']);

			$result = mysql_query( $sql );
			while ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {

				$this->data['Order Invoiced Shipping Amount']=$row['amount'];
				$net+=$row['amount'];
				$tax+=$row['tax'];

			}
			$sql = sprintf("select sum(`Transaction Net Amount`) as amount,sum(`Transaction Tax Amount`) as tax  from `Order No Product Transaction Fact` where `Transaction Type`='Charges'  and `Order Key`=%d" , $this->data ['Order Key']);
			//print "$sql\n";
			$result = mysql_query( $sql );
			while ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {
				//print_r($row);
				$this->data['Order Invoiced Charges Amount']=$row['amount'];


				$this->data['Order Charges Net Amount']=$row['amount'];
				$this->data['Order Charges Tax Amount']=$row['tax'];




				$net+=$row['amount'];
				$tax+=$row['tax'];

			}

			$sql = sprintf("select sum(`Transaction Net Amount`) as amount,sum(`Transaction Tax Amount`) as tax  from `Order No Product Transaction Fact` where `Transaction Type`='Insurance'  and `Order Key`=%d" , $this->data ['Order Key']);
			//print "$sql\n";
			$result = mysql_query( $sql );
			while ($row = mysql_fetch_array( $result, MYSQL_ASSOC )) {
				//print_r($row);
				$this->data['Order Invoiced Charges Amount']=$row['amount'];


				$this->data['Order Insurance Net Amount']=$row['amount'];
				$this->data['Order Insurance Tax Amount']=$row['tax'];




				$net+=$row['amount'];
				$tax+=$row['tax'];

			}


			$this->data['Order Invoiced Net Amount']=$net;
			$this->data['Order Invoiced Tax Amount']=$tax;


		}
		if ($this->data['Order Out of Stock Net Amount']>0) {
			$this->data['Order with Out of Stock']='Yes';
		}else {
			$this->data['Order with Out of Stock']='No';
		}

		//$this->data['Order Balance Net Amount']=$this->data['Order Balance Net Amount']+$this->data['Order Net Credited Amount']+$this->data['Order Net Refund Amount'];
		//$this->data['Order Balance Tax Amount']=$this->data['Order Balance Tax Amount']+$this->data['Order Tax Credited Amount']+$this->data['Order Tax Refund Amount'];
		//$this->data['Order Balance Total Amount']=$this->data['Order Balance Net Amount']+$this->data['Order Balance Tax Amount'];
		//$this->data['Order Outstanding Balance Net Amount']=$this->data['Order Outstanding Balance Net Amount']-$this->data['Order Invoiced Refund Net Amount']+$oustanding_invoiced_refund_net;
		//$this->data['Order Outstanding Balance Tax Amount']=$this->data['Order Outstanding Balance Tax Amount']-$this->data['Order Invoiced Refund Tax Amount']+$oustanding_invoiced_refund_tax;
		//$this->data['Order Outstanding Balance Total Amount']=$this->data['Order Outstanding Balance Net Amount']+$this->data['Order Outstanding Balance Tax Amount'];


		$this->data['Order Invoiced Refund Notes']=preg_replace('/<br\/>/','',$this->data['Order Invoiced Refund Notes']);


		$this->data['Order To Pay Amount']=$this->data['Order Balance Total Amount']-$this->data['Order Payments Amount'];

		$sql=sprintf("update `Order Dimension` set

		`Order To Pay Amount`=%.2f,
		`Order Payments Amount`=%.2f,

                     `Order Invoiced Balance Net Amount`=%.2f,`Order Invoiced Balance Tax Amount`=%.2f,`Order Invoiced Balance Total Amount`=%.2f,
                     `Order Invoiced Outstanding Balance Net Amount`=%.2f,`Order Invoiced Outstanding Balance Tax Amount`=%.2f,`Order Invoiced Outstanding Balance Total Amount`=%.2f,
                     `Order Tax Refund Amount`=%.2f,`Order Net Refund Amount`=%.2f,
                      `Order Tax Credited Amount`=%.2f,`Order Net Credited Amount`=%.2f,
                     `Order Invoiced Profit Amount`=%.2f,
                     `Order Invoiced Items Amount`=%.2f,`Order Invoiced Shipping Amount`=%.2f,`Order Invoiced Charges Amount`=%.2f,
                     `Order Invoiced Net Amount`=%.2f,`Order Invoiced Tax Amount`=%.2f,
                     `Order Out of Stock Net Amount`=%.2f,
                     `Order Out of Stock Tax Amount`=%.2f,
                     `Order No Authorized Net Amount`=%.2f,
                     `Order No Authorized Tax Amount`=%.2f,
                     `Order Not Found Net Amount`=%.2f,
                     `Order Not Found Tax Amount`=%.2f,
                     `Order Not Due Other Net Amount`=%.2f,
                     `Order Not Due Other Tax Amount`=%.2f,

                     `Order Invoiced Refund Net Amount`=%.2f,
                     `Order Invoiced Refund Tax Amount`=%.2f,
                     `Order Invoiced Refund Notes`=%s,
                     `Order Invoiced Total Net Adjust Amount`=%.2f,
                     `Order Invoiced Total Tax Adjust Amount`=%.2f,
                      `Order Balance Net Amount`=%.2f,`Order Balance Tax Amount`=%.2f,`Order Balance Total Amount`=%.2f,
                     `Order Outstanding Balance Net Amount`=%.2f,`Order Outstanding Balance Tax Amount`=%.2f,`Order Outstanding Balance Total Amount`=%.2f,
                      `Order Profit Amount`=%.2f,
					`Order with Out of Stock`=%s


                     where `Order Key`=%d",
			$this->data['Order To Pay Amount'],

			$this->data['Order Payments Amount'],
			$this->data['Order Invoiced Balance Net Amount'],
			$this->data['Order Invoiced Balance Tax Amount'],
			$this->data['Order Invoiced Balance Total Amount'],

			$this->data['Order Invoiced Outstanding Balance Net Amount'],
			$this->data['Order Invoiced Outstanding Balance Tax Amount'],
			$this->data['Order Invoiced Outstanding Balance Total Amount'],

			$this->data['Order Tax Refund Amount'],
			$this->data['Order Net Refund Amount'],
			$this->data['Order Tax Credited Amount'],
			$this->data['Order Net Credited Amount'],

			$this->data['Order Invoiced Profit Amount'],

			$this->data['Order Invoiced Items Amount'],
			$this->data['Order Invoiced Shipping Amount'],
			$this->data['Order Invoiced Charges Amount'],

			$this->data['Order Invoiced Net Amount'],
			$this->data['Order Invoiced Tax Amount'],

			$this->data['Order Out of Stock Net Amount'],
			$this->data['Order Out of Stock Tax Amount'],
			$this->data['Order No Authorized Net Amount'],
			$this->data['Order No Authorized Tax Amount'],
			$this->data['Order Not Found Net Amount'],
			$this->data['Order Not Found Tax Amount'],
			$this->data['Order Not Due Other Net Amount'],
			$this->data['Order Not Due Other Tax Amount'],

			$this->data['Order Invoiced Refund Net Amount'],
			$this->data['Order Invoiced Refund Tax Amount'],
			prepare_mysql($this->data['Order Invoiced Refund Notes']),
			$this->data['Order Invoiced Total Net Adjust Amount'],
			$this->data['Order Invoiced Total Tax Adjust Amount'],
			$this->data['Order Balance Net Amount'],
			$this->data['Order Balance Tax Amount'],
			$this->data['Order Balance Total Amount'],
			$this->data['Order Outstanding Balance Net Amount'],
			$this->data['Order Outstanding Balance Tax Amount'],
			$this->data['Order Outstanding Balance Total Amount'],
			$this->data['Order Profit Amount'],
			prepare_mysql($this->data['Order with Out of Stock']),

			$this->id
		);


		mysql_query($sql);
		//print $sql."\n";





	}

	function update_invoices($args='') {
		global $myconf;
		$sql=sprintf("select `Invoice Key` from `Order Transaction Fact` where `Order Key`=%d group by `Invoice Key`",$this->id);

		$res = mysql_query( $sql );
		$this->invoices=array();
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {
			if ($row['Invoice Key']) {
				$invoice=new Invoice($row['Invoice Key']);
				$this->invoices[$row['Invoice Key']]=$invoice;
			}

		}
		//update no normal fields
		$this->data ['Order XHTML Invoices'] ='';
		foreach ($this->invoices as $invoice) {
			$this->data ['Order XHTML Invoices'] .= sprintf( '<a href="invoice.php?id=%d">%s</a>, ',$invoice->data ['Invoice Key'], $invoice->data ['Invoice Public ID'] );

		}
		$this->data ['Order XHTML Invoices'] =_trim(preg_replace('/\, $/','',$this->data ['Order XHTML Invoices']));
		//$where_dns=preg_replace('/\,$/',')',$where_dns);

		if (!preg_match('/no save/i',$args)) {
			$sql=sprintf("update `Order Dimension`  set `Order XHTML Invoices`=%s where `Order Key`=%d"
				,prepare_mysql($this->data ['Order XHTML Invoices'])
				,$this->id
			);

			mysql_query($sql);

		}

	}


	function update_delivery_notes($args='') {


		$this->update_xhtml_delivery_notes();


	}




	function update_customer_history() {
		//print $this->data['Order Current Dispatch State']."\n";
		$customer=new Customer ($this->data['Order Customer Key']);
		switch ($this->data['Order Current Dispatch State']) {

		case ('Picking & Packing'):
		case('Ready to Pick'):
		case('Ready to Ship'):
		case('Dispatched'):
			$customer->update_history_order_in_warehouse($this);
			break;
		default:

			break;
		}



	}







	function update_dispatch_state($force=false) {


		//Line below has to be replaced, the calling functions have to decide instead, but to lazy now to do it
		if ( $this->data['Order Current Dispatch State']=='Dispatched'  and $this->data['Order Item Actions Taken']!='None') {
			$this->update_post_dispatch_state();
			return;
		}

		if (!$force) {
			if (  in_array($this->data['Order Current Dispatch State'],array('In Process by Customer','Submitted by Customer','Dispatched','Cancelled','Suspended')) )
				return;
		}

		$old_dispatch_state=$this->data['Order Current Dispatch State'];

		$xhtml_dispatch_state='';

		$dispatch_state='Unknown';

		//

		$sql=sprintf("select `Delivery Note XHTML State`,`Delivery Note State`,DN.`Delivery Note Key`,DN.`Delivery Note ID`,`Delivery Note Fraction Picked`,`Delivery Note Assigned Picker Alias`,`Delivery Note Fraction Packed`,`Delivery Note Assigned Packer Alias` from `Order Transaction Fact` B  left join `Delivery Note Dimension` DN  on (DN.`Delivery Note Key`=B.`Delivery Note Key`)
		where `Order Key`=%d group by B.`Delivery Note Key`  order by Field (`Delivery Note State`,  'Dispatched','Cancelled','Cancelled to Restock','Approved' ,'Packed Done' , 'Packed','Ready to be Picked','Picker Assigned','Packer Assigned','Picker & Packer Assigned','Picked','Picking' ,'Packing' ,'Picking & Packing') ",$this->id);

		$res = mysql_query( $sql );
		$delivery_notes=array();
		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {


			//print_r($row);
			if ($row['Delivery Note Key']) {
				if ($row['Delivery Note State']=='Ready to be Picked') {
					$dispatch_state='Ready to Pick';
				}elseif (in_array($row['Delivery Note State'],array('Picker & Packer Assigned','Picking & Packing','Packer Assigned','Ready to be Picked','Picker Assigned','Picking','Picked','Packing','Packed')) ) {
					$dispatch_state='Picking & Packing';

				}elseif ($row['Delivery Note State']=='Packed Done') {
					$dispatch_state='Packed Done';
				}elseif ($row['Delivery Note State']=='Approved') {
					$dispatch_state='Ready to Ship';
				}elseif ($row['Delivery Note State']=='Dispatched') {
					$dispatch_state='Dispatched';
				}else {
					$dispatch_state='Unknown';
				}

				$status=$row['Delivery Note XHTML State'];




				//$xhtml_dispatch_state.=sprintf('<a href="dn.php?id=%d">%s</a> %s',$row['Delivery Note Key'],$row['Delivery Note ID'],$status);
			}

		}
		$this->data['Order Current XHTML Dispatch State']=$xhtml_dispatch_state;

		//print $xhtml_dispatch_state;



		$sql=sprintf("update `Order Dimension` set `Order Current XHTML Dispatch State`=%s where `Order Key`=%d",
			prepare_mysql($xhtml_dispatch_state,false),
			$this->id
		);
		mysql_query($sql);


		$this->data['Order Current Dispatch State']=$dispatch_state;


		if ($old_dispatch_state!=$this->data['Order Current Dispatch State']) {

			$sql=sprintf("update `Order Dimension` set `Order Current Dispatch State`=%s where `Order Key`=%d"
				,prepare_mysql($this->data['Order Current Dispatch State'])

				,$this->id
			);

			mysql_query($sql);
			$this->update_customer_history();
			$this->update_full_search();
		}

	}


	function update_post_dispatch_state() {


		//print "update_post_dispatch_state\n";

		$old_dispatch_state=$this->data['Order Current Post Dispatch State'];

		$xhtml_dispatch_state='';

		$dispatch_state='NA';

		//

		$sql=sprintf("select `Delivery Note XHTML State`,`Delivery Note State`,DN.`Delivery Note Key`,DN.`Delivery Note ID`,`Delivery Note Fraction Picked`,`Delivery Note Assigned Picker Alias`,`Delivery Note Fraction Packed`,`Delivery Note Assigned Packer Alias` from `Order Post Transaction Dimension` B  left join `Delivery Note Dimension` DN  on (DN.`Delivery Note Key`=B.`Delivery Note Key`) where `Order Key`=%d group by B.`Delivery Note Key`  order by Field (`Delivery Note State`,  'Dispatched','Cancelled','Cancelled to Restock','Approved' ,'Packed Done' , 'Packed','Ready to be Picked','Picker Assigned','Packer Assigned','Picker & Packer Assigned','Picked','Picking' ,'Packing' ,'Picking & Packing') ",
			$this->id);

		$res = mysql_query( $sql );
		$delivery_notes=array();


		//print $sql;
		//exit;

		while ($row = mysql_fetch_array( $res, MYSQL_ASSOC )) {


			//print_r($row);
			if ($row['Delivery Note Key']) {
				if ($row['Delivery Note State']=='Ready to be Picked') {
					$dispatch_state='Ready to Pick';
				}elseif (in_array($row['Delivery Note State'],array('Picker & Packer Assigned','Picking & Packing','Packer Assigned','Ready to be Picked','Picker Assigned','Picking','Picked','Packing','Packed')) ) {
					$dispatch_state='Picking & Packing';

				}elseif ($row['Delivery Note State']=='Packed Done') {
					$dispatch_state='Packed Done';
				}elseif ($row['Delivery Note State']=='Approved') {
					$dispatch_state='Ready to Ship';
				}elseif ($row['Delivery Note State']=='Dispatched') {
					$dispatch_state='Dispatched';
				}else {
					$dispatch_state='Unknown';
				}

				$status=$row['Delivery Note XHTML State'];




				//$xhtml_dispatch_state.=sprintf('<a href="dn.php?id=%d">%s</a> %s',$row['Delivery Note Key'],$row['Delivery Note ID'],$status);
			}

		}
		//$this->data['Order Current XHTML Dispatch State']=$xhtml_dispatch_state;


		//print $dispatch_state;


		$sql=sprintf("update `Order Dimension` set `Order Current XHTML Post Dispatch State`=%s where `Order Key`=%d",
			prepare_mysql($xhtml_dispatch_state,false),
			$this->id
		);
		mysql_query($sql);



		$this->data['Order Current Post Dispatch State']=$dispatch_state;

		if ($old_dispatch_state!=$this->data['Order Current Dispatch State']) {

			$sql=sprintf("update `Order Dimension` set `Order Current Post Dispatch State`=%s  where `Order Key`=%d"
				,prepare_mysql($this->data['Order Current Post Dispatch State'])

				,$this->id
			);
			//print $sql;
			mysql_query($sql);
			//$this->update_customer_history();
			//$this->update_full_search();
		}



	}


	function set_order_as_dispatched($date) {

		// TODO dont set as dispatched until all the DN are dispatched (no inclide post transactions)

		$this->data['Order Current Dispatch State']='Dispatched';
		$this->data['Order Current XHTML Dispatch State']=_('Dispatched');

		$sql=sprintf("update `Order Dimension` set `Order Dispatched Date`=%s , `Order Current XHTML Dispatch State`=%s ,`Order Current Dispatch State`=%s where `Order Key`=%d"
			,prepare_mysql($date)
			,prepare_mysql($this->data['Order Current XHTML Dispatch State'])
			,prepare_mysql($this->data['Order Current Dispatch State'])
			,$this->id
		);
		mysql_query($sql);

		$this->update_customer_history();
		$this->update_full_search();

	}
	function set_order_as_completed($date) {

		// TODO dont set as dispatched until all the DN are dispatched (no inclide post transactions)

		$this->data['Order Current Dispatch State']='Dispatched';
		$this->data['Order Current XHTML Dispatch State']=_('Dispatched');

		$sql=sprintf("update `Order Dimension` set `Order Dispatched Date`=%s , `Order Current XHTML Dispatch State`=%s ,`Order Current Dispatch State`=%s where `Order Key`=%d"
			,prepare_mysql($date)
			,prepare_mysql($this->data['Order Current XHTML Dispatch State'])
			,prepare_mysql($this->data['Order Current Dispatch State'])
			,$this->id
		);
		mysql_query($sql);
		//print "$sql\n";
		$this->update_customer_history();
		$this->update_full_search();

	}











	function calculate_state($invoice_extra_info='') {

		$payment_state='';
		$dispatch_state='';
		switch ($this->data['Order Current Dispatch State']) {
		case 'In Process by Customer':
			$dispatch_state=_('In Process by Customer');
			break;
		case 'In Process by Customer':
			$dispatch_state=_('In Process by Customer');
			break;
		case 'In Process':
			$dispatch_state=_('In Process');
			break;
		case 'Submitted by Customer':
			$dispatch_state=_('Submitted by Customer');
			break;
		case 'Ready to Pick':
			$dispatch_state=_('Ready to Pick');
			break;
		case 'Picking & Packing':
			$dispatch_state=_('Picking & Packing');
			break;
		case 'Ready to Ship':
			$dispatch_state=_('Ready to Ship');
			break;
		case 'Dispatched':
			$dispatch_state=_('Dispatched');
			break;
		case 'Packing':
			$dispatch_state=_('Packing');
			break;
		case 'Packed':
			$dispatch_state=_('Packed');
			break;
		case 'Cancelled':
			$dispatch_state=_('Cancelled');
			break;
		case 'Suspended':
			$dispatch_state=_('Suspended');
			break;
		default:
			$dispatch_state=$this->data['Order Current Dispatch State'];
		}

		$state=$dispatch_state;

		/*
		if ($this->data['Order Invoiced']=='Yes') {
			$payment_state=_('Invoiced');
			if ($invoice_extra_info) {
				$payment_state.=' '.$invoice_extra_info;
			}
			switch ($this->data['Order Current Payment State']) {
			case 'Waiting Payment':
				$payment_state.=' ('._('Waiting Payment').')';
				break;
			case 'In Process by Customer':
				$payment_state.=' ('._('Partially Paid').')';
				break;

			default:

			}

		}


		if ($state!='' and $payment_state!='') {
			$state.=', '.$payment_state;

		}
*/
		return $state;
	}



	function update_item_totals_from_order_transactions() {
		if ($this->ghost_order or !$this->data ['Order Key'])
			return;
		$this->get_items_totals_by_adding_transactions();
		$sql = sprintf( "update `Order Dimension` set `Order Items Gross Amount`=%.2f, `Order Items Discount Amount`=%.2f, `Order Items Net Amount`=%.2f , `Order Items Tax Amount`=%.2f where  `Order Key`=%d "
			, $this->data ['Order Items Gross Amount']
			, $this->data ['Order Items Discount Amount']
			, $this->data ['Order Items Net Amount']
			, $this->data ['Order Items Tax Amount']
			, $this->data ['Order Key']
		);
		//print "$sql\n\n";
		mysql_query( $sql );



	}


	function update_totals_from_order_transactions() {
		if ($this->ghost_order or !$this->data ['Order Key'])
			return;

		$this->data['Order Shipping Net Amount']=0;
		$this->data['Order Shipping Tax Amount']=0;

		$sql=sprintf("select sum(`Transaction Net Amount`) as net , sum(`Transaction Tax Amount`) as tax from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`='Shipping' ",
			$this->id
		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			//print_r($row);
			$this->data['Order Shipping Net Amount']=($row['net']==''?0:$row['net']);
			$this->data['Order Shipping Tax Amount']=($row['tax']==''?0:$row['tax']);
		}

		$sql=sprintf("update `Order Dimension` set `Order Shipping Net Amount`=%.2f ,`Order Shipping Tax Amount`=%.2f where `Order Key`=%d"
			,$this->data['Order Shipping Net Amount']
			,$this->data['Order Shipping Tax Amount']
			,$this->id
		);
		mysql_query($sql);

		//print "$sql\n";

		$this->data['Order Charges Net Amount']=0;
		$this->data['Order Charges Tax Amount']=0;

		$sql=sprintf("select sum(`Transaction Net Amount`) as net , sum(`Transaction Tax Amount`) as tax from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`='Charges' ",
			$this->id
		);
		//print "$sql\n";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$this->data['Order Charges Net Amount']=($row['net']==''?0:$row['net']);
			$this->data['Order Charges Tax Amount']=($row['tax']==''?0:$row['tax']);
		}

		$sql=sprintf("update `Order Dimension` set `Order Charges Net Amount`=%.2f ,`Order Charges Tax Amount`=%.2f where `Order Key`=%d"
			,$this->data['Order Charges Net Amount']
			,$this->data['Order Charges Tax Amount']
			,$this->id
		);
		mysql_query($sql);



		$this->data['Order Insurance Net Amount']=0;
		$this->data['Order Insurance Tax Amount']=0;

		$sql=sprintf("select sum(`Transaction Net Amount`) as net , sum(`Transaction Tax Amount`) as tax from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`='Insurance' ",
			$this->id
		);
		//print "$sql\n";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$this->data['Order Insurance Net Amount']=($row['net']==''?0:$row['net']);
			$this->data['Order Insurance Tax Amount']=($row['tax']==''?0:$row['tax']);
		}

		$sql=sprintf("update `Order Dimension` set `Order Insurance Net Amount`=%.2f ,`Order Insurance Tax Amount`=%.2f where `Order Key`=%d"
			,$this->data['Order Insurance Net Amount']
			,$this->data['Order Insurance Tax Amount']
			,$this->id
		);
		mysql_query($sql);


		$this->data ['Order Total Tax Amount'] = $this->data ['Order Items Tax Amount'] + $this->data ['Order Shipping Tax Amount']+  $this->data ['Order Charges Tax Amount']+  $this->data ['Order Insurance Tax Amount'];
		$this->data ['Order Total Net Amount']=$this->data ['Order Items Net Amount']+  ($this->data ['Order Shipping Net Amount']==''?0:$this->data ['Order Shipping Net Amount'])+  $this->data ['Order Charges Net Amount']+  $this->data ['Order Insurance Net Amount'];

		$this->data ['Order Total Amount'] = $this->data ['Order Total Tax Amount'] + $this->data ['Order Total Net Amount'];

		$this->data ['Order Items Adjust Amount']=0;


		// print_r($this->data);

		$sql = sprintf( "update `Order Dimension` set
                         `Order Total Net Amount`=%.2f
                         ,`Order Total Tax Amount`=%.2f ,
                         `Order Total Amount`=%.2f
                         ,`Order Estimated Weight`=%f
                         ,`Order Dispatched Estimated Weight`=%f

                         where  `Order Key`=%d "
			, $this->data ['Order Total Net Amount']
			, $this->data ['Order Total Tax Amount']


			, $this->data ['Order Total Amount']
			, $this->data ['Order Estimated Weight']
			, $this->data ['Order Dispatched Estimated Weight']
			, $this->id
		);


		mysql_query( $sql );





	}




	function use_calculated_shipping() {

		$this->update_shipping_method('Calculated');
		$this->update_shipping();
		$this->updated=true;
		$this->update_item_totals_from_order_transactions();
		$this->get_items_totals_by_adding_transactions();
		$this->update_no_normal_totals('save');
		$this->update_totals_from_order_transactions();
		$this->apply_payment_from_customer_account();
		$this->new_value=$this->data['Order Shipping Net Amount'];

	}


	function use_calculated_items_charges() {

		$this->update_charges();
		$this->updated=true;
		$this->update_item_totals_from_order_transactions();
		$this->get_items_totals_by_adding_transactions();
		$this->update_no_normal_totals('save');
		$this->update_totals_from_order_transactions();
		$this->apply_payment_from_customer_account();
		$this->new_value=$this->data['Order Charges Net Amount'];

	}




	function update_shipping_amount($value) {
		$value=sprintf("%.2f",$value);

	//	if ($value!=$this->data['Order Shipping Net Amount'] or $this->data['Order Shipping Method']!='Set') {
			$this->update_shipping_method('Set');
			$this->data['Order Shipping Net Amount']=$value;
			$this->update_shipping();

			$this->updated=true;
			$this->new_value=$value;

			$this->update_item_totals_from_order_transactions();
			$this->get_items_totals_by_adding_transactions();
			$this->update_no_normal_totals('save');
			$this->update_totals_from_order_transactions();
			$this->apply_payment_from_customer_account();
	//	}

	}





	function update_charges_amount($charge_data) {



		if ($charge_data['Charge Net Amount']!=$this->data['Order Charges Net Amount']) {

			$this->data['Order Charges Net Amount']=$charge_data['Charge Net Amount'];

			$sql=sprintf('delete from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`="Charges" and `Delivery Note Key` IS NULL and `Invoice Key` IS NULL',
				$this->id
			);
			mysql_query($sql);
			// print "$sql\n";

			$total_charges_net=$charge_data['Charge Net Amount'];
			$total_charges_tax=$charge_data['Charge Tax Amount'];
			if ($charge_data['Charge Tax Amount']!=0 or $charge_data['Charge Net Amount']!=0) {
				$sql=sprintf("insert into `Order No Product Transaction Fact` (`Order Key`,`Order Date`,`Transaction Type`,`Transaction Type Key`,`Transaction Description`,`Transaction Gross Amount`,`Transaction Net Amount`,`Tax Category Code`,`Transaction Tax Amount`,
				`Currency Code`,`Currency Exchange`,`Metadata`)  values (%d,%s,%s,%d,%s,%.2f,%.2f,%s,%.2f,%s,%.2f,%s)  ",
					$this->id,
					prepare_mysql($this->data['Order Date']),
					prepare_mysql('Charges'),
					$charge_data['Charge Key'],
					prepare_mysql($charge_data['Charge Description']),
					$charge_data['Charge Net Amount'],
					$charge_data['Charge Net Amount'],
					prepare_mysql($this->data['Order Tax Code']),
					$charge_data['Charge Tax Amount'],

					prepare_mysql($this->data['Order Currency']),
					$this->data['Order Currency Exchange'],
					prepare_mysql($this->data['Order Original Metadata'])
				);

				//print ("$sql\n");
				mysql_query($sql);
			}




			$this->data['Order Charges Net Amount']=$total_charges_net;
			$this->data['Order Charges Tax Amount']=$total_charges_tax;


			$sql=sprintf("update `Order Dimension` set `Order Charges Net Amount`=%s ,`Order Charges Tax Amount`=%.2f where `Order Key`=%d"
				,$this->data['Order Charges Net Amount']
				,$this->data['Order Charges Tax Amount']
				,$this->id
			);
			mysql_query($sql);
			//print "*a $sql\n";

			// exit;

			$this->updated=true;
			$this->new_value=$this->data['Order Charges Net Amount'];

			$this->update_item_totals_from_order_transactions();
			$this->get_items_totals_by_adding_transactions();
			$this->update_no_normal_totals('save');
			$this->update_totals_from_order_transactions();
			$this->apply_payment_from_customer_account();



		}




	}







	// function get_ship_to_from_customer($customer_key){
	//  return $customer->get_ship_to($this->data['Order Date']);
	// }


	function set_data_from_customer($customer_key,$store_key=false) {


		$customer=new Customer($customer_key);
		if (!$store_key) {
			$store_key=$customer->data['Customer Store Key'];
		}



		$this->billing_address=new Address($customer->data['Customer Main Address Key']);
		$this->data ['Order Customer Key'] = $customer->id;
		$this->data ['Order Customer Name'] = $customer->data[ 'Customer Name' ];
		$this->data ['Order Customer Contact Name'] = $customer->data ['Customer Main Contact Name'];
		$this->data ['Order Tax Number'] = $customer->data ['Customer Tax Number'];
		$this->data ['Order Tax Number Valid'] = $customer->data ['Customer Tax Number Valid'];



		$this->data ['Order Customer Order Number']=$customer->get_number_of_orders()+1;

		$this->set_data_from_store($store_key);
	}

	function set_data_from_store($store_key) {
		$store=new Store($store_key);
		if (!$store->id) {
			$this->error=true;
			return;
		}

		$this->data ['Order Store Key'] = $store->id;
		$this->data ['Order Store Code'] = $store->data[ 'Store Code' ];
		$this->data ['Order XHTML Store'] = sprintf( '<a href="store.php?id=%d">%s</a>', $store->id, $store->data[ 'Store Code' ] );
		$this->data ['Order Currency']=$store->data[ 'Store Currency Code' ];

		$this->public_id_format=$store->data[ 'Store Order Public ID Format' ];
		//if (!isset($this->data ['Order Tax Code'])) {
		// $tax_category=new TaxCategory($store->data['Store Tax Category Code']);
		// $this->data ['Order Tax Rate'] = $tax_category->data['Tax Category Rate'];
		// $this->data ['Order Tax Code'] = $tax_category->data['Tax Category Code'];
		//}


		//$this->set_taxes($store->data['Store Tax Country Code']);


	}






	function next_public_id() {



		$sqla=sprintf("UPDATE `Store Dimension` SET `Store Order Last Order ID` = LAST_INSERT_ID(`Store Order Last Order ID` + 1) where `Store Key`=%d"
			,$this->data['Order Store Key']);
		mysql_query($sqla);




		$public_id=mysql_insert_id();


		$this->data['Order Public ID']=sprintf($this->public_id_format,$public_id);
		$this->data['Order File As']=$this->prepare_file_as($this->data['Order Public ID']);
	}

	function get_next_line_number() {

		$sql=sprintf("select count(*) as num_lines from `Order Transaction Fact` where `Order Key`=%d ",$this->id);
		$res=mysql_query($sql);

		$line_number=1;
		if ($row=mysql_fetch_array($res))
			$line_number+=$row['num_lines'];
		return $line_number;


	}






	function update_tax($tax_category_code=false) {



		if ($tax_category_code) {
			$tax_category=new TaxCategory('code',$value);
			if (!$tax_category->id) {
				$this->msg='Invalid tax code';
				$this->error=true;
				return;
			}else {

				$this->data['Order Tax Code']=$tax_category->data['Tax Category Code'];
				$this->data['Order Tax Rate']=$tax_category->data['Tax Category Rate'];
				$this->data['Order Tax Name']=$tax_category->data['Tax Category Name'];
				$this->data['Order Tax Operations']='';
				$this->data['Order Tax Selection Type']='set';

			}


		}else {

			$tax_data=$this->get_tax_data();

			$this->data['Order Tax Code']=$tax_data['code'];
			$this->data['Order Tax Rate']=$tax_data['rate'];
			$this->data['Order Tax Name']=$tax_data['name'];
			$this->data['Order Tax Operations']=$tax_data['operations'];
			$this->data['Order Tax Selection Type']=$tax_data['state'];

		}


		$sql=sprintf("update `Order Transaction Fact` set `Transaction Tax Rate`=%f,`Transaction Tax Code`=%s where `Order Key`=%d and `Consolidated`='No'",
			$this->data['Order Tax Rate'],
			prepare_mysql($this->data['Order Tax Code']),
			$this->id

		);
		mysql_query($sql);

		$sql=sprintf("select `Order No Product Transaction Fact Key`,`Transaction Net Amount` from `Order No Product Transaction Fact`  where `Order Key`=%d and `Consolidated`='No'",
			$this->id
		);


		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$sql=sprintf("update `Order No Product Transaction Fact` set `Transaction Tax Amount`=%f,`Tax Category Code`=%s where `Order No Product Transaction Fact Key`=%d",
				$row['Transaction Net Amount']*$this->data['Order Tax Rate'],
				prepare_mysql($this->data['Order Tax Code']),
				$row['Order No Product Transaction Fact Key']
			);
			//   print $sql;
			mysql_query($sql);
		}


		$sql=sprintf("update `Order Dimension` set `Order Tax Code`=%s ,`Order Tax Rate`=%f,`Order Tax Name`=%s,`Order Tax Operations`=%s,`Order Tax Selection Type`=%s where `Order Key`=%d",

			prepare_mysql($this->data['Order Tax Code']),
			$this->data['Order Tax Rate'],
			prepare_mysql($this->data['Order Tax Name']),
			prepare_mysql($this->data['Order Tax Operations']),
			prepare_mysql($this->data['Order Tax Selection Type']),
			$this->id

		);
		//print $sql;

		mysql_query($sql);

		$this->update_no_normal_totals('save');
		$this->update_item_totals_from_order_transactions() ;
		$this->update_totals_from_order_transactions();
		$this->apply_payment_from_customer_account();

	}






	function update_shipping($dn_key=false,$order_picked=true) {

		if (!$dn_key)$dn_key='';


		if ($dn_key and $order_picked) {
			list($shipping,$shipping_key,$shipping_method)=$this->get_shipping($dn_key);
		} else {
			list($shipping,$shipping_key,$shipping_method)=$this->get_shipping();
		}


//print "$shipping,$shipping_key,$shipping_method";
		if (!is_numeric($shipping)) {

			$this->data['Order Shipping Net Amount']=0;
			$this->data['Order Shipping Tax Amount']=0;
		} else {

			$this->data['Order Shipping Net Amount']=$shipping;
			$this->data['Order Shipping Tax Amount']=$shipping*$this->data['Order Tax Rate'];
		}



		$this->update_shipping_method($shipping_method);


		if (!$dn_key) {

			$sql=sprintf('delete from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`="Shipping" ',
				$this->id
			);
		} else {
			$sql=sprintf('delete from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`="Shipping"  and `Delivery Note Key`=%d and `Invoice Key` IS NULL',
				$this->id,
				$dn_key
			);


		}
		
	
		
		
		//print $sql;
		mysql_query($sql);



		if (!($this->data['Order Shipping Net Amount']==0 and $this->data['Order Shipping Tax Amount']==0)) {
			$sql=sprintf("insert into `Order No Product Transaction Fact` (`Order Key`,`Order Date`,`Transaction Type`,`Transaction Type Key`,`Transaction Description`,
			`Transaction Gross Amount`,`Transaction Net Amount`,`Tax Category Code`,`Transaction Tax Amount`,
			`Currency Code`,`Currency Exchange`,`Metadata`,`Delivery Note Key`)  values (%d,%s,%s,%d,%s,%.2f,%.2f,%s,%.2f,%s,%.2f,%s,%s)  ",
				$this->id,
				prepare_mysql($this->data['Order Date']),
				prepare_mysql('Shipping'),
				$shipping_key,
				prepare_mysql(_('Shipping')),
				$this->data['Order Shipping Net Amount'],
				$this->data['Order Shipping Net Amount'],
				prepare_mysql($this->data['Order Tax Code']),
				$this->data['Order Shipping Tax Amount'],


				prepare_mysql($this->data['Order Currency']),
				$this->data['Order Currency Exchange'],
				prepare_mysql($this->data['Order Original Metadata']),
				prepare_mysql($dn_key)

			);

			//print ("$sql\n");
			mysql_query($sql);
		}



		$this->update_no_normal_totals('save');

		$this->update_totals_from_order_transactions();

		$this->apply_payment_from_customer_account();

	}



	function update_charges($dn_key=false,$order_picked=true) {

		if (!$dn_key) {
			$dn_key='';
			$sql=sprintf('delete from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`="Charges" and `Delivery Note Key` IS NULL and `Invoice Key` IS NULL',
				$this->id
			);
		} else {
			$sql=sprintf('delete from `Order No Product Transaction Fact` where `Order Key`=%d and `Transaction Type`="Charges" and `Delivery Note Key`=%d and `Invoice Key` IS NULL',
				$this->id,
				$dn_key
			);


		}
		//print $sql;
		mysql_query($sql);


		if ($dn_key and $order_picked)
			$charges_array=$this->get_charges($dn_key);
		else
			$charges_array=$this->get_charges();


		$total_charges_net=0;
		$total_charges_tax=0;
		foreach ($charges_array as $charge_data) {
			$total_charges_net+=$charge_data['Charge Net Amount'];
			$total_charges_tax+=$charge_data['Charge Tax Amount'];

			if (!($charge_data['Charge Net Amount']==0 and $charge_data['Charge Tax Amount']==0)) {
				$sql=sprintf("insert into `Order No Product Transaction Fact` (`Order Key`,`Order Date`,`Transaction Type`,`Transaction Type Key`,`Transaction Description`,`Transaction Gross Amount`,`Transaction Net Amount`,`Tax Category Code`,`Transaction Tax Amount`,`Currency Code`,`Currency Exchange`,`Metadata`,`Delivery Note Key`)

				values (%d,%s,%s,%d,%s,%.2f,%.2f,%s,%.2f,%s,%.2f,%s,%s)  ",
					$this->id,
					prepare_mysql($this->data['Order Date']),
					prepare_mysql('Charges'),
					$charge_data['Charge Key'],
					prepare_mysql($charge_data['Charge Description']),
					$charge_data['Charge Net Amount'],
					$charge_data['Charge Net Amount'],
					prepare_mysql($this->data['Order Tax Code']),
					$charge_data['Charge Tax Amount'],

					prepare_mysql($this->data['Order Currency']),
					$this->data['Order Currency Exchange'],
					prepare_mysql($this->data['Order Original Metadata']),
					prepare_mysql($dn_key)

				);

				mysql_query($sql);



			}

		}



		$this->data['Order Charges Net Amount']=$total_charges_net;
		$this->data['Order Charges Tax Amount']=$total_charges_tax;


		$sql=sprintf("update `Order Dimension` set `Order Charges Net Amount`=%s ,`Order Charges Tax Amount`=%.2f where `Order Key`=%d"
			,$this->data['Order Charges Net Amount']
			,$this->data['Order Charges Tax Amount']
			,$this->id
		);
		mysql_query($sql);
		// print "* $sql\n";


	}


	function update_insurance($dn_key=false) {
		$valid_insurances=$this->get_insurances($dn_key);

		$sql=sprintf("select `Transaction Type Key`,`Order No Product Transaction Fact Key`  from `Order No Product Transaction Fact` where `Order Key`=%d  and `Transaction Type`='Insurance' ",
			$this->id

		);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {

			if (!array_key_exists($row['Transaction Type Key'])) {

				$sql=sprintf("delete from `Order No Product Transaction Fact` where `Order No Product Transaction Fact Key`=%d ",
					$row['Order No Product Transaction Fact Key']
				);
				mysql_query($sql);
			}

		}
		$this->update_no_normal_totals('save');
		$this->update_totals_from_order_transactions();
		$this->apply_payment_from_customer_account();

	}

	function remove_insurance($onptf_key) {

		$sql=sprintf("delete from `Order No Product Transaction Fact` where `Order No Product Transaction Fact Key`=%d and `Order Key`=%d",
			$onptf_key,
			$this->id
		);
		mysql_query($sql);

		$this->update_no_normal_totals('save');
		$this->update_totals_from_order_transactions();
		$this->apply_payment_from_customer_account();
	}


	function add_insurance($insurance_key,$dn_key=false) {

		$valid_insurances=$this->get_insurances($dn_key);

		if (array_key_exists($insurance_key,$valid_insurances)) {

			if (!$valid_insurances[$insurance_key]['Order No Product Transaction Fact Key']) {




				$sql=sprintf("insert into `Order No Product Transaction Fact` (`Order Key`,`Order Date`,`Transaction Type`,`Transaction Type Key`,`Transaction Description`
				,`Transaction Gross Amount`,`Transaction Net Amount`,`Tax Category Code`,`Transaction Tax Amount`,`Currency Code`,`Currency Exchange`,`Metadata`,`Delivery Note Key`)
				values (%d,%s,%s,%d,%s,%.2f,%.2f,%s,%.2f,%s,%.2f,%s,%s)  ",
					$this->id,
					prepare_mysql(gmdate("Y-m-d H:i:s")),
					prepare_mysql('Insurance'),
					$insurance_key,
					prepare_mysql($valid_insurances[$insurance_key]['Insurance Description']),
					$valid_insurances[$insurance_key]['Insurance Net Amount'],
					$valid_insurances[$insurance_key]['Insurance Net Amount'],
					prepare_mysql($valid_insurances[$insurance_key]['Insurance Tax Code']),
					$valid_insurances[$insurance_key]['Insurance Tax Amount'],

					prepare_mysql($this->data['Order Currency']),
					$this->data['Order Currency Exchange'],
					prepare_mysql($this->data['Order Original Metadata']),
					prepare_mysql($dn_key)

				);
				mysql_query($sql);

				$onptf_key=mysql_insert_id();

				$this->update_no_normal_totals('save');
				$this->update_totals_from_order_transactions();

				$this->apply_payment_from_customer_account();
			}else {
				$onptf_key=$valid_insurances[$insurance_key]['Order No Product Transaction Fact Key'];
			}

		}else {
			$onptf_key=0;
		}

		return $onptf_key;
	}


	function get_insurances($dn_key=false) {
		$insurances=array();
		if ($this->data['Order Number Items']==0) {

			return $insurances;
		}


		$sql=sprintf("select * from `Insurance Dimension` where `Insurance Trigger`='Order' and (`Insurance Trigger Key`=%d  or `Insurance Trigger Key` is null) and `Insurance Store Key`=%d"
			,$this->id
			,$this->data['Order Store Key']
		);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {



			$apply_insurance=false;

			$order_amount=$this->data[$row['Insurance Terms Type']];



			if ($dn_key) {
				switch ($row['Insurance Terms Type']) {

				case 'Order Items Net Amount':

					$sql=sprintf("select sum(`Order Transaction Net Amount`*(`Delivery Note Quantity`/`Order Quantity`)) as amount from `Order Transaction Fact` where `Order Key`=%d and `Delivery Note Key`=%d and `Order Quantity`!=0",
						$this->id,
						$dn_key
					);
					$res=mysql_query($sql);
					if ($row2=mysql_fetch_assoc($res)) {
						$order_amount=$row2['amount'];
					} else {
						$order_amount=0;
					}
					break;



				case 'Order Items Gross Amount':
				default:
					$sql=sprintf("select sum(`Order Transaction Gross Amount`*(`Delivery Note Quantity`/`Order Quantity`)) as amount from `Order Transaction Fact` where `Order Key`=%d and `Delivery Note Key`=%d and `Order Quantity`!=0",
						$this->id,
						$dn_key
					);
					$res=mysql_query($sql);
					if ($row2=mysql_fetch_assoc($res)) {
						$order_amount=$row2['amount'];
					} else {
						$order_amount=0;
					}
					break;
				}
			}







			$terms_components=preg_split('/;/',$row['Insurance Terms Metadata']);
			$operator=$terms_components[0];
			$amount=$terms_components[1];

			//print_r($order_amount);


			switch ($operator) {
			case('<'):
				if ($order_amount<$amount)
					$apply_insurance=true;
				break;
			case('>'):
				if ($order_amount>$amount)
					$apply_insurance=true;
				break;
			case('<='):
				if ($order_amount<=$amount)
					$apply_insurance=true;
				break;
			case('>='):
				if ($order_amount>=$amount)
					$apply_insurance=true;
				break;
			}


			if ($row['Insurance Tax Category Code']=='') {
				$tax_category_code=$this->data['Order Tax Code'];
				$tax_rate=$this->data['Order Tax Rate'];
			}else {
				$tax_category=new TaxCategory($row['Insurance Tax Category Code']);
				$tax_category_code=$tax_category->data['Tax Category Code'];
				$tax_rate=$tax_category->data['Tax Category Rate'];

			}



			if ($row['Insurance Type']=='Amount') {
				$charge_net_amount=$row['Insurance Metadata'];





				$charge_tax_amount=$row['Insurance Metadata']*$tax_rate;
			}else {

				exit("still to do");
			}


			$sql=sprintf("select `Order No Product Transaction Fact Key`  from `Order No Product Transaction Fact` where `Order Key`=%d  and `Transaction Type`='Insurance' and `Transaction Type Key`=%d ",
				$this->id,
				$row['Insurance Key']
			);
			$res2=mysql_query($sql);
			if ($row2=mysql_fetch_assoc($res2)) {
				$onptf_key=$row2['Order No Product Transaction Fact Key'];
			}else {
				$onptf_key=0;
			}

			if ($apply_insurance)
				$insurances[$row['Insurance Key']]=array(
					'Insurance Net Amount'=>$charge_net_amount,
					'Insurance Tax Amount'=>$charge_tax_amount,
					'Insurance Formated Net Amount'=>money($this->exchange*$charge_net_amount,$this->currency_code),
					'Insurance Formated Tax Amount'=>money($this->exchange*$charge_tax_amount,$this->currency_code),
					'Insurance Tax Code'=>$tax_category_code,
					'Insurance Key'=>$row['Insurance Key'],
					'Insurance Description'=>$row['Insurance Name'],
					'Order No Product Transaction Fact Key'=>$onptf_key
				);



		}
		return $insurances;

	}




	function get_charges($dn_key=false) {
		$charges=array();;
		if ($this->data['Order Number Items']==0) {

			return $charges;
		}


		$sql=sprintf("select * from `Charge Dimension` where `Charge Trigger`='Order' and (`Charge Trigger Key`=%d  or `Charge Trigger Key` is null) and `Store Key`=%d"
			,$this->id
			,$this->data['Order Store Key']
		);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {



			$apply_charge=false;

			$order_amount=$this->data[$row['Charge Terms Type']];



			if ($dn_key) {
				switch ($row['Charge Terms Type']) {

				case 'Order Items Net Amount':

					$sql=sprintf("select sum(`Order Transaction Net Amount`*(`Delivery Note Quantity`/`Order Quantity`)) as amount from `Order Transaction Fact` where `Order Key`=%d and `Delivery Note Key`=%d and `Order Quantity`!=0",
						$this->id,
						$dn_key
					);
					$res=mysql_query($sql);
					if ($row2=mysql_fetch_assoc($res)) {
						$order_amount=$row2['amount'];
					} else {
						$order_amount=0;
					}
					break;



				case 'Order Items Gross Amount':
				default:
					$sql=sprintf("select sum(`Order Transaction Gross Amount`*(`Delivery Note Quantity`/`Order Quantity`)) as amount from `Order Transaction Fact` where `Order Key`=%d and `Delivery Note Key`=%d and `Order Quantity`!=0",
						$this->id,
						$dn_key
					);
					$res=mysql_query($sql);
					if ($row2=mysql_fetch_assoc($res)) {
						$order_amount=$row2['amount'];
					} else {
						$order_amount=0;
					}
					break;
				}
			}







			$terms_components=preg_split('/;/',$row['Charge Terms Metadata']);
			$operator=$terms_components[0];
			$amount=$terms_components[1];

			//print_r($order_amount);


			switch ($operator) {
			case('<'):
				if ($order_amount<$amount)
					$apply_charge=true;
				break;
			case('>'):
				if ($order_amount>$amount)
					$apply_charge=true;
				break;
			case('<='):
				if ($order_amount<=$amount)
					$apply_charge=true;
				break;
			case('>='):
				if ($order_amount>=$amount)
					$apply_charge=true;
				break;
			}




			if ($row['Charge Type']=='Amount') {
				$charge_net_amount=$row['Charge Metadata'];
				$charge_tax_amount=$row['Charge Metadata']*$this->data['Order Tax Rate'];
			}else {

				exit("still to do");
			}


			if ($apply_charge)
				$charges[]=array(
					'Charge Net Amount'=>$charge_net_amount,
					'Charge Tax Amount'=>$charge_tax_amount,
					'Charge Key'=>$row['Charge Key'],
					'Charge Description'=>$row['Charge Name']
				);



		}
		return $charges;

	}

	function get_shipping($dn_key=false) {


		if ($this->data['Order Number Items']==0) {
			return array(0,0,'No Applicable');
		}


		if ($this->data['Order For Collection']=='Yes')
			return array(0,0,'No Applicable');

		if ($this->data['Order Shipping Method']=='Set') {
		
		//print $this->data['Order Shipping Net Amount'].'xx';
			return array(($this->data['Order Shipping Net Amount']==''?0:$this->data['Order Shipping Net Amount']),0,'Set');
		}




		if (in_array($this->data['Order Ship To Country Code'],array('GBR','JEY','GGY','IMN'))) {
			include_once 'common_geography_functions.php';

			$postcode = gbr_postcode_first_part($this->data['Order Ship To Postal Code']);
		}else {
			$postcode =$this->data['Order Ship To Postal Code'];
		}






		$sql=sprintf("select `Shipping Destination Metadata`,`Shipping Key`,`Shipping Metadata`,`Shipping Price Method`  from `Shipping Dimension`  where (select %s like `Shipping Destination Metadata` ) and  `Shipping Destination Type`='Country' and `Shipping Destination Code`=%s  and `Shipping Secondary Destination Check`='Post Code' and `Store Key`=%d "
			,prepare_mysql($postcode)
			,prepare_mysql($this->data['Order Ship To Country Code'])
			,$this->data['Order Store Key']

		);



		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res)) {



			list($shipping,$method)=$this->get_shipping_from_method($row['Shipping Price Method'],$row['Shipping Metadata'],$dn_key);
			return array($shipping,$row['Shipping Key'],$method);
		}


		$sql=sprintf("select `Shipping Key`,`Shipping Metadata`,`Shipping Price Method` from `Shipping Dimension` where  `Shipping Destination Type`='Country' and `Shipping Destination Code`=%s  and   `Shipping Secondary Destination Check`='None'  and `Store Key`=%d  "
			,prepare_mysql($this->data['Order Ship To Country Code'])
			,$this->data['Order Store Key']
		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res)) {
			list($shipping,$method)=$this->get_shipping_from_method($row['Shipping Price Method'],$row['Shipping Metadata'],$dn_key);
			return array($shipping,$row['Shipping Key'],$method);
		}


		return array(0,0,'TBC');


	}




	function get_shipping_from_method($type,$metadata,$dn_key=false) {
		switch ($type) {

		case('Step Order Items Net Amount'):
			return $this->get_shipping_Step_Order_Items_Net_Amount($metadata,$dn_key);
			break;

		case('Step Order Items Gross Amount'):
			return $this->get_shipping_Step_Order_Items_Gross_Amount($metadata,$dn_key);
			break;
		case('On Request'):
			return array(0,'TBC');
			break;

		}

	}


	function get_shipping_Step_Order_Items_Net_Amount($metadata,$dn_key=false) {

		if ($dn_key) {
			$sql=sprintf("select sum( `Order Transaction Amount`*(`Delivery Note Quantity`/`Order Quantity`)  ) as amount from `Order Transaction Fact` where `Order Key`=%d and `Delivery Note Key`=%d and `Order Quantity`!=0",
				$this->id,
				$dn_key
			);
			//print $sql;
			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {
				$amount=$row['amount'];
			} else {
				$amount=0;
			}
		} else {
			$amount=$this->data['Order Items Net Amount'];
		}

		if ($amount==0) {

			return array(0,'Calculated');

		}
		$data=preg_split('/\;/',$metadata);

		foreach ($data as $item) {

			list($min,$max,$value)=preg_split('/\,/',$item);
			//print "$min,$max,$value\n";
			if ($min=='') {
				if ($amount<$max)
					return array($value,'Calculated');
			}
			elseif ($max=='') {
				if ($amount>=$min)
					return array($value,'Calculated');
			}
			elseif ($amount<$max and $amount>=$min) {
				return array($value,'Calculated');

			}


		}
		return array(0,'TBC');

	}


	function update_transaction_discount_percentage($otf_key,$percentage) {
		$sql=sprintf('select `Product Key`,`Order Transaction Fact Key`,`Order Transaction Gross Amount`,`Order Transaction Total Discount Amount` from  `Order Transaction Fact`  where `Order Transaction Fact Key`=%d ',
			$otf_key
		);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res)) {
			$amount=$row['Order Transaction Gross Amount']*$percentage/100;
			return $this->update_transaction_discount_amount($otf_key,$amount);
		}else {
			$this->error=true;
			$this->msg='otf not found';
		}

	}

	function update_transaction_discount_amount($otf_key,$amount,$deal_campaign_key=0,$deal_key=0,$deal_component_key=0) {



		if (!$deal_key) {
			$deal_info='';
		}

		$sql=sprintf('select OTF.`Product Family Key`,OTF.`Product ID`,`Product XHTML Short Description`,`Order Quantity`,`Product Key`,`Order Transaction Fact Key`,`Order Transaction Gross Amount`,`Order Transaction Total Discount Amount` from  `Order Transaction Fact` OTF left join `Product Dimension` P on  (P.`Product ID`=OTF.`Product ID`) where `Order Transaction Fact Key`=%d ',
			$otf_key
		);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {



			if ($amount==$row['Order Transaction Total Discount Amount'] or $row['Order Transaction Gross Amount']==0) {
				$this->msg='Nothing to Change';
				$return_data= array(
					'updated'=>true,
					'otf_key'=>$otf_key,
					'description'=>$row['Product XHTML Short Description'].' <span class="deal_info">'.$deal_info.'</span>',
					'discount_percentage'=>percentage($amount,$row['Order Transaction Gross Amount'],$fixed=1,$error_txt='NA',$psign=''),
					'to_charge'=>money($row['Order Transaction Gross Amount']-
						$amount,$this->data['Order Currency']),
					'qty'=>$row['Order Quantity'],
					'bonus qty'=>0
				);
				//print_r($return_data);
				return $return_data;
			}
			$sql=sprintf("delete from `Order Transaction Deal Bridge` where `Order Transaction Fact Key` =%d",$otf_key);
			mysql_query($sql);

			$this->data['Order Transaction Total Discount Amount']=$amount;
			$sql=sprintf('update `Order Transaction Fact` OTF set  `Order Transaction Total Discount Amount`=%f where `Order Transaction Fact Key`=%d ',
				$amount,
				$otf_key
			);
			mysql_query($sql);
			//print "$sql\n";
			$this->update_item_totals_from_order_transactions();
			$this->update_no_normal_totals('save');

			$this->update_totals_from_order_transactions();
			$this->apply_payment_from_customer_account();
			$deal_info='';
			if ($amount>0  ) {
				$deal_info=percentage($amount,$row['Order Transaction Gross Amount']).' Off';



				$sql=sprintf("insert into `Order Transaction Deal Bridge` (`Order Transaction Fact Key`,`Order Key`,`Product Key`,`Product ID`,`Product Family Key`,`Deal Campaign Key`,`Deal Key`,`Deal Component Key`,`Deal Info`,`Amount Discount`,`Fraction Discount`,`Bunus Quantity`) values (%d,%d,%d,%d,%d,%d,%d,%d,%s,%f,%f,0)",
					$row['Order Transaction Fact Key'],
					$this->id,
					$row['Product Key'],
					$row['Product ID'],
					$row['Product Family Key'],
					$deal_campaign_key,
					$deal_key,
					$deal_component_key,
					prepare_mysql($deal_info,false),
					$amount,
					($amount/$row['Order Transaction Gross Amount'])
				);
				mysql_query($sql);
				$this->updated=true;
			}
			return array(
				'updated'=>true,
				'otf_key'=>$otf_key,
				'description'=>$row['Product XHTML Short Description'].' <span class="deal_info">'.$deal_info.'</span>',
				'discount_percentage'=>percentage($amount,$row['Order Transaction Gross Amount'],$fixed=1,$error_txt='NA',$psign=''),
				'to_charge'=>money($row['Order Transaction Gross Amount']-$amount,$this->data['Order Currency']),
				'qty'=>$row['Order Quantity'],
				'bonus qty'=>0
			);





		}
		else {
			$this->error=true;
			$this->msg='otf not found';
		}


	}

	function update_order_discounts() {
		$sql=sprintf("select D.`Deal Key`,`Deal Description` from `Order Deal Bridge` B left join `Deal Dimension` D on (B.`Deal Key`=D.`Deal Key`) where `Order Key`=%d",
			$this->id);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_array($res)) {

			//  $deal=new Deal($row['Deal Key']);


			$sql=sprintf("select * from `Deal Component Dimension` where `Deal Component Allowance Target` in ('Order','Shipping','Charge') and `Deal Key`=%d",
				$row['Deal Key']);
			$res2=mysql_query($sql);
			while ($row2=mysql_fetch_array($res2)) {
				switch ($row2['Deal Component Allowance Target']) {
				case 'Order':
					switch ($row2['Deal Component Allowance Type']) {
					case 'Credit':

						list($net,$tax_code)=preg_split('/;/',$row2['Deal Component Allowance']);
						$_tax_category=new TaxCategory('code',$tax_code);
						$tax=$_tax_category->data['Tax Category Rate']*$net;
						$sql=sprintf("insert into `Order No Product Transaction Fact` (`Order Key`,`Order Date`,`Transaction Type`,`Transaction Type Key`,`Transaction Description`,`Transaction Gross Amount`,`Transaction Net Amount`,`Tax Category Code`,`Transaction Tax Amount`,`Currency Code`,`Currency Exchange`,`Metadata`)  values (%d,%s,%s,%d,%s,%.2f,%.2f,%s,%.2f,%s,%.2f,%s)  ",
							$this->id,
							prepare_mysql($this->data['Order Date']),
							prepare_mysql('Deal'),
							$row2['Deal Component Key'],
							prepare_mysql($row['Deal Description']),
							$net,
							$net,
							prepare_mysql($tax_code),
							$tax,

							prepare_mysql($this->data['Order Currency']),
							$this->data['Order Currency Exchange'],
							prepare_mysql($this->data['Order Original Metadata'])
						);

						// print ("$sql\n");
						mysql_query($sql);


						$sql=sprintf("update `Order Deal Bridge` set `Used`='Yes' where `Deal Key`=%d ",$row['Deal Key']);
						mysql_query($sql);
						break;
					default:

						break;
					}


					break;
				default:

					break;
				}

			}





		}

	}



	function get_allowances_from_deal_component_data($deal_component_data) {

		//print_r($deal_component_data);

		$deal_info='';
		if ($deal_component_data['Deal Component Name']!='') {
			$deal_info=$deal_component_data['Deal Component Name'].', ';
		}
		$deal_info.=_trim($deal_component_data['Deal Component Terms Description'].' '.$deal_component_data['Deal Component Allowance Description']);



		switch ($deal_component_data['Deal Component Allowance Type']) {
		case('Percentage Off'):
			switch ($deal_component_data['Deal Component Allowance Target']) {
			case('Family'):

				$family_key=$deal_component_data['Deal Component Allowance Target Key'];
				$percentage=$deal_component_data['Deal Component Allowance'];
				if (isset($this->allowance['Family Percentage Off'][$family_key])) {
					if ($this->allowance['Family Percentage Off'][$family_key]['Percentage Off']<$percentage)
						$this->allowance['Family Percentage Off'][$family_key]['Percentage Off']=$percentage;
				} else {




					$this->allowance['Family Percentage Off'][$family_key]=array(
						'Family Key'=>$family_key,
						'Percentage Off'=>$percentage,
						'Deal Campaign Key'=>$deal_component_data['Deal Component Campaign Key'],
						'Deal Component Key'=>$deal_component_data['Deal Component Key'],
						'Deal Key'=>$deal_component_data['Deal Component Deal Key'],
						'Deal Info'=>$deal_info
					);
				}

				break;
			}


			break;
		case('Get Free'):
			switch ($deal_component_data['Deal Component Allowance Target']) {

			case('Charge'):
			case('Shipping'):



				$this->allowance['No Item Transaction'][$deal_component_data['Deal Component Allowance Target']]=array(
					'Percentage Off'=>1,
					'Deal Campaign Key'=>$deal_component_data['Deal Component Campaign Key'],
					'Deal Component Key'=>$deal_component_data['Deal Component Key'],
					'Deal Key'=>$deal_component_data['Deal Component Deal Key'],
					'Deal Info'=>$deal_info
				);




				break;
			case('Product'):
				$product_id=$deal_component_data['Deal Component Allowance Target Key'];
				$get_free_allowance=$deal_component_data['Deal Component Allowance'];
				if (isset($this->allowance['Get Free'][$product_id])) {
					$this->allowance['Get Free'][$product_id]['Get Free']+=$get_free_allowance;
				} else {
					$this->allowance['Get Free'][$product_id]=array(
						'Product ID'=>$product_id,
						'Get Free'=>$get_free_allowance,
						'Deal Campaign Key'=>$deal_component_data['Deal Component Campaign Key'],
						'Deal Component Key'=>$deal_component_data['Deal Component Key'],
						'Deal Key'=>$deal_component_data['Deal Component Deal Key'],
						'Deal Info'=>$deal_info
					);
				}


				break;
			}

			break;


		}

	}

	function update_discounts_items() {


		$this->allowance=array('Family Percentage Off'=>array(),'Get Free'=>array(),'Get Same Free'=>array(),'Credit'=>array(),'No Item Transaction'=>array());
		$this->deals=array('Family'=>array('Deal'=>false,'Terms'=>false,'Deal Multiplicity'=>0,'Terms Multiplicity'=>0));

		$sql=sprintf('update `Order Transaction Fact`  set  `Order Transaction Total Discount Amount`=0 , `Order Transaction Amount`=`Order Transaction Gross Amount` where `Order Key`=%d  '
			,$this->id
		);
		mysql_query($sql);
		$sql=sprintf("delete from `Order Transaction Deal Bridge` where `Order Key` =%d and `Deal Component Key`!=0  ",$this->id);
		mysql_query($sql);








		$this->update_discounts_family_tigger();


		$this->apply_items_discounts() ;

	}


	function update_discounts_no_items() {


		$this->allowance=array('Family Percentage Off'=>array(),'Get Free'=>array(),'Get Same Free'=>array(),'Credit'=>array(),'No Item Transaction'=>array());
		$this->deals=array('Family'=>array('Deal'=>false,'Terms'=>false,'Deal Multiplicity'=>0,'Terms Multiplicity'=>0));

		$sql=sprintf('update `Order No Product Transaction Fact`  set  `Transaction Total Discount Amount`=0 , `Transaction Net Amount`=`Transaction Gross Amount` where `Order Key`=%d  '
			,$this->id
		);
		mysql_query($sql);

		$sql=sprintf("delete from `Order No Product Transaction Deal Bridge` where `Order Key` =%d and `Deal Component Key`!=0  ",$this->id);
		mysql_query($sql);





		$this->update_discounts_order_tigger_no_item_allowances();




		$this->apply_no_items_discounts() ;

	}

	function apply_items_discounts() {



		$sql=sprintf("select * from `Order Transaction Deal Bridge` where `Order Key`=%d  ",$this->id);
		$res=mysql_query($sql);
		//print $sql;
		while ($row=mysql_fetch_assoc($res)) {
			if ( $row['Fraction Discount']>0  ) {
				$sql=sprintf('update `Order Transaction Fact` OTF  set  `Order Transaction Total Discount Amount`=`Order Transaction Gross Amount`*%f where `Order Transaction Fact Key`=%d '
					,$row['Fraction Discount']
					,$row['Order Transaction Fact Key']
				);
				//print $sql;
				mysql_query($sql);

				$sql=sprintf('update `Order Transaction Fact` OTF  set  `Order Transaction Amount`=`Order Transaction Gross Amount`-`Order Transaction Total Discount Amount` where `Order Transaction Fact Key`=%d '
					,$row['Order Transaction Fact Key']
				);
				//print $sql;
				mysql_query($sql);

			}

			if ( $row['Bunus Quantity']>0  ) {
				$sql=sprintf('update `Order Transaction Fact` OTF  set  `Order Bonus Quantity`=%f where `Order Transaction Fact Key`=%d '
					,$row['Bunus Quantity']
					,$row['Order Transaction Fact Key']
				);
				//print $sql;
				mysql_query($sql);
			}


		}





	}


	function apply_no_items_discounts() {




		$sql=sprintf("select * from `Order No Product Transaction Deal Bridge` B left join `Order No Product Transaction Fact`OTF on (OTF.`Order No Product Transaction Fact Key`=B.`Order No Product Transaction Fact Key`)  where B.`Order Key`=%d  ",$this->id);
		$res=mysql_query($sql);
		//print $sql;
		while ($row=mysql_fetch_assoc($res)) {

			if ( $row['Fraction Discount']>0  ) {
				$sql=sprintf('update `Order No Product Transaction Fact` OTF  set      `Transaction Total Discount Amount`=%.2f ,`Transaction Net Amount`=%.2f,`Transaction Tax Amount`=%.2f  where `Order No Product Transaction Fact Key`=%d '
					,$row['Amount Discount']
					,$row['Transaction Net Amount']*(1-$row['Fraction Discount'])
					,$row['Transaction Tax Amount']*(1-$row['Fraction Discount'])

					,$row['Order No Product Transaction Fact Key']
				);
				//print "$sql\n";
				mysql_query($sql);



			}




		}



	}

	function get_allowances_from_order_trigger() {

		$deals_component_data=array();

		$sql=sprintf("select * from `Deal Component Dimension` where `Deal Component Trigger`='Order'   and `Deal Component Status`='Active' "

		);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$deals_component_data[$row['Deal Component Key']]=$row;
		}


		foreach ($deals_component_data as $deal_component_data ) {

			$terms_ok=false;
			$this->deals['Family']['Deal']=true;
			$this->deals['Family']['Deal Multiplicity']++;
			$this->deals['Family']['Terms Multiplicity']++;


			//'Order Total Net Amount AND Order Number','Order Items Net Amount AND Shipping Country','Order Interval','Product Quantity Ordered','Family Quantity Ordered','Total Amount','Order Number','Total Amount AND Shipping Country','Total Amount AND Order Number','Voucher'


			switch ($deal_component_data['Deal Component Terms Type']) {

			case('Order Interval'):

				$sql=sprintf("select count(*) as num from `Order Dimension` where `Order Customer Key`=%d and `Order Key`!=%d and `Order Date`>=%s and `Order Current Dispatch State`='Dispatched' and `Order Invoiced`='Yes'",
					$this->data['Order Customer Key'],
					$this->id,
					prepare_mysql(date('Y-m-d',strtotime("now -".$deal_component_data['Deal Component Terms'])).' 00:00:00')
				);

				$res2=mysql_query($sql);
				if ($_row=mysql_fetch_assoc($res2)) {


					if ($_row['num']>0) {
						$this->deals['Family']['Terms']=true;
						// print_r($deal_component_data);
						$this->get_allowances_from_deal_component_data($deal_component_data);
					}
				}
				break;


			}



		}

	}

	function get_allowances_from_family_trigger() {

		//'Percentage Off','Get Free','Get Same Free','Credit'


		//Get allowances doe to order tiggers ()



		$sql=sprintf("select `Product Family Key` from `Order Transaction Fact` where `Order Key`=%d group by `Product Family Key`",
			$this->id);
		$res_lines=mysql_query($sql);
		while ($row_lines=mysql_fetch_array($res_lines)) {

			$family_key=$row_lines['Product Family Key'];



			$deals_component_data=array();
			$discounts=0;

			$sql=sprintf("select * from `Deal Component Dimension`    where `Deal Component Trigger`='Family' and `Deal Component Trigger Key` =%d  and `Deal Component Status`='Active' ",
				$family_key
			);
			$res=mysql_query($sql);
			while ($row=mysql_fetch_assoc($res)) {
				$deals_component_data[$row['Deal Component Key']]=$row;
			}





			foreach ($deals_component_data as $deal_component_data ) {

				$terms_ok=false;
				$this->deals['Family']['Deal']=true;
				$this->deals['Family']['Deal Multiplicity']++;
				$this->deals['Family']['Terms Multiplicity']++;


				//'Order Total Net Amount AND Order Number','Order Items Net Amount AND Shipping Country','Order Interval','Product Quantity Ordered','Family Quantity Ordered','Total Amount','Order Number','Total Amount AND Shipping Country','Total Amount AND Order Number','Voucher'


				switch ($deal_component_data['Deal Component Terms Type']) {

				case('Order Interval'):

					$sql=sprintf("select count(*) as num from `Order Dimension` where `Order Customer Key`=%d and `Order Key`!=%d and `Order Date`>=%s  and `Order Current Dispatch State`='Dispatched' and `Order Invoiced`='Yes' ",
						$this->data['Order Customer Key'],
						$this->id,
						prepare_mysql(date('Y-m-d',strtotime("now -".$deal_component_data['Deal Component Terms'])).' 00:00:00')
					);

					$res2=mysql_query($sql);
					if ($_row=mysql_fetch_array($res2)) {
						if ($_row['num']>0) {
							$this->deals['Family']['Terms']=true;
							// print_r($deal_component_data);
							$this->get_allowances_from_deal_component_data($deal_component_data);
						}
					}
					break;
				case('Family Quantity Ordered'):



					$qty_family=0;
					$sql=sprintf('select sum(`Order Quantity`) as qty  from `Order Transaction Fact` OTF where `Order Key`=%d and `Product Family Key`=%d '
						,$this->id
						,$family_key
					);

					$res2=mysql_query($sql);
					if ($deal_component_data2=mysql_fetch_array($res2)) {
						$qty_family=$deal_component_data2['qty'];
					}
					if ($qty_family>=$deal_component_data['Deal Component Terms']) {
						$terms_ok=true;;
						$this->deals['Family']['Terms']=true;
						$this->get_allowances_from_deal_component_data($deal_component_data);
					}



					break;


				case('Product Quantity Ordered'):
					//print_r($deal_component_data);

					$qty_product=0;
					$sql=sprintf('select sum(`Order Quantity`) as qty  from `Order Transaction Fact` OTF where `Order Key`=%d and `Product ID`=%d '
						,$this->id
						,$deal_component_data['Deal Component Allowance Target Key']
					);

					$res2=mysql_query($sql);
					if ($deal_component_data2=mysql_fetch_array($res2)) {
						if ($deal_component_data2['qty']=='')
							$qty_product=0;
						else
							$qty_product=$deal_component_data2['qty'];
					}


					//print_r($deal_component_data);

					//print "** $qty_product  -> ".$deal_component_data['Deal Component Terms']."   **\n";


					if ($qty_product>0 and $qty_product>=$deal_component_data['Deal Component Terms']) {
						$terms_ok=true;;
						$this->deals['Family']['Terms']=true;

						// i dont underestad below thing maybe it is wrong
						if ($deal_component_data['Deal Component Terms']!=0)
							$deal_component_data['Deal Component Allowance']=$deal_component_data['Deal Component Allowance']*floor( $qty_product / $deal_component_data['Deal Component Terms']);

						$this->get_allowances_from_deal_component_data($deal_component_data);
					}



					break;

				}



			}


			//if ($row_lines['Product Code']=='ABPX-06') {
			//    exit;
			//  }
			//print_r($this->allowance);
		}
	}

	function update_discounts_order_tigger_no_item_allowances() {
		$this->get_allowances_from_order_trigger();




		foreach ($this->allowance['No Item Transaction'] as $type=>$allowance_data) {




			switch ($type) {
			case 'Charge':
				//print_r($allowance_data);

				if ($type=='Charge')$_type='Charges';
				else
					$_type=$type;

				$sql=sprintf('select *,`Order No Product Transaction Fact Key`,`Transaction Net Amount` from  `Order No Product Transaction Fact` OTF  where `Order Key`=%d and `Transaction Type`=%s '
					,$this->id
					,prepare_mysql($_type)
				);

				$res=mysql_query($sql);
				while ($row=mysql_fetch_assoc($res)) {
					//print_r($row);
					$sql=sprintf("insert into `Order No Product Transaction Deal Bridge` (`Order No Product Transaction Fact Key`,`Order Key`,`Deal Campaign Key`,`Deal Key`,`Deal Component Key`,`Deal Info`,`Amount Discount`,`Fraction Discount`)
					values (%d,%d,%d,%d,%d,%s,%f,%f)"
						,$row['Order No Product Transaction Fact Key']
						,$this->id


						,$allowance_data['Deal Campaign Key']
						,$allowance_data['Deal Key']
						,$allowance_data['Deal Component Key']

						,prepare_mysql($allowance_data['Deal Info'])
						,$row['Transaction Gross Amount']*$allowance_data['Percentage Off']
						,$allowance_data['Percentage Off']
					);
					mysql_query($sql);
					// print "$sql\n";
				}
			}


		}

	}

	function update_discounts_family_tigger() {



		$this->get_allowances_from_family_trigger();



		foreach ($this->allowance['Family Percentage Off'] as $allowance_data) {


			//$sql=sprintf('update `Order Transaction Fact` OTF  set  `Order Transaction Total Discount Amount`=`Order Transaction Gross Amount`*%f where `Order Key`=%d and `Product Family Key`=%d '
			// ,$allowance_data['Percentage Off']
			//  ,$this->id
			//  ,$allowance_data['Family Key']
			// );
			// mysql_query($sql);

			$sql=sprintf('select `Product Family Key`,`Product ID`,OTF.`Product Key`,`Order Transaction Fact Key`,`Order Transaction Gross Amount` from  `Order Transaction Fact` OTF  where `Order Key`=%d and `Product Family Key`=%d '
				,$this->id
				,$allowance_data['Family Key']
			);

			$res=mysql_query($sql);
			while ($row=mysql_fetch_array($res)) {
				$sql=sprintf("insert into `Order Transaction Deal Bridge` (`Order Transaction Fact Key`,`Order Key`,`Product Key`,`Product ID`,`Product Family Key`,`Deal Campaign Key`,`Deal Key`,`Deal Component Key`,`Deal Info`,`Amount Discount`,`Fraction Discount`,`Bunus Quantity`) values
				(%d,%d,%d,%d,%d,%d,%d,%d,%s,%f,%f,0)"
					,$row['Order Transaction Fact Key']
					,$this->id

					,$row['Product Key']
					,$row['Product ID']
					,$row['Product Family Key']
					,$allowance_data['Deal Campaign Key']
					,$allowance_data['Deal Key']
					,$allowance_data['Deal Component Key']

					,prepare_mysql($allowance_data['Deal Info'])
					,$row['Order Transaction Gross Amount']*$allowance_data['Percentage Off']
					,$allowance_data['Percentage Off']
				);
				mysql_query($sql);
				//print "$sql\n";
			}
		}

		foreach ($this->allowance['Get Free'] as $allowance_data) {



			$sql=sprintf('select `Product Family Key`,`Product ID`,OTF.`Product Key`,`Order Transaction Fact Key`,`Order Transaction Gross Amount` from  `Order Transaction Fact` OTF  where `Order Key`=%d and `Product ID`=%d '
				,$this->id
				,$allowance_data['Product ID']
			);

			$res=mysql_query($sql);
			while ($row=mysql_fetch_array($res)) {


				$amount_discount=0;
				$fraction_discount=0;

				$sql=sprintf("insert into `Order Transaction Deal Bridge` (`Order Transaction Fact Key`,`Order Key`,`Product Key`,`Product ID`,`Product Family Key`,`Deal Campaign Key`,`Deal Key`,`Deal Component Key`,`Deal Info`,`Amount Discount`,`Fraction Discount`,`Bunus Quantity`) values (%d,%d,%d,%d,%d,%d,%d,%d,%s,%f,%f,%d)"
					,$row['Order Transaction Fact Key']
					,$this->id

					,$row['Product Key']
					,$row['Product ID']
					,$row['Product Family Key']
					,$allowance_data['Deal Campaign Key']
					,$allowance_data['Deal Key']
					,$allowance_data['Deal Component Key']

					,prepare_mysql($allowance_data['Deal Info'])
					,$amount_discount
					,$fraction_discount
					,$allowance_data['Get Free']
				);
				mysql_query($sql);
				// print "$sql\n";
			}
		}











	}

	function get_discounted_products() {
		$sql=sprintf('select  `Product Key` from   `Order Transaction Deal Bridge`   where `Order Key`=%d  group by `Product Key` '
			,$this->id
		);
		//print "$sql\n";
		$res=mysql_query($sql);
		$disconted_products=array();
		while ($row=mysql_fetch_array($res)) {
			$disconted_products[$row['Product Key']]=$row['Product Key'];
		}
		return $disconted_products;

	}

	function update_deal_bridge_from_assets_deals() {


		$sql=sprintf("select B.`Deal Key` from  `Order Deal Bridge` B  left join `Deal Dimension` D on (D.`Deal Key`=B.`Deal Key`) where `Deal Trigger` in ('Department','Family','Product') and `Order Key`=%d",$this->id);
		// exit("$sql\n");
		$res=mysql_query($sql);
		$deal_keys=array();
		while ($row=mysql_fetch_assoc($res)) {
			$deal_keys[]=$row['Deal Key'];
		}
		if (count($deal_keys)) {
			$sql=sprintf("delete from `Order Deal Bridge` where `Order Key`=%d and `Deal Key` in (%s)   ",$this->id,join(',',$deal_keys));
			mysql_query($sql);
		}

		$sql=sprintf("select `Deal Campaign Key`,`Deal Component Key`, `Deal Key` from  `Order Transaction Deal Bridge`  where`Order Key`=%d and `Deal Component Key`!=0",
			$this->id);

		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$sql=sprintf("insert into `Order Deal Bridge` values(%d,%d,%d,%d,'Yes','Yes') ON DUPLICATE KEY UPDATE `Used`='Yes'",
				$this->id,
				$row['Deal Campaign Key'],
				$row['Deal Key'],
				$row['Deal Component Key']
			);
			mysql_query($sql);
		}







	}

	function update_deals_usage() {

		$sql=sprintf("select `Deal Key` from  `Order Deal Bridge` where `Order Key`=%d",$this->id);
		// exit("$sql\n");
		$res=mysql_query($sql);

		while ($row=mysql_fetch_assoc($res)) {
			$deal=new Deal($row['Deal Key']);
			$deal->update_usage();
		}

	}


	function update_shipping_method($value) {

		$sql=sprintf("update `Order Dimension` set `Order Shipping Method`=%s where `Order Key`=%d"
			,prepare_mysql($value)
			,$this->id
		);
		mysql_query($sql);

		$this->data['Order Shipping Method']=$value;

	}


	function update_order_is_for_collection($value) {

		if ($value!='Yes')
			$value='No';

		$old_value=$this->data['Order For Collection'];
		if ($old_value!=$value) {

			if ($value=='Yes') {
				$store=new Store($this->data['Order Store Key']);
				$collection_address=new Address($store->data['Store Collection Address Key']);
				if ($collection_address->id) {
					$store_2_alpha_country_code=$collection_address->data['Address Country 2 Alpha Code'];
					$store_country_code=$collection_address->data['Address Country Code'];
					$store_town_code=$collection_address->data['Address Town'];
					$store_world_region_code=$collection_address->data['Address World Region'];
					$store_postal_code=$collection_address->data['Address Postal Code'];

				} else {
					$store_2_alpha_country_code='XX';
					$store_country_code='UNK';
					$store_town_code='';
					$store_world_region_code='UNKN';
					$store_postal_code='';


				}
				$sql=sprintf("update `Order Dimension` set `Order For Collection`='Yes' ,
				`Order Ship To Country Code`=%s,
				`Order Ship To Country 2 Alpha Code`=%s,
				`Order Ship To World Region Code`=%s,
				`Order Ship To Town`=%s,
				`Order Ship To Postal Code`=%s,
				`Order XHTML Ship Tos`=%s,
				`Order Ship To Keys`=''
				where `Order Key`=%d"
					,prepare_mysql($store_country_code)
					,prepare_mysql($store_2_alpha_country_code)
					,prepare_mysql($store_world_region_code)
					,prepare_mysql($store_town_code)
					,prepare_mysql($store_postal_code)
					,prepare_mysql($collection_address->display('xhtml'))
					,$this->id
				);
				mysql_query($sql);


			}
			else {
				$customer=new Customer($this->data['Order Customer Key']);

				$ship_to= $customer->set_current_ship_to('return object');





				$sql=sprintf("update `Order Dimension` set `Order For Collection`='No' ,`Order Ship To Country Code`=%s,`Order XHTML Ship Tos`=%s,`Order Ship To Keys`=%s  ,`Order Ship To World Region Code`=%s,`Order Ship To Town`=%s,`Order Ship To Postal Code`=%s      where `Order Key`=%d"
					,prepare_mysql($ship_to->data['Ship To Country Code'])

					,prepare_mysql($ship_to->data['Ship To XHTML Address'])
					,prepare_mysql($ship_to->id)
					,prepare_mysql($ship_to->get('World Region Code'))
					,prepare_mysql($ship_to->data['Ship To Town'])
					,prepare_mysql($ship_to->data['Ship To Postal Code'])
					,$this->id
				);
				mysql_query($sql);
			}
			$this->get_data('id',$this->id);
			$this->new_value=$value;
			$this->updated=true;

			$this->update_shipping();
			$this->update_tax();
			$this->update_item_totals_from_order_transactions();
			$this->get_items_totals_by_adding_transactions();
			$this->update_no_normal_totals('save');
			$this->update_totals_from_order_transactions();

			//print_r($this->data);

			$this->apply_payment_from_customer_account();



		} else {
			$this->msg=_('Nothing to change');

		}


	}

	function update_ship_to($ship_to_key=false) {

		if (!$ship_to_key) {
			$customer=new Customer($this->data['Order Customer Key']);
			$ship_to= $customer->set_current_ship_to('return object');
		} else {
			//TODO
			$ship_to=new Ship_To($ship_to_key);
		}






		$sql=sprintf("update `Order Dimension` set `Order For Collection`='No' ,`Order Ship To Key To Deliver`=%d,  `Order Ship To Country Code`=%s,`Order XHTML Ship Tos`=%s,`Order Ship To Keys`=%s  ,`Order Ship To World Region Code`=%s,`Order Ship To Town`=%s,`Order Ship To Postal Code`=%s   where `Order Key`=%d"
			,$ship_to->id
			,prepare_mysql($ship_to->data['Ship To Country Code'])
			,prepare_mysql($ship_to->data['Ship To XHTML Address'])
			,prepare_mysql($ship_to->id)
			,prepare_mysql($ship_to->get('World Region Code'))
			,prepare_mysql($ship_to->data['Ship To Town'])
			,prepare_mysql($ship_to->data['Ship To Postal Code'])

			,$this->id

		);
		mysql_query($sql);
		if (mysql_affected_rows()>0) {
			$this->get_data('id',$this->id);
			$this->updated=true;
			$this->new_value=$ship_to->data['Ship To XHTML Address'];
		} else {
			$this->msg=_('Nothing to change');
		}

		$this->update_shipping();
		if ($this->data['Order Tax Selection Type']!='set') {
			$this->update_tax();
		}

	}

	function add_ship_to($ship_to_key) {
		$order_ship_to_keys=preg_split('/\s*\,\s*/',$this->data ['Order Ship To Keys']);
		if (!in_array($ship_to_key,$order_ship_to_keys)) {
			$ship_to=new Ship_To($ship_to_key);
			if ($this->data ['Order Ship To Keys']=='') {
				$this->data ['Order Ship To Keys']=$ship_to_key;
				$this->data ['Order XHTML Ship Tos']='<div>'.$ship_to->display('xhtml').'</div>';
				$this->data ['Order Ship To Country Code']=$ship_to->data['Ship To Country Code'];
				$this->data ['Order Ship To Country 2 Alpha Code']=$ship_to->data['Ship To Country 2 Alpha Code'];
				$this->data ['Order Ship To World Region Code']=$ship_to->get('World Region Code');
				$this->data ['Order Ship To Town']=$ship_to->data['Ship To Town'];
				$this->data ['Order Ship To Postal Code']=$ship_to->data['Ship To Postal Code'];
			} else {
				$this->data ['Order Ship To Keys'].=','.$ship_to_key;
				$this->data ['Order XHTML Ship Tos'].='<div>'.$ship_to->display('xhtml').'</div>';
			}
		}
	}



	function update_billing_to($billing_to_key=false) {

		$old_billing_country_2alpha_code=$this->data['Order Billing To Country 2 Alpha Code'];

		if (!$billing_to_key) {
			$customer=new Customer($this->data['Order Customer Key']);
			$billing_to= $customer->set_current_billing_to('return object');
		} else {
			//TODO
			$billing_to=new Billing_To($billing_to_key);
		}



		$sql=sprintf("update `Order Dimension` set `Order Billing To Key To Bill`=%d,  `Order Billing To Country Code`=%s, `Order Billing To Country 2 Alpha Code`=%s,`Order XHTML Billing Tos`=%s,`Order Billing To Keys`=%s  ,`Order Billing To World Region Code`=%s,`Order Billing To Town`=%s,`Order Billing To Postal Code`=%s   where `Order Key`=%d"
			,$billing_to->id
			,prepare_mysql($billing_to->data['Billing To Country Code'])
			,prepare_mysql($billing_to->data['Billing To Country 2 Alpha Code'])
			,prepare_mysql($billing_to->data['Billing To XHTML Address'])
			,prepare_mysql($billing_to->id)
			,prepare_mysql($billing_to->get('World Region Code'))
			,prepare_mysql($billing_to->data['Billing To Town'])
			,prepare_mysql($billing_to->data['Billing To Postal Code'])

			,$this->id

		);
		mysql_query($sql);
		//print $sql;
		if (mysql_affected_rows()>0) {
			$this->get_data('id',$this->id);
			$this->updated=true;
			$this->new_value=$billing_to->data['Billing To XHTML Address'];
		} else {
			$this->msg=_('Nothing to change');
		}
		if ($this->data['Order Tax Selection Type']!='set') {


			if ($this->data['Order Billing To Country 2 Alpha Code']!=$old_billing_country_2alpha_code) {
				include_once 'common_tax_number_functions.php';
				$tax_number_data=check_tax_number($this->data['Order Tax Number'],$this->data['Order Billing To Country 2 Alpha Code']);




				$this->update(
					array(
						'Order Tax Number'=>$this->data['Order Tax Number'],
						'Order Tax Number Valid'=>$tax_number_data['Tax Number Valid'],
						'Order Tax Number Validation Date'=>$tax_number_data['Tax Number Validation Date'],
						'Order Tax Number Associated Name'=>$tax_number_data['Tax Number Associated Name'],
						'Order Tax Number Associated Address'=>$tax_number_data['Tax Number Associated Address'],
					)
				);

			}
			$this->update_tax();
		}


	}


	function add_billing_to($billing_to_key) {
		$order_billing_to_keys=preg_split('/\s*\,\s*/',$this->data ['Order Billing To Keys']);
		if (!in_array($billing_to_key,$order_billing_to_keys)) {
			$billing_to=new Billing_To($billing_to_key);
			if ($this->data ['Order Billing To Keys']=='') {
				$this->data ['Order Billing To Keys']=$billing_to_key;
				$this->data ['Order XHTML Billing Tos']='<div>'.$billing_to->display('xhtml').'</div>';
				$this->data ['Order Billing To Country Code']=$billing_to->data['Billing To Country Code'];
				$this->data ['Order Billing To World Region Code']=$billing_to->get('World Region Code');
				$this->data ['Order Billing To Town']=$billing_to->data['Billing To Town'];
				$this->data ['Order Billing To Postal Code']=$billing_to->data['Billing To Postal Code'];
			} else {
				$this->data ['Order Billing To Keys'].=','.$billing_to_key;
				$this->data ['Order XHTML Billing Tos'].='<div>'.$billing_to->display('xhtml').'</div>';
			}
		}
	}

	function update_full_search() {

		$first_full_search=$this->data['Order Public ID'].' '.$this->data['Order Customer Name'].' '.strftime("%d %b %B %Y",strtotime($this->data['Order Date']));
		$second_full_search=strip_tags(preg_replace('/\<br\/\>/',' ',$this->data['Order XHTML Ship Tos'])).' '.$this->data['Order Customer Contact Name'];
		$img='';

		$amount='';
		if ($this->data['Order Current Payment State']=='Waiting Payment' or $this->data['Order Current Payment State']=='Partially Paid') {
			$amount=' '.money($this->data['Order Total Amount'],$this->data['Order Currency']);
		}
		elseif ($this->data['Order Current Payment State']=='Paid' or $this->data['Order Current Payment State']=='Payment Refunded') {
			$amount=' '.money($this->data['Order Invoiced Balance Total Amount'],$this->data['Order Currency']);
		}

		$show_description=$this->data['Order Customer Name'].' ('.strftime("%e %b %Y", strtotime($this->data['Order Date'])).') '.$this->data['Order Current XHTML Payment State'].$amount;

		$description1='<b><a href="order.php?id='.$this->id.'">'.$this->data['Order Public ID'].'</a></b>';
		$description='<table ><tr style="border:none;"><td  class="col1"'.$description1.'</td><td class="col2">'.$show_description.'</td></tr></table>';


		$sql=sprintf("insert into `Search Full Text Dimension` (`Store Key`,`Subject`,`Subject Key`,`First Search Full Text`,`Second Search Full Text`,`Search Result Name`,`Search Result Description`,`Search Result Image`) values  (%s,'Order',%d,%s,%s,%s,%s,%s) on duplicate key
                     update `First Search Full Text`=%s ,`Second Search Full Text`=%s ,`Search Result Name`=%s,`Search Result Description`=%s,`Search Result Image`=%s"
			,$this->data['Order Store Key']
			,$this->id
			,prepare_mysql($first_full_search)
			,prepare_mysql($second_full_search,false)
			,prepare_mysql($this->data['Order Public ID'],false)
			,prepare_mysql($description,false)
			,prepare_mysql($img,false)
			,prepare_mysql($first_full_search)
			,prepare_mysql($second_full_search,false)
			,prepare_mysql($this->data['Order Public ID'],false)
			,prepare_mysql($description,false)


			,prepare_mysql($img,false)
		);
		mysql_query($sql);



		$sql=sprintf("insert into `Search Full Text Dimension` values  (%s,'Order',%d,%s,%s) on duplicate key update `First Search Full Text`=%s ,`Second Search Full Text`=%s "
			,$this->data['Order Store Key']
			,$this->id
			,prepare_mysql($first_full_search)
			,prepare_mysql($second_full_search)
			,prepare_mysql($first_full_search)
			,prepare_mysql($second_full_search)
		);
		mysql_query($sql);

	}


	public function prepare_file_as($number) {

		$number=strtolower($number);
		if (preg_match("/^\d+/",$number,$match)) {
			$part_number=$match[0];
			$file_as=preg_replace('/^\d+/',sprintf("%012d",$part_number),$number);

		}
		elseif (preg_match("/\d+$/",$number,$match)) {
			$part_number=$match[0];
			$file_as=preg_replace('/\d+$/',sprintf("%012d",$part_number),$number);

		}
		else {
			$file_as=$number;
		}

		return $file_as;
	}


	function get_number_post_order_transactions() {


		$sql=sprintf("select count(*) as num from `Order Post Transaction Dimension` where `Order Key`=%d  ",$this->id);
		$res=mysql_query($sql);
		$number=0;
		if ($row=mysql_fetch_assoc($res)) {
			$number=$row['num'];
		}
		return $number;
	}

	function get_number_products() {
		$sql=sprintf("select count(*) as num from `Order Transaction Fact` where `Order Key`=%d  ",$this->id);
		$res=mysql_query($sql);
		$number=0;
		if ($row=mysql_fetch_assoc($res)) {
			$number=($row['num']==''?0:$row['num']);
		}
		return $number;
	}


	function update_number_products() {
		$this->data['Order Number Products']=$this->get_number_products();
		$sql=sprintf("update `Order Dimension` set `Order Number Products`=%d where `Order Key`=%d",
			$this->data['Order Number Products'],
			$this->id
		);
		mysql_query($sql);
	}

	function get_number_items($type='ordered') {

		switch ($type) {
		default:
			$qty_query=' sum(`Order Quantity`)';
			break;

		}

		$sql=sprintf("select %s as num from `Order Transaction Fact` where `Order Key`=%d  ",
			$qty_query,
			$this->id);
		$res=mysql_query($sql);
		$number=0;
		if ($row=mysql_fetch_assoc($res)) {
			$number=($row['num']==''?0:$row['num']);
		}
		return $number;
	}


	function update_number_items() {
		$this->data['Order Number Items']=$this->get_number_items();
		$sql=sprintf("update `Order Dimension` set `Order Number Items`=%d where `Order Key`=%d",
			$this->data['Order Number Items'],
			$this->id
		);
		mysql_query($sql);
	}


	function mark_all_transactions_for_refund($data) {


		$sql=sprintf("delete from `Order Post Transaction Dimension` where `Order Key`=%d  and `State`='In Process'",
			$this->id
		);
		mysql_query($sql);


		$sql=sprintf("select `Order Transaction Fact Key`, `Invoice Quantity`,`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`) as value  from  `Order Transaction Fact` OTF left join `Order Post Transaction Dimension` POT  on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) where `Invoice Quantity`>0 and OTF.`Order Key`=%d ",
			$this->id

		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {

			$sql=sprintf("insert into `Order Post Transaction Dimension` (`Order Transaction Fact Key`,`Order Key`,`Quantity`,`Operation`,`Reason`,`To Be Returned`,`Customer Key`,'Credit') values (%d,%d,%f,%s,%s,%s,%d,%f)",
				$row['Order Transaction Fact Key'],
				$this->id,
				$row['Invoice Quantity'],
				prepare_mysql('Refund'),
				prepare_mysql($data['Reason']),
				prepare_mysql($data['To Be Returned']),
				$this->data['Order Customer Key'],
				$row['value']
			);
			mysql_query($sql);


		}

	}


	function get_post_transactions_in_process_data() {
		$data=array(
			'Refund'=>array('Distinct_Products'=>0,'Amount'=>0,'Formated_Amount'=>money(0,$this->data['Order Currency'])),
			'Credit'=>array('Distinct_Products'=>0,'Amount'=>0,'Formated_Amount'=>money(0,$this->data['Order Currency']),'State'=>''),
			'Resend'=>array('Distinct_Products'=>0,'Market_Value'=>0,'Formated_Market_Value'=>money(0,$this->data['Order Currency']),'state'=>''),
			'Saved_Credit'=>array('Distinct_Products'=>0,'Amount'=>0,'Formated_Amount'=>money(0,$this->data['Order Currency']),'State'=>'')

		);
		$sql=sprintf("select `Invoice Currency Code`, sum(`Quantity`*(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`)/`Invoice Quantity`) as value, count(DISTINCT OTF.`Product Key` ) as num from `Order Post Transaction Dimension` POT left join `Order Transaction Fact` OTF on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) where `Invoice Quantity`>0 and POT.`Order Key`=%d and   `Operation`='Refund'",
			$this->id

		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$data['Refund']['Distinct_Products']=$row['num'];
			$data['Refund']['Amount']=$row['value'];
			$data['Refund']['Formated_Amount']=money($row['value'],$row['Invoice Currency Code']);
		}

		$sql=sprintf("select `Invoice Currency Code`, sum(`Quantity`*(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`)/`Invoice Quantity`) as value, count(DISTINCT OTF.`Product Key` ) as num from `Order Post Transaction Dimension` POT left join `Order Transaction Fact` OTF on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) where `Invoice Quantity`>0 and POT.`Order Key`=%d and   `Operation`='Credit'",
			$this->id
		);


		$sql=sprintf("select `Invoice Currency Code`, sum(POT.`Credit`) as value, count(DISTINCT OTF.`Product Key` ) as num from `Order Post Transaction Dimension` POT left join `Order Transaction Fact` OTF on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) where   POT.`Order Key`=%d and   `Operation`='Credit' and `State`='Saved'  ",
			$this->id
		);


		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$data['Saved_Credit']['Distinct_Products']=$row['num'];
			$data['Saved_Credit']['Amount']=$row['value'];
			$data['Saved_Credit']['Formated_Amount']=money($row['value'],$row['Invoice Currency Code']);
		}



		$sql=sprintf("select `Invoice Currency Code`, sum(POT.`Credit`) as value, count(DISTINCT OTF.`Product Key` ) as num from `Order Post Transaction Dimension` POT left join `Order Transaction Fact` OTF on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) where   POT.`Order Key`=%d and   `Operation`='Credit' and `State`='In Process'  ",
			$this->id
		);


		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$data['Credit']['Distinct_Products']=$row['num'];
			$data['Credit']['Amount']=$row['value'];
			$data['Credit']['Formated_Amount']=money($row['value'],$row['Invoice Currency Code']);
		}

		$sql=sprintf("select  `State`,`Product Currency`,sum(`Quantity`*`Product History Price`) as value,  count(DISTINCT OTF.`Product Key` ) as num from `Order Post Transaction Dimension` POT left join `Order Transaction Fact` OTF on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) left join `Product History DImension` PH on (OTF.`Product Key`=PH.`Product Key`) left join `Product Dimension` P on (P.`Product ID`=PH.`Product ID`)  where `Operation`='Resend' and POT.`Order Key`=%d ",
			$this->id
		);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$data['Resend']['Distinct_Products']=$row['num'];
			$data['Resend']['State']=$row['State'];

			$data['Resend']['Market_Value']=$row['value'];
			$data['Resend']['Formated_Market_Value']=money($row['value'],$row['Product Currency']);

		}


		return $data;

	}



	function cancel_post_transactions_in_process() {
		$this->deleted_post_transactions=0;
		$sql=sprintf("delete from `Order Post Transaction Dimension` where `Order Key`=%d and `State`='In Process' ",
			$this->id
		);
		mysql_query($sql);
		$this->deleted_post_transactions=mysql_affected_rows();



	}




	function cancel_submited_credits() {
		$sql=sprintf("delete  from `Order Post Transaction Dimension` where `Order Key`=%d and `State`='Saved' and `Operation`='Credit'",
			$this->id
		);
		mysql_query($sql);

	}

	function submit_credits() {
		$sql=sprintf("update `Order Post Transaction Dimension` set `Credit Saved`=`Credit` , `State`='Saved'  where `Order Key`=%d and `State`='In Process' and `Operation`='Credit'",
			$this->id
		);
		mysql_query($sql);

	}

	function create_post_transaction_in_process($otf_key,$key,$values) {


		if (!preg_match('/^(Quantity|Operation|Reason|To Be Returned)$/',$key)) {
			$this->error=true;
			return;
		}
		$this->deleted_post_transaction=false;
		$this->update_post_transaction=false;
		$this->created_post_transaction=false;
		$this->updated=false;
		$sql=sprintf('select * from `Order Post Transaction Dimension` where `Order Transaction Fact Key`=%d',$otf_key);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			if ($row['Order Key']!=$this->id) {
				$this->error=true;
				return;
			}

			if ($key=='Quantity' and $values[$key]<=0) {
				$sql=sprintf("delete from `Order Post Transaction Dimension` where `Order Post Transaction Key`=%d ",
					$row['Order Post Transaction Key']
				);
				mysql_query($sql);
				if (mysql_affected_rows()>0) {
					$this->update_post_transaction=true;
					$this->updated=true;

					$opt_key=$row['Order Post Transaction Key'];
					$this->deleted_post_transaction=true;
				}
			} else {


				$sql=sprintf("update `Order Post Transaction Dimension` set `%s`=%s where `Order Post Transaction Key`=%d ",
					$key,
					prepare_mysql($values[$key]),
					$row['Order Post Transaction Key']
				);
				mysql_query($sql);
				$affected_rows=mysql_affected_rows();
				if ($key=='Quantity' and $row['Operation']=='Credit') {
					$sql=sprintf("select `Invoice Currency Code`, (`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`)/`Invoice Quantity` as value,OTF.`Order Transaction Fact Key` from  `Order Transaction Fact`  OTF where OTF.`Order Transaction Fact Key`=%d",
						$otf_key
					);



					$res2=mysql_query($sql);
					if ($row2=mysql_fetch_assoc($res2)) {
						$sql=sprintf("update `Order Post Transaction Dimension` set `Credit`=%.2f where `Order Post Transaction Key`=%d ",
							$row2['value']*$values[$key],
							$row['Order Post Transaction Key']
						);
						mysql_query($sql);
					}



				}


				if ($key=='Operation' ) {
					$sql=sprintf("select `Invoice Currency Code`, (`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`)/`Invoice Quantity` as value,OTF.`Order Transaction Fact Key` from  `Order Transaction Fact`  OTF where OTF.`Order Transaction Fact Key`=%d",
						$otf_key
					);


					$qty=0;
					if (is_numeric($row['Quantity'])) {
						$qty=$row['Quantity'];
					}

					$res2=mysql_query($sql);
					if ($row2=mysql_fetch_assoc($res2)) {
						$sql=sprintf("update `Order Post Transaction Dimension` set `Credit`=%.2f where `Order Post Transaction Key`=%d ",
							$row2['value']*$qty,
							$row['Order Post Transaction Key']
						);
						mysql_query($sql);
					}



				}

				if ($affected_rows>0) {



					$this->update_post_transaction=true;
					$this->updated=true;
					$opt_key=$row['Order Post Transaction Key'];



				}
			}

		}
		else {
			$sql=sprintf("insert into `Order Post Transaction Dimension` (`Order Transaction Fact Key`,`Order Key`,`Quantity`,`Operation`,`Reason`,`To Be Returned`,`Customer Key`) values (%d,%d,%f,%s,%s,%s,%d)",
				$otf_key,
				$this->id,
				$values['Quantity'],
				prepare_mysql($values['Operation']),
				prepare_mysql($values['Reason']),
				prepare_mysql($values['To Be Returned']),
				$this->data['Order Customer Key']
			);

			mysql_query($sql);
			if (mysql_affected_rows()>0) {
				$this->created_post_transaction=true;
				$this->updated=true;
				$opt_key=mysql_insert_id();



				if ($values['Operation']='Credit') {
					$sql=sprintf("select `Invoice Currency Code`, sum(`Quantity`*(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`)/`Invoice Quantity`) as value from `Order Post Transaction Dimension` POT left join `Order Transaction Fact` OTF on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) where `Invoice Quantity`>0 and OTF.`Order Transaction Fact Key`=%d and  `Operation`='Credit' and `State`='In Process'",
						$otf_key
					);
					$res2=mysql_query($sql);
					if ($row2=mysql_fetch_assoc($res2)) {
						$sql=sprintf("update `Order Post Transaction Dimension` set `Credit`=%.2f where `Order Post Transaction Key`=%d ",
							$row2['value'],
							$opt_key
						);
						mysql_query($sql);
					}



				}


			}

		}
		$transaction_data=array();



		if ($this->created_post_transaction or $this->update_post_transaction) {

			$sql=sprintf('select `Operation`,`Reason`,`Quantity`,`To Be Returned` from `Order Post Transaction Dimension` where `Order Transaction Fact Key`=%d',$otf_key);
			$res2=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res2)) {
				$transaction_data['Quantity']=$row['Quantity'];
				$transaction_data['Operation']=$row['Operation'];
				$transaction_data['Reason']=$row['Reason'];
				$transaction_data['To Be Returned']=$row['To Be Returned'];
			}


			$transaction_data['Order Post Transaction Key']=$opt_key;
		}
		if ($this->deleted_post_transaction) {
			$transaction_data['Quantity']='';
			$transaction_data['Operation']='';
			$transaction_data['Reason']='';
			$transaction_data['To Be Returned']='';
		}
		return $transaction_data;

	}


	function add_post_order_transactions($data) {
		$otf_key=array();
		$sql=sprintf("select `Order Post Transaction Key`,OTF.`Product ID`,`Product Package Weight`,`Quantity`,`Product Units Per Case` from `Order Post Transaction Dimension` POT  left join `Order Transaction Fact` OTF on (OTF.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`) left join `Product History Dimension`  PH on (PH.`Product Key`=OTF.`Product Key`) left join `Product Dimension` P on (P.`Product ID`=PH.`Product ID`)   where POT.`Order Key`=%d  and `State`='In Process' ",
			$this->id);
		//print $sql;
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$order_key=$this->id;
			$order_date=gmdate('Y-m-d H:i:s');
			$order_public_id=$this->data['Order Public ID'];

			$product=new Product('pid',$row['Product ID']);

			$bonus_quantity=0;
			$sql = sprintf( "insert into `Order Transaction Fact` (`Order Date`,`Order Key`,`Order Public ID`,`Delivery Note Key`,`Delivery Note ID`,`Order Bonus Quantity`,`Order Transaction Type`,`Transaction Tax Rate`,`Transaction Tax Code`,`Order Currency Code`,`Estimated Weight`,`Order Last Updated Date`,
                             `Product Key`,`Product ID`,`Product Code`,`Product Family Key`,`Product Department Key`,
                             `Current Dispatching State`,`Current Payment State`,`Customer Key`,`Delivery Note Quantity`,`Ship To Key`,`Billing To Key`,
                             `Order Transaction Gross Amount`,`Order Transaction Total Discount Amount`,`Order Transaction Amount`,`Metadata`,`Store Key`,`Units Per Case`,`Customer Message`)
                             values (%s,%s,%s,%d,%s,%f,%s,%f,%s,%s,%s,  %s,
                             %d,%d,%s,%d,%d,
                             %s,%s,%d,%s,%s,%s,
                             %.2f,%.2f,%.2f,%s,%s,%f,'') ",
				prepare_mysql($order_date),
				prepare_mysql($order_key),
				prepare_mysql($order_public_id),

				0,
				prepare_mysql(''),

				$bonus_quantity,
				prepare_mysql('Resend'),
				$data['Order Tax Rate'],
				prepare_mysql ($data['Order Tax Code']),
				prepare_mysql ( $this->data['Order Currency'] ),
				$row['Product Package Weight']*$row['Quantity'],

				prepare_mysql($order_date),
				$product->id,
				$product->data['Product ID'],
				prepare_mysql($product->data['Product Code']),
				$product->data['Product Family Key'],
				$product->data['Product Main Department Key'],

				prepare_mysql ( 'In Process' ),
				prepare_mysql ( $data ['Current Payment State'] ),
				prepare_mysql ( $this->data['Order Customer Key' ] ),

				$row['Quantity'],
				prepare_mysql ( $data['Ship To Key'] ),
				prepare_mysql ( $data['Billing To Key'] ),
				$data['Gross'],
				0,
				$data['Gross'],
				prepare_mysql ( $data ['Metadata'] ,false),
				prepare_mysql ( $this->data['Order Store Key'] ),
				$row['Product Units Per Case']

			);

			if (! mysql_query( $sql ))
				exit ( "$sql can not update xx orphan transaction\n" );
			$otf_key=mysql_insert_id();

			$sql=sprintf("update  `Order Post Transaction Dimension` set `Order Post Transaction Fact Key`=%d where `Order Post Transaction Key`=%d   ",$otf_key,$row['Order Post Transaction Key']);
			mysql_query( $sql );
			//print $sql;
		}

		if (array_key_exists('Supplier Metadata', $data)) {

			$sql = sprintf( "update`Order Transaction Fact` set  `Supplier Metadata`=%s  where `Order Transaction Fact Key`=%d ",
				prepare_mysql($data['Supplier Metadata']),
				$otf_key

			);
			//        print "$sql\n";
			mysql_query($sql);
		}

		return array('otf_key'=>$otf_key);

	}

	function get_notes() {

		$notes='';
		if ($this->data['Order Customer Sevices Note']!='')
			$notes.="<div><div style='color:#777;font-size:90%;padding-bottom:5px'>"._('Note').":</div>".$this->data['Order Customer Sevices Note']."</div>";
		if ($this->data['Order Customer Message']!='')
			$notes.="<div><div style='color:#777;font-size:90%;padding-bottom:5px'>"._('Customer Note').":</div>".$this->data['Order Customer Message']."</div>";

		return $notes;

	}


	function get_currency_symbol() {
		return currency_symbol($this->data['Order Currency']);
	}

	function get_formated_tax_info() {
		$selection_type=$this->data['Order Tax Selection Type'];
		$formated_tax_info='<span title="'.$selection_type.'">'.$this->data['Order Tax Name'].'</span>';
		return $formated_tax_info;
	}

	function get_formated_tax_info_with_operations() {
		$operations=$this->data['Order Tax Operations'];
		$selection_type=$this->data['Order Tax Selection Type'];
		$formated_tax_info='<span title="'.$selection_type.'">'.$this->data['Order Tax Name'].'</span>'.$operations;
		return $formated_tax_info;
	}


	function get_formated_dispatch_state() {
		return get_order_formated_dispatch_state($this->data['Order Current Dispatch State'],$this->id);

	}



	function set_as_invoiced() {


		$sql=sprintf("update `Order Dimension` set `Order Invoiced`='Yes',`Order Current Payment State`='Waiting Payment'   where `Order Key`=%d ",
			$this->id
		);

		mysql_query($sql);

		$this->data['Order Invoiced']='Yes';
		$this->data['Order Current Payment State']='Waiting Payment';

	}


	function get_no_product_deal_info($type) {
		$deal_info='';
		$sql=sprintf("select `Deal Info` from `Order No Product Transaction Deal Bridge` B left join `Order No Product Transaction Fact` OTF on (OTF.`Order No Product Transaction Fact Key`=B.`Order No Product Transaction Fact Key`) where B.`Order Key`=%d and `Transaction Type`=%s",
			$this->id,
			prepare_mysql($type)
		);

		$res=mysql_query($sql);

		if ($row=mysql_fetch_assoc($res)) {
			$deal_info=$row['Deal Info'];
		}

		return $deal_info;
	}





	function get_tax_data() {


		include_once 'common_geography_functions.php';

		$store=new Store($this->data['Order Store Key']);
		$customer=new Customer($this->data['Order Customer Key']);

		switch ($store->data['Store Tax Country Code']) {
		case 'GBR':

			$tax_category=array();

			$sql=sprintf("select `Tax Category Code`,`Tax Category Type`,`Tax Category Name`,`Tax Category Rate` from `Tax Category Dimension`  where `Tax Category Country Code`='GBR' and `Tax Category Active`='Yes'");
			//exit($sql);
			$res=mysql_query($sql);








			while ($row=mysql_fetch_assoc($res)) {


				switch ($row['Tax Category Name']) {
				case 'Outside the scope of VAT':
					$tax_category_name=_('Outside the scope of VAT');
					break;
				case 'VAT 17.5%':
					$tax_category_name=_('VAT 17.5%');
					break;
				case 'VAT 20%':
					$tax_category_name=_('VAT 20%');
					break;
				case 'VAT 15%':
					$tax_category_name=_('VAT 15%');
					break;
				case 'No Tax':
					$tax_category_name=_('No Tax');
					break;
				case 'Exempt from VAT':
					$tax_category_name=_('Exempt from VAT');
					break;


				default:
					$tax_category_name=$row['Tax Category Name'];
				}



				$tax_category[$row['Tax Category Type']]= array(
					'code'=>$row['Tax Category Code'],
					'name'=>$tax_category_name,
					'rate'=>$row['Tax Category Rate']);



			}


			if (in_array($this->data['Order Ship To Country Code'],array('GBR','UNK','IMN'))) {

				return array(
					'code'=>$tax_category['Standard']['code'],
					'name'=>$tax_category['Standard']['name'],
					'rate'=>$tax_category['Standard']['rate'],
					'state'=>'delivery to GBR',
					'operations'=>''

				);
			}
			elseif (in_array($this->data['Order Billing To Country Code'],array('GBR','UNK','IMN'))) {

				return array(
					'code'=>$tax_category['Standard']['code'],
					'name'=>$tax_category['Standard']['name'],
					'rate'=>$tax_category['Standard']['rate'],
					'state'=>'billing to GBR',
					'operations'=>''
				);
			}
			elseif ( in_array($this->data['Order Billing To Country Code'],get_countries_EC_Fiscal_VAT_area())) {

				if ($this->data['Order Tax Number Valid']=='Yes') {


					$response= array(
						'code'=>$tax_category['Outside']['code'],
						'name'=>$tax_category['Outside']['name'],
						'rate'=>$tax_category['Outside']['rate'],
						'state'=>'EC with valid tax number',
						'operations'=>'<div>'._('Valid tax number').'<br>'.$this->data['Order Tax Number'].'</div>'

					);

				}
				else {

					if ($this->data['Order Tax Number']=='') {



						$response= array(
							'code'=>$tax_category['Standard']['code'],
							'name'=>$tax_category['Standard']['name'],
							'rate'=>$tax_category['Standard']['rate'],
							'state'=>'EC no tax number' ,
							'operations'=>'<div><img  style="width:12px;position:relative:bottom:2px" src="art/icons/information.png"/><span style="font-size:90%"> '._('You are exempt from VAT with a valid tax number').'</span> <div class="buttons small"><button id="set_tax_number" style="margin:0px" onClick="show_set_tax_number_dialog()">'._('Set up tax number').'</button></div></div>'

						);

					}
					else {


						$response= array(
							'code'=>$tax_category['Standard']['code'],
							'name'=>$tax_category['Standard']['name'],
							'rate'=>$tax_category['Standard']['rate'],
							'state'=>'EC with invalid tax number',

							'operations'=>'<div>
							<img style="width:12px;position:relative;bottom:-1px" src="art/icons/error.png">
							<span style="font-size:90%;"  >'._('Invalid tax number').'</span>
							<img style="cursor:pointer;position:relative;top:4px"  onClick="check_tax_number_from_tax_info()"  id="check_tax_number" src="art/validate.png" alt="('._('Validate').')" title="'._('Validate').'">
							<br/>
							<img id="set_tax_number" style="width:14px;cursor:pointer;position:relative;top:2px" src="art/icons/edit.gif"  onClick="show_set_tax_number_dialog()" title="'._('Edit tax number').'"/>

							<span id="tax_number">'.$this->data['Order Tax Number'].'</span>
							</div>'

						);



					}

				}

				return $response;

			}
			else {


				if ( in_array($this->data['Order Ship To Country Code'],get_countries_EC_Fiscal_VAT_area())) {


					return array(
						'code'=>$tax_category['Standard']['code'],
						'name'=>$tax_category['Standard']['name'],
						'rate'=>$tax_category['Standard']['rate'],
						'state'=>'delivery to EC with no EC billing',
						'operations'=>''

					);

				}else {
					return array(
						'code'=>$tax_category['Outside']['code'],
						'name'=>$tax_category['Outside']['name'],
						'rate'=>$tax_category['Outside']['rate'],
						'state'=>'ouside EC',
						'operations'=>'<div>'._('Outside EC fiscal area').'</div>'

					);

				}

			}






			break;
		}





	}

	function get_payment_keys($status='') {

		$payments=array();

		if ($status) {
			if ($status=='Pending')
				$where=sprintf(' and `Payment Transaction Status`=%s  and `Payment Method`!="Account" ',prepare_mysql($status));

			else
				$where=sprintf(' and `Payment Transaction Status`=%s',prepare_mysql($status));
		}else {
			$where='';
		}

		$sql=sprintf("select `Payment Key` from `Payment Dimension` where `Payment Order Key`=%d %s",
			$this->id,
			$where
		);

		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$payments[$row['Payment Key']]=$row['Payment Key'];
		}
		return $payments;
	}


	function get_payment_objects($status='',$load_payment_account=false,$load_payment_service_provider=false) {

		$payments=array();


		foreach ($this->get_payment_keys($status) as $payment_key) {
			$payment=new Payment($payment_key);
			if ($load_payment_account)
				$payment->load_payment_account();
			if ($load_payment_service_provider)
				$payment->load_payment_service_provider();
			$payments[$payment_key]=$payment;
		}


		return $payments;
	}

	function get_number_payments($status='') {


		return count($this->get_payment_keys($status));
	}


	function add_basket_history($data) {

		$sql=sprintf("insert into `Order Basket History Dimension`  (
	`Date`,`Order Transaction Key`,`Site Key`,`Store Key`,`Customer Key`,`Order Key`,`Page Key`,`Product ID`,`Quantity Delta`,`Quantity`,`Net Amount Delta`,`Net Amount`,`Page Store Section Type`)
	value (%s,%s,%d,%d,%d,%d,%d,%d,
	%f,%f,%.2f,%.2f,%s
	) ",
			prepare_mysql(gmdate('Y-m-d H:i:s')),
			prepare_mysql($data['otf_key']),
			$this->data['Order Site Key'],
			$this->data['Order Store Key'],
			$this->data['Order Customer Key'],
			$this->id,
			$data['Page Key'],
			$data['Product ID'],
			$data['Quantity Delta'],
			$data['Quantity'],
			$data['Net Amount Delta'],
			$data['Net Amount'],
			prepare_mysql($data['Page Store Section Type'])



		);
		//print $sql;
		mysql_query($sql);

	}


	function get_last_basket_page() {
		$page_key=0;
		$sql=sprintf("select `Page Key` from `Order Basket History Dimension` where `Order Key`=%d and `Page Store Section Type`!='System' order by `Date` desc limit 1 ",
			$this->id
		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$page_key=$row['Page Key'];
		}
		return $page_key;
	}


	function get_items_info() {
		$items_info=array();
		$sql=sprintf("select (select `Page Key` from `Page Product Dimension` B  where B.`State`='Online' and  B.`Product ID`=OTF.`Product ID` limit 1 ) `Page Key`,(select `Page URL` from `Page Product Dimension` B left join `Page Dimension`  PA  on (PA.`Page Key`=B.`Page Key`) where B.`State`='Online' and  B.`Product ID`=OTF.`Product ID` limit 1 ) `Page URL`,`Order Last Updated Date`,`Order Date`,`Order Quantity`,`Order Transaction Gross Amount`,`Order Currency Code`,`Order Transaction Total Discount Amount`,OTF.`Product ID`,OTF.`Product Code`,`Product XHTML Short Description`,`Product Tariff Code`,(select GROUP_CONCAT(`Deal Info`) from `Order Transaction Deal Bridge` OTDB where OTDB.`Order Key`=OTF.`Order Key` and OTDB.`Order Transaction Fact Key`=OTF.`Order Transaction Fact Key`) as `Deal Info` from `Order Transaction Fact` OTF left join `Product Dimension` P on (P.`Product ID`=OTF.`Product ID`)  where `Order Key`=%d order by OTF.`Product Code` ",
			$this->id

		);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			if ($row['Page URL']!='') {
				$code=sprintf('<a href="%s">%s</a>',$row['Page URL'],$row['Product Code']);
				$code=sprintf('<a href="page.php?id=%d">%s</a>',$row['Page Key'],$row['Product Code']);
			}else {
				$code=$row['Product Code'];
			}

			if ($row['Deal Info']) {
				$deal_info='<br/><span style="font-style:italics;color:#555555;font-size:90%">'.$row['Deal Info'].($row['Order Transaction Total Discount Amount']?', <span style="font-weight:800">-'.money($row['Order Transaction Total Discount Amount'],$row['Order Currency Code']).'</span>':'').'</span>';
			}else {
				$deal_info='';
			}

			$items_info[]=array(
				'pid'=>$row['Product ID'],
				'code'=>$code,
				'code_plain'=>$row['Product Code'],
				'description'=>$row['Product XHTML Short Description'].$deal_info,
				'tariff_code'=>$row['Product Tariff Code'],
				'quantity'=>number($row['Order Quantity']),
				'gross'=>money($row['Order Transaction Gross Amount'],$row['Order Currency Code']),
				'discount'=>money($row['Order Transaction Total Discount Amount'],$row['Order Currency Code']),
				'to_charge'=>money($row['Order Transaction Gross Amount']-$row['Order Transaction Total Discount Amount'],$row['Order Currency Code']),
				'created'=>strftime("%a %e %b %Y %H:%M %Z",strtotime($row['Order Date'].' +0:00')),
				'last_updated'=>strftime("%a %e %b %Y %H:%M %Z",strtotime($row['Order Last Updated Date'].' +0:00'))

			);

		}

		return $items_info;
	}

	function get_name_for_grettings() {

		if ($this->data['Order Customer Name']=='' and $this->data['Order Customer Contact Name']=='')
			return _('Customer');
		$greeting=$this->data['Order Customer Contact Name'];
		if ($greeting and $this->data['Order Customer Name']!=$this->data['Order Customer Contact Name']) {
			$greeting.=', '.$this->data['Order Customer Name'];
		}


		return $greeting;
	}




	function update_payment_state() {
		$payments_amount=0;
		$payments_info='';
		$number_payments=0;
		$sql=sprintf("select * from `Payment Dimension` P left join `Payment Service Provider Dimension` PSPD on (P.`Payment Service Provider Key`=PSPD.`Payment Service Provider Key`) where `Payment Order Key`=%d and `Payment Transaction Status`='Completed'",$this->id);
		//print $sql;
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$number_payments++;
			$payments_amount+=$row['Payment Balance'];

			$payments_info.=sprintf('<div>%s (%s)',

				$row['Payment Service Provider Name'],
				money($row['Payment Balance'],$row['Payment Currency Code'])

			);
			if ($row['Payment Transaction ID']!='')
				$payments_info.=sprintf(', %s: %s',
					_('Reference'),
					$row['Payment Transaction ID']

				);
			$payments_info.='</div>';

		}


		$sql=sprintf("select * from `Payment Dimension` P left join `Payment Service Provider Dimension` PSPD on (P.`Payment Service Provider Key`=PSPD.`Payment Service Provider Key`) where `Payment Order Key`=%d and `Payment Transaction Status`='Pending' and P.`Payment Method`='Account'",$this->id);
		//print $sql;
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$number_payments++;
			$payments_amount+=$row['Payment Balance'];

			$payments_info.=sprintf('<div>%s (%s)',

				$row['Payment Service Provider Name'],
				money($row['Payment Balance'],$row['Payment Currency Code'])

			);
			if ($row['Payment Transaction ID']!='')
				$payments_info.=sprintf(', %s: %s',
					_('Reference'),
					$row['Payment Transaction ID']

				);
			$payments_info.='</div>';

		}


		$payments_amount=round($payments_amount,2);

		//exit($payments_amount);


		if ($payments_amount==$this->data['Order Balance Total Amount']) {
			$payment_state='Paid';
		}elseif ($payments_amount<$this->data['Order Balance Total Amount']) {
			$payment_state='Partially Paid';

		}elseif ($payments_amount>$this->data['Order Balance Total Amount']) {
			$payment_state='Paid';

		}


		if (!$number_payments) {
			$payment_state='Waiting Payment';

			$payments_info=_('Waiting payment');

		}

		$this->data['Order Current Payment State']=$payment_state;
		$this->data['Order Current XHTML Payment State']=$payments_info;

		$this->data['Order Payments Amount']=$payments_amount;
		$this->data['Order To Pay Amount']=$this->data['Order Balance Total Amount']-$payments_amount;


		$sql=sprintf("update `Order Dimension` set `Order Current Payment State`=%s ,`Order Current XHTML Payment State`=%s , `Order Payments Amount`=%.2f ,`Order To Pay Amount`=%.2f where `Order Key`=%d  "
			,prepare_mysql($this->data['Order Current Payment State'])
			,prepare_mysql($this->data['Order Current XHTML Payment State'])
			,$this->data['Order Payments Amount']
			,$this->data['Order To Pay Amount']
			,$this->id);
		mysql_query($sql);
		//  print "$sql\n";

	}

	function get_pending_payment_amount_from_account_balance() {
		$pending_amount=0;
		$sql=sprintf("select `Amount` from `Order Payment Bridge` where `Is Account Payment`='Yes' and `Order Key`=%d ",
			$this->id

		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			$pending_amount=$row['Amount'];

		}
		return $pending_amount;
	}
	function get_formated_pending_payment_amount_from_account_balance() {
		return money($this->get_pending_payment_amount_from_account_balance(),$this->data['Order Currency']);
	}


	function apply_payment_from_customer_account() {



		if ($this->data['Order Apply Auto Customer Account Payment']=='Yes') {


			$customer=new Customer($this->data['Order Customer Key']);
			$original_customer_balance=$customer->data['Customer Account Balance'];


			$sql=sprintf("select `Amount` from `Order Payment Bridge` where `Is Account Payment`='Yes' and `Order Key`=%d ",
				$this->id

			);

			$res=mysql_query($sql);
			if ($row=mysql_fetch_assoc($res)) {

				$current_amount_in_customer_account_payments=$row['Amount'];

			}else {

				$current_amount_in_customer_account_payments=0;
			}


			$customer_account_available_amount=round($current_amount_in_customer_account_payments+$original_customer_balance,2);

			if ($customer_account_available_amount) {
				$order_amount=$this->data['Order Balance Total Amount'];

				if ($customer_account_available_amount==$order_amount) {
					$payment_amount=$order_amount;

				}
				elseif ($customer_account_available_amount>$order_amount) {
					$payment_amount=$order_amount;

				}
				else {

					$payment_amount=$customer_account_available_amount;
				}


				$store=new Store($this->data['Order Store Key']);
				$payment_account_key=$store->data['Store Customer Payment Account Key'];
				$payment_account=new Payment_Account($payment_account_key);

				$payment_key=0;
				$sql=sprintf("select `Payment Key` from `Order Payment Bridge` where `Is Account Payment`='Yes' and `Order Key`=%d ",
					$this->id

				);
				$res=mysql_query($sql);
				if ($row=mysql_fetch_assoc($res)) {
					$payment_key=$row['Payment Key'];

				}



				if ($payment_key) {
					$payment=new Payment($payment_key);

					$data_to_update=array(
						'Payment Created Date'=>gmdate('Y-m-d H:i:s'),
						'Payment Last Updated Date'=>gmdate('Y-m-d H:i:s'),
						'Payment Balance'=>$payment_amount,
						'Payment Amount'=>$payment_amount

					);



					$payment->update($data_to_update);

				}
				else {
					$payment_data=array(
						'Payment Account Key'=>$payment_account->id,
						'Payment Account Code'=>$payment_account->data['Payment Account Code'],

						'Payment Service Provider Key'=>$payment_account->data['Payment Service Provider Key'],
						'Payment Order Key'=>$this->id,
						'Payment Store Key'=>$this->data['Order Store Key'],
						'Payment Site Key'=>$this->data['Order Site Key'],
						'Payment Customer Key'=>$this->data['Order Customer Key'],

						'Payment Balance'=>$payment_amount,
						'Payment Amount'=>$payment_amount,
						'Payment Refund'=>0,
						'Payment Method'=>'Account',
						'Payment Currency Code'=>$this->data['Order Currency'],
						'Payment Created Date'=>gmdate('Y-m-d H:i:s'),
						'Payment Random String'=>md5(mt_rand().date('U'))


					);

					$payment=new Payment('create',$payment_data);

					//print_r($payment);
					//exit;
				}

				$sql=sprintf("insert into `Order Payment Bridge` values (%d,%d,%d,%d,%.2f,'Yes') ON DUPLICATE KEY UPDATE `Amount`=%.2f ",
					$this->id,
					$payment->id,
					$payment_account->id,
					$payment_account->data['Payment Service Provider Key'],
					$payment->data['Payment Amount'],
					$payment->data['Payment Amount']
				);
				mysql_query($sql);
				// print $sql;
				//  exit;


				$this->update(
					array(
						'Order Payments Amount'=>$payment->data['Payment Amount']


					));


				$customer->update(
					array(
						'Customer Account Balance'=>round($customer_account_available_amount-$payment->data['Payment Amount'],2)


					));


				$this->update_payment_state();


			}



		}else {



		}

	}
	function get_date($field) {
		return strftime("%e %b %Y",strtotime($this->data[$field].' +0:00'));
	}

}


?>
