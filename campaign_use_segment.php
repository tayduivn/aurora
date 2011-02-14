<?php
/*
 File: marketing.php 

 UI index page

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/

include_once('common.php');

include_once('class.Product.php');
include_once('class.Order.php');

$page='marketing';

$general_options_list=array();
$general_options_list[]=array('tipo'=>'url','url'=>'marketing_reports.php','label'=>_('Reports'));

$general_options_list[]=array('tipo'=>'url','url'=>'new_email_campaign.php','label'=>_('Create Email Campaign'));
$general_options_list[]=array('tipo'=>'url','url'=>'newsletter.php?new','label'=>_('Create Newsletter'));
$smarty->assign('general_options_list',$general_options_list);

$view_orders=$user->can_view('Orders');


$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 'common.css',
		 'button.css',
		 'container.css',
		 'css/marketing_campaigns.css',
		 'table.css'
		 );
$js_files=array(

		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable-min.js',
		$yui_path.'container/container-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'js/jquery-1.4.4.js',
		'js/plain_campaign_builder.js',
		'common.js.php',
		'table_common.js.php',
		'js/search.js',
		'marketing_create_campaign.js.php',
		'js/track.js'
		);




$getID = isset($_REQUEST['getID'])?$_REQUEST['getID']:''; 


$trackID = isset($_GET['trackID'])?$_GET['trackID']:'0';

	if($trackID!=0)
	{
	
	$sqlTrack = "select `Campaign Mailling List Id`,`Campaign Mailling Track Open`,`Campaign Mailling Track Click`,`Campaign Mailling Plain Text Click` from `Email Campaign Mailling List` where `Email Campaign Mailling List Key` = '".$trackID."'";
		$ressqlTrack = mysql_query($sqlTrack);
		$rowsqlTrack = mysql_fetch_assoc($ressqlTrack);

	//tracking 
	$smarty->assign('open',$rowsqlTrack['Campaign Mailling Track Open']);
	$smarty->assign('click',$rowsqlTrack['Campaign Mailling Track Click']);
	$smarty->assign('text_click',$rowsqlTrack['Campaign Mailling Plain Text Click']);

		
	}


$sql = "select `Email Campaign Mailing List Key`,`List Name`,`Default Reply To Email`,`Default From Name` from `Email Campaign Mailing List` where `Email Campaign Mailing List Key` = '".$getID."'";
		$res = mysql_query($sql);
		$row = mysql_fetch_assoc($res);
	
	//echo $sql; die();

	$smarty->assign('list_id',$row['Email Campaign Mailling List Key']);
	$smarty->assign('subject',$row['List Name']);
	$smarty->assign('default_name',$row['Default From Name']);
	$smarty->assign('email',$row['Default Reply To Email']);
	
	//$smarty->assign('default_name',$row['Campaign Mailling List Default Name']);



 
if (isset($_REQUEST['view'])) {
    $valid_views=array('metrics','email','web_internal','web','other','newsletter');
    if (in_array($_REQUEST['view'], $valid_views))
        $_SESSION['state'][$page]['view']=$_REQUEST['view'];

}
$smarty->assign('view',$_SESSION['state'][$page]['view']);


$smarty->assign('parent','home');
$smarty->assign('title', _('Use Segment'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);


$q='';
$tipo_filter=($q==''?$_SESSION['state'][$page]['email_campaigns']['f_field']:'code');
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value',($q==''?$_SESSION['state'][$page]['email_campaigns']['f_value']:addslashes($q)));
$filter_menu=array(
                 'name'=>array('db_key'=>'name','menu_label'=>'Campaign with name like <i>x</i>','label'=>'Name')
             );
$smarty->assign('filter_menu0',$filter_menu);

$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);
$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);


$smarty->display('campaign_use_segment.tpl');
?>
