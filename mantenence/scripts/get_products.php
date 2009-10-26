<?php

include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Campaign.php');
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
date_default_timezone_set('Europe/London');
include_once('../../set_locales.php');
require('../../locale.php');
$_SESSION['locale_info'] = localeconv();

$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if(!$con){print "Error can not connect with database server\n";exit;}
//$dns_db='dw';
$db=@mysql_select_db($dns_db, $con);
if (!$db){print "Error can not access the database\n";exit;}


require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';           
date_default_timezone_set('Europe/London');

//create_shipping();exit;

$_department_code='';



$software='Get_Products.php';
$version='V 1.0';

$Data_Audit_ETL_Software="$software $version";

$file_name='/data/plaza/AWorder2002.xls';
$csv_file='tmp.csv';
//exec('/usr/local/bin/xls2csv    -s cp1252   -d 8859-1   '.$file_name.' > '.$csv_file);

$handle_csv = fopen($csv_file, "r");
$column=0;
$products=false;
$count=0;

$store_key=1;
;

$nodes=new Nodes('`Category Dimension`');
$data=array('`Category Name`'=>'Use');
$nodes->add_new(0 , $data);



$data=array('`Category Name`'=>'Material');
$nodes->add_new(0 , $data);
$data=array('`Category Name`'=>'Theme');
$nodes->add_new(0 , $data);

$data=array('`Category Name`'=>'Other','`Category Default`'=>'Yes');
$nodes->add_new(1 , $data);
$data=array('`Category Name`'=>'Candles');
$nodes->add_new(1 , $data);
$data=array('`Category Name`'=>'Soap');
$nodes->add_new(1 , $data);
$data=array('`Category Name`'=>'Incense');
$nodes->add_new(1 , $data);
$data=array('`Category Name`'=>'Aromatheraphy');
$nodes->add_new(1 , $data);
$data=array('`Category Name`'=>'Bathroom Product');
$nodes->add_new(1 , $data);
$data=array('`Category Name`'=>'Decoration');
$nodes->add_new(1 , $data);

$data=array('`Category Name`'=>'Other','`Category Default`'=>'Yes');
$nodes->add_new(2 , $data);
$data=array('`Category Name`'=>'Wood');
$nodes->add_new(2 , $data);
$data=array('`Category Name`'=>'Metal');
$nodes->add_new(2 , $data);
$data=array('`Category Name`'=>'Glass');
$nodes->add_new(2 , $data);
$data=array('`Category Name`'=>'Resin');
$nodes->add_new(2 , $data);
$data=array('`Category Name`'=>'Ceramic');
$nodes->add_new(2 , $data);
$data=array('`Category Name`'=>'Mineral');
$nodes->add_new(2 , $data);

$data=array('`Category Name`'=>'None','`Category Default`'=>'Yes');
$nodes->add_new(3 , $data);
$data=array('`Category Name`'=>'Christmas');
$nodes->add_new(3 , $data);
$data=array('`Category Name`'=>'Halloween');
$nodes->add_new(3 , $data);
$data=array('`Category Name`'=>'Love');
$nodes->add_new(3 , $data);
$data=array('`Category Name`'=>'Animals');
$nodes->add_new(3 , $data);
$data=array('`Category Name`'=>'Esoteric');
$nodes->add_new(3 , $data);
$data=array('`Category Name`'=>'Fantasy');
$nodes->add_new(3 , $data);




$store_data=array('Store Code'=>'UK',
		  'Store Name'=>'Ancient Wisdom',
		  'Store Locale'=>'en_GB',
		  'Store Home Country Code 2 Alpha'=>'GB',
		  'Store Currency Code'=>'GBP',
		  'Store Home Country Name'=>'United Kingdom', 
		  'Store Home Country Short Name'=>'UK', 
		  );
$store=new Store('find',$store_data,'create');

$store_data=array('Store Code'=>'DE',
		  'Store Name'=>'AW-Geshenke',
		  'Store Locale'=>'de_DE',
		  'Store Home Country Code 2 Alpha'=>'DE',
		  'Store Currency Code'=>'EUR',
		  'Store Home Country Name'=>'Germany', 
		  'Store Home Country Short Name'=>'DE', 
		  );
$store=new Store('find',$store_data,'create');
$store_data=array('Store Code'=>'FR',
		  'Store Name'=>'AW-Cadeaux',
		  'Store Locale'=>'fr_FR',
		  'Store Home Country Code 2 Alpha'=>'FR',
		  'Store Currency Code'=>'EUR',
		  'Store Home Country Name'=>'France', 
		  'Store Home Country Short Name'=>'FR', 
		  );
$store=new Store('find',$store_data,'create');
$warehouse=new Warehouse('find',array('Warehouse Code'=>'W','Warehouse Name'=>'Parkwood'),'create');

$unk_location=new Location('find',array('Location Code'=>'UNK','Location Name'=>'Unknown'),'create');

$unk_supplier=new Supplier('find',array('Supplier Code'=>'UNK','Supplier Name'=>'Unknown'),'create');

$charge_data=array(
		     'Charge Description'=>'£7.50 small order'
		      ,'Store Key'=>$store_key
		     ,'Charge Trigger'=>'Order'
		     ,'Charge Type'=>'Amount'
		     ,'Charge Name'=>'Small Order Charge'
		     ,'Charge Terms Type'=>'Order Items Gross Amount'
		     ,'Charge Terms Description'=>'when Order Items Gross Amount is less than £50.00'
		     ,'Charge Begin Date'=>''
		     ,'Charge Expiration Date'=>''
		     );
$small_order_charge=new Charge('find create',$charge_data);



$dept_data=array(
		   'Product Department Code'=>'ND',
		   'Product Department Name'=>'Products Without Department',
		   'Product Department Store Key'=>$store_key
		   );

$dept_no_dept=new Department('find',$dept_data,'create');
$dept_no_dept_key=$dept_no_dept->id;

$dept_data=array(
		   'Product Department Code'=>'Promo',
		   'Product Department Name'=>'Promotional Items',
		   'Product Department Store Key'=>$store_key
		   );
$dept_promo=new Department('find',$dept_data,'create');

$dept_promo_key=$dept_promo->id;

$fam_data=array(
		   'Product Family Code'=>'PND_GB',
		   'Product Family Name'=>'Products Without Family',
		   'Product Family Main Department Key'=>$dept_no_dept_key,
		   'Product Family Store Key'=>$store_key,
		   'Product Family Special Characteristic'=>'None'
		   );

$fam_no_fam=new Family('find',$fam_data,'create');
$fam_no_fam_key=$fam_no_fam->id;

