<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>

 Created: 01-10-2019 14:52:55 MYT, Kuala Lumpur, Malaysia
 Copyright (c) 2019, Inikoo

 Version 2.0
*/


include_once 'keyring/dns.php';
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


$db = new PDO(
    "mysql:host=$dns_host;dbname=$dns_db;charset=utf8mb4", $dns_user, $dns_pwd
);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


list($authenticated, $box_id) = authenticate($db);

if ($authenticated == 'OK') {

    $sql  = 'select `Box Key`,`Box Aurora Account Code`,`Box Aurora Account Data` from `Box Dimension` where `Box ID`=?';
    $stmt = $db->prepare($sql);
    $stmt->execute(
        array($box_id)
    );
    if ($row = $stmt->fetch()) {
        $box_key = $row['Box Key'];


        log_api_key_access_success($db, $box_key);

        if($row['Box Aurora Account Code']==''){
            $response= array(
                'state' => 'OK',
                'code'  => 'Registered',
                'msg'   => 'Box registered, still waiting for confirmation on aurora'
            );


            echo json_encode($response);
            exit;
        }




    } else {

        $sql = 'insert into `Box Dimension` (`Box ID`,`Box Model`,`Box Registered Date`) values (?,?,?) ';
        $db->prepare($sql)->execute(
            array(
                $box_id,
                (isset($_REQUEST['model']) ? $_REQUEST['register'] : 'Unknown'),
                gmdate('Y-m-d H:i:s')
            )
        );
        $box_key = $db->lastInsertId();


        return array(
            'state' => 'OK',
            'code'  => 'Registered',
            'msg'   => 'Box registered, waiting for confirmation on aurora'
        );

        log_api_key_access_success($db, $box_key);

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
        } else {
            $auth_header = getAuthorizationHeader();
            if (preg_match('/^Bearer\s(.+)$/', $auth_header, $matches)) {
                $token = $matches[1];
            }


        }


    } else {
        $token = $_SERVER['HTTP_X_AUTH_KEY'];
    }


    //


    if (!$token) {

        $response = log_box_api_key_access_failure(
            $db, 'API Key Missing'
        );

        echo json_encode($response);
        exit;
    } else {
        $api_key = $token;


        if (preg_match('/^([a-z0-9]{8})(.+)$/', $api_key, $matches)) {


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


