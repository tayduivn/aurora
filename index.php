<?
include_once('common.php');
include_once('stock_functions.php');

$view_orders=$LU->checkRight(ORDER_VIEW);
$smarty->assign('view_orders',$view_orders);


//set_sales_all();
//update_family_all();
//update_department_all();
//update_supplier_datos_all();
$week=date("W");
$sql='select sum(total) as total,count(*) from orden where tipo=2 and week(date_index)='.$week;


$smarty->assign('box_layout','yui-t4');

$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 'common.css',
		 'container.css',
		 'table.css'
		 );
$js_files=array(
		'js/passwordmeter.js.php',
		'js/sha256.js.php',
		$yui_path.'yahoo-dom-event/yahoo-dom-event.js',
		$yui_path.'element/element-beta-min.js',
		$yui_path.'utilities/utilities.js',
		$yui_path.'container/container.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'button/button.js',
		$yui_path.'autocomplete/autocomplete.js',
		$yui_path.'datasource/datasource-beta.js',
		$yui_path.'datatable/datatable-beta.js',
		$yui_path.'json/json-min.js',
		'js/common.js.php',
		'js/table_common.js.php',
		'js/index.js.php'

		);


$smarty->assign('parent','index.php');
$smarty->assign('title', _('Home'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

//set_stock_value_all();
//update_department_all();
//update_family_all();


//fix_todotransaction_all();

$smarty->assign('filter',$_SESSION['tables']['proinvoice_list'][5]);
$smarty->assign('filter_value',$_SESSION['tables']['proinvoice_list'][6]);

switch($_SESSION['tables']['proinvoice_list'][5]){
 case('max'):
   $filter_text=_('Maximun Day Interval');
   break;
 case('min'):
   $filter_text=_('Minimun Day Interval');
   break;
 case('public_id'):
   $filter_text=_('Order Number');
   break;
 case('customer_name'):
   $filter_text=_('Customer Name');
   break;
 default:
   $filter_text='?';
 }

$smarty->assign('filter_name',$filter_text);
$smarty->assign('f_date',_('Week').strftime(" %W %Y" ));

$smarty->assign('t_title0',_('Outstanding Orders'));

$smarty->display('default.tpl');





?>

