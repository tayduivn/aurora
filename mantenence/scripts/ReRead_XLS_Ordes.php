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



$sql=sprintf("select * from orders_data.order_data ");
$res = $db->query($sql); 
if ($row=$res->fetchRow()) {
  
  print $row['filename']."\n";
  $handle_csv = fopen($row['filename_cvs'], "r");
  
  $sql=sprintf("update orders_data.order_data set last_read=NOW() where id=%d",$row['id']);
  $db->exec($sql);
  
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
    $db->exec($sql);
 }
?>