<?php
include_once('app_files/db/dns.php');
include_once('class.Image.php');



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

require_once 'common_functions.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once 'conf/conf.php';
setlocale(LC_MONETARY, 'en_GB.UTF-8');


if(!isset($_REQUEST['id'])){
  $id=-1;
}else
  $id=$_REQUEST['id'];


if(isset($_REQUEST['size']) and preg_match('/^large|small|thumbnail|tiny$/',$_REQUEST['size']))
$size=$_REQUEST['size'];
else
$size='original';




$sql=sprintf("select * from `Image Dimension` where `Image Key`=%d",$id);
$result = mysql_query($sql);
if($row=mysql_fetch_array($result, MYSQL_ASSOC)){

//print_r($row);
 
 header('Content-type: image/jpeg');
  header('Content-Disposition: inline; filename='.$row['Image Original Filename']);
  //readfile($row['Attachment Filename']);
// echo  $row['Image Data'];  
 // var_dump(  $row) ;

//exit;

if($size=='original'){
    echo $row['Image Data'];
}elseif($size=='large'){
    if(!$row['Image Large Data'])
         echo $row['Image Data'];
    else
        echo $row['Image Large Data'];
}elseif($size=='small'){
    if(!$row['Image Small Data'])
         echo $row['Image Data'];
    else
        echo  $row['Image Small Data'] ;
  }elseif($size=='thumbnail' or $size=='tiny'){
  echo  $row['Image Thumbnail Data'];  
  
  }else{
   echo $row['Image Data'];
  
  }

  
}else{
  
$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 $yui_path.'assets/skins/sam/autocomplete.css',
		 'common.css',
		 'container.css',
		 'button.css',
		 'table.css',
		 'css/dropdown.css'
		 );
$js_files=array(
		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'dragdrop/dragdrop-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container-min.js',
		$yui_path.'menu/menu-min.js',
		'js/php.default.min.js',
		'js/common.js',
		
		'js/dropdown.js'
		);
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);
   $smarty->display('forbidden.tpl');
   
}

?>
