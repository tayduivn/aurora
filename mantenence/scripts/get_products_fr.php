<?php
//include("../../external_libs/adminpro/adminpro_config.php");
//include("../../external_libs/adminpro/mysql_dialog.php");

include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Deal.php');
include_once('../../class.Charge.php');

include_once('../../class.Family.php');
include_once('../../class.Product.php');
include_once('../../class.Supplier.php');
include_once('../../class.Part.php');
include_once('../../class.Warehouse.php');
include_once('../../class.Node.php');
include_once('../../class.Shipping.php');
include_once('../../class.SupplierProduct.php');
error_reporting(E_ALL);

date_default_timezone_set('UTC');

include_once('../../set_locales.php');
require('../../locale.php');
$_SESSION['locale_info'] = localeconv();
$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if(!$con){print "Error can not connect with database server\n";exit;}

$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}
$codigos=array();


require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           


$software='Get_Products.php';
$version='V 1.0';

$Data_Audit_ETL_Software="$software $version";




$file_name='/data/plaza/AWorder2009France.xls';
$tcsv_file='fr_tmp.8859.csv';
$csv_file='fr_tmp.utf.csv';





exec('/usr/local/bin/xls2csv    -s cp1252   -d 8859-1   '.$file_name.' > '.$tcsv_file);
exec("iconv   -f  ISO8859-1  -t UTF-8  --output  $csv_file $tcsv_file");

//$csv_file='fr_utf8.csv';

$handle_csv = fopen($csv_file, "r");
$column=0;
$products=false;
$count=0;

$store=new Store("code","FR");
$store_key=$store->id;

$gold_camp=new Deal('code','FR.GR');
$vol_camp=new Deal('code','FR.Vol');
$bogof_camp=new Deal('code','FR.BOGOF');
$fam_promo=$fam_promo=new Family('code','Promo_FR',$store_key);
$fam_promo_key=$fam_promo->id;



$__cols=array();
$inicio=false;
while(($_cols = fgetcsv($handle_csv))!== false){
  
  if(count($_cols)<=6){
    continue;

  }


//  foreach($_cols as $_key=>$_value){
 //   if(preg_match('/Diffuseurs /',$_value)){
 //      $_cols[$_key]=preg_replace("/\xC3\x20/","à ",$_value);
 //      print "$_key $_value\n";
 //    print_r($_cols);
     //exit("caca");
 //      }
  //   }


  $code=$_cols[3];

 
  if($code=='AWFO-01' and !$inicio){
    $inicio=true;
    $x=$__cols[count($__cols)-4];
    $z=$__cols[count($__cols)-3];
    $a=$__cols[count($__cols)-2];
    $b=$__cols[count($__cols)-1];
    $c=$_cols;
    $__cols=array();
    $__cols[]=$x;
    $__cols[]=$z;
    $__cols[]=$a;
    $__cols[]=$b;
    $__cols[]=$c;

  }elseif(preg_match('/Special Xmas Bonus/',$_cols[6])){
    break;
  }
  
  $__cols[]=$_cols;
}

$new_family=true;
$department_name='';
$current_fam_name='';
$current_fam_code='';
$fam_position=-10000;
$promotion_position=100000;
$promotion='';