//print_r($fam_no_fam);

$fam_data=array(
		   'Product Family Code'=>'Promo_GB',
		   'Product Family Name'=>'Promotional Items',
		   'Product Family Main Department Key'=>$dept_promo_key,
		   'Product Family Store Key'=>$store_key,
		   'Product Family Special Characteristic'=>'None'
		   );



$fam_promo=new Family('find',$fam_data,'create');



$fam_no_fam_key=$fam_no_fam->id;
$fam_promo_key=$fam_promo->id;


 $campaign=array(
		     'Campaign Name'=>'Gold Reward'
		     ,'Campaign Description'=>'Small order charge waive & discounts on seleted items if last order within 1 calendar month'
		     ,'Campaign Begin Date'=>''
		     ,'Campaign Expiration Date'=>''
		     ,'Campaign Deal Terms Type'=>'Order Interval'
		     ,'Campaign Deal Terms Description'=>'last order within 1 month'
		     ,'Campaign Deal Terms Lock'=>'Yes'

		     );
$gold_camp=new Campaign('find create',$campaign);


$data=array(
	    'Deal Name'=>'[Product Family Code] Gold Reward'
	    ,'Deal Trigger'=>'Family'
	    ,'Deal Allowance Type'=>'Percentage Off'
	    ,'Deal Allowance Description'=>'[Percentage Off] off'
	    ,'Deal Allowance Target'=>'Family'
	    ,'Deal Allowance Lock'=>'No'
		     );
$gold_camp->add_deal_schema($data);

$data=array(
	    'Deal Name'=>'Free [Charge Name]'
	    ,'Deal Trigger'=>'Order'
	    ,'Deal Allowance Type'=>'Percentage Off'
	    ,'Deal Allowance Description'=>'Free [Charge Name]'
	    ,'Deal Allowance Target'=>'Charge'
	    ,'Deal Allowance Key'=>$small_order_charge->id
        ,'Deal Allowance Lock'=>'Yes'

		   
		     );
$gold_camp->add_deal_schema($data);

$data=array('Deal Allowance Target Key'=>$small_order_charge->id);
$gold_camp->create_deal('Free [Charge Name]',$data);

$gold_reward_cam_id=$gold_camp->id;

$campaign=array(
		     'Campaign Name'=>'Volumen Discount'
		     ,'Campaign Trigger'=>'Family'
		     ,'Campaign Description'=>'Percentage off when order more than some quantity of products in the same family'
		     ,'Campaign Begin Date'=>''
		     ,'Campaign Expiration Date'=>''
		      ,'Campaign Deal Terms Type'=>'Family Quantity Ordered'
		     ,'Campaign Deal Terms Description'=>'order [Quantity] or more same family'
		     ,'Campaign Deal Terms Lock'=>'No'
		     );
$vol_camp=new Campaign('find create',$campaign);


$data=array(
		     'Deal Name'=>'[Product Family Code] Volume Discount'
		     ,'Deal Trigger'=>'Family'
		     ,'Deal Allowance Type'=>'Percentage Off'
		     ,'Deal Allowance Description'=>'[Percentage Off] off'
		     ,'Deal Allowance Target'=>'Family'
		   	 ,'Deal Allowance Lock'=>'No'

		     );
$vol_camp->add_deal_schema($data);

$volume_cam_id=$vol_camp->id;


$free_shipping_campaign_data=array(
		     'Campaign Name'=>'Free Shipping'
		     
		     ,'Campaign Description'=>'Free shipping to selected destinations when order more than some amount'
		     ,'Campaign Begin Date'=>''
		     ,'Campaign Expiration Date'=>''
		     ,'Campaign Deal Terms Type'=>'Order Items Net Amount AND Shipping Country'
		     ,'Campaign Deal Terms Description'=>'Orders shipped to {Country Name} and Order Items Net Amount more than {Order Items Net Amount}'
		     ,'Campaign Deal Terms Lock'=>'No'
		     );
$free_shipping_campaign=new Campaign('find create',$free_shipping_campaign_data);


$data=array(
		     'Deal Name'=>'[Country Name] Free Shipping'
		     ,'Deal Trigger'=>'Order'
		     ,'Deal Allowance Type'=>'Percentage Off'
		     ,'Deal Allowance Description'=>'Free Shipping'
		     ,'Deal Allowance Target'=>'Shipping'
		     ,'Deal Allowance Lock'=>'Yes'

		     );
$free_shipping_campaign->add_deal_schema($data);

$free_shipping_campaign_id=$free_shipping_campaign->id;

