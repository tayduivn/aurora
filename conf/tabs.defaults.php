<?php
/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 22 September 2015 12:09:42 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

$default_rrp_options=array(500, 100, 50, 20);

$tab_defaults=array(

	'customers'=>array(
		'view'=>'overview',
		'sort_key'=>'formated_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
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
	'customers_server'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'percentages'=>0
	),
	'orders'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'customer',
		'from'=>'',
		'to'=>'',
		'period'=>'ytd',
		'elements_type'=>''
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
		'period'=>'ytd',
		'elements_type'=>''
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
		'period'=>'ytd',
		'elements_type'=>''
	),
	'orders_server'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>-1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'percentages'=>0
	), 'stores'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	), 'store.departments'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	), 'store.families'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	), 'department.families'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	), 'store.products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	), 'department.products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	), 'family.products'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
		'f_period'=>'ytd',

	), 'websites'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	), 'website.pages'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),'marketing_server'=>array(
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
		'sort_key'=>'formated_id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name',
		'f_period'=>'ytd',
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
	),'warehouses'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
	),
	'locations'=>array(
		'view'=>'overview',
		'sort_key'=>'code',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code'
	),
	'locations.replenishments'=>array(
		'view'=>'overview',
		'sort_key'=>'location',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'location'
	),
	'inventory.parts'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'reference'
	), 'employees'=>array(
		'view'=>'employees',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'name'
	),
	'reports'=>array(
		'view'=>'overview',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'code',
	),
	'users.staff.users'=>array(
		'view'=>'privilegies',
		'sort_key'=>'id',
		'sort_order'=>1,
		'rpp'=>20,
		'rpp_options'=>$default_rrp_options,
		'f_field'=>'handle'
	),

);


$tab_defaults_alias=array(
	'customers.list'=>'customers'
);




?>
