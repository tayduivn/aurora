<?
//include("../../external_libs/adminpro/adminpro_config.php");

include_once('../../app_files/db/dns.php');
include_once('../../classes/Department.php');
include_once('../../classes/Family.php');
include_once('../../classes/Product.php');
include_once('../../classes/Supplier.php');
include_once('../../classes/Order.php');
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
$_SESSION['lang']=1;
include_once('local_map.php');

include_once('map_order_functions.php');

$software='Get_Orders_DB.php';
$version='V 1.0';//75693

$Data_Audit_ETL_Software="$software $version";
srand(12344);

$sql="select * from  orders_data.orders  where   (last_transcribed is NULL  or last_read>last_transcribed)  order by filename ";
//$sql="select * from  orders_data.orders where filename like '%refund.xls'   order by filename";
$contador=0;
//print $sql;
$res=mysql_query($sql);

while($row2=mysql_fetch_array($res, MYSQL_ASSOC)){

 
  $sql="select * from orders_data.data where id=".$row2['id'];
  $result=mysql_query($sql);
  if($row=mysql_fetch_array($result, MYSQL_ASSOC)){


    //           echo "                                                          Memory: ".memory_get_usage(true) . "\n";

    $order_data_id=$row2['id'];
    $filename=$row2['filename'];
    $contador++;
    $total_credit_value=0;

    // check if it is already readed
    $update=false;$old_order_key=0;
    $sql=sprintf("select count(*) as num  from `Order Dimension`  where `Order Original Metadata`=%d  ",$order_data_id);

    $result_test=mysql_query($sql);
    if($row_test=mysql_fetch_array($result_test, MYSQL_ASSOC)){
      if($row_test['num']==0)
	print "NEW $contador $order_data_id $filename ";
      else{
	$update=true;
	print "UPD $contador $order_data_id $filename ";
      }
    }




    

    $header=mb_unserialize($row['header']);
    $products=mb_unserialize($row['products']);

    

    //    echo "Memory: ".memory_get_usage(true) . "\n";





    $filename_number=str_replace('.xls','',str_replace($row2['directory'],'',$row2['filename']));
    $map_act=$_map_act;$map=$_map;$y_map=$_y_map;
     
    // tomando en coeuntas diferencias en la posicion de los elementos
  
    if($filename_number==19015){
      $y_map['code']=4;
    }
     
  
    if($filename_number<18803){// Change map if the orders are old
      $y_map=$_y_map_old;
      foreach($_map_old as $key=>$value)
	$map[$key]=$value;
    }
    $prod_map=$y_map;
    if($filename_number==53378){
      $prod_map['no_price_bonus']=true;
      $prod_map['no_reorder']=true;
      $prod_map['bonus']=11;
    }elseif($filename_number==64607){
      $prod_map['no_price_bonus']=true;
      $prod_map['no_reorder']=true;
      $prod_map['bonus']=11;
    }
     


    list($act_data,$header_data)=read_header($header,$map_act,$y_map,$map);
    $header_data=filter_header($header_data);
    list($tipo_order,$parent_order_id)=get_tipo_order($header_data['ltipo'],$header_data);
  
    

    if(preg_match('/^\d{5}sh$/i',$filename_number)){
      $tipo_order=7;
      $parent_order_id=preg_replace('/sh/i','',$filename_number);
    }
      if(preg_match('/^\d{5}sht$/i',$filename_number)){
      $tipo_order=7;
      $parent_order_id=preg_replace('/sht/i','',$filename_number);
    }

    if(preg_match('/^\d{5}rpl$/i',$filename_number)){
      $tipo_order=6;
      $parent_order_id=preg_replace('/rpl/i','',$filename_number);

    }
    if(preg_match('/^\d{4,5}r$|^\d{4,5}ref$|^\d{4,5}\s?refund$|^\d{4,5}rr$|^\d{4,5}ra$|^\d{4,5}r2$|^\d{4,5}\-2ref$|^\d{5}rfn$/i',$filename_number)){
      $tipo_order=9;
      $parent_order_id=preg_replace('/r$|ref$|refund$|rr$|ra$|r2$|\-2ref$|rfn$/i','',$filename_number);


    }


    //if($tipo_order==2 or $tipo_order==1){
    //  print "\n";
    //  continue;
    // }

    list($date_index,$date_order,$date_inv)=get_dates($row2['timestamp'],$header_data,$tipo_order,true);
    if($date_order=='')$date_index2=$date_index;else$date_index2=$date_order;

    if($tipo_order==2  or $tipo_order==6 or $tipo_order==7 or $tipo_order==9 ){
      $date2=$date_inv;
    }elseif($tipo_order==4  or   $tipo_order==5 or    $tipo_order==8  )
      $date2=$date_index;
    else
      $date2=$date_order;
  

    $transactions=read_products($products,$prod_map);
    unset($products);
  //   echo "Memory: ".memory_get_usage(true) . "x\n";
//     echo "Memory: ".memory_get_usage() . "x\n";
    $_customer_data=setup_contact($act_data,$header_data,$date_index2);

    //    print_r($_customer_data);
    foreach($_customer_data as $_key =>$value){
      $key=$_key;
      if($_key=='type')
      $key=preg_replace('/^type$/','Customer Type',$_key);
      if($_key=='contact_name')
      $key=preg_replace('/^contact_name$/','Customer Main Contact Name',$_key);
      if($_key=='company_name')
      $key=preg_replace('/^company_name$/','Customer Company Name',$_key);
      if($_key=='email')
      $key=preg_replace('/^email$/','Customer Main Plain Email',$_key);
      if($_key=='telephone')
      $key=preg_replace('/^telephone$/','Customer Main Telephone',$_key);
      if($_key=='fax')
      $key=preg_replace('/^fax$/','Customer Main FAX',$_key);
      if($_key=='mobile')
	$key=preg_replace('/^mobile$/','Customer Mobile',$_key);

      $customer_data[$key]=$value;

    }
    if($customer_data['Customer Type']=='Company')
      $customer_data['Customer Name']=$customer_data['Customer Company Name'];
    else
      $customer_data['Customer Name']=$customer_data['Customer Main Contact Name'];
    if(isset($_customer_data['address_data'])){
      $customer_data['Customer Address Line 1']=$_customer_data['address_data']['address1'];
      $customer_data['Customer Address Line 2']=$_customer_data['address_data']['address2'];
      $customer_data['Customer Address Line 3']=$_customer_data['address_data']['address3'];
      $customer_data['Customer Address Town']=$_customer_data['address_data']['town'];
      $customer_data['Customer Address Postal Code']=$_customer_data['address_data']['postcode'];
      $customer_data['Customer Address Country Name']=$_customer_data['address_data']['country'];
      $customer_data['Customer Address Country Primary Division']=$_customer_data['address_data']['country_d1'];
      $customer_data['Customer Address Country Secondary Division']=$_customer_data['address_data']['country_d2'];
      unset($customer_data['address_data']);
    }
    if(isset($_customer_data['address_data']) and $_customer_data['has_shipping']){
      $customer_data['Customer Shipping Address']['Address Line 1']=$_customer_data['shipping_data']['address1'];
      $customer_data['Customer Shipping Address']['Address Line 2']=$_customer_data['shipping_data']['address2'];
      $customer_data['Customer Shipping Address']['Address Line 3']=$_customer_data['shipping_data']['address3'];
      $customer_data['Customer Shipping Address']['Address Town']=$_customer_data['shipping_data']['town'];
      $customer_data['Customer Shipping Address']['Address Postal Code']=$_customer_data['shipping_data']['postcode'];
      $customer_data['Customer Shipping Address']['Address Country Name']=$_customer_data['shipping_data']['country'];
      $customer_data['Customer Shipping Address']['Address Country Primary Division']=$_customer_data['shipping_data']['country_d1'];
      $customer_data['Customer Shipping Address']['Address Country Secondary Division']=$_customer_data['shipping_data']['country_d2'];
      unset($customer_data['shipping_data']);
    }

   


    //  print_r($transactions);
  
    if(strtotime($date_order)>strtotime($date2)){
      
      print "ERROR ON DATES\n >    ".$header_data['date_order']."  ".$header_data['date_inv']."  ".$header_data['order_num']."  $date_order  $date2   $order_data_id   $filename  \n ";
      
       
    }
 

    $data=array();
    $data['order date']=$date_order;
    $data['order id']=$header_data['order_num'];
    $data['order customer message']=$header_data['notes2'];
    if($data['order customer message']==0)
      $data['order customer message']='';

    $data['order original data mime type']='application/vnd.ms-excel';
    $data['order original data']=$row2['filename'];
    $data['order original data source']='DB:orders_data.order.data';
    $data['order original metadata']=$row2['id'];
    $data['Order Main Source Type']='Unknown';
    //print_r($header_data);

    $products_data=array();
    $data_invoice_transactions=array();
    $data_dn_transactions=array();
    $data_bonus_transactions=array();

    $credits=array();
    
    $total_credit_value=0;
    $estimated_w=0;
    //echo "Memory: ".memory_get_usage(true) . "\n";
    foreach($transactions as $transaction){
      $transaction['code']=_trim($transaction['code']);
      
      if(preg_match('/credit|refund/i',$transaction['code'])){


	if(preg_match('/^Credit owed for order no\.\:\d{4,5}$/',$transaction['description'])){
	  $credit_parent_public_id=preg_replace('/[^\d]/','',$transaction['description']);
	  $credit_value=$transaction['credit'];
	  $credit_description=$transaction['description'];
	  $total_credit_value+=$credit_value;
	}elseif(preg_match('/^(Credit owed for order no\.\:|Credit for damage item|Refund for postage .paid by customer)$/i',$transaction['description'])){
	  $credit_parent_public_id='';
	  $credit_value=$transaction['credit'];
	  $credit_description=$transaction['description'];
	  $total_credit_value+=$credit_value;

	}else{
	  $credit_parent_public_id='';
	  $credit_value=$transaction['credit'];
	  $credit_description=$transaction['description'];
	  $total_credit_value+=$credit_value;


	}
	$_parent_key='NULL';
	$_parent_order_date='';
	if($credit_parent_public_id!=''){
	  $credit_parent=new Order('public id',$credit_parent_public_id);
	  if($credit_parent->id){
	    $_parent_key=$credit_parent->id;
	    $_parent_order_date=$credit_parent->data['Order Date'];
	  }
	}

	$credits[]=array(
			'parent_key'=>$_parent_key
			,'value'=>$credit_value
			,'description'=>$credit_description
			,'parent_date'=>$_parent_order_date
			 );
	
	//print_r($transaction);
	//print_r($credits);
	//exit;
	//	$credit[]=array()
	continue;
      }

      if(preg_match('/Freight|^frc-|^cxd-|^wsl$|Postage|^eye$|^\d$|2009promo/i',$transaction['code']))
	continue;
      if(preg_match('/difference in prices|Diff.in price for|difference in prices/i',$transaction['description']))
	continue;
    
      $__code=strtolower($transaction['code']);

      if($__code=='eo-st' or $__code=='mol-st' or  $__code=='jbb-st' or $__code=='lwheat-st' or  $__code=='jbb-st' 
	 or $__code=='scrub-st' or $__code=='eye-st' or $__code=='tbm-st' or $__code=='tbc-st' or $__code=='tbs-st'
	 or $__code=='gemd-st' or $__code=='cryc-st' or $__code=='gp-st'  or $__code=='dc-st'
	 ){
	continue;
      
      }
    




      $transaction['description']=preg_replace('/\s*\(\s*replacements?\s*\)\s*$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\s*(\-|\/)\s*replacements?\s*$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\s*(\-|\/)\s*SHOWROOM\s*$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\s*(\-|\/)\s*to.follow\/?\s*$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\s*(\-|\/)\s*missing\s*$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\/missed off prev.order$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\(missed off on last order\)$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\/from prev order$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\s*\(owed from prev order\)$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\s*\/prev order$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/\s*\-from prev order$/i','',$transaction['description']);
      $transaction['description']=preg_replace('/TO FOLLOW$/','',$transaction['description']);

      if(preg_match('/^sg\-$|^SG\-mix$|^sg-xx$/i',$transaction['code']) ){
	$transaction['code']='SG-mix';
	$transaction['description']='Simmering Granules Mixed Box';
      }
      if(preg_match('/SG-Y2/i',$transaction['code'])   and preg_match('/mix/i',$transaction['description'])){
	$transaction['code']='SG-mix';
	$transaction['description']='Simmering Granules Mixed Box';
      }
      if(preg_match('/sg-bn/i',$transaction['code']) ){
	$transaction['code']='SG-BN';
	$transaction['description']='Simmering Granules Mixed Box';
      }
      if(preg_match('/^sg$/i',$transaction['code']) and preg_match('/^(Mixed Simmering Granules|Mixed Simmering Granuels|Random Mix Simmering Granules)$/i',$transaction['description']) ){
	$transaction['code']='SG-mix';
	$transaction['description']='Simmering Granules Mixed Box';
      }
      if(preg_match('/^(sg|salt)$/i',$transaction['code']) and preg_match('/25/i',$transaction['description']) ){
	$transaction['code']='SG';
	$transaction['description']='25Kg Hydrosoft Granular Salt';
      }
      if(preg_match('/^(salty)$/i',$transaction['code']) and preg_match('/25/i',$transaction['description']) ){
	$transaction['code']='SG';
      }
     
      if(preg_match('/^(salt|salt-xx|salt-11w|Salt-Misc)$/i',$transaction['code']) and preg_match('/fit/i',$transaction['description']) ){
	$transaction['code']='Salt-Fitting';
	$transaction['description']='Spare Fitting for Salt Lamp';
      }
 
     
      if((preg_match('/^(salt-11w)$/i',$transaction['code']) and preg_match('/^Wood Base|^Bases/i',$transaction['description'])) or preg_match('/Salt-11 bases/i',$transaction['code']) or  preg_match('/Black Base for Salt Lamp/i',$transaction['description']) ){
	$transaction['code']='Salt-Base';
	$transaction['description']='Spare Base for Salt Lamp';
      }
     
      if(preg_match('/^wsl-320$/i',$transaction['code'])){
	$transaction['description']='Two Tone Palm Wax Candles Sml';
      }
      if(preg_match('/^wsl-631$/i',$transaction['code'])){
	$transaction['description']='Pewter Pegasus & Ball with LED';
      }

    

      if(preg_match('/^wsl-848$/i',$transaction['code'])   and preg_match('/wsl-848, simple message candle/i',$transaction['description'])){
	$transaction['description']='Simple Message Candle 3x6';
	$transaction['code']='wsl-877';
      }

      if(preg_match('/^bot-01$/i',$transaction['code'])   and preg_match('/10ml Amber Bottles.*tamper.*ap/i',$transaction['description']))
	$transaction['description']='10ml Amber Bottles & Tamper Proof Caps';
      if(preg_match('/^81992$/i',$transaction['code'])   and preg_match('/Amber Bottles/i',$transaction['description']))
	$transaction['code']='Bot-02';
 


      if(preg_match('/^bot-01$/i',$transaction['code'])   and preg_match('/10ml Amber Bottles.*only/i',$transaction['description']))
	$transaction['description']='10ml Amber Bottles Only';
      if(preg_match('/^bot-01$/i',$transaction['code'])   and preg_match('/10ml Amber Bottles.already supplied/i',$transaction['description']))
	$transaction['description']='10ml Amber Bottles';
      if(preg_match('/^bag-07$/i',$transaction['code'])   and preg_match('/^Mini Bag.*mix|^Mixed Mini Bag$/i',$transaction['description']))
	$transaction['description']='Mini Bag - Mix';
      if(preg_match('/^bag-07$/i',$transaction['code'])   and preg_match('/Mini Bag .replacement/i',$transaction['description']))
	$transaction['description']='Mini Bag';
      if(preg_match('/^bag-07$/i',$transaction['code'])   and preg_match('/Mini Organza Bags Mixed|Organza Mini Bag . Mix/i',$transaction['description']))
	$transaction['description']='Organza Mini Bag - Mixed';
      if(preg_match('/^bag-02$/i',$transaction['code']))
	$transaction['description']='Organza Bags';
      if(preg_match('/^bag-02a$/i',$transaction['code'])   and preg_match('/gold/i',$transaction['description']))
	$transaction['description']='Organza Bag - Gold';
      if(preg_match('/^bag-02a$/i',$transaction['code'])   and preg_match('/misc|mix|showroom/i',$transaction['description']))
	$transaction['description']='Organza Bag - Mix';
      if(preg_match('/^bag-07a$/i',$transaction['code'])   and preg_match('/misc|mix|showroom/i',$transaction['description']))
	$transaction['description']='Organza Mini Bag - Mix';
      if(preg_match('/^bag$/i',$transaction['code']) )
	$transaction['description']='Organza Bag - Mix';
      if(preg_match('/^eid-04$/i',$transaction['code']) )
	$transaction['description']='Nag Champa 15g';
      if(preg_match('/^ish-13$/i',$transaction['code'])   and preg_match('/Smoke Boxes Natural/i',$transaction['description'])   )
	$transaction['description']='Smoke Boxes Natural';
      if(preg_match('/^asoap-09$/i',$transaction['code'])   and preg_match('/maychang-orange tint old showrooms/i',$transaction['description'])   )
	$transaction['description']='May Chang - Orange -EO Soap Loaf';
      if(preg_match('/^asoap-02$/i',$transaction['code'])   and preg_match('/old showrooms/i',$transaction['description'])   )
	$transaction['description']='Tea Tree - Green -EO Soap Loaf';

      if(preg_match('/^wsl-1039$/i',$transaction['code'])     )
	$transaction['description']='Arty Coffee Twist Candle 24cm';
      if(preg_match('/^joie-01$/i',$transaction['code'])   and preg_match('/assorted/i',$transaction['description'])    )
	$transaction['description']='Joie Boxed - Assorted';
      if(preg_match('/^wsl-01$/i',$transaction['code'])   and preg_match('/Mixed packs of Incense.Shipp.cost covered./i',$transaction['description'])    )
	$transaction['description']='Mixed packs of Incense';
      if(preg_match('/^gp-01$/i',$transaction['code'])   and preg_match('/^(Glass Pebbles Assorted|Glass Pebbles mixed colours|Glass Pebbles-Mixed)$/i',$transaction['description'])    )
	$transaction['description']='Glass Pebbles mixed colours';

      if(preg_match('/^HemM-01$/i',$transaction['code'])   and preg_match('/Pair of Hermatite Magnets/i',$transaction['description'])    )
	$transaction['description']='Pair of Hematite Magnets';

      if(preg_match('/Box of 6 Nightlights -Flower Garden/i',$transaction['description'])    )
	$transaction['description']='Box of 6 Nightlights - Flower Garden';

 
      if(preg_match('/Grip Seal Bags 4 x 5.5 inch/i',$transaction['description'])    )
	$transaction['description']='Grip Seal Bags 4x5.5inch';

      if(preg_match('/^FW-01$/i',$transaction['code'])  ){
	if(preg_match('/gift|alter/i',$transaction['description'])){
	  $transaction['code']='FW-04';
	}elseif(preg_match('/white/i',$transaction['description'])){
	  $transaction['code']='FW-02';
	  $transaction['description']='Promo Wine White';
	} elseif(preg_match('/rose/i',$transaction['description'])){
	  $transaction['code']='FW-03';
	  $transaction['description']='Promo Wine Rose';
	} elseif(preg_match('/red/i',$transaction['description'])){
	  $transaction['description']='Promo Wine Red';
	} elseif(preg_match('/Veuve/i',$transaction['description'])){
	  $transaction['description']='Veuve Clicquote Champagne';
	} elseif(preg_match('/champagne/i',$transaction['description'])){
	  $transaction['description']='Champagne';
	}
      }
      if(preg_match('/^FW-02$/i',$transaction['code'])  ){
	if(preg_match('/gift|alter/i',$transaction['description'])){
	  $transaction['code']='FW-04';
	}elseif(preg_match('/white/i',$transaction['description'])){
	  $transaction['code']='FW-02';
	  $transaction['description']='Promo Wine White';
	} elseif(preg_match('/rose/i',$transaction['description'])){
	  $transaction['code']='FW-03';
	  $transaction['description']='Promo Wine Rose';
	} elseif(preg_match('/red/i',$transaction['description'])){
	  $transaction['code']='FW-01';
	  $transaction['description']='Promo Wine Red';
	 
	} elseif(preg_match('/Veuve/i',$transaction['description'])){
	  $transaction['code']='FW-01';
	  $transaction['description']='Veuve Clicquote Champagne';
	} elseif(preg_match('/champagne/i',$transaction['description'])){
	  $transaction['code']='FW-01';
	  $transaction['description']='Champagne';
	}
      }
      if(preg_match('/^FW-03$/i',$transaction['code'])  ){
	if(preg_match('/gift|alter/i',$transaction['description'])){
	  $transaction['code']='FW-04';
	}elseif(preg_match('/white/i',$transaction['description'])){
	  $transaction['code']='FW-02';
	  $transaction['description']='Promo Wine White';
	} elseif(preg_match('/rose/i',$transaction['description'])){
	  $transaction['code']='FW-03';
	  $transaction['description']='Promo Wine Rose';
	} elseif(preg_match('/red/i',$transaction['description'])){
	  $transaction['code']='FW-01';
	  $transaction['description']='Promo Wine Red';
	 
	} elseif(preg_match('/Veuve/i',$transaction['description'])){
	  $transaction['code']='FW-01';
	  $transaction['description']='Veuve Clicquote Champagne';
	} elseif(preg_match('/champagne/i',$transaction['description'])){
	  $transaction['code']='FW-01';
	  $transaction['description']='Champagne';
	}
      }
    
      if(preg_match('/^FW-04$/i',$transaction['code'])  ){
	$transaction['description']=preg_replace('/^Alternative Gift\s*\/\s*/i','Alternative Gift to Wine: ',$transaction['description']);
	$transaction['description']=preg_replace('/^Gift\s*(\:|\-)\*/i','Alternative Gift to Wine: ',$transaction['description']);
	$transaction['description']=preg_replace('/^Alternative Gift to Wine(\-|\/)/i','Alternative Gift to Wine: ',$transaction['description']);
	$transaction['description']=preg_replace('/^Alternative Gift\s*(\:|\-)\s*/i','Alternative Gift to Wine: ',$transaction['description']);
	$transaction['description']=preg_replace('/^Alternative Gift to Wine\s*(\-)\*/i','Alternative Gift to Wine: ',$transaction['description']);
	$transaction['description']=preg_replace('/Alternative Gift to Wine (\:|\-)/i','Alternative Gift to Wine: ',$transaction['description']);
   
	if(preg_match('/sim|Alternative Gift to Wine. 1x sg mixed box|SG please|Mix SG/i',$transaction['description'])){
	  $transaction['description']='Alternative Gift to Wine: 1 box of simmering granules';
	}
	if(preg_match('/^(gift|Promo Alternative to wine|Alternative|Alternative Gift|Alternative Gift .from prev order)$|order/i',$transaction['description'])){
	  $transaction['description']='Alternative Gift to Wine';
	}



      }
      if(!is_numeric($transaction['units']))
	$transaction['units']=1;
      if($transaction['price']>0){
	$margin=$transaction['supplier_product_cost']*$transaction['units']/$transaction['price'];
	if($margin>1 or $margin<0.01){
	  $transaction['supplier_product_cost']=0.4*$transaction['price']/$transaction['units'];
	}
      }
      $supplier_product_cost=sprintf("%.4f",$transaction['supplier_product_cost']);
      // print_r($transaction);

    



      $transaction['supplier_product_code']=_trim($transaction['supplier_product_code']);
      $transaction['supplier_product_code']=preg_replace('/^\"\s*/','',$transaction['supplier_product_code']);
      $transaction['supplier_product_code']=preg_replace('/\s*\"$/','',$transaction['supplier_product_code']);


      if(preg_match('/\d+ or more|\d|0.10000007|0.050000038|0.150000076|0.8000006103|1.100000610|1.16666666|1.650001220|1.80000122070/i',$transaction['supplier_product_code']))
	$transaction['supplier_product_code']='';
      if(preg_match('/^(\?|new|0.25|0.5|0.8|8.0600048828125|0.8000006103|01 Glass Jewellery Box|1|0.1|0.05|1.5625|10|\d{1,2}\s?\+\s?\d{1,2}\%)$/i',$transaction['supplier_product_code']))
	$transaction['supplier_product_code']='';
      if($transaction['supplier_product_code']=='same')
	$transaction['supplier_product_code']=$transaction['code'];
	
	
	

      if($transaction['supplier_product_code']=='')
	$transaction['supplier_product_code']='?'.$transaction['code'];
    
      if($transaction['supplier_product_code']=='SSK-452A' and $transaction['supplier_code']=='Smen')
	$transaction['supplier_product_code']='SSK-452A bis';

      if(preg_match('/^(StoneM|Smen)$/i',$transaction['supplier_code'])){
	$transaction['supplier_code']='StoneM';
      }

      if(preg_match('/Ackerman|Ackerrman|Akerman/i',$transaction['supplier_code'])){
	$transaction['supplier_code']='Ackerman';
      }

      if( preg_match('/\d/',$transaction['supplier_code']) ){
	$transaction['supplier_code'] ='';
	$supplier_product_cost='';
      }
      if(preg_match('/^(SG|FO|EO|PS|BO)\-/i',$transaction['code']))
	$transaction['supplier_code'] ='AW';
      if($transaction['supplier_code']=='AW')
	$transaction['supplier_product_code']=$transaction['code'];
      if($transaction['supplier_code']=='' or preg_match('/\d/',$transaction['supplier_code']) )
	$transaction['supplier_code']='Unknown';
      $unit_type='Piece';
      $description=_trim($transaction['description']);
      $description=str_replace("\\\"","\"",$description);
      if(preg_match('/Joie/i',$description) and preg_match('/abpx-01/i',$transaction['code']))
	$description='2 boxes joie (replacement due out of stock)';
    

      
      //print_r($transaction);

      if(is_numeric($transaction['w'])){

	if($transaction['w']<0.001 and $transaction['w']>0)
	  $w=0.001*$transaction['units'];
	else
	  $w=sprintf("%.3f",$transaction['w']*$transaction['units']);
      }else
	$w='';
      $transaction['supplier_product_code']=_trim($transaction['supplier_product_code']);


      if($transaction['supplier_product_code']=='' or $transaction['supplier_product_code']=='0')
	$sup_prod_code='?'._trim($transaction['code']);
      else
	$sup_prod_code=$transaction['supplier_product_code'];


      if(preg_match('/GP-\d{2}/i',$transaction['code']) and $transaction['units']==1200){
	$transaction['units']=1;
	$w=6;
	$supplier_product_cost=4.4500;
	$transaction['rrp']=60;
      }	



      if($transaction['units']=='' OR $transaction['units']<=0)
	$transaction['units']=1;
    
      if(!is_numeric($transaction['price']) or $transaction['price']<=0){
	//       print "Price Zero ".$transaction['code']."\n";
	$transaction['price']=0;
      }

      if(!is_numeric($supplier_product_cost)  or $supplier_product_cost<=0 ){
       
	if(preg_match('/Catalogue/i',$description)){
	  $supplier_product_cost=.25;
	}elseif($transaction['price']==0){
	  $supplier_product_cost=.20;
	}else{
	  $supplier_product_cost=0.4*$transaction['price']/$transaction['units'];
	  //print_r($transaction);
	  //	 print $transaction['code']." assuming supplier cost of 40% $supplier_product_cost **\n";
	}
       
       
       
      }
    
     

      // print_r($transaction);

      $product_data=array(
			  'product code'=>_trim($transaction['code'])
			  ,'product name'=>$description
			  ,'product unit type'=>$unit_type
			  ,'product units per case'=>$transaction['units']
			  ,'product net weight'=>$w
			  ,'product gross weight'=>$w
			  ,'part gross weight'=>$w
			  ,'product rrp'=>sprintf("%.2f",$transaction['rrp']*$transaction['units'])
			  ,'product price'=>sprintf("%.2f",$transaction['price'])
			  ,'supplier code'=>_trim($transaction['supplier_code'])
			  ,'supplier name'=>_trim($transaction['supplier_code'])
			  ,'supplier product cost'=>$supplier_product_cost
			  ,'supplier product code'=>$sup_prod_code
			  ,'supplier product name'=>$description
			  ,'auto_add'=>true
			  ,'date'=>$date_order
			  ,'date2'=>$date2
			  );
   
      // print_r($product_data);
     
      $product=new Product('code-name-units-price',$product_data);

      //     "Ahh canto male pedict\n";
      if(!$product->id){
	print_r($product_data);
	print "Ahh canto male pedict\n";
	exit;
      }



      if($transaction['order']!=0){
      $products_data[]=array(
			     'product_id'=>$product->id
			     ,'Estimated Weight'=>$product->data['Product Gross Weight']*$transaction['order']
			     ,'qty'=>$transaction['order']
			     ,'gross_amount'=>$transaction['order']*$transaction['price']
			     ,'discount_amount'=>$transaction['order']*$transaction['price']*$transaction['discount']
			     ,'units_per_case'=>$product->data['Product Units Per Case']
			     );

      //      print_r($transaction);

      $net_amount=round(($transaction['order']-$transaction['reorder'])*$transaction['price']*(1-$transaction['discount']),2 );
      $gross_amount=round(($transaction['order']-$transaction['reorder'])*$transaction['price'],2);
      $net_discount=-$net_amount+$gross_amount;
      $data_invoice_transactions[]=array(
					 'product_id'=>$product->id
					 ,'invoice qty'=>$transaction['order']-$transaction['reorder']
					 ,'gross amount'=>$gross_amount
					 ,'discount amount'=>$net_discount
					 ,'current payment state'=>'Paid'



					 );		   
      // print_r($data_invoice_transactions);
      $estimated_w+=$product->data['Product Gross Weight']*($transaction['order']-$transaction['reorder']);
      //print "$estimated_w ".$product->data['Product Gross Weight']." ".($transaction['order']-$transaction['reorder'])."\n";
      $data_dn_transactions[]=array(
				    'product_id'=>$product->id
				    ,'Estimated Weight'=>$product->data['Product Gross Weight']*($transaction['order']-$transaction['reorder'])
				    ,'Product ID'=>$product->data['Product ID']
				    ,'Delivery Note Quantity'=>$transaction['order']-$transaction['reorder']
				    ,'Current Autorized to Sell Quantity'=>$transaction['order']
				    ,'Shipped Quantity'=>$transaction['order']-$transaction['reorder']
				    ,'No Shipped Due Out of Stock'=>$transaction['reorder']
				    ,'No Shipped Due No Authorized'=>0
				    ,'No Shipped Due Not Found'=>0
				    ,'No Shipped Due Other'=>0
				    ,'amount in'=>(($transaction['order']-$transaction['reorder'])*$transaction['price'])*(1-$transaction['discount'])
				    ,'given'=>0
				    ,'required'=>$transaction['order']
				    ,'pick_method'=>'historic'
				    ,'pick_method_data'=>array(
							       'supplier product key'=>$product->supplier_product_key,
							       'part sku'=>$product->part_sku,
							       'product part id'=>$product->product_part_id
							       )
				    );		   

      }
      if($transaction['bonus']>0){
	$products_data[]=array(
			       'product_id'=>$product->id
			       ,'qty'=>0
			       ,'gross_amount'=>0
			       ,'discount_amount'=>0
			       ,'Estimated Weight'=>0
			       ,'units_per_case'=>$product->data['Product Units Per Case']
			       );
	$data_invoice_transactions[]=array(
					   'product_id'=>$product->id
					   ,'invoice qty'=>$transaction['bonus']
					   ,'gross amount'=>($transaction['bonus'])*$transaction['price']
					   ,'discount amount'=>($transaction['bonus'])*$transaction['price']
					   ,'current payment state'=>'No Applicable'
					   );		   
    
    $estimated_w+=$product->data['Product Gross Weight']*$transaction['bonus'];
	$data_dn_transactions[]=array(
				      'product_id'=>$product->id
				      ,'Product ID'=>$product->data['Product ID']
				      ,'Delivery Note Quantity'=>$transaction['bonus']
				      ,'Current Autorized to Sell Quantity'=>$transaction['bonus']
				      ,'Shipped Quantity'=>$transaction['bonus']
				      ,'No Shipped Due Out of Stock'=>0
				      ,'No Shipped Due No Authorized'=>0
				      ,'No Shipped Due Not Found'=>0
				      ,'No Shipped Due Other'=>0
				      ,'Estimated Weight'=>$product->data['Product Gross Weight']*($transaction['bonus'])
				      ,'amount in'=>0
				      ,'given'=>$transaction['bonus']
				      ,'required'=>0
				      ,'pick_method'=>'historic'
				      ,'pick_method_data'=>array(
								 'supplier product key'=>$product->supplier_product_key,
								 'part sku'=>$product->part_sku,
								 'product part id'=>$product->product_part_id
								 )
				  
				      );		   


      

      }

    }
    //echo "Memory: ".memory_get_usage(true) . "\n";


  

    $data['type']='direct_data_injection';
    $data['products']=$products_data;
    $data['cdata']=$customer_data;
    // $data['metadata_id']=$order_data_id;
    $data['tax_rate']=.15;
    if(strtotime($date_order)<strtotime('2008-11-01'))
      $data['tax_rate']=.175;
    // print_r($products_data);
    // exit;

    //Tipo order
    // 1 DELIVERY NOTE
    // 2 INVOICE
    // 3 CANCEL
    // 4 SAMPLE
    // 5 donation
    // 6 REPLACEMENT
    // 7 MISSING
    // 8 follow
    // 9 refund
    // 10 crdit
    // 11 quote
  

       if($update){
	 print "Updated ";

	 $sql=sprintf("select `Order Key`  from `Order Dimension`  where `Order Original Metadata`=%d  ",$order_data_id);
	 
	 $result_test=mysql_query($sql);
	 while($row_test=mysql_fetch_array($result_test, MYSQL_ASSOC)){
	   
	   $sql=sprintf("delete from `History Dimension` where `Direct Object Key`=%d and `Direct Object`='Sale'   ",$row_test['Order Key']);
	   if(!mysql_query($sql))
	     print "$sql Warning can no delete oldhidtfgf";
	   
	 };

	 
	 
	 $sql=sprintf("delete from `Order No Product Transaction Fact` where `Metadata`=%d",$order_data_id);
	 if(!mysql_query($sql))
	   print "$sql Warning can no delete old order";

	 //delete things
	 $sql=sprintf("delete from `Order Dimension` where `Order Original Metadata`=%d",$order_data_id);
	 //	 print $sql;

	 if(!mysql_query($sql))
	   print "$sql Warning can no delete old order";
	 $sql=sprintf("delete from `Invoice Dimension` where `Invoice Metadata`=%d",$order_data_id);
	 if(!mysql_query($sql))
	   print "$sql Warning can no delete old inv";
	 $sql=sprintf("delete from `Delivery Note Dimension` where `Delivery Note Metadata`=%d",$order_data_id);
	 if(!mysql_query($sql))
	   print "$sql Warning can no delete old dn";
	 $sql=sprintf("delete from `Order Transaction Fact` where `Metadata`=%d",$order_data_id);
	 if(!mysql_query($sql))
	   print "$sql Warning can no delete tf";
 $sql=sprintf("delete from `Inventory Transaction Fact` where `Metadata`=%d and `Inventory Transaction Type`='Sale'   ",$order_data_id);
	 if(!mysql_query($sql))
	   print "$sql Warning can no delete old inv";
	 


	 
	 $sql=sprintf("delete from `Order No Product Transaction Fact` where `Metadata` ",$order_data_id);
	 if(!mysql_query($sql))
	   print "$sql Warning can no delete oldhidt nio prod";
	
       }
       
       print "$tipo_order\r";
       
       $sales_rep_data=get_user_id($header_data['takenby'],true,'&view=processed');
       $data['Order XHTML Sale Reps']=$sales_rep_data['xhtml'];
       $data['Order Sale Reps IDs']=$sales_rep_data['id'];
    if($tipo_order==2 or $tipo_order==1  or $tipo_order==4 or $tipo_order==5 or   $tipo_order==3   )  {
      //print_r($data);
    



      
      if($tipo_order==1 or $tipo_order==2 or  $tipo_order==3)
	$data['Order Type']='Order';
      else if($tipo_order==4)
	$data['Order Type']='Sample';
      else if($tipo_order==5)
	$data['Order Type']='Donation';

     
      $data['store_id']=1;
      //      print_r($data);
      $order= new Order('new',$data);


      if($tipo_order==2){
	$payment_method=parse_payment_method($header_data['pay_method']);
	
	if($header_data['total_net']!=0)
	  $tax_rate=$header_data['tax1']/$header_data['total_net'];
	else
	  $tax_rate=$data['tax_rate'];
	    
	$data_invoice=array(
			    'Invoice Date'=>$date_inv
			    ,'Invoice Public ID'=>$header_data['order_num']
			    ,'Invoice File As'=>$header_data['order_num']
			    ,'Invoice Main Payment Method'=>$payment_method
			    ,'Invoice Multiple Payment Methods'=>0
			    ,'Invoice Gross Shipping Amount'=>round($header_data['shipping'],2)
			    ,'Invoice Gross Charges Amount'=>round($header_data['charges'],2)
			    ,'Invoice Total Tax Amount'=>round($header_data['tax1'],2)
			    ,'Invoice Refund Amount'=>$total_credit_value
			    ,'Invoice Total Tax Refund Amount'=>$tax_rate*$total_credit_value
			    ,'Invoice Total Amount'=>round($header_data['total_topay'],2)
			    ,'tax_rate'=>$tax_rate
			    ,'Invoice Has Been Paid In Full'=>'Yes'
			    ,'Invoice Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value
			    ,'Invoice XHTML Processed By'=>_('Unknown')
			    ,'Invoice XHTML Charged By'=>_('Unknown')
			    ,'Invoice Processed By Key'=>''
			    ,'Invoice Charged By Key'=>''
			    ,'Invoice Total Adjust Amount'=>round($header_data['total_topay'],2)-round($header_data['tax1'],2)-round($header_data['total_net'],2)
			    );
	
	//print_r($header_data);

	if(!is_numeric($header_data['weight']))
	  $weight=$estimated_w;
	else
	  $weight=$header_data['weight'];


	$picker_data=get_user_id($header_data['pickedby'],true,'&view=picks');
	$packer_data=get_user_id($header_data['packedby'],true,'&view=packs');
	$order_type=$data['Order Type'];
	$data_dn=array(
		       'Delivery Note Date'=>$date_inv
		       ,'Delivery Note ID'=>$header_data['order_num']
		       ,'Delivery Note File As'=>$header_data['order_num']
		       ,'Delivery Note Weight'=>$weight
		       ,'Delivery Note XHTML Pickers'=>$picker_data['xhtml']
		       ,'Delivery Note Number Pickers'=>count($picker_data['id'])
		       ,'Delivery Note Pickers IDs'=>$picker_data['id']
		       ,'Delivery Note XHTML Packers'=>$packer_data['xhtml']
		       ,'Delivery Note Number Packers'=>count($packer_data['id'])
		       ,'Delivery Note Packers IDs'=>$packer_data['id']
		       ,'Delivery Note Type'=>$order_type
		       ,'Delivery Note Title'=>_('Delivery Note for').' '.$order_type.' '.$header_data['order_num']

		       );
	
	$order->create_dn_simple($data_dn,$data_dn_transactions);
	
	if($total_credit_value==0 and $header_data['total_topay']==0){
	  print "Zero value order ".$header_data['order_num']." \n";
	  $order->no_payment_applicable();
	  $order->load('totals');
	}else{
	  $order->create_invoice_simple($data_invoice,$data_invoice_transactions);
	
	  
	foreach($credits as $credit){
	  
	  //	  print_r($header_data);
	  $sql=sprintf("insert into `Order No Product Transaction Fact` values  (%s,%s,%s,%s,'Credit',%s,%.2f,%.2f,%s)"
		       ,prepare_mysql($credit['parent_date'])
		       ,prepare_mysql($order->data['Invoice Date'])
		       ,$credit['parent_key']
		       ,prepare_mysql($order->data['Invoice Key'])
		       ,prepare_mysql($credit['description'])
		       ,$credit['value']
		       ,$tax_rate*$credit['value']
		       ,$order_data_id
		       );

	  if(!mysql_query($sql))
	    exit("$sql\n error can not inser orde rno pro trns");

	  
	  if($credit['parent_key']!='NULL'){
	    $parent=new Order($credit['parent_key']);
	    $parent->load('totals');
	  //   print "******************************************\n$sql\n";
// 	    exit;
	  }
	  
	}

	$order->load('totals');

	}



      }else if($tipo_order==8 ){

// 	$data['Order Type']='Order';
// 	$data['store_id']=1;
	
// 	exit("to follow");



    }else if($tipo_order==4 or $tipo_order==5 ){
	if($header_data['total_net']!=0)
	  $tax_rate=$header_data['tax1']/$header_data['total_net'];
	else
	  $tax_rate=$data['tax_rate'];
	if(!is_numeric($header_data['weight']))
	  $weight=$estimated_w;
	else
	  $weight=$header_data['weight'];
	

	$picker_data=get_user_id($header_data['pickedby'],true,'&view=picks');
	$packer_data=get_user_id($header_data['packedby'],true,'&view=packs');

	$order_type=$data['Order Type'];
	  
	$data_dn=array(
		       'Delivery Note Date'=>$date2
		       ,'Delivery Note ID'=>$header_data['order_num']
		       ,'Delivery Note File As'=>$header_data['order_num']
		       ,'Delivery Note Weight'=>$weight
		       ,'Delivery Note XHTML Pickers'=>$picker_data['xhtml']
		       ,'Delivery Note Number Pickers'=>count($picker_data['id'])
		       ,'Delivery Note Pickers IDs'=>$picker_data['id']
		       ,'Delivery Note XHTML Packers'=>$packer_data['xhtml']
		       ,'Delivery Note Number Packers'=>count($packer_data['id'])
		       ,'Delivery Note Packers IDs'=>$packer_data['id']
		       ,'tax_rate'=>$tax_rate
		       ,'Invoice Has Been Paid In Full'=>'Yes'
		       ,'Invoice Net Amount'=>$header_data['total_items_charge_value']-$total_credit_value
		       ,'Delivery Note Type'=>$order_type
		       ,'Delivery Note Title'=>_('Delivery Note for').' '.$order_type.' '.$header_data['order_num']

		       );
	  

	  $order->create_dn_simple($data_dn,$data_dn_transactions);
	  
	  if($header_data['total_topay']>0){
	  $payment_method=parse_payment_method($header_data['pay_method']);
	  
	  $data_invoice=array(
			      'Invoice Date'=>$date2
			      ,'Invoice Public ID'=>$header_data['order_num']
			      ,'Invoice File As'=>$header_data['order_num']
			      ,'Invoice Main Payment Method'=>$payment_method
			      ,'Invoice Multiple Payment Methods'=>0
			      ,'Invoice Gross Shipping Amount'=>round($header_data['shipping'],2)
			      ,'Invoice Gross Charges Amount'=>round($header_data['charges'],2)
			      ,'tax_rate'=>$tax_rate
			      ,'tax_rate'=>$tax_rate
			      ,'Invoice Has Been Paid In Full'=>'Yes'
			      ,'Invoice Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value
,'Invoice Total Tax Amount'=>$header_data['tax1']
			    ,'Invoice Refund Amount'=>$total_credit_value
			    ,'Invoice Total Tax Refund Amount'=>$tax_rate*$total_credit_value
			      ,'Invoice Total Amount'=>$header_data['total_topay']
			       ,'Invoice XHTML Processed By'=>_('Unknown')
			    ,'Invoice XHTML Charged By'=>_('Unknown')
			    ,'Invoice Processed By Key'=>0
			    ,'Invoice Charged By Key'=>0
			      );
	  $order->create_invoice_simple($data_invoice,$data_invoice_transactions);
	  }else{

	    $order->no_payment_applicable();
	  }



      }else if($tipo_order==3){
	  $order->cancel();

      }
      
      
      $sql="update orders_data.orders set last_transcribed=NOW() where id=".$order_data_id;
      mysql_query($sql);
    }elseif($tipo_order==9 ){




      $order=new Order('public_id',$parent_order_id);
      if(!$order->id){

	//	print "Unknown parent";
	// create new invoice (negative)(no deliver note changes noting)
	
	$data['ghost_order']=true;
	$data['Order Type']='Order';
	$data['store_id']=1;
	$order= new Order('new',$data);
      }else{
	$customer=new Customer($order->data['Order Customer Key']);
	$order->xhtml_billing_address=$customer->get('Customer Main XHTML Address');

      }

      $payment_method=parse_payment_method($header_data['pay_method']);
      
      if($header_data['total_net']!=0)
	  $tax_rate=$header_data['tax1']/$header_data['total_net'];
	else
	  $tax_rate=$data['tax_rate'];
	    
	$factor=1.0;
	if($header_data['total_topay']>0)
	  $factor=-1.0;
	//print_r($header_data);
	$data_invoice=array(
			    'Invoice Date'=>$date_inv
			    ,'Invoice Public ID'=>$header_data['order_num']
			    ,'Invoice File As'=>$header_data['order_num']
			    ,'Invoice Main Payment Method'=>$payment_method
			    ,'Invoice Multiple Payment Methods'=>0
			    ,'Invoice Gross Shipping Amount'=>0
			    ,'Invoice Gross Charges Amount'=>0
			    ,'Invoice Total Tax Amount'=>$header_data['tax1']*$factor
			    ,'Invoice Refund Amount'=>$header_data['total_net']*$factor
			    ,'Invoice Total Tax Refund Amount'=>$header_data['tax1']*$factor
			    ,'Invoice Total Amount'=>$header_data['total_topay']*$factor
			    ,'tax_rate'=>$tax_rate
			    ,'Invoice Has Been Paid In Full'=>'Yes'
			    ,'Invoice Net Amount'=>0
			     ,'Invoice XHTML Processed By'=>_('Unknown')
			    ,'Invoice XHTML Charged By'=>_('Unknown')
			    ,'Invoice Processed By Key'=>0
			    ,'Invoice Charged By Key'=>0
			    );
	//print_r($data_invoice);

	$data_refund_transactions=array();
	$sum_net=0;
	$sum_tax=0;

	if(is_numeric($header_data['shipping']) and $header_data['shipping']!=0){
	$data_refund_transactions[]=array(
					    'Transaction Net Amount'=>$header_data['shipping']*$factor,
					    'Description'=>_('Refund for Shipping')
					    ,'Transaction Tax Amount'=>$header_data['shipping']*$factor*$tax_rate
					    
					    );

	 $sum_net+=$header_data['shipping']*$factor;
	 $sum_tax+=$header_data['shipping']*$factor*$tax_rate;

	}
	if(is_numeric($header_data['charges']) and $header_data['charges']!=0){
	$data_refund_transactions[]=array(
					    'Transaction Net Amount'=>$header_data['charges']*$factor,
					    'Description'=>_('Refund for Charges')
					    ,'Transaction Tax Amount'=>$header_data['charges']*$factor*$tax_rate
					    
					    );
	
	$sum_net+=$header_data['charges']*$factor;
	$sum_tax+=$header_data['charges']*$factor*$tax_rate;
	}
	


	foreach($data_invoice_transactions as $key=>$data){
	  $product=new Product($data_invoice_transactions[$key]['product_id']);
	  if($product->id){
	    $description=_('Refund for')." ".$data_invoice_transactions[$key]['invoice qty']." ".$product->data['Product Code'] ;
	  }else
	    $description=_('Other Redunds');
	  
	  $net=($data_invoice_transactions[$key]['gross amount']-$data_invoice_transactions[$key]['discount amount'])*$factor;
	  $tax=($data_invoice_transactions[$key]['gross amount']-$data_invoice_transactions[$key]['discount amount'])*$factor*$tax_rate;
	  $data_refund_transactions[]=array(
					    'Transaction Net Amount'=>$net
					    ,'Description'=>$description
					    ,'Transaction Tax Amount'=>$tax
					    
					    );
	  
	  $sum_net+=$net;
	  $sum_tax+=$tax;
	}

	foreach($credits as $credit){

	  $net=$credit['value']*$factor;
	  $tax=$credit['value']*$factor*$tax_rate;

	   $data_refund_transactions[]=array(
					    'Transaction Net Amount'=>$net
					    ,'Description'=>$credit['description']
					    ,'Transaction Tax Amount'=>$tax
					    
					    );

	  $sum_net+=$net;
	  $sum_tax+=$tax;
	}

	//	print $header_data['total_net']." ".$sum_net;
	
	$diff_net=($factor*$header_data['total_net'])-$sum_net;
	if(abs($diff_net)>0.01){
	  $data_refund_transactions[]=array(
					    'Transaction Net Amount'=>$diff_net,
					    'Description'=>_('Other Refunds')
					    ,'Transaction Tax Amount'=>$diff_net*$tax_rate
					    
					    );
	}
	
	
	$diff_tax=($factor*$header_data['tax1'])-$sum_tax-$diff_net*$tax_rate;
	if(abs($diff_tax)>0.01){
	  $data_refund_transactions[]=array(
					    'Transaction Net Amount'=>0,
					    'Description'=>_('Other Tax Refunds')
					    ,'Transaction Tax Amount'=>$diff_tax
					    
					    );
	}


	//		print_r($data_refund_transactions);
	$order->create_refund_simple($data_invoice,$data_refund_transactions);

	//print "STSRT----------\n\n\n";
	$order->load('totals');
	//	print "END------------\n\n\n";
	$sql="update orders_data.orders set last_transcribed=NOW() where id=".$order_data_id;
	mysql_query($sql);
      

      //      exit("refund");

    }elseif($tipo_order==6 or $tipo_order==7){
      if($tipo_order==6)
	$order_type='Replacement';
      else
	$order_type='Shortages';


      	if(!is_numeric($header_data['weight']))
	  $weight=$estimated_w;
	else
	  $weight=$header_data['weight'];


	$picker_data=get_user_id($header_data['pickedby'],true,'&view=picks');
	$packer_data=get_user_id($header_data['packedby'],true,'&view=packs');

// 	print_r($picker_data);

	$data_dn=array(
		       'Delivery Note Date'=>$date_inv
		       ,'Delivery Note ID'=>$header_data['order_num']
		       ,'Delivery Note Type'=>$order_type
		       ,'Delivery Note Title'=>$order_type.' '.$header_data['order_num']
		       ,'Delivery Note File As'=>$header_data['order_num']
		       ,'Delivery Note Weight'=>$weight
		       ,'Delivery Note XHTML Pickers'=>$picker_data['xhtml']
		       ,'Delivery Note Number Pickers'=>count($picker_data['id'])
		       ,'Delivery Note Pickers IDs'=>$picker_data['id']
		       ,'Delivery Note XHTML Packers'=>$packer_data['xhtml']
		       ,'Delivery Note Number Packers'=>count($packer_data['id'])
		       ,'Delivery Note Packers IDs'=>$packer_data['id']
		       ,'Delivery Note Metadata'=>$order_data_id
		       );
	




      $parent_order=new Order('public_id',$parent_order_id);
      if($parent_order->id){
	print("prevs order found\n");

	//	print_r($data_dn_transactions);
	$parent_order->load('items');

	
	$customer=new Customer($parent_order->data['Order Customer Key']);
	$parent_order->xhtml_billing_address=$customer->get('Customer Main XHTML Address');
	$parent_order->ship_to_key=$customer->get('Customer Last Ship To Key');
	$parent_order->data['Backlog Date']=$date_inv;
	if($tipo_order==6)
	  $data_dn['Delivery Note Title']=_('Replacents for Order').' '.$parent_order->data['Order Public ID'];
	else
	  $data_dn['Delivery Note Title']=_('Shotages for Order').' '.$parent_order->data['Order Public ID'];

	$parent_order->create_replacement_dn_simple($data_dn,$data_dn_transactions,$products_data,$order_type);

	//	print_r($parent_order->items);
      }else{
       
	$data['ghost_order']=true;
	$data['Order Type']='';
	$data['store_id']=1;
 
	$order= new Order('new',$data);
	
	
	$order->create_replacement_dn_simple($data_dn,$data_dn_transactions,$products_data,$order_type);
	
      
      }
    
      
      $sql="update orders_data.orders set last_transcribed=NOW() where id=".$order_data_id;
       mysql_query($sql);
    }


  }
 }

  
//  print_r($data);
//print "\n$tipo_order\n";
?>
