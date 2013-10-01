<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once '../../app_files/db/dns.php';
include_once '../../class.Department.php';
include_once '../../class.Family.php';
include_once '../../class.Product.php';
include_once '../../class.Supplier.php';
include_once '../../class.Part.php';
include_once '../../class.PartLocation.php';

include_once '../../class.SupplierProduct.php';
error_reporting(E_ALL);

date_default_timezone_set('UTC');
include_once '../../set_locales.php';
require '../../locale.php';
$_SESSION['locale_info'] = localeconv();

$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if (!$con) {print "Error can not connect with database server\n";exit;}

$db=@mysql_select_db($dns_db, $con);
if (!$db) {print "Error can not access the database\n";exit;}


require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';
date_default_timezone_set('UTC');


$options='no_history';

//$sql="select * from `Product Dimension` where `Product Code`='FO-A1'";
$sql="select * from `Part Dimension` where `Part SKU`=10635 order by `Part SKU`";
$sql="select `Part SKU` from `Part Dimension`   order by `Part SKU`";

$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
	$part=new Part('sku',$row['Part SKU']);
	/*
	if ($part->data['Part Tariff Code']!='') {
		$part->update_tariff_code($part->data['Part Tariff Code']);
		$part->update_tariff_code_valid();
	}
	*/

	$part->update_available_forecast();
	$part->update_stock_state();
	$part->update_days_until_out_of_stock();
	$part->update_used_in();

$part->update_last_date_from_transactions('Sale');
//$part->update_last_date_from_transactions('In');
	print $row['Part SKU']."\r";
continue;// maybe the bottom has to run for AWR o AW if not done yet :S

	$product_ids=$part->get_product_ids();



	foreach ($product_ids as $product_id) {
		$product=new Product('pid',$product_id);
		if ($product->data['Product Use Part Properties']=='Yes' ) {
			$product->update_weight_from_parts();
		}
		if ($product->data['Product Use Part Properties']=='Yes' and $product->data['Product Part Units Ratio']==1) {

			$product->update_field_switcher('Product XHTML Package Dimensions',$part->data['Part Package XHTML Dimensions']);
			$product->update_field_switcher('Product XHTML Unit Dimensions',$part->data['Part Unit XHTML Dimensions']);

		}
		
		
		if ($product->data['Product Use Part Tariff Data']=='Yes') {
					$product->update_field('Product Tariff Code',$part->data['Part Tariff Code'],$options);
					$product->update_field('Product Duty Rate',$part->data['Part Duty Rate'],$options);

				}
		

	}


	continue;

	//$locations=$part->get_picking_location_historic('2012-03-14 00:00:00',1);
	//print_r($locations);
	//exit;


	//$part->update_estimated_future_cost();

	/*
  //Get  status
  if(isset($argv[1]) and $argv[1]=='first'){
  $part_valid_from=$part->data['Part Valid From'];

  $sql=sprintf(" select `Product Record Type` from  `Product Part Dimension` PPD  left join    `Product Dimension` P  on (PPD.`Product ID`=P.`Product ID`)    left join `Product Part List` PPL on (PPD.`Product Part Key`=PPL.`Product Part Key`)  where `Part SKU`=%d  and `Product Part Most Recent`='Yes'  ",$part->data['Part SKU']);
  //  print "$sql\n";
  $result2=mysql_query($sql);
  $discontinued=true;
  while($row2=mysql_fetch_array($result2, MYSQL_ASSOC)   ){
    if(!($row2['Product Record Type']=='Historic' or $row2['Product Record Type']=='Discontinued')){
      $discontinued=false;
  }

    if($discontinued){

      $part->update_status('Discontinued');

    }else
      $part->update_status('In Use');






  }
  }
  */
	//  $part->update_number_transactions();
	$part->update_main_state();

	$part->update_used_in();
	$part->update_supplied_by();


	$product_ids=$part->get_product_ids();

	foreach ($product_ids as $product_id) {
		$product=new Product('pid',$product_id);
		$product->update_field('Product Tariff Code',$part->data['Part Tariff Code'],'');
	}

	//  $part->update_picking_location();
	//  $part->update_main_state();
	// $part->update_stock();
	// $part->update_availability();



	//$part->update_up_today_sales();
	//$part->update_interval_sales();
	//$part->update_last_period_sales();




	//   $part->update_stock_history();
	//   $part->update_future_costs();
	print $row['Part SKU']."\r";


}


?>
