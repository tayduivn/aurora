<?
include_once('common.php');
if(!$LU->checkRight(CUST_VIEW))
  exit;


// $q='';
// $q_id=0;
// $not_fount=false;

// if(isset($_REQUEST['q_id1']) and $_REQUEST['q_id1']!=''  ){
//   // SEARCH!!!!!!!!!!!!
//   $q=$_REQUEST['q_id1'];
//   $sql=sprintf("select id from customer where id='%s' ",addslashes($q));

//   $result =& $db->query($sql);

//   if($found=$result->fetchRow()){
//     header('Location: customer.php?id='. $found['id']);
//     exit;
//   }else{
//     $not_fount=true;
//     $smarty->assign('search1',$q);
//     $_SESSION['tables']['customers_list'][4]=sprintf("where id='%s'",addslashes($q));
//     $_SESSION['tables']['customers_list'][5]='id';
//     $_SESSION['tables']['customers_list'][6]='';
//   }

  


//  }else{
//   $_SESSION['state']['customer']['where']='where true ';
//  }

// if(isset($_REQUEST['q_id2'])  ){
//   // SEARCH!!!!!!!!!!!!
//   $q=$_REQUEST['q_id2'];
//   $q_id=2;
//   $sql=sprintf("select id from customer where id2='%s' ",addslashes($q));

//   $result =& $db->query($sql);
//   if($result->numRows()==1){
//     if($found=$result->fetchRow()){
//       header('Location: customer.php?id='. $found['id']);
//       exit;
//     }
//   }
//   $_SESSION['tables']['customers_list'][5]='id2';
//   $_SESSION['tables']['customers_list'][6]=addslashes($q); 

//   $smarty->assign('search2',$q);



//  }

// if(isset($_REQUEST['pc'])   ){
//   // SEARCH!!!!!!!!!!!!
//   $q=$_REQUEST['pc'];
//   $q_id=3;
//   $sql=sprintf("select id from customer where id3='%s' ",addslashes($q));
//   $result =& $db->query($sql);
//   if($result->numRows()==1){
//     if($found=$result->fetchRow()){
//       header('Location: customer.php?id='. $found['id']);
//       exit;
//     }
//   }
//   $_SESSION['tables']['customers_list'][5]='id3';
//   $_SESSION['tables']['customers_list'][6]=addslashes($q); 
  
//   $smarty->assign('search3',$q);

//  }





$smarty->assign('box_layout','yui-t0');









$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 //		 $yui_path.'datatable/assets/skins/sam/datatable.css',
		 $yui_path.'build/assets/skins/sam/skin.css',
		 'common.css',
		 'container.css',
		 'table.css'
		 );
$js_files=array(
		$yui_path.'yahoo-dom-event/yahoo-dom-event.js',
		$yui_path.'connection/connection-min.js',
		$yui_path.'json/json-min.js',
		$yui_path.'element/element-beta-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container_core-min.js',
		$yui_path.'menu/menu-min.js',
		'js/common.js.php',
		'js/table_common.js.php',
		'js/search.js',
		'js/customers.js.php'
		);


$smarty->assign('details',$_SESSION['state']['customers']['details']);
$smarty->assign('advanced_search',$_SESSION['state']['customers']['advanced_search']);


$smarty->assign('parent','customers.php');
$smarty->assign('title', _('Customers'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

$smarty->assign('table_title',_('Customers List'));




$tipo_filter=$_SESSION['state']['customers']['table']['f_field'];
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value',$_SESSION['state']['customers']['table']['f_value']);

$filter_menu=array('customer name'=>array('db_key'=>_('customer name'),'menu_label'=>'Customer Name','label'=>'Name'),
		   );
$smarty->assign('filter_menu',$filter_menu);
$smarty->assign('filter_name',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu',$paginator_menu);


// $sql="select count(distinct `Customer ID`) from  ";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $total_customers=$row['customers'];
//  }


// $sql="select count(*) as customers from customer where contact_id>0 and (num_invoices+num_invoices_nd)>0  ";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $total_customers=$row['customers'];
//  }
// $now="NOW()";
// $sql="select count(*) as active_customers from customer where contact_id>0 and order_interval>=0 and  (order_interval*3)>DATEDIFF($now,last_order)";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// $active_customers=0;
// if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $active_customers=$row['active_customers'];
//  }

// $sql="select count(*) as new_customers from customer  where   contact_id>0 and (num_invoices+num_invoices_nd)>0 and  (91.25)>DATEDIFF($now,first_order)";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// $new_customers=0;
// if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $new_customers=$row['new_customers'];
//  }


// $overview_text=translate("He have indentified  %1\$s  customers so far, %2\$s of them still active (%3\$s%\). Over the last 3 months we acquired  %4\$s new customers representing  %5\$s of the total customer base.",$total_customers,$active_customers,percentage($active_customers,$total_customers),$new_customers,percentage($new_customers,$total_customers));
// $smarty->assign('overview_text',$overview_text);

// $home_country='United Kingdom';
// $home_informal_name=_('the UK');


// $sql="select sum(total_net+total_net_nd) as total_net from customer    ";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $total_net=$row['total_net'];
//  }
// $sql="select sum(total_net+total_net_nd) as total_net from customer  left join contact on (contact_id=contact.id) left join address on (main_address=address.id) where country!='$home_country'";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $export_total_net=$row['total_net'];
//  }

// //print "$total_net $export_total_net";


// $total_net_80p=.8*$total_net;

// $sql="select (total_net+total_net_nd) as total_net from customer order by (total_net+total_net_nd) desc";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());

// $top_customers=1;$_total_net=0;

// while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $_total_net+=$row['total_net'];
//   if($_total_net>$total_net_80p){
//     break;
//   }
//   $top_customers++;

//  }

// $overview_text=translate("%1\$s customers (%2\$s%\) are responsable for 80%% of the sales.",$top_customers,percentage($top_customers,$total_customers));
// $smarty->assign('top_text',$overview_text);



// $export_customers=0;
// $sql="select count(*) as export_customers from customer left join contact on (contact_id=contact.id) left join address on (main_address=address.id) where   contact_id>0 and (num_invoices+num_invoices_nd)>0 and country!='$home_country'";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// $new_customers=0;
// if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//   $export_customers=$row['export_customers'];
//  }
// $domestic_customers=$total_customers-$export_customers;

// $percentage_domestic=percentage($domestic_customers,$total_customers);
// $countries=0;
// $sql="select count(*) as countries from customer left join contact on (contact_id=contact.id) left join address on (main_address=address.id) where country!='$home_country' group by country";
// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// $countries=mysql_num_rows($result);

// $continents=0;
// 		      $sql="select country,continent from customer left join contact on (contact_id=contact.id) left join address on (main_address=address.id) left join list_country on (list_country.name=country) where country!='United Kingdom' group by continent";

// $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
// $continents=mysql_num_rows($result);

// $export_text=translate("%1\$s are based in $home_informal_name, the other %2\$s customers (%3\$s%\ of sales)  are distributed over %4\$s countries and %5\$s continents.",$percentage_domestic,$export_customers,percentage($export_total_net,$total_net),$countries,$continents);
 $smarty->assign('view',$_SESSION['state']['customers']['view']);


// $smarty->assign('export_text',$export_text);
// $smarty->assign('table_info',$total_customers.'  '.ngettext('identified customer','identified customers',$total_customers));



$smarty->display('customers.tpl');






?>