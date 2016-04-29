<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 30 December 2015 at 14:28:35 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/



$elements_options=array(


	'customers'=>array(
		'orders'=>array(
			'label'=>_('Orders'),
			'items'=>array(
				'Yes'=>array('label'=>_('With orders'), 'selected'=>true),
				'No'=>array('label'=>_('Without orders'), 'selected'=>true),
			)


		),
		'activity'=>array(
			'label'=>_('Active/Lost'),
			'items'=>array(
				'Active'=>array('label'=>_('Active'), 'selected'=>true),
				'Losing'=>array('label'=>_('Losing'), 'selected'=>true),
				'Lost'=>array('label'=>_('Lost'), 'selected'=>true),
			)


		),
		'type'=>array(
			'label'=>_('Type'),
			'items'=>array(
				'Normal'=>array('label'=>_('Normal'), 'selected'=>true),
				'VIP'=>array('label'=>_('VIP'), 'selected'=>true),
				'Partner'=>array('label'=>_('Partner'), 'selected'=>true),
				'Staff'=>array('label'=>_('Staff'), 'selected'=>true),
			)
		),
		'location'=>array(
			'label'=>_('Location'),
			'items'=>array(
				'Domestic'=>array('label'=>_('Domestic'), 'selected'=>true),
				'Export'=>array('label'=>_('Export'), 'selected'=>true),

			)


		)
	),
	'customer_history'=>array(
		'type'=>array(
			'label'=>_('Type'),
			'items'=>array(
				'Notes'=>array('label'=>_('Notes'), 'selected'=>true),
				'Orders'=>array('label'=>_('Orders'), 'selected'=>true),
				'Changes'=>array('label'=>_('Changes'), 'selected'=>true),
				'Attachments'=>array('label'=>_('Attachments'), 'selected'=>true),
				'WebLog'=>array('label'=>_('WebLog'), 'selected'=>true),
				'Emails'=>array('label'=>_('Emails'), 'selected'=>true)
			),

		)
	),
	'supplier_history'=>array(
		'type'=>array(
			'label'=>_('Type'),
			'items'=>array(
				'Notes'=>array('label'=>_('Notes'), 'selected'=>true),
				'Changes'=>array('label'=>_('Changes'), 'selected'=>true),
				'WebLog'=>array('label'=>_('WebLog'), 'selected'=>true),
			),

		)
	),
	'supplier_part_history'=>array(
		'type'=>array(
			'label'=>_('Type'),
			'items'=>array(
				'Notes'=>array('label'=>_('Notes'), 'selected'=>true),
				'Changes'=>array('label'=>_('Changes'), 'selected'=>true),
			),

		)
	),
		'agent_history'=>array(
		'type'=>array(
			'label'=>_('Type'),
			'items'=>array(
				'Notes'=>array('label'=>_('Notes'), 'selected'=>true),
				'Changes'=>array('label'=>_('Changes'), 'selected'=>true),
				'WebLog'=>array('label'=>_('WebLog'), 'selected'=>true),
			),

		)
	),
	'orders'=>array(
		'dispatch'=>array(
			'label'=>_('Dispatch state'),
			'items'=>array(
				'InProcessCustomer'=>array('label'=>_('Basket'), 'selected'=>true),
				'InProcess'=>array('label'=>_('In process'), 'selected'=>true),
				'Warehouse'=>array('label'=>_('Warehouse'), 'selected'=>true),
				'Dispatched'=>array('label'=>_('Dispatched'), 'selected'=>true),
				'Cancelled'=>array('label'=>_('Cancelled'), 'selected'=>false),
				'Suspended'=>array('label'=>_('Suspended'), 'selected'=>false)),
		),
		'type'=>array('label'=>_('Payment'),
			'items'=>array(
				'Order'=>array('label'=>_('Order'), 'selected'=>true),
				'Sample'=>array('label'=>_('Sample'), 'selected'=>true),
				'Donation'=>array('label'=>_('Donation'), 'selected'=>true),
				'Other'=>array('label'=>_('Other'), 'selected'=>true),
			)
		),
		'source'=>array(
			'label'=>_('Source'),
			'items'=>array(
				'Internet'=>array('label'=>_('Website'), 'selected'=>true),
				'Call'=>array('label'=>_('Telephone'), 'selected'=>true),
				'Store'=>array('label'=>_('Showroom'), 'selected'=>true),
				'Email'=>array('label'=>_('Email'), 'selected'=>true),
				'Fax'=>array('label'=>_('Fax'), 'selected'=>true),
				'Other'=>array('label'=>_('Other'), 'selected'=>true)
			),

		),
		'payment'=>array(
			'label'=>_('Payment'),
			'items'=>array(
				'Paid'=>array('label'=>_('Paid'), 'selected'=>true),
				'PartiallyPaid'=>array('label'=>_('Partially Paid'), 'selected'=>true),
				'Unknown'=>array('label'=>_('Unknown'), 'selected'=>true),
				'WaitingPayment'=>array('label'=>_('Waiting Payment'), 'selected'=>true),
				'NA'=>array('label'=>_('NA'), 'selected'=>true),
			)
		),


	),
	'invoices'=>array(
		'type'=>array(
			'label'=>_('Type'),
			'items'=>array(
				'Invoice'=>array('label'=>_('Invoice'), 'selected'=>true),
				'Refund'=>array('label'=>_('Refund'), 'selected'=>true),
			)
		),
		'payment_state'=>array('label'=>_('Payment state'),
			'items'=>array(
				'Yes'=>array('label'=>_('Paid'), 'selected'=>true),
				'Partially'=>array('label'=>_('Partially paid'), 'selected'=>true),
				'No'=>array('label'=>_('Waiting payment'), 'selected'=>true),
			)
		)

	),
	'delivery_notes'=>array(
		'dispatch'=>array(
			'label'=>_('Dispatch state'),
			'items'=>array(
				'Ready'=>array('label'=>_('Ready'), 'selected'=>true),
				'Picking'=>array('label'=>_('Picking'), 'selected'=>true),
				'Packing'=>array('label'=>_('Packing'), 'selected'=>true),
				'Done'=>array('label'=>_('Done'), 'selected'=>true),
				'Send'=>array('label'=>_('Send'), 'selected'=>true),
				'Returned'=>array('label'=>_('Returned'), 'selected'=>true),
			)
		),
		'type'=>array('label'=>_('Type'),
			'items'=>array(
				'Order'=>array('label'=>_('Order'), 'selected'=>true),
				'Sample'=>array('label'=>_('Sample'), 'selected'=>true),
				'Donation'=>array('label'=>_('Donation'), 'selected'=>true),
				'Replacements'=>array('label'=>_('Replacements'), 'selected'=>true),
				'Shortages'=>array('label'=>_('Shortages'), 'selected'=>true),
			)
		)

	),

	'products'=>array(
		'status'=>array(
			'label'=>_('Status'),
			'items'=>array(
				'InProcess'=>array('label'=>_('In process'), 'selected'=>false),
				'Active'=>array('label'=>_('Active'), 'selected'=>true),
				'Suspended'=>array('label'=>_('Suspended'), 'selected'=>false),
				'Discontinued'=>array('label'=>_('Discontinued'), 'selected'=>false)
			)



		),
	),
	'parts'=>array(
		'stock_status'=>array(
			'label'=>_('Stock status'),
			'items'=>array(
				'Surplus'=>array('label'=>_('Surplus'), 'selected'=>true),
				'Optimal'=>array('label'=>_('Optimal'), 'selected'=>true),
				'Low'=>array('label'=>_('Low'), 'selected'=>true),
				'Critical'=>array('label'=>_('Critical'), 'selected'=>true),
				'Error'=>array('label'=>_('Error'), 'selected'=>true),
				'Out_Of_Stock'=>array('label'=>_('Out of stock'), 'selected'=>true)

			)



		),
	),
	'supplier_parts'=>array(
		'status'=>array(
			'label'=>_('Status'),
			'items'=>array(
				'Available'=>array('label'=>_('Available'), 'selected'=>true),
				'NoAvailable'=>array('label'=>_('No available'), 'selected'=>true),
				'Discontinued'=>array('label'=>_('Discontinued'), 'selected'=>false)
			)



		),
		'part_status'=>array(
			'label'=>_('Part status'),
			'items'=>array(
				'InUse'=>array('label'=>_('Part active'), 'selected'=>true),
				'NotInUse'=>array('label'=>_('Part discontinued'), 'selected'=>true),
			)



		)
	),
	'barcodes'=>array(
		'status'=>array(
			'label'=>_('Status'),
			'items'=>array(
				'Available'=>array('label'=>_('Available'), 'selected'=>true),
				'Used'=>array('label'=>_('Used'), 'selected'=>true),
				'Reserved'=>array('label'=>_('Reserved'), 'selected'=>true),
			)



		),
	),
	'part_stock_transactions'=>array(
		'type'=>array(
			'label'=>_('Transaction type'),
			'items'=>array(
				'Move'=>array('label'=>_('Movements'), 'selected'=>true),
				'NoDispatched'=>array('label'=>_('No Dispatched'), 'selected'=>true),
				'Audit'=>array('label'=>_('Audits'), 'selected'=>true),
				'In'=>array('label'=>_('In'), 'selected'=>true),
				'Out'=>array('label'=>_('Out'), 'selected'=>true),
				'OIP'=>array('label'=>'OIP', 'selected'=>true,'title'=>_('Orders in Process'))

			)



		),
	),
	'locations'=>array(
		'flags'=>array(
			'label'=>_('Flags'),
			'items'=>array(
				'Blue'=>array('label'=>'Blue', 'selected'=>true),
				'Green'=>array('label'=>'Green', 'selected'=>true),
				'Orange'=>array('label'=>'Orange', 'selected'=>true),
				'Pink'=>array('label'=>'Pink', 'selected'=>true),
				'Purple'=>array('label'=>'Purple', 'selected'=>true),
				'Red'=>array('label'=>'Red', 'selected'=>true),
				'Yellow'=>array('label'=>'Yellow', 'selected'=>true),

			)



		),
	),
		'location_history'=>array(
		'type'=>array(
			'label'=>_('Type'),
			'items'=>array(
				'Notes'=>array('label'=>_('Notes'), 'selected'=>true),
				'Changes'=>array('label'=>_('Changes'), 'selected'=>true),
				'WebLog'=>array('label'=>_('WebLog'), 'selected'=>true),
			),

		)
	),
);

?>
