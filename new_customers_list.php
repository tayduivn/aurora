<?php
include_once('common.php');
include_once('class.Store.php');

if (!$user->can_view('customers') ) {
    header('Location: index.php');
    exit;
}


if (isset($_REQUEST['store']) and is_numeric($_REQUEST['store']) ) {
    $store_id=$_REQUEST['store'];

} else {
    header('Location: customers.php?error');
    exit;
}

if (! ($user->can_view('stores') and in_array($store_id,$user->stores)   ) ) {
    header('Location: customers.php?error_store='.$store_id);
    exit;
}


$store=new Store($store_id);
$smarty->assign('store',$store);
$smarty->assign('store_id',$store->id);

$css_files=array(
                $yui_path.'reset-fonts-grids/reset-fonts-grids.css',
               $yui_path.'menu/assets/skins/sam/menu.css',
               $yui_path.'assets/skins/sam/autocomplete.css',
               'common.css',
               'container.css',
               'button.css',
               'table.css',

               'theme.css.php'
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
              $yui_path.'calendar/calendar-min.js',
              'js/common.js',
              'js/table_common.js',
              'js/search.js',
              'common_customers.js.php',
              'new_customers_list.js.php',
              'js/edit_common.js',
          );

$smarty->assign('search_label',_('Customers'));
$smarty->assign('search_scope','customers');



$_SESSION['state']['customers']['table']['f_value']='';
$_SESSION['state']['customers']['list']['where']='';
$smarty->assign('parent','customers');
$smarty->assign('title', _('Customers Lists'));
$smarty->assign('css_files',$css_files);
$smarty->assign('js_files',$js_files);

$have_options=array(
                  'email'=>array('name'=>_('Email') ,'selected'=>false ),
                  'tel'=>array('name'=>_('Telephone'),'selected'=>false ),
                  'fax'=>array('name'=>_('Fax'),'selected'=>false ),
                  'address'=>array('name'=>_('Address'),'selected'=>false )
              );
			  

$smarty->assign('have_options',$have_options);



$customer_stat=array();

if(isset($_REQUEST['active']) && $_REQUEST['active']==1)
	  $customer_stat['active']=array('name'=>_('Active'),'selected'=>true);
else
	  $customer_stat['active']=array('name'=>_('Active'),'selected'=>false);


if(isset($_REQUEST['losing']) && $_REQUEST['losing']==1)  
		$customer_stat['losing']=array('name'=>_('Losing'),'selected'=>true);
else
		$customer_stat['losing']=array('name'=>_('Losing'),'selected'=>false);



	  
if(isset($_REQUEST['lost']) && $_REQUEST['lost']==1)  
		$customer_stat['lost']=array('name'=>_('Lost'),'selected'=>true);
else
		$customer_stat['lost']=array('name'=>_('Lost'),'selected'=>false);




$smarty->assign('customer_stat',$customer_stat);

/*
Parameter		Function
auto			auto reload
lost			enable lost
active			enable active
datei			set dates {0<i<n}

*/
$v_calpop=array();
for($i=1; $i<=6; $i++){
if(isset($_REQUEST['v_calpop'.$i]))
	$v_calpop['v_calpop'.$i]=$_REQUEST['v_calpop'.$i];
else
	$v_calpop['v_calpop'.$i]='';
}

$smarty->assign('v_calpop',$v_calpop);	



if(isset($_REQUEST['auto']) && $_REQUEST['auto']==1)
	$auto=1;
else
	$auto=0;
	
$smarty->assign('auto',$auto);	




$dont_have_options=array(
                       'email'=>array('name'=>_('Email'),'selected'=>false ),
                       'tel'=>array('name'=>_('Telephone'),'selected'=>false ),
                       'fax'=>array('name'=>_('Fax'),'selected'=>false ),
                       'address'=>array('name'=>_('Address'),'selected'=>false )
                   );
$smarty->assign('dont_have_options',$dont_have_options);

$condition=array(
                       'less'=>array('name'=>_('Less than'),'selected'=>false ),
                       'equal'=>array('name'=>_('Equal'),'selected'=>false ),
                       'more'=>array('name'=>_('More than'),'selected'=>false ),
					   'between'=>array('name'=>_('Between'),'selected'=>false ),
                   );