foreach($__cols as $cols){

 if(!isset($cols[18]) and $cols[3]=='' and $cols[6]==''){
   
    continue;
  }

 

  $is_product=true;
  $code=_trim($cols[3]);
  $price=$cols[9];
  $supplier_code=_trim($cols[23]);
  $part_code=_trim($cols[24]);
  $supplier_cost=$cols[27];
  $units=$cols[5];
  $rrp=$cols[18];
  $supplier_code=_trim($cols[23]);
  $w=$cols[31];
  $description=_trim($cols[6]);
  $fam_special_char=_trim($cols[7]);
  $special_char=_trim($cols[8]);
  //print "FSC:".$cols[7]." $fam_special_char\n";

//$description=_trim( mb_convert_encoding($cols[6], "UTF-8", "ISO-8859-1"));
  //$fam_special_char=_trim( mb_convert_encoding($cols[7], "UTF-8", "ISO-8859-1"));
  //$special_char=_trim( mb_convert_encoding($cols[8], "UTF-8", "ISO-8859-1"));
  
  // if(preg_match('/fo-a3/i',$code))
  //  break;

  if(!preg_match('/^DONE$/i',$cols[0]))
    $is_product=false;
  $code=_trim($code);
  if($code=='' or !preg_match('/\-/',$code) or preg_match('/total/i',$price)  or  preg_match('/^(pi\-|cxd\-|fw\-04)/i',$code))
    $is_product=false;
  if(preg_match('/^(ob\-108|ob\-156|ish\-94|rds\-47)/i',$code))
    $is_product=false;
  if(preg_match('/^staf-set/i',$code) and $price=='')
    $is_product=false;
  if(preg_match('/^hook-/i',$code) and $price=='')
    $is_product=false;
  if(preg_match('/^shop-fit-/i',$code) and $price=='')
    $is_product=false;
  if(preg_match('/^pack-01a|Pack-02a/i',$code) and $price=='')
    $is_product=false;
  if(preg_match('/^(DB-IS|EO-Sticker|ECBox-01|SHOP-Fit)$/i',$code) and $price=='')
    $is_product=false;
  
  
  if(preg_match('/^credit|Freight|^frc\-|^cxd\-|^wsl$|^postage$/i',$code) )
    $is_product=false;


  
  if($is_product){
    
  
    //  if(!preg_match('/reed\-/i',$code))
    // continue;
    //print "$code  ---\n";
    $part_list=array();
    $rules=array();
    
    $current_fam_name=$fam_name;
    $current_fam_code=$fam_code;
    if($new_family){
      //    print "New family $column $promotion_position \n";
      if($promotion!='' and  ($column-$promotion_position)<4 ){
	$current_promotion=$promotion;
      }else
	$current_promotion='';
      $new_family=false;
    }




    
    $deals=array();
    if(preg_match('/a partir de\s*\d+.*\d+\%/i',_trim($current_promotion))){
      if(preg_match('/\d+\%/i',$current_promotion,$match))
	$allowance=$match[0];
      if(preg_match('/a partir de \d+/i',$current_promotion,$match))
	$terms=preg_replace('/[^\d]/','',$match[0]);
      
      $deals[]=array(
		     'Deal Metadata Name'=>'Gold Reward'
		     ,'Deal Metadata Allowance Description'=>$allowance
		     );
      
      $deals[]=array(
		     'Deal Metadata Name'=>'Family Volume Discount'
		     ,'Deal Metadata Allowance Description'=>$allowance
		     ,'Deal Metadata Terms Description'=>'A partir de '.$terms
		     );	
    }else
      $deals=array();
  
    if($units=='' OR $units<=0)
      $units=1;
  
    $description=_trim($cols[6]);
		       //$description=_trim( mb_convert_encoding($cols[6], "UTF-8", "ISO-8859-1"));
    
    $rrp=$cols[18];
    $supplier_code=_trim($cols[21]);
    
    $w=$cols[28];
      $code=preg_replace('/L\&P\-/','LLP-',$code);

    

    if(    preg_match('/Bag-02Mx|Bag-04mx|Bag-05mx|Bag-06mix|Bag-07MX|Bag-12MX|Bag-13MX|FishP-Mix|IncIn-ST|IncB-St|LLP-ST|L\&P-ST|EO-XST|AWRP-ST/i',$code) or      $code=='EO-ST' or $code=='MOL-ST' or  $code=='JBB-st' or $code=='LWHEAT-ST' or  $code=='JBB-St' 
       or $code=='Scrub-St' or $code=='Eye-st' or $code=='Tbm-ST' or $code=='Tbc-ST' or $code=='Tbs-ST'
       or $code=='GemD-ST' or $code=='CryC-ST' or $code=='GP-ST'  or $code=='DC-ST'
	   or ($description=='' and ( $price=='' or $price==0 ))


       ){
      print "Skipping $code\n";
      continue;
    }

      
  if(!is_numeric($price) or $price<=0){
    print "Price Zero  $code \n";
    $price=0;
  }
    
  
  if($code=='Tib-20')
    $supplier_cost=0.2;
  
  if(!is_numeric($supplier_cost)  or $supplier_cost<=0 ){
    //   print_r($cols);
    //print "$code   assumind supplier cost of 40%  \n";
    $supplier_cost=0.4*$price/$units;
    
  }
  
  if(array_key_exists($code,$codigos)){
    print "Product: $code is duplicated\n";
    continue;
  }
  
  $codigos[$code]=1;
  
  $uk_product=new Product('code_store',$code,1);

    
    
    if($units=='')
      $units=1;
      
    if(is_numeric($rrp))
      $rrp=sprintf("%.2f",$rrp*$units);
    else
      $rrp='';
      
    
    if($fam_special_char=='' or $special_char==''){
     
      $_f=preg_replace('/s$/i','',$current_fam_name);
      $special_char=preg_replace('/'.str_replace('/','\/',$_f).'$/i','',$description);
      $special_char=preg_replace('/'.str_replace('/','\/',$current_fam_name).'$/i','',$special_char);
      $special_char=_trim($special_char);
      if($special_char==$description){
	$description=$current_fam_name.' '.$special_char;
	$fam_special_char=$current_fam_name;
      }else
	$fam_special_char=preg_replace('/'.str_replace('/','\/',$special_char).'$/i','',$description);
    }
	    
	

    if(is_numeric($w)){
      $w=$w*$units;
      if($w<0.001 and $w>0)
	$_w=0.001;
      else
	$_w=sprintf("%.3f",$w);
    }else
      $_w='';
      
    if(preg_match('/^pi-|catalogue|^info|Mug-26x|OB-39x|SG-xMIXx|wsl-1275x|wsl-1474x|wsl-1474x|wsl-1479x|^FW-|^MFH-XX$|wsl-1513x|wsl-1487x|wsl-1636x|wsl-1637x/i',_trim($code))){

	
      $family=new Family($fam_promo_key);
	 
    }else{
    
    if($department_name=='Gegenstände für Sammler')
        $department_code='Collect';
        if($department_name=='Ökotaschen')
        $department_code='EcoBag';
        
      $dep_data=array(
		      'Product Department Code'=>$department_code,
		      'Product Department Name'=>$department_name,
		      'Product Department Store Key'=>$store_key
		      );
      $department=new Department('find',$dep_data,'create');

      if($department->error){
      print_r($dep_data);
	print_r($department);
	exit("error\n");
      }
	
      $current_fam_code=preg_replace('/^L\&P$/i','LLP',$current_fam_code);


      $fam_data=array(
		      'Product Family Code'=>$current_fam_code,
		      'Product Family Name'=>$current_fam_name,
		      'Product Family Main Department Key'=>$department->id,
		      'Product Family Store Key'=>$store_key,
		      'Product Family Special Characteristic'=>$fam_special_char
		      );
      $family=new Family('find',$fam_data,'create');		 

    }


    if(!$family->id){
      print_r($family);
      exit("error not fam\n");
   
    }


    foreach($deals as $deal_data){
      //         print_r($deal_data);
      //exit;

      $deal_data['Store Key']=$store_key;

      if(preg_match('/Family Volume/i',$deal_data['Deal Metadata Name'])){

	$data=array(
		    'Deal Metadata Allowance Target Key'=>$family->id,
		    'Deal Metadata Trigger Key'=>$family->id,

		    'Deal Metadata Allowance Description'=>$deal_data['Deal Metadata Allowance Description'],
		    'Deal Metadata Terms Description'=>$deal_data['Deal Metadata Terms Description']
		    
		    );

	$vol_camp->create_deal('[Product Family Code] Volume Discount',$data);


      }


      if(preg_match('/Gold/i',$deal_data['Deal Metadata Name'])){

	$data=array(
		    'Deal Metadata Trigger Key'=>$family->id,
		    'Deal Metadata Allowance Target Key'=>$family->id,
		    'Deal Metadata Allowance Description'=>$deal_data['Deal Metadata Allowance Description']
		    );

	$gold_camp->create_deal('[Product Family Code] Goldprämie',$data);

      }

      if(preg_match('/bogof/i',$deal_data['Deal Metadata Name'])){
	$data=array(
		    'Deal Metadata Trigger Key'=>$family->id,
		    'Deal Metadata Allowance Target Key'=>$family->id,
		    'Deal Metadata Allowance Description'=>$deal_data['Deal Metadata Allowance Description']
		    );

	$bogof_camp->create_deal('[Product Family Code] BOGOF',$data);
      }
    }  
    $data=array(
		'product code'=>$code,
		'product store key'=>$store_key,
		'product locale'=>'fr_FR',
		'product currency'=>'EUR',
  'product stage'=>'Normal',
		'product sales type'=>'Public Sale',
		'product type'=>'Normal',
		'product record type'=>'Normal',
		'Product Web Configuration'=>'Online Auto',

		  
		'product price'=>sprintf("%.2f",$price),
		'product rrp'=>$rrp,
		'product units per case'=>$units,
		'product name'=>$description,
		'product family key'=>$family->id,
		'product special characteristic'=>$special_char,
		'product family special characteristic'=>$fam_special_char,
		'product net weight'=>$_w,
		'product gross weight'=>$_w,
		 'product valid from'=>date('Y-m-d H:i:s'),
		  'product valid to'=>date('Y-m-d H:i:s'),
		//'deals'=>$deals
		);
      //   print_r($data);

    if($uk_product->id)
    $parts=$uk_product->get('Parts SKU');
    else{
      print("Product not found in uk: ".$code."\n");
      continue;
    }

    $product=new Product('find',$data,'create');
    if($product->new){
      $product->update_for_sale_since(date("Y-m-d H:i:s",strtotime("now +1 seconds")));
      if(isset($parts[0])){
 	$part_list[]=array(
 			   'Product ID'=>$product->get('Product ID'),
 			   'Part SKU'=>$parts[0],
 			   'Product Part Id'=>1,
 			   'requiered'=>'Yes',
 			   'Parts Per Product'=>1,
 			   'Product Part Type'=>'Simple Pick'
 			   );
	
$product->new_current_part_list(array(),$part_list);
 	$product->update_parts();
	$part =new Part('sku',$parts[0]);
 	$part->update_used_in();
      }
     
      
    }
 $product->change_current_key($product->id);
 $product->update_rrp('Product RRP',$rrp);
  }else{

    $new_family=true;
    
    // print "Col $column\n";
    //print_r($cols);
    if(  preg_match('/donef/i',$cols[0])       ){
      $fam_code=$cols[3];
      $fam_name=_trim($cols[6]);

  $fam_name=_trim( $cols[6]);


//print "FAM ----- ".$cols[6]." $fam_name  ------\n";
//print_r($cols);
  //$fam_name=_trim( mb_convert_encoding($cols[6], "UTF-8", "ISO-8859-1"));
  //   $fam_name=preg_replace("/\xC3\xA0/","à",$fam_name);
     $fam_position=$column;
//print "fam name $fam_name \n";
      
    }
    
    if(preg_match('/a partir de\s*\d+.*\d+\%/i',_trim($cols[6]))){
      

      $promotion=$cols[6];

      $promotion=preg_replace('/^\s*order\s*/i','',$promotion);
      $promotion=preg_replace('/discount\s*$/i','',$promotion);
      $promotion=preg_replace('/\s*off\s*$/i','',$promotion);

      $promotion=_trim($promotion);
      $promotion_position=$column;
      // print "*********** Promotion $promotion $promotion_position \n";
    }
    if($cols[3]=='' and $cols[6]==''){
      $blank_position=$column;
    }

    if(preg_match('/doned/i',$cols[0])){
      $department_name=_trim($cols[6]);
      $department_code=_trim($cols[3]);
      // $department_name=_trim( mb_convert_encoding($cols[6], "UTF-8", "ISO-8859-1"));
      // $department_code=_trim( mb_convert_encoding($cols[3], "UTF-8", "ISO-8859-1"));
      $department_position=$column;
    }
    
 
  }
  

  
  $column++;
}






?>