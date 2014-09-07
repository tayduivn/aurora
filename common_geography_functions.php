<?php
/*

 About:
 Autor: Raul Perusquia <raul@inikoo.com>

 Copyright (c) 2014, Inikoo
 Created: 25 May 2014 08:32:00 CEST, Malaga Spain 

 Version 2.0
*/

function translate_country_name($country){

	switch($country){
		case 'Spain':
			return _('Spain');
		break;
		case 'Germany':
			return _('Germany');
		break;
		case 'United Kingdom':
			return _('United Kingdom');
		break;
		case 'France':
			return _('France');
		break;
		case 'Italy':
			return _('Italy');
		break;
		case 'Poland':
			return _('Poland');
		break;
		case 'Portugal':
			return _('Portugal');
		break;
		case 'Unknown':
			return _('Unknown');
		break;
		default:
			return $country;
	}

	

}

function get_countries_EC_Fiscal_VAT_area() {
$countries_EC_Fiscal_VAT_area=array();
		$sql=sprintf("select `Country Code`  from kbase.`Country Dimension` where `EC Fiscal VAT Area`='Yes'");
		$res=mysql_query($sql);
		while($row=mysql_fetch_assoc($res)){
			$countries_EC_Fiscal_VAT_area[]=$row['Country Code'];
		}
	
return $countries_EC_Fiscal_VAT_area;
}


function gbr_postcode_first_part($mypostcode) {
if (($posOfSpace = stripos($mypostcode," ")) !== false) return substr($mypostcode,0,$posOfSpace);
    // Deal with the format BS000
    if (strlen($mypostcode) < 5) return $mypostcode;

    $shortened = substr($mypostcode,0,5);
    if ((string)(int)substr($shortened,4,1) === (string)substr($shortened,4,1)) {
       // BS000. Strip one and return
       return substr($shortened,0,4);
    }
    else {
      if ((string)(int)substr($shortened,3,1) === (string)substr($shortened,3,1)) {
         return substr($shortened,0,3);
      }
      else return substr($shortened,0,2);
    }
}


?>
