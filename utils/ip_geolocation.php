<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 2 October 2018 at 13:20:28 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2018, Inikoo

 Version 3.0
*/

function get_ip_geolocation($ip, $db) {

    if ($ip == '') {
        return array(
            false,
            '',
            '',
            ''

        );
    }

    $sql = sprintf('select * from kbase.`IP Geolocation` where `IP`=%s', prepare_mysql($ip));
    if ($result = $db->query($sql)) {
        if ($row = $result->fetch()) {
            return $row;
        } else {


            $access_key = 'e4300f0065502d1ee1cb6f674cc9d1b5';

            // Initialize CURL:
            $ch = curl_init('http://api.ipstack.com/'.$ip.'?access_key='.$access_key.'');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $api_result = json_decode($json, true);

            //print_r($api_result);
            $location = parse_geolocation_data($api_result);
            $sql      = sprintf(
                "insert into kbase.`IP Geolocation` (IP,Latitude,Longitude,Location,`Country Code`,`Region Code`,`Region Name`,Town,`Postal Code`) values (%s,%s,%s,%s,%s,%s,%s,%s,%s)", prepare_mysql($ip), prepare_mysql($api_result['latitude']), prepare_mysql($api_result['longitude']),
                prepare_mysql($location,false), prepare_mysql($api_result['country_code']), prepare_mysql($api_result['region_code']), prepare_mysql($api_result['region_name']), prepare_mysql($api_result['city']), prepare_mysql($api_result['zip'])

            );

           // print "$sql\n";


            $db->exec($sql);

            return array(
                'IP'           => $ip,
                'Latitude'     => $api_result['latitude'],
                'Longitude'    => $api_result['longitude'],
                'Location'     => $location,
                'Country Code' => $api_result['region_code'],
                'Region Code`' => $api_result['region_name'],
                'Town'         => $api_result['city'],
                'Postal Code'  => $api_result['zip'],
            );


        }
    } else {
        print_r($error_info = $db->errorInfo());
        print "$sql\n";
        exit;
    }


}

function parse_geolocation_data($api_result) {

    include_once 'class.Country.php';


    $location = $api_result['city'];
    if ($location == '') {
        $location = $api_result['region_name'];
    } else {
        if ($api_result['region_name'] != '') {
            $location .= ', '.$api_result['region_name'];
        }
    }


    if ($location == '') {
        $location = $api_result['zip'];
    }

    $country = new Country('2alpha', $api_result['country_code']);


    if ($country->id) {
        $location = trim(
            sprintf(
                '<img src="/art/flags/%s.gif" title="%s"> %s', strtolower($country->get('Country 2 Alpha Code')), $country->get('Country Name'), $location
            )
        );
    } elseif($api_result['country_code']!='') {
        $location .= ' '.$api_result['country_name'].' ('.$api_result['country_code'].')';
    }


    return $location;

}


?>
