<?php
require_once 'common.php';
require_once 'ar_common.php';


if (!isset($_REQUEST['tipo'])) {
	$response=array('state'=>405,'resp'=>'Non acceptable request (t)');
	echo json_encode($response);
	exit;
}

$tipo=$_REQUEST['tipo'];
switch ($tipo) {

case('part_in_supplier_product_historic'):
list_part_in_supplier_product_historic();
break;
case ('get_supplier_products_numbers'):
	$data=prepare_values($_REQUEST,array(
			'parent_key'=>array('type'=>'key'),
			'parent'=>array('type'=>'string'),
		));
	get_supplier_products_numbers($data);
	break;
case ('get_history_numbers'):
	$data=prepare_values($_REQUEST,array(
			'subject_key'=>array('type'=>'key'),
			'subject'=>array('type'=>'string'),
		));
	get_history_numbers($data);
	break;
case ('number_supplier_product_transactions_in_interval'):
	$data=prepare_values($_REQUEST,array(
			'supplier_product_pid'=>array('type'=>'key'),
			'from'=>array('type'=>'string'),
			'to'=>array('type'=>'string')
		));
	number_supplier_product_transactions_in_interval($data);
	break;
case('supplier_product_sales_report'):

	list_supplier_product_sales_report();
	break;
case('get_supplier_category_sales_data'):
case('get_supplier_sales_data'):
	$data=prepare_values($_REQUEST,array(
			'parent_key'=>array('type'=>'key'),
			'parent'=>array('type'=>'string'),
			'from'=>array('type'=>'string'),
			'to'=>array('type'=>'string')
		));
	get_supplier_sales_data($data);
	break;
case('is_supplier_product_code'):
	$data=prepare_values($_REQUEST,array(
			'supplier_key'=>array('type'=>'key'),
			'query'=>array('type'=>'string')
		));
	is_supplier_product_code($data);
	break;
case('is_supplier_product_name'):
	$data=prepare_values($_REQUEST,array(
			'supplier_key'=>array('type'=>'key'),
			'query'=>array('type'=>'string')
		));
	is_supplier_product_name($data);
	break;

case('find_supplier'):
	find_supplier();
	break;
case('is_supplier_code'):
	is_supplier_code();
	break;
case('is_product_name'):
	$data=prepare_values($_REQUEST,array(
			'supplier_key'=>array('type'=>'key'),
			'query'=>array('type'=>'string')
		));
	is_product_name($data);
	break;
case('is_product_code'):
	$data=prepare_values($_REQUEST,array(
			'supplier_key'=>array('type'=>'key'),
			'query'=>array('type'=>'string')
		));
	is_product_code($data);
	break;
case('supplier_products'):
	list_supplier_products();
	break;
case('suppliers'):
	list_suppliers();

	break;







case('supplier_categories'):
	list_supplier_categories();
	break;




default:


	$response=array('state'=>404,'resp'=>_('Operation not found'));
	echo json_encode($response);

}

