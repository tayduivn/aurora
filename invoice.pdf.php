<?php

require_once 'common.php';
require_once 'class.Store.php';

require_once 'class.Invoice.php';
include_once 'class.Payment.php';
include_once 'class.Payment_Account.php';
include_once 'class.Payment_Service_Provider.php';

require_once 'common_geography_functions.php';



$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
if (!$id) {
	exit;
}
$invoice=new Invoice($id);
if (!$invoice->id) {
	exit;
}


$invoice->update_tax();



//print_r($invoice);
$store=new Store($invoice->data['Invoice Store Key']);
$customer=new Customer($invoice->data['Invoice Customer Key']);


putenv('LC_ALL='.$store->data['Store Locale'].'.UTF-8');
setlocale(LC_ALL,$store->data['Store Locale'].'.UTF-8');
bindtextdomain("inikoo", "./locales");
textdomain("inikoo");




$order_key=0;
$dn_key=0;


$number_orders=$invoice->get_number_orders();


if ($number_orders==1) {
	$orders=$invoice->get_orders_objects();
	$order=array_pop($orders);
	$smarty->assign('order',$order);
}
$number_dns=$invoice->get_number_delivery_notes();
if ($number_dns==1) {
	$delivery_notes=$invoice->get_delivery_notes_objects();
	$delivery_note=array_pop($delivery_notes);
	$smarty->assign('delivery_note',$delivery_note);
}


$smarty->assign('number_orders',$number_orders);
$smarty->assign('number_dns',$number_dns);


include "external_libs/mpdf/mpdf.php";

$mpdf=new mPDF('win-1252','A4','','',20,15,38,25,10,10);

$mpdf->useOnlyCoreFonts = true;    // false is default
$mpdf->SetTitle(_('Invoice').' '.$invoice->data['Invoice Public ID']);
$mpdf->SetAuthor($store->data['Store Name']);


if ($invoice->data['Invoice Paid']=='Yes') {
	$mpdf->SetWatermarkText(_('Paid'));
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.03;
}

//$mpdf->SetDisplayMode('fullpage');


if (isset($_REQUEST['print'])) {
	$mpdf->SetJS('this.print();');
}
$smarty->assign('store',$store);

$smarty->assign('invoice',$invoice);


if ($invoice->data['Invoice Type']=='Invoice') {
	$smarty->assign('label_title',_('Invoice'));
	$smarty->assign('label_title_no',_('Invoice No.'));

}elseif ($invoice->data['Invoice Type']=='CreditNote') {
	$smarty->assign('label_title',_('Credit Note'));
	$smarty->assign('label_title_no',_('Credit Note No.'));

}else {
	$smarty->assign('label_title',_('Refund'));
	$smarty->assign('label_title_no',_('Refund No.'));
}


if ( in_array($invoice->data['Invoice Delivery Country Code'],get_countries_EC_Fiscal_VAT_area())) {
	$print_tariff_code=false;
}else {
	$print_tariff_code=true;

}



$transactions=array();
$sql=sprintf("select `Product RRP`,`Product Tariff Code`,`Product Tariff Code`,`Invoice Transaction Gross Amount`,`Invoice Transaction Total Discount Amount`,`Invoice Transaction Item Tax Amount`,`Invoice Quantity`,`Invoice Transaction Tax Refund Amount`,`Invoice Currency Code`,`Invoice Transaction Net Refund Amount`,`Product XHTML Short Description`,P.`Product ID`,O.`Product Code` from `Order Transaction Fact` O  left join `Product History Dimension` PH on (O.`Product Key`=PH.`Product Key`) left join  `Product Dimension` P on (PH.`Product ID`=P.`Product ID`) where `Invoice Key`=%d order by `Product Code`", $invoice->id);
//print $sql;exit;
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$row['Amount']=money(($row['Invoice Transaction Gross Amount']-$row['Invoice Transaction Total Discount Amount']),$row['Invoice Currency Code']);
	$row['Discount']=($row['Invoice Transaction Total Discount Amount']==0?'':percentage($row['Invoice Transaction Total Discount Amount'],$row['Invoice Transaction Gross Amount'],0));

	if ($row['Product RRP']!=0) {
		$row['Product XHTML Short Description']=$row['Product XHTML Short Description'].'<br>'._('RRP').': '.money($row['Product RRP'],$row['Invoice Currency Code']);
	}

	if ($print_tariff_code)
		$row['Product XHTML Short Description']=$row['Product XHTML Short Description'].'<br>'._('Tariff Code').': '.$row['Product Tariff Code'];

	$transactions[]=$row;

}


