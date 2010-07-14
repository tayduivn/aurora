<?php
//include("../../external_libs/adminpro/adminpro_config.php");

include_once('../../app_files/db/dns.php');
include_once('../../class.Department.php');
include_once('../../class.Family.php');
include_once('../../class.Product.php');
include_once('../../class.Supplier.php');
include_once('../../class.Order.php');
include_once('../../class.Invoice.php');
include_once('../../class.DeliveryNote.php');
include_once('../../class.Email.php');
include_once('../../class.CurrencyExchange.php');
include_once('common_read_orders_functions.php');



$store_code='F';
$__currency_code='EUR';

$calculate_no_normal_every =500;
$to_update=array(
               'products'=>array(),
               'products_id'=>array(),
               'products_code'=>array(),
               'families'=>array(),
               'departments'=>array(),
               'stores'=>array(),
               'parts'=>array()
           );


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
date_default_timezone_set('UTC');
require_once '../../common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/timezone.php';
date_default_timezone_set(TIMEZONE) ;

include_once('../../set_locales.php');

require_once '../../conf/conf.php';
require('../../locale.php');
$_SESSION['locale_info'] = localeconv();


$_SESSION['lang']=1;





include_once('fr_local_map.php');
include_once('fr_map_order_functions.php');
$myconf['country_code']='FRA';
$myconf['country_2acode']='FR';

$myconf['home_id']='165';
$myconf['country_id']='165';

$software='Get_Orders_DB.php';
$version='V 1.0';




$Data_Audit_ETL_Software="$software $version";
srand(12341);

$store=new Store("code","FR");
$store_key=$store->id;

$dept_data=array(
               'Product Department Code'=>'ND',
               'Product Department Name'=>'Products Without Department',
               'Product Department Store Key'=>$store_key
           );

$dept_no_dept=new Department('find',$dept_data);
$dept_no_dept_key=$dept_no_dept->id;

$dept_data=array(
               'Product Department Code'=>'Promo',
               'Product Department Name'=>'Promotional Items',
               'Product Department Store Key'=>$store_key
           );
$dept_promo=new Department('find',$dept_data);
$dept_promo_key=$dept_promo->id;

$fam_data=array(
              'Product Family Code'=>'PND_FR',
              'Product Family Name'=>'Products Without Family',
              'Product Family Main Department Key'=>$dept_no_dept_key,
              'Product Family Store Key'=>$store_key,
              'Product Family Special Characteristic'=>'None'
          );

$fam_no_fam=new Family('find',$fam_data);
$fam_no_fam_key=$fam_no_fam->id;

$fam_data=array(
              'Product Family Code'=>'Promo_FR',
              'Product Family Name'=>'Promotional Items',
              'Product Family Main Department Key'=>$dept_promo_key,
              'Product Family Store Key'=>$store_key,
              'Product Family Special Characteristic'=>'None'
          );



$fam_promo=new Family('find',$fam_data);



$fam_no_fam_key=$fam_no_fam->id;
$fam_promo_key=$fam_promo->id;

$sql="select * from  fr_orders_data.orders  where deleted='Yes'  order by filename  ";
$res=mysql_query($sql);

while ($row2=mysql_fetch_array($res, MYSQL_ASSOC)) {
    $order_data_id=$row2['id'];
    //print "deleting\n ";

    $sql=sprintf("select `Order Key`  from `Order Dimension`  where `Order Original Metadata`=%s  ",prepare_mysql($store_code.$order_data_id));

    $result_test=mysql_query($sql);
    while ($row_test=mysql_fetch_array($result_test, MYSQL_ASSOC)) {

        $sql=sprintf("delete from `History Dimension` where `Direct Object Key`=%d and `Direct Object`='Sale'   ",$row_test['Order Key']);
        if (!mysql_query($sql))
            print "$sql Warning can no delete oldhidtfgf";

    };



    $sql=sprintf("delete from `Order No Product Transaction Fact` where `Metadata`=%s",prepare_mysql($store_code.$order_data_id));
    if (!mysql_query($sql))
        print "$sql Warning can no delete old order";

    //delete things
    $sql=sprintf("delete from `Order Dimension` where `Order Original Metadata`=%s",prepare_mysql($store_code.$order_data_id));
    //	 print $sql;

    if (!mysql_query($sql))
        print "$sql Warning can no delete old order";
    $sql=sprintf("delete from `Invoice Dimension` where `Invoice Metadata`=%s",prepare_mysql($store_code.$order_data_id));
    if (!mysql_query($sql))
        print "$sql Warning can no delete old inv";
    $sql=sprintf("delete from `Delivery Note Dimension` where `Delivery Note Metadata`=%s",prepare_mysql($store_code.$order_data_id));
    if (!mysql_query($sql))
        print "$sql Warning can no delete old dn";
    $sql=sprintf("delete from `Order Transaction Fact` where `Metadata`=%s",prepare_mysql($store_code.$order_data_id));
    if (!mysql_query($sql))
        print "$sql Warning can no delete tf";
    $sql=sprintf("delete from `Inventory Transaction Fact` where `Metadata`=%s and `Inventory Transaction Type`='Sale'   ",prepare_mysql($store_code.$order_data_id));
    if (!mysql_query($sql))
        print "$sql Warning can no delete old inv";




    $sql=sprintf("delete from `Order No Product Transaction Fact` where `Metadata`=%s ",prepare_mysql($store_code.$order_data_id));
    if (!mysql_query($sql))
        print "$sql Warning can no delete oldhidt nio prod";

}



$sql="select * from  fr_orders_data.orders  where   (last_transcribed is NULL  or last_read>last_transcribed) and deleted='No'   order by filename  ";
//$sql="select * from  fr_orders_data.orders where filename like '%refund.xls'   order by filename";
//$sql="select * from  fr_orders_data.orders  where filename like '/mnt/%FR1178.xls'  order by filename";


$contador=0;

$res=mysql_query($sql);