$shipping_uk=new Shipping('find',array('Country Code'=>'GBR'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','GBR','£175');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);



$campaign=array(
		     'Campaign Name'=>'BOGOF'
		     ,'Campaign Description'=>'Buy one Get one Free'
		     ,'Campaign Begin Date'=>''
		     ,'Campaign Expiration Date'=>''
		       ,'Campaign Deal Terms Type'=>'Product Quantity Ordered'
		     ,'Campaign Deal Terms Description'=>'Buy 1'
		     ,'Campaign Deal Terms Lock'=>'Yes'
		     );
$bogof_camp=new Campaign('find create',$campaign);
$data=array(
		     'Deal Name'=>'[Product Family Code] BOGOF'
		     ,'Deal Trigger'=>'Family'
		     ,'Deal Allowance Type'=>'Get Free'
		     ,'Deal Allowance Description'=>'get 1 free'
		     ,'Deal Allowance Target'=>'Product'
		    ,'Deal Allowance Lock'=>'Yes'
		     );
$bogof_camp->add_deal_schema($data);

$data=array(
	    'Deal Name'=>'[Product Code] BOGOF'
		     ,'Deal Trigger'=>'Product'
		     ,'Deal Allowance Type'=>'Get Same Free'
		     ,'Deal Allowance Description'=>'get 1 free'
		     ,'Deal Allowance Target'=>'Product'
		     ,'Deal Allowance Lock'=>'Yes'

		     );
$bogof_camp->add_deal_schema($data);


$bogof_cam_id=$bogof_camp->id;
$campaign=array(
		     'Campaign Name'=>'First Order Bonus'
		     ,'Campaign Trigger'=>'Order'
		     ,'Campaign Description'=>'When you order over £100+vat for the first time we give you over a £100 of stock. (at retail value).'
		     ,'Campaign Begin Date'=>''
		     ,'Campaign Expiration Date'=>''
		     ,'Campaign Deal Terms Type'=>'Order Total Net Amount AND Order Number'
		     ,'Campaign Deal Terms Description'=>'order over £100+tax on the first order '
		     ,'Campaign Deal Terms Lock'=>'Yes'
		     );
$camp=new Campaign('find create',$campaign);


$data=array(
	    'Deal Name'=>'First Order Bonus [Counter]'
	    ,'Deal Trigger'=>'Order'
            ,'Deal Description'=>'When you order over £100+vat for the first time we give you over a £100 of stock. (at retail value).'
	    ,'Deal Allowance Type'=>'Get Free'
	    ,'Deal Allowance Description'=>'Free Bonus Stock ([Product Code])'
	    ,'Deal Allowance Target'=>'Product'
	    ,'Deal Allowance Lock'=>'No'
	    
	    );
$camp->add_deal_schema($data);

/* $deal_data=array( */
/* 		     'Deal Name'=>'First Order Bonus' */
/* 		     ,'Deal Trigger'=>'Order' */
/* 		     ,'Deal Description'=>'When you order over £100+vat for the first time we give you over a £100 of stock. (at retail value).' */
/* 		     ,'Deal Terms Type'=>'Order Total Net Amount AND Order Number' */
/* 		     ,'Deal Terms Description'=>'First time order over £100+vat'; */
/* 		     ,'Deal Allowance Description'=>'Bonus Stock - worth £100 at retail value' */
/* 		     ,'Deal Allowance Type'=>'Get Free' */
/* 		     ,'Deal Allowance Target'=>'Product' */
/* 		     ,'Deal Allowance Target Key'=>'' */
/* 		     ,'Deal Begin Date'=>'' */
/* 		     ,'Deal Expiration Date'=>'' */
/* 		     ); */
/* $deal=new Deal('find create',$deal_data); */


 
$__cols=array();
$inicio=false;
while(($_cols = fgetcsv($handle_csv))!== false){
  

  $code=$_cols[3];

 
  if($code=='FO-A1' and !$inicio){
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

  }elseif($code=='Credit'){
    break;
  }
  $__cols[]=$_cols;
}




$fam_name='Products Without Family';
$fam_code='PND_GB';


$new_family=true;


$department_name='ND';
$department_code='Products Without Department';

$current_fam_name='';
$current_fam_code='';
$fam_position=-10000;
$promotion_position=100000;
$promotion='';


foreach($__cols as $cols){
  

  $is_product=true;
  
  $code=_trim($cols[3]);


  $price=$cols[7];
  $supplier_code=_trim($cols[21]);
  $part_code=_trim($cols[22]);
  $supplier_cost=$cols[25];
  


  //    if(!preg_match('/bot-10/i',$code)){
  //  continue;
  //   }
  
  $code=_trim($code);
  if($code=='' or !preg_match('/\-/',$code) or preg_match('/total/i',$price)  or  preg_match('/^(pi\-|cxd\-|fw\-04)/i',$code))
    $is_product=false;
  if(preg_match('/^(ob\-108|ish\-94|rds\-47)/i',$code))
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
    
       print "$code\n";

    
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
    if(preg_match('/off\s+\d+\s+or\s+more/i',_trim($current_promotion))){
      if(preg_match('/^\d+\% off/i',$current_promotion,$match))
	$allowance=$match[0];
      if(preg_match('/off.*more/i',$current_promotion,$match))
	$terms=preg_replace('/^off\s*/i','',$match[0]);
     
	//	print "************".$current_promotion."\n";
      $deals[]=array(
		     'Deal Name'=>'Gold Reward'
		     ,'Deal Trigger'=>'Order'

		     ,'Deal Description'=>$allowance.' if last order within 1 calendar month'
		     ,'Deal Terms Type'=>'Order Interval'
		     ,'Deal Terms Description'=>'last order within 1 calendar month'
		     ,'Deal Allowance Description'=>$allowance
		     ,'Deal Allowance Type'=>'Percentage Off'
		     ,'Deal Allowance Target'=>'Family'
		     ,'Deal Allowance Target Key'=>''
		     ,'Deal Begin Date'=>''
		     ,'Deal Expiration Date'=>''
		     );
      
      $deals[]=array(
		     'Deal Name'=>'Family Volume Discount'
		     ,'Deal Trigger'=>'Family'
		     ,'Deal Description'=>$allowance.' if order '.$terms.' same family'
		     ,'Deal Terms Type'=>'Family Quantity Ordered'
		     ,'Deal Terms Description'=>'order '.$terms
		     ,'Deal Allowance Description'=>$allowance
		     ,'Deal Allowance Type'=>'Percentage Off'
		     ,'Deal Allowance Target'=>'Family'
		     ,'Deal Allowance Target Key'=>''
		     ,'Deal Begin Date'=>''
		     ,'Deal Expiration Date'=>''
		     );	

      
      
    }elseif(preg_match('/\d+\s*or more\s*\d+\%$/i',_trim($current_promotion))){
      // print $current_promotion." *********\n";
      preg_match('/\d+\%$/i',$current_promotion,$match);
      $allowance=$match[0].' off';
      preg_match('/\d+\s*or more/i',$current_promotion,$match);
      $terms=_trim(strtolower($match[0]));

      $deals[]=array(
		     'Deal Name'=>'Gold Reward'
		     ,'Deal Trigger'=>'Order'
		     ,'Deal Description'=>$allowance.' if last order within 1 calendar month'
		     ,'Deal Terms Type'=>'Order Interval'
		     ,'Deal Terms Description'=>'last order within 1 calendar month'
		     ,'Deal Allowance Description'=>$allowance
		     ,'Deal Allowance Type'=>'Percentage Off'
		     ,'Deal Allowance Target'=>'Family'
		     ,'Deal Allowance Target Key'=>''
		        ,'Deal Begin Date'=>''
		       ,'Deal Expiration Date'=>''
		       );

	$deals[]=array(
		       'Deal Name'=>'Family Volume Discount'
		       ,'Deal Trigger'=>'Family'
		       ,'Deal Description'=>$allowance.' if '.$terms.' same family'
		       ,'Deal Terms Type'=>'Family Quantity Ordered'
		       ,'Deal Terms Description'=>'order '.$terms
		       ,'Deal Allowance Description'=>$allowance
		       ,'Deal Allowance Type'=>'Percentage Off'
		       ,'Deal Allowance Target'=>'Family'
		       ,'Deal Allowance Target Key'=>''
		       ,'Deal Begin Date'=>''
		       ,'Deal Expiration Date'=>''
		       
		       );	
	

    }elseif(preg_match('/^buy \d+ get \d+ free$/i',_trim($current_promotion))){
      // print $current_promotion." *********\n";
      preg_match('/buy \d+/i',$current_promotion,$match);
      $buy=_trim(preg_replace('/[^\d]/','',$match[0]));

      preg_match('/get \d+/i',$current_promotion,$match);
      $get=_trim(preg_replace('/[^\d]/','',$match[0]));

      $deals[]=array(
		       'Deal Name'=>'BOGOF'
		       ,'Deal Trigger'=>'Product'
		       ,'Deal Description'=>'buy '.$buy.' get '.$get.' free'
		       ,'Deal Terms Type'=>'Product Quantity Ordered'
		       ,'Deal Terms Description'=>'foreach '.$buy
		       ,'Deal Allowance Description'=>$get.' free'
		       ,'Deal Allowance Type'=>'Get Free'
		       ,'Deal Allowance Target'=>'Family'
		       ,'Deal Allowance Target Key'=>''
		       ,'Deal Begin Date'=>''
		       ,'Deal Expiration Date'=>''
		     );	


    }else
       $deals=array();
    
    $units=$cols[5];
    if($units=='' OR $units<=0)
      $units=1;

    $description=_trim( mb_convert_encoding($cols[6], "UTF-8", "ISO-8859-1,UTF-8"));
    

 //    if(preg_match('/wsl-535/i',$code)){
//       print_r($cols);
//       exit;

//     }

    $rrp=$cols[16];
    $supplier_code=_trim($cols[21]);

    $w=$cols[28];



    if($code=='EO-ST' or $code=='MOL-ST' or  $code=='JBB-st' or $code=='LWHEAT-ST' or  $code=='JBB-St' 
       or $code=='Scrub-St' or $code=='Eye-st' or $code=='Tbm-ST' or $code=='Tbc-ST' or $code=='Tbs-ST'
       or $code=='GemD-ST' or $code=='CryC-ST' or $code=='GP-ST'  or $code=='DC-ST'
       ){
      print "Skipping $code\n";
      
    }else{

      
      if(!is_numeric($price) or $price<=0){
	print "Price Zero  $code \n";
	$price=0;
      }


      if($code=='Tib-20')
	$supplier_cost=0.2;

      if($code=='L&P-ST'){
	$supplier_cost=36.30;
	$price=86.40;
      }

    if(!is_numeric($supplier_cost)  or $supplier_cost<=0 ){
      //   print_r($cols);
      print "$code   assumind supplier cost of 40%  \n";
      $supplier_cost=0.4*$price/$units;
      
    }



  
      if($units=='')
	$units=1;
      
      if(is_numeric($rrp))
	$rrp=sprintf("%.2f",$rrp*$units);
      else
	$rrp='';
      
      
    //   $_f=preg_replace('/s$/i','',$current_fam_name);
//       //print "$_f\n";
//       $special_char=preg_replace('/'.str_replace('/','\/',$_f).'$/i','',$description);
//       $special_char=preg_replace('/'.str_replace('/','\/',$current_fam_name).'$/i','',$special_char);
      $fam_special_char=$current_fam_name;
      $special_char=$description;

      if(is_numeric($w)){
	$w=$w*$units;
	if($w<0.001 and $w>0)
	  $_w=0.001;
	else
	  $_w=sprintf("%.3f",$w);
      }else
	$_w='';
      

      if($current_fam_code=='LavF / PF')
	$current_fam_code='PF';
      if($current_fam_code=='MIST / AM')
	$current_fam_code='MIST';
       if($current_fam_code=='LBI / IS')
	$current_fam_code='LBI';

       if($current_fam_code=='Leb - Lebp')
	 $current_fam_code='Leb';
       if($current_fam_code=='Bot/Pack/Wb')
	 $current_fam_code='Bot';
       


      
    
      
       if(preg_match('/^pi-|catalogue|^info|Mug-26x|OB-39x|SG-xMIXx|wsl-1275x|wsl-1474x|wsl-1474x|wsl-1479x|^FW-|^MFH-XX$|wsl-1513x|wsl-1487x|wsl-1636x|wsl-1637x/i',_trim($code))){

	
	 $family=new Family($fam_promo_key);
	 
       }else{


       $dep_data=array(
		       'Product Department Code'=>$department_code,
		       'Product Department Name'=>$department_name,
		       'Product Department Store Key'=>$store_key
		       );
       $department=new Department('find',$dep_data,'create');	

    
	 
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
    exit;
   
   }

   
   
   foreach($deals as $deal_data){
     //         print_r($deal_data);
     //exit;

      $deal_data['Store Key']=$store_key;

      if(preg_match('/Family Volume/i',$deal_data['Deal Name'])){
	//$deal_data['Deal Campaign Key']=$volume_cam_id;
	//$deal_data['Deal Name']=preg_replace('/Family/',$family->data['Product Family Code'],$deal_data['Deal Name']);
	//$deal_data['Deal Description']=preg_replace('/same family/',$family->data['Product Family Name'].' outers',$deal_data['Deal Description']);
   
	$data=array(
		    'Deal Allowance Target Key'=>$family->id,
		    'Deal Trigger Key'=>$family->id,

		    'Deal Allowance Description'=>$deal_data['Deal Allowance Description'],
		    'Deal Terms Description'=>$deal_data['Deal Terms Description']
		    
		    );

	$vol_camp->create_deal('[Product Family Code] Volume Discount',$data);


      }


      if(preg_match('/Gold/i',$deal_data['Deal Name'])){
	//$deal_data['Deal Campaign Key']=$gold_reward_cam_id;
	//$deal_data['Deal Name']=$family->data['Product Family Code'].' '.$deal_data['Deal Name'];

	$data=array(
		    'Deal Trigger Key'=>$family->id,
		    'Deal Allowance Target Key'=>$family->id,
		    'Deal Allowance Description'=>$deal_data['Deal Allowance Description']
		    );

	$gold_camp->create_deal('[Product Family Code] Gold Reward',$data);

      }

      if(preg_match('/bogof/i',$deal_data['Deal Name'])){
		$data=array(
			    'Deal Trigger Key'=>$family->id,
			    'Deal Allowance Target Key'=>$family->id,
			    'Deal Allowance Description'=>$deal_data['Deal Allowance Description']
		    );

	$bogof_camp->create_deal('[Product Family Code] BOGOF',$data);


      }
	 
	

    }  







       $data=array(
		  'product sales state'=>'For sale',
		  'product type'=>'Normal',
		  'product record type'=>'Normal',
		  'product web state'=>'Online Auto',
		  'product store key'=>$store_key,
		  'product currency'=>'GBP',
		  'product locale'=>'en_GB',
		  'product code'=>$code,
		  'product price'=>sprintf("%.2f",$price),
		  'product rrp'=>$rrp,
		  'product units per case'=>$units,
		  'product name'=>$description,
		  'product family key'=>$family->id,
		  //'product main department key'=>$department->id,
		  'product special characteristic'=>$special_char,
		  'product net weight'=>$_w,
		  'product gross weight'=>$_w,
		  'date1'=>date('Y-m-d H:i:s'),
		  'date2'=>date('Y-m-d H:i:s'),
		  'deals'=>$deals
		    );




       $product=new Product('find',$data,'create');
       //  print_r($product);

       if($product->new){
	 $product->update_for_sale_since(date("Y-m-d H:i:s",strtotime("now +1 seconds")));

	$scode=_trim($cols[20]);
	$supplier_code=$cols[21];
       
	if(preg_match('/^SG\-|^info\-/i',$code))
	  $supplier_code='AW';
	if($supplier_code=='AW')
	  $scode=$code;



	if($scode=='SSK-452A' and $supplier_code=='Smen')
	  $scode='SSK-452A bis';


	if(preg_match('/^(StoneM|Smen)$/i',$supplier_code)){
	  $supplier_code='StoneM';
	}


		$the_supplier_data=array(
		      'name'=>$supplier_code,
		      'code'=>$supplier_code,
		      );

	// Suppplier data
	if(preg_match('/Ackerman|Ackerrman|Akerman/i',$supplier_code)){
	  $supplier_code='Ackerman';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Ackerman Group',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Unit 15/16'
							 ,'address2'=>'Hickman Avenue'
							 ,'address3'=>''
							 ,'town'=>'London'
							 ,'town_d1'=>''
							 ,'town_d2'=>'Chingford'
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'E4 9JG'
							 ),
				   'email'=>'office@ackerman.co.uk'
				   ,'telephone'=>'020 8527 6439'
				   );
	}
if(preg_match('/^puck$/i',$supplier_code)){
	  $supplier_code='Puck';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Puckator',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Lowman Works'
							 ,'address2'=>''
							 ,'address3'=>''
							 ,'town'=>'East Taphouse'
							 ,'town_d1'=>''
							 ,'town_d2'=>'Near Liskeard'
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'PL14 4NQ'
							 ),
				   'email'=>'accounts@puckator.co.uk'
				   ,'telephone'=>'1579321550'
				   ,'fax'=>'1579321520'
				   );
	}
 
 if(preg_match('/^decent gem$/i',$supplier_code)){
   $supplier_code='DecGem';
   $the_supplier_data=array(
			    'Supplier Name'=>'Decent Gemstone Exports',
			    'Supplier Code'=>$supplier_code,
			    'address_data'=>array(
						  'type'=>'3line'
						  ,'address1'=>"Besides Balaji's Mandir"
						  ,'address2'=>'Near Rajputwad'
						  ,'address3'=>''
						  ,'town'=>'Khambhat'
						  ,'town_d1'=>''
						  ,'town_d2'=>''
						  ,'country'=>'India'
						  ,'country_d1'=>''
						  ,'country_d2'=>''
						  ,'default_country_id'=>$myconf['country_id']
						  ,'postcode'=>'388620'
						  ),
			    'email'=>'decentstone@sancharnet.in'
			    ,'telephone'=>'00917926578604'
			    ,'fax'=>'00917926584997'
			    );
 }
  if(preg_match('/^kiran$/i',$supplier_code)){

   $the_supplier_data=array(
			    'Supplier Name'=>'Kiran Agencies',
			    'Supplier Code'=>$supplier_code,
			    'address_data'=>array(
						  'type'=>'3line'
						  ,'address1'=>"4D Garstin Place"
						  ,'address2'=>''
						  ,'address3'=>''
						  ,'town'=>'Kolkata'
						  ,'town_d1'=>''
						  ,'town_d2'=>''
						  ,'country'=>'India'
						  ,'country_d1'=>''
						  ,'country_d2'=>''
						  ,'default_country_id'=>$myconf['country_id']
						  ,'postcode'=>'700001'
						  )
			    ,'telephone'=>'919830020595'

			    );
 }
 

if(preg_match('/^watkins$/i',$supplier_code)){

   $the_supplier_data=array(
			    'Supplier Name'=>'Watkins Soap Co Ltd',
			    'Supplier Code'=>$supplier_code,
			    'address_data'=>array(
						  'type'=>'3line'
						  ,'address1'=>"Reed Willos Trading Est"
						  ,'address2'=>'Finborough Rd'
						  ,'address3'=>''
						  ,'town'=>'Stowmarket'
						  ,'town_d1'=>''
						  ,'town_d2'=>''
						  ,'country'=>'UK'
						  ,'country_d1'=>''
						  ,'country_d2'=>''
						  ,'default_country_id'=>$myconf['country_id']
						  ,'postcode'=>'IP14 3BU'
						  )

			    ,'telephone'=>'01142501012'
			    ,'fax'=>'01142501006'
			    );
 }



if(preg_match('/^decree$/i',$supplier_code)){

   $the_supplier_data=array(
			    'Supplier Name'=>'Decree Thermo Limited',
			    'Supplier Code'=>$supplier_code,
			    'address_data'=>array(
						  'type'=>'3line'
						  ,'address1'=>"300 Shalemoor"
						  ,'address2'=>'Finborough Rd'
						  ,'address3'=>''
						  ,'town'=>'Sheffield'
						  ,'town_d1'=>''
						  ,'town_d2'=>''
						  ,'country'=>'UK'
						  ,'country_d1'=>''
						  ,'country_d2'=>''
						  ,'default_country_id'=>$myconf['country_id']
						  ,'postcode'=>'S3 8AL'
						  )
			    ,'contact_name'=>'Zoie'
			    ,'email'=>'Watkins@soapfactory.fsnet.co.uk'
			    ,'telephone'=>'01449614445'
			    ,'fax'=>'014497111643'
			    );
 }

if(preg_match('/^cbs$/i',$supplier_code)){

   $the_supplier_data=array(
			    'Supplier Name'=>'Carrierbagshop',
			    'Supplier Code'=>$supplier_code,
			    'address_data'=>array(
						  'type'=>'3line'
						  ,'address1'=>"Unit C18/21"
						  ,'address2'=>'Hastingwood trading Estate'
						  ,'address3'=>'35 Harbet Road'
						  ,'town'=>'London'
						  ,'town_d1'=>''
						  ,'town_d2'=>''
						  ,'country'=>'UK'
						  ,'country_d1'=>''
						  ,'country_d2'=>''
						  ,'default_country_id'=>$myconf['country_id']
						  ,'postcode'=>'N18 3HU'
						  )
			    ,'contact_name'=>'Neil'
			    ,'email'=>'info@carrierbagshop.co.uk'
			    ,'telephone'=>'08712300980'
			    ,'fax'=>'08712300981'
			    );
 }


if(preg_match('/^giftw$/i',$supplier_code)){

   $the_supplier_data=array(
			    'Supplier Name'=>'Giftworks Ltd',
			    'Supplier Code'=>$supplier_code,
			    'address_data'=>array(
						  'type'=>'3line'
						  ,'address1'=>"Unit 14"
						  ,'address2'=>'Cheddar Bussiness Park'
						  ,'address3'=>'Wedmore Road'
						  ,'town'=>'Cheddar'
						  ,'town_d1'=>''
						  ,'town_d2'=>''
						  ,'country'=>'UK'
						  ,'country_d1'=>''
						  ,'country_d2'=>''
						  ,'default_country_id'=>$myconf['country_id']
						  ,'postcode'=>'BS27 3EB'
						  )
			    ,'email'=>'info@giftworks.tv'
			    ,'telephone'=>'441934742777'
			    ,'fax'=>'441934740033'
			    ,'www.giftworks.tv'
			    );
 }


 if(preg_match('/^Sheikh$/i',$supplier_code)){
	  $supplier_code='Sheikh';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Sheikh Enterprises',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>"Eidgah Road"
							 ,'address2'=>'Opp. Islamia Inter College'
							 ,'address3'=>''
							 ,'town'=>'Saharanpur'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'India'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'247001'
							 )

				   );
	}
