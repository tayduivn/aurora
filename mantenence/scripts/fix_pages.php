<?php
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
include_once '../../app_files/db/dns.php';
include_once '../../class.Department.php';
include_once '../../class.Family.php';
include_once '../../class.Product.php';
include_once '../../class.Supplier.php';
include_once '../../class.Part.php';
include_once '../../class.Store.php';
include_once '../../class.Customer.php';
include_once '../../class.Site.php';
include_once '../../class.Image.php';

error_reporting(E_ALL);




date_default_timezone_set('UTC');

$con=@mysql_connect($dns_host,$dns_user,$dns_pwd );

if (!$con) {print "Error can not connect with database server\n";exit;}
$db=@mysql_select_db($dns_db, $con);
if (!$db) {print "Error can not access the database\n";exit;}


require_once '../../common_functions.php';
require_once '../../common_detect_agent.php';
mysql_query("SET time_zone ='+0:00'");
mysql_query("SET NAMES 'utf8'");
require_once '../../conf/conf.php';

chdir('../../');

//$sql="select * from kbase.`Country Dimension`";
//$result=mysql_query($sql);
//while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
//print "cp ../../examples/_countries/".strtolower(preg_replace('/\s/','_',$row['Country Name']))."/ammap_data.xml ".$row['Country Code'].".xml\n";
//}
//exit;

$sql="select * from `Page Store Dimension` PS  left join `Page Dimension` P on (P.`Page Key`=PS.`Page Key`) where  `Page Site Key`>0 and `Page Store Section` not in ('Login','Client Section','Registration'); ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {


if(in_array($row['Page Store Section'],array('Login','Client Section','Registration'))){
continue;
}
	$site=new Site($row['Page Site Key']);
	$page=new Page($row['Page Key']);

	//$url=$row['Page URL'];

	
	$url=$site->data['Site URL'].'/'.strtolower($row['Page Code']);
	print $row['Page Site Key']." $url\n";
	$sql=sprintf("update `Page Dimension` set `Page URL`=%s where `Page Key`=%d",prepare_mysql($url),$row['Page Key']);
	
print "$sql\n";
	//mysql_query($sql);
	
}	


$sql="select * from `Page Redirection Dimension`  ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

//	$page=new Page($row['Page Target Key']);
//$redirection=preg_replace('/www\.ancietwisdom\.biz/','www.ancientwisdom.biz',$row['Page Target URL']);
$tmp=_trim($row['Source File']);

	$sql=sprintf("update `Page Redirection Dimension` set `Source File`=%s where `Page Redirection Key`=%d",prepare_mysql($tmp),$row['Page Redirection Key']);
	

	mysql_query($sql);
	
}	



$sql="select * from `Page Redirection Dimension`  ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

	$page=new Page($row['Page Target Key']);
//$redirection=preg_replace('/www\.ancietwisdom\.biz/','www.ancientwisdom.biz',$row['Page Target URL']);


	$sql=sprintf("update `Page Redirection Dimension` set `Page Target URL`=%s where `Page Redirection Key`=%d",prepare_mysql($page->data['Page URL']),$row['Page Redirection Key']);
	

	mysql_query($sql);
	
}	

exit;



exit;




exit;

$sql="select * from `Site Dimension`   ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {
	$site=new Site($row['Site Key']);

	$site->update_footers($site->data['Site Default Footer Key']);
	$site->update_headers($site->data['Site Default Header Key']);

}






$sql="select * from `Page Store Dimension` PS  left join `Page Dimension` P on (P.`Page Key`=PS.`Page Key`) where PS.`Page Key`=2205  ";

