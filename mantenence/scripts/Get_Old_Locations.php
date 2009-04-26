<?
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once('../../app_files/db/dns.php');
include_once('../../classes/Department.php');
include_once('../../classes/Family.php');
include_once('../../classes/Product.php');
include_once('../../classes/Supplier.php');
include_once('../../classes/Part.php');
include_once('../../classes/SupplierProduct.php');
include_once('../../classes/Location.php');
include_once('../../classes/PartLocation.php');

error_reporting(E_ALL);
$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );
if(!$con){print "Error can not connect with database server\n";exit;}
$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
require_once '../../common_functions.php';
mysql_query("SET time_zone ='UTC'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           
date_default_timezone_set('Europe/London');

// $sql="select * from aw_old.location  group by code" ;
//  $result=mysql_query($sql);
//  while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
//    $code=$row['code'];
//    if(preg_match('/^(\d+\-\d+\-\d+)$/i',$code))
//      $tipo='storing';
//    else
//      $tipo='picking';
   
//    $data=array(
// 	       'Location Code'=>$code
// 	       ,'Location Mainly Used For'=>$tipo
// 	       ,'Location Warehouse Key'=>1
// 	       ,'Location Area'=>''
// 	       );
//    $location=new Location('code',$code);
//    if(!$location->id)
//      $location=new Location('create',$data);
   
   

//  }
$sql=sprintf("select * from aw_old.product where code='LBI-03'    ");
$result=mysql_query($sql);
while($row2=mysql_fetch_array($result, MYSQL_ASSOC)   ){
  $product_code=$row2['code'];
  print $row2['id']." $product_code \n";
  $sql="select * from aw_old.location  where product_id=".$row2['id']."    " ;
  $result2xxx=mysql_query($sql);
  $primary=true;

  


  while($row=mysql_fetch_array($result2xxx, MYSQL_ASSOC)   ){
    print "--------------\n";
     $location_code=$row['code'];


     $used_for='Picking';
     if(preg_match('/\d\-\d+\-\d/',$location_code))
       $used_for='Storing';
     $location=new Location('code',$location_code);
     if(!$location->id){
       
       $location=new Location('create',array(
					     'Location Warehouse Key'=>1
					     ,'Location Area'=>''
				    ,'Location Code'=>$location_code
					     ,'Location Mainly Used For'=>$used_for
					     ));
     }
     //     // only work if is one to one relation
     


     $product=new Product('code',$product_code);
     if($product->id and $location->id){

       $part_skus=$product->get('Parts SKU');
       if(count($part_skus)!=1){
	 print_r($product->data);
	 exit();
       }


      
       $sku=$part_skus[0];


       
       if($primary){
	 print "PRIMARY ".$row['code']." $product_code  LOC: ".$location->id." SKU: $sku \n";
	 $part= new Part($sku);
	 $part->load('calculate_stock_history','continue');
	 $associated=$part->get('Associated Locations');
	 $num_associated=count($associated);
	 switch($num_associated){
	 case 1:
	   if($associated[0]==1){
	      $pl=new PartLocation('1_'.$sku);
	      $data=array(
			  'user key'=>0
			  ,'note'=>_('First record of location')
			  ,'move_to'=>$location->id
			  ,'qty'=>'all'
			  
			  );
	      
	      $pl->move_to($data);
	     
	      $data=array(
			  'user key'=>0
			  ,'note'=>_('Location now known')
			  
			  );
	      $pl->destroy($data);
	      
	      $part->load('stock');
	      $part->load('stock_history');
	      
	      
	      $location->load('parts_data');
	      $unk=new Location(1);
	      $unk->load('parts_data');
	 

	    }elseif($associated[0]==$location->id){
	      break;
	    }else{
	      print_r( $location->data);
	      print_r($associated);
	      exit("todo a");
	    }
	      break;
	 default:
	   print_r($associated);
	   exit("todo b");
	 }
	 
       }else{
	 print "STORING ".$row['code']." $product_code  LOC: ".$location->id." SKU: $sku \n";
	 
	 $pl=new PartLocation($location->id.'_'.$sku);
	 $data=array(
		     'user key'=>0
		     ,'note'=>''
		     ,'options'=>'unknown'
		     ,'date'=>''
		     );
	 $pl->create($data);
	 $location->load('parts_data');
       }
       $primary=false;
       
      
     }
     
  }

 }



?>