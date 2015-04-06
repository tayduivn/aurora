<?php
/*

 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2011, Inikoo

 Version 2.0
*/

require_once 'common.php';
//require_once 'stock_functions.php';
require_once 'class.Product.php';
require_once 'class.Department.php';
require_once 'class.Family.php';

require_once 'class.Order.php';
require_once 'class.Location.php';
require_once 'class.PartLocation.php';
//require_once 'common_functions.php';
require_once 'ar_common.php';

if (!isset($_REQUEST['tipo'])) {
	$response=array('state'=>405,'msg'=>'Non acceptable request (t)');
	echo json_encode($response);
	exit;
}

$tipo=$_REQUEST['tipo'];
switch ($tipo) {
case('page_list'):
	page_list();
	break;
case('deal_list'):
	deal_list();
	break;
case('campaign_list'):
	campaign_list();
	break;	
case('part_list'):
	part_list();
	break;
case('customer_list'):
	customer_list();
	break;	
case('department_list'):
	department_list();
	break;
case('area_list'):
	area_list();
	break;
case('family_list'):
	family_list();
	break;
case('product_list'):
	product_list();
	break;
case('world_regions_list'):
	world_region_list();
	break;
case('country_list'):
	country_list();
	break;
case('postal_codes_list'):
	postal_code_list();
	break;
case('towns_list'):
	town_list();
	break;
case('active_staff_list'):
	active_staff_list();
	break;
case('store_list'):
	store_list();
	break;
case('category_list'):
	category_list();
	break;
case('supplier_list'):
	supplier_list();
	break;
default:

	$response=array('state'=>404,'msg'=>_('Operation not found'));
	echo json_encode($response);

}