$sql="select * from `Page Store Dimension` PS  left join `Page Dimension` P on (P.`Page Key`=PS.`Page Key`)   ";
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

	$site=new Site($row['Page Site Key']);
	$page=new Page($row['Page Key']);

	$url=$row['Page URL'];

	$url=preg_replace('|^http\:\/\/|','',$url);
	$url=preg_replace('/ancietwisdom/','ancientwisdom',$url);



	//$url=preg_replace('/^www.aw-geschenke.com/','',$url);

	if (preg_match('/^forms\//',$url)) {
		$url=$site->data['Site URL'].'/'.$url;

	}



	if (! (preg_match('/\.(php|html)$/',$url) or preg_match('/\.php/',$url) ) ) {
		$url=$url.'/index.php';
	}

	if (!preg_match('/^www/',$url)) {
		//$url=$site->data['Site URL'].'/'.$url;
	}
	$url=preg_replace('|^\/|','',$url);

	$url=str_replace('//','/',$url);
	$sql=sprintf("update `Page Dimension` set `Page URL`=%s where `Page Key`=%d",prepare_mysql($url),$row['Page Key']);
	//print "$sql\n";
	

	mysql_query($sql);


	if ($row['Page Store Section']=='Department Catalogue' ) {
		$department=new Department($row['Page Parent Key']);
		if ($department->id) {
			$sql=sprintf("update `Page Store Dimension` set `Page Parent Code`=%s where `Page Key`=%d",
				prepare_mysql($department->data['Product Department Code']),
				$row['Page Key']);
			mysql_query($sql);
		}
	}
	if ($row['Page Store Section']=='Family Catalogue' ) {
		$family=new Family($row['Page Parent Key']);
		if ($family->id) {
			$sql=sprintf("update `Page Store Dimension` set `Page Parent Code`=%s where `Page Key`=%d",
				prepare_mysql($family->data['Product Family Code']),
				$row['Page Key']);
			mysql_query($sql);
		}
		$sql=sprintf("update `Page Store Dimension` set `Number See Also Links`=%d where `Page Key`=%d",
			$site->data['Site Default Number See Also Links'],
			$row['Page Key']);
		mysql_query($sql);

		$department=new Department($family->data['Product Family Main Department Key']);
		if ($department->id) {
			$parent_pages_keys=$department->get_pages_keys();
			foreach ($parent_pages_keys as $parent_page_key) {
				$page->add_found_in_link($parent_page_key);
				break;
			}
		}



		//print $sql;
	}else {
		$sql=sprintf("update `Page Store Dimension` set `Number See Also Links`=%d where `Page Key`=%d",
			0,
			$row['Page Key']);
		mysql_query($sql);

	}


$page->get_data('id',$page->id);

	$page->update_see_also();
	$page->update_number_found_in();
	//$page->update_preview_snapshot('aw');

	$old_url=$page->data['Page URL'];
	$sql=sprintf("update `Page Dimension` set `Page URL`=%s where `Page Key`=%d",
		prepare_mysql($site->data['Site URL'].'/'.strtolower($page->data['Page Code'])),
		$page->id);
	mysql_query($sql);
	$page->get_data('id',$page->id);

//print_r($page);

	$redirect_key=$page->add_redirect($old_url);
	if($redirect_key){
	$redirect_data=$page->get_redirect_data($redirect_key);
	//print_r($redirect_data);
	//print $redirect_data['Source File'];
	if(preg_match('/\.html$/',$redirect_data['Source File'])){
		$_source=preg_replace('/\.html$/','.php',$redirect_data['Source']);
		$page->add_redirect($_source);
	}elseif(preg_match('/\.php$/',$redirect_data['Source File'])){
		$_source=preg_replace('/\.php$/','.html',$redirect_data['Source']);
		$page->add_redirect($_source);
	}
	}
	/*
	
//print "old:::  $url $old_url \n";
	if($url=preg_match('/^www.aw-geschenke.com/',$old_url)){
		$sql=sprintf("update `Page Redirection Dimension` set `Can Upload`='Yes' where `Page Redirection Key`=%d", $redirect_key);
		print "$sql\n";
		mysql_query($sql);
	}
	if($url=preg_match('/^www.ancientwisdom.biz/',$old_url)){
		$sql=sprintf("update `Page Redirection Dimension` set `Can Upload`='Yes' where `Page Redirection Key`=%d", $redirect_key);
//print "$sql\n";
		mysql_query($sql);
	}
	if($url=preg_match('/^www.aw-cadeux.com/',$old_url)){
		$sql=sprintf("update `Page Redirection Dimension` set `Can Upload`='Yes' where `Page Redirection Key`=%d", $redirect_key);
//print "$sql\n";
		mysql_query($sql);
	}
	if($url=preg_match('/^www.aw-regali.com/',$old_url)){
		$sql=sprintf("update `Page Redirection Dimension` set `Can Upload`='Yes' where `Page Redirection Key`=%d", $redirect_key);
//print "$sql\n";
		mysql_query($sql);
	}
	if($url=preg_match('/^www.aw-podarki.com/',$old_url)){
		$sql=sprintf("update `Page Redirection Dimension` set `Can Upload`='Yes' where `Page Redirection Key`=%d", $redirect_key);
//print "$sql\n";
		mysql_query($sql);
	}

*/
	if($redirect_key){
		
		
		//$page->upload_htaccess($redirect_key);

	//sleep ( 1 );

	}
	print $page->id."\n";


}




?>
