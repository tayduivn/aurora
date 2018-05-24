<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 13 September 2015 18:30:16 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'class.Store.php';

require_once 'utils/ar_common.php';
require_once 'utils/table_functions.php';
require_once 'utils/object_functions.php';


if (!$user->can_view('customers')) {
    echo json_encode(
        array(
            'state' => 405,
            'resp'  => 'Forbidden'
        )
    );
    exit;
}


if (!isset($_REQUEST['tipo'])) {
    $response = array(
        'state' => 405,
        'resp'  => 'Non acceptable request (t)'
    );
    echo json_encode($response);
    exit;
}


$tipo = $_REQUEST['tipo'];

switch ($tipo) {
    case 'customers':
        customers(get_table_parameters(), $db, $user);
        break;
    case 'prospects':
        prospects(get_table_parameters(), $db, $user);
        break;
    case 'asset_customers':
        asset_customers(get_table_parameters(), $db, $user);
        break;
    case 'lists':
        lists(get_table_parameters(), $db, $user);
        break;
    case 'customers_server':
        customers_server(get_table_parameters(), $db, $user);
        break;
    case 'categories':
        categories(get_table_parameters(), $db, $user);
        break;
    case 'customers_geographic_distribution':
        customers_geographic_distribution(get_table_parameters(), $db, $user);
        break;
    case 'poll_queries':
        poll_queries(get_table_parameters(), $db, $user);
        break;
    case 'poll_query_options':
        poll_query_options(get_table_parameters(), $db, $user);
        break;
    case 'poll_query_answers':
        poll_query_answers(get_table_parameters(), $db, $user);
        break;
    case 'abandoned_cart_mail_list':
        abandoned_cart_mail_list(get_table_parameters(), $db, $user);
        break;
    case 'newsletter_mail_list':
        newsletter_mail_list(get_table_parameters(), $db, $user);
        break;
    default:
        $response = array(
            'state' => 405,
            'resp'  => 'Tipo not found '.$tipo
        );
        echo json_encode($response);
        exit;
        break;
}

