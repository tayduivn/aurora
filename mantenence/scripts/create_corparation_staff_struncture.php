<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Family.php');
include_once('../../class.Product.php');
include_once('../../class.Supplier.php');
include_once('../../class.Page.php');
include_once('../../class.Store.php');
include_once('../../class.CompanyArea.php');
include_once('../../class.CompanyDepartment.php');
include_once('../../class.CompanyPosition.php');
include_once('../../class.TaxCategory.php');
include_once('../../class.Charge.php');
include_once('../../class.Staff.php');
include_once('../../class.Campaign.php');
include_once('../../class.Shipping.php');

error_reporting(E_ALL);

date_default_timezone_set('UTC');


$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if (!$con) {
    print "Error can not connect with database server\n";
    exit;
}

//$dns_db='dw_avant';
$db=@mysql_select_db($dns_db, $con);
if (!$db) {
    print "Error can not access the database\n";
    exit;
}


require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';

global $myconf;


$data=array(
          'editor'=>array('Date'=>'2003-08-28 09:00:00')
	  ,'Company Name'=>'Ancient Wisdom'
	  ,'Company Fiscal Name'=>'Ancient Wisdom Marketing Ltd'
	  ,'Company Tax Number'=>'764298589'
	  ,'Company Registration Number'=>'4108870'
	  ,'Company Main Plain Email'=>'mail@ancientwisdom.biz'
      );


$company=new Company('find create auto',$data);
$sql=sprintf("delete * from  `Corporation Dimension` " );
mysql_query($sql);
$sql=sprintf("insert into `Corporation Dimension` values (%s,'GBP',%d) ",prepare_mysql($company->data['Company Name']),$company->id );
mysql_query($sql);



$areas=array(
           array(
               'Company Key'=>$company->id,
               'Company Area Code'=>'OFC',
               'Company Area Name'=>'Office',
               'Company Area Description'=>'House of the administrative and creative Departments',
           )
           ,array(
               'Company Key'=>$company->id,
               'Company Area Code'=>'WAH',
               'Company Area Name'=>'Warehouse',
               'Company Area Description'=>'House of Picking,Packing and Stock Departments',

           )
           ,array(
               'Company Key'=>$company->id,
               'Company Area Code'=>'PRD',
               'Company Area Name'=>'Production',
               'Company Area Description'=>'House of the Manufacture Departments',

           )
       );

foreach($areas as $areas_data) {
    $area=new CompanyArea('find',$areas_data,'create');
}



$departments=array(
                 'OFC'=>array(
			        array(
                               'Company Department Code'=>'DIR',
                               'Company Department Name'=>'Direction',
                               'Company Department Description'=>'Director Office')
                           , 
			      
                           array(
                               'Company Department Code'=>'CUS.UK',
                               'Company Department Name'=>'Customer Services UK',
                               'Company Department Description'=>'Customer Services')
                           ,array(
                               'Company Department Code'=>'CUS.DE',
                               'Company Department Name'=>'Customer Services Germany',
                               'Company Department Description'=>'Customer Services')
                           ,array(
                               'Company Department Code'=>'CUS.FR',
                               'Company Department Name'=>'Customer Services France',
                               'Company Department Description'=>'Customer Services')
                           ,array(
                               'Company Department Code'=>'CUS.PL',
                               'Company Department Name'=>'Customer Services Poland',
                               'Company Department Description'=>'Customer Services')
                           ,array(
                               'Company Department Code'=>'MRK',
                               'Company Department Name'=>'Marketing',
                               'Company Department Description'=>'Marketing Department')
                           ,array(
                               'Company Department Code'=>'ACC',
                               'Company Department Name'=>'Accounting',
                               'Company Department Description'=>'Accounting Department')
                           ,array(
                               'Company Department Code'=>'SMA',
                               'Company Department Name'=>'Store Product Management',
                               'Company Department Description'=>'Department where we order stock and put it on the webpage for selling')

                       ),'WAH'=>array(
                                   array(
                                       'Company Department Code'=>'OHA',
                                       'Company Department Name'=>'Order Handing',
                                       'Company Department Description'=>'Picking & Packing Department')
                                   ,array(
                                       'Company Department Code'=>'STK',
                                       'Company Department Name'=>'Stock Keeping',
                                       'Company Department Description'=>'Dealing with Deliveries and stock movements')

                               ),'PRD'=>array(
                                           array(
                                               'Company Department Code'=>'GEN',
                                               'Company Department Name'=>'General Production',
                                               'Company Department Description'=>'Product all kinds of products')


                                       )


             );