if(preg_match('/^Gopal$/i',$supplier_code)){
	  $supplier_code='Gopal';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Gopal Corporation Limited',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>"240 Okhla Industrial Estate"
							 ,'address2'=>'Phase III'
							 ,'address3'=>''
							 ,'town'=>'New Delhi'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'India'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'110020'
							 )
				   ,'telephone'=>'00911126320185'
				   );
	}

  if(preg_match('/^CraftS$/i',$supplier_code)){
	  $supplier_code='CraftS';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Craftstones Europe Ltd',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>"52/54 Homethorphe Avenue"
							 ,'address2'=>'Homethorphe Ind. Estate'
							 ,'address3'=>''
							 ,'town'=>'Redhill'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'RH1 2NL'
							 ),
				   'contact_name'=>'Jose'

				   ,'telephone'=>'01737767363'
				   ,'fax'=>'01737768627'
				   );
	}

 if(preg_match('/^Simpson$/i',$supplier_code)){
	  $supplier_code='CraftS';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Simpson Packaging',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>"Unit 1"
							 ,'address2'=>'Shaw Cross Business Park'
							 ,'address3'=>''
							 ,'town'=>'Dewsbury'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'WF12 7RF'
							 ),

				   'email'=>'sales@simpson-packaging.co.uk'
				   ,'telephone'=>'01924869010'
				   ,'fax'=>'01924439252'
				   ,'www'=>'wwww.simpson-packaging.co.uk'
				   );
	}



 if(preg_match('/^amanis$/i',$supplier_code)){
	  $supplier_code='AmAnis';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Amanis',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>"Unit 6"
							 ,'address2'=>'Bowlimng Court Industrial Estate'
							 ,'address3'=>'Mary Street'
							 ,'town'=>'Bradford'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'BD4 8TT'
							 ),

				   'email'=>'saltlamps@aol.com'
				   ,'telephone'=>'4401274394100'
				   ,'fax'=>'4401274743243'
				   ,'www'=>'www.saltlamps-r-us.com'
				   );
	}