function list_suppliers() {
	global $myconf,$user,$corporate_currency;

	include_once 'common_natural_language.php';


	if (isset( $_REQUEST['parent']))
		$parent=$_REQUEST['parent'];
	else
		$parent='none';

	if (isset( $_REQUEST['parent_key']))
		$parent_key=$_REQUEST['parent_key'];
	else
		$parent_key='';

	if ($parent=='category') {
		$conf=$_SESSION['state']['supplier_categories']['suppliers'];
		$conf_table='supplier_categories';
	}
	elseif ($parent=='none') {
		$conf=$_SESSION['state']['suppliers']['suppliers'];
		$conf_table='suppliers';
	}else {

		exit;
	}


	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];
	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=$conf['nr'];
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];

	if (isset( $_REQUEST['where']))
		$where=$_REQUEST['where'];
	else
		$where=$conf['where'];

	if (isset( $_REQUEST['period']))
		$period=$_REQUEST['period'];
	else
		$period=$conf['period'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	$_SESSION['state'][$conf_table]['suppliers']['order']=$order;
	$_SESSION['state'][$conf_table]['suppliers']['order_dir']=$order_direction;
	$_SESSION['state'][$conf_table]['suppliers']['nr']=$number_results;
	$_SESSION['state'][$conf_table]['suppliers']['sf']=$start_from;
	$_SESSION['state'][$conf_table]['suppliers']['where']=$where;
	$_SESSION['state'][$conf_table]['suppliers']['f_field']=$f_field;
	$_SESSION['state'][$conf_table]['suppliers']['f_value']=$f_value;
	$_SESSION['state'][$conf_table]['suppliers']['period']=$period;


	$_order=$order;
	$_dir=$order_direction;



	if ($user->data['User Type']=='Supplier') {

		if (!count($user->suppliers)) {
			$where='where false';
		} else {
			$where=sprintf('where `Supplier Key` in (%s)',join(',',$user->suppliers));
		}
	} else {
		$where='where true';

	}

	if ($parent=='category') {

		$where.=sprintf(" and `Category Key`=%d ",$parent_key);
		$table=' `Supplier Dimension` S left join `Category Bridge` B on (`Subject Key`=`Supplier Key`)  ' ;
	}else {
		$table=' `Supplier Dimension` S  ' ;

	}


	$wheref='';
	if ($f_field=='code'  and $f_value!='')
		$wheref.=" and `Supplier Code` like '".addslashes($f_value)."%'";
	if ($f_field=='name' and $f_value!='')
		$wheref.=" and  `Supplier Name` like '".addslashes($f_value)."%'";
	elseif ($f_field=='low' and is_numeric($f_value))
		$wheref.=" and lowstock>=$f_value  ";
	elseif ($f_field=='outofstock' and is_numeric($f_value))
		$wheref.=" and outofstock>=$f_value  ";


	$sql="select count(*) as total from $table   $where $wheref";
	//  print $sql;
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from $table $where      ";
		$result=mysql_query($sql);
		if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$row['total']-$total;
		}

	}

	$rtext=number($total_records)." ".ngettext('supplier','suppliers',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	elseif ($total_records>0)
		$rtext_rpp=' ('._('Showing all').')';
	else
		$rtext_rpp='';







	$filter_msg='';

	switch ($f_field) {
	case('code'):
		if ($total==0 and $filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any supplier with code")." <b>$f_value</b>* ";
		elseif ($filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('suppliers with code')." <b>$f_value</b>*)";
		break;
	case('name'):
		if ($total==0 and $filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any supplier with name")." <b>$f_value</b>* ";
		elseif ($filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('suppliers with name')." <b>$f_value</b>*)";
		break;
	case('low'):
		if ($total==0 and $filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any supplier with more than ")." <b>".number($f_value)."</b> "._('low stock products');
		elseif ($filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('Suppliers with')." <b><".number($f_value)."</b> "._('low stock products').")";
		break;
	case('outofstock'):
		if ($total==0 and $filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any supplier with more than ")." <b>".number($f_value)."</b> "._('out of stock products');
		elseif ($filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('Suppliers with')." <b><".number($f_value)."</b> "._('out of stock products').")";
		break;
	}


	$db_period=get_interval_db_name($period);

	if ($order=='code')
		$order='`Supplier Code`';
	elseif ($order=='name')
		$order='`Supplier Name`';
	elseif ($order=='id')
		$order='`Supplier Key`';
	elseif ($order=='location')
		$order='`Supplier Main Location`';
	elseif ($order=='email')
		$order='`Supplier Main XHTML Email`';
	elseif ($order=='products')
		$order='`Supplier Active Supplier Products`';
	elseif ($order=='sales') {
		$order="`Supplier $db_period Acc Parts Sold Amount`";
	}
	elseif ($order=='sold') {
		$order="`Supplier $db_period Acc Parts Sold`";
	}
	elseif ($order=='required') {
		$order="`Supplier $db_period Acc Parts Required`";
	}



	elseif ($order=='delta_sales') {

		if (in_array($period,array('all','3y','three_year'))) {

			$order="`Supplier $db_period Acc Parts Sold Amount`";

		}else {
			$order="((`Supplier $db_period Acc Parts Sold Amount`-`Supplier $db_period Acc 1Yb Parts Sold Amount`)/`Supplier $db_period Acc 1Yb Parts Sold Amount`)";
		}
	}



	elseif ($order=='pending_pos') {
		$order='`Supplier Open Purchase Orders`';

	}
	elseif ($order=='margin') {
		$order="`Supplier $db_period Acc Parts Margin`";

	}
	elseif ($order=='cost') {
		$order="`Supplier $db_period Acc Parts Cost`";


	}elseif ($order=='origin') {
		$order="`Supplier Products Origin Country Code`";
	}elseif ($order=='active_sp') {
		$order="`Supplier Active Supplier Products`";
	}elseif ($order=='no_active_sp') {
		$order="`Supplier Discontinued Supplier Products`";
	}elseif ($order=='delivery_time') {
		$order="`Supplier Average Delivery Days`";
	}elseif ($order=='low') {
		$order="`Supplier Low Availability Products`";
	}elseif ($order=='high') {
		$order="`Supplier Surplus Availability Products`";
	}elseif ($order=='normal') {
		$order="`Supplier Optimal Availability Products`";
	}elseif ($order=='critical') {
		$order="`Supplier Critical Availability Products`";
	}elseif ($order=='outofstock') {
		$order="`Supplier Out Of Stock Products`";
	}elseif ($order=='contact') {
		$order="`Supplier Main Contact Name`";
	}elseif ($order=='tel') {
		$order="`Supplier Main Plain Telephone`";
	}




	elseif ($order=='profit_after_storing') {
		$order="`Supplier $db_period Acc Parts Profit After Storing`";

	}

	elseif ($order=='profit') {
		$order="`Supplier $db_period Acc Parts Profit`";
	}
	elseif ($order=='delta_sales_year0') {$order="(-1*(`Supplier Year To Day Acc Parts Sold Amount`-`Supplier Year To Day Acc 1YB Parts Sold Amount`)/`Supplier Year To Day Acc 1YB Parts Sold Amount`)";}
	elseif ($order=='delta_sales_year1') {$order="(-1*(`Supplier 2 Year Ago Sales Amount`-`Supplier 1 Year Ago Sales Amount`)/`Supplier 2 Year Ago Sales Amount`)";}
	elseif ($order=='delta_sales_year2') {$order="(-1*(`Supplier 3 Year Ago Sales Amount`-`Supplier 2 Year Ago Sales Amount`)/`Supplier 3 Year Ago Sales Amount`)";}
	elseif ($order=='delta_sales_year3') {$order="(-1*(`Supplier 4 Year Ago Sales Amount`-`Supplier 3 Year Ago Sales Amount`)/`Supplier 4 Year Ago Sales Amount`)";}
	elseif ($order=='sales_year1') {$order="`Supplier 1 Year Ago Sales Amount`";}
	elseif ($order=='sales_year2') {$order="`Supplier 2 Year Ago Sales Amount`";}
	elseif ($order=='sales_year3') {$order="`Supplier 3 Year Ago Sales Amount`";}
	elseif ($order=='sales_year4') {$order="`Supplier 4 Year Ago Sales Amount`";}
	elseif ($order=='sales_year0') {$order="`Supplier Year To Day Acc Parts Sold Amount`";}

	//print $order;
	//    elseif($order='used_in')
	//        $order='Supplier Product XHTML Sold As';

	$sql="select *  from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
	//print $sql;


	$result=mysql_query($sql);
	$data=array();
	while ($row=mysql_fetch_array($result, MYSQL_ASSOC) ) {

		$id="<a href='supplier.php?id=".$row['Supplier Key']."'>".$myconf['supplier_id_prefix'].sprintf("%05d",$row['Supplier Key']).'</a>';
		$code="<a href='supplier.php?id=".$row['Supplier Key']."'>".$row['Supplier Code']."</a>";


		$sales=money($row["Supplier $db_period Acc Parts Sold Amount"],$corporate_currency);

		if (in_array($period,array('all','3y','three_year'))) {
			$delta_sales='';
		}else {
			$delta_sales='<span title="'.money($row["Supplier $db_period Acc 1YB Parts Sold Amount"],$corporate_currency).'">'.delta($row["Supplier $db_period Acc Parts Sold Amount"],$row["Supplier $db_period Acc 1YB Parts Sold Amount"]).'</span>';
		}

		$profit=money($row["Supplier $db_period Acc Parts Profit"],$corporate_currency);
		$profit_after_storing=money($row["Supplier $db_period Acc Parts Profit After Storing"],$corporate_currency);
		$cost=money($row["Supplier $db_period Acc Parts Cost"],$corporate_currency);
		$margin=percentage($row["Supplier $db_period Acc Parts Margin"],1);
		$sold=number($row["Supplier $db_period Acc Parts Sold"],0);
		$required=number($row["Supplier $db_period Acc Parts Required"],0);





		$data[]=array(
			'id'=>$id,
			'code'=>$code,
			'name'=>$row['Supplier Name'],
			'low'=>number($row['Supplier Low Availability Products']),
			'high'=>number($row['Supplier Surplus Availability Products']),
			'normal'=>number($row['Supplier Optimal Availability Products']),
			'critical'=>number($row['Supplier Critical Availability Products']),
			'outofstock'=>number($row['Supplier Out Of Stock Products']),
			'location'=>$row['Supplier Main Location'],
			'email'=>$row['Supplier Main XHTML Email'],
			'tel'=>$row['Supplier Main XHTML Telephone'],
			'contact'=>$row['Supplier Main Contact Name'],
			'sold'=>$sold,
			'required'=>$required,
			'origin'=>$row['Supplier Products Origin'],
			'active_sp'=>number($row['Supplier Active Supplier Products']),
			'no_active_sp'=>number($row['Supplier Discontinued Supplier Products']),

			'delivery_time'=>seconds_to_string(3600*24*$row['Supplier Average Delivery Days']),

			'sales'=>$sales,
			'delta_sales'=>$delta_sales,
			'profit'=>$profit,
			'profit_after_storing'=>$profit_after_storing,
			'cost'=>$cost,
			'pending_pos'=>number($row['Supplier Open Purchase Orders']),
			'margin'=>$margin,
			'sales_year0'=>money($row['Supplier Year To Day Acc Parts Sold Amount'],$corporate_currency),
			'sales_year1'=>money($row['Supplier 1 Year Ago Sales Amount'],$corporate_currency),
			'sales_year2'=>money($row['Supplier 2 Year Ago Sales Amount'],$corporate_currency),
			'sales_year3'=>money($row['Supplier 3 Year Ago Sales Amount'],$corporate_currency),
			'sales_year4'=>money($row['Supplier 4 Year Ago Sales Amount'],$corporate_currency),

			'delta_sales_year0'=>'<span title="'.money($row["Supplier Year To Day Acc 1YB Parts Sold Amount"],$corporate_currency).'">'.delta($row["Supplier Year To Day Acc Parts Sold Amount"],$row["Supplier Year To Day Acc 1YB Parts Sold Amount"]).'</span>',
			'delta_sales_year1'=>'<span title="'.money($row["Supplier 2 Year Ago Sales Amount"],$corporate_currency).'">'.delta($row["Supplier 1 Year Ago Sales Amount"],$row["Supplier 2 Year Ago Sales Amount"]).'</span>',
			'delta_sales_year2'=>'<span title="'.money($row["Supplier 3 Year Ago Sales Amount"],$corporate_currency).'">'.delta($row["Supplier 2 Year Ago Sales Amount"],$row["Supplier 3 Year Ago Sales Amount"]).'</span>',
			'delta_sales_year3'=>'<span title="'.money($row["Supplier 4 Year Ago Sales Amount"],$corporate_currency).'">'.delta($row["Supplier 3 Year Ago Sales Amount"],$row["Supplier 4 Year Ago Sales Amount"]).'</span>'
		);

	}

	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$data,
			'sort_key'=>$_order,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$start_from+$total,
			'records_perpage'=>$number_results,
			'records_text'=>$rtext,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'filtered'=>$filtered
		)
	);
	echo json_encode($response);
}


function list_supplier_products() {


	if (isset( $_REQUEST['parent']))
		$parent=$_REQUEST['parent'];
	else
		exit;
	if (isset( $_REQUEST['parent_key']))
		$parent_key=$_REQUEST['parent_key'];
	else
		exit;


	if ($parent=='supplier') {
		$conf=$_SESSION['state']['supplier']['supplier_products'];
		$conf_table='supplier';
	}
	elseif ($parent=='none') {
		$conf=$_SESSION['state']['suppliers']['supplier_products'];
		$conf_table='suppliers';
	}
	else {

		exit;
	}

	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];
	if (isset( $_REQUEST['nr']))
		$number_results=$_REQUEST['nr'];
	else
		$number_results=$conf['nr'];
	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];

	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;

	if (isset( $_REQUEST['product_view']))
		$product_view=$_REQUEST['product_view'];
	else
		$product_view=$conf['view'];
	if (isset( $_REQUEST['period']))
		$product_period=$_REQUEST['period'];
	else
		$product_period=$conf['period'];

	if (isset( $_REQUEST['product_percentage']))
		$product_percentage=$_REQUEST['product_percentage'];
	else
		$product_percentage=$conf['percentage'];

	if (isset( $_REQUEST['elements_type']))
		$elements_type=$_REQUEST['elements_type'];
	else
		$elements_type=$conf['elements_type'];



	$elements=$conf['elements'];
	if (isset( $_REQUEST['elements_sp_state_Available'])) {
		$elements['state']['Available']=$_REQUEST['elements_sp_state_Available'];
	}
	if (isset( $_REQUEST['elements_sp_state_NoAvailable'])) {
		$elements['state']['NoAvailable']=$_REQUEST['elements_sp_state_NoAvailable'];
	}
	if (isset( $_REQUEST['elements_sp_state_Discontinued'])) {
		$elements['state']['Discontinued']=$_REQUEST['elements_sp_state_Discontinued'];
	}




	$filter_msg='';
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
	$_order=$order;
	$_dir=$order_direction;



	$_SESSION['state'][$conf_table]['supplier_products']['view']=$product_view;
	$_SESSION['state'][$conf_table]['supplier_products']['percentage']=$product_percentage;
	$_SESSION['state'][$conf_table]['supplier_products']['period']=$product_period;
	$_SESSION['state'][$conf_table]['supplier_products']['order']=$order;
	$_SESSION['state'][$conf_table]['supplier_products']['order_dir']=$order_dir;
	$_SESSION['state'][$conf_table]['supplier_products']['nr']=$number_results;
	$_SESSION['state'][$conf_table]['supplier_products']['sf']=$start_from;
	$_SESSION['state'][$conf_table]['supplier_products']['f_field']=$f_field;
	$_SESSION['state'][$conf_table]['supplier_products']['f_value']=$f_value;
	$_SESSION['state'][$conf_table]['supplier_products']['elements']=$elements;



	switch ($parent) {
	case 'none':
		$where=' where true ';
		break;
	case 'supplier':
		$where=sprintf(' where  `Supplier Key`=%d',$parent_key);
		break;
	}

	switch ($elements_type) {

	case('state'):
		$_elements='';
		$num_elements_checked=0;
		foreach ($elements['state'] as $_key=>$_value) {
			if ($_value) {
				$num_elements_checked++;

				$_elements.=", '$_key'";
			}
		}

		if ($_elements=='') {
			$where.=' and false' ;
		}elseif ($num_elements_checked<3) {
			$_elements=preg_replace('/^,/','',$_elements);
			$where.=' and `Supplier Product State` in ('.$_elements.')' ;
		}
		break;

	}



	$wheref='';

	//print "$f_field -> $f_value";


	if (($f_field=='p.code' ) and $f_value!='')
		$wheref.=" and  `Supplier Product Sold As` like '%".addslashes($f_value)."%'";
	if ($f_field=='sup_code' and $f_value!='')
		$wheref.=" and  `Supplier Product Code` like '".addslashes($f_value)."%'";








	$sql="select count(*) as total from `Supplier Product Dimension`  $where $wheref ";

	//print $sql;
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {

		$sql="select count(*) as total from `Supplier Product Dimension`  $where  ";
		// print $sql;
		$result=mysql_query($sql);
		if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$row['total']-$total;
		}

	}



	$rtext=number($total_records)." ".ngettext('product','products',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
	elseif ($total_records>0)
		$rtext_rpp=' ('._('Showing all').')';
	else
		$rtext_rpp='';
	$filter_msg='';

	switch ($f_field) {
	case('p.code'):
		if ($total==0 and $filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any product with code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('products with code')." <b>".$f_value."*</b>)";
		break;
	case('sup_code'):
		if ($total==0 and $filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any product with supplier code")." <b>".$f_value."*</b> ";
		elseif ($filtered>0)
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('products with supplier code')." <b>".$f_value."*</b>)";
		break;

	}

	$db_period=get_interval_db_name($product_period);

	if ($order=='id')
		$order='`Supplier Product ID`';
	if ($order=='supplier')
		$order='`Supplier Code`';
	elseif ($order=='code')
		$order='`Supplier Product Code`';
	elseif ($order=='used_in')
		$order='`Supplier Product XHTML Sold As`';
	elseif ($order=='tuos')
		$order='`Supplier Product Days Available`';
	elseif ($order=='stock')
		$order='`Supplier Product Stock`';
	elseif ($order=='name')
		$order='`Supplier Product Name`';
	elseif ($order=='profit') {
		$order="`Supplier Product $db_period Acc Parts Profit`";
	}
	elseif ($order=='required') {
		$order="`Supplier Product $db_period Acc Parts Required`";
	}elseif ($order=='state') {
		$order="`Supplier Product State`";
	}
	elseif ($order=='sold') {
		$order="`Supplier Product $db_period Acc Parts Sold`";


	}
	elseif ($order=='sales') {
		$order="`Supplier Product $db_period Acc Parts Sold Amount`";

	}
	elseif ($order=='margin') {
		$order="`Supplier Product $db_period Acc Parts Margin`";

	}
	elseif ($order=='dispatched') {
		$order="`Supplier Product $db_period Acc Parts Dispatched`";

	}else
		$order='`Supplier Product Code`';

	$sql="select `SPH Currency`,`Supplier Product State`,`Supplier Product XHTML Store As`,`Supplier Product Unit Type`,`SPH Case Cost`,`SPH Units Per Case`,`Supplier Code`,`Supplier Key`,`Supplier Product Name`,`Supplier Product Stock`,`Supplier Product XHTML Sold As`,`Supplier Product Days Available`,`Supplier Product Code`,`Supplier Product $db_period Acc Parts Lost`,`Supplier Product $db_period Acc Parts Broken`,`Supplier Product $db_period Acc Parts Sold Amount`,`Supplier Product $db_period Acc Parts Dispatched`,`Supplier Product $db_period Acc Parts Margin`,SP.`Supplier Product ID`,`Supplier Product $db_period Acc Parts Profit`,`Supplier Product $db_period Acc Parts Profit After Storing`,`Supplier Product $db_period Acc Parts Cost`,`Supplier Product $db_period Acc Parts Sold`,`Supplier Product $db_period Acc Parts Required` from `Supplier Product Dimension` SP left join  `Supplier Product History Dimension` SPHD ON (SPHD.`SPH Key`=SP.`Supplier Product Current Key`) $where $wheref  order by $order $order_direction limit $start_from,$number_results ";

	//print $sql;exit;
	$data=array();

	$result=mysql_query($sql);
	while ($row=mysql_fetch_array($result, MYSQL_ASSOC)   ) {

		$profit=money($row["Supplier Product $db_period Acc Parts Profit"]);
		$profit2=money($row["Supplier Product $db_period Acc Parts Profit After Storing"]);
		$allcost=money($row["Supplier Product $db_period Acc Parts Cost"]);
		$sold=number($row["Supplier Product $db_period Acc Parts Sold"]);
		$required=number($row["Supplier Product $db_period Acc Parts Required"]);
		$lost=number($row["Supplier Product $db_period Acc Parts Lost"]);
		$broken=$row["Supplier Product $db_period Acc Parts Broken"];
		$sold_amount=money($row["Supplier Product $db_period Acc Parts Sold Amount"]);
		$dispatched=number($row["Supplier Product $db_period Acc Parts Dispatched"]);
		$margin=percentage($row["Supplier Product $db_period Acc Parts Margin"],1);




		$code=sprintf('<a href="supplier_product.php?pid=%d">%s</a>',$row['Supplier Product ID'],$row['Supplier Product Code']);
		if ($row['Supplier Product Days Available']=='')
			$weeks_until='ND';
		else
			$weeks_until=round($row['Supplier Product Days Available']/7).' w';


		$used_in=$row['Supplier Product XHTML Sold As'];
		if ($row['Supplier Product XHTML Store As']) {
			$used_in.=' ('.$row['Supplier Product XHTML Store As'].')';
		}

		switch ($row['Supplier Product State']) {
		case 'Available':
			$state=_('Available');
			break;
		case 'No Available':
			$state=_('No Available');
			break;
		case 'Discontined':
			$state=_('Discontined');
			break;
		default:
			$state=$row['Supplier Product State'];
		}


		$data[]=array(

			'code'=>$code,
			'stock'=>number($row['Supplier Product Stock']),
			'weeks_until_out_of_stock'=>$weeks_until,
			'supplier'=>sprintf('<a href="supplier.php?id=%d">%s</a>',$row['Supplier Key'],$row['Supplier Code']),
			'name'=>$row['Supplier Product Name'],
			'description'=>'<span style="font-size:95%">'.number($row['SPH Units Per Case']).'x '.$row['Supplier Product Name'].' @ '.money($row['SPH Case Cost']/$row['SPH Units Per Case'],$row['SPH Currency']).' '.$row['Supplier Product Unit Type'].'</span>',
			'cost'=>money($row['SPH Case Cost'],$row['SPH Currency']),
			'used_in'=>$used_in,
			'profit'=>$profit,
			'allcost'=>$allcost,
			'sold'=>$sold,
			'required'=>$required,
			'dispatched'=>$dispatched,
			'lost'=>$lost,
			'broken'=>$broken,
			'sales'=>$sold_amount,
			'margin'=>$margin,
			'state'=>$state

		);
	}


	$response=array('resultset'=>
		array('state'=>200,
			'data'=>$data,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total,
			'records_offset'=>$start_from,
			'records_returned'=>$start_from+$total,
			'records_perpage'=>$number_results,
			'records_text'=>$rtext,
			'records_order'=>$order,
			'records_order_dir'=>$order_dir,
			'filtered'=>$filtered
		)
	);
	echo json_encode($response);
}


function is_supplier_code() {
	if (!isset($_REQUEST['query'])) {
		$response= array(
			'state'=>400,
			'msg'=>'Error'
		);
		echo json_encode($response);
		return;
	} else
		$query=$_REQUEST['query'];
	if ($query=='') {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}



	$sql=sprintf("select `Supplier Key`,`Supplier Name`,`Supplier Code` from `Supplier Dimension` where  `Supplier Code`=%s  "
		,prepare_mysql($query)
	);
	$res=mysql_query($sql);

	if ($data=mysql_fetch_array($res)) {
		$msg=sprintf('Supplier <a href="supplier.php?id=%d">%s</a> already has this code (%s)'
			,$data['Supplier Key']
			,$data['Supplier Name']
			,$data['Supplier Code']
		);
		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	} else {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}

}

function is_product_code($data) {
	$query=$data['query'];
	$supplier_key=$data['supplier_key'];

	if ($query=='') {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}



	$sql=sprintf("select `Supplier Product Name`,`Supplier Product Code`,`Supplier Product Name`,`Supplier Key` from `Supplier Product Dimension` where  `Supplier Key`=%d and `Supplier Product Code`=%s  ",
		$supplier_key,
		prepare_mysql($query)
	);
	$res=mysql_query($sql);

	if ($data=mysql_fetch_array($res)) {
		$msg=sprintf('Product <a href="supplier_product.php?code=%s&supplier_key=%d">%s</a> already has this code (%s)',
			$data['Supplier Product Code'],
			$data['Supplier Key'],
			$data['Supplier Product Name'],
			$data['Supplier Product Code']
		);
		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	} else {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}

}
function is_product_name($data) {
	$query=$data['query'];
	$supplier_key=$data['supplier_key'];

	if ($query=='') {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}



	$sql=sprintf("select `Supplier Product Name`,`Supplier Product Code`,`Supplier Product Name`,`Supplier Key` from `Supplier Product Dimension` where  `Supplier Key`=%d and `Supplier Product Name`=%s  ",
		$supplier_key,
		prepare_mysql($query)
	);
	$res=mysql_query($sql);

	if ($data=mysql_fetch_array($res)) {
		$msg=sprintf('Product <a href="supplier_product.php?code=%s&supplier_key=%d">%s</a> has the same name',
			$data['Supplier Product Code'],
			$data['Supplier Key'],
			$data['Supplier Product Code']
		);
		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	} else {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}

}


function find_supplier() {

	if (!isset($_REQUEST['query']) or $_REQUEST['query']=='') {
		$response= array(
			'state'=>400,
			'data'=>array()
		);
		echo json_encode($response);
		return;
	}


	if (isset($_REQUEST['except']) and  isset($_REQUEST['except_id'])  and   is_numeric($_REQUEST['except_id']) and $_REQUEST['except']=='supplier' ) {

		$sql=sprintf("select `Supplier Key`,`Supplier Name`,`Supplier Code` from `Supplier Dimension` where  (`Supplier Code`=%s or `Supplier Name` like '%%%s%%' ) and `Supplier Key`!=%d limit 20 "
			,prepare_mysql($_REQUEST['query']),addslashes($_REQUEST['query']),$_REQUEST['except_id']);

	} else {
		$sql=sprintf("select `Supplier Key`,`Supplier Name`,`Supplier Code` from `Supplier Dimension` where  (`Supplier Code`=%s or `Supplier Name` like '%%%s%%' ) limit 20"
			,prepare_mysql($_REQUEST['query']),addslashes($_REQUEST['query']));

	}


	$_data=array();
	$res=mysql_query($sql);

	while ($data=mysql_fetch_array($res)) {

		$_data[]= array(


			'key'=>$data['Supplier Key']
			,'code'=>$data['Supplier Code']
			,'name'=>$data['Supplier Name']


		);
	}
	$response= array(
		'state'=>200,
		'data'=>$_data
	);
	echo json_encode($response);


}

function is_supplier_product_name($data) {
	if (!isset($data['query']) or !isset($data['supplier_key'])) {
		$response= array(
			'state'=>400,
			'msg'=>'Error'
		);
		echo json_encode($response);
		return;
	} else
		$query=$data['query'];
	if ($query=='') {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}

	$supplier_key=$data['supplier_key'];

	$sql=sprintf("select `Supplier Product ID`,`Supplier Product Code` from `Supplier Product Dimension` where  `Supplier Key`=%d and  `Supplier Product Name`=%s  "
		,$supplier_key
		,prepare_mysql($query)
	);
	$res=mysql_query($sql);

	if ($data=mysql_fetch_array($res)) {
		$msg=sprintf('Another supplier_product (<a href="supplier_product.php?pid=%d">%s</a>) already has this name'
			,$data['Supplier Product ID']
			,$data['Supplier Product Code']
		);
		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	} else {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}

}

function is_supplier_product_code($data) {
	if (!isset($data['query']) or !isset($data['supplier_key']) ) {
		$response= array(
			'state'=>400,
			'msg'=>'Error'
		);
		echo json_encode($response);
		return;
	} else
		$query=$data['query'];
	if ($query=='') {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}

	$supplier_key=$data['supplier_key'];

	$sql=sprintf("select `Supplier Product ID`,`Supplier Product Name`,`Supplier Product Code` from `Supplier Product Dimension` where `Supplier Key`=%d and `Supplier Product Code`=%s  "
		,$supplier_key
		,prepare_mysql($query)
	);

	$res=mysql_query($sql);

	if ($data=mysql_fetch_array($res)) {
		$msg=sprintf('Product: <a href="supplier_product.php?pid=%d">%s</a> already has this code (%s)'
			,$data['Supplier Product ID']
			,$data['Supplier Product Name']
			,$data['Supplier Product Code']
		);
		$response= array(
			'state'=>200,
			'found'=>1,
			'msg'=>$msg
		);
		echo json_encode($response);
		return;
	} else {
		$response= array(
			'state'=>200,
			'found'=>0
		);
		echo json_encode($response);
		return;
	}

}


function list_supplier_categories() {

	global $corporate_currency;

	$conf=$_SESSION['state']['supplier_categories']['subcategories'];
	$conf2=$_SESSION['state']['supplier_categories'];


	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];

	if (isset( $_REQUEST['nr'])) {
		$number_results=$_REQUEST['nr'];
		if ($start_from>0) {
			$page=floor($start_from/$number_results);
			$start_from=$start_from-$page;
		}

	} else
		$number_results=$conf['nr'];

	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	/*
    if (isset( $_REQUEST['exchange_type'])) {
        $exchange_type=addslashes($_REQUEST['exchange_type']);
        $_SESSION['state']['supplier_categories']['exchange_type']=$exchange_type;
    } else
        $exchange_type=$conf2['exchange_type'];

    if (isset( $_REQUEST['exchange_value'])) {
        $exchange_value=addslashes($_REQUEST['exchange_value']);
        $_SESSION['state']['supplier_categories']['exchange_value']=$exchange_value;
    } else
        $exchange_value=$conf2['exchange_value'];

    if (isset( $_REQUEST['show_default_currency'])) {
        $show_default_currency=addslashes($_REQUEST['show_default_currency']);
        $_SESSION['state']['supplier_categories']['show_default_currency']=$show_default_currency;
    } else
        $show_default_currency=$conf2['show_default_currency'];


*/

	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;


	if (isset( $_REQUEST['percentages'])) {
		$percentages=$_REQUEST['percentages'];
		$_SESSION['state']['supplier_categories']['percentages']=$percentages;
	} else
		$percentages=$_SESSION['state']['supplier_categories']['percentages'];



	if (isset( $_REQUEST['period'])) {
		$period=$_REQUEST['period'];
		$_SESSION['state']['supplier_categories']['period']=$period;
	} else
		$period=$_SESSION['state']['supplier_categories']['period'];

	if (isset( $_REQUEST['avg'])) {
		$avg=$_REQUEST['avg'];
		$_SESSION['state']['supplier_categories']['avg']=$avg;
	} else
		$avg=$_SESSION['state']['supplier_categories']['avg'];

	/*
  if (isset( $_REQUEST['stores_mode'])) {
        $stores_mode=$_REQUEST['stores_mode'];
        $_SESSION['state']['supplier_categories']['stores_mode']=$stores_mode;
    } else
        $stores_mode=$_SESSION['state']['supplier_categories']['stores_mode'];
*/
	$_SESSION['state']['supplier_categories']['subcategories']['order']=$order;
	$_SESSION['state']['supplier_categories']['subcategories']['order_dir']=$order_direction;
	$_SESSION['state']['supplier_categories']['subcategories']['nr']=$number_results;
	$_SESSION['state']['supplier_categories']['subcategories']['sf']=$start_from;
	$_SESSION['state']['supplier_categories']['subcategories']['f_field']=$f_field;
	$_SESSION['state']['supplier_categories']['subcategories']['f_value']=$f_value;

	// print_r($_SESSION['tables']['families_list']);

	//  print_r($_SESSION['tables']['families_list']);

	if (isset( $_REQUEST['category_key'])) {
		$root_category=$_REQUEST['category_key'];
		$_SESSION['state']['supplier_categories']['category_key']=$_REQUEST['category_key'];
	} else
		$root_category=$_SESSION['state']['supplier_categories']['category_key'];





	$where=sprintf("where `Category Subject`='Supplier' and  `Category Parent Key`=%d ",$root_category);

	/*
    if ($stores_mode=='grouped')
        $group=' group by C.`Category Key`';
    else
        $group='';
*/


	$filter_msg='';
	$wheref='';
	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Category Code` like '%".addslashes($f_value)."%'";
	if ($f_field=='label' and $f_value!='')
		$wheref.=" and  `Category Label` like '%".addslashes($f_value)."%'";



	$sql="select count(*) as total   from `Category Dimension` C  left join `Category Bridge` B on (B.`Category Key`=C.`Category Key`)   $where $wheref";

	//$sql=" describe `Category Dimension`;";
	// $sql="select *  from `Category Dimension` where `Category Parent Key`=1 ";
	//print $sql;
	$res=mysql_query($sql);
	if ($row=mysql_fetch_assoc($res)) {
		$total=$row['total'];
		//   print_r($row);
	}
	mysql_free_result($res);

	//exit;
	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total  from `Category Dimension` C  left join `Category Bridge` B on (B.`Category Key`=C.`Category Key`)   $where ";

		$result=mysql_query($sql);
		if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($result);

	}


	$rtext=number($total_records)." ".ngettext('category','categories',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	elseif ($total_records>0)
		$rtext_rpp=' ('._('Showing all').')';
	else
		$rtext_rpp='';


	if ($total==0 and $filtered>0) {
		switch ($f_field) {

		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any category with code like ")." <b>*".$f_value."*</b> ";
			break;

		case('label'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any category with label like ")." <b>*".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {

		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('categories with code like')." <b>*".$f_value."*</b>";
			break;

		case('label'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any category with label like ")." <b>*".$f_value."*</b> ";
			break;
		}
	}
	else
		$filter_msg='';

	$_dir=$order_direction;
	$_order=$order;

	$db_period=get_interval_db_name($period);

	if ($order=='subjects')
		$order='`Category Number Subjects`';

	else if ($order=='profit') {

			$order='`$db_period Acc Profit`';



		}
	elseif ($order=='sales') {

		$order='`$db_period Acc Part Sales`';






	}
	else
		$order='`Category Code`';






	$sql="select * from `Category Dimension` C  left join `Supplier Category Dimension` B on (B.`Category Key`=C.`Category Key`) $where $wheref  order by $order $order_direction limit $start_from,$number_results    ";
	//   print $sql;
	$res = mysql_query($sql);

	$total=mysql_num_rows($res);
	$adata=array();
	$sum_sales=0;
	$sum_profit=0;
	$sum_outofstock=0;
	$sum_low=0;
	$sum_optimal=0;
	$sum_critical=0;
	$sum_surplus=0;
	$sum_unknown=0;
	$sum_departments=0;
	$sum_families=0;
	$sum_todo=0;
	$sum_discontinued=0;

	$DC_tag='';
	//  if ($exchange_type=='day2day' and $show_default_currency  )
	//     $DC_tag=' DC';

	$db_period=get_interval_db_name($period);

	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {



		$sales=money($row["$db_period Acc Part Sales"]);

		if (in_array($period,array('all','3y'))) {
			$delta_sales='';
		}else {

			$delta_sales=percentage($row["$db_period Acc Part Sales"],$row["$db_period Acc 1YB Part Sales"]);
		}
		$profit=money($row["$db_period Acc Profit"],$corporate_currency);
		$cost=money($row["$db_period Acc Cost"],$corporate_currency);




		//  if ($stores_mode=='grouped')
		$code=sprintf('<a href="supplier_category.php?id=%d">%s</a>',$row['Category Key'],$row['Category Code']);
		//    else
		//          $name=$row['Category Key'].' '.$row['Category Code']." (".$row['Category Store Key'].")";
		$label=sprintf('<a href="supplier_category.php?id=%d">%s</a>',$row['Category Key'],$row['Category Label']);


		$adata[]=array(
			//'go'=>sprintf("<a href='edit_category.php?edit=1&id=%d'><img src='art/icons/page_go.png' alt='go'></a>",$row['Category Key']),
			'id'=>$row['Category Key'],
			'code'=>$code,
			'label'=>$label,
			'subjects'=>number($row['Category Number Subjects']),
			'sales'=>$sales,
			'delta_sales'=>$delta_sales,
			'profit'=>$profit,
			'cost'=>$cost,
			'sales_year0'=>money($row['Year To Day Acc Part Sales'],$corporate_currency),
			'sales_year1'=>money($row['1 Year Ago Sales Amount'],$corporate_currency),
			'sales_year2'=>money($row['2 Year Ago Sales Amount'],$corporate_currency),
			'sales_year3'=>money($row['3 Year Ago Sales Amount'],$corporate_currency),
			'sales_year4'=>money($row['4 Year Ago Sales Amount'],$corporate_currency),

			'delta_sales_year0'=>'<span title="'.money($row["Year To Day Acc 1YB Part Sales"],$corporate_currency).'">'.delta($row["Year To Day Acc Part Sales"],$row["Year To Day Acc 1YB Part Sales"]).'</span>',
			'delta_sales_year1'=>'<span title="'.money($row["2 Year Ago Sales Amount"],$corporate_currency).'">'.delta($row["1 Year Ago Sales Amount"],$row["2 Year Ago Sales Amount"]).'</span>',
			'delta_sales_year2'=>'<span title="'.money($row["3 Year Ago Sales Amount"],$corporate_currency).'">'.delta($row["2 Year Ago Sales Amount"],$row["3 Year Ago Sales Amount"]).'</span>',
			'delta_sales_year3'=>'<span title="'.money($row["4 Year Ago Sales Amount"],$corporate_currency).'">'.delta($row["3 Year Ago Sales Amount"],$row["4 Year Ago Sales Amount"]).'</span>'

			/*  'departments'=>number($row['Product Category Departments']),
                              'families'=>number($row['Product Category Families']),
                              'active'=>number($row['Product Category For Public Sale Products']),
                              'todo'=>number($row['Product Category In Process Products']),
                              'discontinued'=>number($row['Product Category Discontinued Products']),
                              'outofstock'=>number($row['Product Category Out Of Stock Products']),
                              'stock_error'=>number($row['Product Category Unknown Stock Products']),
                              'stock_value'=>money($row['Product Category Stock Value']),
                              'surplus'=>number($row['Product Category Surplus Availability Products']),
                              'optimal'=>number($row['Product Category Optimal Availability Products']),
                              'low'=>number($row['Product Category Low Availability Products']),
                              'critical'=>number($row['Product Category Critical Availability Products']),
                              'sales'=>$sales,
                              'profit'=>$profit*/


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


function get_supplier_sales_data($data) {
	global $corporate_currency;
	$parent=$data['parent'];

	$parent_key=$data['parent_key'];
	$from_date=$data['from'];
	$to_date=$data['to'];

	switch ($parent) {
	case 'supplier':
		$table='`Inventory Transaction Fact` ITF ';
		$where=sprintf("and `Supplier Key`=%d ",$parent_key);
		break;
	case 'supplier_category':
		$table='`Inventory Transaction Fact` ITF  left join (`Category Bridge`) on (`Subject Key`=`Supplier Key` and `Subject`="Supplier") ';
		$where=sprintf("and `Category Key`=%d ",$parent_key);
		break;
	default:
		exit('x');
	}


	if ($from_date)$from_date=$from_date.' 00:00:00';
	if ($to_date)$to_date=$to_date.' 23:59:59';
	$where_interval=prepare_mysql_dates($from_date,$to_date,'`Invoice Date`');
	$where_interval=$where_interval['mysql'];

	$sales=0;
	$cost_sales=0;
	$profits=0;
	$profits_after_storing=0;
	$margin=0;
	$gmroi=0;
	$no_supplied=0;
	$given=0;
	$broken=0;
	$required=0;
	$sold=0;
	$lost=0;
	$adquired=0;
	$dispatched=0;

	$not_found=0;
	$out_of_stock=0;

	/*

	$sql=sprintf("select sum(`Amount In`+`Inventory Transaction Amount`) as profit,sum(`Inventory Transaction Storing Charge Amount`) as cost_storing
                     from %s where true %s %s %s" ,
		$table,$where,
		($from_date?sprintf('and  `Date`>=%s',prepare_mysql($from_date)):''),

		($to_date?sprintf('and `Date`<%s',prepare_mysql($to_date)):'')

	);
//print $sql;
	$result=mysql_query($sql);

	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$profits=$row['profit'];
		$profits_after_storing=$row['profit']-$row['cost_storing'];

	}
*/

	$sql=sprintf("select sum(`Inventory Transaction Amount`) as cost, sum(`Inventory Transaction Quantity`) as bought  from $table where `Inventory Transaction Type` like 'In'  $where  %s %s" ,

		($from_date?sprintf('and  `Date`>=%s',prepare_mysql($from_date)):''),
		($to_date?sprintf('and `Date`<%s',prepare_mysql($to_date)):'')

	);
	//print $sql;

	$result=mysql_query($sql);
	//print "$sql\n";
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

		$adquired=$row['bought'];

	}


	$sql=sprintf("select sum(`Amount In`) as sold_amount,sum(`Inventory Transaction Amount`) as cost,sum(`Inventory Transaction Storing Charge Amount`) as cost_storing,
                     sum(`Inventory Transaction Quantity`) as dispatched,
                     sum(`Required`) as required,
                     sum(`Given`) as given,
                     sum(`Required`-`Inventory Transaction Quantity`) as no_dispatched,
                     sum(`Given`-`Inventory Transaction Quantity`) as sold
                     from $table where `Inventory Transaction Type` like 'Sale' $where %s %s" ,

		($from_date?sprintf('and  `Date`>=%s',prepare_mysql($from_date)):''),
		($to_date?sprintf('and `Date`<%s',prepare_mysql($to_date)):'')

	);
	$result=mysql_query($sql);
	// print "xx $sql\n";
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

		$sales=$row['sold_amount'];
		$sold=$row['sold'];
		$dispatched=-1.0*$row['dispatched'];
		$required=$row['required'];
		$given=$row['given'];
		$cost_sales=-1.0*$row['cost'];
		$profits=$sales-$cost_sales;
		$profits_after_storing=$profits-$row['cost_storing'];


	}

	$sql=sprintf("select sum(`Inventory Transaction Quantity`) as broken
                     from $table  where `Inventory Transaction Type`='Broken' $where %s %s" ,

		($from_date?sprintf('and  `Date`>=%s',prepare_mysql($from_date)):''),
		($to_date?sprintf('and `Date`<%s',prepare_mysql($to_date)):'')

	);
	$result=mysql_query($sql);
	//print "$sql\n";
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

		$broken=-1.*$row['broken'];

	}

	$sql=sprintf("select sum(`Inventory Transaction Quantity`) as not_found
                     from $table  where `Inventory Transaction Type`='Not Found'  $where %s %s" ,

		($from_date?sprintf('and  `Date`>=%s',prepare_mysql($from_date)):''),
		($to_date?sprintf('and `Date`<%s',prepare_mysql($to_date)):'')
	);
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

		$not_found=-1.*$row['not_found'];

	}

	$sql=sprintf("select sum(`Inventory Transaction Quantity`) as out_of_stock
                     from $table  where `Inventory Transaction Type`='Out of Stock' $where %s %s" ,

		($from_date?sprintf('and  `Date`>=%s',prepare_mysql($from_date)):''),
		($to_date?sprintf('and `Date`<%s',prepare_mysql($to_date)):'')
	);
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

		$out_of_stock=-1.*$row['out_of_stock'];

	}



	$sql=sprintf("select sum(`Inventory Transaction Quantity`) as lost
                     from $table  where `Inventory Transaction Type` like 'Lost' $where %s %s" ,

		($from_date?sprintf('and  `Date`>=%s',prepare_mysql($from_date)):''),
		($to_date?sprintf('and `Date`<%s',prepare_mysql($to_date)):'')

	);
	$result=mysql_query($sql);
	//print "$sql\n";
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

		$lost=-1.*$row['lost'];

	}


	if ($sales!=0)
		$margin=$profits_after_storing/$sales;
	else
		$margin=0;


	$no_supplied=$not_found+$out_of_stock;
	$response= array('state'=>200,

		'sales'=>money($sales,$corporate_currency),
		'profits'=>money($profits,$corporate_currency),
		'profits_after_storing'=>money($profits_after_storing,$corporate_currency),
		'margin'=>number($margin),
		'gmroi'=>number($gmroi),
		'no_supplied'=>number($no_supplied),
		'not_found'=>number($not_found),
		'out_of_stock'=>number($out_of_stock),
		'cost_sales'=>money($cost_sales,$corporate_currency),
		'given'=>number($given),
		'broken'=>number($broken),
		'required'=>number($required),
		'sold'=>number($sold),
		'lost'=>number($lost),
		'adquired'=>number($adquired),
		'dispatched'=>number($dispatched)
	);


	//print_r($response);

	echo json_encode($response);




}

function list_supplier_product_sales_report() {

	global $user,$corporate_currency;
	$display_total=false;



	if (isset( $_REQUEST['parent']))
		$parent=$_REQUEST['parent'];
	else
		$parent='none';
	if (isset( $_REQUEST['parent_key'])) {
		$parent_key=$_REQUEST['parent_key'];
	}else {
		return;
	}

	if ($parent=='supplier') {
		$conf=$_SESSION['state']['supplier']['supplier_product_sales'];
		$conf_table='store';
	}
	elseif ($parent=='supplier_categories') {
		$conf=$_SESSION['state']['supplier_categories']['supplier_product_sales'];
		$conf_table='stores';
	}
	elseif ($parent=='none') {
		$conf=$_SESSION['state']['suppliers']['supplier_product_sales'];
		$conf_table='stores';
	}
	else {

		exit;
	}



	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];

	if (isset( $_REQUEST['nr'])) {
		$number_results=$_REQUEST['nr'];


	} else
		$number_results=$conf['nr'];


	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];

	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');



	if (isset( $_REQUEST['from'])) {
		$from=$_REQUEST['from'];

	}else {
		$from=$_SESSION['state'][$parent]['from'];

	}

	if (isset( $_REQUEST['to'])) {
		$to=$_REQUEST['to'];

	}else {
		$from=$_SESSION['state'][$parent]['to'];

	}



	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;








	$_SESSION['state'][$conf_table]['supplier_product_sales']['order']=$order;
	$_SESSION['state'][$conf_table]['supplier_product_sales']['order_dir']=$order_dir;
	$_SESSION['state'][$conf_table]['supplier_product_sales']['nr']=$number_results;
	$_SESSION['state'][$conf_table]['supplier_product_sales']['sf']=$start_from;
	$_SESSION['state'][$conf_table]['supplier_product_sales']['f_field']=$f_field;
	$_SESSION['state'][$conf_table]['supplier_product_sales']['f_value']=$f_value;




	$where_type='';
	$where_interval='';




	switch ($parent) {
	case('supplier'):
		$table='`Supplier Product Dimension` P  left join  `Inventory Transaction Fact`  ITF  on (ITF.`Supplier Product ID`=P.`Supplier Product ID`) left join `Supplier Dimension` S on (ITF.`Supplier Key`=S.`Supplier Key`)';
		$where=sprintf(' where ITF.`Supplier Key`=%d and  `Inventory Transaction Type`="Sale"  ',$parent_key);
		break;


	case('none'):
		$table='`Supplier Product Dimension` P  left join  `Inventory Transaction Fact`  ITF  on (ITF.`Supplier Product ID`=P.`Supplier Product ID`) left join `Supplier Dimension` S on (ITF.`Supplier Key`=S.`Supplier Key`)';

		$where=' where `Inventory Transaction Type`="Sale" ';

		break;
	case('supplier_categories'):
		$table='`Supplier Product Dimension` P  left join  `Inventory Transaction Fact`  ITF  on (ITF.`Supplier Product ID`=P.`Supplier Product ID`)  left join `Category Bridge` on (ITF.`Supplier Key`=`Subject Key` and `Subject`="Supplier")  left join `Supplier Dimension` S on (ITF.`Supplier Key`=S.`Supplier Key`) ';
		$where=sprintf(' where `Category Key`=%d and  `Inventory Transaction Type`="Sale"  ',$parent_key);
		break;




	}


	if ($from)$from=$from.' 00:00:00';
	if ($to)$to=$to.' 23:59:59';

	$where_interval=prepare_mysql_dates($from,$to,'`Date`');
	$where_interval=$where_interval['mysql'];
	$where.=$where_interval;
	$where.=$where_type;

	$filter_msg='';

	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');


	$_order=$order;
	$_dir=$order_direction;
	$filter_msg='';
	$wheref='';
	if ($f_field=='code' and $f_value!='')
		$wheref.=" and  `Supplier Product Code` like '".addslashes($f_value)."%'";
	elseif ($f_field=='description' and $f_value!='')
		$wheref.=" and  `Supplier Product Name` like '%".addslashes($f_value)."%'";



	$sql="select count(distinct ITF.`Supplier Product ID`)  as total from $table $where $wheref      ";


	// print $sql;
	$res=mysql_query($sql);
	if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

		$total=$row['total'];
	}
	if ($wheref!='') {
		$sql="select count(distinct ITF.`Supplier Product ID`)  as total_without_filters from $table  $where      ";


		$res=mysql_query($sql);
		if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

			$total_records=$row['total_without_filters'];
			$filtered=$row['total_without_filters']-$total;
		}

	} else {
		$filtered=0;
		$filter_total=0;
		$total_records=$total;
	}
	mysql_free_result($res);


	$rtext=number($total_records)." ".ngettext('supplier product','supplier products',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	elseif ($total_records)
		$rtext_rpp=' ('._("Showing all").')';
	else
		$rtext_rpp='';

	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any supplier product with code like ")." <b>".$f_value."*</b> ";
			break;
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any supplier product with name like ")." <b>".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('supplier products with code like')." <b>".$f_value."*</b>";
			break;
		case('name'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('supplier products with name like')." <b>".$f_value."*</b>";
			break;
		}
	}
	else
		$filter_msg='';

	$_order=$order;
	$_order_dir=$order_dir;


	if ($order=='supplier')
		$order='`Supplier Code`';

	elseif ($order=='name')
		$order='`PSupplier Product Name`';
	elseif ($order=='sales')
		$order='net';
	elseif ($order=='sold')
		$order='qty_delivered';
	elseif ($order=='profit')
		$order='profit';

	else
		$order='`Supplier Product Code`';



	$sql="select P.`Supplier Product Code`,P.`Supplier Product Name`,ITF.`Supplier Key`,P.`Supplier Code`,ITF.`Supplier Product ID`,sum(`Amount In`) as net,sum(`Inventory Transaction Quantity`) as qty_delivered from  $table $where $wheref group by ITF.`Supplier Product ID` order by $order $order_direction limit $start_from,$number_results    ";

	// print $sql;
	$adata=array();

	$res = mysql_query($sql);


	while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {




		$code=sprintf('<a href="supplier_product.php?pid=%s">%s</a>',$row['Supplier Product ID'],$row['Supplier Product Code']);
		$store=sprintf('<a href="supplier.php?id=%d">%s</a>',$row['Supplier Key'],$row['Supplier Code']);





		$sold=-1*$row['qty_delivered'];
		$tsall=$row['net'];
		// $tprofit=$row['profit'];




		include_once 'locale.php';



		$adata[]=array(
			'store'=>$store,
			'code'=>$code,
			'name'=>$row['Supplier Product Name'],
			'sales'=>(is_numeric($tsall)?money($tsall,$corporate_currency):$tsall),
			//'profit'=>(is_numeric($tprofit)?money($tprofit,$currency):$tprofit),
			'sold'=>(is_numeric($sold)?number($sold,0):$sold),
			//'state'=>$main_type
		);



	}
	mysql_free_result($res);







	$response=array('resultset'=>
		array(
			'state'=>200,
			'data'=>$adata,
			'sort_key'=>$_order,
			'sort_dir'=>$_dir,
			'tableid'=>$tableid,
			'filter_msg'=>$filter_msg,
			'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp,
			'total_records'=>$total_records,
			'records_offset'=>$start_from+1,
			'records_perpage'=>$number_results,
		)
	);




	echo json_encode($response);
}

function number_supplier_product_transactions_in_interval($data) {
	$supplier_product_pid=$data['supplier_product_pid'];

	$from=$data['from'];
	$to=$data['to'];

	$transactions=array(
		'all_transactions'=>0,
		'in_transactions'=>0,
		'out_transactions'=>0,
		'audit_transactions'=>0,
		'oip_transactions'=>0,
		'move_transactions'=>0
	);

	$where_interval=prepare_mysql_dates($from,$to,'`Date`','dates_only.startend');
	$where_interval=$where_interval['mysql'];
	$sql=sprintf("select sum(if(`Inventory Transaction Type` not in ('Move In','Move Out','Associate','Disassociate'),1,0))  as all_transactions , sum(if(`Inventory Transaction Type`='Not Found' or `Inventory Transaction Type` like 'No Dispatched' or `Inventory Transaction Type` like 'Audit',1,0)) as audit_transactions,sum(if(`Inventory Transaction Type`='Move',1,0)) as move_transactions,sum(if(`Inventory Transaction Type` like 'Sale' or `Inventory Transaction Type` like 'Other Out' or `Inventory Transaction Type`='Broken' or `Inventory Transaction Type` like 'Lost',1,0)) as out_transactions, sum(if(`Inventory Transaction Type`='Order In Process',1,0)) as oip_transactions, sum(if(`Inventory Transaction Type` like 'In',1,0)) as in_transactions from `Inventory Transaction Fact` where `Supplier Product ID`=%d %s",
		$supplier_product_pid,
		$where_interval
	);

	$res=mysql_query($sql);
	if ($row=mysql_fetch_assoc($res)) {

		$transactions=array(
			'all_transactions'=>number($row['all_transactions']),
			'in_transactions'=>number($row['in_transactions']),
			'out_transactions'=>number($row['out_transactions']),
			'audit_transactions'=>number($row['audit_transactions']),
			'oip_transactions'=>number($row['oip_transactions']),
			'move_transactions'=>number($row['move_transactions'])
		);
	}
	// }
	$response= array('state'=>200,'transactions'=>$transactions);
	echo json_encode($response);
}


function get_history_numbers($data) {
	$subject_key=$data['subject_key'];

	$elements_numbers=array('WebLog'=>0,'Notes'=>0,'Orders'=>0,'Changes'=>0,'Attachments'=>0,'Emails'=>0);
	$sql=sprintf("select count(*) as num , `Type` from  `Supplier History Bridge` where `Supplier Key`=%d group by `Type`",$subject_key);
	$res=mysql_query($sql);
	while ($row=mysql_fetch_assoc($res)) {
		$elements_numbers[$row['Type']]=$row['num'];
	}
	$response= array('state'=>200,'elements_numbers'=>$elements_numbers);
	echo json_encode($response);
}

function get_supplier_products_numbers($data) {
	global $user;

	$parent_key=$data['parent_key'];
	$parent=$data['parent'];


	$awhere='';
	$elements_type='';
	$f_field ='';
	$f_value='';

	$table='`Supplier Product Dimension`';
	switch ($parent) {
	case 'supplier':

		$where=sprintf("where `Supplier Key`=%d",$parent_key);
		break;
		
	case 'none':

		$where='';
		break;	
	default:
		exit('uknown parent');
	}


	$elements_numbers=array(
		'state'=>array('Available'=>0,'NoAvailable'=>0,'Discontinued'=>0)
	);


	//print "$table $where";

	$sql=sprintf("select count(*) as number,`Supplier Product State` as element from %s %s group by `Supplier Product State` ",
		$table,$where

	);
	//print $sql;
	$res=mysql_query($sql);
	while ($row=mysql_fetch_assoc($res)) {


		$elements_numbers['state'][$row['element']]=$row['number'];
	}

	foreach ($elements_numbers['state'] as $key=>$value) {
		$elements_numbers['state'][$key]=number($value);
	}






	//print_r($elements_numbers);
	$response= array('state'=>200,'elements_numbers'=>$elements_numbers);
	echo json_encode($response);

}

function list_part_in_supplier_product_historic() {

	$conf=$_SESSION['state']['supplier_product']['historic_parts'];


	if (isset( $_REQUEST['sp_id']))
		$sp_id=$_REQUEST['sp_id'];
	else {
		exit("");
	}


	if (isset( $_REQUEST['sf']))
		$start_from=$_REQUEST['sf'];
	else
		$start_from=$conf['sf'];


	if (isset( $_REQUEST['nr'])) {
		$number_results=$_REQUEST['nr'];
	} else
		$number_results=$conf['nr'];


	if (isset( $_REQUEST['o']))
		$order=$_REQUEST['o'];
	else
		$order=$conf['order'];
	if (isset( $_REQUEST['od']))
		$order_dir=$_REQUEST['od'];
	else
		$order_dir=$conf['order_dir'];
	$order_direction=(preg_match('/desc/',$order_dir)?'desc':'');



	if (isset( $_REQUEST['f_field']))
		$f_field=$_REQUEST['f_field'];
	else
		$f_field=$conf['f_field'];

	if (isset( $_REQUEST['f_value']))
		$f_value=$_REQUEST['f_value'];
	else
		$f_value=$conf['f_value'];


	if (isset( $_REQUEST['tableid']))
		$tableid=$_REQUEST['tableid'];
	else
		$tableid=0;



	$_SESSION['state']['supplier_product']['historic_parts']['order']=$order;
	$_SESSION['state']['supplier_product']['historic_parts']['order_dir']=$order_direction;
	$_SESSION['state']['supplier_product']['historic_parts']['nr']=$number_results;
	$_SESSION['state']['supplier_product']['historic_parts']['sf']=$start_from;
	$_SESSION['state']['supplier_product']['historic_parts']['f_field']=$f_field;
	$_SESSION['state']['supplier_product']['historic_parts']['f_value']=$f_value;




	$filter_msg='';

	$wheref='';
	$where=sprintf("where `Supplier Product Part Most Recent`='No' and  PP.`Supplier Product ID`=%d ",$sp_id);

	if ($f_field=='code' and $f_value!='')
		$wheref.=sprintf(" and `Supplier Product Code` like '%s%%'   ",addslashes($f_value));



	$sql="select count(*) as total from `Supplier Product Part List`  L  left join `Supplier Product Part Dimension` PP on (L.`Supplier Product Part Key`=PP.`Supplier Product Part Key`) left join `Supplier Product Dimension` P on (P.`Supplier Product ID`=PP.`Supplier Product ID`)left join `Supplier Dimension` S on (P.`Supplier Key`=S.`Supplier Key`)  $where $wheref";
//	  print $sql;
	$result=mysql_query($sql);
	if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		$total=$row['total'];
	}
	mysql_free_result($result);

	if ($wheref=='') {
		$filtered=0;
		$total_records=$total;
	} else {
		$sql="select count(*) as total from `Supplier Product Part List`  L  left join `Supplier Product Part Dimension` PP on (L.`Supplier Product Part Key`=PP.`Supplier Product Part Key`) left join `Supplier Product Dimension` P on (P.`Supplier Product ID`=PP.`Supplier Product ID`)left join `Supplier Dimension` S on (P.`Supplier Key`=S.`Supplier Key`)  $where ";
		//print $sql;
		$result=mysql_query($sql);
		if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$total_records=$row['total'];
			$filtered=$total_records-$total;
		}
		mysql_free_result($result);

	}


	$rtext=number($total_records)." ".ngettext('part','parts',$total_records);
	if ($total_records>$number_results)
		$rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
	elseif($total_records>10)
		$rtext_rpp=' ('._('Showing all').')';
	else
		$rtext_rpp='';	

	if ($total==0 and $filtered>0) {
		switch ($f_field) {
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any product with this code ")." <b>".$f_value."*</b> ";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any product with description like ")." <b>".$f_value."*</b> ";
			break;
		}
	}
	elseif ($filtered>0) {
		switch ($f_field) {
		case('code'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('product with code like')." <b>".$f_value."*</b>";
			break;
		case('description'):
			$filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('product with description like')." <b>".$f_value."*</b>";
			break;
		}
	}
	else
		$filter_msg='';
	/*  }else{//products parts for new product */

	/*      $total=count($_SESSION['state']['new_product']['parts']); */
	/*      $total_records=$total; */
	/*      $filtered=0; */
	/*    } */






	$_dir=$order_direction;
	$_order=$order;


	if ($order=='from') {
		$order='`Supplier Product Part Valid From`';
	}else if ($order=='to') {
			$order='`Supplier Product Part Valid To`';
		}else if ($order=='code') {
			$order='`Supplier Product Code`';
		}else if ($order=='name') {
			$order='`Supplier Product Name`';
		}else if ($order=='supplier') {
			$order='`Supplier Code`';
		}else if ($order=='relation') {
			$order='`SSupplier Product Units Per Part`';
		}else {
		$order='`Part SKU`';
	}

	$sql="select `Part Unit Description`,`Part Reference`,`Supplier Product Part Valid From`,`Supplier Product Part Valid To`,`Supplier Product Status`,`Supplier Product Part Most Recent`,`Supplier Product Part Valid To`,`Supplier Product Part Valid From`,P.`Supplier Product ID`,`Supplier Product Part List Key`,`Supplier Product Part In Use`,`Supplier Product Name`,`Supplier Product Units Per Part`,Pa.`Part SKU`,`Supplier Product Code` ,S.`Supplier Code`,S.`Supplier Key`
		from `Supplier Product Part List` L
		left join `Supplier Product Part Dimension` PP on (L.`Supplier Product Part Key`=PP.`Supplier Product Part Key`)
		left join `Supplier Product Dimension` P on (P.`Supplier Product ID`=PP.`Supplier Product ID`)
		left join `Part Dimension` Pa on (Pa.`Part SKU`=L.`Part SKU`)
		left join `Supplier Dimension` S on (P.`Supplier Key`=S.`Supplier Key`) $where $wheref   order by $order $order_direction limit $start_from,$number_results    ";
	//print $sql;
	$res = mysql_query($sql);
	$total=mysql_num_rows($res);
	$adata=array();
	while ($row=mysql_fetch_array($res, MYSQL_ASSOC) ) {
		// $meta_data=preg_split('/,/',$row['Deal Component Allowance']);

		if ($row['Supplier Product Part In Use']=='Yes') {
			$available_state=_('Available');
		} else {
			$available_state=_('No available');
		}

		if ($row['Supplier Product Status']=='In Use') {
			$formated_status=_('Ok');
		} else {
			$formated_status=_('Discontinued');
		}




		$relation=$row['Supplier Product Units Per Part'].' &rarr; 1';
		$adata[]=array(
			'sppl_key'=>$row['Supplier Product Part List Key'],
			'sku'=>$row['Part SKU'],
			'formated_sku'=>sprintf('<a href="part.php?sku=%d">SKU%05d</a>',$row['Part SKU'],$row['Part SKU']),
			'reference'=>sprintf('<a href="part.php?sku=%d">%s</a>',$row['Part SKU'],$row['Part Reference']),
			'relation'=>$relation,
			'code'=>'<a href="supplier_product.php?pid='.$row['Supplier Product ID'].'">'.$row['Supplier Product Code'].' ('.$row['Supplier Product ID'].')'.'</a>',
			'name'=>$row['Part Unit Description'],
			'supplier'=>'<a href="supplier.php?id='.$row['Supplier Key'].'">'.$row['Supplier Code'].'</a>',
			'available'=>$row['Supplier Product Part In Use'],
			'available_state'=>$available_state,
			'status'=>$row['Supplier Product Status'],
			'formated_status'=>$formated_status,
			'from'=>sprintf('<span title="%s">%s</span>',  strftime("%a %e %b %Y %H:%M %Z", strtotime($row['Supplier Product Part Valid From'].' +0:00')),strftime("%d-%m-%Y", strtotime($row['Supplier Product Part Valid From'].' +0:00'))),
			'to'=>sprintf('<span title="%s">%s</span>',  strftime("%a %e %b %Y %H:%M %Z", strtotime($row['Supplier Product Part Valid To'].' +0:00')),strftime("%d-%m-%Y", strtotime($row['Supplier Product Part Valid To'].' +0:00'))),


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

?>
