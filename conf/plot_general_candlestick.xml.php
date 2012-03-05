<?php
chdir("../");
include_once 'common.php';
if (!isset($_REQUEST['tipo'])) {

	exit;
}

$tipo=$_REQUEST['tipo'];
switch ($tipo) {

case('number_of_customers'):
	if (!isset($_REQUEST['store_key'])) {
		exit;
	}

	$sql=sprintf("select `Store Name`,`Store Code`,`Store Currency Code` from `Store Dimension` where `Store Key` in (%s)",addslashes($_REQUEST['store_key']));
	$res=mysql_query($sql);
	$graphs_data=array();
	$gid=0;
	while ($row=mysql_fetch_assoc($res)) {
		$graphs_data[]=array(
			'gid'=>$gid,
			'label'=>_('Active Customers').":",
			'title'=>$row['Store Name'].': '._('Number of Active Customers'),
			'csv_args'=>'tipo=number_of_customers&store_key='.$_REQUEST['store_key']

		);
		$gid++;
	}
	break;
case('part_stock_history'):
	if (!isset($_REQUEST['parent_key'])  or !isset($_REQUEST['parent'])  or !isset($_REQUEST['output'])  ) {
		exit;
	}
$output=$_REQUEST['output'];

if(!in_array($output,array('stock','value'))){
return;
}

	$parent=$_REQUEST['parent'];
	$parent_key=$_REQUEST['parent_key'];
	$graphs_data=array();
	$gid=0;
	if ($parent=='part') {

		$sql=sprintf("select ISF.`Location Key`,`Location Code` from `Inventory Spanshot Fact` ISF left join `Location Dimension` L on (L.`Location Key`=ISF.`Location Key`) where `part SKU` =%d group by `Location Key` ",$parent_key);
		$res=mysql_query($sql);


		while ($row=mysql_fetch_assoc($res)) {
			$graphs_data[]=array(
				'gid'=>$gid,
				'label'=>_('Stock').":",
				'title'=>$row['Location Code'],
				'short_title'=>$row['Location Code'],
				'csv_args'=>'tipo=part_location_stock_history&output='.$output.'&location_key='.$row['Location Key'].'&part_sku='.$parent_key

			);
			$gid++;
		}

		if ($gid>1 ) {
			$all_locations=array(
				'gid'=>$gid,
				'label'=>_('Stock').":",
				'title'=>_('All locations'),
								'short_title'=>_('All locations'),

				'csv_args'=>'tipo=part_location_stock_history&output='.$output.'&location_key=0&part_sku='.$parent_key

			);


			array_unshift($graphs_data, $all_locations);
		}

	}else {
		$graphs_data[]=array(
			'gid'=>$gid,
			'label'=>_('Stock Value').":",
			'title'=>_('Warehouse'),
			'short_title'=>_('Warehouse'),

			'csv_args'=>'tipo=warehouse_stock_history&warehouse='.$parent_key

		);
		$gid++;

	}

	break;
}


$smarty->assign('locale_data',localeconv());

$smarty->assign('graphs_data',$graphs_data);

//$smarty->assign('data_args',$data_args);

$smarty->display('plot_general_candlestick.xml.tpl');
?>
