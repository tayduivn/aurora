<?php
/*

 About:
 Author: Raul Perusquia <raul@inikoo.com>

 Copyright (c) 2014, Inikoo
 Created: 21 January 2016 at 12:25:20 GMT+8, Kuala Lumpur, Malaysia

 Version 2.0
*/
chdir('../');
require_once 'keyring/dns.php';
require_once 'keyring/au_deploy_conf.php';
require_once 'keyring/key.php';
include_once 'utils/i18n.php';

require_once 'utils/general_functions.php';

$db = new PDO(
    "mysql:host=$dns_host;port=$dns_port;dbname=$dns_db;charset=utf8mb4", $dns_user, $dns_pwd
);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


if (isset($_REQUEST['locale'])) {
    $locale = $_REQUEST['locale'];
} else {
    $locale = 'en_GB.UTF-8';
}

set_locale($locale);

$country_translator = "var country_translator = { \n";
$sql = sprintf(
    'SELECT `Country Name`,`Country Local Name`,`Country 2 Alpha Code`,`Country Telephone Code`,`Country Telephone Code Metadata`,`Country Currency Code` FROM kbase.`Country Dimension` WHERE `Country Display Address Field`="Yes" ORDER BY `Country Code` DESC'
);
if ($result = $db->query($sql)) {

    foreach ($result as $data) {


        $country_translator .= sprintf(
            '%s:{name:"%s%s",currency:"%s"},', strtolower($data['Country 2 Alpha Code']), $data['Country Name'],
            (($data['Country Local Name'] != '' and $data['Country Local Name'] != $data['Country Name']) ? ' ('.$data['Country Local Name'].')' : ''), $data['Country Currency Code']
        );


    }

} else {
    print_r($error_info = $db->errorInfo());
    exit;
}
$country_translator = preg_replace('/\, $/', '', $country_translator);
$country_translator .= '};';
// country.name = country_translator.country.name.name

print "
$country_translator

var countryData = window.intlTelInputGlobals.getCountryData();
$.each(countryData, function(i, country) {

country.name = country_translator[country.iso2].name
country.currency=country_translator[country.iso2].currency
});

var tuples = [];

for (var key in countryData) tuples.push([key, countryData[key]]);

tuples.sort(function(a, b) {
    a = a[1].name;
    b = b[1].name;

    return a < b ? -1 : (a > b ? 1 : 0);
});

for (var i = 0; i < tuples.length; i++) {
        countryData[i] =tuples[i][1];
    }


";


	