$smarty->assign('condition',$condition);

$allow_options=array(
                      
                       'newsletter'=>array('name'=>_('Newsletter'),'selected'=>false ),
                       'marketing_email'=>array('name'=>_('Marketing Email'),'selected'=>false ),
                       'marketing_post'=>array('name'=>_('Marketing Post'),'selected'=>false ),
                        'all'=>array('name'=>'No restrictions','selected'=>true), 
                   );
$smarty->assign('allow_options',$allow_options);


$smarty->assign('business_type',true);

$currency=$store->data['Store Currency Code'];
$currency_symbol=currency_symbol($currency);
$smarty->assign('view',$_SESSION['state']['customers']['table']['view']);

$tipo_filter=$_SESSION['state']['customers']['table']['f_field'];
$smarty->assign('filter0',$tipo_filter);
$smarty->assign('filter_value0',$_SESSION['state']['customers']['table']['f_value']);

$filter_menu=array(
                 'customer name'=>array('db_key'=>_('customer name'),'menu_label'=>_('Customer Name'),'label'=>_('Name')),
                 'postcode'=>array('db_key'=>_('postcode'),'menu_label'=>_('Customer Postcode'),'label'=>_('Postcode')),
                 'country'=>array('db_key'=>_('country'),'menu_label'=>_('Customer Country'),'label'=>_('Country')),

                 'min'=>array('db_key'=>_('min'),'menu_label'=>_('Mininum Number of Orders'),'label'=>_('Min No Orders')),
                 'max'=>array('db_key'=>_('min'),'menu_label'=>_('Maximum Number of Orders'),'label'=>_('Max No Orders')),
                 'last_more'=>array('db_key'=>_('last_more'),'menu_label'=>_('Last order more than (days)'),'label'=>_('Last Order >(Days)')),
                 'last_less'=>array('db_key'=>_('last_more'),'menu_label'=>_('Last order less than (days)'),'label'=>_('Last Order <(Days)')),
                 'maxvalue'=>array('db_key'=>_('maxvalue'),'menu_label'=>_('Balance less than').' '.$currency_symbol  ,'label'=>_('Balance')." <($currency_symbol)"),
                 'minvalue'=>array('db_key'=>_('minvalue'),'menu_label'=>_('Balance more than').' '.$currency_symbol  ,'label'=>_('Balance')." >($currency_symbol)"),
             );


$smarty->assign('filter_menu0',$filter_menu);
$smarty->assign('filter_name0',$filter_menu[$tipo_filter]['label']);

$paginator_menu=array(10,25,50,100,500);
$smarty->assign('paginator_menu0',$paginator_menu);



$tipo_filter1='wregion_code';
$filter_menu1=array(
                  'wregion_code'=>array('db_key'=>_('wregion_code'),'menu_label'=>_('World Region Code'),'label'=>_('Region Code')),
                  'wregion_name'=>array('db_key'=>_('wregion_name'),'menu_label'=>_('World Region Name'),'label'=>_('Region Name')),
              );
$smarty->assign('filter_name1',$filter_menu1[$tipo_filter1]['label']);
$smarty->assign('filter_menu1',$filter_menu1);
$smarty->assign('filter1',$tipo_filter1);
$smarty->assign('filter_value1','');



$tipo_filter2='code';
$filter_menu2=array(
                  'code'=>array('db_key'=>_('code'),'menu_label'=>_('Country Code'),'label'=>_('Code')),
                   'name'=>array('db_key'=>_('name'),'menu_label'=>_('Country Name'),'label'=>_('Name')),

                 'wregion'=>array('db_key'=>_('wregion'),'menu_label'=>_('World Region Name'),'label'=>_('Region')),
              );
$smarty->assign('filter_name2',$filter_menu2[$tipo_filter2]['label']);
$smarty->assign('filter_menu2',$filter_menu2);
$smarty->assign('filter2',$tipo_filter2);
$smarty->assign('filter_value2','');