if(preg_match('/^amanis$/i',$supplier_code)){
	  $supplier_code='AmAnis';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Amanis',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>"Unit 6"
							 ,'address2'=>'Bowlimng Court Industrial Estate'
							 ,'address3'=>'Mary Street'
							 ,'town'=>'Bradford'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'BD4 8TT'
							 ),

				   'email'=>'saltlamps@aol.com'
				   ,'telephone'=>'4401274394100'
				   ,'fax'=>'4401274743243'
				   ,'www'=>'www.saltlamps-r-us.com'
				   );
	}


if(preg_match('/^Wenzels$/i',$supplier_code)){
	  $supplier_code='Wenzels';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Richard Wenzel GMBH & CO KG',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>"Benzstraße 5"
							 ,'address2'=>''
							 ,'address3'=>''
							 ,'town'=>'Aschaffenburg'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'Germany'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'63741'
							 ),


				   'telephone'=>'49602134690'
				   ,'fax'=>'496021346940'

				   );
	}
	

	if(preg_match('/^AW$/i',$supplier_code)){
	  $supplier_code='AW';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Ancient Wisdom Marketing',
				   'Supplier Code'=>$supplier_code,
				   'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Block B'
							 ,'address2'=>'Parkwood Business Park'
							 ,'address3'=>'Parkwood Road'
							 ,'town'=>'Sheffield'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'S3 8AL'
							 ),
				   'email'=>'mail@ancientwisdom.biz'
				   ,'telephone'=>'44 (0)114 2729165'

				   );
	}


	if(preg_match('/^EB$/i',$supplier_code)){
	  $supplier_code='EB';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Elements Bodycare Ltd'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Unit 2'
							 ,'address2'=>'Carbrook Bussiness Park'
							 ,'address3'=>'Dunlop Street'
							 ,'town'=>'Sheffield'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'S9 2HR'
							 )

				   ,'telephone'=>'011422434000'
				   ,'www'=>'www.elements-bodycare.co.uk'
				   ,'email'=>'info@elements-bodycare.co.uk'

				   );
	}

	if(preg_match('/^Paradise$/i',$supplier_code)){
	  $supplier_code='Paradise';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Paradise Music Ltd'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'PO BOX 998'
							 ,'address2'=>'Carbrook Bussiness Park'
							 ,'address3'=>'Dunlop Street'
							 ,'town'=>'Tring'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'HP23 4ZJ'
							 )

				   ,'telephone'=>'01296668193'


				   );
	}
	if(preg_match('/^MCC$/i',$supplier_code)){
	  $supplier_code='MCC';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Manchester Candle Company'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'The Manchester Group'
							 ,'address2'=>'Kenwood Road'
							 ,'address3'=>''
							 ,'town'=>'North Reddish'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'SK5 6PH'
							 )
				   ,'contact_name'=>'Brian'
				   ,'telephone'=>'01614320811'
				   ,'fax'=>'01614310328'
				   ,'www'=>'manchestercandle.com'

				   );
	}
	if(preg_match('/^Aquavision$/i',$supplier_code)){
	  $supplier_code='Aquavision';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Aquavision Music Ltd'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'PO BOX 2796'
							 ,'address2'=>''
							 ,'address3'=>''
							 ,'town'=>'Iver'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'SL0 9ZR'
							 )

				   ,'telephone'=>'01753653188'
				   ,'fax'=>'01753655059'
				   ,'www'=>'www.aquavisionwholesale.co.uk'
				   ,'email'=>'info@aquavisionwholesale.co.uk'
				   );
	}

	if(preg_match('/^CXD$/i',$supplier_code)){
	  $supplier_code='CXD';
	  $the_supplier_data=array(
				   'Supplier Name'=>'CXD Designs Ltd'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Unit 2'
							 ,'address2'=>'Imperial Park'
							 ,'address3'=>'Towerfiald Road'
							 ,'town'=>'Shoeburyness'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>'Essex'
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'SS3 9QT'
							 )

				   ,'telephone'=>'01702292028'
				   ,'fax'=>'01702298486'

				   );
	}
	if(preg_match('/^(AWR|costa)$/i',$supplier_code)){
	  $supplier_code='AWR';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Costa Imports'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Nave 8'
							 ,'address2'=>'Polígono Ind. Alhaurín de la Torre Fase 1'
							 ,'address3'=>'Paseo de la Hispanidad'
							 ,'town'=>'Málaga'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'Spain'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'29130'
							 )
				   ,'contact_name'=>'Carlos'
				   ,'email'=>'carlos@aw-regalos.com'
				   ,'telephone'=>'(+34) 952 417 609'
				   );
	}

	if(preg_match('/^(salco)$/i',$supplier_code)){
	  $supplier_code='Salco';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Salco Group'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Salco House'
							 ,'address2'=>'5 Central Road'
							 ,'address3'=>''
							 ,'town'=>'Harlow'
							 ,'town_d1'=>''
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>'Essex'
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'CM20 2ST'
							 )
				   //				   ,'contact_name'=>'Carlos'
				   ,'email'=>'alco@salcogroup.com'
				   ,'telephone'=>'01279 439991'
				   );
	}
	if(preg_match('/^(apac)$/i',$supplier_code)){
	  $supplier_code='Salco';
	  $the_supplier_data=array(
				   'Supplier Name'=>'APAC Packaging Ltd'
				   ,'Supplier Code'=>$supplier_code
				   ,'address_data'=>array(
							 'type'=>'3line'
							 ,'address1'=>'Loughborough Road'
							 ,'address2'=>''
							 ,'address3'=>''
							 ,'town'=>'Leicester'
							 ,'town_d1'=>'Rothley'
							 ,'town_d2'=>''
							 ,'country'=>'UK'
							 ,'country_d1'=>''
							 ,'country_d2'=>''
							 ,'default_country_id'=>$myconf['country_id']
							 ,'postcode'=>'LE7 7NL'
							 )
				   //				   ,'contact_name'=>'Carlos'
				   ,'email'=>''
				   ,'telephone'=>'0116 230 2555'
				   ,'www'=>'www.apacpackaging.com'
				   ,'fax'=>'0116 230 3555'
				   );
	}
	if(preg_match('/^(andy.*?)$/i',$supplier_code)){
	  $supplier_code='Andy';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Andy'
				   ,'Supplier Code'=>$supplier_code
				   );
	}


	if($supplier_code=='' or $supplier_code=='0'){
	  $supplier_code='Unknown';
	  $the_supplier_data=array(
				   'Supplier Name'=>'Unknown Supplier'
				   ,'Supplier Code'=>$supplier_code
				   );
	}

	$supplier=new Supplier('code',$supplier_code);
	if(!$supplier->id){
	  //print "neew: $supplier_code\n";
	  //print_r($the_supplier_data);
	  $supplier=new Supplier('new',$the_supplier_data);
	}




	$scode=_trim($scode);
	$scode=preg_replace('/^\"\s*/','',$scode);
	$scode=preg_replace('/\s*\"$/','',$scode);
	


	if(preg_match('/\d+ or more|0.10000007|8.0600048828125|0.050000038|0.150000076|0.8000006103|1.100000610|1.16666666|1.650001220|1.80000122070/i',$scode))
	  $scode='';
	if(preg_match('/^(\?|new|\d|0.25|0.5|0.8|0.8000006103|01 Glass Jewellery Box|1|0.1|0.05|1.5625|10|\d{1,2}\s?\+\s?\d{1,2}\%)$/i',$scode))
	  $scode='';

	

	if($scode=='same')
	  $scode=$code;
	if($scode=='' or $scode=='0')
	  $scode='?'.$code;
	$sp_data=array(
		       'Supplier Key'=>$supplier->id,
		       'Supplier Product Code'=>$scode,
		       'Supplier Product Cost'=>sprintf("%.4f",$supplier_cost),
		       'Supplier Product Name'=>$description,
		       'Supplier Product Description'=>$description,
		       'Supplier Product Valid From'=>date('Y-m-d H:i:s'),
		       'Supplier Product Valid To'=>date('Y-m-d H:i:s')
		       );
	$supplier_product=new SupplierProduct('find',$sp_data,'create');
	
	$part_data=array(
			 'Part Most Recent'=>'Yes',
			 'Part XHTML Currently Supplied By'=>sprintf('<a href="supplier.php?id=%d">%s</a>',$supplier->id,$supplier->get('Supplier Code')),
			 'Part XHTML Currently Used In'=>sprintf('<a href="product.php?id=%d">%s</a>',$product->id,$product->get('Product Code')),
			 'Part XHTML Description'=>preg_replace('/\(.*\)\s*$/i','',$product->get('Product XHTML Short Description')),
			 'part valid from'=>date('Y-m-d H:i:s'),
			 'part valid to'=>date('Y-m-d H:i:s'),
			 'Part Gross Weight'=>$w
			 );
	$part=new Part('new',$part_data);
	//	print_r($part->data);
	
	$rules[]=array('Part Sku'=>$part->data['Part SKU'],
		       'Supplier Product Units Per Part'=>$units
		       ,'supplier product part most recent'=>'Yes'
		       ,'supplier product part valid from'=>date('Y-m-d H:i:s')
		       ,'supplier product part valid to'=>date('Y-m-d H:i:s')
		       ,'factor supplier product'=>1
		       );
	$supplier_product->new_part_list('',$rules);
	
	$part_list[]=array(
			   'Product ID'=>$product->get('Product ID'),
			   'Part SKU'=>$part->get('Part SKU'),
			   'Product Part Id'=>1,
			   'requiered'=>'Yes',
			   'Parts Per Product'=>1,
			   'Product Part Type'=>'Simple Pick'
			   );
	$product->new_part_list('',$part_list);
	$supplier_product->load('used in');
	$product->load('parts');
	$part->load('used in');
	$part->load('supplied by');
	$product->load('cost');

       
    
       }
  //print_r($deals);

    

   
    
    
    }
  }else{

    $new_family=true;
    
    // print "Col $column\n";
    //print_r($cols);
    if($cols[3]!='' and $cols[6]!=''  and $cols[3]!='SHOP-Fit' and $cols[3]!='RDS-47' and $cols[3]!='ISH-94' and $cols[3]!='OB-108' and !preg_match('/^DB-/',$cols[3])  and !preg_match('/^pack-/i',$cols[3])  ){
      $fam_code=$cols[3];
      $fam_name=_trim( mb_convert_encoding($cols[6], "UTF-8", "ISO-8859-1,UTF-8"));
      $fam_position=$column;

      
    }
    
    if(preg_match('/off\s+\d+\s+or\s+more|\s*\d+\s*or more\s*\d+|buy \d+ get \d+ free/i',_trim($cols[6]))){
      

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

    if($cols[6]!='' and preg_match('/Sub Total/i',$cols[11])){
      $department_name=$cols[6];
      $department_position=$column;


        $department_code=_trim($department_name);
      if($department_code=='Ancient Wisdom Home Fragrance'){
	$department_code='Home';
	$department_name='AW Home Fragrance';
      }
      if($department_code=='Ancient Wisdom Aromatherapy Dept.'){
	$department_code='Aroma';
	$department_name='AW Aromatherapy Department';
      }if($department_code=='Bathroom Heaven')
	 $department_code='Bath';
      if($department_code=='Exotic Incense Dept Order'){
	$department_code='Incense';
	$department_name='Exotic Incense Department';
      }if($department_code=='While Stocks Last Order'){
	$department_code='WSL';
	$department_name='While Stocks Last';
      }if($department_code=='Collectables Department'){
	$department_code='Collec';
      }
      if($department_code=='Crystal Department'){
	$department_code='Crystal';
      }
   if($department_code=='Cards, Posters & Gift Wrap'){
	$department_code='Paper';
      }
   if($department_code=='Retail Display Stands'){
	$department_code='RDS';
      }
if($department_code=='Candle Dept'){
	$department_code='Candles';
      }
   if($department_code=='Stoneware'){
	$department_code='Stone';
	$department_name='Stoneware Department';

      }
   if($department_code=='Jewellery Quarter'){
	$department_code='Jewells';
      }
   if($department_code=='Relaxing Music Collection'){
	$department_code='Music';
      }
 if($department_code=='BagsBags.Biz'){
	$department_code='Bags';
      }
 if($department_code=='Christmas Time'){
	$department_code='Xmas';
      }

if($department_code=='CraftsCrafts.biz'){
	$department_code='Crafts';
      }
if($department_code=='Florist-Supplies.biz'){
	$department_code='Flor';
      }
if($department_code=='Soft Furnishings & Textiles'){
	$department_code='Textil';
      }
if($department_code=='Woodware Dept'){
  $department_code='Wood';
  $department_name='Woodware Department';

      }




    }
    
    $posible_fam_code=$cols[3];
    $posible_fam_name=$cols[6];
  }
  

  
  $column++;
  }



