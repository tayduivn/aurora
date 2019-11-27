<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 05-05-2019 09:49:14, CEST Tranava, Slovakia
 Copyright (c) 2019, Inikoo

 Version 3

*/

error_reporting(E_ALL ^ E_DEPRECATED);

require_once 'vendor/autoload.php';
require_once "class.Account.php";
require_once 'fork.common.php';
require_once 'utils/fake_session.class.php';

include 'utils/aes.php';
include 'utils/general_functions.php';
include 'utils/system_functions.php';
include 'utils/natural_language.php';
include 'send_mailshot.fork.php';

$worker = new GearmanWorker();
$worker->addServer('127.0.0.1');

$worker->addFunction("au_send_mailshot", "fork_send_mailshot");

while ($worker->work()) {
    if ($worker->returnCode() == GEARMAN_SUCCESS) {
        $db = null;
        exec("kill -9 ".getmypid());
        die();
    }
}

