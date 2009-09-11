<?php
error_reporting(E_ALL);

require_once '../../app_files/db/dns.php';
require_once '../../class.TimeSeries.php';
$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );
if(!$con){print "Error can not connect with database server\n";exit;}
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}

require_once '../../common_functions.php';
mysql_query("SET time_zone ='UTC'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/timezone.php'; 
date_default_timezone_set(TIMEZONE) ;

include_once('../../set_locales.php');

require_once '../../conf/conf.php';   


date_default_timezone_set('Europe/Madrid');
$_SESSION['lang']=1;


$forecast=false;


if(true){
  print "inv\n";
  $tm=new TimeSeries(array('w','invoices'));
  $tm->get_values();
  $tm->save_values();
  if($forecast)
    $tm->forecast();
  exit;
  $tm=new TimeSeries(array('q','invoices'));
  $tm->get_values();
  $tm->save_values();
  if($forecast)
    $tm->forecast();
  exit;

  $tm=new TimeSeries(array('m','invoices'));
  $tm->get_values();
  $tm->save_values();
  if($forecast)
    $tm->forecast();

  
  $tm=new TimeSeries(array('m','profit invoices'));
  $tm->get_values();$tm->save_values();
  if($forecast)
    $tm->forecast();

}
exit;
$sql="select * from `Store Dimension`";
$res=mysql_query($sql);

while( $row=mysql_fetch_array($res)){
  print 'store ('.$row['Store Key'].') sales';
  $tm=new TimeSeries(array('m','store ('.$row['Store Key'].') sales'));
  $tm->get_values();

  $tm->save_values();
  if($forecast)
    $tm->forecast();
 
}

$sql="select * from `Product Department Dimension` ";
$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
  print 'product department ('.$row['Product Department Key'].') sales'."\n";
  $tm=new TimeSeries(array('m','product department ('.$row['Product Department Key'].') sales'));
  $tm->get_values();
  $tm->save_values();
  if($forecast)
    $tm->forecast();
}

$sql="select * from `Product Family Dimension`";
$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
  print 'product family ('.$row['Product Family Key'].') sales'."\n";
  $tm=new TimeSeries(array('m','product family ('.$row['Product Family Key'].') sales'));
  $tm->get_values();
  $tm->save_values();
   if($forecast)
     $tm->forecast();
}


$sql="select * from `Product Dimension` group by `Product ID`";
$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
  print 'product id ('.$row['Product Code'].') '.$row['Product ID'].' sales'."\n";
  $tm=new TimeSeries(array('m','product id ('.$row['Product ID'].') sales'));
  $tm->get_values();
  $tm->save_values();
   if($forecast)
  $tm->forecast();
}


$sql="select * from `Product Dimension` group by `Product Same Code Most Recent Key`";
$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
  print 'product code ('.$row['Product Code'].') sales'."\n";
  $tm=new TimeSeries(array('m','product code ('.$row['Product Same Code Most Recent Key'].') sales'));
  $tm->get_values();
  $tm->save_values();
  if($forecast)
    $tm->forecast();
}

?>