while ($row2=mysql_fetch_array($res, MYSQL_ASSOC)) {


    $sql="select * from fr_orders_data.data where id=".$row2['id'];
    //print "$sql\n";

    $result=mysql_query($sql);
    if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {


        //           echo "                                                          Memory: ".memory_get_usage(true) . "\n";

        $order_data_id=$row2['id'];
        $filename=$row2['filename'];
        $contador++;
        $total_credit_value=0;

        // check if it is already readed
        $update=false;
        $old_order_key=0;
        $sql=sprintf("select count(*) as num  from `Order Dimension`  where `Order Original Metadata`=%s  ",prepare_mysql($store_code.$order_data_id));

        $result_test=mysql_query($sql);
        if ($row_test=mysql_fetch_array($result_test, MYSQL_ASSOC)) {
            if ($row_test['num']==0) {
                print "NEW $contador $order_data_id $filename \n";
            } else {
                $update=true;
                print "UPD $contador $order_data_id $filename \n";
            }
        }






        $header=mb_unserialize($row['header']);
        $products=mb_unserialize($row['products']);




        $filename_number=str_replace('.xls','',str_replace($row2['directory'],'',$row2['filename']));
        $map_act=$_map_act;
        $map=$_map;
        $y_map=$_y_map;

    


        $prod_map=$y_map;


        list($act_data,$header_data)=read_header($header,$map_act,$y_map,$map,false);
        $header_data=filter_header($header_data);
        list($tipo_order,$parent_order_id,$header_data)=get_tipo_order($header_data['ltipo'],$header_data);

        //print_r($header_data);

        if (preg_match('/^FR\d{4,5}sh$/i',$filename_number)) {
            $tipo_order=7;
            $parent_order_id=preg_replace('/sh/i','',$filename_number);
        }
        if (preg_match('/^FR\d{4,5}sht$/i',$filename_number)) {
            $tipo_order=7;
            $parent_order_id=preg_replace('/sht/i','',$filename_number);
        }

        if (preg_match('/^FR\d{4,5}rpl$/i',$filename_number)) {
            $tipo_order=6;
            $parent_order_id=preg_replace('/rpl/i','',$filename_number);

        }
        if (preg_match('/^FR\d{4,5}r$|^FR\d{4,5}ref$|^FR\d{4,5}\s?refund$|^FR\d{4,5}rr$|^FR\d{4,5}ra$|^FR\d{4,5}r2$|^FR\d{4,5}\-2ref$|^FR\d{4,5}rfn$/i',$filename_number)) {
            $tipo_order=9;
            $parent_order_id=preg_replace('/r$|ref$|refund$|rr$|ra$|r2$|\-2ref$|rfn$/i','',$filename_number);


        }



        //if($tipo_order==2 or $tipo_order==1){
        //  print "\n";
        //  continue;
        // }

        list($date_index,$date_order,$date_inv)=get_dates($row2['timestamp'],$header_data,$tipo_order,true);


        $editor=array("Date"=>$date_order);

        if ($tipo_order==9) {
            if ( $date_inv=='NULL' or  strtotime($date_order)>strtotime($date_inv)) {
                $date_inv=$date_order;
            }
        }


        if ( $date_inv!='NULL' and  strtotime($date_order)>strtotime($date_inv)) {


            //$date2=date("Y-m-d H:i:s",strtotime($date_order.' +1 hour'));
            print "Warning (Fecha Factura anterior Fecha Orden) $filename $date_order  $date_inv\n  ".strtotime($date_order).' > '.strtotime($date_inv)."\n";
            $date_inv=date("Y-m-d H:i:s",strtotime($date_order.' +1 hour'));

            // print "new date: ".$date2."\n";

        }


        if ($date_order=='')
            $date_index2=$date_index;
        else
            $date_index2=$date_order;

        if ($tipo_order==2  or $tipo_order==6 or $tipo_order==7 or $tipo_order==9 ) {
            $date2=$date_inv;
        }
        elseif($tipo_order==4  or   $tipo_order==5 or    $tipo_order==8  )
        $date2=$date_index;
        else
            $date2=$date_order;

        $header_data['Order Main Source Type']='Unknown';
        $header_data['Delivery Note Dispatch Method']='Unknown';
        $header_data['staff sale key']=0;;
        $header_data['collection']='No';
        $header_data['shipper_code']='';
        $header_data['staff sale']='No';
        $header_data['showroom']='No';
        $header_data['staff sale name']='';



        if (!$header_data['notes']) {
            $header_data['notes']='';
        }
        if (!$header_data['notes2'] or preg_match('/^vat|Special Instructions$/i',_trim($header_data['notes2']))) {
            $header_data['notes2']='';
        }

        if (preg_match('/^(Int Freight|Intl Freight|Internation Freight|Intl FreightInternation Freight|International Frei.*|International Freigth|Internation Freight|Internatinal Freight|nternation(al)? Frei.*|Internationa freight|International|International freight|by sea)$/i',$header_data['notes']))
            $header_data['notes']='International Freight';

        //delete no data notes

        $header_data=is_to_be_collected($header_data);

        $header_data=is_shipping_supplier($header_data);
        $header_data=is_staff_sale($header_data);



        if (preg_match('/^(|International Freight)$/',$header_data['notes'])) {
            $header_data['notes']='';

        }
        //  print "N1: ".$header_data['notes']."\n";
        //  print "N2: ".$header_data['notes2']."\n\n";
        // }
        // if(!preg_match('/^(|0|\s*)$/',$header_data['notes2']))

        $header_data=get_tax_number($header_data);
        $header_data=get_customer_msg($header_data);

        if ($header_data['notes']!='' and $header_data['notes2']!='') {
            $header_data['notes2']=_trim($header_data['notes'].', '.$header_data['notes2']);
            $header_data['notes']='';
        }
        elseif($header_data['notes']!='') {
            $header_data['notes2']=$header_data['notes'];
            $header_data['notes']='';
        }

        $header_data=get_customer_msg($header_data);

        if (preg_match('/^(IE 9575910F|85 467 757 063|ie 7214743D|ES B92544691|IE-7251185|SE556670-257601|x5686842-t)$/',$header_data['notes2'])) {
            $data['tax_number']=$header_data['notes2'];
            $header_data['notes']='';
        }





        $transactions=read_products($products,$prod_map);
        unset($products);
        //   echo "Memory: ".memory_get_usage(true) . "x\n";
        //     echo "Memory: ".memory_get_usage() . "x\n";
        $_customer_data=setup_contact($act_data,$header_data,$date_index2);


        $customer_data=array();

        if (isset($header_data['tax_number']) and $header_data['tax_number']!='') {
            $customer_data['Customer Tax Number']=$header_data['tax_number'];
        }


        //    print_r($_customer_data);
        foreach($_customer_data as $_key =>$value) {
            $key=$_key;
            if ($_key=='type')
                $key=preg_replace('/^type$/','Customer Type',$_key);
            if ($_key=='contact_name')
                $key=preg_replace('/^contact_name$/','Customer Main Contact Name',$_key);
            if ($_key=='company_name')
                $key=preg_replace('/^company_name$/','Customer Company Name',$_key);
            if ($_key=='email')
                $key=preg_replace('/^email$/','Customer Main Plain Email',$_key);
            if ($_key=='telephone')
                $key=preg_replace('/^telephone$/','Customer Main Plain Telephone',$_key);
            if ($_key=='fax')
                $key=preg_replace('/^fax$/','Customer Main Plain FAX',$_key);
            if ($_key=='mobile')
                $key=preg_replace('/^mobile$/','Customer Main Plain Mobile',$_key);

            $customer_data[$key]=$value;

        }
        $customer_data['Customer Store Key']=3;
        if ($customer_data['Customer Type']=='Company')
            $customer_data['Customer Name']=$customer_data['Customer Company Name'];
        else
            $customer_data['Customer Name']=$customer_data['Customer Main Contact Name'];
        if (isset($_customer_data['address_data'])) {
            $customer_data['Customer Address Line 1']=$_customer_data['address_data']['address1'];
            $customer_data['Customer Address Line 2']=$_customer_data['address_data']['address2'];
            $customer_data['Customer Address Line 3']=$_customer_data['address_data']['address3'];
            $customer_data['Customer Address Town']=$_customer_data['address_data']['town'];
            $customer_data['Customer Address Postal Code']=$_customer_data['address_data']['postcode'];
            $customer_data['Customer Address Country Name']=$_customer_data['address_data']['country'];
            $customer_data['Customer Address Country First Division']=$_customer_data['address_data']['country_d1'];
            $customer_data['Customer Address Country Second Division']=$_customer_data['address_data']['country_d2'];
            unset($customer_data['address_data']);
        }
        $customer_data['Customer Delivery Address Link']='Contact';
        $customer_data['Customer First Contacted Date']=$date_order;

        $shipping_addresses=array();
        if (isset($_customer_data['address_data']) and $_customer_data['has_shipping']) {

            if (!is_same_address($_customer_data)) {
                $customer_data['Customer Delivery Address Link']='None';
            }


            $shipping_addresses['Address Line 1']=$_customer_data['shipping_data']['address1'];
            $shipping_addresses['Address Line 2']=$_customer_data['shipping_data']['address2'];
            $shipping_addresses['Address Line 3']=$_customer_data['shipping_data']['address3'];
            $shipping_addresses['Address Town']=$_customer_data['shipping_data']['town'];
            $shipping_addresses['Address Postal Code']=$_customer_data['shipping_data']['postcode'];
            $shipping_addresses['Address Country Name']=$_customer_data['shipping_data']['country'];
            $shipping_addresses['Address Country First Division']=$_customer_data['shipping_data']['country_d1'];
            $shipping_addresses['Address Country Second Division']=$_customer_data['shipping_data']['country_d2'];
            unset($customer_data['shipping_data']);
        }

        //  print_r($transactions);

        if (strtotime($date_order)>strtotime($date2)) {



            //$date2=date("Y-m-d H:i:s",strtotime($date_order.' +1 hour'));
            print "Warning (Fecha Factura anterior Fecha Orden) $filename $date_order  $date2 \n";
            $date2=date("Y-m-d H:i:s",strtotime($date_order.' +8 hour'));

            print "new date: ".$date2."\n";

        }

        if (strtotime($date_order)>strtotime('now')   ) {

            print "ERROR (Fecha en el futuro) $filename  $date_order   \n ";

            continue;
        }

        if (strtotime($date_order)<strtotime($myconf['data_from'])  ) {

            print "ERROR (Fecha sospechosamente muy  antigua) $filename $date_order \n";

            continue;
        }


        $extra_shipping=0;

        $data=array();
        $data['editor']=array('Date'=>$date_order);

        $data['order date']=$date_order;
        $data['order id']=$header_data['order_num'];
        $data['order customer message']=$header_data['notes2'];
        if ($data['order customer message']==0)
            $data['order customer message']='';



        $tmp_filename=preg_replace('/\/mnt\/z\/Orders-france\//',"\\\\\\networkspace1\\openshare\\Orders\\",$row2['filename']);
        $tmp_filename=preg_replace('/\//',"\\",$tmp_filename);

        $data['Order Original Data Filename']=$tmp_filename;
        $data['order original data mime type']='application/vnd.ms-excel';
        $data['order original data']='';
        $data['order original data source']='Excel File';

        $data['Order Original Metadata']=$store_code.$row2['id'];


        $products_data=array();
        $data_invoice_transactions=array();
        $data_dn_transactions=array();
        $data_bonus_transactions=array();

        $credits=array();

        $total_credit_value=0;
        $estimated_w=0;
        //echo "Memory: ".memory_get_usage(true) . "\n";
        foreach($transactions as $transaction) {
            $transaction['code']=_trim($transaction['code']);

            if (preg_match('/credit|refund/i',$transaction['code'])) {


                if (preg_match('/^Credit owed for order no\.\:\d{4,5}$/',$transaction['description'])) {
                    $credit_parent_public_id=preg_replace('/[^\d]/','',$transaction['description']);
                    $credit_value=$transaction['credit'];
                    $credit_description=$transaction['description'];
                    $total_credit_value+=$credit_value;
                }
                elseif(preg_match('/^(Credit owed for order no\.\:|Credit for damage item|Refund for postage .paid by customer)$/i',$transaction['description'])) {
                    $credit_parent_public_id='';
                    $credit_value=$transaction['credit'];
                    $credit_description=$transaction['description'];
                    $total_credit_value+=$credit_value;

                }
                else {
                    $credit_parent_public_id='';
                    $credit_value=$transaction['credit'];
                    $credit_description=$transaction['description'];
                    $total_credit_value+=$credit_value;


                }
                $_parent_key='NULL';
                $_parent_order_date='';
                if ($credit_parent_public_id!='') {
                    $credit_parent=new Order('public id',$credit_parent_public_id);
                    if ($credit_parent->id) {
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


            if (preg_match('/Sub Total/',$transaction['bonus'])  )
                continue;
            if (preg_match('/Freight|^frc-|Postage/i',$transaction['code'])) {

                $extra_shipping+=$transaction['price'];
                continue;

            }
            if (preg_match('/^cxd-|^wsl$|^eye$|^\d$|2009promo/i',$transaction['code']))
                continue;
            if (preg_match('/difference in prices|Diff.in price for|difference in prices/i',$transaction['description']))
                continue;

            $__code=strtolower($transaction['code']);

            if (  preg_match('/Bag-02Mx|Bag-04mx|Bag-05mx|Bag-06mix|Bag-07MX|Bag-12MX|Bag-13MX|FishP-Mix|IncIn-ST|IncB-St|LLP-ST|L\&P-ST|EO-XST|AWRP-ST/i',$__code) or    $__code=='eo-st' or $__code=='mol-st' or  $__code=='jbb-st' or $__code=='lwheat-st' or  $__code=='jbb-st'
                    or $__code=='scrub-st' or $__code=='eye-st' or $__code=='tbm-st' or $__code=='tbc-st' or $__code=='tbs-st'
                    or $__code=='gemd-st' or $__code=='cryc-st' or $__code=='gp-st'  or $__code=='dc-st'
               ) {
                continue;

            }



            $transaction['code']=preg_replace('/L\&P\-/','LLP-',$transaction['code']);



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

            if (preg_match('/^sg\-$|^SG\-mix$|^sg-xx$/i',$transaction['code']) ) {
                $transaction['code']='SG-mix';
                $transaction['description']='Simmering Granules Mixed Box';
            }
            if (preg_match('/SG-Y2/i',$transaction['code'])   and preg_match('/mix/i',$transaction['description'])) {
                $transaction['code']='SG-mix';
                $transaction['description']='Simmering Granules Mixed Box';
            }
            if (preg_match('/sg-bn/i',$transaction['code']) ) {
                $transaction['code']='SG-BN';
                $transaction['description']='Simmering Granules Mixed Box';
            }
            if (preg_match('/^sg$/i',$transaction['code']) and preg_match('/^(Mixed Simmering Granules|Mixed Simmering Granuels|Random Mix Simmering Granules)$/i',$transaction['description']) ) {
                $transaction['code']='SG-mix';
                $transaction['description']='Simmering Granules Mixed Box';
            }
            if (preg_match('/^(sg|salt)$/i',$transaction['code']) and preg_match('/25/i',$transaction['description']) ) {
                $transaction['code']='SG';
                $transaction['description']='25Kg Hydrosoft Granular Salt';
            }
            if (preg_match('/^(salty)$/i',$transaction['code']) and preg_match('/25/i',$transaction['description']) ) {
                $transaction['code']='SG';
            }

            if (preg_match('/^(salt|salt-xx|salt-11w|Salt-Misc)$/i',$transaction['code']) and preg_match('/fit/i',$transaction['description']) ) {
                $transaction['code']='Salt-Fitting';
                $transaction['description']='Spare Fitting for Salt Lamp';
            }


            if ((preg_match('/^(salt-11w)$/i',$transaction['code']) and preg_match('/^Wood Base|^Bases/i',$transaction['description'])) or preg_match('/Salt-11 bases/i',$transaction['code']) or  preg_match('/Black Base for Salt Lamp/i',$transaction['description']) ) {
                $transaction['code']='Salt-Base';
                $transaction['description']='Spare Base for Salt Lamp';
            }

            if (preg_match('/^wsl-320$/i',$transaction['code'])) {
                $transaction['description']='Two Tone Palm Wax Candles Sml';
            }
            if (preg_match('/^wsl-631$/i',$transaction['code'])) {
                $transaction['description']='Pewter Pegasus & Ball with LED';
            }



            if (preg_match('/^wsl-848$/i',$transaction['code'])   and preg_match('/wsl-848, simple message candle/i',$transaction['description'])) {
                $transaction['description']='Simple Message Candle 3x6';
                $transaction['code']='wsl-877';
            }

            if (preg_match('/^bot-01$/i',$transaction['code'])   and preg_match('/10ml Amber Bottles.*tamper.*ap/i',$transaction['description']))
                $transaction['description']='10ml Amber Bottles & Tamper Proof Caps';
            if (preg_match('/^81992$/i',$transaction['code'])   and preg_match('/Amber Bottles/i',$transaction['description']))
                $transaction['code']='Bot-02';



            if (preg_match('/^bot-01$/i',$transaction['code'])   and preg_match('/10ml Amber Bottles.*only/i',$transaction['description']))
                $transaction['description']='10ml Amber Bottles Only';
            if (preg_match('/^bot-01$/i',$transaction['code'])   and preg_match('/10ml Amber Bottles.already supplied/i',$transaction['description']))
                $transaction['description']='10ml Amber Bottles';
            if (preg_match('/^bag-07$/i',$transaction['code'])   and preg_match('/^Mini Bag.*mix|^Mixed Mini Bag$/i',$transaction['description']))
                $transaction['description']='Mini Bag - Mix';
            if (preg_match('/^bag-07$/i',$transaction['code'])   and preg_match('/Mini Bag .replacement/i',$transaction['description']))
                $transaction['description']='Mini Bag';
            if (preg_match('/^bag-07$/i',$transaction['code'])   and preg_match('/Mini Organza Bags Mixed|Organza Mini Bag . Mix/i',$transaction['description']))
                $transaction['description']='Organza Mini Bag - Mixed';
            if (preg_match('/^bag-02$/i',$transaction['code']))
                $transaction['description']='Organza Bags';
            if (preg_match('/^bag-02a$/i',$transaction['code'])   and preg_match('/gold/i',$transaction['description']))
                $transaction['description']='Organza Bag - Gold';
            if (preg_match('/^bag-02a$/i',$transaction['code'])   and preg_match('/misc|mix|showroom/i',$transaction['description']))
                $transaction['description']='Organza Bag - Mix';
            if (preg_match('/^bag-07a$/i',$transaction['code'])   and preg_match('/misc|mix|showroom/i',$transaction['description']))
                $transaction['description']='Organza Mini Bag - Mix';
            if (preg_match('/^bag$/i',$transaction['code']) )
                $transaction['description']='Organza Bag - Mix';
            if (preg_match('/^eid-04$/i',$transaction['code']) )
                $transaction['description']='Nag Champa 15g';
            if (preg_match('/^ish-13$/i',$transaction['code'])   and preg_match('/Smoke Boxes Natural/i',$transaction['description'])   )
                $transaction['description']='Smoke Boxes Natural';
            if (preg_match('/^asoap-09$/i',$transaction['code'])   and preg_match('/maychang-orange tint old showrooms/i',$transaction['description'])   )
                $transaction['description']='May Chang - Orange -EO Soap Loaf';
            if (preg_match('/^asoap-02$/i',$transaction['code'])   and preg_match('/old showrooms/i',$transaction['description'])   )
                $transaction['description']='Tea Tree - Green -EO Soap Loaf';

            if (preg_match('/^wsl-1039$/i',$transaction['code'])     )
                $transaction['description']='Arty Coffee Twist Candle 24cm';
            if (preg_match('/^joie-01$/i',$transaction['code'])   and preg_match('/assorted/i',$transaction['description'])    )
                $transaction['description']='Joie Boxed - Assorted';
            if (preg_match('/^wsl-01$/i',$transaction['code'])   and preg_match('/Mixed packs of Incense.Shipp.cost covered./i',$transaction['description'])    )
                $transaction['description']='Mixed packs of Incense';
            if (preg_match('/^gp-01$/i',$transaction['code'])   and preg_match('/^(Glass Pebbles Assorted|Glass Pebbles mixed colours|Glass Pebbles-Mixed)$/i',$transaction['description'])    )
                $transaction['description']='Glass Pebbles mixed colours';

            if (preg_match('/^HemM-01$/i',$transaction['code'])   and preg_match('/Pair of Hermatite Magnets/i',$transaction['description'])    )
                $transaction['description']='Pair of Hematite Magnets';

            if (preg_match('/Box of 6 Nightlights -Flower Garden/i',$transaction['description'])    )
                $transaction['description']='Box of 6 Nightlights - Flower Garden';


            if (preg_match('/Grip Seal Bags 4 x 5.5 inch/i',$transaction['description'])    )
                $transaction['description']='Grip Seal Bags 4x5.5inch';

            if (preg_match('/^FW-01$/i',$transaction['code'])  ) {
                if (preg_match('/gift|alter/i',$transaction['description'])) {
                    $transaction['code']='FW-04';
                }
                elseif(preg_match('/white/i',$transaction['description'])) {
                    $transaction['code']='FW-02';
                    $transaction['description']='Promo Wine White';
                }
                elseif(preg_match('/rose/i',$transaction['description'])) {
                    $transaction['code']='FW-03';
                    $transaction['description']='Promo Wine Rose';
                }
                elseif(preg_match('/red/i',$transaction['description'])) {
                    $transaction['description']='Promo Wine Red';
                }
                elseif(preg_match('/Veuve/i',$transaction['description'])) {
                    $transaction['description']='Veuve Clicquote Champagne';
                }
                elseif(preg_match('/champagne/i',$transaction['description'])) {
                    $transaction['description']='Champagne';
                }
            }
            if (preg_match('/^FW-02$/i',$transaction['code'])  ) {
                if (preg_match('/gift|alter/i',$transaction['description'])) {
                    $transaction['code']='FW-04';
                }
                elseif(preg_match('/white/i',$transaction['description'])) {
                    $transaction['code']='FW-02';
                    $transaction['description']='Promo Wine White';
                }
                elseif(preg_match('/rose/i',$transaction['description'])) {
                    $transaction['code']='FW-03';
                    $transaction['description']='Promo Wine Rose';
                }
                elseif(preg_match('/red/i',$transaction['description'])) {
                    $transaction['code']='FW-01';
                    $transaction['description']='Promo Wine Red';

                }
                elseif(preg_match('/Veuve/i',$transaction['description'])) {
                    $transaction['code']='FW-01';
                    $transaction['description']='Veuve Clicquote Champagne';
                }
                elseif(preg_match('/champagne/i',$transaction['description'])) {
                    $transaction['code']='FW-01';
                    $transaction['description']='Champagne';
                }
            }
            if (preg_match('/^FW-03$/i',$transaction['code'])  ) {
                if (preg_match('/gift|alter/i',$transaction['description'])) {
                    $transaction['code']='FW-04';
                }
                elseif(preg_match('/white/i',$transaction['description'])) {
                    $transaction['code']='FW-02';
                    $transaction['description']='Promo Wine White';
                }
                elseif(preg_match('/rose/i',$transaction['description'])) {
                    $transaction['code']='FW-03';
                    $transaction['description']='Promo Wine Rose';
                }
                elseif(preg_match('/red/i',$transaction['description'])) {
                    $transaction['code']='FW-01';
                    $transaction['description']='Promo Wine Red';

                }
                elseif(preg_match('/Veuve/i',$transaction['description'])) {
                    $transaction['code']='FW-01';
                    $transaction['description']='Veuve Clicquote Champagne';
                }
                elseif(preg_match('/champagne/i',$transaction['description'])) {
                    $transaction['code']='FW-01';
                    $transaction['description']='Champagne';
                }
            }

            if (preg_match('/^FW-04$/i',$transaction['code'])  ) {
                $transaction['description']=preg_replace('/^Alternative Gift\s*\/\s*/i','Alternative Gift to Wine: ',$transaction['description']);
                $transaction['description']=preg_replace('/^Gift\s*(\:|\-)\*/i','Alternative Gift to Wine: ',$transaction['description']);
                $transaction['description']=preg_replace('/^Alternative Gift to Wine(\-|\/)/i','Alternative Gift to Wine: ',$transaction['description']);
                $transaction['description']=preg_replace('/^Alternative Gift\s*(\:|\-)\s*/i','Alternative Gift to Wine: ',$transaction['description']);
                $transaction['description']=preg_replace('/^Alternative Gift to Wine\s*(\-)\*/i','Alternative Gift to Wine: ',$transaction['description']);
                $transaction['description']=preg_replace('/Alternative Gift to Wine (\:|\-)/i','Alternative Gift to Wine: ',$transaction['description']);

                if (preg_match('/sim|Alternative Gift to Wine. 1x sg mixed box|SG please|Mix SG/i',$transaction['description'])) {
                    $transaction['description']='Alternative Gift to Wine: 1 box of simmering granules';
                }
                if (preg_match('/^(gift|Promo Alternative to wine|Alternative|Alternative Gift|Alternative Gift .from prev order)$|order/i',$transaction['description'])) {
                    $transaction['description']='Alternative Gift to Wine';
                }



            }
            if (!is_numeric($transaction['units']))
                $transaction['units']=1;
            if ($transaction['price']>0) {
                $margin=$transaction['supplier_product_cost']*$transaction['units']/$transaction['price'];
                if ($margin>1 or $margin<0.01) {
                    $transaction['supplier_product_cost']=0.4*$transaction['price']/$transaction['units'];
                }
            }
            $supplier_product_cost=sprintf("%.4f",$transaction['supplier_product_cost']);
            // print_r($transaction);





            $transaction['supplier_product_code']=_trim($transaction['supplier_product_code']);
            $transaction['supplier_product_code']=preg_replace('/^\"\s*/','',$transaction['supplier_product_code']);
            $transaction['supplier_product_code']=preg_replace('/\s*\"$/','',$transaction['supplier_product_code']);


            if (preg_match('/\d+ or more|\d|0.10000007|0.050000038|0.150000076|0.8000006103|1.100000610|1.16666666|1.650001220|1.80000122070/i',$transaction['supplier_product_code']))
                $transaction['supplier_product_code']='';
            if (preg_match('/^(\?|new|0.25|0.5|0.8|8.0600048828125|0.8000006103|01 Glass Jewellery Box|1|0.1|0.05|1.5625|10|\d{1,2}\s?\+\s?\d{1,2}\%)$/i',$transaction['supplier_product_code']))
                $transaction['supplier_product_code']='';
            if ($transaction['supplier_product_code']=='same')
                $transaction['supplier_product_code']=$transaction['code'];




            if ($transaction['supplier_product_code']=='')
                $transaction['supplier_product_code']='?'.$transaction['code'];

            if ($transaction['supplier_product_code']=='SSK-452A' and $transaction['supplier_code']=='Smen')
                $transaction['supplier_product_code']='SSK-452A bis';

            if (preg_match('/^(StoneM|Smen)$/i',$transaction['supplier_code'])) {
                $transaction['supplier_code']='StoneM';
            }

            if (preg_match('/Ackerman|Ackerrman|Akerman/i',$transaction['supplier_code'])) {
                $transaction['supplier_code']='Ackerman';
            }

            if ( preg_match('/\d/',$transaction['supplier_code']) ) {
                $transaction['supplier_code'] ='';
                $supplier_product_cost='';
            }
            if (preg_match('/^(SG|FO|EO|PS|BO)\-/i',$transaction['code']))
                $transaction['supplier_code'] ='AW';
            if ($transaction['supplier_code']=='AW')
                $transaction['supplier_product_code']=$transaction['code'];
            if ($transaction['supplier_code']=='' or preg_match('/\d/',$transaction['supplier_code']) )
                $transaction['supplier_code']='Unknown';
            $unit_type='Piece';
            $description=_trim($transaction['description']);
            $description=str_replace("\\\"","\"",$description);
            if (preg_match('/Joie/i',$description) and preg_match('/abpx-01/i',$transaction['code']))
                $description='2 boxes joie (replacement due out of stock)';



            //print_r($transaction);

            if (is_numeric($transaction['w'])) {

                if ($transaction['w']<0.001 and $transaction['w']>0)
                    $w=0.001*$transaction['units'];
                else
                    $w=sprintf("%.3f",$transaction['w']*$transaction['units']);
            } else
                $w='';
            $transaction['supplier_product_code']=_trim($transaction['supplier_product_code']);


            if ($transaction['supplier_product_code']=='' or $transaction['supplier_product_code']=='0')
                $sup_prod_code='?'._trim($transaction['code']);
            else
                $sup_prod_code=$transaction['supplier_product_code'];


            if (preg_match('/GP-\d{2}/i',$transaction['code']) and $transaction['units']==1200) {
                $transaction['units']=1;
                $w=6;
                $supplier_product_cost=4.4500;
                $transaction['rrp']=60;
            }



            if ($transaction['units']=='' OR $transaction['units']<=0)
                $transaction['units']=1;

            if (!is_numeric($transaction['price']) or $transaction['price']<=0) {
                //       print "Price Zero ".$transaction['code']."\n";
                $transaction['price']=0;
            }

            if (!is_numeric($supplier_product_cost)  or $supplier_product_cost<=0 ) {

                if (preg_match('/Catalogue/i',$description)) {
                    $supplier_product_cost=.25;
                }
                elseif($transaction['price']==0) {
                    $supplier_product_cost=.20;
                }
                else {
                    $supplier_product_cost=0.4*$transaction['price']/$transaction['units'];
                    //print_r($transaction);
                    //	 print $transaction['code']." assuming supplier cost of 40% $supplier_product_cost **\n";
                }



            }




            $fam_key=$fam_no_fam_key;

	    
	    
            $dept_key=$dept_no_dept_key;
            if (preg_match('/^pi-|catalogue|^info|Mug-26x|OB-39x|SG-xMIXx|wsl-1275x|wsl-1474x|wsl-1474x|wsl-1479x|^FW-|^MFH-XX$|wsl-1513x|wsl-1487x|wsl-1636x|wsl-1637x/i',_trim($transaction['code']))) {
                $fam_key=$fam_promo_key;
                $dept_key=$dept_promo_key;
            }


            $__code=preg_split('/-/',_trim($transaction['code']));
            $__code=$__code[0];
            $sql=sprintf('select * from `Product Family Dimension` where `Product Family Store Key`=%d and `Product Family Code`=%s'
                         ,$store_key
                         ,prepare_mysql($__code));
            $result=mysql_query($sql);
            if ( ($__row=mysql_fetch_array($result, MYSQL_ASSOC))) {
                $fam_key=$__row['Product Family Key'];
                $dept_key=$__row['Product Family Main Department Key'];
            }


            //     print_r($transaction);
            $code=_trim($transaction['code']);

            $product_data=array(
				'Product Store Key'=>$store_key
				,'Product Main Department Key'=>$dept_key
				,'product sales type'=>'Not for Sale'
				,'product type'=>'Normal'
				,'product record type'=>'Normal'
				,'product web state'=>'Offline'
				
				,'Product Family Key'=>$fam_key
                                                                                                                                                                                         ,'Product Locale'=>'fr_FR'
				,'Product Currency'=>$__currency_code
				,'product code'=>$code
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
				
				,'product valid from'=>$date_order
				,'product valid to'=>$date2
				,'editor'=>array('Date'=>$date_order)
				);
	    
            // print_r( $product_data);

            $product=new Product('find',$product_data,'create');
            if (!$product->id) {
                print_r($product_data);
                print "Error inserting a product\n";
                exit;
            }

            $supplier_code=_trim($transaction['supplier_code']);
            if ($supplier_code=='' or $supplier_code=='0' or  preg_match('/^costa$/i',$supplier_code))
                $supplier_code='Unknown';
            $supplier=new Supplier('code',$supplier_code);
            if (!$supplier->id) {
                $the_supplier_data=array(
                                       'Supplier Name'=>$supplier_code
                                                       ,'Supplier Code'=>$supplier_code
                                   );

                if ( $supplier_code=='Unknown'  ) {
                    $the_supplier_data=array(
                                           'Supplier Name'=>'Unknown Supplier'
                                                           ,'Supplier Code'=>$supplier_code
                                       );
                }

                $supplier=new Supplier('new',$the_supplier_data);
            }


            $part_list=array();
            if ($product->new_id ) {

                $uk_product=new Product('code_store',$code,1);
                $parts=$uk_product->get('Parts SKU');


                if (isset($parts[0])) {
                    // print "found part \n";
                    $part=new Part('sku',$parts[0]);
                    
                    
                    $part_list[]=array(

                                 'Part SKU'=>$part->get('Part SKU'),

                                 'Parts Per Product'=>$parts_per_product,
                                 'Product Part Type'=>'Simple'

                             );
                $product_part_header=array(
                                         'Product Part Valid From'=>$date_order,
                                         'Product Part Valid To'=>$date2,
                                         'Product Part Most Recent'=>'No',
                                         'Product Part Type'=>'Simple'

                                     );
                    
                    
                    
                    

                } else {

                    //creamos una parte nueva
                    $part_data=array(
                                   'Part Status'=>'Not In Use',
                                   'Part XHTML Currently Supplied By'=>sprintf('<a href="supplier.php?id=%d">%s</a>',$supplier->id,$supplier->get('Supplier Code')),
                                   'Part XHTML Currently Used In'=>sprintf('<a href="product.php?id=%d">%s</a>',$product->id,$product->get('Product Code')),
                                   'Part XHTML Description'=>preg_replace('/\(.*\)\s*$/i','',$product->get('Product XHTML Short Description')),
                                   'part valid from'=>$date_order,
                                   'part valid to'=>$date2,
                                   'Part Gross Weight'=>$w
                               );
                    $part=new Part('new',$part_data);
                    $parts_per_product=1;
                    $part_list=array();
                    
                            $part_list[]=array(

                                 'Part SKU'=>$part->get('Part SKU'),

                                 'Parts Per Product'=>$parts_per_product,
                                 'Product Part Type'=>'Simple'

                             );
                $product_part_header=array(
                                         'Product Part Valid From'=>$date_order,
                                         'Product Part Valid To'=>$date2,
                                         'Product Part Most Recent'=>'No',
                                         'Product Part Type'=>'Simple'

                                     );
                    
                    
                }
                //	print_r($part_list);
                  $product->new_historic_part_list($product_part_header,$part_list);
                $used_parts_sku=array($part->sku => array('parts_per_product'=>$parts_per_product,'unit_cost'=>$supplier_product_cost*$transaction['units']));

            } else {


  $sql=sprintf("select `Part SKU` from `Product Part List` PPL left join `Product Part Dimension` PPD on (PPL.`Product Part Key`=PPD.`Product Part Key`)where  `Product ID`=%d  ",$product->pid);
                $res_x=mysql_query($sql);
                if ($row_x=mysql_fetch_array($res_x)) {
                    $part_sku=$row_x['Part SKU'];
                } else {
                    print_r($product);
                    exit("error: $sql");
                }
                mysql_free_result($res_x);

                $part=new Part('sku',$part_sku);
                $part->update_valid_dates($date_order);
                $part->update_valid_dates($date2);
                $parts_per_product=1;
                $part_list=array();
                $part_list[]=array(

                                 'Part SKU'=>$part->sku,

                                 'Parts Per Product'=>$parts_per_product,
                                 'Product Part Type'=>'Simple'

                             );
                //print_r($part_list);
                $product_part_key=$product->find_product_part_list($part_list);
                if (!$product_part_key) {
                    exit("Error can not find product part list (get_orders_db)\n");
                }

                $product->update_product_part_list_historic_dates($product_part_key,$date_order,$date2);

                $used_parts_sku=array($part->sku=>array('parts_per_product'=>$parts_per_product,'unit_cost'=>$supplier_product_cost*$transaction['units']));

              
            }


           
            //creamos una supplier parrt nueva
            $scode=$sup_prod_code;
            $scode=_trim($scode);
            $scode=preg_replace('/^\"\s*/','',$scode);
            $scode=preg_replace('/\s*\"$/','',$scode);
            if (preg_match('/\d+ or more|0.10000007|8.0600048828125|0.050000038|0.150000076|0.8000006103|1.100000610|1.16666666|1.650001220|1.80000122070/i',$scode))
                $scode='';
            if (preg_match('/^(\?|new|\d|0.25|0.5|0.8|0.8000006103|01 Glass Jewellery Box|1|0.1|0.05|1.5625|10|\d{1,2}\s?\+\s?\d{1,2}\%)$/i',$scode))
                $scode='';
            if ($scode=='same')
                $scode=$code;
            if ($scode=='' or $scode=='0')
                $scode='?'.$code;

            // $scode= preg_replace('/\?/i','_unk',$scode);

            $sp_data=array(
                         'Supplier Key'=>$supplier->id,
                         'Supplier Product Status'=>'Not In Use',
                         'Supplier Product Code'=>$scode,
                         'Supplier Product Cost'=>sprintf("%.4f",$supplier_product_cost),
                         'Supplier Product Name'=>$description,
                         'Supplier Product Description'=>$description
                                                        ,'Supplier Product Valid From'=>$date_order
                                                                                       ,'Supplier Product Valid To'=>$date2
                     );
            // print "-----$scode <-------------\n";
            //print_r($sp_data);
            $supplier_product=new SupplierProduct('find',$sp_data,'create');

            // print 'NS'.$supplier_product->new." p:  ".$part->new."\n";

            if ($supplier_product->new or $part->new) {
                $rules=array();
                $rules[]=array('Part Sku'=>$part->data['Part SKU'],
                               'Supplier Product Units Per Part'=>$transaction['units']
                                                                 ,'supplier product part most recent'=>'Yes'
                                                                                                      ,'supplier product part valid from'=>$date_order
                                                                                                                                          ,'supplier product part valid to'=>$date2
                                                                                                                                                                            ,'factor supplier product'=>1
                              );
                //print_r($rules);
                $supplier_product->new_part_list('',$rules);
            } else {
                //Note assuming only one sppl
                $sql=sprintf("update `Supplier Product Part List`  set  `Supplier Product Part Valid From`=%s where `Supplier Product Part Valid From`>%s and `Supplier Product Code`=%s and `Supplier Key`=%d and `Part SKU`=%d and  `Supplier Product Part Most Recent`='Yes'"
                             ,prepare_mysql($date_order)
                             ,prepare_mysql($date_order)
                             ,prepare_mysql($supplier_product->code)
                             ,$supplier_product->supplier_key
                             ,$part->sku
                            );
                mysql_query($sql);
                $sql=sprintf("update  `Supplier Product Part List` set `Supplier Product Part Valid To`=%s where `Supplier Product Part Valid To`<%s and `Supplier Product Code`=%s  and `Supplier Key`=%d and `Part SKU`=%d and  `Supplier Product Part Most Recent`='Yes'"
                             ,prepare_mysql($date2)
                             ,prepare_mysql($date2)
                             ,prepare_mysql($supplier_product->code)
                             ,$supplier_product->supplier_key
                             ,$part->sku
                            );
                mysql_query($sql);

            }

            $used_parts_sku[$part->sku]['supplier_product_key']=$supplier_product->id;

            if ($transaction['order']!=0) {
                $products_data[]=array(
                                     'Product Key'=>$product->id
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
                if ($net_amount>0 ) {
                    $product->update_last_sold_date($date_order);
                    $product->update_first_sold_date($date_order);
                    $product->update_for_sale_since(date("Y-m-d H:i:s",strtotime("$date_order -1 second")));
                    if ($product->updated_field['Product For Sale Since Date']) {
                        $_date_order=date("Y-m-d H:i:s",strtotime("$date_order -2 second"));
                        $sql=sprintf("update `History Dimension` set `History Date`=%s  where `Action`='created' and `Direct Object`='Product' and `Direct Object Key`=%d  ",prepare_mysql($_date_order),$product->pid);
                        mysql_query($sql);


                    }
                }

                $data_invoice_transactions[]=array(
                                                 'Product Key'=>$product->id
                                                               ,'invoice qty'=>$transaction['order']-$transaction['reorder']
                                                                              ,'gross amount'=>$gross_amount
                                                                                              ,'discount amount'=>$net_discount
                                                                                                                 ,'current payment state'=>'Paid'



                                             );
                // print_r($data_invoice_transactions);
                $estimated_w+=$product->data['Product Gross Weight']*($transaction['order']-$transaction['reorder']);
                //print "$estimated_w ".$product->data['Product Gross Weight']." ".($transaction['order']-$transaction['reorder'])."\n";
                $data_dn_transactions[]=array(
                                            'Product Key'=>$product->id
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
                                                                                                                                                                                                                                                                                                                                                                            'parts_sku'=>$used_parts_sku
                                                                                                                                                                                                                                                                                                                                                                                        // 'supplier product key'=>$product->supplier_product_key,
                                                                                                                                                                                                                                                                                                                                                                                        //'part sku'=>$product->part_sku,
                                                                                                                                                                                                                                                                                                                                                                                        //'product part id'=>$product->product_part_id
                                                                                                                                                                                                                                                                                                                                                                        )
                                        );

            }
            if ($transaction['bonus']>0) {
                $products_data[]=array(
                                     'Product Key'=>$product->id
                                                   ,'qty'=>0
                                                          ,'gross_amount'=>0
                                                                          ,'discount_amount'=>0
                                                                                             ,'Estimated Weight'=>0
                                                                                                                 ,'units_per_case'=>$product->data['Product Units Per Case']
                                 );
                $data_invoice_transactions[]=array(
                                                 'Product Key'=>$product->id
                                                               ,'invoice qty'=>$transaction['bonus']
                                                                              ,'gross amount'=>($transaction['bonus'])*$transaction['price']
                                                                                              ,'discount amount'=>($transaction['bonus'])*$transaction['price']
                                                                                                                 ,'current payment state'=>'No Applicable'
                                             );

                $estimated_w+=$product->data['Product Gross Weight']*$transaction['bonus'];
                $data_dn_transactions[]=array(
                                            'Product Key'=>$product->id
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
                                                                                                                                                                                                                                                                                                                                                                            'parts_sku'=>$used_parts_sku

                                                                                                                                                                                                                                                                                                                                                                                        //'supplier product key'=>$product->supplier_product_key,
                                                                                                                                                                                                                                                                                                                                                                                        //'part sku'=>$product->part_sku,
                                                                                                                                                                                                                                                                                                                                                                                        //'product part id'=>$product->product_part_id
                                                                                                                                                                                                                                                                                                                                                                        )

                                        );




            }

        }
        //echo "Memory: ".memory_get_usage(true) . "\n";


        // print_r($products_data);


        $data['Order For']='Customer';

        $data['Order Main Source Type']='Unknown';
        if (  $header_data['showroom']=='Yes')
            $data['Order Main Source Type']='Store';

        $data['Delivery Note Dispatch Method']='Shipped';

        if ($header_data['collection']=='Yes') {
            $data['Delivery Note Dispatch Method']='Collected';
        }
        elseif($header_data['shipper_code']!='') {
            $data['Delivery Note Dispatch Method']='Shipped';
        }
        elseif($header_data['shipping']>0 or  $header_data['shipping']=='FOC') {
            $data['Delivery Note Dispatch Method']='Shipped';
        }


        if ($header_data['shipper_code']=='_OWN')
            $data['Delivery Note Dispatch Method']='Collected';

        if ($header_data['staff sale']=='Yes') {

            $data['Order For']='Staff';

        }


        if ($data['Delivery Note Dispatch Method']=='Collected') {
            $_customer_data['has_shipping']=false;
            $shipping_addresses=array();
        }


        if (array_empty($shipping_addresses)) {
            $data['Delivery Note Dispatch Method']='Collected';
            $_customer_data['has_shipping']=false;
            $shipping_addresses=array();
        }



        if ($customer_data['Customer Delivery Address Link']=='Contact') {
            $_customer_data['has_shipping']=true;
            $shipping_addresses=array();
        }



        //  print_r($data);
        $data['staff sale']=$header_data['staff sale'];
        $data['staff sale key']=$header_data['staff sale key'];




        $data['type']='direct_data_injection';
        $data['products']=$products_data;
        $data['Customer Data']=$customer_data;
        $data['Shipping Address']=$shipping_addresses;
        // $data['metadata_id']=$order_data_id;
        $data['tax_rate']=.15;
        if (strtotime($date_order)<strtotime('2008-11-01'))
            $data['tax_rate']=.175;
        $currency=$__currency_code;

        chdir('../../');

        if ($tipo_order==2 or $tipo_order==9)
            $exchange_date=$date_inv;
        else
            $exchange_date=$date_order;

        $currency_exchange = new CurrencyExchange($__currency_code.'GBP',$exchange_date);
        $exchange= $currency_exchange->get_exchange();
        chdir('mantenence/scripts/');

        if ($exchange==0) {

            exit("error exhange is zero for $exchange_date\n");
        }
        list($parcels,$parcel_type)=parse_parcels($header_data['parcels']);

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


        if ($update) {
            print "Updated ";

            $sql=sprintf("select `Order Key`  from `Order Dimension`  where `Order Original Metadata`=%s  ",prepare_mysql($store_code.$order_data_id));

            $result_test=mysql_query($sql);
            while ($row_test=mysql_fetch_array($result_test, MYSQL_ASSOC)) {

                $sql=sprintf("delete from `History Dimension` where `Direct Object Key`=%d and `Direct Object`='Sale'   ",$row_test['Order Key']);
                if (!mysql_query($sql))
                    print "$sql Warning can no delete oldhidtfgf";

            };



            $sql=sprintf("delete from `Order No Product Transaction Fact` where `Metadata`=%s",prepare_mysql($store_code.$order_data_id));
            if (!mysql_query($sql))
                print "$sql Warning can no delete old order";

            //delete things
            $sql=sprintf("delete from `Order Dimension` where `Order Original Metadata`=%s",prepare_mysql($store_code.$order_data_id));
            //	 print $sql;

            if (!mysql_query($sql))
                print "$sql Warning can no delete old order";
            $sql=sprintf("delete from `Invoice Dimension` where `Invoice Metadata`=%s",prepare_mysql($store_code.$order_data_id));
            if (!mysql_query($sql))
                print "$sql Warning can no delete old inv";
            $sql=sprintf("delete from `Delivery Note Dimension` where `Delivery Note Metadata`=%s",prepare_mysql($store_code.$order_data_id));
            if (!mysql_query($sql))
                print "$sql Warning can no delete old dn";
            $sql=sprintf("delete from `Order Transaction Fact` where `Metadata`=%s",prepare_mysql($store_code.$order_data_id));
            if (!mysql_query($sql))
                print "$sql Warning can no delete tf";
            $sql=sprintf("delete from `Inventory Transaction Fact` where `Metadata`=%s and `Inventory Transaction Type`='Sale'   ",prepare_mysql($store_code.$order_data_id));
            if (!mysql_query($sql))
                print "$sql Warning can no delete old inv";




            $sql=sprintf("delete from `Order No Product Transaction Fact` where `Metadata`=%s ",prepare_mysql($store_code.$order_data_id));
            if (!mysql_query($sql))
                print "$sql Warning can no delete oldhidt nio prod";

        }

        //print "$tipo_order \n";
        $data['Order Currency']=$currency;
        $data['Order Currency Exchange']=$exchange;
        $sales_rep_data=get_user_id($header_data['takenby'],true,'&view=processed',$header_data['order_num'],$editor);
        $data['Order XHTML Sale Reps']=$sales_rep_data['xhtml'];
        $data['Order Customer Contact Name']=$customer_data['Customer Main Contact Name'];
        $data['Order Sale Reps IDs']=$sales_rep_data['id'];
        $data['Order Currency']=$currency;
        $data['Order Currency Exchange']=$exchange;
        $data['store_id']=$store_key;
        $payment_method=parse_payment_method($header_data['pay_method']);
        $lag=(strtotime($date_inv)-strtotime($date_order))/24/3600;
        if ($lag==0 or $lag<0)
            $lag='';
        $taxable='Yes';
        $tax_code='UNK';
        if ($header_data['total_net']!=0) {
            if ($header_data['tax1']+$header_data['tax2']==0) {
                $tax_code='EX0';
            }
            $tax_rate=($header_data['tax1']+$header_data['tax2'])/$header_data['total_net'];
            foreach($myconf['tax_rates'] as $_tax_code=>$_tax_rate) {
                $upper=1.1*$_tax_rate;
                $lower=0.9*$_tax_rate;
                if ($tax_rate>=$lower and $tax_rate<=$upper) {
                    $tax_code=$_tax_code;
                    break;
                }
            }
        } else {
            $tax_code='ZV';
        }

        $data['Order Tax Code']=$tax_code;
        $data['Order Tax Rate']=$tax_rate;


        if ($tipo_order==2 or $tipo_order==1  or $tipo_order==4 or $tipo_order==5 or   $tipo_order==3 or   $tipo_order==8    )  {

            if ($tipo_order==1 or $tipo_order==2 or  $tipo_order==3  )
                $data['Order Type']='Order';
            else if ($tipo_order==4)
                $data['Order Type']='Sample';
            else if ($tipo_order==5)
                $data['Order Type']='Donation';


            foreach($data['products'] as $key=>$val) {
                $data['products'][$key]['tax_rate']=$tax_rate;
                $data['products'][$key]['tax_code']=$tax_code;
                //   $data['products'][$key]['tax amount']=$tax_rate*($val['gross amount']-($val['discount amount']));
            }


            // print_r($data['products']);




            if ( $tipo_order!=8 ) {
                $order= new Order('new',$data);
                $order->categorize();
                $order->set_shipping(round($header_data['shipping']+$extra_shipping,2),$tax_rate);
                $order->set_charges(round($header_data['charges'],2),$tax_rate);
            }

            if ($tipo_order==2) {


                foreach($data_invoice_transactions as $key=>$val) {
                    $data_invoice_transactions[$key]['tax rate']=$tax_rate;
                    $data_invoice_transactions[$key]['tax code']=$tax_code;
                    $data_invoice_transactions[$key]['tax amount']=$tax_rate*($val['gross amount']-($val['discount amount']));
                }



                $data_invoice=array(
                                  'Invoice Date'=>$date_inv
                                                 ,'Invoice Public ID'=>$header_data['order_num']
                                                                      ,'Invoice File As'=>$header_data['order_num']
                                                                                         ,'Invoice Main Payment Method'=>$payment_method
                                                                                                                        ,'Invoice Multiple Payment Methods'=>0
                                                                                                                                                            ,'Invoice Shipping Net Amount'=>round($header_data['shipping']+$extra_shipping,2)
                                                                                                                                                                                           ,'Invoice Charges Net Amount'=>round($header_data['charges'],2)
                                                                                                                                                                                                                         ,'Invoice Total Tax Amount'=>round($header_data['tax1'],2)
                                                                                                                                                                                                                                                     ,'Invoice Refund Net Amount'=>$total_credit_value
                                                                                                                                                                                                                                                                                  ,'Invoice Refund Tax Amount'=>$tax_rate*$total_credit_value
                                                                                                                                                                                                                                                                                                               ,'Invoice Total Amount'=>round($header_data['total_topay'],2)
                                                                                                                                                                                                                                                                                                                                       ,'tax_rate'=>$tax_rate
                                                                                                                                                                                                                                                                                                                                                   ,'Invoice Has Been Paid In Full'=>'No'
                                                                                                                                                                                                                                                                                                                                                                                    ,'Invoice Items Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value
                                                                                                                                                                                                                                                                                                                                                                                                                ,'Invoice XHTML Processed By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                              ,'Invoice XHTML Charged By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                          ,'Invoice Processed By Key'=>''
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ,'Invoice Charged By Key'=>''
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ,'Invoice Total Adjust Amount'=>round($header_data['total_topay'],2)-round($header_data['tax1'],2)-round($header_data['total_net'],2)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               ,'Invoice Tax Code'=>$tax_code
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   ,'Invoice Taxable'=>$taxable
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ,'Invoice Dispatching Lag'=>$lag
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ,'Invoice Customer Contact Name'=>$customer_data['Customer Main Contact Name']
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  ,'Invoice Currency'=>$currency
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ,'Invoice Currency Exchange'=>$exchange
                              );
                //print_r($data_invoice);
                //print_r($header_data);
                //exit;
                if (!is_numeric($header_data['weight']))
                    $weight=$estimated_w;
                else
                    $weight=$header_data['weight'];


                $picker_data=get_user_id($header_data['pickedby'],true,'&view=picks',$header_data['order_num'],$editor);
                $packer_data=get_user_id($header_data['packedby'],true,'&view=packs',$header_data['order_num'],$editor);
                $order_type=$data['Order Type'];

                // TODO in the Ci, DE


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
                                                                                                                                                                                                                                                                                                                                                            ,'Delivery Note Has Shipping'=>$_customer_data['has_shipping']
                                                                                                                                                                                                                                                                                                                                                                                          ,'Delivery Note Shipper Code'=>$header_data['shipper_code']
                                                                                                                                                                                                                                                                                                                                                                                                                        ,'Delivery Note Dispatch Method'=>$data['Delivery Note Dispatch Method']

                         );

                //$order->create_dn_simple($data_dn,$data_dn_transactions);
                $dn=new DeliveryNote('create',$data_dn,$data_dn_transactions,$order);
                $order->update_delivery_notes('save');
                $order->update_dispatch_state('Ready to Pick');
                $order->update_invoices('save');
                if ($total_credit_value==0 and $header_data['total_topay']==0) {
                    print "Zero value order ".$header_data['order_num']." \n";
                    $order->no_payment_applicable();
                    $dn->pick_simple($data_dn_transactions);
                    $order->update_dispatch_state('Ready to Pack');
                    $dn->pack('all');
                    $dn->set_parcels($parcels,$parcel_type);
                    $order->update_dispatch_state('Ready to Ship');
                    $order->load('totals');
                    $order->update_dispatch_state('Dispached');
                    $order-> update_payment_state('No Applicable');

                } else {// paid for it
                    //$order->create_invoice_simple($data_invoice,$data_invoice_transactions);

                    $invoice=new Invoice ('create',$data_invoice,$data_invoice_transactions,$order->id);
                    //to update otter scripts read TODO
                    $invoice->add_tax_item('IVA',$header_data['tax1'],'Yes');
                    $invoice->add_tax_item('I2',$header_data['tax2'],'Yes');

                    foreach($credits as $credit) {

                        //	  print_r($header_2data);
                        $sql=sprintf("insert into `Order No Product Transaction Fact`  (`Order Date`,`Invoice Date`,`Order Key`,`Invoice Key`,`Transaction Type`,`Transaction Description`,`Transaction Net Amount`,`Transaction Tax Amount`,`Currency Code`,`Currency Exchange`,`Metadata`)  values  (%s,%s,%s,%s,'Credit',%s,%.2f,%.2f,%s,%f,%s)"
                                     ,prepare_mysql($credit['parent_date'])
                                     ,prepare_mysql($invoice->data['Invoice Date'])
                                     ,$credit['parent_key']
                                     ,prepare_mysql($invoice->data['Invoice Key'])
                                     ,prepare_mysql($credit['description'])
                                     ,$credit['value']
                                     ,$tax_rate*$credit['value']
                                     ,prepare_mysql($currency)
                                     ,$exchange
                                     ,prepare_mysql($store_code.$order_data_id)
                                    );

                        if (!mysql_query($sql))
                            exit("$sql\n error can not inser orde rno pro trns");


                        if ($credit['parent_key']!='NULL') {
                            $parent=new Order($credit['parent_key']);
                            $parent->load('totals');
                            //   print "******************************************\n$sql\n";
                            // 	    exit;
                        }
                    }
                    //print_r($data_dn_transactions);

                    $dn->pick_simple($data_dn_transactions);
                    $order->update_dispatch_state('Ready to Pack');
                    // print"xxxxxxxxxxxxxxxxxxxxxxxxxxxxx\n";

                    $dn->pack('all');
                    $dn->set_parcels($parcels,$parcel_type);
                    $order->update_dispatch_state('Ready to Ship');
                    $invoice->data['Invoice Paid Date']=$date_inv;
                    $invoice->pay('full',
                                  array(
                                      'Invoice Items Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value-$extra_shipping
                                                                 ,'Invoice Total Net Amount'=>round($header_data['total_net'],2)
                                                                                             ,'Invoice Total Tax Amount'=>round($header_data['tax1']+$header_data['tax2'],2)
                                                                                                                         ,'Invoice Total Amount'=>round($header_data['total_topay'],2)

                                  ));
                    $order-> update_payment_state('Paid');
                    $dn->dispatch('all',$data_dn_transactions);
                    $order->update_dispatch_state('Dispached');

                    $order->load('totals');
                    $invoice->categorize('save');
                }


            }
            elseif($tipo_order==8) {

                $parent_order=new Order('public_id',$parent_order_id);
                if (!$parent_order->id) {
                    // try to get same customer last order
                    $customer = new Customer ( 'find', $data['Customer Data'] );
                    $order_id=$customer->get_last_order();
                    if ($order_id) {
                        $parent_order=new Order('id',$order_id);
                        print "found last order\n";
                    } else {
                        print "ast order not found created new one\n";
                        $data['Order Type']='Order';
                        $parent_order=new Order('new',$data);
                        $order->categorize();


                    }

                }

                if (!is_numeric($header_data['weight']))
                    $weight=$estimated_w;
                else
                    $weight=$header_data['weight'];


                $picker_data=get_user_id($header_data['pickedby'],true,'&view=picks',$header_data['order_num'],$editor);
                $packer_data=get_user_id($header_data['packedby'],true,'&view=packs',$header_data['order_num'],$editor);
                $order_type='Follow on';  ;
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
                                                                                                                                                                                                                                                                                                                                                            ,'Delivery Note Has Shipping'=>$_customer_data['has_shipping']
                                                                                                                                                                                                                                                                                                                                                                                          ,'Delivery Note Shipper Code'=>$header_data['shipper_code']
                                                                                                                                                                                                                                                                                                                                                                                                                        ,'Delivery Note Dispatch Method'=>$data['Delivery Note Dispatch Method']
                         );

                //$order->create_dn_simple($data_dn,$data_dn_transactions);

                $dn=new DeliveryNote('create',$data_dn,$data_dn_transactions,$parent_order);
                $parent_order->update_delivery_notes('save');
                $parent_order->update_dispatch_state('Ready to Pick');
                $parent_order->update_invoices('save');



                if ($header_data['total_topay']!=0) {
                    foreach($data_invoice_transactions as $key=>$val) {
                        $data_invoice_transactions[$key]['tax rate']=$tax_rate;
                        $data_invoice_transactions[$key]['tax code']=$tax_code;
                        $data_invoice_transactions[$key]['tax amount']=$tax_rate*($val['gross amount']-($val['discount amount']));
                    }
                    $data_invoice=array(
                                      'Invoice Date'=>$date_inv
                                                     ,'Invoice Public ID'=>$header_data['order_num']
                                                                          ,'Invoice File As'=>$header_data['order_num']
                                                                                             ,'Invoice Main Payment Method'=>$payment_method
                                                                                                                            ,'Invoice Multiple Payment Methods'=>0
                                                                                                                                                                ,'Invoice Shipping Net Amount'=>round($header_data['shipping']+$extra_shipping,2)
                                                                                                                                                                                               ,'Invoice Charges Net Amount'=>round($header_data['charges'],2)
                                                                                                                                                                                                                             ,'Invoice Total Tax Amount'=>round($header_data['tax1'],2)
                                                                                                                                                                                                                                                         ,'Invoice Refund Net Amount'=>$total_credit_value
                                                                                                                                                                                                                                                                                      ,'Invoice Refund Tax Amount'=>$tax_rate*$total_credit_value
                                                                                                                                                                                                                                                                                                                   ,'Invoice Total Amount'=>round($header_data['total_topay'],2)
                                                                                                                                                                                                                                                                                                                                           ,'tax_rate'=>$tax_rate
                                                                                                                                                                                                                                                                                                                                                       ,'Invoice Has Been Paid In Full'=>'No'
                                                                                                                                                                                                                                                                                                                                                                                        ,'Invoice Items Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value
                                                                                                                                                                                                                                                                                                                                                                                                                    ,'Invoice XHTML Processed By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                                  ,'Invoice XHTML Charged By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                              ,'Invoice Processed By Key'=>''
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          ,'Invoice Charged By Key'=>''
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ,'Invoice Total Adjust Amount'=>round($header_data['total_topay'],2)-round($header_data['tax1'],2)-round($header_data['total_net'],2)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   ,'Invoice Tax Code'=>$tax_code
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       ,'Invoice Taxable'=>$taxable
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          ,'Invoice Dispatching Lag'=>$lag
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     ,'Invoice Customer Contact Name'=>$customer_data['Customer Main Contact Name']
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ,'Invoice Currency'=>$currency
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          ,'Invoice Currency Exchange'=>$exchange
                                  );

                    $invoice=new Invoice ('create',$data_invoice,$data_invoice_transactions,$parent_order->id);
                    $invoice->add_tax_item('IVA',$header_data['tax1'],'Yes');
                    $invoice->add_tax_item('I2',$header_data['tax2'],'Yes');
                } else {// no payment


                }

                $dn->pick_simple($data_dn_transactions);
                $parent_order->update_dispatch_state('Ready to Pack');

                $dn->pack('all');
                $dn->set_parcels($parcels,$parcel_type);
                $parent_order->update_dispatch_state('Ready to Ship');
                if ($header_data['total_topay']!=0) {
                    $invoice->data['Invoice Paid Date']=$date_inv;
                    $invoice->pay('full',
                                  array(
                                      'Invoice Items Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value-$extra_shipping
                                                                 ,'Invoice Total Net Amount'=>round($header_data['total_net'],2)
                                                                                             ,'Invoice Total Tax Amount'=>round($header_data['tax1']+$header_data['tax2'],2)
                                                                                                                         ,'Invoice Total Amount'=>round($header_data['total_topay'],2)

                                  ));
                    $parent_order-> update_payment_state('Paid');
                    $invoice->categorize('save');

                }

                $dn->dispatch('all',$data_dn_transactions);
                $parent_order->update_dispatch_state('Dispached');
                $parent_order->load('totals');


            }
            else if ($tipo_order==4 or $tipo_order==5 ) {

                if ($header_data['total_net']!=0)
                    $tax_rate=$header_data['tax1']/$header_data['total_net'];
                else
                    $tax_rate=$data['tax_rate'];
                if (!is_numeric($header_data['weight']))
                    $weight=$estimated_w;
                else
                    $weight=$header_data['weight'];


                $picker_data=get_user_id($header_data['pickedby'],true,'&view=picks',$header_data['order_num'],$editor);
                $packer_data=get_user_id($header_data['packedby'],true,'&view=packs',$header_data['order_num'],$editor);

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
                                                                                                                                                                                                                                                                                                                           ,'Invoice Has Been Paid In Full'=>'No'
                                                                                                                                                                                                                                                                                                                                                            ,'Invoice Items Net Amount'=>$header_data['total_items_charge_value']-$total_credit_value
                                                                                                                                                                                                                                                                                                                                                                                        ,'Delivery Note Type'=>$order_type
                                                                                                                                                                                                                                                                                                                                                                                                              ,'Delivery Note Title'=>_('Delivery Note for').' '.$order_type.' '.$header_data['order_num']

                                                                                                                                                                                                                                                                                                                                                                                                                                     ,'Delivery Note Has Shipping'=>$_customer_data['has_shipping']
                                                                                                                                                                                                                                                                                                                                                                                                                                                                   ,'Delivery Note Shipper Code'=>$header_data['shipper_code']
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ,'Delivery Note Dispatch Method'=>$data['Delivery Note Dispatch Method']
                         );


                //$order->create_dn_simple($data_dn,$data_dn_transactions);
                $dn=new DeliveryNote('create',$data_dn,$data_dn_transactions,$order);

                if ($header_data['total_topay']>0) {
                    $payment_method=parse_payment_method($header_data['pay_method']);


                    $taxable='Yes';
                    $tax_code='UNK';

                    if ($header_data['total_net']!=0) {

                        if ($header_data['tax1']+$header_data['tax2']==0) {
                            $tax_code='EX0';
                        }
                        $tax_rate=($header_data['tax1']+$header_data['tax2'])/$header_data['total_net'];
                        foreach($myconf['tax_rates'] as $_tax_code=>$_tax_rate) {
                            // print "$_tax_code => $_tax_rate $tax_rate\n ";
                            $upper=1.1*$_tax_rate;
                            $lower=0.9*$_tax_rate;
                            if ($tax_rate>=$lower and $tax_rate<=$upper) {
                                $tax_code=$_tax_code;
                                break;
                            }
                        }
                    } else {
                        $tax_code='ZV';

                    }

                    $lag=(strtotime($date_inv)-strtotime($date_order))/24/3600;
                    if ($lag==0 or $lag<0)
                        $lag='';



                    foreach($data_invoice_transactions as $key=>$val) {
                        $data_invoice_transactions[$key]['tax rate']=$tax_rate;
                        $data_invoice_transactions[$key]['tax code']=$tax_code;
                        // print_r($val);exit;
                        $data_invoice_transactions[$key]['tax amount']=$tax_rate*($val['gross amount']-($val['discount amount']));
                    }

                    $data_invoice=array(
                                      'Invoice Date'=>$date2
                                                     ,'Invoice Public ID'=>$header_data['order_num']
                                                                          ,'Invoice File As'=>$header_data['order_num']
                                                                                             ,'Invoice Main Payment Method'=>$payment_method
                                                                                                                            ,'Invoice Multiple Payment Methods'=>0
                                                                                                                                                                ,'Invoice Shipping Net Amount'=>round($header_data['shipping']+$extra_shipping,2)
                                                                                                                                                                                               ,'Invoice Charges Net Amount'=>round($header_data['charges'],2)
                                                                                                                                                                                                                             ,'tax_rate'=>$tax_rate
                                                                                                                                                                                                                                         ,'tax_rate'=>$tax_rate
                                                                                                                                                                                                                                                     ,'Invoice Has Been Paid In Full'=>'No'
                                                                                                                                                                                                                                                                                      ,'Invoice Items Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value
                                                                                                                                                                                                                                                                                                                  ,'Invoice Total Tax Amount'=>$header_data['tax1']
                                                                                                                                                                                                                                                                                                                                              ,'Invoice Refund Net Amount'=>$total_credit_value
                                                                                                                                                                                                                                                                                                                                                                           ,'Invoice Refund Tax Amount'=>$tax_rate*$total_credit_value
                                                                                                                                                                                                                                                                                                                                                                                                        ,'Invoice Total Amount'=>$header_data['total_topay']
                                                                                                                                                                                                                                                                                                                                                                                                                                ,'Invoice XHTML Processed By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                                              ,'Invoice XHTML Charged By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          ,'Invoice Processed By Key'=>0
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ,'Invoice Charged By Key'=>0
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ,'Invoice Tax Code'=>$tax_code
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ,'Invoice Taxable'=>$taxable
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       ,'Invoice Dispatching Lag'=>$lag
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  ,'Invoice Currency'=>$currency
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ,'Invoice Currency Exchange'=>$exchange
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   ,'Invoice Customer Contact Name'=>$customer_data['Customer Main Contact Name']

                                  );
                    // $order->create_invoice_simple($data_invoice,$data_invoice_transactions);
                    $invoice=new Invoice ('create',$data_invoice,$data_invoice_transactions,$order->id);
                    $invoice->add_tax_item('IVA',$header_data['tax1'],'Yes');
                    $invoice->add_tax_item('I2',$header_data['tax2'],'Yes');
                    $invoice->data['Invoice Paid Date']=$date_inv;
                    $invoice->pay('full',
                                  array(
                                      'Invoice Items Net Amount'=>round($header_data['total_items_charge_value'],2)-$total_credit_value-$extra_shipping
                                                                 ,'Invoice Total Net Amount'=>round($header_data['total_net'],2)
                                                                                             ,'Invoice Total Tax Amount'=>round($header_data['tax1']+$header_data['tax2'],2)
                                                                                                                         ,'Invoice Total Amount'=>round($header_data['total_topay'],2)
                                  ));
                    $order-> update_payment_state('Paid');




                    $dn->pick_simple($data_dn_transactions);
                    $order->update_dispatch_state('Ready to Pack');

                    $dn->pack('all');
                    $dn->set_parcels($parcels,$parcel_type);
                    $order->update_dispatch_state('Ready to Ship');
                    $dn->dispatch('all',$data_dn_transactions);
                    $order->update_dispatch_state('Dispached');


                    $order->load('totals');
                    $invoice->categorize('save');
                } else {

                    $dn->pick_simple($data_dn_transactions);
                    $order->update_dispatch_state('Ready to Pack');

                    $dn->pack('all');
                    $dn->set_parcels($parcels,$parcel_type);
                    $order->update_dispatch_state('Ready to Ship');
                    $dn->dispatch('all',$data_dn_transactions);
                    $order->update_dispatch_state('Dispached');


                    $order->no_payment_applicable();
                    $order->load('totals');




                }








            } else if ($tipo_order==3) {
                $order->cancel();

            } else {

                $order->load('totals');

            }


            $sql="update fr_orders_data.orders set last_transcribed=NOW() where id=".$order_data_id;
            mysql_query($sql);
        }
        elseif($tipo_order==9 ) {
            // refund

            $taxable='Yes';
            $tax_code='UNK';

            if ($header_data['total_net']!=0) {

                if ($header_data['tax1']+$header_data['tax2']==0) {
                    $tax_code='EX0';
                }

                $tax_rate=($header_data['tax1']+$header_data['tax2'])/$header_data['total_net'];
                foreach($myconf['tax_rates'] as $_tax_code=>$_tax_rate) {
                    // print "$_tax_code => $_tax_rate $tax_rate\n ";
                    $upper=1.1*$_tax_rate;
                    $lower=0.9*$_tax_rate;
                    if ($tax_rate>=$lower and $tax_rate<=$upper) {
                        $tax_code=$_tax_code;
                        break;
                    }
                }
            } else {
                $tax_code='ZV';
            }

            foreach($data_invoice_transactions as $key=>$val) {
                $data_invoice_transactions[$key]['tax rate']=$tax_rate;
                $data_invoice_transactions[$key]['tax code']=$tax_code;
                $data_invoice_transactions[$key]['tax amount']=$tax_rate*($val['gross amount']-($val['discount amount']));
            }

            $order=new Order('public_id',$parent_order_id);

            if (!$order->id) {// try to get last customer order
                $customer = new Customer ( 'find', $data['Customer Data'] );
                $order_id=$customer->get_last_order();
                if ($order->id)
                    print "Using last known customer order to scope the refund\n";
            }



            if (!$order->id) {

                print "Unknown parent $parent_order_id\n";
                // Create an invoice (refund not realted to the customer)

                //	print_r($data);
                //exit;
                //$invoice=new Invoice ('create',$data_invoice,$data_invoice_transactions,false);

                // create new invoice (negative)(no deliver note changes noting)
                //	exit;
                $data['ghost_order']=true;
                $data['Order Type']='Order';
                $data['store_id']=$store_key;
                $order= new Order('new',$data);
                $order->categorize();




            }

            $payment_method=parse_payment_method($header_data['pay_method']);



            $factor=1.0;
            if ($header_data['total_topay']>0)
                $factor=-1.0;




            $data_invoice=array(
                              'Invoice Date'=>$date_inv
                                             ,'Invoice Public ID'=>$header_data['order_num']
                                                                  ,'Invoice File As'=>$header_data['order_num']
                                                                                     ,'Invoice Main Payment Method'=>$payment_method
                                                                                                                    ,'Invoice Multiple Payment Methods'=>0
                                                                                                                                                        ,'Invoice Shipping Net Amount'=>0
                                                                                                                                                                                       ,'Invoice Charges Net Amount'=>0
                                                                                                                                                                                                                     ,'Invoice Total Tax Amount'=>$header_data['tax1']*$factor
                                                                                                                                                                                                                                                 ,'Invoice Refund Net Amount'=>$header_data['total_net']*$factor
                                                                                                                                                                                                                                                                              ,'Invoice Refund Tax Amount'=>$header_data['tax1']*$factor
                                                                                                                                                                                                                                                                                                           ,'Invoice Total Amount'=>$header_data['total_topay']*$factor
                                                                                                                                                                                                                                                                                                                                   ,'tax_rate'=>$tax_rate
                                                                                                                                                                                                                                                                                                                                               ,'Invoice Has Been Paid In Full'=>'No'
                                                                                                                                                                                                                                                                                                                                                                                ,'Invoice Items Net Amount'=>0
                                                                                                                                                                                                                                                                                                                                                                                                            ,'Invoice XHTML Processed By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                          ,'Invoice XHTML Charged By'=>_('Unknown')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                      ,'Invoice Processed By Key'=>0
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  ,'Invoice Charged By Key'=>0
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ,'Invoice Tax Code'=>$tax_code
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ,'Invoice Taxable'=>$taxable
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   ,'Invoice Dispatching Lag'=>''
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              ,'Invoice Currency'=>$currency
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  ,'Invoice Currency Exchange'=>$exchange
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               ,'Invoice Customer Contact Name'=>$customer_data['Customer Main Contact Name']
                          );
            //	print_r($data_invoice);

            $data_refund_transactions=array();
            $sum_net=0;
            $sum_tax=0;

            if (is_numeric($header_data['shipping']) and $header_data['shipping']!=0) {
                $data_refund_transactions[]=array(
                                                'Transaction Net Amount'=>$header_data['shipping']*$factor,
                                                'Description'=>_('Refund for Shipping')
                                                              ,'Transaction Tax Amount'=>$header_data['shipping']*$factor*$tax_rate

                                            );

                $sum_net+=$header_data['shipping']*$factor;
                $sum_tax+=$header_data['shipping']*$factor*$tax_rate;

            }
            if (is_numeric($header_data['charges']) and $header_data['charges']!=0) {
                $data_refund_transactions[]=array(
                                                'Transaction Net Amount'=>$header_data['charges']*$factor,
                                                'Description'=>_('Refund for Charges')
                                                              ,'Transaction Tax Amount'=>$header_data['charges']*$factor*$tax_rate

                                            );

                $sum_net+=$header_data['charges']*$factor;
                $sum_tax+=$header_data['charges']*$factor*$tax_rate;
            }



            foreach($data_invoice_transactions as $key=>$data) {
                $product=new Product($data_invoice_transactions[$key]['Product Key']);
                if ($product->id) {
                    $description=_('Refund for')." ".$data_invoice_transactions[$key]['invoice qty']." ".$product->data['Product Code'] ;
                } else
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

            foreach($credits as $credit) {

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
            if (abs($diff_net)>0.01) {
                $data_refund_transactions[]=array(
                                                'Transaction Net Amount'=>$diff_net,
                                                'Description'=>_('Other Refunds')
                                                              ,'Transaction Tax Amount'=>$diff_net*$tax_rate

                                            );
            }


            $diff_tax=($factor*$header_data['tax1'])-$sum_tax-$diff_net*$tax_rate;
            if (abs($diff_tax)>0.01) {
                $data_refund_transactions[]=array(
                                                'Transaction Net Amount'=>0,
                                                'Description'=>_('Other Tax Refunds')
                                                              ,'Transaction Tax Amount'=>$diff_tax

                                            );
            }


            //		print_r($data_refund_transactions);
            //$order->create_refund_simple($data_invoice,$data_refund_transactions);
            // print $order->id;

            $refund = new Invoice('create refund',$data_invoice,$data_refund_transactions,$order);
            $refund->add_tax_item('IVA',$header_data['tax1'],'Yes');
            $refund->add_tax_item('I2',$header_data['tax2'],'Yes');
            $refund->data['Invoice Paid Date']=$date_inv;
            $refund->pay('full',
                         array(

                             'Invoice Total Net Amount'=>round($header_data['total_net']*$factor,2)
                                                        ,'Invoice Total Tax Amount'=>round(($header_data['tax1']+$header_data['tax2'])*$factor,2)
                                                                                    ,'Invoice Total Amount'=>round($header_data['total_topay']*$factor,2)
                         )
                        );
            $refund->categorize('save');

            if ($order->id)
                $order-> update_payment_state('Paid');


            //print "STSRT----------\n\n\n";
            //$order->load('totals');
            //	print "END------------\n\n\n";
            $sql="update fr_orders_data.orders set last_transcribed=NOW() where id=".$order_data_id;
            mysql_query($sql);


            //      exit("refund");
        }
        else if ($tipo_order==11) {
            //TODO make quites and insert them in the customer space
            $sql="update fr_orders_data.orders set last_transcribed=NOW() where id=".$order_data_id;
            mysql_query($sql);

        }
        elseif($tipo_order==6 or $tipo_order==7 ) {
            if ($tipo_order==6)
                $order_type='Replacement';
            else
                $order_type='Shortages';

            print("$order_type\n");

            if (!is_numeric($header_data['weight']))
                $weight=$estimated_w;
            else
                $weight=$header_data['weight'];


            $picker_data=get_user_id($header_data['pickedby'],true,'&view=picks',$header_data['order_num'],$editor);
            $packer_data=get_user_id($header_data['packedby'],true,'&view=packs',$header_data['order_num'],$editor);

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
                                                                                                                                                                                                                                                                                                                                                        ,'Delivery Note Metadata'=>$store_code.$order_data_id
                                                                                                                                                                                                                                                                                                                                                                                  ,'Delivery Note Has Shipping'=>$_customer_data['has_shipping']
                                                                                                                                                                                                                                                                                                                                                                                                                ,'Delivery Note Shipper Code'=>$header_data['shipper_code']
                                                                                                                                                                                                                                                                                                                                                                                                                                              ,'Delivery Note Dispatch Method'=>$data['Delivery Note Dispatch Method']
                     );

            $parent_order=new Order('public_id',$parent_order_id);


            if (!$parent_order->id) {
                $customer = new Customer ( 'find', $data['Customer Data'] );
                $order_id=$customer->get_last_order();
                if ($order_id) {
                    $parent_order=new Order('id',$order_id);
                    print "Parent Order not given, using customer last order\n";
                } else {
                    print "Parent order can not be found skipping (Rpl/Sht)\n";
                    continue;
                }
            } else
                print "Using given Parent Order\n";


            $parent_order->load('items');
            $customer=new Customer($parent_order->data['Order Customer Key']);


            if ($_customer_data['has_shipping']  and isset($data['Shipping Address']) and is_array($data['Shipping Address']) and !array_empty($data['Shipping Address'])) {

                $address=new Address('find create',$data['Shipping Address']);
                $customer->update(array('Customer Delivery Address Link','None'));
                $customer->associate_delivery_address($address->id);
                $ship_to_data=$customer->get_ship_to_data();

                $ship_to=new Ship_To('find create',$ship_to_data);

                $parent_order->add_ship_to($ship_to->id);
                $parent_order->data ['Order Ship To Key To Deliver']=$ship_to->id;

            }


            $parent_order->data['Backlog Date']=$date_inv;
            if ($tipo_order==6)
                $data_dn['Delivery Note Title']=_('Replacents for Order').' '.$parent_order->data['Order Public ID'];
            else
                $data_dn['Delivery Note Title']=_('Shotages for Order').' '.$parent_order->data['Order Public ID'];
            $dn=new DEliveryNote('create',$data_dn,$data_dn_transactions,$parent_order);


            $sql="update fr_orders_data.orders set last_transcribed=NOW() where id=".$order_data_id;
            mysql_query($sql);
        }







        if ($contador % $calculate_no_normal_every  == 0) {
            update_data($to_update);
            $to_update=array(
                           'products'=>array(),
                           'products_id'=>array(),
                           'products_code'=>array(),
                           'families'=>array(),
                           'departments'=>array(),
                           'stores'=>array(),
                           'parts'=>array()

                       );


        }
    }
    mysql_free_result($result);
}
mysql_free_result($res);
update_data($to_update);