function create_shipping(){
  $max_cost=-1;
  $sql="select `World Region`,GROUP_CONCAT(\"'\",`Country Name`,\"'\") as countries,`World Region Key`  from kbase.`Country Dimension` group by `World Region`  ";
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    $world_region=$row['World Region'];
    $countries=$row['countries'];
    $wr_key=$row['World Region Key'];

    //print "$world_region \n";
    //$sql="insert into kbase.`World Region Dimension` (`World Region Name`) values ('$world_region')";
    //mysql_query($sql);
    //$wr_key=mysql_insert_id();
    //mysql_query("update kbase.`Country Dimension` set `World Region Key`=$wr_key where `World Region`='$world_region'  ");

   
    $sql=sprintf("select AVG(carriage) as cost from aw_old.paso_ordersxls  left join aw_old.paso_customer as pc on (pc.id=paso_cust) where tipo=3 and  a38!=40849 and carriage>0  and a10 not like  'UK' and  a10 not like '' and a10 in (%s);"
		 ,$countries
		 );
    $res2=mysql_query($sql);
    if($row2=mysql_fetch_array($res2)){
      $cost=(float) $row2['cost'];
      if($max_cost<$cost)
	$max_cost=$cost;
    }else
      $cost=0;
    
    $data=array('Shipping Type'=>'Normal','Shipping Destination Type'=>'World Region','Shipping Destination Key'=>$wr_key,'Shipping Price Method'=>'Flat','Shipping Allowance Metadata'=>sprintf("%.2f",$cost));
    
    $shipping=new Shipping('find create',$data);
    
    print "$world_region $cost $max_cost\n";
  }

  $sql=sprintf("update `Shipping Dimension` set `Shipping Allowance Metadata`=%.2f where  `Shipping Allowance Metadata`='0.00'",$max_cost);
   $res=mysql_query($sql);



$sql="select *  from kbase.`Country Dimension`   ";
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    
    $sql=sprintf("select AVG(carriage) as cost from aw_old.paso_ordersxls  left join aw_old.paso_customer as pc on (pc.id=paso_cust) where tipo=3 and  a38!=40849 and carriage>0  and a10 in ('%s');"
		 ,$row['Country Name']
		 );
    $res2=mysql_query($sql);
    if($row2=mysql_fetch_array($res2)){
      $cost=(float) $row2['cost'];
 
      
    }else{
      $cost=0;

    }

      

    if($cost>0){
      $cost=(float) $row2['cost'];
 $data=array('Shipping Type'=>'Normal','Shipping Destination Type'=>'Country','Shipping Destination Key'=>$row['Country Key'],'Shipping Price Method'=>'Flat','Shipping Allowance Metadata'=>sprintf("%.2f",$cost));
    
    $shipping=new Shipping('find create',$data);
      
    }else{
       $data=array('Shipping Type'=>'Normal','Shipping Destination Type'=>'Country','Shipping Destination Key'=>$wr_key,'Shipping Price Method'=>'Parent','Shipping Allowance Metadata'=>$row['World Region Key']);
    
       $shipping=new Shipping('find create',$data);

    }
    

  }

}



?>