function customers($_data, $db, $user) {


    if ($_data['parameters']['parent'] == 'favourites') {
        $rtext_label = 'customer who favored';
    } else {
        $rtext_label = 'customer';
    }

    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            if ($parameters['parent'] == 'category') {
                $category_other_value = $data['Other Note'];
            } else {
                $category_other_value = '';
            }


            if ($data['Customer Orders'] == 0) {
                $last_order_date = '';
            } else {
                $last_order_date = strftime(
                    "%e %b %y", strtotime($data['Customer Last Order Date']." +00:00")
                );
            }

            if ($data['Customer Orders Invoiced'] == 0 or $data['Customer Last Invoiced Order Date'] == '') {
                $last_invoice_date = '';
            } else {
                $last_invoice_date = strftime(
                    "%e %b %y", strtotime(
                                  $data['Customer Last Invoiced Order Date']." +00:00"
                              )
                );
            }




            $contact_since = strftime(
                "%e %b %y", strtotime($data['Customer First Contacted Date']." +00:00")
            );


            if ($data['Customer Billing Address Link'] == 'Contact') {
                $billing_address = '<i>'._('Same as Contact').'</i>';
            } else {
                $billing_address = $data['Customer Invoice Address Formatted'];
            }

            if ($data['Customer Delivery Address Link'] == 'Contact') {
                $delivery_address = '<i>'._('Same as Contact').'</i>';
            } elseif ($data['Customer Delivery Address Link'] == 'Billing') {
                $delivery_address = '<i>'._('Same as Billing').'</i>';
            } else {
                $delivery_address = $data['Customer Delivery Address Formatted'];
            }

            switch ($data['Customer Type by Activity']) {
                case 'ToApprove':
                    $activity = _('To be approved');
                    break;
                case 'Inactive':
                    $activity = _('Lost');
                    break;
                case 'Active':
                    $activity = _('Active');
                    break;
                case 'Prospect':
                    $activity = _('Prospect');
                    break;
                default:
                    $activity = $data['Customer Type by Activity'];
                    break;
            }


            if ($parameters['parent'] == 'store') {
                $link_format  = '/customers/%d/%d';
                $formatted_id = sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $parameters['parent_key'], $data['Customer Key'], $data['Customer Key']);

            } elseif ($parameters['parent'] == 'customer_poll_query_option' or $parameters['parent'] == 'customer_poll_query') {
                $link_format  = '/customers/%d/%d';
                $formatted_id = sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $data['Customer Store Key'], $data['Customer Key'], $data['Customer Key']);

            } else {
                $link_format = '/'.$parameters['parent'].'/%d/customer/%d';

                $formatted_id = sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $parameters['parent_key'], $data['Customer Key'], $data['Customer Key']);

            }


            $adata[] = array(
                'id'           => (integer)$data['Customer Key'],
                'store_key'    => $data['Customer Store Key'],
                'formatted_id' => $formatted_id,

                'name'         => $data['Customer Name'],
                'company_name' => $data['Customer Company Name'],
                'contact_name' => $data['Customer Main Contact Name'],

                'location' => $data['Customer Location'],

                'invoices'  => (integer)$data['Customer Orders Invoiced'],
                'email'     => $data['Customer Main Plain Email'],
                'telephone' => $data['Customer Main XHTML Telephone'],
                'mobile'    => $data['Customer Main XHTML Mobile'],
                'orders'    => number($data['Customer Orders']),

                'last_order'    => $last_order_date,
                'last_invoice'  => $last_invoice_date,
                'contact_since' => $contact_since,

                'other_value' => $category_other_value,

                'total_payments'            => money($data['Customer Payments Amount'], $currency),
                'total_invoiced_amount'     => money($data['Customer Invoiced Amount'], $currency),
                'total_invoiced_net_amount' => money($data['Customer Invoiced Net Amount'], $currency),


                'top_orders'       => percentage(
                    $data['Customer Orders Top Percentage'], 1, 2
                ),
                'top_invoices'     => percentage(
                    $data['Customer Invoices Top Percentage'], 1, 2
                ),
                'top_balance'      => percentage(
                    $data['Customer Balance Top Percentage'], 1, 2
                ),
                'top_profits'      => percentage(
                    $data['Customer Profits Top Percentage'], 1, 2
                ),
                'address'          => $data['Customer Contact Address Formatted'],
                'billing_address'  => $billing_address,
                'delivery_address' => $delivery_address,

                'activity'      => $activity,
                'logins'        => number($data['Customer Number Web Logins']),
                'failed_logins' => number($data['Customer Number Web Failed Logins']),
                'requests'      => number($data['Customer Number Web Requests']),


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        print "$sql\n";
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function lists($_data, $db, $user) {

    $rtext_label = 'list';
    include_once 'prepare_table/init.php';

    $sql = "select $fields from `List Dimension` CLD $where $wheref order by $order $order_direction limit $start_from,$number_results";

    $adata = array();
    if ($result = $db->query($sql)) {

        foreach ($result as $data) {
            switch ($data['List Type']) {
                case 'Static':
                    $customer_list_type = _('Static');
                    $items              = number($data['List Number Items']);
                    break;
                default:
                    $customer_list_type = _('Dynamic');
                    $items              = '~'.number(
                            $data['List Number Items']
                        );
                    break;

            }

            $adata[] = array(
                'id'            => (integer)$data['List key'],
                'type'          => $customer_list_type,
                'name'          => sprintf('<span class="link"  onclick="change_view(\'customers/list/%d\')">%s</span>', $data['List key'], $data['List Name']),
                'creation_date' => strftime(
                    "%a %e %b %Y %H:%M %Z", strtotime($data['List Creation Date']." +00:00")
                ),
                //'add_to_email_campaign_action'=>'<div class="buttons small"><button class="positive" onClick="add_to_email_campaign('.$data['List key'].')">'._('Add Emails').'</button></div>',
                'items'         => $items,
                'delete'        => '<img src="/art/icons/cross.png"/>'
            );

        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function categories($_data, $db, $user) {

    $rtext_label = 'category';
    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {

            switch ($data['Category Branch Type']) {
                case 'Root':
                    $level = _('Root');
                    break;
                case 'Head':
                    $level = _('Head');
                    break;
                case 'Node':
                    $level = _('Node');
                    break;
                default:
                    $level = $data['Category Branch Type'];
                    break;
            }
            $level = $data['Category Branch Type'];


            $adata[] = array(
                'id'                  => (integer)$data['Category Key'],
                'store_key'           => (integer)$data['Category Store Key'],
                'code'                => $data['Category Code'],
                'label'               => $data['Category Label'],
                'subjects'            => number(
                    $data['Category Number Subjects']
                ),
                'level'               => $level,
                'subcategories'       => number($data['Category Children']),
                'percentage_assigned' => percentage(
                    $data['Category Number Subjects'], ($data['Category Number Subjects'] + $data['Category Subjects Not Assigned'])
                )
            );

        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function customers_server($_data, $db, $user) {


    //print_r($_data);

    $rtext_label = 'store';
    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";


    $total_contacts                    = 0;
    $total_active_contacts             = 0;
    $total_new_contacts                = 0;
    $total_lost_contacts               = 0;
    $total_losing_contacts             = 0;
    $total_contacts_with_orders        = 0;
    $total_active_contacts_with_orders = 0;
    $total_new_contacts_with_orders    = 0;
    $total_lost_contacts_with_orders   = 0;
    $total_losing_contacts_with_orders = 0;
    $total_users                       = 0;


    if ($result = $db->query($sql)) {

        foreach ($result as $data) {

            $total_contacts += $data['Store Contacts'];

            $total_active_contacts             += $data['active'];
            $total_new_contacts                += $data['Store New Contacts'];
            $total_lost_contacts               += $data['Store Lost Contacts'];
            $total_losing_contacts             += $data['Store Losing Contacts'];
            $total_contacts_with_orders        += $data['Store Contacts With Orders'];
            $total_active_contacts_with_orders += $data['active_with_orders'];
            $total_new_contacts_with_orders    += $data['Store New Contacts With Orders'];
            $total_lost_contacts_with_orders   += $data['Store Lost Contacts With Orders'];
            $total_losing_contacts_with_orders += $data['Store Losing Contacts With Orders'];


            $contacts                    = number($data['Store Contacts']);
            $new_contacts                = number($data['Store New Contacts']);
            $active_contacts             = number($data['active']);
            $losing_contacts             = number(
                $data['Store Losing Contacts']
            );
            $lost_contacts               = number($data['Store Lost Contacts']);
            $contacts_with_orders        = number(
                $data['Store Contacts With Orders']
            );
            $new_contacts_with_orders    = number(
                $data['Store New Contacts With Orders']
            );
            $active_contacts_with_orders = number($data['active_with_orders']);
            $losing_contacts_with_orders = number(
                $data['Store Losing Contacts With Orders']
            );
            $lost_contacts_with_orders   = number(
                $data['Store Lost Contacts With Orders']
            );
            $total_users                 = $data['Store Total Users'];

            //  $contacts_with_orders=number($data['contacts_with_orders']);
            // $active_contacts=number($data['active_contacts']);
            // $new_contacts=number($data['new_contacts']);
            // $lost_contacts=number($data['lost_contacts']);
            // $new_contacts_with_orders=number($data['new_contacts']);


            /*
                if ($parameters['percentages']) {
                    $contacts_with_orders=percentage($data['contacts_with_orders'],$total_contacts_with_orders);
                    $active_contacts=percentage($data['active_contacts'],$total_active);
                    $new_contacts=percentage($data['new_contacts'],$total_new);
                    $lost_contacts=percentage($data['los_contactst'],$total_lost);
                    $contacts=percentage($data['contacts'],$total_contacts);
                    $new_contacts_with_orders=percentage($data['new_contacts'],$total_new_contacts);

                } else {
                    $contacts_with_orders=number($data['contacts_with_orders']);
                    $active_contacts=number($data['active_contacts']);
                    $new_contacts=number($data['new_contacts']);
                    $lost_contacts=number($data['lost_contacts']);
                    $contacts=number($data['contacts']);
                    $new_contacts_with_orders=number($data['new_contacts']);

                }
        */
            $adata[] = array(
                'store_key'                   => $data['Store Key'],
                'code'                        => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Store Key'], $data['Store Code']),
                'name'                        => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Store Key'], $data['Store Name']),
                'contacts'                    => (integer)$data['Store Contacts'],
                'active_contacts'             => (integer)$data['active'],
                'new_contacts'                => (integer)$data['Store New Contacts'],
                'lost_contacts'               => (integer)$data['Store Lost Contacts'],
                'losing_contacts'             => (integer)$data['Store Losing Contacts'],
                'contacts_with_orders'        => $contacts_with_orders,
                'active_contacts_with_orders' => $active_contacts_with_orders,
                'new_contacts_with_orders'    => $new_contacts_with_orders,
                'lost_contacts_with_orders'   => $lost_contacts_with_orders,
                'losing_contacts_with_orders' => $losing_contacts_with_orders,
                'users'                       => $total_users


            );

        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    if ($parameters['percentages']) {
        $sum_total        = '100.00%';
        $sum_active       = '100.00%';
        $sum_new          = '100.00%';
        $sum_lost         = '100.00%';
        $sum_contacts     = '100.00%';
        $sum_new_contacts = '100.00%';
    } else {
        // $total_contacts=number($total_contacts);
        // $total_active_contacts=number($total_active_contacts);
        // $total_new_contacts=number($total_new_contacts);
        // $total_lost_contacts=number($total_lost_contacts);
        // $total_losing_contacts=number($total_losing_contacts);
        // $total_contacts_with_orders=number($total_contacts_with_orders);
        // $total_active_contacts_with_orders=number($total_active_contacts_with_orders);
        // $total_new_contacts_with_orders=number($total_new_contacts_with_orders);
        // $total_lost_contacts_with_orders=number($total_lost_contacts_with_orders);
        // $total_losing_contacts_with_orders=number($total_losing_contacts_with_orders);

        // $sum_total=number($total_contacts_with_orders);
        // $sum_active=number($total_active_contacts);
        // $sum_new=number($total_new_contacts);
        // $sum_lost=number($total_lost_contacts);
        // $sum_contacts=number($total_contacts);
        // $sum_new_contacts=number($total_new_contacts);
    }


    $adata[] = array(
        'store_key'                   => '',
        'name'                        => '',
        'code'                        => _('Total').($filtered > 0 ? ' '.'<i class="fa fa-filter fa-fw"></i>' : ''),
        'contacts'                    => (integer)$total_contacts,
        'active_contacts'             => (integer)$total_active_contacts,
        'new_contacts'                => (integer)$total_new_contacts,
        'lost_contacts'               => (integer)$total_lost_contacts,
        'losing_contacts'             => (integer)$total_losing_contacts,
        'contacts_with_orders'        => (integer)$total_contacts_with_orders,
        'active_contacts_with_orders' => (integer)$total_active_contacts_with_orders,
        'new_contacts_with_orders'    => (integer)$total_new_contacts_with_orders,
        'lost_contacts_with_orders'   => (integer)$total_lost_contacts_with_orders,
        'losing_contacts_with_orders' => (integer)$total_losing_contacts_with_orders,
        'users'                       => (integer)$total_users


    );


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total
        )
    );
    echo json_encode($response);
}


function customers_geographic_distribution($_data, $db, $user) {


    $rtext_label = 'country';


    if ($_data['parameters']['parent'] == 'store') {
        $store           = get_object('Store', $_data['parameters']['parent_key']);
        $total_customers = $store->get('Store Contacts');
        $total_sales     = $store->get('Store Total Acc Invoiced Amount');

        $currency = $store->get('Store Currency Code');
    } else {
        exit('ar_customers_tables, todo E:1234a');
    }


    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            $adata[] = array(
                'id'      => (integer)$data['Country Key'],
                'country' => $data['Country Name'],
                'flag'    => sprintf('<img alt="%s" title="%s" src="/art/flags/%s.gif"/>', $data['Country 2 Alpha Code'], $data['Country 2 Alpha Code'].' '.$data['Country Name'], strtolower($data['Country 2 Alpha Code'])),

                'customers'            => number($data['customers']),
                'customers_percentage' => percentage($data['customers'], $total_customers),
                'invoices'             => number($data['invoices']),
                'sales'                => money($data['sales'], $currency),
                'sales_percentage'     => percentage($data['sales'], $total_sales),
                'sales_per_customer'   => money($data['sales_per_registration'], $currency),


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function abandoned_cart_mail_list($_data, $db, $user) {


    $rtext_label = 'recipient';


    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            $inactive_since = strftime("%e %b %y", strtotime($data['Order Date']." +00:00"));


            $customer_link_format = '/customers/%d/%d';
            $order_link_format    = '/orders/%d/%d';


            $adata[] = array(
                'id'           => (integer)$data['Customer Key'],
                'store_key'    => $data['Customer Store Key'],
                'formatted_id' => sprintf('<span class="link" onClick="change_view(\''.$customer_link_format.'\')">%06d</span>', $data['Order Store Key'], $data['Customer Key'], $data['Customer Key']),
                'order'        => sprintf('<span class="link" onClick="change_view(\''.$order_link_format.'\')">%s</span>', $data['Order Store Key'], $data['Order Key'], $data['Order Public ID']),

                'name'         => $data['Customer Name'],
                'company_name' => $data['Customer Company Name'],
                'contact_name' => $data['Customer Main Contact Name'],

                'email'          => $data['Customer Main Plain Email'],
                'inactive_since' => $inactive_since,
                'inactive_days'  => '<span title="'.sprintf(_('Inactive since %s'), $inactive_since).'">'.number($data['inactive_days']).'</span>'


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function newsletter_mail_list($_data, $db, $user) {


    $rtext_label = 'recipient';


    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            $customer_link_format = '/customers/%d/%d';


            $adata[] = array(
                'id'           => (integer)$data['Customer Key'],
                'store_key'    => $data['Customer Store Key'],
                'formatted_id' => sprintf('<span class="link" onClick="change_view(\''.$customer_link_format.'\')">%06d</span>', $data['Customer Store Key'], $data['Customer Key'], $data['Customer Key']),

                'name'         => $data['Customer Name'],
                'company_name' => $data['Customer Company Name'],
                'contact_name' => $data['Customer Main Contact Name'],

                'email' => $data['Customer Main Plain Email'],


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function poll_queries($_data, $db, $user) {


    if ($_data['parameters']['parent'] == 'store') {
        $store           = get_object('Store', $_data['parameters']['parent_key']);
        $total_customers = $store->get('Store Contacts');

    } else {
        exit('ar_customers_tables, todo E:1234a');
    }

    $rtext_label = 'poll query';

    $ordinal_formatter = new \NumberFormatter("en-GB", \NumberFormatter::ORDINAL);

    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            switch ($data['Customer Poll Query Type']) {
                case 'Options':
                    $type = _('Multiple choice');

                    $title_on_enough_options = _('Query will not be shown to customer until it has more than one option');

                    if ($data['Customer Poll Query Options'] == 0) {


                        $type            .= ' <span class="error">('._('Not options set').')</span>';
                        $in_registration = ($data['Customer Poll Query In Registration'] == 'Yes' ? '<i title="'.$title_on_enough_options.'" class="fa fa-check error discreet"></i>' : '<i class="fa fa-check discreet"></i>');
                        $in_profile      = ($data['Customer Poll Query In Profile'] == 'Yes' ? '<i title="'.$title_on_enough_options.'" class="fa fa-check error discreet"></i>' : '<i class="fa fa-check discreet"></i>');
                    } elseif ($data['Customer Poll Query Options'] == 1) {
                        $type            .= ' <span class="warning">('._('Only one options set').')</span>';
                        $in_registration = ($data['Customer Poll Query In Registration'] == 'Yes' ? '<i title="'.$title_on_enough_options.'" class="fa fa-check error warning"></i>' : '<i class="fa fa-check discreet"></i>');
                        $in_profile      = ($data['Customer Poll Query In Profile'] == 'Yes' ? '<i title="'.$title_on_enough_options.'" class="fa fa-check error warning"></i>' : '<i class="fa fa-check discreet"></i>');
                    } else {
                        $type .= ' ('.sprintf(
                                ngettext(
                                    "%s option", "%s options", $data['Customer Poll Query Options']
                                ), number($data['Customer Poll Query Options'])
                            ).')';

                        $in_registration = ($data['Customer Poll Query In Registration'] == 'Yes' ? '<i class="fa fa-check success"></i>' : '<i class="fa fa-check discreet"></i>');
                        $in_profile      = ($data['Customer Poll Query In Profile'] == 'Yes' ? '<i class="fa fa-check success"></i>' : '<i class="fa fa-check discreet"></i>');

                    }


                    break;
                case 'Open':
                    $type            = _('Open answer');
                    $in_registration = ($data['Customer Poll Query In Registration'] == 'Yes' ? '<i class="fa fa-check success"></i>' : '<i class="fa fa-check discreet"></i>');
                    $in_profile      = ($data['Customer Poll Query In Profile'] == 'Yes' ? '<i class="fa fa-check success"></i>' : '<i class="fa fa-check discreet"></i>');
                    break;
                default:
                    exit('error not customer poll query E1');
                    break;

            }


            $adata[] = array(
                'id'                   => (integer)$data['Customer Poll Query Key'],
                'type'                 => $type,
                'query'                => sprintf(
                    '<span class="link" onclick="change_view(\'/customers/%d/poll_query/%d\')" title="%s">%s</span>', $data['Customer Poll Query Store Key'], $data['Customer Poll Query Key'], $data['Customer Poll Query Label'], $data['Customer Poll Query Name']
                ),
                'label'                => $data['Customer Poll Query Label'],
                'in_registration'      => $in_registration,
                'in_profile'           => $in_profile,
                'customers'            => number($data['Customer Poll Query Customers']),
                'customers_percentage' => percentage($data['Customer Poll Query Customers'], $total_customers),

                'position' => $ordinal_formatter->format($data['Customer Poll Query Position']),


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function poll_query_options($_data, $db, $user) {


    if ($_data['parameters']['parent'] == 'Customer_Poll_Query') {
        $poll            = get_object('Customer_Poll_Query', $_data['parameters']['parent_key']);
        $total_customers = $poll->get('Customer Poll Query Customers');

    } else {
        exit('ar_customers_tables, todo E:1234a');
    }

    $rtext_label = 'poll option';

    $ordinal_formatter = new \NumberFormatter("en-GB", \NumberFormatter::ORDINAL);

    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            if ($data['Customer Poll Query Option Last Answered'] != '') {
                $last_chose =  strftime("%e %b %y", strtotime($data['Customer Poll Query Option Last Answered']." +00:00"))  ;
            } else {
                $last_chose = '';
            }


            $adata[] = array(
                'id'    => (integer)$data['Customer Poll Query Option Key'],
                'code'  => sprintf(
                    '<span class="link" onclick="change_view(\'/customers/%d/poll_query/%d/option/%d\')" title="%s">%s</span>', $data['Customer Poll Query Option Store Key'], $data['Customer Poll Query Option Query Key'], $data['Customer Poll Query Option Key'],
                    $data['Customer Poll Query Option Label'], $data['Customer Poll Query Option Name']
                ),
                'label' => $data['Customer Poll Query Option Label'],

                'customers'            => number($data['Customer Poll Query Option Customers']),
                'customers_percentage' => percentage($data['Customer Poll Query Option Customers'], $total_customers),
                'last_chose'           => $last_chose


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function poll_query_answers($_data, $db, $user) {


    $rtext_label = 'answer';


    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            $link_format = '/customers/%d/%d';


            $adata[] = array(
                'id'           => (integer)$data['Customer Poll Key'],
                'formatted_id' => sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $data['Customer Store Key'], $data['Customer Key'], $data['Customer Key']),
                'customer'     => $data['Customer Name'],
                'answer'       => $data['Customer Poll Reply'],
                'date'         => strftime("%e %b %y", strtotime($data['Customer Poll Date']." +00:00"))


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}



function asset_customers($_data, $db, $user) {


    if ($_data['parameters']['parent'] == 'favourites') {
        $rtext_label = 'customer who favored';
    } else {
        $rtext_label = 'customer';
    }

    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {






             if ($data['invoices']==0 or $data['last_invoice'] == '') {
                 $last_invoice_date = '';
                 $invoiced_amount='';
             } else {
                 $last_invoice_date = strftime(
                     "%e %b %y", strtotime(
                                   $data['last_invoice']." +00:00"
                               )
                 );
                 $invoiced_amount=money($data['invoiced_amount'],$data['Invoice Currency Code']);
             }



            switch ($data['Customer Type by Activity']) {
                case 'ToApprove':
                    $activity = _('To be approved');
                    break;
                case 'Inactive':
                    $activity = _('Lost');
                    break;
                case 'Active':
                    $activity = _('Active');
                    break;
                case 'Prospect':
                    $activity = _('Prospect');
                    break;
                default:
                    $activity = $data['Customer Type by Activity'];
                    break;
            }



                $link_format  = '/customers/%d/%d';
                $formatted_id = sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $data['Customer Store Key'], $data['Customer Key'], $data['Customer Key']);




            $adata[] = array(
                'id'           => (integer)$data['Customer Key'],
                'store_key'    => $data['Customer Store Key'],
                'formatted_id' => $formatted_id,
                'name'         => $data['Customer Name'],
                'location' => $data['Customer Location'],
                'invoices'  => $data['invoices'],
                'last_invoice'  => $last_invoice_date,
                'activity'      => $activity,
                'invoiced_amount'=>$invoiced_amount,
                'favourited'=>'<span class="'.(!$data['favourited']?'super_discreet':'').'">'.number($data['favourited']).'</span>',
                'basket_amount'=>($data['basket_amount']==0?'':money($data['basket_amount'],$data['Invoice Currency Code']))



            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        print "$sql\n";
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}



function prospects($_data, $db, $user) {


        $rtext_label = 'prospect';
    

    include_once 'prepare_table/init.php';

    $sql = "select  $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {



            $contact_since = strftime(
                "%e %b %y", strtotime($data['Prospect First Contacted Date']." +00:00")
            );



            switch ($data['Prospect Status']) {
                case 'NoContacted':
                    $activity = _('To be contacted');
                    break;
                case 'Contacted':
                    $activity = _('Contacted');
                    break;
                case 'NotInterested':
                    $activity = _('Not interested');
                    break;
                case 'Registered':
                    $activity = _('Registered');
                    break;
                default:
                    $activity = $data['Prospect Type by Activity'];
                    break;
            }


            if ($parameters['parent'] == 'store') {
                $link_format  = '/prospects/%d/%d';
                $formatted_id = sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $parameters['parent_key'], $data['Prospect Key'], $data['Prospect Key']);

            } elseif ($parameters['parent'] == 'prospect_poll_query_option' or $parameters['parent'] == 'prospect_poll_query') {
                $link_format  = '/prospects/%d/%d';
                $formatted_id = sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $data['Prospect Store Key'], $data['Prospect Key'], $data['Prospect Key']);

            } else {
                $link_format = '/'.$parameters['parent'].'/%d/prospect/%d';

                $formatted_id = sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%06d</span>', $parameters['parent_key'], $data['Prospect Key'], $data['Prospect Key']);

            }


            $adata[] = array(
                'id'           => (integer)$data['Prospect Key'],
                'store_key'    => $data['Prospect Store Key'],
                'formatted_id' => $formatted_id,

                'name'         => $data['Prospect Name'],
                'company_name' => $data['Prospect Company Name'],
                'contact_name' => $data['Prospect Main Contact Name'],

                'location' => $data['Prospect Location'],

                'invoices'  => (integer)$data['Prospect Orders Invoiced'],
                'email'     => $data['Prospect Main Plain Email'],
                'telephone' => $data['Prospect Main XHTML Telephone'],
                'mobile'    => $data['Prospect Main XHTML Mobile'],
                'orders'    => number($data['Prospect Orders']),

                'last_order'    => $last_order_date,
                'last_invoice'  => $last_invoice_date,
                'contact_since' => $contact_since,

                'other_value' => $category_other_value,

                'total_payments'            => money($data['Prospect Payments Amount'], $currency),
                'total_invoiced_amount'     => money($data['Prospect Invoiced Amount'], $currency),
                'total_invoiced_net_amount' => money($data['Prospect Invoiced Net Amount'], $currency),


                'top_orders'       => percentage(
                    $data['Prospect Orders Top Percentage'], 1, 2
                ),
                'top_invoices'     => percentage(
                    $data['Prospect Invoices Top Percentage'], 1, 2
                ),
                'top_balance'      => percentage(
                    $data['Prospect Balance Top Percentage'], 1, 2
                ),
                'top_profits'      => percentage(
                    $data['Prospect Profits Top Percentage'], 1, 2
                ),
                'address'          => $data['Prospect Contact Address Formatted'],
                'billing_address'  => $billing_address,
                'delivery_address' => $delivery_address,

                'activity'      => $activity,
                'logins'        => number($data['Prospect Number Web Logins']),
                'failed_logins' => number($data['Prospect Number Web Failed Logins']),
                'requests'      => number($data['Prospect Number Web Requests']),


            );
        }

    } else {
        print_r($error_info = $db->errorInfo());
        print "$sql\n";
        exit;
    }


    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $adata,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}

?>
