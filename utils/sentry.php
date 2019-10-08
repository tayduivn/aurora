<?php
/*

 About:
 Author: Raul Perusquia <rulovico@gmail.com>
 Created: 10 November 2018 at 04:03:30 GMT+8, Kuala Lumpur, Malaysia

 Copyright (c) 2017, Inikoo

 Version 2.0
*/
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;

if(defined('ROLLBACK_ACCESS_TOKEN')) {
    Rollbar::init(
        array(
            'access_token' => ROLLBACK_ACCESS_TOKEN,
            'environment'  => 'AU'
        )
    );
}

if(defined('SENTRY_DNS_AU')){
    Sentry\init(['dsn' => SENTRY_DNS_AU ]);
}

