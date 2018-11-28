<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 3 November 2015 at 15:07:32 CET, Tessera, Italy
 Copyright (c) 2015, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/ar_common.php';
require_once 'utils/table_functions.php';
require_once 'utils/object_functions.php';


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
    case 'payment_service_providers':
        payment_service_providers(get_table_parameters(), $db, $user);
        break;
    case 'accounts':
        payment_accounts(get_table_parameters(), $db, $user);
        break;
    case 'stores':
        stores(get_table_parameters(), $db, $user);
        break;
    case 'payments':
    case 'order.payments':
    case 'invoice.payments':
    case 'refund.payments':
    case 'account.payments':

        payments(get_table_parameters(), $db, $user);
        break;

    case 'credits':
    case 'account.credits':

        credits(get_table_parameters(), $db, $user);
        break;
    case 'payments_group_by_store':

        payments_group_by_store(get_table_parameters(), $db, $user, $account);
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


function payment_service_providers($_data, $db, $user) {
    global $db, $account;
    $rtext_label = 'payment_service_provider';
    include_once 'prepare_table/init.php';

    $account_currency = $account->get('Account Currency');

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    foreach ($db->query($sql) as $data) {


        $other_currency = ($account_currency != $data['Payment Service Provider Currency']);

        $adata[] = array(
            'id'           => (integer)$data['Payment Service Provider Key'],
            'code'         => $data['Payment Service Provider Code'],
            'name'         => $data['Payment Service Provider Name'],
            'accounts'     => number(
                $data['Payment Service Provider Accounts']
            ),
            'transactions' => number(
                $data['Payment Service Provider Transactions']
            ),
            'payments'     => money(
                $data['Payment Service Provider Payments Amount'], $account_currency
            ),
            'refunds'      => money(
                $data['Payment Service Provider Refunds Amount'], $account_currency
            ),
            'balance'      => money(
                $data['Payment Service Provider Balance Amount'], $account_currency
            )
        );

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


function payment_accounts($_data, $db, $user) {
    global $db, $account;
    $rtext_label = 'payment_account';
    include_once 'prepare_table/init.php';

    $account_currency = $account->get('Account Currency');

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();


    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            //$other_currency = ($account_currency != $data['Payment Account Currency']);


           // print_r($data);

            if ($data['stores'] != '') {
                $stores='';
                foreach (preg_split('/\|/',$data['stores']) as $_store_data) {
                    $store_data=preg_split('/\,\:\,/',$_store_data);

                   // print $_store_data;
                    $stores.=sprintf('<span class="link" onclick="change_view(\'payment_accounts/%d\')">%s</span>, ',$store_data[0],$store_data[1]);
                }
                $stores=preg_replace('/\, $/','',$stores);
            } else {
                $stores = '';
            }


            $adata[] = array(
                'id'           => (integer)$data['Payment Account Key'],
                'code'         => $data['Payment Account Code'],
                'name'         => $data['Payment Account Name'],
                'transactions' => number($data['Payment Account Transactions']),
                'payments'     => money($data['Payment Account Payments Amount'], $account_currency),
                'refunds'      => money($data['Payment Account Refunds Amount'], $account_currency),
                'balance'      => money($data['Payment Account Balance Amount'], $account_currency),
                'stores'       => $stores
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


function payments($_data, $db, $user) {
    global $db, $account;
    $rtext_label = 'transactions';
    include_once 'prepare_table/init.php';


    if ($_data['parameters']['parent'] == 'store_payment_account') {

        $tmp = preg_split('/\_/', $_data['parameters']['parent_key']);

        $parent = get_object('payment_account', $tmp[1]);

    } else {
        $parent = get_object($_data['parameters']['parent'], $_data['parameters']['parent_key']);

    }


    $is_refund = false;


    /*
    if ($parent->get_object_name() == 'Order' and $parent->get('State Index') < 90) {
        $show_operations = true;

    } elseif ($parent->get_object_name() == 'Invoice' and $parent->get('Invoice Type') == 'Refund') {
        $show_operations = true;
        $is_refund       = true;
    } else {
        $show_operations = false;

    }
    */

    if ($parent->get_object_name() == 'Invoice' and $parent->get('Invoice Type') == 'Refund') {
        $is_refund = true;
    }

    $show_operations = true;

    $sql = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";

    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            switch ($data['Payment Type']) {
                case 'Payment':
                    $type          = _('Order payment');
                    $_remove_label = _('Cancel payment');
                    break;
                case 'Refund':
                    $type          = _('Refund pay back');
                    $_remove_label = _('Cancel refund');

                    break;
                case 'Credit':
                    $type          = _('Credit');
                    $_remove_label = _('Cancel credit');
                    break;
                default:
                    $type = $data['Payment Type'];
                    break;
            }


            $refund_amount = $data['Payment Transaction Amount Refunded'] + $data['Payment Transaction Amount Credited'];

            $refundable_amount = $data['Payment Transaction Amount'] - $refund_amount;


            if ($is_refund) {
                $to_refund = -1 * $parent->get('Invoice To Pay Amount');

                if ($to_refund < $refundable_amount) {
                    $refundable_amount = $to_refund;

                }

            }

            $operations = '';
            switch ($data['Payment Transaction Status']) {
                case 'Pending':
                    $status = _('Pending');
                    break;
                case 'Completed':
                    $status = _('Completed');


                    if ($data['Payment Account Block'] == 'Accounts') {
                        $operations = '';
                    } else {


                        $operations = sprintf(
                            '<span class="operations">
                            <i class="far fa-trash-alt button %s" aria-hidden="true" title="'.$_remove_label.'"  onClick="cancel_payment(this,%d)"  ></i>
                            <i class="fa fa-share fa-flip-horizontal button %s" data-settings=\'{"reference":"%s","amount_formatted":"%s","amount":"%s","can_refund_online":"%s"}\'   aria-hidden="true" title="'._('Refund/Credit payment').'"  onClick="open_refund_dialog(this,%d)"  ></i>

                            </span>', (($data['Payment Submit Type'] != 'Manual' or ($is_refund and $data['Payment Type'] != 'Refund')) ? 'hide' : ''), $data['Payment Key'],

                            (($data['Payment Type'] == 'Payment' and $refundable_amount > 0) ? '' : 'hide'),

                            htmlspecialchars($data['Payment Transaction ID']), money($refundable_amount, $data['Payment Currency Code']), $refundable_amount, ($data['Payment Account Block'] == 'BTree' ? true : false), $data['Payment Key']
                        );
                    }


                    break;
                case 'Cancelled':
                    $status = _('Cancelled');
                    break;

                case 'Error':
                    $status = _('Error');
                    break;
                case 'Declined':
                    $status = '<span class="error">'._('Declined').'</span>';
                    break;
                default:
                    $status = $data['Payment Transaction Status'];
                    break;
            }


            $status .= '<br><span class="small">'.$data['Payment Transaction Status Info'].'</span>';

            $notes = '';


            $amount = '<span class=" '.($data['Payment Transaction Amount'] < 0 ? 'error' : '').'  '.($data['Payment Transaction Status'] != 'Completed' ? 'strikethrough' : '').'" >'.money($data['Payment Transaction Amount'], $data['Payment Currency Code']).'</span>';


            $refunds = '';

            if ($data['Payment Transaction Amount Refunded'] != 0) {

                $refunds = '<span style="font-style: italic" class="discreet">'.money($data['Payment Transaction Amount Refunded'], $data['Payment Currency Code']).' '._('refunded').'</span>';
            }
            if ($data['Payment Transaction Amount Credited'] != 0) {
                $refunds .= ', <span style="font-style: italic" class="discreet">'.money($data['Payment Transaction Amount Credited'], $data['Payment Currency Code']).' '._('credited').'</span>';
            }

            $refunds = preg_replace('/^, /', '', $refunds);


            if ($data['Payment Account Block'] == 'Accounts') {
                $account = _('Customer credits');

            } else {
                $account = $data['Payment Account Code'];
            }


            if ($data['Order Key'] != '') {
                $order = sprintf(
                    "<span class='link' onclick='change_view(\"/orders/%d/%d\")' >%s</span>", $data['Order Store Key'], $data['Order Key'], $data['Order Public ID']
                );

            } else {
                $order = '';
            }

            if ($_data['parameters']['parent'] == 'store_payment_account') {
                $reference=sprintf(
                    "<span class='link' onclick='change_view(\"/payment/%d/%d\")' >%s</span>", $tmp[0],$data['Payment Key'], ($data['Payment Transaction ID'] == '' ? '<span class="discreet italic">'._('Reference missing').'</span>' : $data['Payment Transaction ID'])
                );
            }elseif ($_data['parameters']['parent'] == 'payment_account') {
                $reference=sprintf(
                    "<span class='link' onclick='change_view(\"/payment_account/%d/payment/%d\")' >%s</span>", $_data['parameters']['parent_key'],$data['Payment Key'], ($data['Payment Transaction ID'] == '' ? '<span class="discreet italic">'._('Reference missing').'</span>' : $data['Payment Transaction ID'])
                );
            }else{
                $reference=sprintf(
                    "<span class='link' onclick='change_view(\"/payment/%d\")' >%s</span>", $data['Payment Key'], ($data['Payment Transaction ID'] == '' ? '<span class="discreet italic">'._('Reference missing').'</span>' : $data['Payment Transaction ID'])
                );
            }




            $adata[] = array(
                'id'         => (integer)$data['Payment Key'],
                'currency'   => $data['Payment Currency Code'],
                'amount'     => $amount,
                'reference'  => $reference,
                'type'       => $type,
                'status'     => $status,
                'order'      => $order,
                'notes'      => $notes,
                'date'       => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Payment Last Updated Date'].' +0:00')),
                'operations' => ($show_operations ? $operations : ''),
                'refunds'    => $refunds,
                'account'    => $account,
                'store'      => sprintf("<span class='link' onclick='change_view(\"/payments/%d\")' title='%s'>%s</span>", $data['Store Key'], $data['Store Name'], $data['Store Code'])


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

function stores($_data, $db, $user) {


    $rtext_label = 'store';

    include_once 'prepare_table/init.php';

    $sql         = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $record_data = array();


    $max_length = 36;

    foreach ($db->query($sql) as $data) {


        if ($data['payment_account_data'] == '') {
            $data['Payment Account Store Key']          = '';
            $data['Payment Account Store Status']       = '';
            $data['Payment Account Store Show In Cart'] = '';
        } else {
            list($data['Payment Account Store Key'], $data['Payment Account Store Status'], $data['Payment Account Store Show In Cart']) = preg_split('/,/', $data['payment_account_data']);

        }


        $name = (strlen($data['Store Name']) > $max_length ? substr($data['Store Name'], 0, $max_length)."..." : $data['Store Name']);


        if ($data['Payment Account Store Status'] == '') {
            $accepted         = sprintf('<span class="very_discreet ">%s</span>', _('No applicable'));
            $shown_in_website = '';
        } else {
            if ($data['Payment Account Store Status'] == 'Active') {
                $accepted = sprintf('<span class="success button ">%s</span>', _('Yes'));

                if ($data['Payment Account Store Show In Cart'] == 'Yes') {
                    $shown_in_website = sprintf('<span class="success button ">%s</span>', _('Yes'));

                } else {
                    $shown_in_website = sprintf('<span class="error discreet button ">%s</span>', _('No'));
                }

            } else {
                $accepted         = sprintf('<span class="error discreet button ">%s</span>', _('No'));
                $shown_in_website = '';
            }
        }


        $record_data[] = array(
            'access' => (in_array($data['Store Key'], $user->stores) ? '' : '<i class="fa fa-lock "></i>'),

            'id'      => (integer)$data['Store Key'],
            'code'    => sprintf('<span class="link" onClick="change_view(\'store/%d\')" >%s</span>', $data['Store Key'], $data['Store Code']),
            'name'    => sprintf('<span class="link" onClick="change_view(\'store/%d\')" >%s</span>', $data['Store Key'], $name),
            'website' => sprintf('<span class="link" onClick="change_view(\'store/%d/website\')" title="%s" >%s</span>', $data['Store Key'], $data['Website Name'], $data['Website Code']),

            'accepted'         => $accepted,
            'shown_in_website' => $shown_in_website


        );

    }

    $response = array(
        'resultset' => array(
            'state'         => 200,
            'data'          => $record_data,
            'rtext'         => $rtext,
            'sort_key'      => $_order,
            'sort_dir'      => $_dir,
            'total_records' => $total

        )
    );
    echo json_encode($response);
}


function credits($_data, $db, $user) {
    global $db, $account;
    $rtext_label = 'customer with credit';
    include_once 'prepare_table/init.php';


    $sql = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";


    $adata = array();

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            $adata[] = array(
                'id'          => (integer)$data['Customer Key'],
                'amount'      => money($data['Customer Account Balance'], $data['Store Currency Code']),
                'store'       => sprintf("<span class='link' onclick='change_view(\"/credits/%d\")' title='%s'>%s</span>", $data['Store Key'], $data['Store Name'], $data['Store Code']),
                'customer_id' => sprintf("<span class='link' onclick='change_view(\"/customers/%d/%d\")' >%05d</span>", $data['Store Key'], $data['Customer Key'], $data['Customer Key']),
                'customer'    => sprintf("<span class='link' onclick='change_view(\"/customers/%d/%d\")' >%s</span>", $data['Store Key'], $data['Customer Key'], $data['Customer Name']),

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


function payments_group_by_store($_data, $db, $user, $account) {

    $rtext_label = 'store';
    include_once 'prepare_table/init.php';
    include_once 'utils/currency_functions.php';

    $sql = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";

    //print $sql;

    $total_payments        = 0;
    $total_payments_amount = 0;
    $total_credits         = 0;
    $total_credits_amount  = 0;

    $mix_currencies = false;

    if ($result = $db->query($sql)) {

        foreach ($result as $data) {


            if ($data['Store Currency Code'] != $account->get('Account Currency')) {


                if ($data['Store Total Acc Credits Amount'] != 0) {
                    $exchange = currency_conversion(
                        $db, $data['Store Currency Code'], $account->get('Account Currency'), '- 6 hours'
                    );

                    $mix_currencies       = true;
                    $total_credits_amount += $exchange * $data['Store Total Acc Credits Amount'];


                }


            } else {

                $total_credits_amount += $data['Store Total Acc Credits Amount'];

            }


            $total_payments        += $data['Store Total Acc Payments'];
            $total_payments_amount += $data['Store Total Acc Payments Amount'];
            $total_credits         += $data['Store Total Acc Credits'];


            $adata[] = array(
                'store_key'       => $data['Store Key'],
                'code'            => sprintf('<span class="link" onclick="change_view(\'orders/%d\')">%s</span>', $data['Store Key'], $data['Store Code']),
                'name'            => sprintf('<span class="link" onclick="change_view(\'orders/%d\')">%s</span>', $data['Store Key'], $data['Store Name']),
                'payments'        => sprintf('<span class=" %s">%s</span>', ($data['Store Total Acc Payments'] == 0 ? 'super_discreet' : ''), number($data['Store Total Acc Payments'])),
                'payments_amount' => sprintf('<span class=" %s">%s</span>', ($data['Store Total Acc Payments'] == 0 ? 'super_discreet' : ''), money($data['Store Total Acc Payments Amount'], $data['Store Currency Code'])),

                'credits'        => sprintf('<span class=" %s">%s</span>', ($data['Store Total Acc Credits'] == 0 ? 'super_discreet' : ''), number($data['Store Total Acc Credits'])),
                'credits_amount' => sprintf('<span class=" %s">%s</span>', ($data['Store Total Acc Credits'] == 0 ? 'super_discreet' : ''), money($data['Store Total Acc Credits Amount'], $data['Store Currency Code'])),


            );

        }

    } else {
        print_r($error_info = $db->errorInfo());
        exit;
    }


    $adata[] = array(
        'store_key' => '',
        'name'      => '',
        'code'      => _('Total').($filtered > 0 ? ' '.'<i class="fa fa-filter fa-fw"></i>' : ''),

        'payments'        => number($total_payments),
        'payments_amount' => sprintf('<span class=" %s">%s</span>', ($mix_currencies ? 'italic discreet' : ''), money($total_payments_amount, $account->get('Currency Code'))),
        'credits'         => number($total_credits),
        'credits_amount'  => sprintf('<span class=" %s">%s</span>', ($mix_currencies ? 'italic discreet' : ''), money($total_credits_amount, $account->get('Currency Code'))),

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


?>
