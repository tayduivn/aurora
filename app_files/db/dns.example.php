<?php
/*
Change:
PASSWORD
USER
HOST

to suit your database configuration
and save as to dns.php



*/

$dns_pwd=PASSWORD;
$dns_db='dw';
$dns_user=USER;
$dns_host=HOST;
$dsn = 'mysql://'.$dns_user.':'.$dns_pwd.'@'.$dns_host.'/'.$dns_db;

?>