foreach($departments as $area_code=>$departments_data) {
    $area=new CompanyArea('code',$area_code);
    
    if($area_code=='PRD')
    $production_area_key=$area->id;
       if($area_code=='WAH')
    $warehouse_area_key=$area->id;
       if($area_code=='OFC')
    $office_area_key=$area->id;
    
    
    foreach($departments_data as $data) {
        $area->add_department($data);
    }
}


$positions=array(
		 'MRK'=>array(
			      array(
				    'Company Position Code'=>'MRK.O',
				    'Company Position Title'=>'Marketing',
				    'Company Position Description'=>'Marketing'
				    )
			      ,array(
				    'Company Position Code'=>'WEB',
				    'Company Position Title'=>'Web Designer',
				    'Company Position Description'=>'Web Designer'
				    )
			      
			      )
		 ,'DIR'=>array(
			      array(
				    'Company Position Code'=>'DIR',
				    'Company Position Title'=>'Director',
				    'Company Position Description'=>'General Director'
				    )
			      )
		 	 
		 ,'ACC'=>array(
			      array(
				    'Company Position Code'=>'ACC',
				    'Company Position Title'=>'Accounts',
				    'Company Position Description'=>'General Accounts '
				    )
			      )
		 	 
		 
		 	 
		 
		 	 
		 ,'STK'=>array(
			       
                          array(
				'Company Position Code'=>'WAH.SK',
				'Company Position Title'=>'Warehouse Stock Keeper',
                             'Company Position Description'=>'Stock Receaving & Handing'
                         ),array(
                             'Company Position Code'=>'OFC.SK',
                             'Company Position Title'=>'Stock Controller',
                             'Company Position Description'=>'Stock Control'
                         )
                          
                          
                         
                     )
		  ,'SMA'=>array(
			       
                    array(
                             'Company Position Code'=>'BUY',
                             'Company Position Title'=>'Buyer',
                             'Company Position Description'=>'Buyer'
                         )
                          
                          
                         
                     )
               ,'OHA'=>array(
                         array(
                             'Company Position Code'=>'PICK',
                             'Company Position Title'=>'Picker',
                             'Company Position Description'=>'Warehouse Parts Picker'
                         ),
                          array(
                             'Company Position Code'=>'PACK',
                             'Company Position Title'=>'Packer',
                             'Company Position Description'=>'Orders Packer'
                         ),
                     
                          array(
                             'Company Position Code'=>'OHA.DM',
                             'Company Position Title'=>'Dispatch Supervisor',
                             'Company Position Description'=>'Dispatch Supervisor'
                         ),
                          array(
                             'Company Position Code'=>'OHA.M',
                             'Company Position Title'=>'Warehouse Manager',
                             'Company Position Description'=>'Warehouse Supervisor'
                         )
                          
                         
                     )
	       ,'GEN'=>array(
                         array(
                             'Company Position Code'=>'PROD.M',
                             'Company Position Title'=>'Production Commander in Chief',
                             'Company Position Description'=>'Production Supervisor'
                         ),
                          array(
                             'Company Position Code'=>'PROD.O',
                             'Company Position Title'=>'Production Operative',
                             'Company Position Description'=>'Production Associate'
                         )
			     )
	       ,'CUS.UK,CUS.PL,CUS.DE,CUS.FR'=>array(
			array(
                             'Company Position Code'=>'CUS',
                             'Company Position Title'=>'Customer Service',
                             'Company Position Description'=>'Customer Service'
                         )	   
						     )
						     
		 );
$departments_keys=array();
foreach($positions as $department_codes=>$positions_data) {
  foreach(preg_split('/,/',$department_codes) as $key =>$department_code ){

    $department=new CompanyDepartment('code',$department_code);
    $departments_keys[$department_code]=$department->id;
    if(!$department->id){
      print_r($department);
    exit;
    }
    foreach($positions_data as $data) {
      $department->add_position($data);
    }
  }

}






