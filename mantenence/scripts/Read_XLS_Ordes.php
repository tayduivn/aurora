<?


error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
include_once('../../app_files/db/dns.php');
include_once('../../classes/Department.php');
include_once('../../classes/Family.php');
include_once('../../classes/Product.php');
include_once('../../classes/Supplier.php');
include_once('../../classes/Order.php');
include_once('local_map.php');

include_once('map_order_functions.php');


require_once 'MDB2.php';            // PEAR Database Abstraction Layer
require_once '../../common_functions.php';


$db =& MDB2::factory($dsn);       
if (PEAR::isError($db)){echo $db->getMessage() . ' ' . $db->getUserInfo();}

$db->setFetchMode(MDB2_FETCHMODE_ASSOC);  
$db->query("SET time_zone ='UTC'");
$db->query("SET NAMES 'utf8'");
$PEAR_Error_skiptrace = &PEAR::getStaticProperty('PEAR_Error','skiptrace');$PEAR_Error_skiptrace = true;// Fix memory leak
require_once '../../myconf/conf.php';           
date_default_timezone_set('Europe/London');

$tmp_directory='/tmp/';

$old_mem=0;



$outstock_norecord=array('7927'=>true);
$partners=array('7927'=>true,'10'=>true);

//mb_ucwords('st hellen');
//exit;
$update_all=false;
$contador=1;
$do_refunds=false;
$correct_partner=true;
$force_update=true;


$orders_array_full_path = glob("/mnt/*/Orders/86403.xls");
$orders_array_full_path=array_reverse($orders_array_full_path);

if(count($orders_array_full_path)==0)
  exit;

foreach($orders_array_full_path as $key=>$order){
  $tmp=str_replace('.xls','',$order);
  $tmp=preg_replace('/.*rders\//i','',$tmp);
  $orders_array[]=$tmp;
}




$good_files=array();
$good_files_number=array();

foreach($orders_array as $order_index=>$order){
  if(preg_match('/^\d{4,5}$/i',$order)){
    $good_files[]=$orders_array_full_path[$order_index];
    $good_files_number[]=$order;


  }

}


foreach($orders_array as $order_index=>$order){
  if(preg_match('/^\d{4,5}r$|^\d{4,5}ref$|^\d{4,5}\s?refund$|^\d{4,5}rr$|^\d{4,5}ra$|^\d{4,5}r2$|^\d{4,5}.2ref$/i',$order)){
     $good_files[]=$orders_array_full_path[$order_index];
    $good_files_number[]=$order;
  }

}




//include_once('z.php');

$cvs_repo='/data/orders_data/';


foreach($good_files_number as $order_index=>$order){

  $updated=false;

  $is_refund=false;
  $act_data=array();
  $map=array();
  if(!preg_match('/^\d{4,5}$/i',$order)){
    $is_refund=true;
  }
  $filename=$good_files[$order_index];
  print "$filename\n";

  $filedate=filemtime($filename);
  $filedatetime=date("Y-m-d H:i:s",strtotime('@'.$filedate));
  $just_file=preg_replace('/.*\//i','',$filename);
  $directory=preg_replace("/$just_file$/",'',$filename);
  
  $sql=sprintf("select * from orders_data.order_data where  `filename`=%s",prepare_mysql($filename));
  $res = $db->query($sql); 
  if ($row=$res->fetchRow()) {
    $sql=sprintf("update orders_data.order_data set last_checked=NOW(),date=%s,timestamp=%d where id=%d",
		 prepare_mysql($filedatetime)
		 ,$filedate
		 ,$row['id']);
    $db->exec($sql);
    
    $date_read=$row['timestamp'];
    if($filedate>$date_read or $force_update){
      $random=mt_rand();
      $tmp_file=$tmp_directory.$order."_$random.xls";
      copy($filename, $tmp_file);// copy to local directory
      $checksum=md5_file($tmp_file);
      
      if($checksum!=$row['checksum'] or $force_update){
	$csv_file=$tmp_directory.$order."_$random.csv";
	exec('/usr/local/bin/xls2csv    -s cp1252   -d 8859-1   '.$tmp_file.' > '.$csv_file);
	$handle_csv = fopen($csv_file, "r");
	unlink($tmp_file);
	copy($csv_file,$row['filename_cvs'] );
	$handle_csv = fopen($csv_file, "r");
	unlink($csv_file);
	$sql=sprintf("update orders_data.order_data set last_read=NOW() where id=%d",$row['id']);
	$db->exec($sql);
	$updated=true;
	$id =$row['id'];
      }



    }
  }else{//new
    $random=mt_rand();
    $tmp_file=$tmp_directory.$order."_$random.xls";
    copy($filename, $tmp_file);// copy to local directory
    $checksum=md5_file($tmp_file);
    $csv_file=$tmp_directory.$order."_$random.csv";
    exec('/usr/local/bin/xls2csv    -s cp1252   -d 8859-1   '.$tmp_file.' > '.$csv_file);
    $handle_csv = fopen($csv_file, "r");
    unlink($tmp_file);
    
    $sql=sprintf("insert into orders_data.order_data (directory,filename,checksum,date,timestamp,last_checked,last_read) values (%s,%s,%s,%s,%s,NOW(),NOW())"
		 ,prepare_mysql($directory)
		 ,prepare_mysql($filename)
		 ,prepare_mysql($checksum)
		 ,prepare_mysql($filedatetime)
		 ,prepare_mysql($filedate)
		 );
    $db->exec($sql);
    $id = $db->lastInsertID();
    
    $cvs_filename=sprintf("%06d.csv",$id);
    copy($csv_file,$cvs_repo.$cvs_filename );
    $handle_csv = fopen($csv_file, "r");
    unlink($csv_file);
    
    $sql=sprintf("update orders_data.order_data set filename_cvs=%s where id=%d",prepare_mysql($cvs_repo.$cvs_filename),$id);
    $db->exec($sql);
    $updated=true;
    
  }


  if($updated){

    $map_act=$_map_act;
      $map=$_map;
      $y_map=$_y_map;
      if($order<18803){// Change map if the orders are old
	$y_map=$_y_map_old;
	foreach($_map_old as $key=>$value)
	  $map[$key]=$value;
      }
      $prod_map=$y_map;
      if($order==53378){
	$prod_map['no_price_bonus']=true;
	$prod_map['no_reorder']=true;
	$prod_map['bonus']=11;
      }

      list($header,$products )=read_records($handle_csv,$prod_map,$number_header_rows);
      $_header=serialize($header);
      $_products=serialize($products);
      $checksum_header= md5($_header);
      $checksum_products= md5($_products);
      
      $sql=sprintf("update orders_data.order_data set header=%s ,checksum_header=%s,products=%s,checksum_prod=%s where id=%d"

		   ,prepare_mysql(mb_convert_encoding($_header, "UTF-8", "ISO-8859-1,UTF-8"))

		   ,prepare_mysql($checksum_header)
		   ,prepare_mysql(mb_convert_encoding($_products, "UTF-8", "ISO-8859-1,UTF-8"))
		   ,prepare_mysql($checksum_products)
		   ,$id);
      // print $sql;
    $db->exec($sql);


  }

}






