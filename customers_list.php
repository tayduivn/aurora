<?php
include_once('common.php');
if (!$user->can_view('customers') ) {
    header('Location: index.php');
    exit;
}
//$modify=$user->can_edit('staff');
$general_options_list=array();
if (isset($_REQUEST['id']))
    $id=$_REQUEST['id'];
else {
    header('Location: index.php?error=no_id_in_customers_list');
    exit;

}


$sql=sprintf("select * from `Customer List Dimension` where `Customer List Key`=%d",$id);

$res=mysql_query($sql);
if (!$customer_list_data=mysql_fetch_assoc($res)) {
    header('Location: index.php?error=id_in_customers_list_not_found');
    exit;

}
$store=new Store($customer_list_data['Customer List Store Key']);


$static_list_name=$customer_list_data['Customer List Name'];
$smarty->assign('static_list_name',$static_list_name);
$smarty->assign('static_list_id',$customer_list_data['Customer List Key']);



$general_options_list[]=array('tipo'=>'js','id'=>'export_data','label'=>_('Export Data(CSV)'));
$general_options_list[]=array('tipo'=>'url','url'=>'pdf_customer_list.php?id='.$id,'label'=>_('Print Address Label'));
$general_options_list[]=array('tipo'=>'url','url'=>'customers_lists.php?store='.$store->id,'label'=>_('Customers Lists'));
$general_options_list[]=array('tipo'=>'url','url'=>'customers.php?store='.$store->id,'label'=>_('Customers'));

$smarty->assign('general_options_list',$general_options_list);

$smarty->assign('options_box_width','450px');

$css_files=array(
               $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
               $yui_path.'build/assets/skins/sam/skin.css',
               $yui_path.'menu/assets/skins/sam/menu.css',
               'common.css',
               'container.css',
               'table.css'
           );

$css_files=array(
               $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
               $yui_path.'menu/assets/skins/sam/menu.css',
               $yui_path.'calendar/assets/skins/sam/calendar.css',
               $yui_path.'button/assets/skins/sam/button.css',
               $yui_path.'assets/skins/sam/autocomplete.css',
               'common.css',
               'container.css',
               'table.css'
           );

$js_files=array(
              $yui_path.'utilities/utilities.js',
              $yui_path.'json/json-min.js',
              $yui_path.'paginator/paginator-min.js',
              $yui_path.'datasource/datasource-min.js',
              $yui_path.'autocomplete/autocomplete-min.js',
              $yui_path.'datatable/datatable.js',
              $yui_path.'container/container-min.js',
              $yui_path.'menu/menu-min.js',


              'common.js.php',
              'table_common.js.php',
              'js/search.js',
              'js/edit_common.js',
              'js/csv_common.js',
              'common_customers.js.php',
              'customers_list.js.php?id='.$id
          );
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);
$smarty->assign('parent','customers');
//$smarty->assign('sub_parent','areas');
$smarty->assign('view',$_SESSION['state']['customers']['view']);

$smarty->assign('title', _('Customer Static List'));
$smarty->assign('search_label',_('Customers'));
$smarty->assign('search_scope','customers');

$smarty->display('customers_list.tpl');
?>
