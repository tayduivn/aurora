<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 31 July 2018 at 22:19:01 GMT+8
 Copyright (c) 2018, Inikoo

 Version 3

*/

chdir('../');

require_once 'vendor/autoload.php';
require_once 'utils/sentry.php';

require_once 'keyring/dns.php';
require_once 'keyring/key.php';
require_once 'utils/i18n.php';
require_once 'utils/general_functions.php';
require_once 'utils/object_functions.php';
require_once 'utils/fake_session.class.php';

require_once "class.Account.php";

if (class_exists('Memcached')) {
    $mem = new Memcached();
    $mem->addServer($memcache_ip, 11211);
}



$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$db = new PDO(
    "mysql:host=$dns_host;dbname=$dns_db;charset=utf8mb4", $dns_user, $dns_pwd
);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


$session = new fake_session;

$warehouse_key = '';
$sql           = sprintf('SELECT `Warehouse Key` FROM `Warehouse Dimension` WHERE `Warehouse State`="Active" limit 1');

if ($result2 = $db->query($sql)) {
    if ($row2 = $result2->fetch()) {
        $warehouse_key = $row2['Warehouse Key'];
    }
} else {
    print_r($error_info = $db->errorInfo());
    print "$sql\n";
    exit;
}
$session->set('current_warehouse', $warehouse_key);




$account = new Account($db);
date_default_timezone_set($account->data['Account Timezone']);

