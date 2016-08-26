<?php

/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created:19 April 2016 at 11:32:28 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'class.Part.php';
require_once 'class.PurchaseOrder.php';
include_once 'utils/currency_functions.php';


//'InProcess','Submitted','Inputted','Dispatched','Received','Checked','Placed','Cancelled'
$sql=sprintf('select `Purchase Order Key` from `Purchase Order Dimension` where `Purchase Order State`="InProcess"    ');

if ($result=$db->query($sql)) {
	foreach ($result as $row) {
		$purchase_order=new PurchaseOrder($row['Purchase Order Key']);

		$purchase_order->update(
			array(
				'Purchase Order Currency Exchange'=>currency_conversion($db, $purchase_order->get('Purchase Order Currency Code'), $account->get('Account Currency'), '- 15 minutes')
			)


		);
	}
}else {
	print_r($error_info=$db->errorInfo());
	exit;
}


$sql=sprintf('select `Part SKU` from `Part Dimension` order by `Part SKU`  ');

if ($result=$db->query($sql)) {
	foreach ($result as $row) {
		$part=new Part($row['Part SKU']);
		print $part->sku."\r";
		$part->update_cost();
	}

}else {
	print_r($error_info=$db->errorInfo());
	exit;
}



?>
