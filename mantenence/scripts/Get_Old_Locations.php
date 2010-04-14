<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Family.php');
include_once('../../class.Product.php');
include_once('../../class.Supplier.php');
include_once('../../class.Part.php');
include_once('../../class.SupplierProduct.php');
include_once('../../class.Location.php');
include_once('../../class.PartLocation.php');


error_reporting(E_ALL);
date_default_timezone_set('Europe/London');
include_once('../../set_locales.php');
require('../../locale.php');
$_SESSION['locale_info'] = localeconv();

$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );
if(!$con){print "Error can not connect with database server\n";exit;}
$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           
date_default_timezone_set('Europe/London');


$sql=
"INSERT INTO `dw`.`Location Dimension` (`Location Key` ,`Location Warehouse Key` ,`Location Warehouse Area Key` ,`Location Code` ,`Location Mainly Used For` ,`Location Max Weight` ,`Location Max Volume` ,`Location Max Slots` ,`Location Distinct Parts` ,`Location Has Stock` ,`Location Stock Value`)VALUES ('1', '1', '1','Unknown', 'Picking', NULL , NULL , NULL , '0', 'Unknown', '0.00');";
$loc= new Location(1);
if(!$loc->id)
  mysql_query($sql);
$sql2=
"INSERT INTO `dw`.`Location Dimension` (`Location Key` ,`Location Warehouse Key` ,`Location Warehouse Area Key` ,`Location Code` ,`Location Mainly Used For` ,`Location Max Weight` ,`Location Max Volume` ,`Location Max Slots` ,`Location Distinct Parts` ,`Location Has Stock` ,`Location Stock Value`)VALUES ('2', '1', '1','LoadBay', 'Loading', NULL , NULL , NULL , '0', 'Unknown', '0.00');";
$loc= new Location(2);
if(!$loc->id)
  mysql_query($sql2);

$wa_data=array(
	       'Warehouse Area Name'=>'Unknown'
	       ,'Warehouse Area Code'=>'Unk'
	       ,'Warehouse Key'=>1
	       );

$wa=new WarehouseArea('find',$wa_data,'create');



