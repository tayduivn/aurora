<?
include_once('../../app_files/db/dns.php');
include_once('../../classes/Department.php');
include_once('../../classes/Family.php');
include_once('../../classes/Product.php');
include_once('../../classes/Supplier.php');
include_once('../../classes/Part.php');
include_once('../../classes/SupplierProduct.php');
error_reporting(E_ALL);



$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if(!$con){print "Error can not connect with database server\n";exit;}
$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
  

require_once '../../common_functions.php';
mysql_query("SET time_zone ='UTC'");
mysql_query("SET NAMES 'utf8'");
require_once '../../myconf/conf.php';           
date_default_timezone_set('Europe/London');


$start_date='2003-01-01';

$end_date='2012-01-01';

$i=0;
$date=strtotime($start_date);
//print "$date ". strtotime($end_date)."\n";
while($date<strtotime($end_date)){
  $i++;
  $sql=sprintf("insert into `Month Dimension` values ('%s','%s')",date('Ym',$date),date('Y-m-d',$date));
  mysql_query($sql);
  print "$sql\n";
  $date=strtotime(date('Y-m-d',$date).' +1 month');
  if($i>100000)
    exit;


 }



?>