$staff=array(
	      'PROD.O'=>array(
	     
			      array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Joanna Ciba','Staff Alias'=>'joana','Staff Type'=>'Temporal Worker')
			      ,array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Magdalena Dawiskiba','Staff Alias'=>'magda')
			      ,array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Dusan Belan','Staff Alias'=>'dusan')
			      ,array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Lucie Sicova','Staff Alias'=>'lucie')
			      ,array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Daniela Matovlava','Staff Alias'=>'daniela')
			      ,array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Olga Belanova','Staff Alias'=>'olga')
			      ,array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Dana Marsallova','Staff Alias'=>'dana')
			      ,array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Danielle Cox','Staff Alias'=>'danielle','Staff Type'=>'Temporal Worker')
			      )
	      ,'PROD.M'=>array(
	     
			      array('Staff Area Key'=>$production_area_key,'Staff Department Key'=>$departments_keys['GEN'],'Staff Name'=>'Neal','Staff Alias'=>'neal')
			      )

	      ,'PICK'=>array(
			    array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Steffanie Cox','Staff Alias'=>'stephanie','Staff Type'=>'Temporal Worker','Staff Currently Working'=>'No')
			    ,array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Adriana Bobokova','Staff Alias'=>'adriana')
			    ,array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Janet Walker','Staff Alias'=>'janet')
			    , array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Lenka Ondrisova','Staff Alias'=>'lenka','Staff Currently Working'=>'No')
			    )
	      ,'PACK'=>array(
			     array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Andrew Barry','Staff Alias'=>'andy')
			     ,array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Lucy Adams','Staff Alias'=>'lucy','Staff Type'=>'Temporal Worker')
			     			     ,array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Ben','Staff Alias'=>'ben','Staff Type'=>'Temporal Worker')

			     )
	       ,'WAH.SK'=>array(
			     array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['STK'],'Staff Name'=>'Michael Wragg','Staff Alias'=>'michael')
			     ,array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['STK'],'Staff Name'=>'Brian','Staff Alias'=>'brian')

			    
			     )
	       ,'OHA.M'=>array(
			     array('Staff Area Key'=>$warehouse_area_key,'Staff Department Key'=>$departments_keys['OHA'],'Staff Name'=>'Craige Blakemore','Staff Alias'=>'craige')
			     )
	  

	      ,'BUY'=>array(
			     array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['SMA'],'Staff Name'=>'Alan W','Staff Alias'=>'alan')
			     
				)
	      ,'OFC.SK'=>array(
			     array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['STK'],'Staff Name'=>'Eric Zee','Staff Alias'=>'eric')
				)
	      ,'WEB'=>array(
			     array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.UK'],'Staff Name'=>'Raul Perusquia','Staff Alias'=>'raul')
				)
	      ,'DIR'=>array(
			     array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['DIR'],'Staff Name'=>'David Hardy','Staff Alias'=>'david')
				)
	       ,'ACC'=>array(
			     array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['ACC'],'Staff Name'=>'Slavka Hardy','Staff Alias'=>'slavka')
			     )
	       ,'MRK.O'=>array(
			     array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['MRK'],'Staff Name'=>'Katka Buchy','Staff Alias'=>'katka','Staff Currently Working'=>'No')
			     ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['MRK'],'Staff Name'=>'Tomas Belam','Staff Alias'=>'tomas')
			     )


	      ,'CUS'=>array(
			    array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.UK'],'Staff Name'=>'Kerry Miskelly','Staff Alias'=>'kerry')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.UK'],'Staff Name'=>'Sarka Doubravova','Staff Alias'=>'sarka')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.UK'],'Staff Name'=>'Zoe','Staff Alias'=>'zoe','Staff Currently Working'=>'No')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.UK'],'Staff Name'=>'Philippe Buchy','Staff Alias'=>'philippe')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.DE'],'Staff Name'=>'Martina Otte','Staff Alias'=>'martina')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.FR'],'Staff Name'=>'Nassim Khelifa','Staff Alias'=>'nassim','Staff Currently Working'=>'No')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.FR'],'Staff Name'=>'Bruno Petit-Jean','Staff Alias'=>'bruno')

			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.FR'],'Staff Name'=>'Nabil','Staff Alias'=>'nabil','Staff Currently Working'=>'No')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.UK'],'Staff Name'=>'Amanda Fray','Staff Alias'=>'amanda','Staff Currently Working'=>'No')
			    ,Array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.PL'],'Staff Name'=>'Urszula Baka','Staff Alias'=>'urszula')
			    ,array('Staff Area Key'=>$office_area_key,'Staff Department Key'=>$departments_keys['CUS.UK'],'Staff Name'=>'Zoe Hilbert','Staff Alias'=>'zhilbert')

			    
			    )


	     );