$sql=sprintf("select * from aw_old.product where code='xob-21'  order by code   ");
$result=mysql_query($sql);
while($row2=mysql_fetch_array($result, MYSQL_ASSOC)   ){
  $product_code=$row2['code'];
  $stock_old_db=$row2['stock'];
  print $row2['id']." $product_code \n";
  $sql="select * from aw_old.location  where product_id=".$row2['id']."    " ;
  $result2xxx=mysql_query($sql);
  $primary=true;

  


  while($row=mysql_fetch_array($result2xxx, MYSQL_ASSOC)   ){
     $location_code=$row['code'];
     //  print "$product_code $location_code\n";

     $used_for='Picking';
     if(preg_match('/\d\-\d+\-\d/',$location_code))
       $used_for='Storing';
    // $location=new Location('code',$location_code);
   //  if(!$location->id){
        $location_data=array(
					     'Location Warehouse Key'=>1
					     ,'Location Warehouse Area Key'=>1
					     ,'Location Code'=>$location_code
					     ,'Location Mainly Used For'=>$used_for
					     );
       $location=new Location('find',$location_data,'create');
     
    
     //}
     //     // only work if is one to one relation
     


     $product=new Product('code_store',$product_code,1);
     if($product->id and $location->id){

       $part_skus=$product->get('Parts SKU');
       if(count($part_skus)!=1){
	 print_r($product->data);
	 exit();
       }


       
       $sku=$part_skus[0];

    print "P: $product_code $location_code $used_for Stock: $stock_old_db\n";
  
       
  if($used_for=='Picking'){
	 print "PRIMARY Loc Name:".$row['code']." $product_code  LOC: ".$location->id." SKU: $sku \n";
	
	 //wrap it again
	 
	// print "wraping sku $sku\n";
	// wrap_it($sku);

	 

	 $part= new Part($sku);
	 //	 $part->load('calculate_stock_history','last');


	 print "--------------\n";
	 $associated=$part->get('Current Associated Locations');
	 $num_associated=count($associated);
	 print_r($associated);
	 print "Num associated $num_associated\n";
	 

	  $part_location=new PartLocation('find',array('Part SKU'=>$sku,'Location Key'=>$location->id),'create');
	  $part_location->update_can_pick('Yes');
	 $note='xxx';
	 foreach($associated as  $key=>$location_key){
	    $part_location=new PartLocation($sku.'_'.$location_key);
	     $data=array(
			  'user key'=>0
			  ,'note_out'=>''
			  ,'note_associate'=>''
			  ,'note_in'=>$note

			  ,'Destination Key'=>$location->id
			  ,'Quantity To Move'=>'all'
			  );
	    $part_location->move_stock($data);
	 
	 }
	 
	 
if(false){
	 switch($num_associated){
	 case 1:
	   if($associated[0]==1){
	     //   print "+++++++++\n";
	      $pl=new PartLocation($sku.'_1');


	      $note=_('Part')." SKU".$part->data['Part SKU']." "._('associated with')." ".$location->data['Location Code'];
	      if($location->data['Location Distinct Parts']==0)
		$note.=" ("._("First part-location record").")"." ("._("First record of location been used").")";
	      else
		$note.=" ("._("First part-location record").")";
	      $data=array(
			  'user key'=>0
			  ,'note_out'=>''
			  ,'note_associate'=>''
			  ,'note_in'=>$note

			  ,'Destination Key'=>$location->id
			  ,'Quantity To Move'=>'all'
			  
			  );
	      // EXIT;
	      $pl->move_stock($data);
	   
	      
	      $data=array(
			  'user key'=>0
			  ,'note'=>_('Location now known')
			  );
	      // print "Destroing \n";
	      $pl->destroy($data);
	      
	      

	      //$part->load('stock_history','last');
	      //$part->load('stock');
	      //$location->load('parts_data');
	      //$unk=new Location(1);
	      //$unk->load('parts_data');
	 

	    }elseif($associated[0]==$location->id){
	     print "************  sku ".$sku."  loc ".$location->id."  :) \n";
	  $part_location=new PartLocation('find',array('Part SKU'=>$sku,'Location Key'=>$location->id));

	  //print_r($part_location);
	  $part=new Part($sku);
	  
	  $part->load('calculate_stock_history','last');
	  // print "caca\n";
	  

	    //  if($stock_old_db>0)
	     //exit;
	  break;
	   }else{
	   
	   
	     $part_location=new PartLocation('find',array('Part SKU'=>$sku,'Location Key'=>$location->id),'create');
	    // $part_location->associate();
	       $part->load('calculate_stock_history','last');
	       
	       
	       //  if($stock_old_db!=0)
	      
	       
	       break;
	       
	       
	    }
	   break;
	 case 0:
	   $part_location=new PartLocation('find',array('Part SKU'=>$sku,'Location Key'=>$location->id),'create');
	  // $part_location->associate();
	   $part->load('calculate_stock_history','last');
	  
	   break;
	 default:
	   //	   print_r($associated);
	  
	      $part_location=new PartLocation('find',array('Part SKU'=>$sku,'Location Key'=>$location->id),'create');
	  // $part_location->associate();
	   $part->load('calculate_stock_history','last');
	   // exit;
	   break;

	   // exit("todo b");
	 }
	 
	 }
	 
       }else{
	 print "STORING ".$row['code']." $product_code  LOC: ".$location->id." SKU: $sku \n";
	  $part_location=new PartLocation('find',array('Part SKU'=>$sku,'Location Key'=>$location->id),'create');
	  
	  //$part_location->associate();


 // $location->load('parts_data');
       }
       $primary=false;
       
      
     }
     
  }
  mysql_free_result($result2xxx);

 }
 mysql_free_result($result);



function wrap_it($sku){


  $sql=sprintf("select * from `Inventory Transaction Fact` where `Inventory Transaction Type`='Associate' and `Part SKU`=%d  ",$sku);
//print $sql;
$res=mysql_query($sql);
while($row=mysql_fetch_array($res)){
  $date=$row['Date'];
  $sku=$row['Part SKU'];
  $location_key=$row['Location Key'];
  
  $sql=sprintf("select * from `Inventory Transaction Fact` where `Inventory Transaction Type`='Disassociate'  and `Date`>%s  and `Part SKU`=%d and `Location Key`=%d "
	       ,prepare_mysql($date)
	       ,$sku
	       ,$location_key
	       );
  //print "$sql\n";
  $res2=mysql_query($sql);
  $do_it=true;
  if($row2=mysql_fetch_array($res2)){
    $do_it=false;

  }
  if($do_it){
    //print "adding $sku $location_key\n";
    $part_location=new PartLocation('find',array('Part SKU'=>$sku,'Location Key'=>$location_key),'create');
  }

}
mysql_free_result($res);

}

?>