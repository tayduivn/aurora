<?php
include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Family.php');
include_once('../../class.Product.php');
include_once('../../class.Supplier.php');
include_once('../../class.Part.php');
include_once('../../class.SupplierProduct.php');
error_reporting(E_ALL);



$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if(!$con){print "Error can not connect with database server\n";exit;}
//$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
  

require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           
date_default_timezone_set('GMT');

/* $sql="select `Product Code` from `Product Dimension`  group by `Product Code` "; */

/* $result=mysql_query($sql); */
/* while($row=mysql_fetch_array($result)   ){ */
/*   $code=$row['Product Code']; */
/*   print "$code\n"; */

$pics=array();

$path="/home/raul/aw_pic_bk/";
$img_array_full_path = glob($path."*.jpg");
//print_r($img_array_full_path);
foreach($img_array_full_path as $pic_path){
   $_pic_path=preg_replace('/.*\//','',$pic_path);
  if(preg_match('/[a-z0-9\-]+(|_l|_bis|_tris|_quad|_display|_displ|dpl|_box)+.jpg/',$_pic_path)){
    // print "$pic_path\n";
    if(preg_match('/^[a-z]+\-\d+[a-z]{0,3}/',$_pic_path,$match)){
      $root=$match[0];
      //print "$root -> $pic_path\n";
      if(array_key_exists($root,$pics))
	$pics[$root][]=$pic_path;
      else
	$pics[$root]=array($pic_path);
    }
  }
} 
//print_r($pics);


chdir('../../');
foreach($pics as $key=>$value){
  $sql=sprintf("select `Product ID` from `Product Dimension` where `Product Code`=%s ",prepare_mysql($key));
  //print "$sql\n";
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    $product=new Product('pid',$row['Product ID']);
    foreach($value as $img_filename){
     
      //print "---------- ".$img_filename."\n";
      $rand=rand().rand();
      $tmp_file='app_files/pics/tmp.jpg';
      copy($img_filename,$tmp_file );
      
      $product->add_image($tmp_file,'principal');
      //print_r($product);
      // print $product->msg."\n";
      $product->update_main_image();
      // unlink($tmp_file);
    }

  }
}


?>