foreach($staff as $position_codes=>$staff_data) {
  foreach(preg_split('/,/',$position_codes) as $key =>$position_code ){

    $position=new CompanyPosition('code',$position_code);
    if(!$position->id){
      print "$position_code\n";
      //print_r($position);
    exit;
    }
    foreach($staff_data as $data) {
      $position->add_staff($data);
    }
  }

}


$data=array(
	    'Tax Category Code'=>'S1',
	    'Tax Category Name'=>'VAT 17.5%',
'Tax Category Rate'=>0.175
);
$cat_tax=new TaxCategory('find',$data,'create');



$data=array(
	    'Tax Category Code'=>'S2',
'Tax Category Name'=>'VAT 20%',
'Tax Category Rate'=>0.2
);
$cat_tax=new TaxCategory('find',$data,'create');
$data=array(
	    'Tax Category Code'=>'S3',
'Tax Category Name'=>'VAT 15%',
'Tax Category Rate'=>0.15
);
$cat_tax=new TaxCategory('find',$data,'create');

$store_data=array('Store Code'=>'UK',
		  'Store Name'=>'Ancient Wisdom',
		  'Store Locale'=>'en_GB',
		  'Store Home Country Code 2 Alpha'=>'GB',
		  'Store Currency Code'=>'GBP',
		  'Store Home Country Name'=>'United Kingdom', 
		  'Store Home Country Short Name'=>'UK', 
		  'Store URL'=>'ancietwisdom.biz',
		  'Store Email'=>'mail@ancientwisdom.biz',
		  'Store Telephone'=>'+44 (0) 114 272 9165',
		  'Store FAX'=>'+44 (0) 114 270 6571',
		  'Store Slogan'=>'giftware sourced worldwide',
		  'Store Tax Category Code'=>'S1'
		  );
$store=new Store('find',$store_data,'create');

$store_data=array('Store Code'=>'DE',
		  'Store Name'=>'AW-Geshenke',
		  'Store Locale'=>'de_DE',
		  'Store Home Country Code 2 Alpha'=>'DE',
		  'Store Currency Code'=>'EUR',
		  'Store Home Country Name'=>'Germany', 
		  'Store Home Country Short Name'=>'DE', 
		  'Store URL'=>'aw-geschenke.com',
		  'Store Email'=>'martina@aw-geschenke.com',
		  'Store Telephone'=>'+49 (0)831 2531 986',
		  'Store FAX'=>'',
		  'Store Slogan'=>'Geschenkwaren',
'Store Tax Category Code'=>'S1'
		  );
$store=new Store('find',$store_data,'create');
$store_data=array('Store Code'=>'FR',
		  'Store Name'=>'AW-Cadeaux',
		  'Store Locale'=>'fr_FR',
		  'Store Home Country Code 2 Alpha'=>'FR',
		  'Store Currency Code'=>'EUR',
		  'Store Home Country Name'=>'France', 
		  'Store Home Country Short Name'=>'FR', 
		  'Store URL'=>'aw-cadeux.com',
		  'Store Email'=>'nassim@aw-cadeux.com',
		  'Store Telephone'=>'',
		  'Store FAX'=>'',
		  'Store Slogan'=>'',
		  'Store Tax Category Code'=>'S1'
		  );
$store=new Store('find',$store_data,'create');

$warehouse=new Warehouse('find',array('Warehouse Code'=>'W','Warehouse Name'=>'Parkwood'),'create');

$unk_location=new Location('find',array('Location Code'=>'UNK','Location Name'=>'Unknown'),'create');

