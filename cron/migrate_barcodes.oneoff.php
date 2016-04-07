<?php

/*
 About:
 Autor: Raul Perusquia <raul@inikoo.com>
 Created: 16 April 2016 at 15:25:28 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

$handle = fopen("barcodes.csv", "r");
require_once 'common.php';


$default_DB_link=@mysql_connect($dns_host, $dns_user, $dns_pwd );
if (!$default_DB_link) {
	print "Error can not connect with database server\n";
}
$db_selected=mysql_select_db($dns_db, $default_DB_link);
if (!$db_selected) {
	print "Error can not access the database\n";
	exit;
}
mysql_set_charset('utf8');
mysql_query("SET time_zone='+0:00'");



require_once 'utils/get_addressing.php';
require_once 'utils/parse_natural_language.php';
require_once 'utils/barcode_functions.php';

require_once 'class.Barcode.php';
require_once 'class.Part.php';


$sql="truncate `Barcode Dimension`; truncate `Barcode Asset Bridge`; truncate `Barcode History Bridge`";
$db->exec($sql);

$sql=sprintf('update  `Part Dimension` set `Part Barcode Number`=NULL , `Part Barcode Key`=NULL;  ');
$db->exec($sql);

$editor=array(
	'Author Name'=>'',
	'Author Alias'=>'',
	'Author Type'=>'',
	'Author Key'=>'',
	'User Key'=>0,
	'Date'=>gmdate('Y-m-d H:i:s')
);


$row = 1;
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	$num = count($data);
	$row++;
	if ($num>1 and strlen($data[1])==12) {
		$number=$data[1];

		if (isset($data[8]) and   strlen($data[8])==13) {



			$note=$data[6].', '.$data[7];
			$note=preg_replace('/\, $/', '', $note);

			$cross_reference_number=$data[8];

			$check_digit=ean_checkdigit($number);

			if ($number.$check_digit!=$cross_reference_number) {
				print "Error check digit  $number $check_digit  $cross_reference_number \n";
				continue;
			}
			$barcode_data=array(
				'Barcode Number'=>$number.$check_digit,
				'Barcode Sticky Note'=>$note
			);

			$add_asset=false;

			if ($data[2]!='') {
				$part=new Part('reference', $data[2]);
				if ($part->id) {
					$add_asset=true;


				}else {
					$barcode_data['Barcode Status']='Reserved';
					$note=$data[2].', '.$note;
					$note=preg_replace('/\, $/', '', $note);
					
					$barcode_data['Barcode Sticky Note']=$note;
					
					
				}


			}


			$barcode=new Barcode('find', $barcode_data, 'create');

			if ($add_asset ) {
				$asset_data=array(
					'Barcode Asset Type'=>'Part',
					'Barcode Asset Key'=>$part->id,
					'Barcode Asset Assigned Date'=>$part->get('Part Valid From')
				);

				$barcode->assign_asset($asset_data);


			}


		}

	}


}
fclose($handle);



?>
