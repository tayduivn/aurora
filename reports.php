<?php
/*
 File: reports.php 

 UI reports index page

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/
include_once('common.php');
include_once('map_url.php');


$tipo=$_SESSION['state']['reports']['view'];



if(isset($_SESSION['state']['reports'][$tipo]['plot']))
  $tipo_plot=$_SESSION['state']['reports'][$tipo]['plot'];
else
  $tipo_plot=false;
  



$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		 'common.css',
		 'container.css',
		 'table.css',
		 			'css/dropdown.css'

		 );
$js_files=array(





		$yui_path.'utilities/utilities.js',
		$yui_path.'calendar/calendar-min.js',
		$yui_path.'json/json-min.js',
		$yui_path.'datasource/datasource-min.js',
		$yui_path.'charts/charts-experimental-min.js',
		$yui_path.'calendar/calendar-min.js',
		$yui_path.'container/container-min.js',
		$yui_path.'menu/menu-min.js',


		'common.js.php',
		'reports.js.php',
			'js/dropdown.js'
		);


$plot_title=array(
		  'total_sales_groupby_month'=>_('Net sales grouped by month')."."
		  ,'total_sales_month'=>_('Monthly net sales')."."

);

$smarty->assign('plot_title',$plot_title);
$smarty->assign('plot_tipo',$tipo_plot);

$region=$_SESSION['state']['reports']['geosales']['region'];
$map_exclude=$_SESSION['state']['reports']['geosales']['map_exclude'];

$map_url=get_map_url($region);
$smarty->assign('map_url',$map_url);
$smarty->assign('region',$region);
$smarty->assign('map_excludes',$map_exclude);


$sql="select `Page Parent Category`,`Page Short Title`,`Page URL`,`Image URL` from `Page Dimension` P  left join `Page Internal Dimension`  I on (P.`Page Key`=I.`Page Key`) left join `Image Dimension` IM on (IM.`Image Key`=`Page Thumbnail Image Key`) where `Page Type`='Internal' and `Page Section`='Reports'";
//print $sql;
$res=mysql_query($sql);
$current_category='';
$report_index=array();
while($row=mysql_fetch_array($res)){
  if($current_category!=$row['Page Parent Category']){
    switch($row['Page Parent Category']){
    case('Sales Reports'):
      $title=_('Sales');
      break;
    case('Activity/Performace Reports'):
      $title=_('Activity/Performace');
      break;
    default:
      $title=$row['Page Parent Category'];

    }
    $report_index[$row['Page Parent Category']]=array('title'=>$title,'reports'=>array());
  }
  $title=$row['Page Short Title'];
  
  $report_index[$row['Page Parent Category']]['reports'][]=array('title'=>$title,'url'=>$row['Page URL'],'snapshot'=>$row['Image URL']);
    

}


$smarty->assign('report_index',$report_index);



$smarty->assign('tipo',$tipo);

$smarty->assign('parent','reports');
$smarty->assign('title', _('Reports'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);
$smarty->assign('year',date('Y'));
$smarty->assign('month',date('m'));
$smarty->assign('day',date('d'));

$smarty->assign('month_name',date('F'));

$smarty->assign('week',date('W'));
$smarty->assign('from',date('d-m-Y'));
$smarty->assign('to',date('d-m-Y'));


$smarty->display('reports.tpl');

?>