$unk_supplier=new Supplier('find',array('Supplier Code'=>'UNK','Supplier Name'=>'Unknown'),'create');

$store_key=1;
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
		     ,'Campaign Code'=>'GB.GR'
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
		      ,'Campaign Code'=>'GB.Vol'
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
		      ,'Campaign Code'=>'GB.FShip'
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
		       ,'Campaign Code'=>'GB.BOGOF'
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
		       ,'Campaign Code'=>'GB.FOB'
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


//=============================================================
// Germany
$store=new Store("code","DE");
$store_key=$store->id;

//exit($store_key);
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
		'Product Family Code'=>'PND_DE',
		'Product Family Name'=>'Products Without Family',
		'Product Family Main Department Key'=>$dept_no_dept_key,
		'Product Family Store Key'=>$store_key,
		'Product Family Special Characteristic'=>'None'
		);

$fam_no_fam=new Family('find',$fam_data,'create');
$fam_no_fam_key=$fam_no_fam->id;

//print_r($fam_no_fam);

$fam_data=array(
		'Product Family Code'=>'Promo_DE',
		'Product Family Name'=>'Promotional Items',
		'Product Family Main Department Key'=>$dept_promo_key,
		'Product Family Store Key'=>$store_key,
		'Product Family Special Characteristic'=>'None'
		);



$fam_promo=new Family('find',$fam_data,'create');



$fam_no_fam_key=$fam_no_fam->id;
$fam_promo_key=$fam_promo->id;

$campaign=array(
		'Campaign Name'=>'Goldprämie'
		 ,'Campaign Code'=>'DE.GR'
		,'Campaign Description'=>'Small order charge waive & discounts on seleted items if last order within 1 calendar month'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Order Interval'
		,'Campaign Deal Terms Description'=>'last order within 1 month'
		,'Campaign Deal Terms Lock'=>'Yes'
        ,'Store Key'=>$store_key
		);
$gold_camp=new Campaign('find create',$campaign);
//print_r($gold_camp);
//exit;

$data=array(
	    'Deal Name'=>'[Product Family Code] Goldprämie'
	    ,'Deal Trigger'=>'Family'
	    ,'Deal Allowance Type'=>'Percentage Off'
	    ,'Deal Allowance Description'=>'[Percentage Off] off'
	    ,'Deal Allowance Target'=>'Family'
	    ,'Deal Allowance Lock'=>'No'
	    );
$gold_camp->add_deal_schema($data);



//$data=array('Deal Allowance Target Key'=>$small_order_charge->id);
//$gold_camp->create_deal('Free [Charge Name]',$data);

$gold_reward_cam_id=$gold_camp->id;

