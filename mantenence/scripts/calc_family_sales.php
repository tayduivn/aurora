<?php

include_once('../../app_files/db/dns.php');
include_once('../../class.Family.php');
require_once 'MDB2.php';            // PEAR Database Abstraction Layer
require_once '../../common_functions.php';
$db =& MDB2::singleton($dsn);       
if (PEAR::isError($db)){echo $db->getMessage() . ' ' . $db->getUserInfo();}
if(DEBUG)PEAR::setErrorHandling(PEAR_ERROR_RETURN);
  
mysql_query("SET time_zone ='+0:00'");
require_once '../../myconf/conf.php';           
date_default_timezone_set('UTC');


$sql="select id from product_group ";
$res=mysql_query($sql);if (PEAR::isError($res) and DEBUG ){die($res->getMessage());}
  while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
    $id=$row['id'];
    $family=new Family($id);
    $family->load('first_date','save');
    $family->load('sales','save');
    printf("$id\r");
  }

?>