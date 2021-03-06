<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 16 January 2018 at 19:20:46 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/


$tab     = 'customers_poll.reply.customers';
$ar_file = 'ar_customers_tables.php';
$tipo    = 'poll_reply_customers';

$default = $user->get_tab_defaults($tab);


$table_views = array(
    'overview' => array(
        'label' => _('Overview'),
        'title' => _('Overview')
    ),
    'contact'  => array(
        'label' => _('Contact'),
        'title' => _('Contact details')
    ),
    'invoices' => array(
        'label' => _('Invoices/Balance'),
        'title' => _('Invoices & Account balance')
    ),
    'weblog'   => array(
        'label' => _('Weblog'),
        'title' => _('Weblog')
    )

);

$table_filters = array(
    'name'         => array(
        'label' => _('Name'),
        'title' => _('Customer name')
    ),
    'email'        => array(
        'label' => _('Email'),
        'title' => _('Customer email')
    ),
    'company_name' => array(
        'label' => _('Company name'),
        'title' => _('Company name')
    ),
    'contact_name' => array(
        'label' => _('Contact name'),
        'title' => _('Contact name')
    )

);


include('utils/get_table_html.php');


?>