function world_region_list() {

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='wregion_code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='wregion_code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';




	$where=sprintf('where `World Region Code`!="UNKN"    ');


	$filter_msg='';
	$wheref='';


	if ($f_field=='wregion_code' and $f_value!='')
		$wheref.=" and  `World Region Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='wregion_code' and $f_value!='')
		$wheref.=" and  `Continent Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='wregion_name' and $f_value!='')
		$wheref.=" and  `World Region` like '".addslashes($f_value)."%'";


	$sql="select count(Distinct  `World Region Code`) as total from kbase.`Country Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(Distinct  `World Region Code`) as total from kbase.`Country Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Region','Regions',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {

	case('wregion_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any world region with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('regions with code like')." <b>$f_value</b>)";
		break;
	case('continent_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any continent with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('continents with code like')." <b>$f_value</b>)";
		break;
	}







	$_order=$order;
	$_dir=$order_direction;


	if ($order=='wregion_code' )
		$order='`World Region Code`';
	else
		$order='`World Region`';







	$adata=array();
	$sql="select group_concat(concat('<img src=\"art/flags/',lower(`Country 2 Alpha Code`),'.gif\"> ') separator ' ') as flags, count(*) as Countries,sum(`Country GNP`) as GNP,sum(`Country Population`) as Population, `World Region`,`World Region Code` from kbase.`Country Dimension` $where $wheref group by `World Region Code` order by $order $order_direction  limit $start_from,$number_results;";

	// print $sql;
	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {
		$adata[]=array(
			'wregion_name'=>$row['World Region'],
			'wregion_code'=>$row['World Region Code'],
			'countries'=>number($row['Countries']),
			'flags'=>$row['flags']
		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}
function area_list() {

	global $user;

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	if (isset( $_REQUEST['warehouse_key']))$warehouse_key=$_REQUEST['warehouse_key'];
	else $warehouse_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	// if (!in_array($store_key,$user->stores)) {
	//     $where=sprintf('where false ');
	//  } else {
	$where=sprintf('where `Warehouse Key`=%d',$warehouse_key);
	//   }




	$filter_msg='';
	$wheref='';


	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Warehouse Area Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Warehouse Area Name` like '".addslashes($f_value)."%'";


	$sql="select count(DISTINCT `Warehouse Area Name`) as total from `Warehouse Area Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Warehouse Area Name`) as total from `Warehouse Area Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Area','Areas',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any area with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('areas with code like')." <b>$f_value</b>)";
		break;
	case('name'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any area with name")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('areas with name like')." <b>$f_value</b>)";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Warehouse Area Name`';
	else
		$order='`Warehouse Area Code`';





	$adata=array();
	$sql="select  `Warehouse Area Key`, `Warehouse Area Name`,`Warehouse Area Code` from `Warehouse Area Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'key'=>$row['Warehouse Area Key'],
			'name'=>$row['Warehouse Area Name'],
			'code'=>$row['Warehouse Area Code'],


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}
function department_list() {

	global $user;

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else$store_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Product Department Store Key`=%d',$store_key);
	}




	$filter_msg='';
	$wheref='';


	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Product Department Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Product Department Name` like '".addslashes($f_value)."%'";


	$sql="select count(DISTINCT `Product Department Name`) as total from `Product Department Dimension` $where $wheref  ";
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Product Department Name`) as total from `Product Department Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Department','Departments',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with code like')." <b>$f_value</b>";
		break;
	case('name'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with name like')." <b>$f_value</b>";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Product Department Name`';
	else
		$order='`Product Department Code`';





	$adata=array();
	$sql="select  `Product Department Key`, `Product Department Name`,`Product Department Code` from `Product Department Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'key'=>$row['Product Department Key'],
			'name'=>$row['Product Department Name'],
			'code'=>$row['Product Department Code'],


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function customer_list() {

	global $user;

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else$store_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Customer Store Key`=%d',$store_key);
	}




	$filter_msg='';
	$wheref='';


	if ($f_field=='id' and $f_value!='')
		$wheref.=" and  `Customer ID` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Customer Name` like '".addslashes($f_value)."%'";


	$sql="select count(*) as total from `Customer Dimension` $where $wheref  ";
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from `Customer Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Customer','Customers',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with code like')." <b>$f_value</b>";
		break;
	case('name'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with name like')." <b>$f_value</b>";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Customer Name`';
	else
		$order='`Customer Key`';

	$adata=array();
	$sql="select  `Customer Key`, `Customer Name` from `Customer Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";

	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'key'=>$row['Customer Key'],
			'name'=>$row['Customer Name'],
			'formated_id'=>sprintf("%05d",$row['Customer Key'])


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}


function part_list() {

	global $user;

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=0;
	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=20;
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order='formated_sku';
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir='';
	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field='sku';

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value='';

	if (isset( $_REQUEST['store_key']))
		$store_key=$_REQUEST['store_key'];
	else
		$store_key='';


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;



	$elements=array('InUse'=>1,'NotInUse'=>0);
	
	
	if (isset( $_REQUEST['elements_InUse'])) {
		$elements['InUse']=$_REQUEST['elements_InUse'];
	}
	
	if (isset( $_REQUEST['elements_NotInUse'])) {
		$elements['NotInUse']=$_REQUEST['elements_NotInUse'];
	}
	


	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';


	/*
    if (!in_array($store_key,$user->stores)) {
        $where=sprintf('where false ');
    } else {
        $where=sprintf('where `Product Family Store Key`=%d',$store_key);

    }
  */

	$where='where true ';



  	$_elements='';
		$elements_count=0;
		foreach ($elements as $_key=>$_value) {
			if ($_value) {
				$elements_count++;

				if ($_key=='InUse') {
					$_key='In Use';
				}else {
					$_key='Not In Use';
				}

				$_elements.=','.prepare_mysql($_key);
			}
		}
		$_elements=preg_replace('/^\,/','',$_elements);
		if ($elements_count==0) {
			$where.=' and false' ;
		} elseif ($elements_count==1) {
			$where.=' and `Part Status` in ('.$_elements.')' ;
		}
  

	$filter_msg='';
	$wheref='';
if ($f_field=='reference' and $f_value!='')
		$wheref.=" and  `Part Reference` like '".addslashes($f_value)."%'";
	if ($f_field=='used_in' and $f_value!='')
		$wheref.=" and  `Part Currently Used In` like '%".addslashes($f_value)."%'";
	elseif ($f_field=='description' and $f_value!='')
		$wheref.=sprintf(' and  `Part Unit Description` like  REGEXP "[[:<:]]%s" ',addslashes($f_value));
	elseif ($f_field=='supplied_by' and $f_value!='')
		$wheref.=" and  `Part XHTML Currently Supplied By` like '".addslashes($f_value)."%'";
	elseif ($f_field=='sku' and $f_value!='')
		$wheref.=" and  `Part SKU`='".addslashes($f_value)."'";

	$sql="select count(DISTINCT `Part SKU`) as total from `Part Dimension` $where $wheref  ";
	//print $sql;
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Part SKU`) as total from `Part Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('part','parts',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
	elseif ($total_records)
		$rtext_rpp=' ('._("Showing all").')';
	else
		$rtext_rpp='';
	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('sku'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any part with SKU")." <b>".sprintf("SKU%05d",$f_value)."</b> ";
			break;

		case('used_in'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any part used in ")." <b>".$f_value."*</b> ";
			break;
		case('suppiled_by'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any part supplied by ")." <b>".$f_value."*</b> ";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any part with description like ")." <b>*".$f_value."*</b> ";
			break;
		case('reference'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any part with reference like ")." <b>".$f_value."*</b> ";
			break;	
		}
	}
	elseif ($filtered>0) {


		switch ($f_field) {
		case('sku'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('parts with SKU')." <b>".sprintf("SKU%05d",$f_value)."</b>";
			break;

		case('used_in'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('parts used in')." <b>*".$f_value."*</b>";
			break;
		case('supplied_by'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('parts supplied by')." <b>".$f_value."*</b>";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('parts with description like')." <b>".$f_value."*</b>";
			break;
		case('reference'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('parts with reference like')." <b>".$f_value."*</b>";
			break;	
		}
	}
	else
		$filter_msg='';








	$_order=$order;
	$_dir=$order_direction;

if ($order=='reference')
		$order='`Part Reference`';
elseif ($order=='status')
		$order='`Part Status`';
	elseif ($order=='description')
		$order='`Part Unit Description`';
	else
		$order='`Part SKU`';





	$adata=array();
	$sql="select  `Part Reference`,`Part Status`,`Part SKU`,`Part Currently Used In`,`Part Unit Description` from `Part Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";
	//print $sql;exit;

	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'sku'=>$row['Part SKU'],
			'reference'=>$row['Part Reference'],

			'formated_sku'=>sprintf('%05d',$row['Part SKU']),
			'description'=>$row['Part Unit Description'],
			'used_in'=>$row['Part Currently Used In'],
			'status'=>($row['Part Status']=='In Use'?'':_('No keeped')),


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function family_list() {

	global $user;

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=0;
	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=20;
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order='code';
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir='';
	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field='code';

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value='';

	if (isset( $_REQUEST['store_key']))
		$store_key=$_REQUEST['store_key'];
	else
		$store_key='';


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;




	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Product Family Store Key`=%d',$store_key);

	}



	$filter_msg='';
	$wheref='';
	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Product Family Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Product Family Name` like '".addslashes($f_value)."%'";

	$sql="select count(DISTINCT `Product Family Name`) as total from `Product Family Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Product Family Name`) as total from `Product Family Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Family','Families',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with code like')." <b>$f_value</b>";
		break;
	case('name'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with name like')." <b>$f_value</b>";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Product Family Name`';
	else
		$order='`Product Family Code`';





	$adata=array();
	$sql="select  `Product Family Key`, `Product Family Name`,`Product Family Code`, `Product Family Store Key` from `Product Family Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";
	//print $sql;

	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {
		$adata[]=array(
			'key'=>$row['Product Family Key'],
			'name'=>$row['Product Family Name'],
			'code'=>$row['Product Family Code'],
			'store_key'=>$row['Product Family Store Key']

		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function product_list() {

	global $user;

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=0;
	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=20;
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order='code';
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir='';
	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field='code';

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value='';

	if (isset( $_REQUEST['store_key']))
		$store_key=$_REQUEST['store_key'];
	else
		$store_key='';


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;




	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Product Store Key`=%d',$store_key);

	}




	$filter_msg='';
	$wheref='';


	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Product Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Product Name` like '".addslashes($f_value)."%'";


	$sql="select count(DISTINCT `Product Name`) as total from `Product Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Product Name`) as total from `Product Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Product','Products',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with code like')." <b>$f_value</b>";
		break;
	case('name'):
		if ($filtered>0  or  ($total==0 and $filtered>0))
			$filter_msg=" ".$total." "._('with name like')." <b>$f_value</b>";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Product Name`';
	else
		$order='`Product Code`';





	$adata=array();
	$sql="select  `Product Name`,`Product Code`,`Product ID` from `Product Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'pid'=>$row['Product ID'],
			'key'=>$row['Product ID'],
			'name'=>$row['Product Name'] ,
			'code'=>$row['Product Code'],


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function country_list() {
	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='wregion_code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';


	$where=sprintf('where true ');


	$filter_msg='';
	$wheref='';


	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Country Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Country Name` like '".addslashes($f_value)."%'";
	elseif ($f_field=='wregion' and $f_value!='')
		$wheref.=" and  `World Region` like '".addslashes($f_value)."%'";
	elseif ($f_field=='wregion_code' and $f_value!='')
		$wheref.=" and  `World Region Code` like '".addslashes($f_value)."%'";

	$sql="select count(*) as total from kbase.`Country Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from kbase.`Country Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Country','Countries',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('country_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any country with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('countries with code like')." <b>$f_value</b>)";
		break;
	case('wregion_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any world region with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('regions with code like')." <b>$f_value</b>)";
		break;
	case('continent_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any continent with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('continents with code like')." <b>$f_value</b>)";
		break;
	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='code')
		$order='`Country Code`';
	elseif ($order=='wregion')
		$order='`World Region`';
	else
		$order='`Country Name`';





	$adata=array();
	$sql="select  `Country Postal Code Format`,`Country Postal Code Regex`,`World Region Code`,`World Region`,`Country GNP`,`Country Population`,`Country Code`,`Country Name`,`Country 2 Alpha Code` from kbase.`Country Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$country_flag=sprintf('<img  src="art/flags/%s.gif" alt="">',strtolower($row['Country 2 Alpha Code']));



		$adata[]=array(

			'name'=>$row['Country Name'],
			'code'=>$row['Country Code'],
			'flag'=>$country_flag,

			'wregion'=>$row['World Region'],



		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function postal_code_list() {
	global $user;
	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='wregion_code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;
	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else$store_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';

	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Customer Store Key`=%d',$store_key);
	}


	$where.=sprintf(' and `Customer Main Postal Code`!="" ');


	$filter_msg='';
	$wheref='';

	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Customer Main Postal Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='country_code' and $f_value!='')
		$wheref.=" and  `Customer Main Country Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='country_name' and $f_value!='')
		$wheref.=" and  `Customer Main Country` like '".addslashes($f_value)."%'";


	// elseif($f_field=='used' and $f_value!='')
	//   $wheref.=sprintf(" and times_used>=%d ",$f_value);

	$sql="select count(DISTINCT `Customer Main Postal Code`) as total from `Customer Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Customer Main Postal Code`) as total from `Customer Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Postal Code','Postal Codes',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('country_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any country with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('countries with code like')." <b>$f_value</b>)";
		break;
	case('wregion_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any world region with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('regions with code like')." <b>$f_value</b>)";
		break;
	case('continent_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any continent with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('continents with code like')." <b>$f_value</b>)";
		break;


	case('postal_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any postal code with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('postal codes with code like')." <b>$f_value</b>)";
		break;
	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='times_used')
		$order='`times_used`';
	else if ($order=='name')
			$order='`Customer Main Country`';
		else
			$order='`Customer Main Postal Code`';





		$adata=array();
	$sql="select  count(*) times_used,`Customer Main Postal Code`,`Customer Main Country 2 Alpha Code`,`Customer Main Country` from `Customer Dimension` $where $wheref  group by `Customer Main Postal Code` order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {
		$country_flag=sprintf('<img  src="art/flags/%s.gif" alt="">',strtolower($row['Customer Main Country 2 Alpha Code']));


		$adata[]=array(

			'name'=>$row['Customer Main Country'],
			'code'=>$row['Customer Main Postal Code'],
			'flag'=>$country_flag,
			'times_used'=>number($row['times_used']),


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function town_list() {
	global $user;
	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='city';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='wregion_code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;
	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else$store_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';


	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Customer Main Town`!="" and  `Customer Store Key`=%d',$store_key);
	}





	$filter_msg='';
	$wheref='';


	if ($f_field=='city' and $f_value!='')
		$wheref.=" and  `Customer Main Town` like '".addslashes($f_value)."%'";

	elseif ($f_field=='country_code' and $f_value!='')
		$wheref.=" and  `Customer Main Country Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='country_name' and $f_value!='')
		$wheref.=" and  `Customer Main Country` like '".addslashes($f_value)."%'";
	//  elseif($f_field=='used' and $f_value!='')
	//   $wheref.=sprintf(" and times_used>=%d ",$f_value);


	$sql="select count(DISTINCT `Customer Main Town`) as total from `Customer Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Customer Main Town`) as total from `Customer Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('City','Cities',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('country_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any country with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('countries with code like')." <b>$f_value</b>)";
		break;
	case('wregion_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any world region with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('regions with code like')." <b>$f_value</b>)";
		break;
	case('continent_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any continent with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('continents with code like')." <b>$f_value</b>)";
		break;


	case('postal_code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any postal code with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('postal codes with code like')." <b>$f_value</b>)";
		break;
	case('city'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any city with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('city with code like')." <b>$f_value</b>)";
		break;
	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='times_used')
		$order='times_used';
	elseif ($order=='name')
		$order='`Customer Main Country`';
	else
		$order='`Customer Main Town`';





	$adata=array();
	$sql="select   count(*) times_used,`Customer Main Country 2 Alpha Code`,`Customer Main Country`,`Customer Main Country 2 Alpha Code`,`Customer Main Town` from `Customer Dimension` $where $wheref group by `Customer Main Town` order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {
		$country_flag=sprintf('<img  src="art/flags/%s.gif" alt="">',strtolower($row['Customer Main Country 2 Alpha Code']));


		$adata[]=array(
			'times_used'=>number($row['times_used']),
			'name'=>$row['Customer Main Country'],
			'city'=>$row['Customer Main Town'],
			'flag'=>$country_flag,
		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}


function deal_list() {

	global $user;

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else$store_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Deal Store Key`=%d',$store_key);
	}




	$filter_msg='';
	$wheref='';


	if ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Deal Name` like '".addslashes($f_value)."%'";
	elseif ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Deal Code` like '".addslashes($f_value)."%'";


	$sql="select count(DISTINCT `Deal Name`) as total from `Deal Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Deal Name`) as total from `Deal Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Offer','Offers',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {

	case('name'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any offer with name")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('offers with name like')." <b>$f_value</b>)";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Deal Name`';
	else
		$order='`Deal Name`';





	$adata=array();
	$sql="select  `Deal Code`,`Deal Key`,`Deal Name`,`Deal Description` from `Deal Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'code'=>$row['Deal Code'],
			'name'=>$row['Deal Name'],
			'description'=>$row['Deal Description'],
			'id'=>$row['Deal Key'],
			'key'=>$row['Deal Key']
			//  'code'=>$row['Product Department Code'],


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function campaign_list() {

	global $user;

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else$store_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Deal Campaign Store Key`=%d',$store_key);
	}




	$filter_msg='';
	$wheref='';


	if ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Deal Campaign Name` like '".addslashes($f_value)."%'";


	$sql="select count(DISTINCT `Deal Campaign Name`) as total from `Deal Campaign Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Deal Campaign Name`) as total from `Deal Campaign Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Campaign','Campaigns',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {

	case('name'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any campaign with name")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('campaigns with name like')." <b>$f_value</b>)";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Deal Campaign Name`';
	else
		$order='`Deal Campaign Name`';





	$adata=array();
	$sql="select  `Deal Campaign Key`,`Deal Campaign Code`,`Deal Campaign Name` from `Deal Campaign Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'name'=>$row['Deal Campaign Name'],
			'code'=>$row['Deal Campaign Code'],
			'id'=>$row['Deal Campaign Key'],
			'key'=>$row['Deal Campaign Key']
		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function page_list() {


	$site_key=$_REQUEST['site_key'];


	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;
	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else$store_key='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';






	// print_r($_SESSION['tables']['families_list']);

	//  print_r($_SESSION['tables']['families_list']);

	$where=sprintf(' where `Page Type`="Store" and `Page Site Key`=%d ',$site_key);


	$filter_msg='';
	$wheref='';

	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Page Code` like '".addslashes($f_value)."%'";
	if ($f_field=='header' and $f_value!='')
		$wheref.=" and  `Page Store Title` like '%".addslashes($f_value)."%'";







	$sql="select count(*) as total from `Page Dimension` P left join `Page Store Dimension` PS on (P.`Page Key`=PS.`Page Key`)  $where $wheref";

	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($result);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from `Page Dimension` P left join `Page Store Dimension` PS on (P.`Page Key`=PS.`Page Key`)   $where ";

		$result=mysql_query($sql);
		if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($result);

	}


	$rtext=number($total_records)." ".ngettext('page','pages',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	elseif ($total_records>0)
		$rtext_rpp=' ('._('Showing all').')';
	else
		$rtext_rpp='';


	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any charge with this name ")." <b>".$f_value."*</b> ";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any charge with description like ")." <b>".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('charges with name like')." <b>".$f_value."*</b>";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('charges with description like')." <b>".$f_value."*</b>";
			break;
		}
	}
	else
		$filter_msg='';

	$_dir=$order_direction;
	$_order=$order;


	if ($order=='title')
		$order='`Page Title`';
	else
		$order='`Page Code`';


	$adata=array();
	$sql="select *  from `Page Dimension`  P left join `Page Store Dimension` PS on (P.`Page Key`=PS.`Page Key`) $where  $wheref  order by $order $order_direction limit $start_from,$number_results    ";

	$res = mysql_query($sql);

	$total=mysql_num_rows($res);

	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {





		$type=$row['Page Store Section'];
		switch ($type) {
		case 'Department Catalogue':
			$type=_('Department');
			break;
		case 'Family Catalogue':
			$type=_('Family');
			break;
		default:
			$type=_('Other');
			break;
		}

		$adata[]=array(
			'key'=>$row['Page Key'],
			'code'=>$row['Page Code'],
			'store_title'=>$row['Page Store Title'],
			'type'=>$type


		);
	}
	mysql_free_result($res);



	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from,
			'records_perpage'=>$number_results,
		)
	);
	echo json_encode($response);
}

function active_staff_list() {

	global $user;

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	if (isset( $_REQUEST['active']))$active=$_REQUEST['active'];
	else $active='Yes';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	$where=sprintf('where `Staff Currently Working`=%s',prepare_mysql($active));





	$filter_msg='';
	$wheref='';


	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Staff Alias` like '".addslashes($f_value)."%'";


	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Staff Name` like '".addslashes($f_value)."%'";


	$sql="select count(DISTINCT `Staff Name`) as total from `Staff Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Staff Name`) as total from `Staff Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Employee','Employees',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("None with alias")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg="$total "._('with alias like')." <b>".$f_value."*</b> ";
		break;
	case('name'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("None with name")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg="$total "._('with name like')." <b>".$f_value."*</b> ";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='``Staff Name`';
	else
		$order='`Staff Alias`';





	$adata=array();
	$sql="select  `Staff Key`, `Staff Name`,`Staff Alias` from `Staff Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";

	//print $sql;
	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'key'=>$row['Staff Key'],
			'name'=>$row['Staff Name'],
			'code'=>$row['Staff Alias'],


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function store_list() {

	global $user;

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=0;
	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=20;
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order='code';
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir='';
	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field='code';

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value='';

	if (isset( $_REQUEST['store_key']))
		$store_key=$_REQUEST['store_key'];
	else
		$store_key='';


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;




	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	$where='where true';

	$filter_msg='';
	$wheref='';
	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Store Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Store Name` like '".addslashes($f_value)."%'";

	$sql="select count(DISTINCT `Store Name`) as total from `Store Dimension` $where $wheref  ";
	//print $sql;
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(DISTINCT `Store Name`) as total from `Store Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Store','Stores',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any family with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('families with code like')." <b>$f_value</b>)";
		break;
	case('name'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any family with name")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('families with name like')." <b>$f_value</b>)";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='name')
		$order='`Store Name`';
	else
		$order='`Store Code`';





	$adata=array();
	$sql="select  `Store Key`, `Store Name`,`Store Code` from `Store Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		$adata[]=array(
			'key'=>$row['Store Key'],
			'name'=>$row['Store Name'],
			'code'=>$row['Store Code'],


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}

function category_list() {

	global $user;

	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	if (isset( $_REQUEST['store_key']))$store_key=$_REQUEST['store_key'];
	else {
		exit('no store');
	}

	if (isset( $_REQUEST['subject']))$subject=$_REQUEST['subject'];
	else {
		exit('no subject');
	}

if (isset( $_REQUEST['branch_type']))$branch_type=$_REQUEST['branch_type'];
	else {
		$branch_type='';
	}

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';



	if (!in_array($store_key,$user->stores)) {
		$where=sprintf('where false ');
	} else {
		$where=sprintf('where `Category Store Key`=%d',$store_key);
	}

     $where.=sprintf(' and `Category Subject`=%s',prepare_mysql($subject));

	if($branch_type!=''){
	     $where.=sprintf(' and `Category Branch Type`=%s',prepare_mysql($branch_type));

	}


	$filter_msg='';
	$wheref='';


	if ($f_field=='tree' and $f_value!='')
		$wheref.=" and  `Category Plain Branch Tree` like '%".addslashes($f_value)."%'";
	elseif ($f_field=='label' and $f_value!='')
		$wheref.=" and  `Category Label` like '".addslashes($f_value)."%'";


	$sql="select count(*) as total from `Category Dimension` $where $wheref  ";
	//print $sql;
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from `Category Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('category','categories',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('tree'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any category with tree like")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('categories with tree like')." <b>$f_value</b>)";
		break;
	case('label'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any category with label")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('categories with name label')." <b>$f_value</b>)";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='label')
		$order='`Category Label`';
	if ($order=='tree')
		$order='`Category Plain Branch Tree`';
	else
		$order='`Category Code`';





	$adata=array();
	$sql="select  * from `Category Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {

		if ($row['Category Label']!=$row['Category Label']) {
			$label=$row['Category Label'].' ('.$row['Category Code'].')';
		}else {
			$label=$row['Category Label'];
		}


		$adata[]=array(
			'key'=>$row['Category Key'],
			'label'=>$label,
			'code'=>$row['Category Code'],
			'tree'=>$row['Category Plain Branch Tree'],
			'subjects'=>number($row['Category Number Subjects'])


		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}


function supplier_list() {
	if (isset( $_REQUEST['sf']))$start_from=$_REQUEST['sf'];
	else $start_from=0;
	if (isset( $_REQUEST['nr']))$number_results=$_REQUEST['nr'];
	else $number_results=20;
	if (isset( $_REQUEST['o'])) $order=$_REQUEST['o'];
	else$order='code';
	if (isset( $_REQUEST['od']))$order_dir=$_REQUEST['od'];
	else$order_dir='';
	if (isset( $_REQUEST['f_field']))$f_field=$_REQUEST['f_field'];
	else$f_field='wregion_code';
	if (isset( $_REQUEST['f_value']))$f_value=$_REQUEST['f_value'];
	else$f_value='';
	if (isset( $_REQUEST['tableid']))$tableid=$_REQUEST['tableid'];
	else$tableid=0;

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';


	$where=sprintf('where true ');


	$filter_msg='';
	$wheref='';


	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Supplier Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Supplier Name` like '".addslashes($f_value)."%'";

	$sql="select count(*) as total from `Supplier Dimension` $where $wheref  ";

	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($res);
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from `Supplier Dimension`  $where   ";
		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($res);
	}


	$rtext=number($total_records)." ".ngettext('Supplier','Suppliers',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	else
		$rtext_rpp="("._('Showing all').")";


	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any supplier with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('suppliers with code like')." <b>$f_value</b>)";
		break;
	case('name'):
		if ($total==0 and $filtered>0)
			$filter_msg=_("There isn't any supplier with name")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg=_('Showing')." $total ("._('suppliers with name like')." <b>$f_value</b>)";
		break;

	}





	$_order=$order;
	$_dir=$order_direction;



	if ($order=='code')
		$order='`Supplier Code`';
	elseif ($order=='name')
		$order='`Supplier Name`';






	$adata=array();
	$sql="select  `Supplier Key`,`Supplier Name`,`Supplier Code` from `Supplier Dimension` $where $wheref  order by $order $order_direction  limit $start_from,$number_results;";


	$res=mysql_query($sql);

	while ($row=mysql_fetch_array($res)) {




		$adata[]=array(
			'key'=>$row['Supplier Key'],

			'name'=>$row['Supplier Name'],
			'code'=>$row['Supplier Code'],




		);

	}
	mysql_free_result($res);

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$total,
			'records_perpage'=>$number_results,
			// 'records_text'=>$rtext,
			// 'records_order'=>$order,
			// 'records_order_dir'=>$order_dir,
			// 'filtered'=>$filtered,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
		)
	);

	echo json_encode($response);
}
