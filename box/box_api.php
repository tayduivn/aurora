<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>

 Created: 01-10-2019 14:52:55 MYT, Kuala Lumpur, Malaysia
 Copyright (c) 2019, Inikoo

 Version 2.0
*/


include_once 'keyring/box_dns.php';
require_once 'vendor/autoload.php';

if (defined('SENTRY_DNS_API')) {
    Sentry\init(['dsn' => SENTRY_DNS_API]);
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token,HTTP_X_AUTH_KEY');

error_reporting(E_ALL);
date_default_timezone_set('UTC');
include_once 'utils/general_functions.php';
include_once 'utils/object_functions.php';
include_once 'utils/network_functions.php';


global $db;

$db_box = new PDO(
    "mysql:host=$dns_host;dbname=$dns_db;charset=utf8mb4", $dns_user, $dns_pwd
);
$db_box->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


list($authenticated, $box_id) = authenticate($db_box);

if ($authenticated == 'OK') {

    $sql  = 'select `Box Key`,`Box Aurora Account Code`,`Box Aurora Account Data` from `Box Dimension` where `Box ID`=?';


    $stmt = $db_box->prepare($sql);
    $stmt->execute(
        array($box_id)
    );
    if ($row = $stmt->fetch()) {


        $box_key = $row['Box Key'];


        log_api_key_access_success($db_box, $box_key);

        if ($row['Box Aurora Account Code'] != '' and $row['Box Aurora Account Data'] != '') {

            $_data = json_decode($row['Box Aurora Account Data'], true);
            $tenant_db=$_data['db'];
            if(! in_array($tenant_db,['dw','es','sk','demo'])){
                exit('error');
            }



            $db = new PDO(
                "mysql:host=$dns_host;dbname=$tenant_db;charset=utf8mb4", $dns_user, $dns_pwd
            );
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);




            if (isset($_REQUEST['register'])) {


                $_data = json_decode($row['Box Aurora Account Data'], true);

                $_timezone = new DateTimeZone($_data['timezone']);
                $_datetime = new DateTime("now", $_timezone);


                list($wifi_encrypted_password, $enc_iv) = explode("::", $_data['wifi_token']);
                $cipher_method = 'aes-128-ctr';
                $enc_key       = openssl_digest(SHARED_KEY, 'SHA256', true);
                $wifi_pwd      = openssl_decrypt($wifi_encrypted_password, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
                unset($wifi_encrypted_password, $cipher_method, $enc_key, $enc_iv);


                $response = array(
                    'state'       => 'Registered',
                    'name'        => $_data['name'].'@'.strtolower($row['Box Aurora Account Code']).'.au.sys',
                    'time_offset' => timezone_offset_get($_timezone, $_datetime),
                    'SSID'        => $_data['SSID'],
                    'wifi_pwd'    => $wifi_pwd,


                );

                print json_encode($response);
                exit;


            }elseif (isset($_REQUEST['clocking'])) {

                $editor = array(
                    'Author Name'  => 'Clocking Machine',
                    'Author Alias' => 'Clocking Machine',
                    'Author Type'  =>'',
                    'Author Key'   =>'',
                    'User Key'     => '',
                    'Date'         => gmdate('Y-m-d H:i:s')
                );

                include_once 'class.Staff.php';
                $staff=new Staff('id',$_REQUEST['staff_id']);
                if($staff->id){


                    $data = array(
                        'Timesheet Record Date'   =>$_REQUEST['clocking'],
                        'Timesheet Record Source' => $_REQUEST['source'],
                        'Timesheet Record Type'   => 'ClockingRecord',
                        'editor'                  => $editor
                    );



                    $staff->create_timesheet_record($data);




                    if ($staff->create_timesheet_record_error) {

                        if ($staff->create_timesheet_record_duplicated) {

                            $response = array(
                                'result'     => 'error',
                            'msg'=>"Record already exists"
                            );

                        } else {


                            $response = array(
                                'result'     => 'error',
                                'msg'=> "Error creating record"
                            );

                        }

                        echo json_encode($response);
                        exit;
                    } else {
                        $response = array(
                            'result'     => 'ok',
                        );
                        echo json_encode($response);
                        exit;

                    }


                }




            }elseif (isset($_REQUEST['set'])) {

                switch ($_REQUEST['set']) {
                    case 'nfc':



                        $sql='update `Staff Dimension` set `Staff Properties` = JSON_SET(`Staff Properties`,?,?) where `Staff Key`=?';



                        $stmt=$db->prepare($sql);
                        $stmt->execute(
                            array(
                                '$.nfc',
                                $_REQUEST['nfc'],
                                $_REQUEST['staff_id']
                            )
                        );



                        $_response = array(
                            'result'     => 'nfc updated'
                        );


                        break;
                    case 'pin':


                        $sql='update `Staff Dimension` set `Staff Properties` = JSON_SET(`Staff Properties`,?,?) where `Staff Key`=?';

                        $db->prepare($sql)->execute(
                            array(
                                '$.pin',
                                $_REQUEST['pin'],
                                $_REQUEST['staff_id']
                            )
                        );

                        $_response = array(
                            'result'     => 'pin updated'
                        );

                        break;
                    default:
                        $_response = array(
                            'result'     => 'error'
                        );


                }

                echo json_encode($_response);
                exit;

            } elseif (!empty($_REQUEST['get'])) {
                switch ($_REQUEST['get']) {







                    case 'staff':

                        $_data = json_decode($row['Box Aurora Account Data'], true);




                        $staff=[];


                        $sql='select `Staff Key`,`Staff Name`,`Staff Properties` from `Staff Dimension` where `Staff Currently Working`="Yes"';

                        $stmt = $db->prepare($sql);
                        $stmt->execute(
                            array(

                            )
                        );
                        while ($row = $stmt->fetch()) {

                            $_data = json_decode($row['Staff Properties'], true);

                            if(isset($_data['nfc'])){
                                $nfc=$_data['nfc'];
                            }else{
                                $nfc='';
                            }
                            if(isset($_data['pin'])){
                                $pin=$_data['pin'];
                            }else{
                                $pin='';
                            }



                            $staff[]=[
                                'key'=>$row['Staff Key'],
                                'name'=>$row['Staff Name'],
                                 'nfc'=>$nfc,
                                'pin'=>$pin
                            ];
                            }


                        $response = array(
                            'staff'     => $staff
                        );

                        break;
                    case 'time_offset':
                        $_data = json_decode($row['Box Aurora Account Data'], true);

                        $_timezone = new DateTimeZone($_data['timezone']);

                        $_datetime = new DateTime("now", $_timezone);
                        $response  = array(
                            'offset' => timezone_offset_get($_timezone, $_datetime),
                        );

                        break;
                    case 'wifi':

                        $_data = json_decode($row['Box Aurora Account Data'], true);

                        list($wifi_encrypted_password, $enc_iv) = explode("::", $_data['wifi_token']);
                        $cipher_method = 'aes-128-ctr';
                        $enc_key       = openssl_digest(SHARED_KEY, 'SHA256', true);
                        $wifi_pwd      = openssl_decrypt($wifi_encrypted_password, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
                        unset($wifi_encrypted_password, $cipher_method, $enc_key, $enc_iv);

                        $response = array(
                            'SSID'     => $_data['SSID'],
                            'wifi_pwd' => $wifi_pwd,
                        );

                        break;
                    default:
                        $response = array();

                }
                echo json_encode($response);
                exit;


            } elseif (!empty($_REQUEST['send_tag_id'])) {

                $_data = json_decode($row['Box Aurora Account Data'], true);

                $_timezone = new DateTimeZone($_data['timezone']);

                $tag_id = $_REQUEST['send_tag_id'];

                if (!empty($_REQUEST['timestamp'])) {
                    $_datetime = new DateTime();
                    $_datetime->setTimestamp($_REQUEST['timestamp']);
                    $_datetime->setTimezone($_timezone);

                } else {
                    $_datetime = new DateTime("now", $_timezone);

                }


                list($encrypted_api_secret, $enc_iv) = explode("::", $_data['api_secret']);

                $cipher_method = 'aes-128-ctr';
                $enc_key       = openssl_digest(SHARED_KEY, 'SHA256', true);
                $api_secret    = openssl_decrypt($encrypted_api_secret, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
                unset($encrypted_api_secret, $cipher_method, $enc_key, $enc_iv);


                $api_url = $_data['api_url'].'/api/box?action=send_tag_id&box_key='.$_data['box_key'].'&tag_id='.$tag_id.'&date='.urlencode($_datetime->format('Y-m-d H:i:s'));

                //$api_url = 'au.geko/'.'/api/box?action=send_tag_id&box_key='.$_data['box_key'].'&tag_id='.$tag_id.'&date='.urlencode($_datetime->format('Y-m-d H:i:s'));

                //  print $api_url."\n";


                $curl = curl_init();

                curl_setopt_array(
                    $curl, array(

                             CURLOPT_URL            => $api_url,
                             CURLOPT_RETURNTRANSFER => true,
                             CURLOPT_ENCODING       => "",
                             CURLOPT_MAXREDIRS      => 10,
                             CURLOPT_TIMEOUT        => 30,
                             CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                             CURLOPT_CUSTOMREQUEST  => "GET",
                             CURLOPT_POSTFIELDS     => "",
                             CURLOPT_HTTPHEADER     => array(
                                 "X-Auth-Key: ".$_data['api_code'].'.'.$api_secret,
                                 "cache-control: no-cache"
                             )
                         )
                );



                $response = json_decode(curl_exec($curl), true);
                $err      = curl_error($curl);


                curl_close($curl);

                if ($err) {

                    $_response = array(
                        'state' => 'Fail',
                        'msg'=>$err
                    );

                } else {


                    if ($response['state'] == 'Success') {

                            $_response = array(
                                'state' => 'Success',
                                'code'  => $response['staff_name']
                            );


                    }elseif ($response['state'] == 'Pending_Tag') {

                            $_response = array(
                                'state' => 'Pending_Tag',
                                'code'  => $response['nfc_tag_hash']
                            );


                    } else {
                        $_response = array(
                            'state' => 'Fail',
                            'msg'   => $response['msg']
                        );
                    }


                }


                echo json_encode($_response);
                exit;


            }


        } else {
            $response = array(
                'state' => 'Waiting',
                'msg'   => 'Waiting for confirmation on aurora'
            );


            echo json_encode($response);
            exit;
        }


    } else {

        $sql = 'insert into `Box Dimension` (`Box ID`,`Box Model`,`Box Registered Date`) values (?,?,?) ';
        $db_box->prepare($sql)->execute(
            array(
                $box_id,
                (isset($_REQUEST['register']) ? $_REQUEST['register'] : 'Unknown'),
                gmdate('Y-m-d H:i:s')
            )
        );
        $box_key = $db_box->lastInsertId();


        $response = array(
            'state' => 'Waiting',
            'msg'   => 'Waiting for confirmation on aurora'
        );

        log_api_key_access_success($db_box, $box_key);

        echo json_encode($response);
        exit;

    }


}


function authenticate($db) {


    $_headers = _apache_request_headers();

    $token = false;



    if (empty($_SERVER['HTTP_X_AUTH_KEY'])) {
        if (!empty($_headers['HTTP_X_AUTH_KEY'])) {
            $token = $_headers['HTTP_X_AUTH_KEY'];
        } elseif (!empty($_headers['http_x_auth_key'])) {
            $token = $_headers['http_x_auth_key'];
        }elseif (!empty($_SERVER['HTTP_X_API_KEY'])) {
            $token = $_SERVER['HTTP_X_API_KEY'];
        } elseif (!empty($_REQUEST['AUTH_KEY'])) {
            $token = $_REQUEST['AUTH_KEY'];
        } else {
            $auth_header = getAuthorizationHeader();
            if (preg_match('/^Bearer\s(.+)$/', $auth_header, $matches)) {
                $token = $matches[1];
            }


        }


    } else {
        $token = $_SERVER['HTTP_X_AUTH_KEY'];
    }


    if (!$token) {
        if (!empty($_SERVER['HTTP_X_API_KEY'])) {
            $token = $_SERVER['HTTP_X_API_KEY'];
        }
    }



    if (!$token) {

        $response = log_box_api_key_access_failure(
            $db, 'API Key Missing'
        );

        echo json_encode($response);
        exit;
    } else {
        $api_key = $token;


        if (preg_match('/^([a-z0-9]{6})(.+)$/', $api_key, $matches)) {


            $box_id         = $matches[1];
            $api_key_secret = preg_replace('/^\./', '', $matches[2]);


            if ($api_key_secret == API_KEY_SECRET) {
                return array(
                    'OK',
                    $box_id,

                );
            } else {
                $response = log_box_api_key_access_failure(
                    $db, 'API Key No Match'
                );

                echo json_encode($response);
                exit;
            }


        } else {

            $response = log_box_api_key_access_failure(
                $db, 'Invalid API Key'
            );
            echo json_encode($response);
            exit;
        }


    }

}


function log_box_api_key_access_failure($db, $fail_type) {


    $sql = 'INSERT INTO `Fail API Box Request Dimension` (`Fail API Box Request IP`,`Fail API Box Request Date`,`Fail API Box Request Type`) VALUES(?,?,?)';

    $stmt = $db->prepare($sql);

    if (!$stmt->execute(
        [
            ip(),
            gmdate('Y-m-d H:i:s'),
            $fail_type
        ]
    )) {
        print_r($stmt->errorInfo());
    }

    return array(
        'state' => 'Error',
        'code'  => $fail_type,
        'msg'   => 'Access failed'
    );

}


function log_api_key_access_success($db, $box_key) {

    if (DEBUG) {
        $debug = json_encode(
            array(
                $_SERVER,
                $_REQUEST
            )
        );

    } else {
        $debug = '';

    }

    $sql = 'INSERT INTO `API Box Request Dimension` (`API Box Request Box Key`,`API Box Request Date`,`API Box Request IP`,`API Box Request Metadata`) VALUES(?,?,?,?)';
    $db->prepare($sql)->execute(
        [
            $box_key,
            gmdate('Y-m-d H:i:s'),
            ip(),
            $debug
        ]
    );

}


function _apache_request_headers() {
    $arh     = array();
    $rx_http = '/\AHTTP_/';
    foreach ($_SERVER as $key => $val) {
        if (preg_match($rx_http, $key)) {
            $arh_key    = preg_replace($rx_http, '', $key);
            $rx_matches = array();
            // do some nasty string manipulations to restore the original letter case
            // this should work in most cases
            $rx_matches = explode('_', $arh_key);
            if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                foreach ($rx_matches as $ak_key => $ak_val) {
                    $rx_matches[$ak_key] = ucfirst($ak_val);
                }
                $arh_key = implode('-', $rx_matches);
            }
            $arh[$arh_key] = $val;
        }
    }

    return ($arh);
}


function getAuthorizationHeader() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
    }

    return $headers;
}


