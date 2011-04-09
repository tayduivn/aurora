<?php
include_once('common.php');
include_once('assets_header_functions.php');

$smarty->assign('box_layout','yui-t0');
$css_files=array(
		 $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
		 $yui_path.'menu/assets/skins/sam/menu.css',
		 $yui_path.'calendar/assets/skins/sam/calendar.css',
		 $yui_path.'button/assets/skins/sam/button.css',
		
		 'button.css',
		 'container.css'
		 );

if($common)
{
array_push($css_files, 'themes_css/'.$common);   
array_push($css_files, 'themes_css/'.$row['Themes css4']);
array_push($css_files, 'themes_css/'.$row['Themes css2']); 
}    

else{
array_push($css_files, 'common.css'); 
array_push($css_files, 'css/dropdown.css');
array_push($css_files, 'table.css');
}


$js_files=array(

		$yui_path.'utilities/utilities.js',
		$yui_path.'json/json-min.js',
		$yui_path.'paginator/paginator-min.js',
		$yui_path.'datasource/datasource.js',
		$yui_path.'autocomplete/autocomplete-min.js',
		$yui_path.'datatable/datatable.js',
		$yui_path.'container/container-min.js',
		$yui_path.'menu/menu-min.js',
		$yui_path.'calendar/calendar-min.js',
		'js/common.js',
		'js/table_common.js',
		'js/search.js',
		'parts.js.php',
		 'js/dropdown.js',
		 'js/edit_common.js',
		 'js/dropdown.js',
		 'js/csv_common.js'
		);

if(!$user->can_view('parts')){

  $smarty->assign('parent','home');
  $smarty->assign('title', _('Forbidden'));
  $smarty->assign('css_files',$css_files);
  $smarty->assign('js_files',$js_files);

   $smarty->display('forbidden.tpl');
   exit;

}



$view_sales=$user->can_view('product sales');
$view_stock=$user->can_view('product stock');
$create=$user->can_create('parts');
$modify=$user->can_edit('parts');

$smarty->assign('view_sales',$view_sales);
$smarty->assign('view_stock',$view_stock);
$smarty->assign('create',$create);
$smarty->assign('modify',$modify);

get_header_info($user,$smarty);

$smarty->assign('search_label',_('Parts'));
$smarty->assign('search_scope','part');




$q='';

$sql="select count(*) as total_parts   from `Part Dimension` ";
$result=mysql_query($sql);
if(!$parts=mysql_fetch_array($result, MYSQL_ASSOC)   ){
  
  exit("Internal Error\n");
 }
  

$smarty->assign('parent','products');
$smarty->assign('title', _('Parts Index'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

$product_home="Products Home";
$smarty->assign('home',$product_home);




$tipo_filter=($q==''?$_SESSION['state']['parts']['table']['f_field']:'code');
$smarty->assign('filter',$tipo_filter);
$smarty->assign('filter_value',($q==''?$_SESSION['state']['parts']['table']['f_value']:addslashes($q)));
$filter_menu=array(
		   'used_in'=>array('db_key'=>'used_in','menu_label'=>'Used in <i>x</i>','label'=>'Used in'),
		   'supplied_by'=>array('db_key'=>'supplied_by','menu_label'=>'Supplied by <i>x</i>','label'=>'Supplied by'),
		   'description'=>array('db_key'=>'description','menu_label'=>'Part Description like <i>x</i>','label'=>'Description'),

		   );
$smarty->assign('filter_menu0',$filter_menu);

$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);
$smarty->assign('view',$_SESSION['state']['parts']['view']);
$smarty->assign('period',$_SESSION['state']['parts']['period']);
$smarty->assign('avg',$_SESSION['state']['parts']['avg']);

$smarty->assign('currency',$myconf['currency_symbol']);
$smarty->assign('parts',$parts['total_parts']);





 $csv_export_options=array(
                            'description'=>array(
                                              'title'=>_('Description'),
                                              'rows'=>
                                                     array(
                                                         array(
                                                             'sku'=>array('label'=>_('Code'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['sku']),
                                                             'used_in'=>array('label'=>_('Name'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['used_in']),
                                                             'description'=>array('label'=>_('Stores'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['description']),
                                                             
                                                             'stock'=>array('label'=>_('Products'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['stock']),
							      'stock_cost'=>array('label'=>_('Products'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['stock_cost'])
                                                   
                                                            
                                                     
                                                         )
                                                     )
                                          ),
                            'parts_details'=>array(
                                        'title'=>_('Parts Details'),
                                        'rows'=>
                                               array(
                                                   array(
                                                       'unit'=>array('label'=>_('Surplus'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['unit']),
                                                       'status'=>array('label'=>_('Ok'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['status']),
                                                       'valid_from'=>array('label'=>_('Low'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['valid_from']),
                                                       'valid_to'=>array('label'=>_('Critical'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['valid_to'])
                                                      
                                                

                                                   )
                                               )
                                    ),
			   'total'=>array(
                                        'title'=>_('Total'),
                                        'rows'=>
                                               array(
                                                   array(
                                                       'total_lost'=>array('label'=>_('Surplus'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['total_lost']),
                                                       'total_broken'=>array('label'=>_('Ok'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['total_broken']),
                                                       'total_sold'=>array('label'=>_('Low'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['total_sold']),
                                                       'total_given'=>array('label'=>_('Critical'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['total_given'])
                                                      
                                                

                                                   )
                                               )
                                    ),
                            'sales_all'=>array('title'=>_('Sales (All times)'),
                            'rows'=>
                                               array(
                                                   array(
                                                       'sales_all'=>array('label'=>_('Sales'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['sales_all']),
                                                       'profit_all'=>array('label'=>_('Profit'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['profit_all']),
                                                      
                                                   )
                            )
                            ),
'sales_1y'=>array('title'=>_('Sales (1 Year)'),
                            'rows'=>
                                               array(
                                                   array(
                                                       'sales_1y'=>array('label'=>_('Sales'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['sales_1y']),
                                                       'profit_1y'=>array('label'=>_('Profit'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['profit_1y']),
                                                       
                                                   )
                            )
                            ),
'sales_1q'=>array('title'=>_('Sales (1 Quarter)'),
                            'rows'=>
                                               array(
                                                   array(
                                                       'sales_1q'=>array('label'=>_('Sales'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['sales_1q']),
                                                       'profit_1q'=>array('label'=>_('Profit'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['profit_1q']),
                                                       
                                                   )
                            )
                            ),
'sales_1m'=>array('title'=>_('Sales (1 Month)'),
                            'rows'=>
                                               array(
                                                   array(
                                                       'sales_1m'=>array('label'=>_('Sales'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['sales_1m']),
                                                       'profit_1m'=>array('label'=>_('Profit'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['profit_1m']),
                                                      
                                                   )
                            )
                            ),
                            'sales_1w'=>array('title'=>_('Sales (1 Week)'),
                            'rows'=>
                                               array(
                                                   array(
                                                       'sales_1w'=>array('label'=>_('Sales'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['sales_1w']),
                                                       'profit_1w'=>array('label'=>_('Profit'),'selected'=>$_SESSION['state']['parts']['table']['csv_export']['profit_1w']),
                                                      
                                                   )
                            )
                            )
                        );
$smarty->assign('export_csv_table_cols',8);

                        
$smarty->assign('csv_export_options',$csv_export_options);





$smarty->display('parts.tpl');
?>