$tipo_filter3='code';
$filter_menu3=array(
                  'code'=>array('db_key'=>_('code'),'menu_label'=>_('Postal Code'),'label'=>_('Postal Code')),
                   'country_code'=>array('db_key'=>_('country_code'),'menu_label'=>_('Country Code'),'label'=>_('Country Code')),
                   'country_name'=>array('db_key'=>_('country_name'),'menu_label'=>_('Country Name'),'label'=>_('Country Name')),
                //   'used'=>array('db_key'=>_('used'),'menu_label'=>_('Times present in the contacts'),'label'=>_('Used')),
              );
$smarty->assign('filter_name3',$filter_menu3[$tipo_filter3]['label']);
$smarty->assign('filter_menu3',$filter_menu3);
$smarty->assign('filter3',$tipo_filter3);
$smarty->assign('filter_value3','');

$tipo_filter4='city';
$filter_menu4=array(
                  'city'=>array('db_key'=>_('city'),'menu_label'=>_('Postal Code'),'label'=>_('City')),
                   'country_code'=>array('db_key'=>_('country_code'),'menu_label'=>_('Country Code'),'label'=>_('Country Code')),
                   'country_name'=>array('db_key'=>_('country_name'),'menu_label'=>_('Country Name'),'label'=>_('Country Name')),
                //   'used'=>array('db_key'=>_('used'),'menu_label'=>_('Times present in the contacts'),'label'=>_('Used')),
              );
$smarty->assign('filter_name4',$filter_menu4[$tipo_filter4]['label']);
$smarty->assign('filter_menu4',$filter_menu4);
$smarty->assign('filter4',$tipo_filter4);
$smarty->assign('filter_value4','');


$tipo_filter5='code';
$filter_menu5=array(
                  'code'=>array('db_key'=>_('code'),'menu_label'=>_('Code'),'label'=>_('Code')),
                  'name'=>array('db_key'=>_('name'),'menu_label'=>_('Name'),'label'=>_('Name')),
              );
$smarty->assign('filter_name5',$filter_menu5[$tipo_filter5]['label']);
$smarty->assign('filter_menu5',$filter_menu5);
$smarty->assign('filter5',$tipo_filter5);
$smarty->assign('filter_value5','');

$tipo_filter6='code';
$filter_menu6=array(
                  'code'=>array('db_key'=>_('code'),'menu_label'=>_('Code'),'label'=>_('Code')),
                  'name'=>array('db_key'=>_('name'),'menu_label'=>_('Name'),'label'=>_('Name')),
              );
$smarty->assign('filter_name6',$filter_menu6[$tipo_filter6]['label']);
$smarty->assign('filter_menu6',$filter_menu6);
$smarty->assign('filter6',$tipo_filter6);
$smarty->assign('filter_value6','');

$tipo_filter7='code';
$filter_menu7=array(
                  'code'=>array('db_key'=>_('code'),'menu_label'=>_('Code'),'label'=>_('Code')),
                  'name'=>array('db_key'=>_('name'),'menu_label'=>_('Name'),'label'=>_('Name')),
              );
$smarty->assign('filter_name7',$filter_menu7[$tipo_filter7]['label']);
$smarty->assign('filter_menu7',$filter_menu7);
$smarty->assign('filter7',$tipo_filter7);
$smarty->assign('filter_value7','');



$general_options_list=array();


 $general_options_list[]=array('tipo'=>'url','url'=>'customer_categories.php?store_id='.$store->id.'&id=0','label'=>_('Categories'));
$general_options_list[]=array('tipo'=>'url','url'=>'customers_lists.php?store='.$store->id,'label'=>_('Lists'));
$general_options_list[]=array('tipo'=>'url','url'=>'search_customers.php?store='.$store->id,'label'=>_('Advanced Search'));
$general_options_list[]=array('tipo'=>'url','url'=>'customers_stats.php','label'=>_('Stats'));
$general_options_list[]=array('tipo'=>'url','url'=>'customers.php?store='.$store->id,'label'=>_('Customers'));

$general_options_list[]=array('class'=>'return','tipo'=>'url','url'=>'customers_lists.php?store='.$store->id,'label'=>_('Go back').' &#8617;');


//$smarty->assign('general_options_list',$general_options_list);
$smarty->assign('options_box_width','550px');


$smarty->display('new_customers_list.tpl');
?>