//  print_r($data);
//print "\n$tipo_order\n";

function update_data($to_update) {

    if (false) {
        $tm=new TimeSeries(array('q','invoices'));
        $tm->to_present=true;
        $tm->get_values();
        $tm->save_values();
        $tm=new TimeSeries(array('m','invoices'));

        $tm->to_present=true;
        $tm->get_values();
        $tm->save_values();

        $tm=new TimeSeries(array('y','invoices'));
        $tm->to_present=true;
        $tm->get_values();
        $tm->save_values();


        foreach($to_update['products'] as $key=>$value) {
            $product=new Product($key);
            $product->load('sales');


        }

    }

    if (false) {
        foreach($to_update['products_id'] as $key=>$value) {

            $tm=new TimeSeries(array('m','product id ('.$key.') sales'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('y','product id ('.$key.') sales'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('q','product id ('.$key.') sales'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('m','product id ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('y','product id ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('q','product id ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();


        }
    }



    foreach($to_update['families'] as $key=>$value) {
        $product=new Family($key);
        $product->load('sales');
        if (false) {
            // $tm=new TimeSeries(array('m','family ('.$key.') sales'));
            // $tm->get_values();
            // $tm->save_values();
            // $tm=new TimeSeries(array('y','family ('.$key.') sales'));
            // $tm->get_values();
            // $tm->save_values();
            // $tm=new TimeSeries(array('q','family ('.$key.') sales'));
            // $tm->get_values();
            // $tm->save_values();
            $tm=new TimeSeries(array('m','family ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('y','family ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('q','family ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
        }
    }
    foreach($to_update['departments'] as $key=>$value) {
        $product=new Department($key);
        $product->load('sales');
        if (false) {
            $tm=new TimeSeries(array('m','department ('.$key.') sales'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('y','department ('.$key.') sales'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('q','department ('.$key.') sales'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('m','department ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('y','department ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('q','department ('.$key.') profit'));
            $tm->get_values();
            $tm->save_values();
        }
    }
    foreach($to_update['stores'] as $key=>$value) {
        $product=new Store($key);
        $product->load('sales');
        if (false) {
            $tm=new TimeSeries(array('m','store ('.$key.') sales'));
            $tm->to_present=true;
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('y','store ('.$key.') sales'));
            $tm->to_present=true;
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('q','store ('.$key.') sales'));
            $tm->to_present=true;
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('m','store ('.$key.') profit'));
            $tm->to_present=true;
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('y','store ('.$key.') profit'));
            $tm->to_present=true;
            $tm->get_values();
            $tm->save_values();
            $tm=new TimeSeries(array('q','store ('.$key.') profit'));
            $tm->to_present=true;
            $tm->get_values();
            $tm->save_values();
        }
    }
    foreach($to_update['parts'] as $key=>$value) {
        $product=new Part('sku',$key);
        $product->load('sales');
    }

    printf("updated P:%d F%d D%d S%d\n"
           ,count($to_update['products'])
           ,count($to_update['families'])
           ,count($to_update['departments'])
           ,count($to_update['stores'])

          );
}



function is_same_address($data) {
    $address1=$data['address_data'];
    $address2=$data['shipping_data'];
    unset($address1['telephone']);
    unset($address2['telephone']);
    unset($address2['email']);
    unset($address1['company']);
    unset($address2['company']);
    unset($address1['name']);
    unset($address2['name']);
    //  print_r($address1);
    //print_r($address2);

    if ($address1==$address2)
        return true;
    else
        return false;







}






?>
