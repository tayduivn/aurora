<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 30 June 2017 at 20:37:24 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2017, Inikoo

 Version 3

*/

use ReallySimpleJWT\Token;


include_once 'ar_web_common_logged_out.php';


require_once 'utils/get_addressing.php';


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
    case 'register':
        $data = prepare_values(
            $_REQUEST, array(
                         'data'      => array('type' => 'json array'),
                         'store_key' => array('type' => 'key')
                     )
        );
        register($db, $website, $data, $editor);
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

/**
 * @param $db      \PDO
 * @param $website \Public_Website
 * @param $data
 * @param $editor
 *
 * @throws \Exception
 */
function register($db, $website, $data, $editor) {

    include_once 'utils/new_fork.php';
    include_once 'class.Public_Store.php';

    $store = new Public_Store($data['store_key']);


    $labels = $website->get('Localised Labels');


    $store->editor = $editor;
    $raw_data      = $data['data'];


    if ($store->id) {


        if ($website->settings('captcha_server') != '') {

            if (isset($raw_data['captcha']) && !empty($raw_data['captcha'])) {

                $ip = '';
                if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];

                } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                    $ip = $_SERVER['REMOTE_ADDR'];

                }


                $secretKey = $website->settings('captcha_server');

                $request = 'https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$raw_data['captcha'];
                if ($ip != '') {
                    $request .= '&remoteip='.$ip;
                }

                $verifyResponse = file_get_contents($request);


                $responseData = json_decode($verifyResponse);

                if (!$responseData->success) {

                    echo json_encode(
                        array(
                            'state' => 400,
                            'msg'   => (!empty($labels['_captcha_fail']) ? $labels['_captcha_fail'] : _('Robot verification failed, please try again'))
                        )
                    );
                }
            } else {

                echo json_encode(
                    array(
                        'state' => 400,
                        'msg'   => (!empty($labels['_captcha_missing']) ? $labels['_captcha_missing'] : _('Please check on the reCAPTCHA box'))

                    )
                );
                exit;
            }
        }

        $customer_data = array(
            'Customer Main Contact Name'   => $raw_data['name'],
            'Customer Company Name'        => $raw_data['organization'],
            'Customer Registration Number' => $raw_data['registration_number'],
            'Customer Tax Number'          => $raw_data['tax_number'],
            'Customer Main Plain Email'    => $raw_data['email'],
            'Customer Main Plain Mobile'   => $raw_data['tel'],
            'Customer Type by Activity'    => ($website->get('Website Registration Type') == 'ApprovedOnly' ? 'ToApprove' : 'Active')

        );

        if (array_key_exists('locality', $raw_data)) {
            $customer_data['Customer Contact Address locality'] = $raw_data['locality'];
        }
        if (array_key_exists('postalCode', $raw_data)) {
            $customer_data['Customer Contact Address postalCode'] = $raw_data['postalCode'];
        }
        if (array_key_exists('addressLine1', $raw_data)) {
            $customer_data['Customer Contact Address addressLine1'] = $raw_data['addressLine1'];
        }
        if (array_key_exists('addressLine2', $raw_data)) {
            $customer_data['Customer Contact Address addressLine2'] = $raw_data['addressLine2'];
        }
        if (array_key_exists('administrativeArea', $raw_data)) {
            $customer_data['Customer Contact Address administrativeArea'] = $raw_data['administrativeArea'];
        }
        if (array_key_exists('dependentLocality', $raw_data)) {
            $customer_data['Customer Contact Address dependentLocality'] = $raw_data['dependentLocality'];
        }
        if (array_key_exists('sortingCode', $raw_data)) {
            $customer_data['Customer Contact Address sortingCode'] = $raw_data['sortingCode'];
        }
        if (array_key_exists('country', $raw_data)) {
            $customer_data['Customer Contact Address country'] = $raw_data['country'];
        }

        if (isset($raw_data['subscription']) and $raw_data['subscription'] == 'on') {
            $customer_data['Customer Send Newsletter']       = 'Yes';
            $customer_data['Customer Send Email Marketing']  = 'Yes';
            $customer_data['Customer Send Postal Marketing'] = 'Yes';

        } else {
            $customer_data['Customer Send Newsletter']       = 'No';
            $customer_data['Customer Send Email Marketing']  = 'No';
            $customer_data['Customer Send Postal Marketing'] = 'No';
        }


        list($customer, $website_user) = $store->create_customer($customer_data, array('Website User Password' => $raw_data['new-password']));

        if ($store->new_customer and $store->new_website_user) {


            foreach ($raw_data as $_key => $value) {

                if (preg_match('/^poll_(\d+)/i', $_key, $matches)) {
                    $poll_key = $matches[1];
                    $customer->update(array('Customer Poll Query '.$poll_key => $value), 'no_history');
                }


            }


            new_housekeeping_fork(
                'au_housekeeping', array(
                'type'         => 'customer_registered',
                'customer_key' => $customer->id,
                'website_key'  => $website->id
            ), DNS_ACCOUNT_CODE
            );


            if ($website->get('Website Registration Type') != 'ApprovedOnly') {

                include_once('class.WebAuth.php');
                $auth = new WebAuth($db);

                list($logged_in, $website_user_log_key) = $auth->authenticate_from_register($website_user->id, $customer->id, $store->get('Store Website Key'));

                if ($logged_in) {
                    $_SESSION['logged_in']        = true;
                    $_SESSION['customer_key']     = $customer->id;
                    $_SESSION['website_user_key'] = $website_user->id;

                    $_SESSION['UTK'] = [
                        'C'   => $customer->id,
                        'WU'  => $website_user->id,
                        'WUL' => $website_user_log_key,
                        'CUR' => $website->get('Currency Code'),
                        'LOC' => $website->get('Website Locale')
                    ];

                    $token = Token::customPayload($_SESSION['UTK'], JWT_KEY);
                    setcookie('UTK', $token, time() + 157680000);
                    setcookie('AUK', strtolower(DNS_ACCOUNT_CODE).'.'.$_SESSION['customer_key'], time() + 157680000);


                } else {

                    echo json_encode(
                        array(
                            'state' => 400,
                        )
                    );
                    exit;

                }

            }


            echo json_encode(
                array(
                    'state' => 200,
                    'msg'   => 'reg'
                )
            );
            exit;

        } else {
            echo json_encode(
                array(
                    'state' => 400,
                    'msg'   => $store->msg
                )
            );
            exit;
        }


    } else {
        echo json_encode(
            array(
                'state' => 400,
                'resp'  => 'Store not found '.$data['store_key']
            )
        );
        exit;

    }


}

