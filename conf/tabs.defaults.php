<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 22 September 2015 12:09:42 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

include 'conf/export_fields.php';
include 'conf/elements_options.php';

$default_rrp_options=array(500, 100, 50, 20);



$tab_defaults=array(

	'customers'=>array(
		'view'=>'overview',
		'sort_key'=>'formatted_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'elements_type'=>each(  $elements_options['customers']  ) ['key'],
		'elements'=>$elements_options['customers'],
		'export_fields'=>$export_fields['customers']

	),
	'customers.lists'=>array(
		'view'=>'overview',
		'sort_key'=>'creation_date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'customers.categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'customer.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',
		'elements_type'=>each($elements_options['customer_history'])['key'],
		'elements'=>$elements_options['customer_history']
	),
	'customer.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['orders'])['key'],
		'elements'=>$elements_options['orders']
	),
	'customer.marketing.favourites'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',
	),

	'customers_server'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'percentages'=>0
	),
	'customer.marketing.products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	),
	'orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>'dispatch',
		'elements'=>$elements_options['orders'],
		'export_fields'=>$export_fields['orders']

	),
	'orders_server'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>'dispatch',
		'elements'=>$elements_options['orders']

	),
	'order.items'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>1000,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',

	),
	'order.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'order.invoices'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['invoices']

	),
	'order.delivery_notes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['delivery_notes']

	),
	'order.payments'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'

	),
	'delivery_note.invoices'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['invoices']


	),
	'delivery_note.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['orders']


	),
	'delivery_note.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'delivery_note.items'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>1000,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',

	),
	'invoice.items'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>1000,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',

	),
	'invoice.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['orders'],


	),
	'invoice.delivery_notes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['delivery_notes']


	),
	'invoice.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'invoices'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'customer',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['invoices'])['key'],
		'elements'=>$elements_options['invoices'],
		'export_fields'=>$export_fields['invoices']

	),
	'invoices.categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'invoices_server'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'customer',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['invoices'])['key'],
		'elements'=>$elements_options['invoices']
	),
	'invoices_server.categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'delivery_notes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'customer',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['delivery_notes'])['key'],
		'elements'=>$elements_options['delivery_notes'],
		'export_fields'=>$export_fields['delivery_notes']

	),
	'delivery_notes_server'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'customer',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['delivery_notes'])['key'],
		'elements'=>$elements_options['delivery_notes']
	),
	'orders_index'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'percentages'=>0
	),
	'stores'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	),

	'store.products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',
		'elements_type'=>each(  $elements_options['products']  ) ['key'],
		'elements'=>$elements_options['products'],

	),
	'category.products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',
		'elements_type'=>each(  $elements_options['products']  ) ['key'],
		'elements'=>$elements_options['products'],

	),
	'category.all_products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',
		'elements_type'=>each(  $elements_options['products']  ) ['key'],
		'elements'=>$elements_options['products'],

	),
	'products.categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),

	'product.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'product.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'customer',
		'from'=>'',
		'to'=>'',
		'period'=>'ytd',
		'elements_type'=>each($elements_options['orders'])['key'],
		'elements'=>$elements_options['orders'],
		'export_fields'=>$export_fields['orders']
	),
	'websites'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'website.webpages'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',
		'elements_type'=>each($elements_options['webpages'])['key'],
		'elements'=>$elements_options['webpages'],
	),
	'website.root_nodes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	),
	'node.nodes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	),
	'website.node.pages'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	),
	'website.node.nodes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	),
	'page.blocks'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	),

	'website.favourites.customers'=>array(
		'view'=>'overview',
		'sort_key'=>'formatted_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'elements_type'=>each($elements_options['customers'])['key'],
		'elements'=>$elements_options['customers']
	),
	'website.search.queries'=>array(
		'view'=>'overview',
		'sort_key'=>'number',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'query',

	),
	'website.search.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'query',

	),
	'website.users'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle',

	),
	'page.users'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle',

	),
	'website.user.login_history'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'ip',
		'f_period'=>'all',

	),
	'website.user.pageviews'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'page',
		'f_period'=>'all',

	),
	'marketing_server'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',
	),
	'suppliers'=>array(
		'view'=>'overview',
		'sort_key'=>'formatted_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'f_period'=>'ytd',
	),
	'category.suppliers'=>array(
		'view'=>'overview',
		'sort_key'=>'formatted_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'f_period'=>'ytd',
	),
	'category.all_suppliers'=>array(
		'view'=>'overview',
		'sort_key'=>'formatted_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'f_period'=>'ytd',
	),
	'suppliers_edit'=>array(
		'view'=>'overview',
		'sort_key'=>'formatted_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',

	),
	'suppliers.lists'=>array(
		'view'=>'overview',
		'sort_key'=>'creation_date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'suppliers.categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,

		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'suppliers.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['supplier_orders'])['key'],
		'elements'=>$elements_options['supplier_orders']
	),
	'suppliers.deliveries'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['supplier_deliveries'])['key'],
		'elements'=>$elements_options['supplier_deliveries']
	),
	'supplier.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',
		'elements_type'=>each($elements_options['supplier_history'])['key'],
		'elements'=>$elements_options['supplier_history']
	),
	'supplier.supplier_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'reference',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',
		'elements_type'=>each($elements_options['supplier_parts'])['key'],
		'elements'=>$elements_options['supplier_parts']

	),
	'supplier.order.all_supplier_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'reference',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',

	),
	'supplier.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['supplier_orders'])['key'],
		'elements'=>$elements_options['supplier_orders']
	),
	'supplier.deliveries'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['supplier_deliveries'])['key'],
		'elements'=>$elements_options['supplier_deliveries']
	),
	'supplier.order.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',
		'elements_type'=>each($elements_options['supplier_order_history'])['key'],
		'elements'=>$elements_options['supplier_order_history']
	),
	'supplier.order.items'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>1000,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',

	),
	'deleted.supplier.order.items'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>1000,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',

	),
	'supplier.order.supplier_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'reference',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',
		'elements_type'=>each($elements_options['supplier_parts'])['key'],
		'elements'=>$elements_options['supplier_parts']

	),
	'supplier.delivery.items'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>1000,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',

	),

	'supplier.delivery.check_items'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>1000,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',

	),

	'supplier.delivery.supplier_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'reference',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',
		'elements_type'=>each($elements_options['supplier_parts'])['key'],
		'elements'=>$elements_options['supplier_parts']

	),
	'supplier_part.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',
		'elements_type'=>each($elements_options['supplier_part_history'])['key'],
		'elements'=>$elements_options['supplier_part_history']
	),
	'agents'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'f_period'=>'ytd',
	),
	'agent.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',
		'elements_type'=>each($elements_options['agent_history'])['key'],
		'elements'=>$elements_options['agent_history']
	),
	'agent.supplier_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'reference',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',
		'elements_type'=>each($elements_options['supplier_parts'])['key'],
		'elements'=>$elements_options['supplier_parts']

	),
	'agent.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['supplier_orders'])['key'],
		'elements'=>$elements_options['supplier_orders']
	),
	'agent.deliveries'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['supplier_deliveries'])['key'],
		'elements'=>$elements_options['supplier_deliveries']
	),
	'warehouses'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
	),
	'part.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'part.images'=>array(
		'view'=>'overview',
		'sort_key'=>'order',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'caption'
	),
	'part.products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',
		'elements_type'=>each(  $elements_options['products']  ) ['key'],
		'elements'=>$elements_options['products'],

	),
	'part.supplier_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'reference',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',

	),
	'warehouse.locations'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'elements_type'=>each(  $elements_options['locations']  ) ['key'],
		'elements'=>$elements_options['locations'],
	),
	'warehouse.replenishments'=>array(
		'view'=>'overview',
		'sort_key'=>'location',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'location'
	),
	'warehouse.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'location.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',
		'elements_type'=>each($elements_options['location_history'])['key'],
		'elements'=>$elements_options['location_history']
	),
	'inventory.parts'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',
		'f_period'=>'ytd',
		'elements_type'=>each(  $elements_options['parts']  ) ['key'],
		'elements'=>$elements_options['parts'],
	),

	'inventory.discontinued_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'to',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'
	),
	'parts.categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'part.stock.transactions'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>100,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',
		'elements_type'=>each(  $elements_options['part_stock_transactions']  ) ['key'],
		'elements'=>$elements_options['part_stock_transactions'],
	),

	'part.stock.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>100,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note',

	),

	'category.parts'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',
		'f_period'=>'ytd',
		'elements_type'=>each(  $elements_options['parts']  ) ['key'],
		'elements'=>$elements_options['parts'],
	),
	'category.all_parts'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference',
		'f_period'=>'ytd',
		'elements_type'=>each(  $elements_options['parts']  ) ['key'],
		'elements'=>$elements_options['parts'],

	),
	'inventory.barcodes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'elements_type'=>each(  $elements_options['barcodes']  ) ['key'],
		'elements'=>$elements_options['barcodes'],
	),
	'part_family.product_families'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd'
	),
	'barcode.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),

	'operatives'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'batches'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'id'
	),
	'manufacture_tasks'=>array(
		'view'=>'overview',
		'sort_key'=>'name',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),

	'overtimes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'
	),
	'overtime.timesheets'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'alias',

	),
	'overtime.employees'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'alias',

	),
	'overtimes'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'
	),
	'employees'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'exemployees'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'deleted.employees'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'contractors'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'deleted.contractors'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),

	'timesheets.months'=>array(
		'view'=>'overview',
		'sort_key'=>'month',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'year'=>strtotime('now'),

	),
	'timesheets.weeks'=>array(
		'view'=>'overview',
		'sort_key'=>'month',
		'sort_order'=>-1,
		'rpp'=>100,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'year'=>strtotime('now'),

	),
	'timesheets.days'=>array(
		'view'=>'overview',
		'sort_key'=>'month',
		'sort_order'=>-1,
		'rpp'=>500,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'year'=>strtotime('now'),

	),
	'timesheets.timesheets'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'alias',
	),
	'timesheets.employees'=>array(
		'view'=>'overview',
		'sort_key'=>'name',
		'sort_order'=>1,
		'rpp'=>100,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',

	),
	'fire'=>array(
		'view'=>'overview',
		'sort_key'=>'status',
		'sort_order'=>-1,
		'rpp'=>100,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',

	),

	'employees.timesheets'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
	),
	'employees.timesheets.records'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'from'=>'',
		'to'=>'',
		'period'=>'all',

	),
	'employee.timesheets.records'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'from'=>'',
		'to'=>'',
		'period'=>'all',

	),
	'employee.timesheets'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
	),
	'employee.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'employee.attachments'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'caption'
	),
	'employee.images'=>array(
		'view'=>'overview',
		'sort_key'=>'order',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'caption'
	),
	'deleted.employee.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'hr.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'timesheet.records'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',


	),
	'employee.attachment.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),

	'contractor.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
		'deleted.contractor.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'upload.employees'=>array(
		'view'=>'overview',
		'sort_key'=>'row',
		'sort_order'=>0,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'object_name'
	),
	'hr.uploads'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>0,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>''
	),
	'reports'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
	),
	'data_sets'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),

	'timeseries'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),
	'timeserie.records'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['timeserie_records']

	),
	'data_sets.images'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),

	'data_sets.attachments'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),
	'data_sets.uploads'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),
	'upload.records'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),

	'account.users'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),
	'users.staff.groups'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
	),
	'payment_service_providers'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'

	),
	'payments'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'


	),
	'payment_service_provider.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'payment_service_provider.accounts'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'payment_service_provider.payments'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'
	),

	'payment_account.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'payment_account.payments'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'
	),
	'payment.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'payment_accounts'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'account.users.staff'=>array(
		'view'=>'privilegies',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle'
	),




	'staff.user.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'staff.user.login_history'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'ip',
		'from'=>'',
		'to'=>'',
		'period'=>'all',

	),
	'staff.user.api_keys'=>array(
		'view'=>'overview',
		'sort_key'=>'formatted_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',

	),
	'staff.user.api_key.requests'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'from'=>'',
		'to'=>'',
		'period'=>'all',

	),

	'deleted.staff.user.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'note'
	),
	'deleted.staff.user.login_history'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'ip',
		'from'=>'',
		'to'=>'',
		'period'=>'all',

	),
	'users.staff.login_history'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle',
		'f_period'=>'all',

	),

	'account.users.suppliers'=>array(
		'view'=>'privilegies',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle'
	),
	'account.users.contractors'=>array(
		'view'=>'privilegies',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle'
	), 'account.users.agents'=>array(
		'view'=>'privilegies',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle'
	),
	'account.deleted.users'=>array(
		'view'=>'privilegies',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle'
	),
	'billingregion_taxcategory'=>array(
		'view'=>'overview',
		'sort_key'=>'billing_region',
		'sort_order'=>1,
		'rpp'=>100,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'from'=>'',
		'to'=>'',
		'period'=>'last_m',
		'excluded_stores'=>array()
	),

	'billingregion_taxcategory.invoices'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['invoices']


	),
	'billingregion_taxcategory.refunds'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'export_fields'=>$export_fields['invoices']

	),
	'category.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>''
	),
	'category.categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'category.images'=>array(
		'view'=>'overview',
		'sort_key'=>'order',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'caption'
	),
	'subject_categories'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),

	'deals'=>array(
		'view'=>'overview',
		'sort_key'=>'name',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'elements_type'=>each($elements_options['deals'])['key'],
		'elements'=>$elements_options['deals'],
	),
	'campaigns'=>array(
		'view'=>'overview',
		'sort_key'=>'name',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'elements_type'=>each($elements_options['campaigns'])['key'],
		'elements'=>$elements_options['campaigns'],
	),
	'campaign.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'elements_type'=>each($elements_options['campaign_history'])['key'],
		'elements'=>$elements_options['campaign_history']
	),
	'campaign.deals'=>array(
		'view'=>'overview',
		'sort_key'=>'name',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'elements_type'=>each($elements_options['deals'])['key'],
		'elements'=>$elements_options['deals'],
	),
	'campaign.orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'number',
		'from'=>'',
		'to'=>'',
		'period'=>'all',
		'elements_type'=>each($elements_options['orders'])['key'],
		'elements'=>$elements_options['orders']
	),
	'deal.history'=>array(
		'view'=>'overview',
		'sort_key'=>'date',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'',
		'elements_type'=>each($elements_options['deal_history'])['key'],
		'elements'=>$elements_options['deal_history']
	),

);


$tab_defaults_alias=array(
	'customers.list'=>'customers'
);






?>
