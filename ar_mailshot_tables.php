<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 31 May 2018 at 11:30:25 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2018, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/ar_common.php';
require_once 'utils/table_functions.php';
require_once 'utils/object_functions.php';


if (!$user->can_view('stores')) {
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
    case 'mailshots':
        mailshots(get_table_parameters(), $db, $user);
        break;
    case 'subject_sent_emails':
        subject_sent_emails(get_table_parameters(), $db, $user);
        break;


    case 'sent_emails':
        sent_emails(get_table_parameters(), $db, $user);
        break;
    case 'email_tracking_events':
        email_tracking_events(get_table_parameters(), $db, $user);
        break;
    case 'oss_notification_next_recipients':
        oss_notification_next_recipients(get_table_parameters(), $db, $user);
        break;
    case 'gr_reminder_next_recipients':
        gr_reminder_next_recipients(get_table_parameters(), $db, $user);
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



function subject_sent_emails($_data, $db, $user) {

    $rtext_label = 'email';
    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();


    $parent = get_object($_data['parameters']['parent'], $_data['parameters']['parent_key']);

    // print $sql;
    //'Ready','Send to SES','Rejected by SES','Send','Read','Hard Bounce','Soft Bounce','Spam','Delivered','Opened','Clicked','Error'


    if ($result = $db->query($sql)) {
        foreach ($result as $data) {


            switch ($data['Email Tracking State']) {
                case 'Ready':
                    $state = _('Ready to send');
                    break;
                case 'Sent to SES':
                    $state = _('Sending');
                    break;

                    break;
                case 'Delivered':
                    $state = _('Delivered');
                    break;
                case 'Opened':
                    $state = _('Opened');
                    break;
                case 'Clicked':
                    $state = _('Clicked');
                    break;
                case 'Error':
                    $state = '<span class="warning">'._('Error').'</span>';
                    break;
                case 'Hard Bounce':
                    $state = '<span class="error"><i class="fa fa-exclamation-circle"></i>  '._('Bounced').'</span>';
                    break;
                case 'Soft Bounce':
                    $state = '<span class="warning"><i class="fa fa-exclamation-triangle"></i>  '._('Probable bounce').'</span>';
                    break;
                case 'Spam':
                    $state = '<span class="error"><i class="fa fa-exclamation-circle"></i>  '._('Mark as spam').'</span>';
                    break;
                default:
                    $state = $data['Email Tracking State'];
            }


            $subject = sprintf('<span class="link" onclick="change_view(\'%s/%d/%d/email/%d\')"  >%s</span>', strtolower($parent->get_object_name()).'s', $parent->get('Store Key'), $parent->id, $data['Email Tracking Key'], $data['Published Email Template Subject']);

            if($_data['parameters']['parent']=='prospect_agent') {
                $recipient = sprintf('<span class="link" onclick="change_view(\'prospects/%d/%d\')"  >%s</span>',$data['store_key'] , $data['recipient_key'], $data['recipient_name']);
                $email = sprintf('<span class="link" onclick="change_view(\'report/prospect_agents/%d/email/%d\')"  >%s</span>', $_data['parameters']['parent_key'],$data['Email Tracking Key'],$data['Email Tracking Email']);

            }else{
                $recipient='';
                $email='';
            }




            $adata[] = array(
                'id'      => (integer)$data['Email Tracking Key'],
                'state'   => $state,
                'subject' => $subject,
                'recipient' => $recipient,
                'email' => $email,
                'date'    => strftime("%a, %e %b %Y %R", strtotime($data['Email Tracking Created Date']." +00:00")),


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


function email_tracking_events($_data, $db, $user) {

    $rtext_label = 'event';

    include_once 'prepare_table/init.php';

    $sql = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";

    //print $sql;


    $adata = array();


    foreach ($db->query($sql) as $data) {




        switch ($data['Email Tracking Event Type']) {
            case 'Opened':
                $event = _('Opened');
                $_data = json_decode($data['data'], true);

                $note = $_data['userAgent'].' '.$_data['ipAddress'];
                $note = '';
                break;
            default:

                $event = $data['Email Tracking Event Type'];
                $note  = '';
        }

        $adata[] = array(
            'id'   => (integer)$data['Email Tracking Event Key'],
            'date' => strftime("%a %e %b %Y %H:%M %Z ", strtotime($data['Email Tracking Event Date']." +00:00")),

            'note'  => $note,
            'event' => $event,


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


function sent_emails($_data, $db, $user) {

    $rtext_label = 'email';
    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
    $adata = array();



    $parent = get_object($_data['parameters']['parent'], $_data['parameters']['parent_key']);
    if ($_data['parameters']['parent'] == 'mailshot') {
        $email_campaign_type= get_object('email_campaign_type',$parent->get('Email Campaign Email Template Type Key'));
    }elseif ($_data['parameters']['parent'] == 'customer') {
        $_parent= get_object('customer',$_data['parameters']['parent_key']);
    }

    //'Ready','Send to SES','Rejected by SES','Send','Read','Hard Bounce','Soft Bounce','Spam','Delivered','Opened','Clicked','Error'


    if ($result = $db->query($sql)) {
        foreach ($result as $data) {

            switch ($data['Email Campaign Type Code']) {
                case 'Newsletter':
                    $_type  = _('Newsletter');
                    break;
                case 'Marketing':
                    $_type  = _('Marketing mailshot');
                    break;
                case 'AbandonedCart':
                    $_type  = _('Abandoned cart');
                    break;
                case 'OOS Notification':
                    $_type = _('Back in stock email');
                    break;
                case 'Registration':
                    $_type = _('Welcome email');
                    break;
                case 'Password Reminder':
                    $_type = _('Password reset email');
                    break;
                case 'Order Confirmation':
                    $_type = _('Order confirmation');
                    break;
                case 'GR Reminder':
                    $_type = _('Reorder reminder');
                    break;
                case 'Invite Mailshot':
                    $_type  = _('Invitation');
                    break;
                case 'Invite':
                    $_type  = _('Personalized invitation');
                    break;
                default:
                    $_type = $data['Email Campaign Type Code'];


            }

            switch ($data['Email Tracking State']) {
                case 'Ready':
                    $state = _('Ready to send');
                    break;
                case 'Sent to SES':
                    $state = _('Sending');
                    break;

                    break;
                case 'Delivered':
                    $state = _('Delivered');
                    break;
                case 'Opened':
                    $state = _('Opened');
                    break;
                case 'Clicked':
                    $state = _('Clicked');
                    break;
                case 'Error':
                    $state = '<span class="warning">'._('Error').'</span>';
                    break;
                case 'Hard Bounce':
                    $state = '<span class="error"><i class="fa fa-exclamation-circle"></i>  '._('Bounced').'</span>';
                    break;
                case 'Soft Bounce':
                    $state = '<span class="warning"><i class="fa fa-exclamation-triangle"></i>  '._('Probable bounce').'</span>';
                    break;
                case 'Spam':
                    $state = '<span class="error"><i class="fa fa-exclamation-circle"></i>  '._('Mark as spam').'</span>';
                    break;
                default:
                    $state = $data['Email Tracking State'];
            }


            $customer = sprintf('<span class="link" onclick="change_view(\'customers/%d/%d\')"  >%s (%05d)</span>', $data['store_key'], $data['recipient_key'], $data['recipient_name'], $data['recipient_key']);
            $prospects= sprintf('<span class="link" onclick="change_view(\'prospects/%d/%d\')"  >%s (%05d)</span>', $data['store_key'], $data['recipient_key'], $data['recipient_name'], $data['recipient_key']);

            $subject='';
            if ($_data['parameters']['parent'] == 'email_campaign_type') {
                $email = sprintf('<span class="link" onclick="change_view(\'email_campaign_type/%d/%d/tracking/%d\')"  >%s</span>', $parent->get('Store Key'), $parent->id, $data['Email Tracking Key'], $data['Email Tracking Email']);




            } elseif ($_data['parameters']['parent'] == 'mailshot') {

                $email = sprintf(
                    '<span class="link" onclick="change_view(\'email_campaign_type/%d/%d/mailshot/%d/tracking/%d\')"  >%s</span>', $email_campaign_type->get('Store Key'),$email_campaign_type->id, $parent->id, $data['Email Tracking Key'], $data['Email Tracking Email']
                );

            } elseif ($_data['parameters']['parent'] == 'customer') {
                $email='';
                $subject = sprintf(
                    '<span class="link" onclick="change_view(\'customers/%d/%d/email/%d\')"  >%s</span>', $_parent->get('Store Key'),$_parent->id, $data['Email Tracking Key'], $data['Published Email Template Subject']
                );

            }elseif ($_data['parameters']['parent'] == 'email_campaign') {
                $subject='';
                $email = sprintf(
                    '<span class="link" onclick="change_view(\'orders/%d/dashboard/website/mailshots/%d/tracking/%d\')"  >%s</span>',$data['store_key'],$parent->id, $data['Email Tracking Key'], $data['Email Tracking Email']
                );

            }


            $adata[] = array(
                'id'       => (integer)$data['Email Tracking Key'],
                'state'    => $state,
                'email'    => $email,
                'type'    => $_type,
                'subject' => $subject,
                'prospect' => $prospects,
                'customer' => $customer,
                'date'     => strftime("%a, %e %b %Y %R:%S", strtotime($data['Email Tracking Created Date']." +00:00")),


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


function oss_notification_next_recipients($_data, $db, $user) {

    $rtext_label = 'customer';
    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref $group_by order by $order $order_direction  limit $start_from,$number_results";
    $adata = array();


    //print $sql;
    //'Ready','Send to SES','Rejected by SES','Send','Read','Hard Bounce','Soft Bounce','Spam','Delivered','Opened','Clicked','Error'


    if ($result = $db->query($sql)) {
        foreach ($result as $data) {




            $customer = sprintf('<span class="link" onclick="change_view(\'customers/%d/%d\')"  >%s (%05d)</span>', $data['Customer Store Key'], $data['Customer Key'], $data['Customer Name'], $data['Customer Key']);

            $products = '';
            //print_r($data);
            $products_data = preg_split('/\,/', $data['products']);
            // print_r($products_data);
            foreach ($products_data as $product_data) {
                $_product_data = preg_split('/\|/', $product_data);

                $products .= sprintf('<span class="link" onclick="change_view(\'products/%d/%d\')" >%s</span>, ', $data['Customer Store Key'], $_product_data[0], $_product_data[1]);
            }
            $products = preg_replace('/, $/', '', $products);

            $adata[] = array(
                'id' => (integer)$data['Back in Stock Reminder Customer Key'],

                'customer' => $customer,
                'products' => $products,


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


function gr_reminder_next_recipients($_data, $db, $user) {

    $rtext_label = 'customer';
    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref $group order by $order $order_direction  limit $start_from,$number_results";
    $adata = array();


    //print $sql;
    //'Ready','Send to SES','Rejected by SES','Send','Read','Hard Bounce','Soft Bounce','Spam','Delivered','Opened','Clicked','Error'


    if ($result = $db->query($sql)) {
        foreach ($result as $data) {


            //print_r($data);


            $customer   = sprintf('<span class="link" onclick="change_view(\'customers/%d/%d\')"  >%s (%05d)</span>', $data['Customer Store Key'], $data['Customer Key'], $data['Customer Name'], $data['Customer Key']);
            $last_order = sprintf('<span class="link" onclick="change_view(\'orders/%d/%d\')"  >%s</span>', $data['Customer Store Key'], $data['Order Key'], $data['Order Public ID']);


            $adata[] = array(
                'id' => (integer)$data['Customer Key'],

                'customer'   => $customer,
                'last_order' => $last_order,


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


function mailshots($_data, $db, $user) {



    $email_template_type=get_object('email_template_type',$_data['parameters']['parent_key']);

    if($email_template_type->get('Code')=='Newsletter'){
        $rtext_label = 'newsletter';

    }else{
        $rtext_label = 'mailshot';

    }



    include_once 'prepare_table/init.php';

    $sql   = "select $fields from $table $where $wheref $group order by $order $order_direction  limit $start_from,$number_results";
    $adata = array();


    if ($result = $db->query($sql)) {
        foreach ($result as $data) {


            switch ($data['Email Campaign State']) {
                case 'Ready':
                    $state = _('Ready to send');
                    break;
                case 'Sent to SES':
                    $state = _('Sending');
                    break;

                    break;
                case 'Delivered':
                    $state = _('Delivered');
                    break;
                case 'Opened':
                    $state = _('Opened');
                    break;
                case 'Clicked':
                    $state = _('Clicked');
                    break;
                case 'Error':
                    $state = '<span class="warning">'._('Error').'</span>';
                    break;
                case 'Hard Bounce':
                    $state = '<span class="error"><i class="fa fa-exclamation-circle"></i>  '._('Bounced').'</span>';
                    break;
                case 'Soft Bounce':
                    $state = '<span class="warning"><i class="fa fa-exclamation-triangle"></i>  '._('Probable bounce').'</span>';
                    break;
                case 'Spam':
                    $state = '<span class="error"><i class="fa fa-exclamation-circle"></i>  '._('Mark as spam').'</span>';
                    break;
                default:
                    $state = $data['Email Campaign State'];
            }


            $name = sprintf(
                '<span class="link" onclick="change_view(\'email_campaign_type/%d/%d/mailshot/%d\')"  >%s</span>', $data['Email Campaign Store Key'], $_data['parameters']['parent_key'], $data['Email Campaign Key'], $data['Email Campaign Name']
            );


            $adata[] = array(
                'id' => (integer)$data['Email Campaign Key'],

                'name'  => $name,
                'state' => $state,

                'date' => strftime("%a, %e %b %Y %R", strtotime($data['Email Campaign Last Updated Date']." +00:00")),
                'sent' => number($data['Email Campaign Sent']),

                'hard_bounces' => sprintf(
                    '<span class="%s" title="%s">%s</span>', ($data['Email Campaign Delivered'] == 0 ? 'super_discreet' : ($data['Email Campaign Hard Bounces'] == 0 ? 'success super_discreet' : '')), number($data['Email Campaign Hard Bounces']),
                    percentage($data['Email Campaign Hard Bounces'], $data['Email Campaign Sent'])
                ),
                'soft_bounces' => sprintf(
                    '<span class="%s" title="%s">%s</span>', ($data['Email Campaign Delivered'] == 0 ? 'super_discreet' : ($data['Email Campaign Soft Bounces'] == 0 ? 'success super_discreet' : '')), number($data['Email Campaign Soft Bounces']),
                    percentage($data['Email Campaign Soft Bounces'], $data['Email Campaign Sent'])
                ),


                'delivered' => ($data['Email Campaign Sent'] == 0 ? '<span class="super_discreet">'._('NA').'</span>' : number($data['Email Campaign Delivered'])),

                'open'    => sprintf(
                    '<span class="%s" title="%s">%s</span>', ($data['Email Campaign Delivered'] == 0 ? 'super_discreet' : ''), number($data['Email Campaign Open']), percentage($data['Email Campaign Open'], $data['Email Campaign Delivered'])
                ),
                'clicked' => sprintf(
                    '<span class="%s" title="%s">%s</span>', ($data['Email Campaign Delivered'] == 0 ? 'super_discreet' : ''), number($data['Email Campaign Clicked']), percentage($data['Email Campaign Clicked'], $data['Email Campaign Delivered'])
                ),
                'spam'    => sprintf(
                    '<span class="%s " title="%s">%s</span>', ($data['Email Campaign Delivered'] == 0 ? 'super_discreet' : ($data['Email Campaign Spams'] == 0 ? 'success super_discreet' : '')), number($data['Email Campaign Spams']),
                    percentage($data['Email Campaign Spams'], $data['Email Campaign Delivered'])
                ),


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


?>
