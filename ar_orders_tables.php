<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 23 September 2015 15:34:56 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/ar_common.php';
require_once 'utils/table_functions.php';
require_once 'utils/order_functions.php';
require_once 'utils/natural_language.php';
require_once 'utils/object_functions.php';

if (!$user->can_view('orders')) {
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
    case 'orders_in_process_not_paid':
        orders_in_process_not_paid(get_table_parameters(), $db, $user);
        break;
    case 'orders_in_process_paid':
        orders_in_process_paid(get_table_parameters(), $db, $user);
        break;
    case 'orders_in_process':
        orders_in_process(get_table_parameters(), $db, $user);
        break;

    case 'orders_in_warehouse':
        orders_in_warehouse(get_table_parameters(), $db, $user);
        break;
    case 'orders_in_warehouse_no_alerts':
        orders_in_warehouse_no_alerts(get_table_parameters(), $db, $user);
        break;
    case 'orders_in_warehouse_with_alerts':
        orders_in_warehouse_with_alerts(get_table_parameters(), $db, $user);
        break;
    case 'orders_packed_done':
        orders_packed_done(get_table_parameters(), $db, $user);
        break;
    case 'orders_approved':
        orders_approved(get_table_parameters(), $db, $user);
        break;
    case 'orders_dispatched_today':
        orders_dispatched_today(get_table_parameters(), $db, $user);
        break;

    case 'archived_orders':
        archived_orders(get_table_parameters(), $db, $user);
        break;


    case 'orders_server':
        orders_server(get_table_parameters(), $db, $user);
        break;
    case 'orders':
        orders(get_table_parameters(), $db, $user);
        break;

    case 'invoices':
        invoices(get_table_parameters(), $db, $user);
        break;
    case 'delivery_notes':
        delivery_notes(get_table_parameters(), $db, $user);
        break;
    case 'pending_delivery_notes':
        pending_delivery_notes(get_table_parameters(), $db, $user);
        break;
    case 'orders_index':
        orders_index(get_table_parameters(), $db, $user);
        break;
    case 'orders_group_by_store':
        orders_group_by_store(get_table_parameters(), $db, $user);
        break;

    case 'invoice_categories':
        invoice_categories(get_table_parameters(), $db, $user);
        break;
    case 'order.items':
        order_items(get_table_parameters(), $db, $user);
        break;
    case 'invoice.items':
        invoice_items(get_table_parameters(), $db, $user);
        break;
    case 'delivery_note_cancelled.items':
        delivery_note_cancelled_items(get_table_parameters(), $db, $user);
        break;
    case 'delivery_note.items':
        delivery_note_items(get_table_parameters(), $db, $user);
        break;
    case 'delivery_note.fast_track_packing':
        delivery_note_fast_track_packing(get_table_parameters(), $db, $user);
        break;
    case 'invoice_categories':
        invoice_categories(get_table_parameters(), $db, $user);
        break;
    case 'orders_in_website':

        orders_in_website(get_table_parameters(), $db, $user);
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


function orders_in_process_not_paid($_data, $db, $user) {
    $rtext_label = 'order submitted not paid';



    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';
        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        $operations .= sprintf(
            "<i id=\"send_to_warehouse_button_%d\" class=\"%s fa fa-hand-lizard-o fa-flip-horizontal button edit \" onClick=\"create_delivery_note_from_list(this,%d)\" title='%s'></i>",
            $data['Order Key'], ($data['Order Number Items'] == 0 ? 'disabled' : ''), $data['Order Key'], _('Send for picking')
        );

        //$operations.=sprintf("<button onClick=\"location.href='order.php?id=%d&referral=store_pending_orders'\"><img style='height:12px;width:12px' src='art/icons/cart_edit.png'> %s</button>",$data['Order Key'],_('Edit Order'));

        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_in_process_paid($_data, $db, $user) {
    $rtext_label = 'order submitted paid';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';
        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        $operations .= sprintf(
            "<i id=\"send_to_warehouse_button_%d\" class=\"%s fa fa-hand-lizard-o fa-flip-horizontal button edit \" onClick=\"create_delivery_note_from_list(this,%d)\" title='%s'></i>",
            $data['Order Key'], ($data['Order Number Items'] == 0 ? 'disabled' : ''), $data['Order Key'], _('Send for picking')
        );

        //$operations.=sprintf("<button onClick=\"location.href='order.php?id=%d&referral=store_pending_orders'\"><img style='height:12px;width:12px' src='art/icons/cart_edit.png'> %s</button>",$data['Order Key'],_('Edit Order'));

        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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

function orders_in_warehouse($_data, $db, $user) {
    $rtext_label = 'order warehouse';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';


        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error  padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        foreach (preg_split('/,/', $data['delivery_notes']) as $delivery_note_data) {
            $operations .= sprintf(
                "<i class=\"fa fa-truck fa-flip-horizontal   button\" onClick=\"change_view('delivery_notes/%d/%d')\"></i>", $data['Order Store Key'], $delivery_note_data

            );
        }


        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_in_warehouse_no_alerts($_data, $db, $user) {
    $rtext_label = 'order warehouse';



    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';


        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error  padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        foreach (preg_split('/,/', $data['delivery_notes']) as $delivery_note_data) {
            $operations .= sprintf(
                "<i class=\"fa fa-truck fa-flip-horizontal   button\" onClick=\"change_view('delivery_notes/%d/%d')\"></i>", $data['Order Store Key'], $delivery_note_data

            );
        }


        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_in_warehouse_with_alerts($_data, $db, $user) {
    $rtext_label = 'order warehouse with alerts';



    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';


        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error  padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        foreach (preg_split('/,/', $data['delivery_notes']) as $delivery_note_data) {
            $operations .= sprintf(
                "<i class=\"fa fa-truck fa-flip-horizontal   button\" onClick=\"change_view('delivery_notes/%d/%d')\"></i>", $data['Order Store Key'], $delivery_note_data

            );
        }


        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_packed_done($_data, $db, $user) {
    $rtext_label = 'order packed done';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';


        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error  padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        foreach (preg_split('/,/', $data['delivery_notes']) as $delivery_note_data) {
            $operations .= sprintf(
                "<i class=\"fa fa-truck fa-flip-horizontal   button\" onClick=\"change_view('delivery_notes/%d/%d')\"></i>", $data['Order Store Key'], $delivery_note_data

            );
        }


        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_approved($_data, $db, $user) {
    $rtext_label = 'order approved';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';


        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error  padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        foreach (preg_split('/,/', $data['delivery_notes']) as $delivery_note_data) {
            $operations .= sprintf(
                "<i class=\"fa fa-truck fa-flip-horizontal   button\" onClick=\"change_view('delivery_notes/%d/%d')\"></i>", $data['Order Store Key'], $delivery_note_data

            );
        }


        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_dispatched_today($_data, $db, $user) {
    $rtext_label = 'order dispatched today';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        $operations .= '<div class="buttons small '.$class.'">';


        $operations .= sprintf(
            "<i class=\"fa fa-minus-circle error  padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
            $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
        );

        foreach (preg_split('/,/', $data['delivery_notes']) as $delivery_note_data) {
            $operations .= sprintf(
                "<i class=\"fa fa-truck fa-flip-horizontal   button\" onClick=\"change_view('delivery_notes/%d/%d')\"></i>", $data['Order Store Key'], $delivery_note_data

            );
        }


        $operations .= '</div>';


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => sprintf(
                '<span class="link"  onclick="change_view(\'orders/%s/%d\')" >%s</span>', ($_data['parameters']['parent'] == 'store' ? $_data['parameters']['parent_key'] : 'all'), $data['Order Key'],
                $data['Order Public ID']
            ),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_in_process_old($_data, $db, $user) {
    $rtext_label = 'order';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        switch ($data['Order Current Payment State']) {
            case 'No Applicable':
                ///$payment_state='<span style="opacity:.6">'._('No Applicable').'</span>';
                $payment_state = '';
                break;
            case 'Waiting Payment':

                $payment_state = '<i class="fa fa-check super_discreet" aria-hidden="true"></i>';


                break;
            case 'Overpaid':
                $payment_state = _('Overpaid');

                break;
            case 'Unknown':
                $payment_state = '<i class="fa fa-question" aria-hidden="true"></i>';

                break;
            case 'Paid':
                $payment_state = '<i class="fa fa-check success" aria-hidden="true"></i>';
                break;
            case 'Partially Paid':

                $payment_state = '<i class="fa fa-check discreet warning" aria-hidden="true"></i>';

                break;
            default:
                $payment_state = $data['Order Current Payment State'];
        }
        if ($payment_state != '') {
            $payment_state = '<span id="payment_state_'.$data['Order Key'].'">'.$payment_state.'</span>';
        }


        include_once 'class.Order.php';

        $operations = '<div id="operations'.$data['Order Key'].'">';
        $class      = 'right';


        if ($data['Order State'] == 'InProcess') {
            $operations .= '<div class="buttons small '.$class.'">';
            $operations .= sprintf(
                "<i class=\"fa fa-minus-circle error padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
                $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
            );

            $operations .= sprintf(
                "<i id=\"send_to_warehouse_button_%d\" class=\"%s fa fa-hand-lizard-o fa-flip-horizontal button edit \" onClick=\"create_delivery_note_from_list(this,%d)\" title='%s'></i>",
                $data['Order Key'], ($data['Order Number Items'] == 0 ? 'disabled' : ''), $data['Order Key'], _('Send for picking')
            );

            //$operations.=sprintf("<button onClick=\"location.href='order.php?id=%d&referral=store_pending_orders'\"><img style='height:12px;width:12px' src='art/icons/cart_edit.png'> %s</button>",$data['Order Key'],_('Edit Order'));

            $operations .= '</div>';

        } elseif ($data['Order State'] == 'InBasket') {
            $operations .= '<div class="buttons small '.$class.'">';


            $operations .= sprintf(
                "<i class=\"fa fa-minus-circle error padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
                $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
            );

            if ($data['Order Number Items'] > 0) {

                $operations .= sprintf(
                    "<i id=\"send_to_warehouse_button_%d\" class=\"%s fa fa-hand-lizard-o fa-flip-horizontal button edit \" onClick=\"create_delivery_note_from_list(this,%d)\" title='%s'></i>",
                    $data['Order Key'], ($data['Order Number Items'] == 0 ? 'disabled' : ''), $data['Order Key'], _('Send for picking')
                );
            }


            $operations .= '</div>';

        } elseif (in_array(
            $data['Order State'], array(
                                    'InWarehouse',

                                )
        )) {

            $operations .= '<div class="buttons small '.$class.'">';


            $operations .= sprintf(
                "<i class=\"fa fa-minus-circle error  padding_right_10 button edit\" onClick=\"open_cancel_dialog_from_list(this,%d,'%s, %s')\" title='%s'></i>", $data['Order Key'],
                $data['Order Public ID'], $data['Order Customer Name'], _('Cancel')
            );

            foreach (preg_split('/,/', $data['delivery_notes']) as $delivery_note_data) {
                $operations .= sprintf(
                    "<i class=\"fa fa-truck fa-flip-horizontal   button\" onClick=\"change_view('delivery_notes/%d/%d')\"></i>", $data['Order Store Key'], $delivery_note_data

                );
            }


            $operations .= '</div>';

        } elseif ($data['Order State'] == 'PackedDone') {

            $operations .= '<div class="buttons small '.$class.'">';
            if ($data['Order Invoiced'] == 'No') {
                $operations .= '<button  onClick="create_invoice(this,'.$data['Order Key'].')"><img id="create_invoice_img_'.$data['Order Key']
                    .'" style="height:12px;width:12px" src="/art/icons/money.png"> '._('Create Invoice')."</button>";;
            } else {
                $operations .= '<button  onClick="approve_dispatching(this,'.$data['Order Key'].')"><img id="approve_dispatching_img_'.$data['Order Key']
                    .'" style="height:12px;width:12px" src="/art/icons/package_green.png"> '._('Approve Dispatching')."</button>";;


            }
            $operations .= '</div>';

        } elseif ($data['Order State'] == 'Approved') {
            $operations .= '<div class="buttons small '.$class.'">';
            $order      = new Order($data['Order Key']);
            $dns        = $order->get_delivery_notes_objects();
            if (count($dns) == 1) {
                foreach ($dns as $dn) {

                    $operations .= '<button  onClick="set_as_dispatched('.$dn->data['Delivery Note Key'].','.$user->get_staff_key().',\'order\',\''.$data['Order Key']
                        .'\')" ><img id="set_as_dispatched_img_'.$dn->data['Delivery Note Key'].'" src="/art/icons/lorry_go.png" alt=""> '._(
                            'Mark as Dispatched'
                        )."</button>";
                }
            }

            $operations .= '</div>';

        }


        $operations .= '</div>';


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'checked'        => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'store_key'      => (integer)$data['Order Store Key'],
            'public_id'      => $data['Order Public ID'],
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => $payment_state,
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),
            'actions'        => $operations


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


function orders_in_website($_data, $db, $user) {
    $rtext_label = 'order in basket';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //   print $sql;
    foreach ($db->query($sql) as $data) {


        $adata[] = array(
            'id'           => (integer)$data['Order Key'],
            'checked'      => sprintf('<i class="fa fa-square-o fa-fw button"  aria-hidden="true" onClick="select_order(this)"></i>'),
            'public_id'    => sprintf('<span class="link" onClick="change_view(\'orders/%d/%d\')">%s</span>', $data['Order Store Key'], $data['Order Key'], $data['Order Public ID']),
            'date'         => strftime("%e %b %Y", strtotime($data['Order Created Date'].' +0:00')),
            'last_updated' => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'     => sprintf('<span class="link" onClick="change_view(\'customers/%d/%d\')">%s</span>', $data['Order Store Key'], $data['Order Customer Key'], $data['Order Customer Name']),
            'total_amount' => money($data['Order Total Amount'], $data['Order Currency']),
            'idle_time'    => number($data['idle_time'])


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


function archived_orders($_data, $db, $user) {
    $rtext_label = 'order';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();


    foreach ($db->query($sql) as $data) {


        switch ($data['Order State']) {
            case 'Dispatched':
                $dispatch_state = '<i class="fa fa-paper-plane" aria-hidden="true" tile="'._('Dispatched').'" ></i>';
                break;
            case 'Cancelled':
                $dispatch_state = '<i class="fa fa-minus-circle error" aria-hidden="true" tile="'._('Cancelled').'" ></i>';
                break;
            default:
                $dispatch_state = '<i class="fa fa-question warning" aria-hidden="true" tile="'.$data['Order State'].'" ></i>';
                break;
        }

        $adata[] = array(
            'id' => (integer)$data['Order Key'],

            'dispatch_state' => $dispatch_state,
            'public_id'      => sprintf('<span class="link" onClick="change_view(\'orders/%d/%d\')">%s</span>', $data['Order Store Key'], $data['Order Key'], $data['Order Public ID']),
            'date'           => strftime("%a %e %b %Y", strtotime($data['Order Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d\')">%s</span>', $data['Order Customer Key'], $data['Order Customer Name']),
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),


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


function orders_server($_data, $db, $user) {
    $rtext_label = 'order';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();


    $link_format = '/orders/all/%d';


    //'InBasket','InProcess','InWarehouse','PackedDone','
    //
    //
    //DispatchAproved','Dispatched','Cancelled'


    foreach ($db->query($sql) as $data) {

        switch ($data['Order State']) {
            case('InBasket'):
                $state = _('In Basket');
                break;
            case('InProcess'):
                $state = _('Submitted');
                break;
            case('InWarehouse'):
                $state = _('In Warehouse');
                break;
            case('PackedDone'):
                $state = _('Packed Done');
                break;
            case('Dispatch Approved'):
                $state = _('Dispatch Approved');
                break;
            case('Dispatched'):
                $state = _('Dispatched');
                break;
            case('Cancelled'):
                $state = _('Cancelled');
                break;
            default:
                $state = $data['Order State'];

        }


        $adata[] = array(
            'id'             => (integer)$data['Order Key'],
            'store'          => sprintf('<span class="link" onClick="change_view(\'/orders/%d\')">%s</span>', $data['Order Store Key'], $data['Store Code']),
            'state'          => $state,
            'public_id'      => sprintf('<span class="link" onClick="change_view(\'/orders/all/%d\')">%s</span>', $data['Order Key'], $data['Order Public ID']),
            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d/%d\')">%s</span>', $data['Order Store Key'], $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => get_order_formatted_payment_state($data),
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),


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

function orders($_data, $db, $user) {
    $rtext_label = 'order';


    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();


    if ($parameters['parent'] == 'store') {
        $link_format = '/orders/%d/%d';
    } else {
        $link_format = '/'.$parameters['parent'].'/%d/order/%d';
    }


    foreach ($db->query($sql) as $data) {


        switch ($data['Order State']) {
            case('InBasket'):
                $state = _('In Basket');
                break;
            case('InProcess'):
                $state = _('Submitted');
                break;
            case('InWarehouse'):
                $state = _('In Warehouse');
                break;
            case('PackedDone'):
                $state = _('Packed Done');
                break;
            case('Dispatch Approved'):
                $state = _('Dispatch Approved');
                break;
            case('Dispatched'):
                $state = _('Dispatched');
                break;
            case('Cancelled'):
                $state = _('Cancelled');
                break;
            default:
                $state = $data['Order State'];

        }


        $adata[] = array(
            'id' => (integer)$data['Order Key'],

            'public_id' => sprintf('<span class="link" onClick="change_view(\''.$link_format.'\')">%s</span>', $parameters['parent_key'], $data['Order Key'], $data['Order Public ID']),
            'state'     => $state,

            'date'           => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Date'].' +0:00')),
            'last_date'      => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Order Last Updated Date'].' +0:00')),
            'customer'       => sprintf('<span class="link" onClick="change_view(\'customers/%d/%d\')">%s</span>', $data['Order Store Key'], $data['Order Customer Key'], $data['Order Customer Name']),
            'dispatch_state' => get_order_formatted_dispatch_state($data['Order State'], $data['Order Key']),
            'payment_state'  => get_order_formatted_payment_state($data),
            'total_amount'   => money($data['Order Total Amount'], $data['Order Currency']),


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


function delivery_notes($_data, $db, $user) {


    $rtext_label = 'delivery_note';
    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";

    $adata = array();


    foreach ($db->query($sql) as $data) {


        $notes='';

        switch ($data['Delivery Note State']) {

            case 'Picker & Packer Assigned':
                $state= _('Picker & packer assigned');
                break;
            case 'Picking & Packing':
                $state= _('Picking & packing');
                break;
            case 'Packer Assigned':
                $state= _('Packer assigned');
                break;
            case 'Ready to be Picked':
                $state= _('Waiting');
                break;
            case 'Picker Assigned':
                $state= _('Picker assigned');
                break;
            case 'Picking':
                $state= _('Picking');
                break;
            case 'Picked':
                $state= _('Picked');
                break;
            case 'Packing':
                $state= _('Packing');
                break;
            case 'Packed':
                $state= _('Packed');
                break;
            case 'Approved':
                $state= _('Approved');
                $notes=sprintf('<a class="pdf_link " target=\'_blank\' href="/pdf/dn.pdf.php?id=%d"> <img style="width: 50px;height:16px;position: relative;top:2px" src="/art/pdf.gif"></a>',
                               $data['Delivery Note Key']
                    );
                break;
            case 'Dispatched':
                $state= _('Dispatched');
                $notes=sprintf('<a class="pdf_link " target=\'_blank\' href="/pdf/dn.pdf.php?id=%d"> <img style="width: 50px;height:16px;position: relative;top:2px" src="/art/pdf.gif"></a>',
                               $data['Delivery Note Key']
                );
                break;
            case 'Cancelled':
                $state= _('Cancelled');
                break;
            case 'Cancelled to Restock':
                $state= _('Cancelled to restock');
                break;
            case 'Packed Done':
                $state= _('Packed done');
                break;
            default:
                $state= $data['Delivery Note State'];
                break;
        }

        switch ($data['Delivery Note Type']) {
            case('Order'):
                $type = _('Order');
                break;
            case('Sample'):
                $type = _('Sample');
                break;
            case('Donation'):
                $type = _('Donation');
                break;
            case('Replacement'):
            case('Replacement & Shortages'):
                $type = _('Replacement');
                break;
            case('Shortages'):
                $type = _('Shortages');
                break;
            default:
                $type = $data['Delivery Note Type'];

        }

        switch ($data['Delivery Note Parcel Type']) {
            case('Pallet'):
                $parcel_type = ' <i class="fa fa-calendar  fa-flip-vertical" aria-hidden="true"></i>';
                break;
            case('Envelope'):
                $parcel_type = ' <i class="fa fa-envelope" aria-hidden="true"></i>';
                break;
            default:
                $parcel_type = ' <i class="fa fa-archive" aria-hidden="true"></i>';

        }

        if ($data['Delivery Note Number Parcels'] == '') {
            $parcels = '?';
        } elseif ($data['Delivery Note Parcel Type'] == 'Pallet' and $data['Delivery Note Number Boxes']) {
            $parcels = number($data['Delivery Note Number Parcels']).$parcel_type.' ('.$data['Delivery Note Number Boxes'].' b)';
        } else {
            $parcels = number($data['Delivery Note Number Parcels']).$parcel_type;
        }







        $adata[] = array(
            'id'           => (integer)$data['Delivery Note Key'],


            'number'   => sprintf('<span class="link" onclick="change_view(\'delivery_notes/%d/%d\')">%s</span>',$data['Delivery Note Store Key'],$data['Delivery Note Key'],$data['Delivery Note ID']),
            'customer' => sprintf('<span class="link" onclick="change_view(\'customers/%d/%d\')">%s</span>',$data['Delivery Note Store Key'],$data['Delivery Note Customer Key'],$data['Delivery Note Customer Name']),

            'date'    => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Delivery Note Date Created'].' +0:00')),
            'state'   => $data['Delivery Note XHTML State'],
            'weight'  => weight($data['Delivery Note Weight']),
            'parcels' => $parcels,
            'type'    => $type,
            'state'    => $state,
            'notes'    => $notes,

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


function pending_delivery_notes($_data, $db, $user) {


    $rtext_label = 'delivery_note';
    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref $group_by order by $order $order_direction limit $start_from,$number_results";

    $adata = array();


    foreach ($db->query($sql) as $data) {


        switch ($data['Delivery Note Type']) {
            case('Order'):
                $type = _('Order');
                break;
            case('Sample'):
                $type = _('Sample');
                break;
            case('Donation'):
                $type = _('Donation');
                break;
            case('Replacement'):
            case('Replacement & Shortages'):
                $type = _('Replacement');
                break;
            case('Shortages'):
                $type = _('Shortages');
                break;
            default:
                $type = $data['Delivery Note Type'];

        }

        switch ($data['Delivery Note Parcel Type']) {
            case('Pallet'):
                $parcel_type = 'P';
                break;
            case('Envelope'):
                $parcel_type = 'e';
                break;
            default:
                $parcel_type = 'b';

        }

        if ($data['Delivery Note Number Parcels'] == '') {
            $parcels = '?';
        } elseif ($data['Delivery Note Parcel Type'] == 'Pallet' and $data['Delivery Note Number Boxes']) {
            $parcels = number($data['Delivery Note Number Parcels']).' '.$parcel_type.' ('.$data['Delivery Note Number Boxes'].' b)';
        } else {
            $parcels = number($data['Delivery Note Number Parcels']).' '.$parcel_type;
        }


        $adata[] = array(
            'id'           => (integer)$data['Delivery Note Key'],
            'store_key'    => (integer)$data['Delivery Note Store Key'],
            'customer_key' => (integer)$data['Delivery Note Customer Key'],

            'number'   => $data['Delivery Note ID'],
            'customer' => $data['Delivery Note Customer Name'],

            'date'    => strftime("%a %e %b %Y %H:%M %Z", strtotime($data['Delivery Note Date Created'].' +0:00')),
            'state'   => $data['Delivery Note XHTML State'],
            'weight'  => weight($data['Delivery Note Weight']),
            'parcels' => $parcels,
            'type'    => $type,


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


function invoices($_data, $db, $user) {

    $rtext_label = 'invoice';
    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();

    //print $sql;

    foreach ($db->query($sql) as $data) {

        if ($data['Invoice Paid'] == 'Yes') {
            $state = _('Paid');
        } elseif ($data['Invoice Paid'] == 'Partially') {
            $state = _('Partially Paid');
        } else {
            $state = _('No Paid');
        }


        if ($data['Invoice Type'] == 'Invoice') {
            $type = _('Invoice');
        } elseif ($data['Invoice Type'] == 'CreditNote') {
            $type = _('Credit Note');
        } else {
            $type = _('Refund');
        }

        switch ($data['Invoice Main Payment Method']) {
            default:
                $method = $data['Invoice Main Payment Method'];
        }

        $adata[] = array(
            'id'           => (integer)$data['Invoice Key'],
            'store_key'    => (integer)$data['Invoice Store Key'],
            'customer_key' => (integer)$data['Invoice Customer Key'],

            'number'       => $data['Invoice Public ID'],
            'customer'     => $data['Invoice Customer Name'],
            'date'         => strftime(
                "%a %e %b %Y %H:%M %Z", strtotime($data['Invoice Date'].' +0:00')
            ),
            'total_amount' => money(
                $data['Invoice Total Amount'], $data['Invoice Currency']
            ),
            'net'          => money(
                $data['Invoice Total Net Amount'], $data['Invoice Currency']
            ),
            'shipping'     => money(
                $data['Invoice Shipping Net Amount'], $data['Invoice Currency']
            ),
            'items'        => money(
                $data['Invoice Items Net Amount'], $data['Invoice Currency']
            ),
            'type'         => $type,
            'method'       => $method,
            'state'        => $state,


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


function orders_group_by_store($_data, $db, $user) {

    $rtext_label = 'store';
    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";

    $total_orders         = 0;
    $total_invoices       = 0;
    $total_delivery_notes = 0;
    $total_payments       = 0;


    if ($result = $db->query($sql)) {

        foreach ($result as $data) {

            $total_orders         += $data['orders'];
            $total_invoices       += $data['invoices'];
            $total_delivery_notes += $data['delivery_notes'];
            $total_payments       += $data['payments'];

            $adata[] = array(
                'store_key'      => $data['Store Key'],
                'code'           => sprintf('<span class="link" onclick="change_view(\'orders/%d\')">%s</span>',$data['Store Key'],$data['Store Code']),
                'name'           => sprintf('<span class="link" onclick="change_view(\'orders/%d\')">%s</span>',$data['Store Key'],$data['Store Name']),
                'orders'         => number($data['orders']),
                'delivery_notes' => number($data['delivery_notes']),
                'invoices'       => sprintf('<span class="link" onclick="change_view(\'invoices/%d\')">%s</span>',$data['Store Key'],number($data['invoices'])),
                'payments'       => number($data['payments']),
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

        'orders'         => number($total_orders),
        'delivery_notes' => number($total_delivery_notes),
        'invoices'       => number($total_invoices),
        'payments'       => number($total_payments),

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

function orders_index($_data, $db, $user) {

    $rtext_label = 'store';
    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";

    $total_orders         = 0;
    $total_invoices       = 0;
    $total_delivery_notes = 0;
    $total_payments       = 0;


    if ($result = $db->query($sql)) {

        foreach ($result as $data) {

            $total_orders         += $data['orders'];
            $total_invoices       += $data['invoices'];
            $total_delivery_notes += $data['delivery_notes'];
            $total_payments       += $data['payments'];

            $adata[] = array(
                'store_key'      => $data['Store Key'],
                'code'           => $data['Store Code'],
                'name'           => $data['Store Name'],
                'orders'         => number($data['orders']),
                'delivery_notes' => number($data['delivery_notes']),
                'invoices'       => number($data['invoices']),
                'payments'       => number($data['payments']),
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

    }


    $adata[] = array(
        'store_key' => '',
        'name'      => '',
        'code'      => _('Total').($filtered > 0 ? ' '.'<i class="fa fa-filter fa-fw"></i>' : ''),

        'orders'         => number($total_orders),
        'delivery_notes' => number($total_delivery_notes),
        'invoices'       => number($total_invoices),
        'payments'       => number($total_payments),

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


function order_items($_data, $db, $user) {

    global $_locale;// fix this locale stuff

    $rtext_label = 'item';

    include_once 'prepare_table/init.php';


    $customer_order = get_object('Order', $_data['parameters']['parent_key']);


    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();
    foreach ($db->query($sql) as $data) {


        if ($data['Order Bonus Quantity'] != 0) {
            if ($data['Order Quantity'] != 0) {
                $quantity .= '<br/> +'.number($data['Order Bonus Quantity']).' '._('free');
            } else {
                $quantity = number($data['Order Bonus Quantity']).' '._('free');
            }
        }


        if (is_numeric($data['Product Availability'])) {
            $stock = number($data['Product Availability']);
        } else {
            $stock = '?';
        }

        $deal_info = '';
        if ($data['Deal Info'] != '') {
            $deal_info = '<br/> <span class="deal_info">'.$data['Deal Info'].'</span>';
        }

        $units    = $data['Product Units Per Case'];
        $name     = $data['Product History Name'];
        $price    = $data['Product History Price'];
        $currency = $data['Product Currency'];


        $description = '';
        if ($units > 1) {
            $description = number($units).'x ';
        }
        $description .= ' '.$name;
        if ($price > 0) {
            $description .= ' ('.money($price, $currency, $_locale).')';
        }


        $description .= ' <span style="color:#777">['.$stock.']</span> '.$deal_info;


        if ($data['Current Dispatching State'] == 'Out of Stock in Basket') {
            $description .= '<br> <span class="warning"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '._('Product out of stock, removed from basket').'</span>';
            $quantity    = number($data['Out of Stock Quantity']);

            $class = 'out_of_stock';

        }


        if (in_array(
            $customer_order->get('Order State'), array(
                                                   'InProcess',
                                                   'InWarehouse',
                                                   'InBasket'
                                               )
        )) {
            $quantity = sprintf(
                '<span    data-settings=\'{"field": "Order Quantity", "transaction_key":"%d","item_key":%d, "item_historic_key":%d ,"on":1 }\'   ><input class="order_qty width_50" value="%s" ovalue="%s"> <i onClick="save_item_qty_change(this)" class="fa  fa-plus fa-fw button" aria-hidden="true"></i></span>',
                $data['Order Transaction Fact Key'], $data['Product ID'], $data['Product Key'], $data['Order Quantity'] + 0, $data['Order Quantity'] + 0
            );
        } else {
            $quantity = number($data['Order Quantity']);
        }


        if (in_array(
            $customer_order->get('Order State'), array(
                                                   'Cancelled',
                                                   'Approved',
                                                   'Dispatched',
                                               )
        )) {
            $discounts_class = '';
            $discounts_input = '';
        } else {
            $discounts_class = 'button';
            $discounts_input = sprintf(
                '<span class="hide order_item_percentage_discount_form" data-settings=\'{ "field": "Percentage" ,"transaction_key":"%d","item_key":%d, "item_historic_key":%d ,"on":1 }\'   ><input class="order_item_percentage_discount_input" style="width: 70px" value="%s"> <i class="fa save fa-cloud" aria-hidden="true"></i></span>',
                $data['Order Transaction Fact Key'], $data['Product ID'], $data['Product Key'], percentage($data['Order Transaction Total Discount Amount'], $data['Order Transaction Gross Amount'])
            );
        }
        $discounts = $discounts_input.'<span class="order_item_percentage_discount   '.$discounts_class.' '.($data['Order Transaction Total Discount Amount'] == 0 ? 'super_discreet' : '')
            .'"><span style="padding-right:5px">'.percentage($data['Order Transaction Total Discount Amount'], $data['Order Transaction Gross Amount']).'</span> <span class="'
            .($data['Order Transaction Total Discount Amount'] == 0 ? 'hide' : '').'">'.money($data['Order Transaction Total Discount Amount'], $data['Order Currency Code']).'</span></span>';


        $adata[] = array(

            'id'          => (integer)$data['Order Transaction Fact Key'],
            'product_pid' => (integer)$data['Product ID'],
            'code'        => sprintf('<span class="link" onclick="change_view(\'/products/%d/%d\')">%s</span>', $customer_order->get('Order Store Key'), $data['Product ID'], $data['Product Code']),
            'description' => $description,
            'quantity'    => $quantity,
            'discounts'   => '<span class="_item_discounts">'.$discounts.'</span>',


            'net' => sprintf('<span class="_order_item_net">%s</span>', money($data['Order Transaction Amount'], $data['Order Currency Code'])),


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


function invoice_items($_data, $db, $user) {

    global $_locale;// fix this locale stuff

    $rtext_label = 'item';
    include_once 'utils/geography_functions.php';

    include_once 'prepare_table/init.php';
    include_once 'class.Invoice.php';


    $invoice = new Invoice($_data['parameters']['parent_key']);
    if (in_array(
        $invoice->data['Invoice Delivery Country Code'], get_countries_EC_Fiscal_VAT_area($db)
    )) {
        $print_tariff_code = false;
    } else {
        $print_tariff_code = true;
    }


    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();
    foreach ($db->query($sql) as $data) {

        $net = money(
            ($data['Order Transaction Amount']), $data['Invoice Currency Code']
        );

        $tax    = money(
            ($data['Invoice Transaction Item Tax Amount']), $data['Invoice Currency Code']
        );
        $amount = money(
            ($data['Invoice Transaction Gross Amount'] - $data['Invoice Transaction Total Discount Amount'] + $data['Invoice Transaction Item Tax Amount']), $data['Invoice Currency Code']
        );


        $discount = ($data['Invoice Transaction Total Discount Amount'] == 0
            ? ''
            : percentage(
                $data['Invoice Transaction Total Discount Amount'], $data['Invoice Transaction Gross Amount'], 0
            ));

        $units    = $data['Product Units Per Case'];
        $name     = $data['Product History Name'];
        $price    = $data['Product History Price'];
        $currency = $data['Product Currency'];

        $desc = '';
        if ($units > 1) {
            $desc = number($units).'x ';
        }
        $desc .= ' '.$name;
        if ($price > 0) {
            $desc .= ' ('.money($price, $currency, $_locale).')';
        }

        $description = $desc;

        if ($discount != '') {
            $description .= ' '._('Discount').':'.$discount;
        }

        if ($data['Product RRP'] != 0) {
            $description .= ' <br>'._('RRP').': '.money(
                    $data['Product RRP'], $data['Invoice Currency Code']
                );
        }

        if ($print_tariff_code and $data['Product Tariff Code'] != '') {
            $description .= '<br>'._('Tariff Code').': '.$data['Product Tariff Code'];
        }


        $quantity = number($data['Delivery Note Quantity']);


        $adata[] = array(
            'id'          => (integer)$data['Order Transaction Fact Key'],
            'product_pid' => (integer)$data['Product ID'],
            'code'        => $data['Product Code'],
            'description' => $description,
            'quantity'    => $quantity,
            'net'         => $net,
            'tax'         => $net,
            'amount'      => $net,


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


function delivery_note_fast_track_packing($_data, $db, $user) {

    //print_r($_data);

    include_once('class.DeliveryNote.php');
    include_once('utils/order_handing_functions.php');


    global $_locale;// fix this locale stuff

    $rtext_label = 'item';


    $dn = new DeliveryNote($_data['parameters']['parent_key']);

    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref  $group_by order by $order $order_direction  limit $start_from,$number_results";
    // print $sql;
    $adata = array();
    foreach ($db->query($sql) as $data) {


        $to_pick = $data['quantity'] - $data['Picked'];
        $to_pack = $data['quantity'] - $data['Packed'];


        switch ($dn->data['Delivery Note State']) {
            case 'Dispatched':
                $state = _('dispatched');
                break;
            case 'Cancelled':
                $state = '';
                break;
            case 'Cancelled to Restock':
                $state = _('to be restocked');
                break;
            default:
                $state = _('to be dispatched');
                break;
        }


        $notes = '<b>'.number(-1 * $data['Inventory Transaction Quantity']).'</b> '.$state.'<br/>';

        if ($data['Out of Stock'] != 0) {
            $notes .= '<span style="margin-left:10px">'.number(
                    $data['Out of Stock']
                ).'</span> '._('out of stock').'<br/>';
        }
        if ($data['Not Found'] != 0) {
            $notes .= number($data['Not Found']).' '._('Not found').'<br/>';
        }
        if ($data['No Picked Other'] != 0) {
            $notes .= _('not picked (other)').' '.number(
                    $data['No Picked Other']
                ).'<br/>';
        }


        $description = $data['Part Package Description'];


        if ($data['Part UN Number']) {
            $description .= ' <span style="background-color:#f6972a;border:.5px solid #231e23;color:#231e23;padding:0px;font-size:90%">'.$data['Part UN Number'].'</span>';
        }


        $quantity = '<div class="quantity_components">'.get_item_quantity($data['quantity'], $data['to_pick']).'</div>';

        $picked = '<div class="picked_quantity_components">'.get_item_picked(
                $data['pending'], $data['Quantity On Hand'], $data['Inventory Transaction Key'], $data['Part SKU'], $data['Picked'], $data['Part Current On Hand Stock'], $data['Part SKO Barcode'],
                $data['Part Reference'], base64_encode($data['Part Package Description'].($data['Picking Note'] != '' ? ' <span>('.$data['Picking Note'].'</span>' : '')), $data['Part Main Image Key']

            ).'</div>';


        $packed   = '<div class="packed_quantity_components">'.get_item_packed($to_pack, $data['Inventory Transaction Key'], $data['Part SKU'], $data['Packed']).'</div>';
        $location = '<div class="location_components">'.get_item_location(
                $data['pending'], $data['Quantity On Hand'], $data['Date Picked'], $data['Location Key'], $data['Location Code'], $data['Part Current On Hand Stock'], $data['Part SKO Barcode']
            ).'</div>';


        if ($data['Picked'] == $data['quantity']) {
            $picked_info = '<i class="fa fa-fw fa-check success" aria-hidden="true"></i>';

        } else {
            $picked_info = '';
        }


        $picked_offline_input = '<div class="picked_quantity_components">'.get_picked_offline_input(
                $data['quantity'], $data['pending'], $data['Quantity On Hand'], $data['Inventory Transaction Key'], $data['Part SKU'], $data['Picked'], $data['Part Current On Hand Stock'],
                $data['Part SKO Barcode'], $data['Part Reference'], base64_encode($data['Part Package Description'].($data['Picking Note'] != '' ? ' <span>('.$data['Picking Note'].'</span>' : '')),
                $data['Part Main Image Key']

            ).'</div>';
        $adata[]              = array(
            'id' => (integer)$data['Inventory Transaction Key'],

            'reference'         => sprintf('<span onclick="change_view(\'part/%d\')">%s</span>', $data['Part SKU'], $data['Part Reference']),
            //   'product_pid' => $data['Product ID'],
            'description'       => $description,
            'quantity'          => $quantity,
            'dispatched'        => number(
                -1 * $data['Inventory Transaction Quantity']
            ),
            'overview_required' => number($data['Required']),

            'overview_packed'  => number($data['Packed']),
            'overview_picked'  => number($data['Picked']),
            'overview_problem' => number($data['Out of Stock']),


            'packed'               => $packed,
            'picked'               => $picked,
            'picked_info'          => $picked_info,
            'location'             => $location,
            'picked_offline_input' => $picked_offline_input


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


function delivery_note_items($_data, $db, $user) {

    //print_r($_data);

    include_once('class.DeliveryNote.php');
    include_once('utils/order_handing_functions.php');


    global $_locale;// fix this locale stuff

    $rtext_label = 'item';


    $dn = new DeliveryNote($_data['parameters']['parent_key']);

    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref  $group_by order by $order $order_direction  limit $start_from,$number_results";
    // print $sql;
    $adata = array();
    foreach ($db->query($sql) as $data) {


        $to_pick = $data['quantity'] - $data['Picked'];
        $to_pack = $data['quantity'] - $data['Packed'];


        switch ($dn->data['Delivery Note State']) {
            case 'Dispatched':
                $state = _('dispatched');
                break;
            case 'Cancelled':
                $state = '';
                break;
            case 'Cancelled to Restock':
                $state = _('to be restocked');
                break;
            default:
                $state = _('to be dispatched');
                break;
        }


        $notes = '<b>'.number(-1 * $data['Inventory Transaction Quantity']).'</b> '.$state.'<br/>';

        if ($data['Out of Stock'] != 0) {
            $notes .= '<span style="margin-left:10px">'.number(
                    $data['Out of Stock']
                ).'</span> '._('out of stock').'<br/>';
        }
        if ($data['Not Found'] != 0) {
            $notes .= number($data['Not Found']).' '._('Not found').'<br/>';
        }
        if ($data['No Picked Other'] != 0) {
            $notes .= _('not picked (other)').' '.number(
                    $data['No Picked Other']
                ).'<br/>';
        }


        $description = $data['Part Package Description'];


        if ($data['Part UN Number']) {
            $description .= ' <span style="background-color:#f6972a;border:.5px solid #231e23;color:#231e23;padding:0px;font-size:90%">'.$data['Part UN Number'].'</span>';
        }


        $quantity = '<div class="quantity_components">'.get_item_quantity($data['quantity'], $data['to_pick']).'</div>';

        $picked = '<div class="picked_quantity_components">'.get_item_picked(
                $data['pending'], $data['Quantity On Hand'], $data['Inventory Transaction Key'], $data['Part SKU'], $data['Picked'], $data['Part Current On Hand Stock'], $data['Part SKO Barcode'],
                $data['Part Reference'], base64_encode($data['Part Package Description'].($data['Picking Note'] != '' ? ' <span>('.$data['Picking Note'].'</span>' : '')), $data['Part Main Image Key']

            ).'</div>';


        $packed   = '<div class="packed_quantity_components">'.get_item_packed($to_pack, $data['Inventory Transaction Key'], $data['Part SKU'], $data['Packed']).'</div>';
        $location = '<div class="location_components">'.get_item_location(
                $data['pending'], $data['Quantity On Hand'], $data['Date Picked'], $data['Location Key'], $data['Location Code'], $data['Part Current On Hand Stock'], $data['Part SKO Barcode']
            ).'</div>';


        if ($data['Picked'] == $data['quantity']) {
            $picked_info = '<i class="fa fa-fw fa-check success" aria-hidden="true"></i>';

        } else {
            $picked_info = '';
        }


        $picked_offline_input = '<div class="picked_quantity_components">'.get_picked_offline_input(
                $data['quantity'], $data['pending'], $data['Quantity On Hand'], $data['Inventory Transaction Key'], $data['Part SKU'], $data['Picked'], $data['Part Current On Hand Stock'],
                $data['Part SKO Barcode'], $data['Part Reference'], base64_encode($data['Part Package Description'].($data['Picking Note'] != '' ? ' <span>('.$data['Picking Note'].'</span>' : '')),
                $data['Part Main Image Key']

            ).'</div>';
        $adata[]              = array(
            'id' => (integer)$data['Inventory Transaction Key'],

            'reference'         => sprintf('<span onclick="change_view(\'part/%d\')">%s</span>', $data['Part SKU'], $data['Part Reference']),
            //   'product_pid' => $data['Product ID'],
            'description'       => $description,
            'quantity'          => $quantity,
            'dispatched'        => number(
                -1 * $data['Inventory Transaction Quantity']
            ),
            'overview_required' => number($data['Required']),

            'overview_packed'  => number($data['Packed']),
            'overview_picked'  => number($data['Picked']),
            'overview_problem' => number($data['Out of Stock']),


            'packed'               => $packed,
            'picked'               => $picked,
            'picked_info'          => $picked_info,
            'location'             => $location,
            'picked_offline_input' => $picked_offline_input


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


function delivery_note_cancelled_items($_data, $db, $user) {

    //print_r($_data);

    include_once('class.DeliveryNote.php');
    include_once('utils/order_handing_functions.php');


    global $_locale;// fix this locale stuff

    $rtext_label = 'item';


    $dn = new DeliveryNote($_data['parameters']['parent_key']);

    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref  $group_by order by $order $order_direction  limit $start_from,$number_results";
    // print $sql;
    $adata = array();
    foreach ($db->query($sql) as $data) {




        $description = $data['Part Package Description'];


        $adata[] = array(
            'id' => (integer)$data['Inventory Transaction Key'],

            'reference'         => sprintf('<span onclick="change_view(\'part/%d\')">%s</span>', $data['Part SKU'], $data['Part Reference']),
            'description'       => $description,
            'overview_required' => number($data['Required']),

            'overview_packed'  => number($data['Packed']),
            'overview_picked'  => number($data['Picked']),
            'overview_problem' => number($data['Out of Stock']),
            'overview_restock' => sprintf(
                '%s returned to %s',

                number(-1*$data['Inventory Transaction Quantity']).' SKO',
                sprintf('<span class="button strong" onclick="change_view(\'/locations/%d/%d\')"  >%s</span>', $data['Warehouse Key'], $data['Location Key'], $data['Location Code'])
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


function invoice_categories($_data, $db, $user) {


    $rtext_label = 'category';
    include_once 'prepare_table / init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();


    foreach ($db->query($sql) as $data) {

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
            'code'                => $data['Category Code'],
            'label'               => $data['Category Label'],
            'subjects'            => number($data['Category Number Subjects']),
            'level'               => $level,
            'subcategories'       => number($data['Category Children']),
            'percentage_assigned' => percentage(
                $data['Category Number Subjects'], ($data['Category Number Subjects'] + $data['Category Subjects Not Assigned'])
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


?>