$transactions_out_of_stock=array();
$sql=sprintf("select (`No Shipped Due Out of Stock`+`No Shipped Due No Authorized`+`No Shipped Due Not Found`+`No Shipped Due Other`) as qty,`Product RRP`,`Product Tariff Code`,`Product Tariff Code`,`Invoice Transaction Gross Amount`,`Invoice Transaction Total Discount Amount`,`Invoice Transaction Item Tax Amount`,`Invoice Quantity`,`Invoice Transaction Tax Refund Amount`,`Invoice Currency Code`,`Invoice Transaction Net Refund Amount`,`Product XHTML Short Description`,P.`Product ID`,O.`Product Code` from `Order Transaction Fact` O  left join `Product History Dimension` PH on (O.`Product Key`=PH.`Product Key`) left join  `Product Dimension` P on (PH.`Product ID`=P.`Product ID`) where `Invoice Key`=%d and (`No Shipped Due Out of Stock`>0  or  `No Shipped Due No Authorized`>0 or `No Shipped Due Not Found`>0 or `No Shipped Due Other` )  order by `Product Code`", $invoice->id);
//print $sql;exit;
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$row['Amount']='';
	$row['Discount']='';

	if ($row['Product RRP']!=0) {
		$row['Product XHTML Short Description']=$row['Product XHTML Short Description'].'<br>'._('RRP').': '.money($row['Product RRP'],$row['Invoice Currency Code']);
	}

	$row['Quantity']='<span >('.$row['qty'].')</span>';
	$transactions_out_of_stock[]=$row;

}

$smarty->assign('number_transactions_out_of_stock',count($transactions_out_of_stock));

$smarty->assign('transactions_out_of_stock',$transactions_out_of_stock);



if ($invoice->data['Invoice Type']=='CreditNote') {

	$sql=sprintf("select * from `Order No Product Transaction Fact` where `Invoice Key`=%d ", $invoice->id);
	//print $sql;exit;
	$result=mysql_query($sql);
	while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		switch ($row['Transaction Type']) {
		case('Credit'):
			$code=_('Credit');
			break;
		case('Refund'):
			$code=_('Refund');
			break;
		case('Shipping'):
			$code=_('Shipping');
			break;
		case('Charges'):
			$code=_('Charges');
			break;
		case('Adjust'):
			$code=_('Adjust');
			break;
		case('Other'):
			$code=_('Other');
			break;
		case('Deal'):
			$code=_('Deal');
			break;
		case('Insurance'):
			$code=_('Insurance');
			break;
		default:
			$code=$row['Transaction Type'];


		}
		$row['Product Code']=$code;
		$row['Product XHTML Short Description']=$row['Transaction Description'];
		$row['Amount']=money(($row['Transaction Invoice Net Amount']),$row['Currency Code']);
		$row['Discount']='';
		$transactions[]=$row;


	}
}

$smarty->assign('transactions',$transactions);




$tax_data=array();
$sql=sprintf("select `Tax Category Name`,`Tax Category Rate`,`Tax Amount` from  `Invoice Tax Bridge` B  left join `Tax Category Dimension` T on (T.`Tax Category Code`=B.`Tax Code`)  where B.`Invoice Key`=%d ",$invoice->id);

$res=mysql_query($sql);
while ($row=mysql_fetch_assoc($res)) {

	if ($row['Tax Amount']==0) {
		continue;
	}

	switch ($row['Tax Category Name']) {
	case 'Outside the scope of VAT':
		$tax_category_name=_('Outside the scope of VAT');
		break;
	case 'VAT 17.5%':
		$tax_category_name=_('VAT').' 17.5%';
		break;
	case 'VAT 20%':
		$tax_category_name=_('VAT').' 20%';
		break;
	case 'VAT 15%':
		$tax_category_name=_('VAT').' 15%';
		break;
	case 'No Tax':
		$tax_category_name=_('No Tax');
		break;
	case 'RE (5,2%)':
		$tax_category_name='RE 5,2%';
		break;	
	case 'Exempt from VAT':
		$tax_category_name=_('Exempt from VAT');
		break;


	default:
		$tax_category_name=$row['Tax Category Name'];
	}



	$tax_data[]=array(
		'name'=>$tax_category_name,
		'amount'=>money($row['Tax Amount'],$invoice->data['Invoice Currency']));
}


$smarty->assign('tax_data',$tax_data);


$html=$smarty->fetch('invoice.pdf.tpl');




$mpdf->WriteHTML($html);

//$mpdf->WriteHTML('<pagebreak resetpagenum="1" pagenumstyle="1" suppress="off" />');



$mpdf->Output();

?>
