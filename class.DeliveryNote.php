<?php
/*
  File: Delivery Note.php

  This file contains the DeliveryNote Class

  Each delivery note has to be associated with a contact if no contac data is provided when the Delivery Note is created an anonimous contact will be created as well.


  About:
  Autor: Raul Perusquia <rulovico@gmail.com>

  Copyright (c) 2009,Inikoo

  Version 2.0
*/
include_once 'class.DB_Table.php';
include_once 'class.Order.php';
include_once 'class.Product.php';


class DeliveryNote extends DB_Table {

	var $update_stock=true;

	function DeliveryNote($arg1=false,$arg2=false,$arg3=false,$arg4=false) {

		$this->table_name='Delivery Note';
		$this->ignore_fields=array('Delivery Note Key');


		if (!$arg1 and !$arg2) {
			$this->error=true;
			$this->msg='No data provided';
			return;
		}
		if (is_numeric($arg1)) {
			$this->get_data('id',$arg1);
			return;
		}
		if (preg_match('/(create|new).*(replacements?|shortages?)/i',$arg1)) {
			$this->create_replacement($arg2,$arg3,$arg4);
			return;
		}
		if (preg_match('/create|new/i',$arg1)) {
			$this->create($arg2,$arg3,$arg4);
			return;
		}
		//    if(preg_match('/find/i',$arg1)){
		//  $this->find($arg2,$arg1);
		//  return;
		// }
		$this->get_data($arg1,$arg2);
	}
	/*
      Method: get_data
      Load the data from the database

      See Also:
      <find>
    */
	function get_data($tipo,$tag) {
		if ($tipo=='id')
			$sql=sprintf("select * from `Delivery Note Dimension` where  `Delivery Note Key`=%d",$tag);
		elseif ($tipo=='public_id')
			$sql=sprintf("select * from `Delivery Note Dimension` where  `Delivery Note Public ID`=%s",prepare_mysql($tag));
		else
			return;
		//   print $sql;
		$result=mysql_query($sql);
		if ($this->data=mysql_fetch_array($result,MYSQL_ASSOC)  ) {
			$this->id=$this->data['Delivery Note Key'];

		}

	}
	protected function create($dn_data,$order=false) {
		global $myconf;

		if (isset($dn_data ['Delivery Note Date']))
			$this->data ['Delivery Note Date'] = $dn_data ['Delivery Note Date'];
		else
			$this->data ['Delivery Note Date'] ='';


		if (isset($dn_data ['Delivery Note Dispatch Method']))
			$this->data ['Delivery Note Dispatch Method'] = $dn_data ['Delivery Note Dispatch Method'];
		else
			$this->data ['Delivery Note Dispatch Method'] ='Unknown';

		if (isset($dn_data ['Delivery Note Weight']))
			$this->data ['Delivery Note Weight'] = $dn_data ['Delivery Note Weight'];
		else
			$this->data ['Delivery Note Weight'] ='';

		if (isset($dn_data ['Delivery Note Order Date Placed']))
			$this->data ['Delivery Note Order Date Placed'] = $dn_data ['Delivery Note Order Date Placed'];
		else
			$this->data ['Delivery Note Order Date Placed'] ='';




		if (isset($dn_data ['Delivery Note XHTML Pickers']))
			$this->data ['Delivery Note XHTML Pickers'] = $dn_data ['Delivery Note XHTML Pickers'];
		else
			$this->data ['Delivery Note XHTML Pickers'] ='';

		if (isset($dn_data ['Delivery Note Number Pickers']))
			$this->data ['Delivery Note Number Pickers'] = $dn_data ['Delivery Note Number Pickers'];
		else
			$this->data ['Delivery Note Number Pickers'] ='';

		if (isset($dn_data ['Delivery Note Pickers IDs']))
			$this->data ['Delivery Note Pickers IDs'] = $dn_data ['Delivery Note Pickers IDs'];
		else
			$this->data ['Delivery Note Pickers IDs'] ='';

		if (isset($dn_data ['Delivery Note Warehouse Key']))
			$this->data ['Delivery Note Warehouse Key'] = $dn_data ['Delivery Note Warehouse Key'];
		else
			$this->data ['Delivery Note Warehouse Key'] =1;


		if (isset($dn_data ['Delivery Note XHTML Packers']))
			$this->data ['Delivery Note XHTML Packers'] = $dn_data ['Delivery Note XHTML Packers'];
		else
			$this->data ['Delivery Note XHTML Packers'] ='';

		if (isset($dn_data ['Delivery Note Number Packers']))
			$this->data ['Delivery Note Number Packers'] = $dn_data ['Delivery Note Number Packers'];
		else
			$this->data ['Delivery Note Number Packers'] ='';

		if (isset($dn_data ['Delivery Note Packers IDs']))
			$this->data ['Delivery Note Packers IDs'] = $dn_data ['Delivery Note Packers IDs'];
		else
			$this->data ['Delivery Note Packers IDs'] ='';

		$this->data ['Delivery Note ID'] = $dn_data ['Delivery Note ID'];
		$this->data ['Delivery Note File As'] = $dn_data ['Delivery Note File As'];

		$customer=new Customer ($dn_data['Delivery Note Customer Key']);



		$this->data ['Delivery Note Customer Key'] = $customer->id;
		$this->data ['Delivery Note Customer Name'] = $customer->data['Customer Name'];
		$this->data ['Delivery Note Store Key'] = $customer->data['Customer Store Key'];
		$store=new Store($this->data ['Delivery Note Store Key']);

		$this->data['Delivery Note Show in Warehouse Orders']=$store->data['Store Show in Warehouse Orders'];



		if (isset($dn_data ['Delivery Note Metadata'])) {
			$this->data ['Delivery Note Metadata'] = $dn_data ['Delivery Note Metadata'];
		} else if ($order) {

				$this->data ['Delivery Note Metadata'] = $order->data ['Order Original Metadata'];
			} else {
			$this->data ['Delivery Note Metadata']='';
		}

		if (isset($dn_data ['Delivery Note Date Created'])) {
			$this->data ['Delivery Note Date Created'] = $dn_data ['Delivery Note Date Created'];
		} else {
			$this->data ['Delivery Note Date Created'] =gmdate('Y-m-d H:i:s');
		}
		if (isset($dn_data ['Delivery Note State'])) {
			$this->data ['Delivery Note State'] = $dn_data ['Delivery Note State'];
		} else {
			$this->data ['Delivery Note State'] ='Ready to be Picked';
		}



		$this->data ['Delivery Note Type'] = $dn_data ['Delivery Note Type'];
		$this->data ['Delivery Note Title'] = $dn_data ['Delivery Note Title'];

		$this->data ['Delivery Note Dispatch Method'] = $dn_data ['Delivery Note Dispatch Method'];




		if ($this->data ['Delivery Note Dispatch Method']=='Collection') {

			$this->data ['Delivery Note Shipper Code']='';
			$this->data ['Delivery Note XHTML Ship To'] = _('Collected');
			$store=new Store($this->data['Delivery Note Store Key']);
			$collection_address=new Address($store->data['Store Collection Address Key']);
			if ($collection_address->id) {
				$this->data ['Delivery Note Country 2 Alpha Code'] =$collection_address->data['Address Country 2 Alpha Code'];
				$this->data ['Delivery Note Country Code']=$collection_address->data['Address Country Code'];
				$this->data ['Delivery Note World Region Code']=$collection_address->data['Address World Region'];
				$this->data ['Delivery Note Town']=$collection_address->data['Address Town'];
				$this->data ['Delivery Note Postal Code']=$collection_address->data['Address Postal Code'];

			} else {
				$this->data ['Delivery Note Country 2 Alpha Code'] ='XX';
				$this->data ['Delivery Note Country Code']='UNK';
				$this->data ['Delivery Note World Region Code']='UNKN';
				$this->data ['Delivery Note Town']='';
				$this->data ['Delivery Note Postal Code']='';
			}




			$this->data ['Delivery Note Ship To Key'] =0;


		} else {

			if (isset($dn_data ['Delivery Note Shipper Code'])) {
				$this->data ['Delivery Note Shipper Code']=$dn_data ['Delivery Note Shipper Code'];
			} else {
				$this->data ['Delivery Note Shipper Code']='';
			}



			if ($order and $order->data ['Order Ship To Key To Deliver']) {
				$ship_to=new Ship_To($order->data ['Order Ship To Key To Deliver']);
			} else {
				$ship_to=$customer->get_ship_to($this->data ['Delivery Note Date Created']);
			}

			$this->data ['Delivery Note Ship To Key'] =$ship_to->id;
			$this->data ['Delivery Note XHTML Ship To'] =$ship_to->data['Ship To XHTML Address'];
			$this->data ['Delivery Note Country 2 Alpha Code'] = ($ship_to->data['Ship To Country 2 Alpha Code']==''?'XX':$ship_to->data['Ship To Country 2 Alpha Code']);

			$this->data ['Delivery Note Country Code']=($ship_to->data['Ship To Country Code']==''?'UNK':$ship_to->data['Ship To Country Code']);
			$this->data ['Delivery Note World Region Code']=$ship_to->get('World Region Code');
			$this->data ['Delivery Note Town']=$ship_to->data['Ship To Town'];
			$this->data ['Delivery Note Postal Code']=$ship_to->data['Ship To Postal Code'];




		}



		$this->create_header();
		$this->update_xhtml_state();
		if ($order) {

			$this->update_order_transaction_after_create_dn($order);

		}


	}

	function update_order_transaction_after_create_dn($order) {

		$line_number = 0;
		$amount = 0;
		$discounts = 0;

		$total_estimated_weight=0;
		$distinct_items=0;
		$sql=sprintf('select `Order Bonus Quantity`,`Product Package Weight`,`Order Quantity`,`Order Transaction Fact Key` from `Order Transaction Fact` OTF left join `Product History Dimension` PH  on (OTF.`Product Key`=PH.`Product Key`)  left join `Product Dimension` P  on (PH.`Product ID`=P.`Product ID`)     where `Order Key`=%d  and (`Delivery Note Key` IS NULL or `Delivery Note Key`=0)',$order->id);
		//    print "$sql\n";
		//  exit;
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$estimated_weight=($row['Order Quantity']+$row['Order Bonus Quantity'])*$row['Product Package Weight'];
			$total_estimated_weight+=$estimated_weight;
			$distinct_items++;
			$sql = sprintf("update  `Order Transaction Fact` set `Estimated Weight`=%f,`Order Last Updated Date`=%s,`Delivery Note ID`=%s,`Delivery Note Key`=%d ,`Destination Country 2 Alpha Code`=%s where `Order Transaction Fact Key`=%d"
				,$estimated_weight
				,prepare_mysql ($this->data ['Delivery Note Date Created'])
				,prepare_mysql ($this->data ['Delivery Note ID'])


				,$this->data ['Delivery Note Key']
				,prepare_mysql($this->data ['Delivery Note Country 2 Alpha Code'])
				,$row['Order Transaction Fact Key']

			);
			mysql_query($sql);
		}


		$sql=sprintf('select `Order No Product Transaction Fact Key` from `Order No Product Transaction Fact` where `Order Key`=%d and (`Delivery Note Key` IS NULL  or `Delivery Note Key`=0) ',$order->id);
		$res=mysql_query($sql);
		//print "$sql\n";
		while ($row=mysql_fetch_assoc($res)) {
			$sql = sprintf("update  `Order No Product Transaction Fact` set `Delivery Note Date`=%s,`Delivery Note Key`=%d where `Order No Product Transaction Fact Key`=%d",
				prepare_mysql ($this->data ['Delivery Note Date Created']),
				$this->id,
				$row['Order No Product Transaction Fact Key']

			);
			mysql_query($sql);
			//print "$sql\n";
		}





		$this->update_item_totals();




		foreach ($this->get_orders_objects() as $order_key=>$order) {
			$sql = sprintf("insert into `Order Delivery Note Bridge` values (%d,%d)",$order_key,$this->id);
			// print "caca $sql\n";
			mysql_query($sql);

			$order->update_xhtml_delivery_notes();


		}





