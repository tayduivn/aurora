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
//$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
require_once '../../common_functions.php';
mysql_query("SET time_zone ='UTC'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           
date_default_timezone_set('Europe/London');
 if(isset($argv[1]) and $argv[1]=='fl'){
$sql="select * from `Product Dimension`   order by `Product Key` desc ";
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)   ){
   $product=new Product($row['Product Key']);
   $for_sale_since=$product->data['Product Valid From'];
   $last_sold_date=$product->data['Product Valid To'];
   $sql=sprintf("update `Product Dimension` set `Product For Sale Since Date`=%s ,`Product Last Sold Date`=%s where `Product Key`=%d "
		,prepare_mysql($for_sale_since)
		,prepare_mysql($last_sold_date)
		,$product->id
		);
   if(!mysql_query($sql))
     exit("$sql\ncan not update product days\n");

   print "Pre ".$product->id."\r";

}
 }

//$sql="select * from `Product Dimension` where `Product Code`='FO-A1'";
$sql="select * from `Product Dimension`   order by `Product Key`  ";
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)   ){

  
  
  

  
  $product=new Product($row['Product Key']);
  
  $product->load('sales');
  $product->load('parts');
 

  if(isset($argv[1]) and $argv[1]=='first'){


  if($product->data['Product Same Code Most Recent']=='Yes'){
    $state='For sale';
    if($product->data['Product 1 Year Acc Quantity Ordered']==0 and (strtotime($product->data['Product Valid From'])<strtotime('today -1 year')    ))
      $state='Discontinued';
    
    $sql=sprintf("select id,code  from aw_old.product  where product.code=%s and  condicion=2 and stock=0  ",prepare_mysql($product->data['Product Code']));
    $result2a=mysql_query($sql);
    if($row2a=mysql_fetch_array($result2a, MYSQL_ASSOC)   ){
      $state='Discontinued';
    }
    
  }else
    $state='Historic';

  if($state=='Historic'){
    $record_state='Historic';
    $state='Not for sale';
    $web_state='Offline';
  }
 if($state=='Discontinued'){
    $record_state='Normal';
    $state='Discontinued';
    $web_state='Online';
  }
 if($state=='For sale'){
    $record_state='Normal';
    $state='For sale';
    $web_state='Online';
  }
  

   $sql=sprintf("update `Product Dimension` set  `Product Sales State`=%s,`Product Record Type`=%s,`Product Web State`=%s  where `Product Key`=%s",
		prepare_mysql($state)
		,prepare_mysql($record_state)
		,prepare_mysql($web_state)

		,$product->id);
   // print "$sql\n\n";
  if(!mysql_query($sql))
    exit("can not upodate state of the product");
  }
  $product->load('days');
  $product->load('stock');

  print $row['Product Key']."\n";




 }



?>