$campaign=array(
		'Campaign Name'=>'Volumen Discount'
	 ,'Campaign Code'=>'DE.Vol'
		,'Campaign Trigger'=>'Family'
		,'Campaign Description'=>'Percentage off when order more than some quantity of products in the same family'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Family Quantity Ordered'
		,'Campaign Deal Terms Description'=>'order [Quantity] or more same family'
		,'Campaign Deal Terms Lock'=>'No'
		,'Store Key'=>$store_key
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
		     	 ,'Campaign Code'=>'DE.FShip'
				   ,'Campaign Description'=>'Free shipping to selected destinations when order more than some amount'
				   ,'Campaign Begin Date'=>''
				   ,'Campaign Expiration Date'=>''
				   ,'Campaign Deal Terms Type'=>'Order Items Net Amount AND Shipping Country'
				   ,'Campaign Deal Terms Description'=>'Orders shipped to {Country Name} and Order Items Net Amount more than {Order Items Net Amount}'
				   ,'Campaign Deal Terms Lock'=>'No'
				   ,'Store Key'=>$store_key
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


$shipping_uk=new Shipping('find',array('Country Code'=>'DEU'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','DEU','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);


$shipping_uk=new Shipping('find',array('Country Code'=>'DNK'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','DNK','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);



$shipping_uk=new Shipping('find',array('Country Code'=>'AUT'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','AUT','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);


$shipping_uk=new Shipping('find',array('Country Code'=>'NOR'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','AUT','€795');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);

$campaign=array(
		'Campaign Name'=>'BOGOF'
	 ,'Campaign Code'=>'DE.BOGOF'
		,'Campaign Description'=>'Buy one Get one Free'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Product Quantity Ordered'
		,'Campaign Deal Terms Description'=>'Buy 1'
		,'Campaign Deal Terms Lock'=>'Yes'
		,'Store Key'=>$store_key
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
	 ,'Campaign Code'=>'DE.FOB'
		,'Campaign Trigger'=>'Order'
		,'Campaign Description'=>'When you order over €100+vat for the first time we give you over a €100 of stock. (at retail value).'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Order Total Net Amount AND Order Number'
		,'Campaign Deal Terms Description'=>'order over £100+tax on the first order '
		,'Campaign Deal Terms Lock'=>'Yes'
		,'Store Key'=>$store_key
		);
$camp=new Campaign('find create',$campaign);


$data=array(
	    'Deal Name'=>'First Order Bonus [Counter]'
	    ,'Deal Trigger'=>'Order'
            ,'Deal Description'=>'When you order over €100+vat for the first time we give you over a €100 of stock. (at retail value).'
	    ,'Deal Allowance Type'=>'Get Free'
	    ,'Deal Allowance Description'=>'Free Bonus Stock ([Product Code])'
	    ,'Deal Allowance Target'=>'Product'
	    ,'Deal Allowance Lock'=>'No'
	    
	    );
$camp->add_deal_schema($data);
//============================================================
// France

$store=new Store("code","DE");
$store_key=$store->id;

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
		'Product Family Code'=>'PND_FR',
		'Product Family Name'=>'Products Without Family',
		'Product Family Main Department Key'=>$dept_no_dept_key,
		'Product Family Store Key'=>$store_key,
		'Product Family Special Characteristic'=>'None'
		);

$fam_no_fam=new Family('find',$fam_data,'create');
$fam_no_fam_key=$fam_no_fam->id;

//print_r($fam_no_fam);

$fam_data=array(
		'Product Family Code'=>'Promo_FR',
		'Product Family Name'=>'Promotional Items',
		'Product Family Main Department Key'=>$dept_promo_key,
		'Product Family Store Key'=>$store_key,
		'Product Family Special Characteristic'=>'None'
		);



$fam_promo=new Family('find',$fam_data,'create');



$fam_no_fam_key=$fam_no_fam->id;
$fam_promo_key=$fam_promo->id;

$campaign=array(
		'Campaign Name'=>'Statut Gold'	
 ,'Campaign Code'=>'FR.GR'
		,'Campaign Description'=>'Small order charge waive & discounts on seleted items if last order within 1 calendar month'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Order Interval'
		,'Campaign Deal Terms Description'=>'last order within 1 month'
		,'Campaign Deal Terms Lock'=>'Yes'
        ,'Store Key'=>$store_key
		);
$gold_camp=new Campaign('find create',$campaign);
//print_r($gold_camp);
//exit;

$data=array(
	    'Deal Name'=>'[Product Family Code] Statut Gold'
	    ,'Deal Trigger'=>'Family'
	    ,'Deal Allowance Type'=>'Percentage Off'
	    ,'Deal Allowance Description'=>'[Percentage Off] off'
	    ,'Deal Allowance Target'=>'Family'
	    ,'Deal Allowance Lock'=>'No'
	    );
$gold_camp->add_deal_schema($data);



//$data=array('Deal Allowance Target Key'=>$small_order_charge->id);
//$gold_camp->create_deal('Free [Charge Name]',$data);

$gold_reward_cam_id=$gold_camp->id;

$campaign=array(
		'Campaign Name'=>'Volumen Discount' ,'Campaign Code'=>'FR.Vol'
		,'Campaign Trigger'=>'Family'
		,'Campaign Description'=>'Percentage off when order more than some quantity of products in the same family'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Family Quantity Ordered'
		,'Campaign Deal Terms Description'=>'order [Quantity] or more same family'
		,'Campaign Deal Terms Lock'=>'No'
		,'Store Key'=>$store_key
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
		      ,'Campaign Code'=>'FR.FShip'
				   ,'Campaign Description'=>'Free shipping to selected destinations when order more than some amount'
				   ,'Campaign Begin Date'=>''
				   ,'Campaign Expiration Date'=>''
				   ,'Campaign Deal Terms Type'=>'Order Items Net Amount AND Shipping Country'
				   ,'Campaign Deal Terms Description'=>'Orders shipped to {Country Name} and Order Items Net Amount more than {Order Items Net Amount}'
				   ,'Campaign Deal Terms Lock'=>'No'
				   ,'Store Key'=>$store_key
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


$shipping_uk=new Shipping('find',array('Country Code'=>'DEU'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','DEU','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);


$shipping_uk=new Shipping('find',array('Country Code'=>'DNK'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','DNK','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);



$shipping_uk=new Shipping('find',array('Country Code'=>'AUT'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','AUT','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);


$shipping_uk=new Shipping('find',array('Country Code'=>'NOR'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','AUT','€795');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);

$campaign=array(
		'Campaign Name'=>'BOGOF'
 ,'Campaign Code'=>'FR.BOGOF'
		,'Campaign Description'=>'Buy one Get one Free'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Product Quantity Ordered'
		,'Campaign Deal Terms Description'=>'Buy 1'
		,'Campaign Deal Terms Lock'=>'Yes'
		,'Store Key'=>$store_key
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
		'Campaign Name'=>'First Order Bonus' ,'Campaign Code'=>'FR.FOB'

		,'Campaign Trigger'=>'Order'
		,'Campaign Description'=>'When you order over €100+vat for the first time we give you over a €100 of stock. (at retail value).'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Order Total Net Amount AND Order Number'
		,'Campaign Deal Terms Description'=>'order over £100+tax on the first order '
		,'Campaign Deal Terms Lock'=>'Yes'
		,'Store Key'=>$store_key
		);
$camp=new Campaign('find create',$campaign);


$data=array(
	    'Deal Name'=>'First Order Bonus [Counter]'
	    ,'Deal Trigger'=>'Order'
            ,'Deal Description'=>'When you order over €100+vat for the first time we give you over a €100 of stock. (at retail value).'
	    ,'Deal Allowance Type'=>'Get Free'
	    ,'Deal Allowance Description'=>'Free Bonus Stock ([Product Code])'
	    ,'Deal Allowance Target'=>'Product'
	    ,'Deal Allowance Lock'=>'No'
	    
	    );
$camp->add_deal_schema($data);

//==================================================
// Poland

$store_data=array('Store Code'=>'PL',
		  'Store Name'=>'AW Podarki',
		  'Store Locale'=>'pl_PL',
		  'Store Home Country Code 2 Alpha'=>'PL',
		  'Store Currency Code'=>'PLN',
		  'Store Home Country Name'=>'Poland', 
		  'Store Home Country Short Name'=>'PL', 
		  'Store URL'=>'www.aw-podarki.com',
		  'Store Telephone'=>'+48 1142 677 736',
		  'Store Email'=>'urszula@aw-podarki.com',
		  );
$store=new Store('find',$store_data,'create');
$store_key=$store->id;

$dept_data=array(
		 'Product Department Code'=>'ND_PL',
		 'Product Department Name'=>'Products Without Department',
		 'Product Department Store Key'=>$store_key
		 );

$dept_no_dept=new Department('find',$dept_data,'create');
$dept_no_dept_key=$dept_no_dept->id;

$dept_data=array(
		 'Product Department Code'=>'Promo_PL',
		 'Product Department Name'=>'Promotional Items',
		 'Product Department Store Key'=>$store_key
		 );
$dept_promo=new Department('find',$dept_data,'create');
$dept_promo_key=$dept_promo->id;

$fam_data=array(
		'Product Family Code'=>'PND_PL',
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
		'Campaign Name'=>'Goldprämie'
		,'Campaign Code'=>'PL.GR'

		,'Campaign Description'=>'Small order charge waive & discounts on seleted items if last order within 1 calendar month'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Order Interval'
		,'Campaign Deal Terms Description'=>'last order within 1 month'
		,'Campaign Deal Terms Lock'=>'Yes'
        ,'Store Key'=>$store_key
		);
$gold_camp=new Campaign('find create',$campaign);
//print_r($gold_camp);
//exit;

$data=array(
	    'Deal Name'=>'[Product Family Code] Goldprämie'
	    ,'Deal Trigger'=>'Family'
	    ,'Deal Allowance Type'=>'Percentage Off'
	    ,'Deal Allowance Description'=>'[Percentage Off] off'
	    ,'Deal Allowance Target'=>'Family'
	    ,'Deal Allowance Lock'=>'No'
	    );
$gold_camp->add_deal_schema($data);



//$data=array('Deal Allowance Target Key'=>$small_order_charge->id);
//$gold_camp->create_deal('Free [Charge Name]',$data);

$gold_reward_cam_id=$gold_camp->id;

$campaign=array(
		'Campaign Name'=>'Volumen Discount'	,'Campaign Code'=>'PL.Vol'
		,'Campaign Trigger'=>'Family'
		,'Campaign Description'=>'Percentage off when order more than some quantity of products in the same family'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Family Quantity Ordered'
		,'Campaign Deal Terms Description'=>'order [Quantity] or more same family'
		,'Campaign Deal Terms Lock'=>'No'
		,'Store Key'=>$store_key
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
				   'Campaign Name'=>'Free Shipping'	,'Campaign Code'=>'PL.FShip'
		     
				   ,'Campaign Description'=>'Free shipping to selected destinations when order more than some amount'
				   ,'Campaign Begin Date'=>''
				   ,'Campaign Expiration Date'=>''
				   ,'Campaign Deal Terms Type'=>'Order Items Net Amount AND Shipping Country'
				   ,'Campaign Deal Terms Description'=>'Orders shipped to {Country Name} and Order Items Net Amount more than {Order Items Net Amount}'
				   ,'Campaign Deal Terms Lock'=>'No'
				   ,'Store Key'=>$store_key
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


$shipping_uk=new Shipping('find',array('Country Code'=>'DEU'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','DEU','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);


$shipping_uk=new Shipping('find',array('Country Code'=>'DNK'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','DNK','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);



$shipping_uk=new Shipping('find',array('Country Code'=>'AUT'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','AUT','€500');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);


$shipping_uk=new Shipping('find',array('Country Code'=>'NOR'));
$terms_description=sprintf('Orders shipped to %s with Order Items Net Amount more than %s','AUT','€795');
$data=array(
	    'Deal Allowance Target Key'=>$shipping_uk->id
	    ,'Deal Terms Description'=>$terms_description
	    );
$free_shipping_campaign->create_deal('[Country Name] Free Shipping',$data);

$campaign=array(
		'Campaign Name'=>'BOGOF'	,'Campaign Code'=>'PL.BOGOF'
		,'Campaign Description'=>'Buy one Get one Free'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Product Quantity Ordered'
		,'Campaign Deal Terms Description'=>'Buy 1'
		,'Campaign Deal Terms Lock'=>'Yes'
		,'Store Key'=>$store_key
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
		'Campaign Name'=>'First Order Bonus'	,'Campaign Code'=>'PL.FOB'
		,'Campaign Trigger'=>'Order'
		,'Campaign Description'=>'When you order over €100+vat for the first time we give you over a €100 of stock. (at retail value).'
		,'Campaign Begin Date'=>''
		,'Campaign Expiration Date'=>''
		,'Campaign Deal Terms Type'=>'Order Total Net Amount AND Order Number'
		,'Campaign Deal Terms Description'=>'order over £100+tax on the first order '
		,'Campaign Deal Terms Lock'=>'Yes'
		,'Store Key'=>$store_key
		);
$camp=new Campaign('find create',$campaign);


$data=array(
	    'Deal Name'=>'First Order Bonus [Counter]'
	    ,'Deal Trigger'=>'Order'
            ,'Deal Description'=>'When you order over €100+vat for the first time we give you over a €100 of stock. (at retail value).'
	    ,'Deal Allowance Type'=>'Get Free'
	    ,'Deal Allowance Description'=>'Free Bonus Stock ([Product Code])'
	    ,'Deal Allowance Target'=>'Product'
	    ,'Deal Allowance Lock'=>'No'
	    
	    );
$camp->add_deal_schema($data);

?>