		$this->update_xhtml_orders();
	}


	function update_item_totals() {


		$estimated_weight=0;
		$distinct_items=0;
		$sql=sprintf('select sum((`Order Bonus Quantity`+`Order Quantity`)*`Product Package Weight`) as estimated_weight,count(*) as num from `Order Transaction Fact` OTF left join `Product History Dimension` PH  on (OTF.`Product Key`=PH.`Product Key`)  left join `Product Dimension` P  on (PH.`Product ID`=P.`Product ID`)     where `Delivery Note Key`=%d  ',$this->id);
		//    print "$sql\n";
		//  exit;
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$estimated_weight=$row['estimated_weight'];
			$distinct_items=$row['num'];

		}


		$this->data['Delivery Note Distinct Items']=$distinct_items;
		$this->data['Delivery Note Estimated Weights']=$estimated_weight;

		$sql = sprintf("update   `Delivery Note Dimension` set `Delivery Note Distinct Items`=%d,`Delivery Note Estimated Weight`=%f where `Delivery Note Key`=%d"
			,$distinct_items
			,$estimated_weight
			,$this->id);
		mysql_query($sql);
	}



	function create_header() {
		$sql = sprintf("insert into `Delivery Note Dimension` (`Delivery Note Order Date Placed`,`Delivery Note Show in Warehouse Orders`,`Delivery Note Warehouse Key`,`Delivery Note State`,`Delivery Note Date Created`,`Delivery Note Dispatch Method`,`Delivery Note Store Key`,`Delivery Note XHTML Orders`,`Delivery Note XHTML Invoices`,`Delivery Note Date`,`Delivery Note ID`,`Delivery Note File As`,`Delivery Note Customer Key`,`Delivery Note Customer Name`,`Delivery Note XHTML Ship To`,`Delivery Note Ship To Key`,`Delivery Note Metadata`,`Delivery Note Weight`,`Delivery Note XHTML Pickers`,`Delivery Note Number Pickers`,`Delivery Note XHTML Packers`,`Delivery Note Number Packers`,`Delivery Note Type`,`Delivery Note Title`,`Delivery Note Shipper Code`,
                         `Delivery Note Country 2 Alpha Code`,
                         `Delivery Note Country Code`,
                         `Delivery Note World Region Code`,
                         `Delivery Note Town`,
                         `Delivery Note Postal Code`

                        ) values (%s,%s,%s,%s,%s,%s,%s,'','',%s,%s,%s,%s,%s,%s,%s,%s,%f,%s,%d,%s,%d,%s,%s,%s,%s      ,%s,%s,%s,%s )"
			,prepare_mysql ($this->data ['Delivery Note Order Date Placed'])

			,prepare_mysql ($this->data ['Delivery Note Show in Warehouse Orders'])
			,$this->data ['Delivery Note Warehouse Key']

			,prepare_mysql ($this->data ['Delivery Note State'])

			,prepare_mysql ($this->data ['Delivery Note Date Created'])
			,prepare_mysql ($this->data ['Delivery Note Dispatch Method'])
			,prepare_mysql ($this->data ['Delivery Note Store Key'])
			,prepare_mysql ($this->data ['Delivery Note Date'])
			,prepare_mysql ($this->data ['Delivery Note ID'])
			,prepare_mysql ($this->data ['Delivery Note File As'])
			,prepare_mysql ($this->data ['Delivery Note Customer Key'])
			,prepare_mysql ($this->data ['Delivery Note Customer Name'] ,false)
			,prepare_mysql ($this->data ['Delivery Note XHTML Ship To'])
			,prepare_mysql ($this->data ['Delivery Note Ship To Key'])
			,prepare_mysql ($this->data ['Delivery Note Metadata'])
			,$this->data ['Delivery Note Weight']
			,prepare_mysql ($this->data ['Delivery Note XHTML Pickers'])
			,$this->data ['Delivery Note Number Pickers'],prepare_mysql ($this->data ['Delivery Note XHTML Packers']),$this->data ['Delivery Note Number Packers'],prepare_mysql ($this->data ['Delivery Note Type'])
			,prepare_mysql ($this->data ['Delivery Note Title'])
			,prepare_mysql ($this->data ['Delivery Note Shipper Code'])

			,prepare_mysql ($this->data ['Delivery Note Country 2 Alpha Code'])
			,prepare_mysql ($this->data ['Delivery Note Country Code'])
			,prepare_mysql ($this->data ['Delivery Note World Region Code'])
			,prepare_mysql ($this->data ['Delivery Note Town'])
			,prepare_mysql ($this->data ['Delivery Note Postal Code'])

		);

		//print $sql;
		if (mysql_query($sql)) {

			$this->data ['Delivery Note Key'] = mysql_insert_id();
			$this->id=$this->data ['Delivery Note Key'];
			$this->get_data('id',$this->id);


		} else {
			exit ("$sql \n Error can not create dn header");
		}

	}

	function get($key) {

		switch ($key) {


		case('Date'):
			return strftime("%a %e %b %Y %H:%M %Z",strtotime($this->data['Delivery Note Date'].' +0:00'));
			break;
		case('Date Created'):
			return strftime("%a %e %b %Y %H:%M %Z",strtotime($this->data['Delivery Note Date Created'].' +0:00'));
			break;
		case('Date Start Picking'):
		case('Date Finish Picking'):
		case('Date Start Packing'):
		case('Date Finish Packing'):
		case('Order Date Placed'):
			if ($this->data["Delivery Note $key"]=='')return'';
			return strftime("%a %e %b %Y %H:%M %Z",strtotime($this->data["Delivery Note $key"].' +0:00'));
			break;

		case('Estimated Weight'):
			return weight($this->data['Delivery Note Estimated Weight']);
			break;
		case('Weight'):

			if ($this->data['Delivery Note Weight Source']=='Given')
				return weight($this->data['Delivery Note Weight']);

			else
				return "&#8494;" .weight($this->data['Delivery Note Estimated Weight'],'',0);
			break;

		case('Weight For Edit'):

			if ($this->data['Delivery Note Weight Source']=='Given')
				return $this->data['Delivery Note Weight'];

			else
				return "";
			break;


		case('Consignment'):
			$consignment=$this->data['Delivery Note Shipper Consignment'];
			if ($this->data['Delivery Note Shipper Code']!='') {
				$consignment.=sprintf(' [<a href="shipper.php?code=%s">%s</a>]',
					$this->data['Delivery Note Shipper Code'],
					$this->data['Delivery Note Shipper Code']
				);
			}
			return $consignment;
			break;
		case('Items Gross Amount'):
		case('Items Discount Amount'):
		case('Items Net Amount'):
		case('Items Tax Amount'):
		case('Refund Net Amount'):
		case('Charges Net Amount'):
		case('Shipping Net Amount'):

			return money($this->data['Delivery Note '.$key]);
			break;
		case('Fraction Packed'):
		case('Fraction Picked'):
			return percentage($this->data['Delivery Note'.' '.$key],1);
		}


		if (isset($this->data[$key]))
			return $this->data[$key];

		return false;
	}
	function display($tipo='xml') {



		switch ($tipo) {

		default:
			return 'todo';

		}


	}



	function get_formated_parcels() {

		if (!is_numeric($this->data['Delivery Note Number Parcels']))
			return;

		switch ($this->data['Delivery Note Parcel Type']) {
		case('Box'):
			$parcel_type=ngettext('box','boxes',$this->data['Delivery Note Number Parcels']);
			break;
		case('Pallet'):
			$parcel_type=ngettext('pallet','pallets',$this->data['Delivery Note Number Parcels']);
			break;
		case('Envelope'):
			$parcel_type=ngettext('envelope','envelopes',$this->data['Delivery Note Number Parcels']);
			break;
		case('Small Parcel'):
			$parcel_type=ngettext('small parcel','small parcels',$this->data['Delivery Note Number Parcels']);
			break;
		case('Other'):
			$parcel_type=ngettext('container (other)','containers (other)',$this->data['Delivery Note Number Parcels']);
			break;

		case('None'):
			return;
			break;

		default:
			$parcel_type=$this->data['Delivery Note Parcel Type'];
		}


		return number($this->data['Delivery Note Number Parcels']).' '.$parcel_type;
	}

	function get_orders_ids() {
		$sql=sprintf("select `Order Key` from `Order Transaction Fact` where `Delivery Note Key`=%d group by `Order Key`",$this->id);

		$res = mysql_query($sql);
		$orders=array();
		while ($row = mysql_fetch_array($res,MYSQL_ASSOC)) {
			if ($row['Order Key']) {
				$orders[$row['Order Key']]=$row['Order Key'];
			}

		}
		return $orders;

	}
	function get_orders_objects() {
		$orders=array();
		$orders_ids=$this->get_orders_ids();
		foreach ($orders_ids as $order_id) {
			$orders[$order_id]=new Order($order_id);
		}
		return $orders;
	}
	function get_invoices_ids() {
		$invoices=array();
		$sql=sprintf("select `Invoice Key` from `Order Transaction Fact` where `Delivery Note Key`=%d group by `Invoice Key`",$this->id);

		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res,MYSQL_ASSOC)) {
			if ($row['Invoice Key']) {
				$invoices[$row['Invoice Key']]=$row['Invoice Key'];
			}
		}
		$sql=sprintf("select `Refund Key` from `Order Transaction Fact` where `Delivery Note Key`=%d group by `Refund Key`",$this->id);
		$res = mysql_query($sql);
		while ($row = mysql_fetch_array($res,MYSQL_ASSOC)) {
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

		case 'parcels_weight':

			$this->set_weight($value);
			break;
		case('Delivery Note XHTML Invoices'):
			$this->update_xhtml_invoices();
			break;
		case('Delivery Note XHTML Orders'):
			$this->update_xhtml_orders();
			break;
		default:
			$base_data=$this->base_data();
			if (array_key_exists($field,$base_data)) {
				if ($value!=$this->data[$field]) {
					$this->update_field($field,$value,$options);
				}
			}
		}
	}
	function update_xhtml_orders() {
		$prefix='';
		$this->data ['Delivery Note XHTML Orders'] ='';

		foreach ($this->get_orders_objects() as $order) {
			$this->data ['Delivery Note XHTML Orders'] .= sprintf('%s <a href="order.php?id=%d">%s</a>,',$prefix,$order->data ['Order Key'],$order->data ['Order Public ID']);
		}
		$this->data ['Delivery Note XHTML Orders'] =_trim(preg_replace('/\,$/','',$this->data ['Delivery Note XHTML Orders']));

		$sql=sprintf("update `Delivery Note Dimension` set `Delivery Note XHTML Orders`=%s where `Delivery Note Key`=%d "
			,prepare_mysql($this->data['Delivery Note XHTML Orders'])
			,$this->id
		);
		mysql_query($sql);
	}
	function update_xhtml_invoices() {



		$prefix='';
		$this->data ['Delivery Note XHTML Invoices'] ='';
		foreach ($this->get_invoices_objects() as $invoice) {

			if ($invoice->get('Invoice Paid')=='Yes')
				$state='<img src="art/icons/money.png" style="height:14px">';

			else

				$state='<img src="art/icons/money_bw.png" style="width:14px">';

			$this->data ['Delivery Note XHTML Invoices'] .= sprintf( ' %s <a href="invoice.php?id=%d">%s%s</a> <a href="invoice.pdf.php?id=%d" target="_blank"><img style="height:10px;position:relative;bottom:2.5px" src="art/pdf.gif" alt=""></a><br/>',
				$state,
				$invoice->data ['Invoice Key'],

				$prefix,
				$invoice->data ['Invoice Public ID'],
				$invoice->data ['Invoice Key'] );
		}
		$this->data ['Delivery Note XHTML Invoices'] =_trim(preg_replace('/\<br\/\>$/','',$this->data ['Delivery Note XHTML Invoices']));
		$sql=sprintf("update `Delivery Note Dimension` set `Delivery Note XHTML Invoices`=%s where `Delivery Note Key`=%d "
			,prepare_mysql($this->data['Delivery Note XHTML Invoices'])
			,$this->id
		);
		mysql_query($sql);







	}


	function create_orphan_inventory_transaction_fact($date) {
		$skus_data=array();
		$sql=sprintf('select  OTF.`Product Code`,OTF.`Product Key`,`Product Package Weight`,`Delivery Note Quantity`,`Order Transaction Fact Key` from `Order Transaction Fact` OTF left join `Product History Dimension` PH  on (OTF.`Product Key`=PH.`Product Key`)  left join `Product Dimension` P  on (PH.`Product ID`=P.`Product ID`)     where `Current Dispatching State` in ("Submitted by Customer","In Process") and `Delivery Note Key`=%d '
			,$this->id);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$product=new Product('id',$row['Product Key']);
			$part_list=$product->get_part_list($date);

			$map_to_otf_key=$row['Order Transaction Fact Key'];
			$map_to_otf_metadata='';
			$state='Ready to Pick';
			$sql = sprintf("update `Order Transaction Fact` set `Current Dispatching State`=%s where `Order Transaction Fact Key`=%d  ",
				prepare_mysql($state),

				$row['Order Transaction Fact Key']
			);
			mysql_query($sql);

			$part_index=0;
			$multipart_data=sprintf('<a href="product.php?id=%d">%s</a>',$row['Product Key'],$row['Product Code']);
			$multipart_data_multiplicity=count($part_list);
			
			foreach ($part_list as $part_data) {

				$part = new Part ('sku',$part_data['Part SKU']);

				$supplier_products=$part->get_supplier_products($date);
				$supplier_product_id=0;
				$supplier_product_key=0;
				$supplier_key=0;
				if (count($supplier_products)>0) {
					$supplier_products_rnd_key=array_rand($supplier_products,1);
					$supplier_products_keys=preg_split('/,/',$supplier_products[$supplier_products_rnd_key]['Supplier Product Keys']);
					$supplier_product_key=$supplier_products_keys[array_rand($supplier_products_keys)];
					$supplier_key=$supplier_products[$supplier_products_rnd_key]['Supplier Key'];
					$supplier_product_id=$supplier_products[$supplier_products_rnd_key]['Supplier Product ID'];
				}

				$product = new product ($row ['Product Key']);

				$quantity_to_be_taken=$part_data['Parts Per Product'] * $row ['Delivery Note Quantity'];
				$locations=$part->get_picking_location_key($date,$quantity_to_be_taken);

				$a = sprintf('<a href="product.php?id=%d">%s</a> <a href="dn.php?id=%d">%s</a>'
					,$product->id
					,$product->code
					,$this->id
					,$this->data['Delivery Note ID']
				);


				$location_index=0;
				foreach ($locations as $location_data) {
					$location_key=$location_data['location_key'];
					$quantity_taken_from_location=$location_data['qty'];

					if ($location_key) {
						if ($location_key==1) {
							$a.=' '._('Taken from an')." ".sprintf("<a href='location.php?id=1'>%s</a>",_('Unknown Location'));
						} else {
							$location = new Location($location_key);
							$a.=' '._('Taken from').": ".sprintf("<a href='location.php?id=%d'>%s</a>",$location->id,$location->data['Location Code']);
						}

					}


					$note = $a;



					$sql = sprintf("insert into `Inventory Transaction Fact`  (`Map To Order Transaction Fact Parts Multiplicity`,`Map To Order Transaction Fact XHTML Info`,`Inventory Transaction Record Type`,`Inventory Transaction Section`,`Dispatch Country Code`,`Picking Note`,`Inventory Transaction Weight`,`Date Created`,`Date`,`Delivery Note Key`,`Part SKU`,`Location Key`,`Inventory Transaction Quantity`,`Inventory Transaction Type`,`Inventory Transaction Amount`,`Required`,`Given`,`Amount In`,`Metadata`,`Note`,`Supplier Product ID`,`Supplier Product Historic Key`,`Supplier Key`,`Map To Order Transaction Fact Key`,`Map To Order Transaction Fact Metadata`) values
					(%d,%s,%s,%s,%s,%s,%f,%s,%s,%d,%s,%d,%s,%s,%.2f,%f,%f,%f,%s,%s,%d,%d,%d,%d,%s) ",
						$multipart_data_multiplicity,
						prepare_mysql ($multipart_data),
						"'Movement'",
						"'OIP'",
						prepare_mysql ($this->data['Delivery Note Country Code']),
						prepare_mysql ($part_data['Product Part List Note']),
						0,
						prepare_mysql ($date),
						prepare_mysql ($date),
						$this->id,
						prepare_mysql ($part_data['Part SKU']),
						$location_key,
						0,
						"'Order In Process'",
						0,
						$quantity_taken_from_location,
						0,
						0,
						prepare_mysql ($this->data ['Delivery Note Metadata']),
						prepare_mysql ($note),
						$supplier_product_id,
						$supplier_product_key,
						$supplier_key,
						$map_to_otf_key,
						prepare_mysql (prepare_mysql($part_index.';'.$part_data['Parts Per Product'].';'.$location_index))
					);
					mysql_query($sql);
					//print "$sql\n\n\n";
					$location_index++;

					$part_location=new PartLocation($part_data['Part SKU'].'_'.$location_key);
					$part_location->update_stock();
				}
				$part_index++;
				$part->update_stock();
			}
		}
	}


	function create_post_order_inventory_transaction_fact($order_key,$date) {



		$skus_data=array();

		$sql=sprintf('select OTF.`Product Code`,`Order Post Transaction Key`,OTF.`Product Key`,`Delivery Note Quantity`,OTF.`Order Transaction Fact Key` from  `Order Transaction Fact` OTF  left join `Order Post Transaction Dimension` POT on (POT.`Order Post Transaction Fact Key`=OTF.`Order Transaction Fact Key`)   where OTF.`Order Key`=%d  and `Order Transaction Type`="Resend" and `Current Dispatching State` in ("In Process")  '
			,$order_key);
		$res=mysql_query($sql);
		//print $sql;
		while ($row=mysql_fetch_assoc($res)) {

			$product=new Product('id',$row['Product Key']);
			$part_list=$product->get_part_list($date);

			$map_to_otf_key=$row['Order Transaction Fact Key'];
			$map_to_otf_metadata='';
			$state='In Warehouse';
			$sql = sprintf("update `Order Post Transaction Dimension` set `State`=%s where `Order Post Transaction Key`=%d  ",
				prepare_mysql($state),
				$row['Order Post Transaction Key']
			);
			mysql_query($sql);

			$state='Ready to Pick';
			$sql = sprintf("update `Order Transaction Fact` set `Current Dispatching State`=%s where `Order Transaction Fact Key`=%d  ",
				prepare_mysql($state),

				$row['Order Transaction Fact Key']
			);
			mysql_query($sql);

			$part_index=0;
			
			$multipart_data=sprintf('<a href="product.php?id=%d">%s</a>',$row['Product Key'],$row['Product Code']);
			$multipart_data_multiplicity=count($part_list);
			
			
			foreach ($part_list as $part_data) {
				if ($part_data['Parts Per Product']!=1)
					$map_to_otf_metadata=$part_data['Parts Per Product'];
				$part = new Part ('sku',$part_data['Part SKU']);

				$supplier_products=$part->get_supplier_products($date);
				$supplier_product_id=0;
				$supplier_product_key=0;
				$supplier_key=0;
				if (count($supplier_products)>0) {
					$supplier_products_rnd_key=array_rand($supplier_products,1);
					$supplier_products_keys=preg_split('/,/',$supplier_products[$supplier_products_rnd_key]['Supplier Product Keys']);
					$supplier_product_key=$supplier_products_keys[array_rand($supplier_products_keys)];
					$supplier_key=$supplier_products[$supplier_products_rnd_key]['Supplier Key'];
					$supplier_product_id=$supplier_products[$supplier_products_rnd_key]['Supplier Product ID'];
				}


				$product = new product ($row ['Product Key']);
				$a = sprintf('<a href="product.php?id=%d">%s</a> <a href="dn.php?id=%d">%s</a>'
					,$product->id
					,$product->code
					,$this->id
					,$this->data['Delivery Note ID']
				);


				$quantity_to_be_taken=$part_data['Parts Per Product'] * $row ['Delivery Note Quantity'];
				$locations=$part->get_picking_location_key($date,$quantity_to_be_taken);
				$location_index=0;
				foreach ($locations as $location_data) {
					$location_key=$location_data['location_key'];
					$quantity_taken_from_location=$location_data['qty'];

					if ($location_key) {
						if ($location_key==1) {
							$a.=' '._('Taken from an')." ".sprintf("<a href='location.php?id=1'>%s</a>",_('Unknown Location'));
						} else {
							$location = new Location($location_key);
							$a.=' '._('Taken from').": ".sprintf("<a href='location.php?id=%d'>%s</a>",$location->id,$location->data['Location Code']);
						}

					}


					$note = $a;

					$sql = sprintf("insert into `Inventory Transaction Fact`  (`Map To Order Transaction Fact Parts Multiplicity`,`Map To Order Transaction Fact XHTML Info`,`Inventory Transaction Record Type`,`Inventory Transaction Section`,`Dispatch Country Code`,`Inventory Transaction Weight`,`Date Created`,`Date`,`Delivery Note Key`,`Part SKU`,`Location Key`,`Inventory Transaction Quantity`,`Inventory Transaction Type`,`Inventory Transaction Amount`,`Required`,`Given`,`Amount In`,`Metadata`,`Note`,`Supplier Product ID`,`Supplier Product Historic Key`,`Supplier Key`,`Map To Order Transaction Fact Key`,`Map To Order Transaction Fact Metadata`) values
					(%d,%s,%s,%s,%s,%f,%s,%s,%d,%s,%d,%s,%s,%.2f,%f,%f,%f,%s,%s,%d,%d,%d,%d,%s) ",
						$multipart_data_multiplicity,
						prepare_mysql ($multipart_data),
						"'Movement'",
						"'OIP'",
						prepare_mysql ($this->data['Delivery Note Country Code']),
						// prepare_mysql ($part_data['Product Part List Note']),
						0,
						prepare_mysql ($date),
						prepare_mysql ($date),
						$this->id,
						prepare_mysql ($part_data['Part SKU']),
						$location_key,
						0,
						"'Order In Process'",
						0,
						$quantity_taken_from_location,
						0,
						0,
						prepare_mysql ($this->data ['Delivery Note Metadata']),
						prepare_mysql ($note),
						$supplier_product_id,
						$supplier_product_key,
						$supplier_key,
						$map_to_otf_key,
						prepare_mysql($part_index.';'.$part_data['Parts Per Product'].';'.$location_index)
					);
					//print $sql;
					//print "$sql\n\n\n";
					mysql_query($sql);
					$location_index++;
					$part_location=new PartLocation($part_data['Part SKU'].'_'.$location_key);
					$part_location->update_stock();
				}
				$part_index++;

			}
		}

	}






	function actualize_inventory_transaction_facts($actualization_date=false) {

		if ($actualization_date)
			$date=$actualization_date;
		else
			$date=gmdate("Y-m-d H:i:s");

		$last_used_index=0;
		$sql=sprintf("select * from `Inventory Transaction Fact` where `Delivery Note Key`=%d  ",$this->id);
		$res=mysql_query($sql);
		$inventory_to_actualize=array();
		// print $sql;
		while ($row=mysql_fetch_assoc($res)) {
			//print_r($row);

			//  $todo=$row['Required']-$row['Picked']-$row['Out of Stock']-$row['Not Found']-$row['No Picked Other'];


			$to_pick=$row['Required']-$row['Picked']-$row['Out of Stock']-$row['Not Found']-$row['No Picked Other'];
			$metadata=preg_split('/;/',$row['Map To Order Transaction Fact Metadata']);
			//print "xxx $to_pick   xxx<br>";



			if ($to_pick==0) {
				continue;
			}else if ($row['Picked']>0) {
					$sql=sprintf("update `Inventory Transaction Fact`  set `Required`=%d  where `Inventory Transaction Key`=%d ",
						$row['Required']-$to_pick,
						$row['Inventory Transaction Key']);
					mysql_query($sql);
				} else {
				$sql=sprintf("delete from `Inventory Transaction Fact`  where `Inventory Transaction Key`=%d ",$row['Inventory Transaction Key']);
				mysql_query($sql);

				//$part_location=new PartLocation($row['Part SKU'].'_'.$row['Location Key']);
				//$part_location->update_stock();

			}
			//print "$sql\n";


			$part_index=$metadata[0];
			$parts_per_product=$metadata[1];
			$location_index=$metadata[2];
			if ($to_pick>0) {

				$sql=sprintf("select `Product Key` from `Order Transaction Fact` where `Order Transaction Fact Key`=%d ",$row['Map To Order Transaction Fact Key']);
				$res2=mysql_query($sql);
				$product_key=0;
				if ($row2=mysql_fetch_assoc($res2)) {
					$product_key=$row2['Product Key'];
				}

				$transaction_data=array(
					'itf'=>$row['Inventory Transaction Key'],
					'qty'=>$to_pick,

					'sku'=>$row['Part SKU'],
					'part_index'=>$part_index,
					'location_index'=>$location_index,
					'otf'=>$row['Map To Order Transaction Fact Key'],
					'product_key'=>$product_key,
					'picking_note'=>$row['Picking Note'],
					'parts_per_product'=>$parts_per_product,
					'parts_multiplicity'=>$row['Map To Order Transaction Fact Parts Multiplicity'],
					'otf_info'=>$row['Map To Order Transaction Fact XHTML Info']
					);
					
					
				$inventory_to_actualize[$row['Map To Order Transaction Fact Key']][$row['Part SKU']]=$transaction_data;


			}


		}


		//    print_r($inventory_to_actualize);

		foreach ($inventory_to_actualize as $otf=>$transactions_parts) {

			foreach ($transactions_parts as $part_sku=>$transaction_locations) {
				$number_locations=count($transaction_locations);
				$part=new Part($part_sku);



				$locations=$part->get_picking_location_key($actualization_date,$transaction_locations['qty']);
				// continue;

				$product=new Product($transaction_locations['product_key']);
				$picking_note=$transaction_locations['picking_note'];
				$map_to_otf_key=$transaction_locations['otf'];
				$parts_per_product=$transaction_locations['parts_per_product'];


				$supplier_products=$part->get_supplier_products($date);
				$supplier_product_id=0;
				$supplier_product_key=0;
				$supplier_key=0;
				if (count($supplier_products)>0) {
					$supplier_products_rnd_key=array_rand($supplier_products,1);
					$supplier_products_keys=preg_split('/,/',$supplier_products[$supplier_products_rnd_key]['Supplier Product Keys']);
					$supplier_product_key=$supplier_products_keys[array_rand($supplier_products_keys)];
					$supplier_key=$supplier_products[$supplier_products_rnd_key]['Supplier Key'];
					$supplier_product_id=$supplier_products[$supplier_products_rnd_key]['Supplier Product ID'];
				}



				$a = sprintf('<a href="product.php?id=%d">%s</a> <a href="dn.php?id=%d">%s</a>'
					,$product->id
					,$product->code
					,$this->id
					,$this->data['Delivery Note ID']
				);


				$location_index=0;
				//   print_r($locations);
				foreach ($locations as $location_data) {


					$location_key=$location_data['location_key'];
					$quantity_taken_from_location=$location_data['qty'];

					if ($location_key) {
						if ($location_key==1) {
							$a.=' '._('To be taken from an')." ".sprintf("<a href='location.php?id=1'>%s</a>",_('Unknown Location'));
						} else {
							$location = new Location($location_key);
							$a.=' '._('To be taken from').": ".sprintf("<a href='location.php?id=%d'>%s</a>",$location->id,$location->data['Location Code']);
						}

					}


					$note = $a;



					$sql = sprintf("insert into `Inventory Transaction Fact`  (`Map To Order Transaction Fact Parts Multiplicity`,`Map To Order Transaction Fact XHTML Info`,`Inventory Transaction Record Type`,`Inventory Transaction Section`,`Dispatch Country Code`,`Picking Note`,`Inventory Transaction Weight`,`Date Created`,`Date`,`Delivery Note Key`,`Part SKU`,
                                     `Location Key`,`Inventory Transaction Quantity`,`Inventory Transaction Type`,`Inventory Transaction Amount`,
                                     `Required`,`Given`,`Amount In`,
                                     `Metadata`,`Note`,`Supplier Product ID`,`Supplier Product Historic Key`,`Supplier Key`,`Map To Order Transaction Fact Key`,`Map To Order Transaction Fact Metadata`) values
                                     (%d,%s,%s,%s,%s,%s,%f,%s,%s,%d,%s,%d,%s,%s,%.2f,%f,%f,%f,%s,%s,%d,%d,%d,%d,%s) ",
						
						$transaction_locations['parts_multiplicity'],
						prepare_mysql ($transaction_locations['otf_info']),
						"'Movement'",
						"'OIP'",
						prepare_mysql ($this->data['Delivery Note Country Code']),
						prepare_mysql ($picking_note),
						0,
						prepare_mysql ($date),
						prepare_mysql ($date),
						$this->id,
						prepare_mysql ($part->sku),
						$location_key,
						0,
						"'Order In Process'",
						0,
						$quantity_taken_from_location,
						0,
						0,
						prepare_mysql ($this->data ['Delivery Note Metadata']),
						prepare_mysql ($note),
						$supplier_product_id,
						$supplier_product_key,
						$supplier_key,
						$map_to_otf_key,
						prepare_mysql($part_index.';'.$parts_per_product.';'.$location_index)
					);
					mysql_query($sql);
					//print "a $sql\n\n\n";
					//$part_location=new PartLocation($part->sku.'_'.$location_key);
					//$part_location->update_stock();

					//        print "$sql\n";
					$location_index++;
				}













			}

		}


	}

	function update_inventory_transaction_fact($otf_key,$quantity,$date=false) {

		if (!$date)$date=gmdate("Y-m-d H:i:s");

		$sql=sprintf("select `Inventory Transaction Key` from `Inventory Transaction Fact` where `Delivery Note Key`=%d and `Map To Order Transaction Fact Key`=%d ",
			$this->id,
			$otf_key
		);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {




			$sql=sprintf("update `Inventory Transaction Fact` set `Required`=%f where `Inventory Transaction Key`=%d ",
				$quantity,
				$row['Inventory Transaction Key']

			);

			mysql_query($sql);

		}else {


			$sql=sprintf('select OTF.`Product Key`,`Product Package Weight`,`Order Bonus Quantity`,`Order Quantity`,`Supplier Metadata`,`Order Bonus Quantity`,`Order Transaction Fact Key`,`Current Autorized to Sell Quantity` from `Order Transaction Fact` OTF left join `Product History Dimension` PH  on (OTF.`Product Key`=PH.`Product Key`)  left join `Product Dimension` P  on (PH.`Product ID`=P.`Product ID`)     where `Order Transaction Fact Key`=%d '
				,$otf_key);
			$res=mysql_query($sql);

			while ($row=mysql_fetch_assoc($res)) {



				//print_r($row);

				//print "  xx ".$row['Current Autorized to Sell Quantity']+$row['Order Bonus Quantity']." xxx";

				$this->create_inventory_transaction_fact_item(
					$row['Product Key'],
					$row['Order Transaction Fact Key'],
					$row['Current Autorized to Sell Quantity']+$row['Order Bonus Quantity'],
					$date,
					$row['Supplier Metadata'],
					$row['Order Bonus Quantity']
				);

			}

		}


	}


	function create_inventory_transaction_fact_item($product_key,$map_to_otf_key,$to_sell_quantity,$date,$supplier_metadata_array,$bonus_qty) {






		//$date=$this->data['Delivery Note Date Created'];

		$skus_data=array();



		$product=new Product('id',$product_key);


		//  print $product->data['Product Code']." ".$product->data['Product ID']." $date $map_to_otf_key $to_sell_quantity\n";

		$part_list=$product->get_part_list($date);
		//print_r($part_list);


		if (count($part_list)==0) {
			//print "xxx $product_key,$map_to_otf_key,  -> $to_sell_quantity,$supplier_metadata_array xxx\n";
			//print $product->data['Product Code']." ".$product->data['Product ID']." $date $map_to_otf_key $to_sell_quantity\n";
			//print_r($part_list);

		}

		$state='Ready to Pick';
		$sql = sprintf("update `Order Transaction Fact` set `Current Dispatching State`=%s where `Order Transaction Fact Key`=%d  ",
			prepare_mysql($state),

			$map_to_otf_key
		);
		mysql_query($sql);

		$part_index=0;


			$multipart_data=sprintf('<a href="product.php?id=%d">%s</a>',$product->id,$product->data['Product Code']);
			$multipart_data_multiplicity=count($part_list);

		foreach ($part_list as $part_data) {


			$part = new Part ('sku',$part_data['Part SKU']);

			if ($part->sku) {

				$quantity_to_be_taken=$part_data['Parts Per Product'] * $to_sell_quantity;

				$locations=$part->get_picking_location_key($date,$quantity_to_be_taken);

				$supplier_product_id=0;
				$supplier_product_key=0;
				$supplier_key=0;

				if ($supplier_metadata_array!='') {
					$supplier_metadata=unserialize($supplier_metadata_array);
					if (!is_array($supplier_metadata))
						$supplier_metadata=array();
				} else {
					$supplier_metadata=array();

				}


				//print "P ".$product->pid."  art:".$part_data['Part SKU']."  p:".$part->sku." \n";

				if (array_key_exists($part->sku,$supplier_metadata)  and $supplier_metadata[$part->sku]) {
					//print "xxx\n";
					//print_r($supplier_metadata[$part->sku]);
					//print "-xxx\n";

					$supplier_product_id=$supplier_metadata[$part->sku]['supplier_product_pid'];
					$supplier_product_key=$supplier_metadata[$part->sku]['supplier_product_key'];
					$supplier_key=$supplier_metadata[$part->sku]['supplier_key'];

				}
				else {

					$supplier_products=$part->get_supplier_products($date);

					if (count($supplier_products)>0) {
						$supplier_products_rnd_key=array_rand($supplier_products,1);
						$supplier_products_keys=preg_split('/,/',$supplier_products[$supplier_products_rnd_key]['Supplier Product Keys']);
						$supplier_product_key=$supplier_products_keys[array_rand($supplier_products_keys)];
						$supplier_key=$supplier_products[$supplier_products_rnd_key]['Supplier Key'];
						$supplier_product_id=$supplier_products[$supplier_products_rnd_key]['Supplier Product ID'];
					}


				}


				$a = sprintf('<a href="product.php?id=%d">%s</a> <a href="dn.php?id=%d">%s</a>'
					,$product->id
					,$product->code
					,$this->id
					,$this->data['Delivery Note ID']
				);


				$location_index=0;

				if ($part->unknown_location_associated) {
					$part->associate_unknown_location_historic($date);
				}

				//print_r($locations);

				foreach ($locations as $location_data) {


					$location_key=$location_data['location_key'];
					$quantity_taken_from_location=$location_data['qty'];

					if ($bonus_qty>0) {
						if ($bonus_qty>=$quantity_taken_from_location) {
							$given=$quantity_taken_from_location;
							$required=0;
							$bonus_qty=$quantity_taken_from_location-$bonus_qty;
						}else {
							$given=$bonus_qty;
							$required=$quantity_taken_from_location-$given;
							$bonus_qty=0;
						}



					}else {
						$given=0;
						$required=$quantity_taken_from_location;

					}




					if ($location_key) {
						if ($location_key==1) {
							$a.=' '._('Taken from an')." ".sprintf("<a href='location.php?id=1'>%s</a>",_('Unknown Location'));
						} else {
							$location = new Location($location_key);
							$a.=' '._('Taken from').": ".sprintf("<a href='location.php?id=%d'>%s</a>",$location->id,$location->data['Location Code']);
						}

					}


					$note = $a;

					$picking_note=$part_data['Product Part List Note'];
					
					$sql = sprintf("insert into `Inventory Transaction Fact`  (`Map To Order Transaction Fact Parts Multiplicity`,`Map To Order Transaction Fact XHTML Info`,`Inventory Transaction Record Type`,`Inventory Transaction Section`,`Dispatch Country Code`,`Picking Note`,`Inventory Transaction Weight`,`Date Created`,`Date`,`Delivery Note Key`,`Part SKU`,`Location Key`,`Inventory Transaction Quantity`,`Inventory Transaction Type`,`Inventory Transaction Amount`,`Required`,`Given`,`Amount In`,`Metadata`,`Note`,`Supplier Product ID`,`Supplier Product Historic Key`,`Supplier Key`,`Map To Order Transaction Fact Key`,`Map To Order Transaction Fact Metadata`) 
					values (%d,%s,%s,%s,%s,%s,%f,%s,%s,%d,%s,%d,%s,%s,%.2f,%f,%f,%f,%s,%s,%d,%d,%d,%d,%s) ",
						$multipart_data_multiplicity,
						prepare_mysql ($multipart_data),
						
						"'Movement'","'OIP'",
						prepare_mysql ($this->data['Delivery Note Country Code']),
						prepare_mysql ($picking_note),
						0,
						prepare_mysql ($date),
						prepare_mysql ($date),
						$this->id,
						prepare_mysql ($part_data['Part SKU']),
						$location_key,
						0,
						"'Order In Process'",
						0,
						$required,
						$given,
						0,
						prepare_mysql ($this->data ['Delivery Note Metadata']),
						prepare_mysql ($note),
						$supplier_product_id,
						$supplier_product_key,
						$supplier_key,
						$map_to_otf_key,
						prepare_mysql($part_index.';'.$part_data['Parts Per Product'].';'.$location_index)
					);
					mysql_query($sql);
					//print "$sql\n";
					//exit;






					if ($this->update_stock) {


						$part_location=new PartLocation($part_data['Part SKU'].'_'.$location_key);
						$part_location->update_stock();
					}
					//print "$sql\n";
					$location_index++;
				}



				$part_index++;

			}

			if ($part_index==0) {

				exit ("\nWarning no part in product ".$product->pid." on $date\n");

			}


		}


	}


	function create_inventory_transaction_fact($order_key,$date=false,$extra_data=false) {


		if (!$extra_data)$extra_data=array();

		if (!$date)$date=gmdate("Y-m-d H:i:s");

		$sql=sprintf('select OTF.`Product Key`,`Product Package Weight`,`Order Quantity`,`Supplier Metadata`,`Order Bonus Quantity`,`Order Transaction Fact Key`,`Current Autorized to Sell Quantity` from `Order Transaction Fact` OTF left join `Product History Dimension` PH  on (OTF.`Product Key`=PH.`Product Key`)  left join `Product Dimension` P  on (PH.`Product ID`=P.`Product ID`)     where `Order Key`=%d  and `Current Dispatching State` in ("Submitted by Customer","In Process")  '
			,$order_key);
		$res=mysql_query($sql);

		while ($row=mysql_fetch_assoc($res)) {

			//print_r($row);

			$this->create_inventory_transaction_fact_item(
				$row['Product Key'],
				$row['Order Transaction Fact Key'],
				$row['Current Autorized to Sell Quantity']+$row['Order Bonus Quantity'],
				$date,
				$row['Supplier Metadata'],
				$row['Order Bonus Quantity']
			);

		}
	}



	function undo_dispatch() {



		$sql=sprintf("delete from  `Inventory Transaction Fact` where `Inventory Transaction Record Type`=%s and `Inventory Transaction Section`=%s and `Inventory Transaction Type`=%s and `Delivery Note Key`=%d",
			"'Movement'",
			"'Audit'",
			"'Adjust'",
			$this->id

		);
		mysql_query($sql);

		$sql=sprintf("update `Inventory Transaction Fact` set `Inventory Transaction Type` = 'Order In Process',`Inventory Transaction Section`='OIP' where `Delivery Note Key`=%d and `Inventory Transaction Type` = 'No Dispatched' and `Inventory Transaction Section`='NoDispatched'  ",

			$this->id

		);
		mysql_query($sql);


		$sql=sprintf("update `Inventory Transaction Fact` set `Inventory Transaction Type` = 'Order In Process',`Inventory Transaction Section`='OIP' where `Delivery Note Key`=%d and `Inventory Transaction Type` = 'Sale' and `Inventory Transaction Section`='Out'  ",

			$this->id

		);
		mysql_query($sql);


		$sql=sprintf("select * from `Inventory Transaction Fact` where `Delivery Note Key`=%d",$this->id);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {




			if ($this->update_stock) {
				$part_location=new PartLocation($row['Part SKU'].'_'.$row['Location Key']);
				$part_location->update_stock();
			}

		}




		$sql=sprintf("select `Delivery Note Quantity`,`Order Transaction Fact Key` from `Order Transaction Fact` where `Delivery Note Key`=%s  and `Current Dispatching State`='Dispatched'  ",
			$this->id);

		$result=mysql_query($sql);
		$_data=array();
		while ($row=mysql_fetch_array($result,MYSQL_ASSOC)  ) {

			$sql = sprintf("update  `Order Transaction Fact` set `Actual Shipping Date`=NULL,`Shipped Quantity`=0, `Current Dispatching State`=%s where   `Order Transaction Fact Key`=%d",
				prepare_mysql('Ready to Ship'),
				$row['Order Transaction Fact Key']
			);
			mysql_query($sql);
			//print "$sql\n";
		}


		$this->data['Delivery Note State']='Packed Done';
		$this->data['Delivery Note Date']=$this->data['Delivery Note Date Dispatched Approved'];

		$sql=sprintf("update `Delivery Note Dimension` set `Delivery Note State`=%s,`Delivery Note Date`=%s where `Delivery Note Key`=%d",
			prepare_mysql($this->data['Delivery Note State']),
			prepare_mysql($this->data['Delivery Note Date']),
			$this->id
		);
		mysql_query($sql);

		$this->update_xhtml_state();
		foreach ($this->get_orders_objects() as $key=>$order) {



			if (in_array($this->data['Delivery Note Type'],array('Replacement & Shortages','Replacement','Shortages'))) {
				$order->update_post_dispatch_state();

			}else {
				$order->update_dispatch_state(true);;

			}

			$order->update_xhtml_delivery_notes();

		}

		$store=new store($this->data['Delivery Note Store Key']);
		$store->update_orders();

	}

	private function handle_to_customer($data) {



		if (!array_key_exists('Delivery Note Date',$data) or !$data['Delivery Note Date'] ) {
			$data['Delivery Note Date']=    gmdate('Y-m-d H:i:s');
		}


		$sql=sprintf("select * from `Inventory Transaction Fact` where `Delivery Note Key`=%d",$this->id);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {

			if ($row['Out of Stock']>0) {


				$note=_('Out of Stock');
				$sql = sprintf("insert into `Inventory Transaction Fact`  (`Inventory Transaction Record Type`,`Inventory Transaction Section`,`Dispatch Country Code`,`Inventory Transaction Weight`,`Date Created`,`Date`,`Delivery Note Key`,`Part SKU`,`Location Key`,`Inventory Transaction Quantity`,`Inventory Transaction Type`,`Inventory Transaction Amount`,`Required`,`Given`,`Amount In`,`Metadata`,`Note`,`Supplier Product ID`) values (%s,%s,%s,%f,%s,%s,%d,%s,%d,%s,%s,%.2f,%f,%f,%f,%s,%s,%s) ",
					"'Movement'",
					"'Audit'",
					prepare_mysql ($this->data['Delivery Note Country Code']),
					0,
					prepare_mysql ($this->data['Delivery Note Date Finish Picking']),
					prepare_mysql ($this->data['Delivery Note Date Finish Picking']),
					$this->id,
					$row['Part SKU'],
					$row['Location Key'],
					0,
					"'Adjust'",
					0,
					0,
					0,
					0,
					prepare_mysql ($row['Metadata']),
					prepare_mysql ($note),
					$row['Supplier Product ID']

				);
				mysql_query($sql);


			}


			if ($row['Inventory Transaction Quantity']==0) {
				$sql=sprintf("update `Inventory Transaction Fact` set `Inventory Transaction Type` = 'No Dispatched',`Inventory Transaction Section`='NoDispatched' where `Delivery Note Key`=%d  and `Inventory Transaction Key`=%d  ",
					$this->id,
					$row['Inventory Transaction Key']

				);

				mysql_query($sql);

			}
			else {

				$sql=sprintf("update `Inventory Transaction Fact` set `Date Shipped`=%s,`Inventory Transaction Type` = 'Sale',`Inventory Transaction Section`='Out' where `Delivery Note Key`=%d  and `Inventory Transaction Key`=%d  ",
					prepare_mysql($data['Delivery Note Date']),

					$this->id,
					$row['Inventory Transaction Key']

				);
				//print "$sql\n";
				mysql_query($sql);
			}
			if ($this->update_stock) {
				$part_location=new PartLocation($row['Part SKU'].'_'.$row['Location Key']);
				$part_location->update_stock();
			}

		}




		$sql=sprintf("select `Delivery Note Quantity`,`Order Transaction Fact Key` from `Order Transaction Fact` where `Delivery Note Key`=%s  and `Current Dispatching State`='Ready to Ship'  ",
			$this->id);

		$result=mysql_query($sql);
		$_data=array();
		while ($row=mysql_fetch_array($result,MYSQL_ASSOC)  ) {

			$sql = sprintf("update  `Order Transaction Fact` set `Actual Shipping Date`=%s,`Shipped Quantity`=%f, `Current Dispatching State`=%s where   `Order Transaction Fact Key`=%d",
				prepare_mysql($data['Delivery Note Date']),
				$row['Delivery Note Quantity'],
				prepare_mysql('Dispatched'),
				$row['Order Transaction Fact Key']
			);
			mysql_query($sql);
			//print "$sql\n";
		}


		$this->data['Delivery Note State']='Dispatched';
		$this->data['Delivery Note Date']=$data['Delivery Note Date'];

		$sql=sprintf("update `Delivery Note Dimension` set `Delivery Note State`=%s,`Delivery Note Date`=%s where `Delivery Note Key`=%d",
			prepare_mysql($this->data['Delivery Note State']),
			prepare_mysql($this->data['Delivery Note Date']),
			$this->id
		);
		mysql_query($sql);

		$this->update_xhtml_state();
		foreach ($this->get_orders_objects() as $key=>$order) {



			if (in_array($this->data['Delivery Note Type'],array('Replacement & Shortages','Replacement','Shortages'))) {
				$order->update_post_dispatch_state();

			}else {
				$order->set_order_as_dispatched($data['Delivery Note Date']);
			}

			$order->update_xhtml_delivery_notes();

		}

		$store=new store($this->data['Delivery Note Store Key']);
		$store->update_orders();

	}



	function dispatch($data) {


		$this->handle_to_customer($data);

		$customer=new Customer($this->data['Delivery Note Customer Key']);
		$numbers_of_times_used=0;
		$sql=sprintf('select count(*) as num from `Delivery Note Dimension` where `Delivery Note Customer Key`=%d and `Delivery Note Ship To Key`=%d',$customer->id,$this->data['Delivery Note Ship To Key']);
		$res2=mysql_query($sql);
		if ($row2=mysql_fetch_assoc($res2)) {
			$numbers_of_times_used=$row2['num'];
		}


		$sql=sprintf('select * from `Customer Ship To Bridge` where `Customer Key`=%d and `Ship To Key`=%d',
			$customer->id,
			$this->data['Delivery Note Ship To Key']);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {


			$from_date=$row['Ship To From Date'];
			$to_date=$row['Ship To Last Used'];
		}

		if (strtotime($this->data['Delivery Note Date'])< strtotime($from_date))
			$from_date=$this->data['Delivery Note Date'];
		if (strtotime($this->data['Delivery Note Date'])> strtotime($to_date))
			$to_date=$this->data['Delivery Note Date'];

		$sql=sprintf('update `Customer Ship To Bridge` set `Times Used`=%d ,`Ship To From Date`=%s,`Ship To Last Used`=%s  where `Customer Key`=%d and `Ship To Key`=%d',
			$numbers_of_times_used,
			prepare_mysql($from_date),
			prepare_mysql($to_date),

			$customer->id,
			$this->data['Delivery Note Ship To Key']
		);
		mysql_query($sql);

		$customer->update_last_ship_to_key();
		$customer->update_ship_to_stats();


		foreach ($this->get_invoices_objects() as $invoice) {
			$invoice->update_delivery_note_data(
				array(
					'Invoice Delivery Country 2 Alpha Code'=>$this->data['Delivery Note Country 2 Alpha Code'],
					'Invoice Delivery Country Code'=>$this->data['Delivery Note Country Code'],
					'Invoice Delivery World Region Code'=>$this->data['Delivery Note World Region Code'],
					'Invoice Delivery Town'=>$this->data['Delivery Note Town'],
					'Invoice Delivery Postal Code'=>$this->data['Delivery Note Postal Code'],
				)

			);
		}

		// print "Dispatching\n";




	}
	function set_as_collected($data) {



		$this->handle_to_customer($data);



	}

	function set_weight($weight) {

		if (is_numeric($weight) and $weight>=0) {

			$this->update_field('Delivery Note Weight Source','Given');
			$this->update_field('Delivery Note Weight',$weight);


		} else {

			$this->update_field('Delivery Note Weight Source','Estimated');
			$this->update_field('Delivery Note Weight',$this->data['Delivery Note Estimated Weight']);


		}


	}

	function set_parcels($parcels,$parcel_type='Box') {

		if (is_numeric($parcels)) {
			$sql=sprintf("update `Delivery Note Dimension` set `Delivery Note Number Parcels`=%d,`Delivery Note Parcel Type`=%s where `Delivery Note Key`=%d"
				,$parcels
				,prepare_mysql($parcel_type)
				,$this->id
			);
			$this->data['Delivery Note Number Parcels']=$parcels;
		} else {
			$sql=sprintf("update `Delivery Note Dimension` set `Delivery Note Number Parcels`=NULL,`Delivery Note Parcel Type`=%s where `Delivery Note Key`=%d"

				,prepare_mysql($parcel_type)
				,$this->id
			);
			$this->data['Delivery Note Number Parcels']='';
		}
		mysql_query($sql);
		// print $sql;
		$this->data['Delivery Note Parcel Type']=$parcel_type;


	}

	function delete() {
		$parts_to_update_stock=array();
		$sql=sprintf("select `Part SKU`,`Location Key` from  `Inventory Transaction Fact` where `Delivery Note Key`=%d  and `Inventory Transaction Type`='Order In Process'  ",
			$this->id);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$parts_to_update_stock[]=$row['Part SKU'].'_'.$row['Location Key'];
		}

		$sql=sprintf("delete from  `Inventory Transaction Fact` where `Delivery Note Key`=%d  and `Inventory Transaction Type`='Order In Process'  ",
			$this->id);
		mysql_query($sql);

		foreach ($parts_to_update_stock as $part_to_update_stock) {
			$part_location=new PartLocation($part_to_update_stock);
			$part_location->update_stock();
		}

		$orders=$this->get_orders_objects();
		$invoices=$this->get_invoices_objects();

		$store_key=$this->data['Delivery Note Store Key'];

		$sql=sprintf("delete from  `Delivery Note Dimension` where `Delivery Note Key`=%d  ",
			$this->id);
		mysql_query($sql);
		//print $sql;

		foreach ($orders as $order) {
			$order->update_xhtml_delivery_notes();
		}
		foreach ($invoices as $invoice) {
			$invoice->update_xhtml_delivery_notes();
		}

		$store=new Store($store_key);
		$store->update_orders();

	}

	function cancel($note='',$date=false,$force=false) {


		//print_r($this->data);

		$this->cancelled=false;
		if (preg_match('/Dispatched/',$this->data ['Delivery Note State'])) {
			$this->msg=_('Delivery Note can not be cancelled,because has already been dispatched');
			return;
		}
		if (preg_match('/Cancelled/',$this->data ['Delivery Note State'])) {
			$this->_('Order is already cancelled');
			return;
		} else {

			if (!$date)
				$date=gmdate('Y-m-d H:i:s');


			if (preg_match('/Ready to be Picked/',$this->data ['Delivery Note State']) or $force) {




				$this->delete();
				$this->cancelled=true;
				return;


			} else {
				$this->data ['Delivery Note State'] = 'Cancelled to Restock';

			}

			$this->data ['Delivery Note Dispatch Method'] ='NA';




			$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note State`=%s ,`Delivery Note Dispatch Method`=%s where `Delivery Note Key`=%d"
				,prepare_mysql ($this->data ['Delivery Note State'])
				,prepare_mysql ($this->data ['Delivery Note Dispatch Method'])


				,$this->id);
			if (! mysql_query($sql))
				exit ("$sql arror can not update cancel\n");

			$this->cancelled=true;
		}
	}

	function suspend($note='',$date=false) {
		$this->suspended=false;
		if (preg_match('/Dispatched/',$this->data ['Delivery Note State'])) {
			$this->msg=_('Delivery Note can not be suspended, because has already been dispatched');
			return;
		}
		elseif (preg_match('/Suspended/',$this->data ['Delivery Note State'])) {
			$this->_('Order is already suspended');
			return;
		}
		elseif (preg_match('/Cancelled/',$this->data ['Delivery Note State'])) {
			$this->_('Order is already cancelled');
			return;
		}
		else {

			if (!$date)
				$date=gmdate('Y-m-d H:i:s');
			if (preg_match('/Ready to be Picked/',$this->data ['Delivery Note State'])) {
				$this->delete();
				$this->suspended=true;
				return;
			} else {
				$this->data ['Delivery Note State'] = 'Cancelled to Restock';
			}

			$this->data ['Delivery Note Dispatch Method'] ='NA';

			$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note State`=%s ,`Delivery Note Dispatch Method`=%s where `Delivery Note Key`=%d",
				prepare_mysql ($this->data ['Delivery Note State']),
				prepare_mysql ($this->data ['Delivery Note Dispatch Method']),
				$this->id);
			mysql_query($sql);
			$this->suspended=true;

		}



	}



	function assign_picker($staff_key) {
		$this->assigned=false;

		if (!preg_match('/^(Ready to be Picked|Picker Assigned|Picking)$/',$this->data ['Delivery Note State'])) {
			$this->error=true;
			$this->msg=$this->data ['Delivery Note State'].' '._('Delivery Note can not be assigned to a picker,because has already been picked');
			return;
		}

		$staff=new Staff($staff_key);

		if (!$staff->id) {
			$this->error=true;
			$this->msg=_('Staff not found');
			return;
		}

		$this->data ['Delivery Note State']='Picker Assigned';
		$this->data ['Delivery Note Assigned Picker Key']=$staff->id;
		$this->data ['Delivery Note Assigned Picker Alias']=$staff->data['Staff Alias'];
		$this->data ['Delivery Note XHTML Pickers']=sprintf('<a href="staff.php?id=%d">%s</a>',$staff->id,ucfirst($staff->data['Staff Name']));
		$this->data ['Delivery Note Number Pickers']=1;
		$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note Number Pickers`=%d,`Delivery Note XHTML Pickers`=%s,`Delivery Note State`=%s ,`Delivery Note Assigned Picker Key`=%d ,`Delivery Note Assigned Picker Alias`=%s where `Delivery Note Key`=%d"
			,$this->data ['Delivery Note Number Pickers']
			,prepare_mysql ($this->data ['Delivery Note XHTML Pickers'])
			,prepare_mysql ($this->data ['Delivery Note State'])
			,$this->data ['Delivery Note Assigned Picker Key']
			,prepare_mysql ($this->data ['Delivery Note Assigned Picker Alias'])
			,$this->id);
		mysql_query($sql);
//print $sql;
		$this->update_state($this->get_state());

		foreach ($this->get_orders_objects() as $order) {
			$order->update_dispatch_state();
		}


		$this->assigned=true;

		$this->dn_key=$this->id;

	}
	function assign_packer($staff_key,$force=false) {
		$this->assigned=false;

		//print $this->data ['Delivery Note State'];
		if (preg_match('/^(Picked|Packer Assigned|Picking \& Packer Assigned|Picking)$/',$this->data ['Delivery Note State'])  or $force ) {

		} else if (preg_match('/^(Ready to be Picked|Picker Assigned)$/',$this->data ['Delivery Note State'])) {
				$this->error=true;
				$this->msg=$this->data ['Delivery Note State'].' '._('Delivery Note can not be assigned to a packer,because has not been picked');
				return;
			}
		elseif (preg_match('/^(Packed|Packing|Picking \& Packing)$/',$this->data ['Delivery Note State'])) {
			$this->error=true;
			$this->msg=$this->data ['Delivery Note State'].' '._('Packer has been already assigned');
			return;
		}
		else {
			$this->error=true;
			$this->msg=$this->data ['Delivery Note State'].'; '._('Delivery Note has been already packed');
			return;

		}

		$staff=new Staff($staff_key);

		if (!$staff->id) {
			$this->error=true;
			$this->msg=_('Staff not found');
			return;
		}

		//if ($this->data ['Delivery Note Assigned Packer Key']==$staff->id) {
		// return;
		//}



		$this->data ['Delivery Note Assigned Packer Key']=$staff->id;
		$this->data ['Delivery Note Assigned Packer Alias']=$staff->data['Staff Alias'];
		$this->data ['Delivery Note XHTML Packers']=sprintf('<a href="staff.php?id=%d">%s</a>',$staff->id,ucfirst($staff->data['Staff Name']));
		$this->data ['Delivery Note Number Packers']=1;



		$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note Number Packers`=%d,`Delivery Note XHTML Packers`=%s ,`Delivery Note Assigned Packer Key`=%d ,`Delivery Note Assigned Packer Alias`=%s where `Delivery Note Key`=%d"
			,$this->data ['Delivery Note Number Packers']
			,prepare_mysql ($this->data ['Delivery Note XHTML Packers'])
			,$this->data ['Delivery Note Assigned Packer Key']
			,prepare_mysql ($this->data ['Delivery Note Assigned Packer Alias'])


			,$this->id);
		mysql_query($sql);


		$this->update_state($this->get_state());

		foreach ($this->get_orders_objects() as $order) {
			$order->update_dispatch_state();
		}

		$this->assigned=true;


	}
	function start_picking($staff_key,$date=false) {

		if (!$date)
			$date=gmdate("Y-m-d H:i:s");
		$this->assigned=false;

		if (!preg_match('/^(Ready to be Picked|Picker Assigned)$/',$this->data ['Delivery Note State'])) {
			// $this->error=true;
			// $this->msg=$this->data ['Delivery Note State'].' '._('Delivery Note already picking');
			// return;
		}

		if (!$staff_key) {
			$staff_key='';
			$staff_alias=_('Unknown');
			$xhtml_pickers=_('Unknown');

		} else {

			$staff=new Staff($staff_key);

			if (!$staff->id) {
				$this->error=true;
				$this->msg=_('Staff not found');
				return;
			}

			$staff_alias=$staff->data['Staff Name'];
			$staff_key=$staff->id;
			$xhtml_pickers=sprintf('<a href="staff.php?id=%d">%s</a>',$staff_key,ucfirst($staff_alias));

		}

$this->data ['Delivery Note XHTML Pickers']=$xhtml_pickers;
$this->data ['Delivery Note Number Pickers']=1;
		//if ($this->data ['Delivery Note Assigned Picker Key']==$staff_key) {
		// return;
		//}
		// }elseif ($this->data['Delivery Note Assigned Picker Alias'] and $this->data['Delivery Note Date Start Picking']!='' and !$this->data['Delivery Note Assigned Packer Alias']) {



		$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note Date Start Picking`=%s,`Delivery Note State`=%s ,`Delivery Note XHTML Pickers`=%s ,`Delivery Note Number Pickers`=%d ,`Delivery Note Assigned Picker Key`=%s,`Delivery Note Assigned Picker Alias`=%s where `Delivery Note Key`=%d",
			prepare_mysql($date),
			prepare_mysql ($this->data ['Delivery Note State']),
			prepare_mysql ($this->data ['Delivery Note XHTML Pickers']),
			$this->data ['Delivery Note Number Pickers'],
			prepare_mysql ($staff_key),
			prepare_mysql ($staff_alias,false),
			$this->id);
		$result=mysql_query($sql);
	//	print $sql;
		$this->get_data('id',$this->id);


		$this->assigned=true;


		$sql = sprintf("update `Order Transaction Fact` set `Start Picking Date`=%s  where `Delivery Note Key`=%d",
			prepare_mysql($date),
			$this->id);
		mysql_query($sql);

		$this->update_picking_percentage($date);


	}


	function start_packing($staff_key,$date=false) {

		if (!$date)
			$date=gmdate("Y-m-d H:i:s");
		$this->assigned=false;

		if (!preg_match('/^(Picked|Picking|Packer Assigned|Picking & Packer Assigned)$/',$this->data ['Delivery Note State'])) {
			$this->error=true;
			$this->msg=$this->data ['Delivery Note State'].'<'._('Delivery Note can not be assigned to a packer,because is been packed');
			return;
		}

		if (!$staff_key) {
			$staff_key='';
			$staff_alias='';

		} else {

			$staff=new Staff($staff_key);

			if (!$staff->id) {
				$this->error=true;
				$this->msg=_('Staff not found');
				return;
			}

			$staff_alias=$staff->data['Staff Name'];
			$staff_key=$staff->id;
		}

		//if ($this->data ['Delivery Note Assigned Packer Key']==$staff_key) {
		// return;
		//}


		$this->data ['Delivery Note State']='Packing';
		$this->data ['Delivery Note XHTML Packers']=sprintf('<a href="staff.php?id=%d">%s</a>',$staff_key,ucfirst($staff_alias));
		$this->data ['Delivery Note Number Packers']=1;
		$this->data ['Delivery Note Assigned Packer Key']=$staff_key;
		$this->data ['Delivery Note Assigned Packer Alias']=$staff_alias;

		$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note Date Start Packing`=%s,`Delivery Note State`=%s ,`Delivery Note XHTML Packers`=%s ,`Delivery Note Number Packers`=%d ,`Delivery Note Assigned Packer Key`=%s,`Delivery Note Assigned Packer Alias`=%s where `Delivery Note Key`=%d"
			,prepare_mysql ($date)
			,prepare_mysql ($this->data ['Delivery Note State'])
			,prepare_mysql ($this->data ['Delivery Note XHTML Packers'])
			,$this->data ['Delivery Note Number Packers']
			,prepare_mysql ($staff_key)
			,prepare_mysql ($staff_alias,false)
			,$this->id);
		// print $sql;
		mysql_query($sql);
		$this->assigned=true;

		$sql = sprintf("update `Order Transaction Fact` set `Start Packing Date`=%s  where `Delivery Note Key`=%d",
			prepare_mysql($date),
			$this->id);
		//  print $sql;
		mysql_query($sql);

		$this->update_packing_percentage($date);

	}



	function get_formated_state() {

		$state=$this->get_state();
		switch ($state) {
		case 'Dispatched':
			return _('Dispatched');
			break;
		case 'Packed':
			return _('Packed');
			break;
		case 'Packed Done':
			return _('Packed & Checked');
			break;
		case 'Picking & Packing':
			return _('Picking & Packing');
			break;
		case 'Ready to be Picked':
			return _('Ready to be Picked');
			break;
		case 'Picker Assigned':
			return _('Picker Assigned');
			break;
		case 'Picking':
			return _('Picking');
			break;
		case 'Picked':
			return _('Picked');
			break;
		case 'Packing':
			return _('Packing');
			break;
		case 'Packer Assigned':
			return _('Packer Assigned');
			break;
		case 'Unknown':
			return _('Unknown');
			break;


		default:
			return $state;

		}


	}


	function get_state() {

		$state='Unknown';
		//'Picker & Packer Assigned','Picking & Packing','Packer Assigned','Ready to be Picked','Picker Assigned','Picking','Picked','Packing','Packed','Approved','Dispatched','Cancelled','Cancelled to Restock','Packed Done'

		if ($this->data['Delivery Note State']=='Dispatched') {
			return 'Dispatched';
		}
		if ($this->data['Delivery Note Fraction Picked']==1 and $this->data['Delivery Note Fraction Packed']==1) {

			if ($this->data['Delivery Note Approved Done']=='Yes') {

				$state='Packed Done';
			}else {

				$state='Packed';
			}
		}elseif ($this->data['Delivery Note Fraction Picked']==1 and  $this->data['Delivery Note Fraction Packed']==0) {
			$state='Picked';

			if ($this->data['Delivery Note Assigned Packer Alias']!='') {

				if ($this->data['Delivery Note Date Start Packing']=='') {
					$state='Packer Assigned';
				}else {
					$state='Packing';
				}
			}

		}elseif ($this->data['Delivery Note Fraction Picked']==1 and $this->data['Delivery Note Fraction Packed']>0 and $this->data['Delivery Note Fraction Packed']<1) {
			$state='Packing';

		}
		elseif ($this->data['Delivery Note Fraction Picked']>0 and $this->data['Delivery Note Fraction Packed']>0) {
			$state='Picking & Packing';
		}elseif ($this->data['Delivery Note Assigned Picker Alias'] and $this->data['Delivery Note Date Start Picking']!='' and  $this->data['Delivery Note Fraction Packed']==0) {
			$state='Picking';

		}elseif ($this->data['Delivery Note Assigned Picker Alias'] and $this->data['Delivery Note Assigned Packer Alias'] and $this->data['Delivery Note Fraction Picked']==0 and $this->data['Delivery Note Fraction Packed']==0) {
			$state='Ready to be Picked';
		}
		elseif (!$this->data['Delivery Note Assigned Picker Alias'] and !$this->data['Delivery Note Assigned Packer Alias']) {
			$state='Ready to be Picked';
		}elseif ($this->data['Delivery Note Assigned Picker Alias'] and $this->data['Delivery Note Fraction Picked']==0 and !$this->data['Delivery Note Assigned Packer Alias']) {
			$state='Picker Assigned';
		}elseif ($this->data['Delivery Note Assigned Picker Alias'] and $this->data['Delivery Note Fraction Picked']<1 and !$this->data['Delivery Note Assigned Packer Alias']) {
			$state='Picking';
		}elseif ($this->data['Delivery Note Assigned Packer Alias'] and $this->data['Delivery Note Fraction Packed']==0 and $this->data['Delivery Note Date Start Packing']=='') {
			$state='Packer Assigned';
		}elseif ($this->data['Delivery Note Assigned Packer Alias'] and $this->data['Delivery Note Fraction Packed']<1) {
			$state='Packing';

		}else {
			$this->error=true;
			$this->msg="unknown error in update_picking_percentage\n";


		}



		return $state;

	}





	function get_number_transactions() {

		$sql=sprintf("select count(*) as number from   `Inventory Transaction Fact` ITF        where `Delivery Note Key`=%d "
			,$this->id

		);

		$res=mysql_query($sql);
		$number=0;
		if ($row=mysql_fetch_assoc($res)) {
			$number=$row['number'];
		}
		return $number;
	}


	function get_operations($user,$parent='order',$parent_key='') {
		include_once 'order_common_functions.php';

		return get_dn_operations($this->data,$user,$parent,$parent_key);
	}

	function get_notes() {

		$notes='';
		if ($this->data['Delivery Note Customer Sevices Note']!='')
			$notes.="<div><div style='color:#777;font-size:90%;padding-bottom:5px'>"._('Customer Services Notes').":</div>".$this->data['Delivery Note Customer Sevices Note']."</div>";
		if ($this->data['Delivery Note Warehouse Note']!='')
			$notes.="<div><div style='color:#777;font-size:90%;padding-bottom:5px'>"._('Warehouse Notes').":</div>".$this->data['Delivery Note Warehouse Note']."</div>";

		return $notes;

	}

	function get_number_picked_transactions() {

		$sql=sprintf("select count(*) as number from   `Inventory Transaction Fact` ITF        where `Delivery Note Key`=%d and (`Given`+`Required`=`Out of Stock`+`Picked`+`Not Found`+`No Picked Other`) "
			,$this->id

		);
		//     print $sql;
		$res=mysql_query($sql);
		$number=0;
		if ($row=mysql_fetch_assoc($res)) {
			$number=$row['number'];
		}
		return $number;
	}

	function get_number_packed_transactions() {

		$sql=sprintf("select count(*) as number from   `Inventory Transaction Fact` ITF        where `Delivery Note Key`=%d and (`Picked`=`Packed` and `Picked`>0) "
			,$this->id

		);
		//     print $sql;
		$res=mysql_query($sql);
		$number=0;
		if ($row=mysql_fetch_assoc($res)) {
			$number=$row['number'];
		}
		return $number;
	}


	function get_picking_percentage() {
		$sql=sprintf("select `Required`,`Not Found`,`No Picked Other`,`Out of Stock`,ifnull(`Part Package Weight`,0) as `Part Package Weight`,`Picked` ,`Given`,`Inventory Transaction Quantity` from   `Inventory Transaction Fact` ITF           left join `Part Dimension` P on (P.`Part SKU`=ITF.`Part SKU`) where `Delivery Note Key`=%d  "
			,$this->id

		);
		$res=mysql_query($sql);
		$required_weight=0;
		$required_items=0;
		$picked_weight=0;
		$picked_items=0;
		while ($row=mysql_fetch_assoc($res)) {
			//print_r($row);
			$to_be_picked=$row['Required']+$row['Given'];
			$qty=$row['Out of Stock']+$row['Picked']+$row['Not Found']+$row['No Picked Other'];
			$required_weight+=$to_be_picked*$row['Part Package Weight'];
			$required_items++;

			// print "$to_be_picked $qty \n";

			if ($to_be_picked==0) {


			} else if ($qty>=$to_be_picked) {
					$picked_weight+=$to_be_picked*$row['Part Package Weight'];
					$picked_items++;
				} else {


				$picked_weight+=$qty*$row['Part Package Weight'];
				$picked_items+=($qty/$to_be_picked);
			}
			// print "$to_be_picked $qty | $picked_items   $picked_weight  | $required_items $required_weight  \n";

		}
		if ($required_items==0) {
			$percentage_picked=1;
		}
		elseif ($picked_items<$required_items) {
			if ($required_weight>0)
				$percentage_picked=(($picked_items/$required_items)+($picked_weight/$required_weight))/2;
			else
				$percentage_picked=   ($picked_items/$required_items);
		}
		else {
			$percentage_picked=1;
		}




		//print "percentage picked $percentage_picked\n";

		return $percentage_picked;
	}

	function update_picking_percentage($date=false) {

		if (!$date) {
			$date=gmdate("Y-m-d H:i:s");
		}

		$percentage_picked=$this->get_picking_percentage();



		if ($percentage_picked==1) {
			$finish_picking_date=$date;
		}else {
			$finish_picking_date='';
		}

		// print "Picking percentage:".$percentage_picked."\n";
		$sql=sprintf('update `Delivery Note Dimension` set `Delivery Note Date Finish Picking`=%s ,`Delivery Note Fraction Picked`=%f where `Delivery Note Key`=%d  ',
			prepare_mysql($finish_picking_date),
			$percentage_picked,
			$this->id
		);
		mysql_query($sql);

		$this->data['Delivery Note Fraction Picked']=$percentage_picked;
		$this->data['Delivery Note Date Finish Picking']=$finish_picking_date;

		//if($this->data['Delivery Note Fraction Picked']>0 and $this->data['Delivery Note Date Start Picking']){




		$state=$this->get_state();
		$this->update_state($state);
		$this->update_xhtml_state();
		foreach ($this->get_orders_objects() as $order) {


			$order->update_dispatch_state();
		}


	}

	function update_packing_percentage($date=false) {

		if (!$date) {
			$date=gmdate("Y-m-d H:i:s");
		}

		$percentage_packed=$this->get_packing_percentage();
		if ($percentage_packed==1) {
			$finish_packing_date=$date;
		}else {
			$finish_packing_date='';
		}


		$sql=sprintf('update `Delivery Note Dimension` set `Delivery Note Date Finish Packing`=%s ,`Delivery Note Fraction Packed`=%f where `Delivery Note Key`=%d  ',
			prepare_mysql($finish_packing_date),
			$percentage_packed,
			$this->id
		);
		mysql_query($sql);
		$this->data['Delivery Note Fraction Packed']=$percentage_packed;
		$this->data['Delivery Note Date Finish Packing']=$finish_packing_date;


		$state=$this->get_state();




		$this->update_state($state);


		foreach ($this->get_orders_objects() as $order) {

			$order->update_item_totals_from_order_transactions();
			$order->update_totals_from_order_transactions();



			$order->update_dispatch_state();
		}


	}

	function get_packing_percentage() {
		$sql=sprintf("select `Required`,`Out of Stock`,ifnull(`Part Package Weight`,0) as `Part Package Weight`,`Not Found`,`No Picked Other`,`Packed` ,`Given`,`Inventory Transaction Quantity` from   `Inventory Transaction Fact` ITF   left join `Part Dimension` P on (P.`Part SKU`=ITF.`Part SKU`) where `Delivery Note Key`=%d  "
			,$this->id

		);
		$res=mysql_query($sql);
		$required_weight=0;
		$required_items=0;
		$packed_weight=0;
		$packed_items=0;
		//print $sql;
		while ($row=mysql_fetch_assoc($res)) {
			$to_be_packed=$row['Required']+$row['Given']-$row['Out of Stock']-$row['Not Found']-$row['No Picked Other'];




			$qty=$row['Packed'];


			// print "Packing $qty $to_be_packed\n";

			if ($qty>$to_be_packed)
				$qty=$to_be_packed;

			$required_weight+=$to_be_packed*$row['Part Package Weight'];

			if ($to_be_packed)
				$required_items++;

			if ($to_be_packed>0) {
				$packed_weight+=$qty*$row['Part Package Weight'];
				$packed_items+=($qty/$to_be_packed);
			}
		}


		if ($required_items==0) {
			$percentage_packed=1;
		}
		elseif ($packed_items<$required_items) {
			if ($required_weight>0)
				$percentage_packed=(($packed_items/$required_items)+($packed_weight/$required_weight))/2;
			else
				$percentage_packed=   ($packed_items/$required_items);
		}
		else {
			$percentage_packed=1;

		}


		// print "packing percentage: $percentage_packed\n";

		return $percentage_packed;

	}
	function update_state($state) {
		$this->data['Delivery Note State']=$state;
		$sql=sprintf('update `Delivery Note Dimension` set `Delivery Note State`=%s where `Delivery Note Key`=%d  '
			,prepare_mysql($state)
			,$this->id
		);

		mysql_query($sql);
		$this->update_xhtml_state();
	}





	function update_xhtml_state() {


		$state='';
		if ($this->data['Delivery Note State']=='Ready to be Picked') {
			$state=_('Ready to be Picked');
		}
		else if ($this->data['Delivery Note State']=='Ready to be Picked') {
				$state=_('Ready to be Dispatched');
			}
		else if ($this->data['Delivery Note State']=='Dispatched') {
				$state=_('Dispatched');
			}
		else if ($this->data['Delivery Note State']=='Cancelled to Restock') {
				$state=_('Cancelled to Restock');
			}
		else if ($this->data['Delivery Note State']=='Approved') {
				$state=_('Ready to Ship');
			}
		else if ($this->data['Delivery Note State']=='Cancelled') {
				$state=_('Cancelled');
			}
		else if ($this->data['Delivery Note State']=='Picking' and $this->data['Delivery Note Assigned Picker Alias']=='') {
				$state=_('Picking');
			}
		else {


			if ($this->data['Delivery Note Assigned Picker Alias']) {

				if ($this->data['Delivery Note Fraction Picked']==0) {

					if ($this->data['Delivery Note Date Start Picking']=='')
						$_tmp=_('Picker assigned');
					else
						$_tmp=_('Picking').' ('.percentage($this->data['Delivery Note Fraction Picked'],1,0).')';

				}elseif ($this->data['Delivery Note Fraction Picked']==1) {
					$_tmp=_('Picked');
				}else {
					$_tmp=_('Picking').' ('.percentage($this->data['Delivery Note Fraction Picked'],1,0).')';
				}
				$state.='<span id="dn_state'.$this->data['Delivery Note Key'].'">'.$_tmp.' <b>'.$this->data['Delivery Note XHTML Pickers'].'</b></span>';
			}

			if ($this->data['Delivery Note Assigned Packer Alias']) {

				if ($this->data['Delivery Note Fraction Packed']==0) {


					if ($this->data['Delivery Note Date Start Packing']=='')
						$_tmp=_('Packer assigned');
					else
						$_tmp=_('Packing').' ('.percentage($this->data['Delivery Note Fraction Packed'],1,0).')';





				}elseif ($this->data['Delivery Note Fraction Packed']==1) {

					$_tmp=_('Packed');

				}else {
					$_tmp=_('Packing').'('.percentage($this->data['Delivery Note Fraction Packed'],1,0).')';
				}
				$state.='<br/><span id="dn_state_pack'.$this->data['Delivery Note Key'].'">'.$_tmp.' <b>'.$this->data['Delivery Note XHTML Packers'].'</b> </span>';
				if ($this->data['Delivery Note Approved Done']=='Yes') {
					$state.=' &#x2713;';
				}

			}
		}

		$this->data['Delivery Note XHTML State']=$state;
		$sql=sprintf('update `Delivery Note Dimension` set `Delivery Note XHTML State`=%s where `Delivery Note Key`=%d  '
			,prepare_mysql($state)
			,$this->id
		);
		mysql_query($sql);

	}


	function approve_packed($date=false) {

		$this->data['Delivery Note Approved Done']="Yes";
		$this->data['Delivery Note State']="Packed Done";

		if (!$date)
			$this->data['Delivery Note Date Done Approved']=gmdate('Y-m-d H:i:s');
		else
			$this->data['Delivery Note Date Done Approved']=$date;

		$sql=sprintf('update `Delivery Note Dimension` set `Delivery Note State`=%s,`Delivery Note Approved Done`="Yes" ,`Delivery Note Date Done Approved`=%s where `Delivery Note Key`=%d'
			,prepare_mysql($this->data['Delivery Note State'])
			,prepare_mysql($this->data['Delivery Note Date Done Approved'])
			,$this->id
		);
		mysql_query($sql);

		$sql=sprintf('update `Order Transaction Fact` set `Current Dispatching State`="Packed Done"  where `Delivery Note Key`=%d and `Current Dispatching State`="Packed"'

			,$this->id
		);


		mysql_query($sql);


		$this->update_xhtml_state();
		foreach ($this->get_orders_objects() as $order) {
			$order->update_dispatch_state();
			$order->update_xhtml_delivery_notes();
		}
		foreach ($this->get_invoices_objects() as $invoice) {
			$invoice->update_xhtml_delivery_notes();
		}


		$this->updated=true;
	}



	function approved_for_shipping($date=false) {


		//print "-->> approve shipping\n";

		$this->data['Delivery Note Approved To Dispatch']="Yes";

		$this->data['Delivery Note State']="Approved";
		if (!$date)
			$this->data['Delivery Note Date Dispatched Approved']=gmdate('Y-m-d H:i:s');
		else
			$this->data['Delivery Note Date Dispatched Approved']=$date;

		$sql=sprintf('update `Delivery Note Dimension` set `Delivery Note State`=%s,`Delivery Note Approved To Dispatch`="Yes" ,`Delivery Note Date Dispatched Approved`=%s where `Delivery Note Key`=%d'
			,prepare_mysql($this->data['Delivery Note State'])
			,prepare_mysql($this->data['Delivery Note Date Dispatched Approved'])
			,$this->id
		);
		mysql_query($sql);

		$sql=sprintf('update `Order Transaction Fact` set `Current Dispatching State`="Ready to Ship"  where `Delivery Note Key`=%d and `Current Dispatching State`="Packed Done"'

			,$this->id
		);
		mysql_query($sql);



		$this->update_xhtml_state();
		foreach ($this->get_orders_objects() as $order) {
			// $order->update_shipping($this->id);
			//  $order->update_charges($this->id);
			$order->update_dispatch_state();
			$order->update_xhtml_delivery_notes();

		}

		foreach ($this->get_invoices_objects() as $invoice) {
			$invoice->update_xhtml_delivery_notes();
		}
		//$shipping_amount=$this->calculate_shipping();
		//$charges_amount=$this->calculate_charges();



	}





	function set_as_out_of_stock($itf_key,$qty,$date=false,$picker_key=false) {

		if ($qty==0) {
		return;
		}

		$sql=sprintf("select `Inventory Transaction Quantity`,`Inventory Transaction Amount`,`Part SKU`,`Required`,`Picked`,`Map To Order Transaction Fact Key`,`Map To Order Transaction Fact Metadata` from   `Inventory Transaction Fact` where `Inventory Transaction Key`=%d  "
			,$itf_key
		);
		$res=mysql_query($sql);

		if ($row=mysql_fetch_assoc($res)) {
			$sku=$row['Part SKU'];

			$todo=$row['Required']-$row['Picked'];
			if ($qty>$todo) {
				$qty=$todo;
			}

			$transaction_value=$this->get_transaction_value($sku,$qty,$date);

			$sql = sprintf("update `Inventory Transaction Fact` set `Out of Stock`=%f ,`Out of Stock Lost Amount`=%f ,`Out of Stock Tag`='Yes'  where   `Inventory Transaction Key`=%d ",
				$qty,
				-1*$transaction_value,
				$itf_key
			);
			mysql_query($sql);
			//print_r($row);
			//print "$sql\n";

			if ($row['Required']==0 or $todo==$qty) {
				$picking_factor=1;
			} else {
				$picking_factor=($qty+$row['Picked'])/$row['Required'];
			}

			if ($qty==$todo) {
				$state='No Picked Due Out of Stock';
			} else {

				$state='Picking';
			}


			$otf_key=$row['Map To Order Transaction Fact Key'];


			$metadata=preg_split('/;/',$row['Map To Order Transaction Fact Metadata']);
			$parts_per_product=$metadata[1];

			$lost_amount=0;

			$sql = sprintf("update `Order Transaction Fact` set  `Current Dispatching State`=%s,`No Shipped Due Out of Stock`=%f,`Packing Finished Date`=%s,`Picker Key`=%s ,`Picking Factor`=%f where `Order Transaction Fact Key`=%d  ",

				prepare_mysql($state),
				$qty/$parts_per_product,
				prepare_mysql ($date),
				prepare_mysql ($picker_key),
				$picking_factor,
				$otf_key
			);
			mysql_query($sql);

			$sql = sprintf("update `Order Transaction Fact` set `Order Out of Stock Lost Amount`=IFNULL(`Order Transaction Amount`*`No Shipped Due Out of Stock`/`Order Quantity`,0)  where `Order Transaction Fact Key`=%d  ",
				$otf_key
			);
			mysql_query($sql);



			//print "$sql\n";


			foreach ($this->get_orders_objects() as $order_key=>$order) {
				$order->update_no_normal_totals();
			}



		}

	}


	function get_transaction_value($sku,$qty,$date=false) {

		$sql=sprintf("select sum(ifnull(`Inventory Transaction Quantity`,0)) as stock ,ifnull(sum(`Inventory Transaction Amount`),0) as value from `Inventory Transaction Fact` where  `Date`<%s and `Part SKU`=%d "
			,prepare_mysql($date)
			,$sku

		);
		$res_old_stock=mysql_query($sql);
		//print "$sql\n";
		$old_qty=0;
		$old_value=0;

		if ($row_old_stock=mysql_fetch_array($res_old_stock)) {
			$old_qty=round($row_old_stock['stock'],3);
			$old_value=$row_old_stock['value'];
		}
		$transaction_value=$this->get_value_change($sku,-1*$qty,$old_qty,$old_value,$date);
		return $transaction_value;

	}



	function set_as_picked($itf_key,$qty,$date=false,$picker_key=false) {

		$historic=true;

		if (!$date) {
			$date=gmdate("Y-m-d H:i:s");
			$false=true;
		}
		$this->historic=false;

		if (!$picker_key) {
			$picker_key=$this->data['Delivery Note Assigned Picker Key'];

		}
		$sql=sprintf("select `Given`,`Map To Order Transaction Fact Key`,`Location Key`,`Out of Stock`,`No Picked Other`,`Not Found`,`Part SKU`,`Required`,`Picked`,`Packed`,`Map To Order Transaction Fact Key`  from   `Inventory Transaction Fact` where `Inventory Transaction Key`=%d  "
			,$itf_key
		);

		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {


			$original_qty=$qty;

			$out_of_stock=$row['Out of Stock'];
			$not_found=$row['No Picked Other'];
			$no_picked_other=$row['Not Found'];
			$packed=$row['Packed'];
			$pending=$row['Required']+$row['Given']-$out_of_stock-$not_found-$no_picked_other-$packed;

			//print "qty: $qty ; pending: $pending ";


			if ($pending!=0) {
				$picking_factor=round($qty/$pending,4);
			} else
				$picking_factor=0;




			$sku=$row['Part SKU'];






			$transaction_value=$this->get_transaction_value($sku,$qty,($historic?$date:false));


			$cost_storing=0;


			//****
			//transaction value

			//****

			//$cost_supplier=ge        $part->get_unit_cost($date)*$qty;

			$sql=sprintf('select `Product Key` from `Order Transaction Fact` where `Order Transaction Fact Key`=%d  '
				,$row['Map To Order Transaction Fact Key']);
			$resx=mysql_query($sql);

			if ($row_x=mysql_fetch_assoc($resx)) {
				$product = new product ($row_x ['Product Key']);
				$a = sprintf('<a href="product.php?id=%d">%s</a> <a href="dn.php?id=%d">%s</a>'
					,$product->id
					,$product->code
					,$this->id
					,$this->data['Delivery Note ID']
				);



			}
			else {
				$a='';
			}



			$pending=$pending-$qty;


			$location_key=$row['Location Key'];
			if ($location_key) {
				if ($location_key==1) {
					if ($pending) {
						$a.=' '._('To be taken from an')." ";
					}else {
						$a.=' '._('Taken from an')." ";
					}

					$a.=sprintf("<a href='location.php?id=1'>%s</a>",_('Unknown Location'));
				} else {
					if ($pending) {
						$a.=' '._('To be taken from').": ";
					}else {
						$a.=' '._('Taken from').": ";
					}



					$location = new Location($location_key);
					$a.=sprintf("<a href='location.php?id=%d'>%s</a>",$location->id,$location->data['Location Code']);
				}

			}


			$note = $a;



			$sql = sprintf("update `Inventory Transaction Fact` set `Note`=%s,`Picked`=%f,`Inventory Transaction Quantity`=%f,`Inventory Transaction Amount`=%f,`Date Picked`=%s,`Date`=%s ,`Picker Key`=%s where `Inventory Transaction Key`=%d  "
				,prepare_mysql ($note)
				,$qty
				,-1*$qty
				,$transaction_value
				,prepare_mysql ($date)
				,prepare_mysql ($date)
				,prepare_mysql ($picker_key)
				,$itf_key
			);
			mysql_query($sql);

			//print "$sql\n";

			if ($this->update_stock) {


				$part_location=new PartLocation($sku.'_'.$location_key);
				$part_location->update_stock();
			}
			$otf_key=$row['Map To Order Transaction Fact Key'];




			if ($picking_factor>=1)
				$state='Ready to Pack';
			elseif ($picking_factor==0)
				$state='Ready to Pick';
			else
				$state='Picking';

			// print "$picking_factor $state xx";

			$sql = sprintf("update `Order Transaction Fact` set `Picked Quantity`=%f,`Current Dispatching State`=%s,`Picking Finished Date`=%s,`Picker Key`=%s,`Picking Factor`=%f ,`Cost Supplier`=%f,`Cost Storing`=%f where `Order Transaction Fact Key`=%d  ",
				$qty,
				prepare_mysql ($state),
				prepare_mysql ($date),
				prepare_mysql ($picker_key),
				$picking_factor,
				$transaction_value,
				$cost_storing,
				$otf_key
			);
			mysql_query($sql);








			return array(
				'Picked'=>$qty,
				'Packed'=>$packed,
				'Out of Stock'=>$out_of_stock,
				'Not Found'=>$not_found,
				'No Picked Other'=>$no_picked_other,
				'Pending'=>$pending

			);

		} else {
			print "Error no itf found $itf_key\n";
		}




	}

	function update_unpicked_transaction_data($itf_key,$data) {
		if (array_key_exists('Date',$data))
			$date=$data['Date'];
		else
			$date=gmdate("Y-m-d H:i:s");

		if (array_key_exists('Picker Key',$data))
			$picker_key=$data['Picker Key'];
		else
			$picker_key=$this->data['Delivery Note Assigned Picker Key'];
		$sql=sprintf("select * from `Inventory Transaction Fact` where `Inventory Transaction Key`=%d and `Delivery Note Key`=%d",
			$itf_key,
			$this->id);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {
			if ($data['Out of Stock']==$row['Out of Stock'] and    $row['Not Found']==$data['Not Found'] and  $row['No Picked Other']==$data['No Picked Other']) {

				return;
			}

			$todo=$row['Required']-$row['Picked']-$data['Out of Stock']-$data['Not Found']-$data['No Picked Other'];
			if ($todo<0) {
				$this->error=true;
				$this->msg=_('Error,the sum of out of stock and not found units are greater than the number of not picked units')."(".$row['Required']."+".$row['Picked'].")";
				return;
			}
			$picked=$row['Picked'];
			$out_of_stock=$row['Out of Stock'];
			$not_found=$row['Not Found'];
			$no_picked_other=$row['No Picked Other'];
			//   $no_authorized=$row['No Authorized'];

			$pending=$row['Required']-$row['Picked']-$out_of_stock-$not_found-$no_picked_other;
			if ($pending!=0) {
				$picking_factor=round($row['Picked']/$pending,4);
			} else
				$picking_factor=0;
			$otf_key=$row['Map To Order Transaction Fact Key'];


			$sql=sprintf("update `Inventory Transaction Fact` set `Out of Stock`=%f ,`Not Found`=%f,`No Picked Other`=%f where `Inventory Transaction Key`=%d ",
				$data['Out of Stock'],
				$data['Not Found'],
				$data['No Picked Other'],

				$itf_key
			);
			mysql_query($sql);

			$out_of_stock=$data['Out of Stock'];
			$not_found=$data['Not Found'];
			$no_picked_other=$data['No Picked Other'];
			$pending=$row['Required']-$picked-$out_of_stock-$not_found-$no_picked_other;


			if ($picking_factor>=1)
				$state='Ready to Pack';
			else
				$state='Picking';



			$sql = sprintf("update `Order Transaction Fact` set `No Shipped Due Not Found`=%f,`No Shipped Due Other`=%f,`No Shipped Due Out of Stock`=%f,`Current Dispatching State`=%s,`Picking Finished Date`=%s,`Picker Key`=%s,`Picking Factor`=%f where `Order Transaction Fact Key`=%d  ",
				$not_found,
				$no_picked_other,
				$out_of_stock,
				prepare_mysql ($state),
				prepare_mysql ($date),
				prepare_mysql ($picker_key),
				$picking_factor,

				$otf_key
			);
			mysql_query($sql);

			$sql = sprintf("update `Order Transaction Fact` set `Order Out of Stock Lost Amount`=IFNULL(`Order Transaction Amount`*`No Shipped Due Out of Stock`/`Order Quantity`,0)  where `Order Transaction Fact Key`=%d  ",
				$otf_key
			);
			mysql_query($sql);





			$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note Date Finish Picking`=%s where `Delivery Note Key`=%d",
				prepare_mysql ($date),
				$this->id);
			mysql_query($sql);

			foreach ($this->get_orders_objects() as $order_key=>$order) {
				$order->update_no_normal_totals();
			}



			$this->updated=true;


			return array(
				'Picked'=>$picked,
				'Out of Stock'=>$out_of_stock,
				'Not Found'=>$not_found,
				'No Picked Other'=>$no_picked_other,
				'Pending'=>$pending
			);

		} else {
			$this->msg='itf not found';

		}


	}


	function get_packed_estimated_weight() {
		$weight=0;
		$sql=sprintf("select sum(`Estimated Dispatched Weight`) as weight from `Order Transaction Fact` where `Order Quantity`!=0 and `Delivery Note Key`=%d",
			$this->id
		);
		$res=mysql_query($sql);
		while ($row=mysql_fetch_assoc($res)) {
			$weight=$row['weight'];
		}
		return $weight;
	}

	function set_as_packed($itf_key,$qty,$date=false,$packer_key=false) {



		if (!$date)
			$date=gmdate("Y-m-d H:i:s");
		$this->updated=false;

		if (!$packer_key) {
			$packer_key=$this->data['Delivery Note Assigned Packer Key'];
		}

		$sql=sprintf("select `Given`,`Not Found`,`No Picked Other`,`Note`,`Inventory Transaction Amount`,`Inventory Transaction Storing Charge Amount`,`Part SKU`,`Required`,`Picked`,`Packed`,`Out of Stock`,`Map To Order Transaction Fact Key`,`Map To Order Transaction Fact Metadata`  from   `Inventory Transaction Fact` where `Inventory Transaction Key`=%d  "
			,$itf_key
		);
		$res=mysql_query($sql);
		if ($row=mysql_fetch_assoc($res)) {



			$sku=$row['Part SKU'];
			if ($row['Required']+$row['Given']-$row['Out of Stock']<=0 or $row['Picked']==0) {
				return;
			}

			$original_qty=$qty;
			//$qty=$qty+$row['Packed'];

			if ($row['Picked']<$qty) {
				$qty=$row['Picked'];
			}

			$out_of_stock=$row['Out of Stock'];
			$not_found=$row['No Picked Other'];
			$no_picked_other=$row['Not Found'];
			$pending=$row['Required']+$row['Given']-$out_of_stock-$not_found-$no_picked_other;

			if ($pending==0)
				$packing_factor=1;
			else
				$packing_factor=round($qty/$pending,4);


			//print $packing_factor;

			$part=new Part($row['Part SKU']);
			$weight=$qty*$part->data['Part Package Weight'];



			$sql = sprintf("update `Inventory Transaction Fact` set `Inventory Transaction Weight`=%f,`Packed`=%f,`Date Packed`=%s,`Date`=%s ,`Packer Key`=%s where `Inventory Transaction Key`=%d  "
				,$weight
				,$qty
				,prepare_mysql ($date)
				,prepare_mysql ($date)
				,prepare_mysql ($packer_key)

				,$itf_key
			);
			mysql_query($sql);
			//print $sql;
			$otf_key=$row['Map To Order Transaction Fact Key'];

			$metadata=preg_split('/;/',$row['Map To Order Transaction Fact Metadata']);

			//  print_r($metadata);
			$parts_per_product=$metadata[1];

			if ($packing_factor>=1)
				$state='Packed';
			else
				$state='Packing';




			$sql = sprintf("update `Order Transaction Fact` set `Estimated Dispatched Weight`=%f,`Current Dispatching State`=%s,`Delivery Note Quantity`=%f,`Packing Finished Date`=%s,`Packer Key`=%s ,`Packing Factor`=%f  where `Order Transaction Fact Key`=%d  ",
				$weight/$parts_per_product,
				prepare_mysql($state),
				$qty/$parts_per_product,
				prepare_mysql ($date),
				prepare_mysql ($packer_key),
				$packing_factor,

				$otf_key
			);
			mysql_query($sql);
			//            print "$sql\n";

			$weight=$this->get_packed_estimated_weight();
			$sql = sprintf("update `Delivery Note Dimension` set `Delivery Note Estimated Weight`=%f  where `Delivery Note Key`=%d",
				$weight,

				$this->id
			);
			mysql_query($sql);



			return array(
				'Packed'=>$qty,

				'Picked'=>$row['Picked'],
			);


		} else {
			$this->error=true;
			$this->msg='SKU not in order';

		}

	}




	function create_invoice($date=false) {
		if (!$date)
			$date=gmdate("Y-m-d H:i:s");

		$tax_code='UNK';
		$orders_ids='';
		$sales_representatives=array();
		$orders=$this->get_orders_objects();

		$billing_to_key=false;

		foreach ($orders as $order) {

			$tax_code=$order->data['Order Tax Code'];
			$order_ids=$order->id.',';
			foreach ($order->get_sales_representative_keys() as $sales_representative_key) {
				$sales_representatives[$sales_representative_key]=$sales_representative_key;
			}


			if ($order->data['Order Invoiced']=='No') {
				$billing_to_key=$order->data['Order Billing To Key To Bill'];
			}

		}
		$orders_ids=preg_replace('/\,$/','',$order_ids);

		$data_invoice=array(
			'Invoice Date'=>$date,
			'Invoice Type'=>'Invoice',
			'Invoice Public ID'=>$this->data['Delivery Note ID'],
			'Delivery Note Keys'=>$this->id,
			'Orders Keys'=>$orders_ids,
			'Invoice Store Key'=>$this->data['Delivery Note Store Key'],
			'Invoice Customer Key'=>$this->data['Delivery Note Customer Key'],
			'Invoice Tax Code'=>$tax_code,
			'Invoice Tax Shipping Code'=>$tax_code,
			'Invoice Tax Charges Code'=>$tax_code,
			'Invoice Sales Representative Keys'=>$sales_representatives,
			'Invoice Billing To Key'=>$billing_to_key,
			'Invoice Metadata'=>$this->data['Delivery Note Metadata']

		);

		$invoice=new Invoice ('create',$data_invoice);
		$invoice->update_totals();

		foreach ($orders as $order) {
			$order->update_xhtml_state();

		}

		return $invoice;
	}

	function calculate_shipping() {
		$shipping=0;
		foreach ($this->get_orders_objects() as $order) {
			list($_shipping,$tmp)=$order->get_shipping($this->id);
			$shipping+=$_shipping;
		}
		return $shipping;
	}
	function calculate_charges() {
		$charges=0;
		foreach ($this->get_orders_objects() as $order) {
			$charges_data=$order->get_charges($this->id);

			foreach ($charges_data as $charge_data) {
				$charges+=$charge_data['Charge Net Amount'];
			}


		}
		return $charges;
	}

	function update_orders_shipping() {


	}






	function add_orphan_transactions($data) {

		// print_r($data);

		if ($data['Order Key']) {
			$order_key=$data['Order Key'];
			$order_date=$data['Order Date'];
			$order_public_id=$data['Order Public ID'];
		} else {
			$order_key='';
			$order_date='';
			$order_public_id='';
		}
		$bonus_quantity=0;

		$product=new Product('id',$data ['Product Key']);

		$sql = sprintf("insert into `Order Transaction Fact` (`Order Date`,`Order Key`,`Order Public ID`,`Delivery Note Key`,`Delivery Note ID`,`Order Bonus Quantity`,`Order Transaction Type`,`Transaction Tax Rate`,`Transaction Tax Code`,`Order Currency Code`,`Estimated Weight`,`Order Last Updated Date`,
		`Product Key`,
		`Product ID`,
		`Product Code`,
		`Current Dispatching State`,`Current Payment State`,`Customer Key`,`Delivery Note Quantity`,`Ship To Key`,`Order Transaction Gross Amount`,`Order Transaction Total Discount Amount`,`Metadata`,`Store Key`,`Units Per Case`,`Customer Message`)
                         values (%s,%s,%s,%d,%s,%f,%s,%f,%s,%s,%s, %s,%d,%d,%s,%s,%s,%d,%s,%s,%.2f,%.2f,%s,%s,%f,'') ",
			prepare_mysql($order_date),
			prepare_mysql($order_key),
			prepare_mysql($order_public_id),

			$this->id,
			prepare_mysql($this->data['Delivery Note ID']),

			$bonus_quantity,
			prepare_mysql('Resend'),
			$data['Order Tax Rate'],
			prepare_mysql ($data['Order Tax Code']),
			prepare_mysql ($data['Order Currency']),
			$data['Estimated Weight'],

			prepare_mysql ($data ['Date']),
			$product->id,
			$product->pid,
			prepare_mysql($product->code),
			prepare_mysql ($data ['Current Dispatching State']),
			prepare_mysql ($data ['Current Payment State']),
			prepare_mysql ($data['Order Customer Key' ]),

			$data['Quantity'],
			prepare_mysql ($data['Ship To Key']),
			$data['Gross'],
			0,
			prepare_mysql ($data ['Metadata'] ,false),
			prepare_mysql ($data['Order Store Key']),
			$data ['units_per_case']

		);


		if (! mysql_query($sql))
			exit ("$sql can not update orphan transaction\n");
		$otf_key=mysql_insert_id();


		$sql=sprintf("insert into `Order Post Transaction Dimension` (`Delivery Note Key`,`Order Transaction Fact Key`,`Order Post Transaction Fact Key`,`Order Key`,`Quantity`,`Operation`,`Reason`,`To Be Returned`,`State`,`Order Post Transaction Metadata`) values (%d,%s,%d,%s,%f,%s,%s,%s,%s,%s)  ",
			$this->id,
			prepare_mysql($data ['Order Transaction Fact Key']),
			$otf_key,
			prepare_mysql($order_key),
			$data['Quantity'],
			"'Resend'",
			prepare_mysql($data['Reason']),
			"'No'",
			"'Dispatched'",
			prepare_mysql ($data ['Metadata'] ,false)
		);
		mysql_query($sql);

		//print "\n\n order key:$order_key ".$sql."\n";


		$this->update_xhtml_orders();
		foreach ($this->get_orders_objects() as $order) {
			$order->update_xhtml_delivery_notes();
		}

		return array('otf_key'=>$otf_key);

	}

	function get_value_change($sku,$qty_change,$old_qty,$old_value,$date) {
		$qty=$old_qty+$qty_change;
		if ($qty_change>0) {

			list($qty_above_zero,$qty_below_zero)=$this->qty_analysis($old_qty,$qty);
			$value_change=0;
			if ($qty_below_zero) {
				$unit_cost=$old_value/$old_qty;
				$value_change+=$qty_below_zero*$unit_cost;
			}

			if ($qty_above_zero) {
				$part=new Part($sku);
				$unit_cost=$part->get_unit_cost($date);
				$value_change+=$qty_above_zero*$unit_cost;
			}


		}
		elseif ($qty_change<0) {

			list($qty_above_zero,$qty_below_zero)=$this->qty_analysis($old_qty,$qty);

			$value_change=0;
			if ($qty_below_zero) {
				$part=new Part($sku);
				$unit_cost=$part->get_unit_cost($date);
				$value_change+=-$qty_below_zero*$unit_cost;

			}

			if ($qty_above_zero) {

				$unit_cost=$old_value/$old_qty;
				$value_change+=-$qty_above_zero*$unit_cost;

			}



		}
		else {

			$value_change=0;
		}

		return $value_change;
	}
	function qty_analysis($a,$b) {
		if ($b<$a) {
			$tmp=$a;
			$a=$b;
			$b=$tmp;
		}

		if ($a>=0 and $b>=0) {
			$above=$b-$a;
			$below=0;
		}else if ($a<=0 and $b<=0) {
				$above=0;
				$below=$b-$a;
			}else {
			$above=$b;
			$below=-$a;
		}
		return array($above,$below);

	}

	function get_date($field) {
		return strftime("%e %b %Y",strtotime($this->data[$field].' +0:00'));
	}

}


?>
