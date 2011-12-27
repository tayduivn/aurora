<?php
/*
 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2011, Inikoo

 Version 2.0
*/

require_once 'common.php';
require_once 'class.Site.php';
require_once 'class.PageHeader.php';
require_once 'class.PageFooter.php';

require_once 'ar_edit_common.php';

if (!isset($_REQUEST['tipo'])) {
    $response=array('state'=>405,'msg'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
}



$tipo=$_REQUEST['tipo'];
switch ($tipo) {
case('update_page_height'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),
                             'footer'=>array('type'=>'numeric'),
                             'header'=>array('type'=>'numeric'),
                             'content'=>array('type'=>'numeric'),

                         ));
    update_page_height($data);
    break;
    break;
case('update_page_preview_snapshot'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),
                         ));
    update_page_preview_snapshot($data);
    break;
case('edit_page_product_list'):
    $data=prepare_values($_REQUEST,array(
                             'newvalue'=>array('type'=>'string'),
                             'key'=>array('type'=>'string'),
                             'id'=>array('type'=>'key'),
                         ));

    edit_page_product_list($data);
    break;
case('page_product_lists'):
    list_page_product_lists_for_edition();
    break;
case('page_product_buttons'):
    list_page_product_buttons_for_edition();
    break;
case('set_default_header'):
    $data=prepare_values($_REQUEST,array(
                             'header_key'=>array('type'=>'key'),
                             'site_key'=>array('type'=>'key'),
                         ));
    set_default_header($data);
    break;

case('set_default_footer'):
    $data=prepare_values($_REQUEST,array(
                             'footer_key'=>array('type'=>'key'),
                             'site_key'=>array('type'=>'key'),
                         ));
    set_default_footer($data);
    break;

case('delete_page_header'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),

                         ));
    delete_page_header($data);
    break;
case('delete_page_footer'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),

                         ));
    delete_page_footer($data);
    break;
case('page_headers'):
    list_headers_for_edition();
    break;
case('page_footers'):
    list_footers_for_edition();
    break;
case('add_see_also_page'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),
                             'see_also_key'=>array('type'=>'key')

                         ));

    add_see_also_page($data);


    break;
case('edit_site_menu'):
case('edit_site_search'):

case('edit_site'):

    $data=prepare_values($_REQUEST,array(
                             'site_key'=>array('type'=>'key'),
                             'values'=>array('type'=>'json array')

                         ));

    edit_site($data);
    break;
case('edit_checkout_method'):
    $data=prepare_values($_REQUEST,array(
                             'site_key'=>array('type'=>'key'),
                             'store_key'=>array('type'=>'key'),
                             'site_checkout_method'=>array('type'=>'string'),

                         ));

    edit_checkout_method($data);


    break;

case('edit_registration_method'):
    $data=prepare_values($_REQUEST,array(
                             'site_key'=>array('type'=>'key'),
                             'store_key'=>array('type'=>'key'),
                             'site_registration_method'=>array('type'=>'string'),

                         ));

    edit_registration_method($data);


    break;
case('delete_see_also_page'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),
                             'see_also_key'=>array('type'=>'key')

                         ));

    delete_see_also_page($data);


    break;
case('add_found_in_page'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),
                             'found_in_key'=>array('type'=>'key')

                         ));

    add_found_in_page($data);


    break;
case('delete_found_in_page'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),
                             'found_in_key'=>array('type'=>'key')

                         ));

    delete_found_in_page($data);


    break;
case('delete_page_store'):

    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),

                         ));

    delete_page_store($data);
    break;
case('new_department_page'):
    $data=prepare_values($_REQUEST,array(
                             'site_key'=>array('type'=>'key'),
                             'department_key'=>array('type'=>'key')
                         ));

    new_department_page($data);
    break;

case('update_see_also_quantity'):
    $data=prepare_values($_REQUEST,array(
                             'id'=>array('type'=>'key'),
                             'operation'=>array('type'=>'string')
                         ));
    update_see_also_quantity($data);
    break;
case('new_family_page'):
    $data=prepare_values($_REQUEST,array(
                             'site_key'=>array('type'=>'key'),
                             'family_key'=>array('type'=>'key')
                         ));

    new_family_page($data);
    break;
case('edit_page_layout'):
    edit_page_layout();
    break;
case('edit_page_html_head'):
case('edit_page_header'):
case('edit_page_content'):
case('edit_page_properties'):
    require_once 'class.Family.php';


//print_r($_REQUEST);

    $data=prepare_values($_REQUEST,array(
                             'newvalue'=>array('type'=>'string'),
                             'key'=>array('type'=>'string'),
                             'okey'=>array('type'=>'string'),
                             'id'=>array('type'=>'key')
                         ));

    edit_page($data);
    break;

case('edit_family_page_html_head'):
case('edit_family_page_header'):
case('edit_family_page_content'):
case('edit_family_page_properties'):
    require_once 'class.Family.php';


    $data=prepare_values($_REQUEST,array(
                             'newvalue'=>array('type'=>'string'),
                             'key'=>array('type'=>'string'),
                             'id'=>array('type'=>'key')
                         ));

    edit_page($data);
    break;

case('edit_store_page_html_head'):
case('edit_store_page_header'):
case('edit_store_page_content'):
case('edit_store_page_properties'):
    require_once 'class.Store.php';


    $data=prepare_values($_REQUEST,array(
                             'newvalue'=>array('type'=>'string'),
                             'key'=>array('type'=>'string'),
                             'id'=>array('type'=>'key')
                         ));

    edit_page($data);
    break;

case('edit_department_page_html_head'):
case('edit_department_page_header'):
case('edit_department_page_content'):
case('edit_department_page_properties'):
    require_once 'class.Department.php';


    $data=prepare_values($_REQUEST,array(
                             'newvalue'=>array('type'=>'string'),
                             'key'=>array('type'=>'string'),
                             'id'=>array('type'=>'key')
                         ));

    edit_page($data);
    break;
    break;
case('family_page_list'):
case('department_page_list'):
case('store_pages'):
case('pages'):
    list_pages_for_edition();
    break;

default:

    $response=array('state'=>404,'msg'=>_('Operation not found'));
    echo json_encode($response);

}

function update_see_also_quantity($data) {

    include_once('class.Family.php');
    global $editor;
    $page=new Page($data['id']);
    $page->editor=$editor;

    if ($page->data['Number See Also Links']==0 and $data['operation']=='remove') {
        $response= array('state'=>401,'msg'=>$page->msg);
        echo json_encode($response);
        return;
    }


    if ($data['operation']=='remove') {
        $quantity=$page->data['Number See Also Links']-1;
    } else {
        $quantity=$page->data['Number See Also Links']+1;
    }
    $page->update_field_switcher('Number See Also Links',$quantity);

    if ($page->updated) {
        $page->update_see_also();
        //$page->update_preview_snapshot();
        $response= array('state'=>200,'newvalue'=>$page->new_value);
    } else {
        $response= array('state'=>400,'msg'=>$page->msg);
    }
    echo json_encode($response);


}

function  edit_page($data) {

    global $editor;
    $page=new Page($data['id']);
    $page->editor=$editor;

    $value=stripslashes(urldecode($data['newvalue']));

    if ($data['key']=='Page Store Source') {
        $value=preg_replace("/\{(.*)\}/e",'"{".html_entity_decode(\'$1\')."}"', $value);
         $page->update_field_switcher($data['key'],$value,'no_history');
    }else{
    $page->update_field_switcher($data['key'],$value);
    }

   
    if ($page->updated) {
       
        $response= array('state'=>200,'key'=>$data['okey'],'newvalue'=>$page->new_value);
    } else {
        $response= array('state'=>400,'msg'=>$page->msg,'key'=>$data['key']);
    }
    echo json_encode($response);

}

function edit_page_layout() {
    $page_key=$_REQUEST['page_key'];
    $layout=$_REQUEST['layout'];
    $value=$_REQUEST['newvalue'];

    $page=new Page($page_key);
    $page->update_show_layout($layout,$value);

    if ($page->updated) {
        $response= array('state'=>200,'newvalue'=>$page->new_value);

    } else {
        $response= array('state'=>400,'msg'=>$page->msg);
    }
    echo json_encode($response);


}


function new_department_page($data) {
    include_once('class.Department.php');
    $site=new Site($data['site_key']);
    $department=new Department($data['department_key']);
    $page_data=array();
    $page_data['Page Parent Key']=$department->id;
    $page_data['Page Store Slogan']='';
    $page_data['Page Store Resume']='';
    $page_data['Page Store Section']='Department Catalogue';
    $page_data['Showcases Layout']='Splited';
    $page_data['Page URL']='www.ancientwisdom.biz/'.strtolower($department->data['Product Department Code']);
    $site->add_department_page($page_data);

    if ($site->new_page) {
        $response= array('state'=>200,'action'=>'created');

    } else {
        $response= array('state'=>400,'msg'=>$site->msg);

    }
    echo json_encode($response);
}

function new_family_page($data) {
    include_once('class.Family.php');
    $site=new Site($data['site_key']);
    $family=new Family($data['family_key']);
    $page_data=array();
    $page_data['Page Parent Key']=$family->id;
    $page_data['Page Store Slogan']='';
    $page_data['Page Store Resume']='';
    $page_data['Page Store Section']='Family Catalogue';
    $page_data['Showcases Layout']='Splited';
    $page_data['Page URL']='www.ancientwisdom.biz/forms/'.strtolower($family->data['Product Family Code']);
    $site->add_family_page($page_data);

    if ($site->new_page) {
        $response= array('state'=>200,'action'=>'created');

    } else {
        $response= array('state'=>400,'msg'=>$site->msg);

    }
    echo json_encode($response);
}


function list_pages_for_edition() {






    $parent= $_REQUEST['parent'];
    $parent_key=$_REQUEST['parent_key'];






    $conf=$_SESSION['state'][$parent]['edit_pages'];




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



    $_SESSION['state'][$parent]['edit_pages']['order']=$order;
    $_SESSION['state'][$parent]['edit_pages']['order_dir']=$order_direction;
    $_SESSION['state'][$parent]['edit_pages']['nr']=$number_results;
    $_SESSION['state'][$parent]['edit_pages']['sf']=$start_from;
    $_SESSION['state'][$parent]['edit_pages']['f_field']=$f_field;
    $_SESSION['state'][$parent]['edit_pages']['f_value']=$f_value;


    if ($parent=='site') {
        $where=sprintf(' where `Page Type`="Store" and `Page Site Key`=%d ',$parent_key);

    } else if ($parent=='store')
        $where.sprintf("and `Page Store Section`  not in ('Department Catalogue','Product Description','Family Catalogue') and `Page Store Key`=%d ",$parent_key);
    else if ($parent=='family')
        $where=sprintf("where `Page Store Section`='Family Catalogue'   and `Page Parent Key`=%d ",$parent_key);
    else if ($parent=='department')
        $where=sprintf("where `Page Store Section`='Department Catalogue'   and `Page Parent Key`=%d ",$parent_key);

    $filter_msg='';
    $wheref='';

    if ($f_field=='code'  and $f_value!='')
        $wheref.=" and `Page Code` like '".addslashes($f_value)."%'";
    elseif ($f_field=='title' and $f_value!='')
    $wheref.=" and  `Page Store Title` like '".addslashes($f_value)."%'";







    $sql="select count(*) as total from `Page Dimension` P left join `Page Store Dimension` PS on (P.`Page Key`=PS.`Page Key`)  $where $wheref";
//print $sql;
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
//print $sql;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $total_records=$row['total'];
            $filtered=$total_records-$total;
        }
        mysql_free_result($result);

    }


    $rtext=$total_records." ".ngettext('page','pages',$total_records);
    if ($total_records>$number_results)
        $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
    else
        $rtext_rpp=' ('._('Showing all').')';


    $filter_msg='';

    switch ($f_field) {
    case('code'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any page with code")." <b>$f_value</b>* ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('pages with code')." <b>$f_value</b>*)";
        break;
    case('name'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any page with name")." <b>$f_value</b>* ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('pages with name')." <b>$f_value</b>*)";
        break;

    }

    $_dir=$order_direction;
    $_order=$order;


    if ($order=='title')
        $order='`Page Title`';
    else
        $order='`Page Section`';


    $adata=array();
    $sql="select *  from `Page Dimension`  P left join `Page Store Dimension` PS on (P.`Page Key`=PS.`Page Key`) left join `Site Dimension` S on (`Site Key`=`Page Site Key`)  $where $wheref   order by $order $order_direction limit $start_from,$number_results    ";

    $res = mysql_query($sql);

    $total=mysql_num_rows($res);

    while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {







        $adata[]=array(
                     'id'=>$row['Page Key'],
                     'section'=>$row['Page Section'],
                     'code'=>$row['Page Code'],
                     'site'=>sprintf('<a href="site.php?id=%d">%s</a>',$row['Page Site Key'],$row['Site Code']),
                     'store_title'=>$row['Page Store Title'],
                     'link_title'=>$row['Page Short Title'],
                     'url'=>$row['Page URL'],
                     'page_title'=>$row['Page Title'],
                     'page_description'=>$row['Page Store Resume'],


                     'go'=>sprintf("<a href='edit_page.php?id=%d&referral=%s&referral_key=%s'><img src='art/icons/page_go.png' alt='go'></a>",$row['Page Key'],$parent,$parent_key),

                     'delete'=>"<img src='art/icons/cross.png'  alt='"._('Delete')."'  title='"._('Delete')."' />"

                 );
    }
    mysql_free_result($res);



    // if($total<$number_results)
    //  $rtext=$total.' '.ngettext('store','stores',$total);
    //else
    //  $rtext='';

//   $total_records=ceil($total_records/$number_results)+$total_records;

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




function  delete_page_store($data) {

    $page=new Page($data['id']);
    $page->delete();

    if ($page->deleted) {
        $response= array('state'=>200,'action'=>'deleted');

    } else {
        $response= array('state'=>400,'msg'=>$page->msg);

    }
    echo json_encode($response);

}

function add_found_in_page($data) {

    $page_key=$data['id'];
    $found_in_key=$data['found_in_key'];
    $sql=sprintf("insert into `Page Store Found In Bridge` values (%d,%d)  ",
                 $page_key,
                 $found_in_key);

    mysql_query($sql);
    $response= array('state'=>200,'action'=>'created','page_key'=>$page_key);
    echo json_encode($response);

}


function delete_found_in_page($data) {

    $page_key=$data['id'];
    $found_in_key=$data['found_in_key'];
    $sql=sprintf("delete from  `Page Store Found In Bridge` where `Page Store Key`=%d and `Page Store Found In Key`=%d   ",
                 $page_key,
                 $found_in_key);
    mysql_query($sql);
    $response= array('state'=>200,'action'=>'deleted','page_key'=>$page_key);
    echo json_encode($response);

}

function add_see_also_page($data) {

    $page_key=$data['id'];
    $see_also_key=$data['see_also_key'];
    $sql=sprintf("insert into `Page Store See Also Bridge` values (%d,%d,'Manual',null)  ",
                 $page_key,
                 $see_also_key);

    mysql_query($sql);
    $response= array('state'=>200,'action'=>'created','page_key'=>$page_key);
    echo json_encode($response);

}


function delete_see_also_page($data) {

    $page_key=$data['id'];
    $see_also_key=$data['see_also_key'];
    $sql=sprintf("delete from  `Page Store See Also Bridge` where `Page Store Key`=%d and `Page Store See Also Key`=%d   ",
                 $page_key,
                 $see_also_key);
    mysql_query($sql);
    $response= array('state'=>200,'action'=>'deleted','page_key'=>$page_key);
    echo json_encode($response);

}

function edit_checkout_method($data) {
//print_r($data);
    $site = new Site($data['site_key']);
    if (!$site) {
        $response= array('state'=>400,'msg'=>'Site not found','key'=>$data['site_key']);
        echo json_encode($response);

        exit;
    }
//print_r($site);

    $method=$data['site_checkout_method'];
//print $method;
    $response=$site->update(array('Site Checkout Method'=>$method));
    if ($site->updated) {
        $response= array('state'=>200,'action'=>'updated','msg'=>$site->msg, 'new_value'=>$site->new_value);
    } else
        $response= array('state'=>400,'msg'=>$site->msg);

    echo json_encode($response);
}

function edit_registration_method($data) {
    $site = new Site($data['site_key']);
    if (!$site) {
        $response= array('state'=>400,'msg'=>'Site not found','key'=>$data['site_key']);
        echo json_encode($response);

        exit;
    }
    
     
     
     
     
    if(!in_array($data['site_registration_method'],array('Simple','Wholesale','None'))){
       $response= array('state'=>400,'msg'=>'wrong value '.$data['site_registration_method'],'key'=>$data['site_key']);
        echo json_encode($response);

        exit;
    
    }
    

    $response=$site->update(array('Site Registration Method'=>$data['site_registration_method']));
    if ($site->updated) {
        $response= array('state'=>200,'action'=>'updated','msg'=>$site->msg, 'new_value'=>strtolower($site->new_value));
    } else
        $response= array('state'=>400,'msg'=>$site->msg);

    echo json_encode($response);
}

function edit_site($data) {
    $site=new Site($data['site_key']);
    if (!$site->id) {
        $response= array('state'=>400,'msg'=>'Site not found','key'=>$data['key']);
        echo json_encode($response);

        exit;
    }
    $values=array();
    foreach($data['values'] as $value_key=>$value_data) {
        if ($value_data['value']=='') {
            $values[$value_key]=$value_data;
            unset($data['values'][$value_key]);
        }
    }

    foreach($data['values'] as $value_key=>$value_data) {

        $values[$value_key]=$value_data;

    }

//   print_r($values);

    $responses=array();
    foreach($values as $key=>$values_data) {
        //print_r($values_data);
        $responses[]=edit_site_field($site->id,$key,$values_data);
    }

    echo json_encode($responses);


}

function edit_site_field($site_key,$key,$value_data) {

    //print $value_data;
    //print "$customer_key,$key,$value_data ***";
    $site=new site($site_key);

    global $editor;
    $site->editor=$editor;

    $key_dic=array(
                 'slogan'=>'Site Slogan',
                 'name'=>'Site Name',
                 'url'=>'Site URL',
                 'ftp'=>'Site FTP Credentials',

             );

    if (array_key_exists($key,$key_dic))
        $key=$key_dic[$key];

    $the_new_value=_trim($value_data['value']);
    //print "$key: $the_new_value";

    $site->update(array($key=>$the_new_value));

    if ($site->updated) {
        $response= array('state'=>200,'action'=>'updated','msg'=>$site->msg, 'newvalue'=>strtolower($site->new_value),'key'=>$value_data['okey']);
    } else
        $response= array('state'=>400,'msg'=>$site->msg,'key'=>$value_data['okey']);


    //$response=array();
    return $response;

}


function delete_page_header($data) {
    $page_header=new PageHeader($data['id']);
    $page_header->delete();
    if ($page_header->deleted) {
        $response= array('state'=>200,'action'=>'deleted');

    } else {
        $response= array('state'=>400,'msg'=>$page_header->msg);

    }
    echo json_encode($response);
}

function delete_page_footer($data) {
    $page_footer=new Pagefooter($data['id']);
    $page_footer->delete();
    if ($page_footer->deleted) {
        $response= array('state'=>200,'action'=>'deleted');

    } else {
        $response= array('state'=>400,'msg'=>$page_footer->msg);

    }
    echo json_encode($response);
}




function list_headers_for_edition() {
    if (isset( $_REQUEST['parent']) and in_array($_REQUEST['parent'],array('site','department','family','product')) ) {
        $parent=$_REQUEST['parent'];

    } else {
        return;
    }

    if (isset( $_REQUEST['parent_key'])) {
        $parent_key=$_REQUEST['parent_key'];

    } else {
        return;
    }


    $conf=$_SESSION['state'][$parent]['edit_headers'];




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



    $_SESSION['state'][$parent]['edit_headers']['order']=$order;
    $_SESSION['state'][$parent]['edit_headers']['order_dir']=$order_direction;
    $_SESSION['state'][$parent]['edit_headers']['nr']=$number_results;
    $_SESSION['state'][$parent]['edit_headers']['sf']=$start_from;
    $_SESSION['state'][$parent]['edit_headers']['f_field']=$f_field;
    $_SESSION['state'][$parent]['edit_headers']['f_value']=$f_value;
//    $_SESSION['state'][$parent]['edit_headers']['parent_key']=$parent_key;
//    $_SESSION['state'][$parent]['edit_headers']['parent']=$parent;



    switch ($parent) {
    case 'site':
        $table='  `Page Header Dimension`  ';
        $where=sprintf(' where `Site Key`=%d',$parent_key);
        break;
    default:

        break;
    }



    $filter_msg='';
    $wheref='';

    if ($f_field=='name'  and $f_value!='')
        $wheref.=" and `Page Header Name` like '".addslashes($f_value)."%'";
//    elseif ($f_field=='title' and $f_value!='')
//    $wheref.=" and  `Page Store Title` like '".addslashes($f_value)."%'";



    $sql="select count(*) as total from $table  $where $wheref";
//print $sql;
    $result=mysql_query($sql);
    if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $total=$row['total'];
    }
    mysql_free_result($result);
    if ($wheref=='') {
        $filtered=0;
        $total_records=$total;
    } else {
        $sql="select count(*) as total from $table  $where  ";
//print $sql;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $total_records=$row['total'];
            $filtered=$total_records-$total;
        }
        mysql_free_result($result);

    }


    $rtext=$total_records." ".ngettext('header','headers',$total_records);
    if ($total_records>$number_results)
        $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
    else
        $rtext_rpp=' ('._('Showing all').')';


    $filter_msg='';

    switch ($f_field) {

    case('name'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any header with name")." <b>$f_value</b>* ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('headers with name')." <b>$f_value</b>*)";
        break;

    }

    $_dir=$order_direction;
    $_order=$order;


    if ($order=='pages')
        $order='`Number Pages`';
    else
        $order='`Page Header Name`';


    $adata=array();
    $sql="select *  from $table $where $wheref   order by $order $order_direction limit $start_from,$number_results    ";

    $res = mysql_query($sql);

    $total=mysql_num_rows($res);


    if ($parent=='site') {

        $site=new Site($parent_key);
        $default_header_key=$site->data['Site Default Header Key'];
    }


    while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {


        if ($default_header_key==$row['Page Header Key']) {
            $is_default=true;
        $default=_('Default');
        } else {
        $default='<div class="buttons small"><button class="positive" onClick="set_default_header('.$row['Page Header Key'].')">'._('Set as default').'</button></div>';
            $is_default=false;


        }

        $adata[]=array(
                     'id'=>$row['Page Header Key'],
                     'name'=>$row['Page Header Name'],
                     'pages'=>number($row['Number Pages']),
                     'image'=>'<img alt="preview" style="width:300px" src="image.php?id='.$row['Page Header Preview Image Key'].'"/>',
                     'default'=>$default,
                     'go'=>sprintf("<a href='edit_page_header.php?id=%d&referral=%s&referral_key=%s'><img src='art/icons/page_go.png' alt='go'></a>",$row['Page Header Key'],$parent,$parent_key),

                     'delete'=>(($row['Number Pages'] or $is_default)?'':"<img src='art/icons/cross.png'  alt='"._('Delete')."'  title='"._('Delete')."' />")

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


function list_footers_for_edition() {
    if (isset( $_REQUEST['parent']) and in_array($_REQUEST['parent'],array('site','department','family','product')) ) {
        $parent=$_REQUEST['parent'];

    } else {
        return;
    }

    if (isset( $_REQUEST['parent_key'])) {
        $parent_key=$_REQUEST['parent_key'];

    } else {
        return;
    }


    $conf=$_SESSION['state'][$parent]['edit_footers'];




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



    $_SESSION['state'][$parent]['edit_footers']['order']=$order;
    $_SESSION['state'][$parent]['edit_footers']['order_dir']=$order_direction;
    $_SESSION['state'][$parent]['edit_footers']['nr']=$number_results;
    $_SESSION['state'][$parent]['edit_footers']['sf']=$start_from;
    $_SESSION['state'][$parent]['edit_footers']['f_field']=$f_field;
    $_SESSION['state'][$parent]['edit_footers']['f_value']=$f_value;
//    $_SESSION['state'][$parent]['edit_footers']['parent_key']=$parent_key;
//    $_SESSION['state'][$parent]['edit_footers']['parent']=$parent;



    switch ($parent) {
    case 'site':
        $table='  `Page Footer Dimension`  ';
        $where=sprintf(' where `Site Key`=%d',$parent_key);
        break;
    default:

        break;
    }



    $filter_msg='';
    $wheref='';

    if ($f_field=='name'  and $f_value!='')
        $wheref.=" and `Page Footer Name` like '".addslashes($f_value)."%'";
//    elseif ($f_field=='title' and $f_value!='')
//    $wheref.=" and  `Page Store Title` like '".addslashes($f_value)."%'";



    $sql="select count(*) as total from $table  $where $wheref";
//print $sql;
    $result=mysql_query($sql);
    if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $total=$row['total'];
    }
    mysql_free_result($result);
    if ($wheref=='') {
        $filtered=0;
        $total_records=$total;
    } else {
        $sql="select count(*) as total from $table  $where  ";
//print $sql;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $total_records=$row['total'];
            $filtered=$total_records-$total;
        }
        mysql_free_result($result);

    }


    $rtext=$total_records." ".ngettext('footer','footers',$total_records);
    if ($total_records>$number_results)
        $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
    else
        $rtext_rpp=' ('._('Showing all').')';


    $filter_msg='';

    switch ($f_field) {

    case('name'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any footer with name")." <b>$f_value</b>* ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('footers with name')." <b>$f_value</b>*)";
        break;

    }

    $_dir=$order_direction;
    $_order=$order;


    if ($order=='pages')
        $order='`Number Pages`';
    else
        $order='`Page Footer Name`';


    $adata=array();
    $sql="select *  from $table $where $wheref   order by $order $order_direction limit $start_from,$number_results    ";

    $res = mysql_query($sql);

    $total=mysql_num_rows($res);


    if ($parent=='site') {

        $site=new Site($parent_key);
        $default_footer_key=$site->data['Site Default Footer Key'];
    }


    while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {


        if ($default_footer_key==$row['Page Footer Key']) {
            $is_default=true;
        $default=_('Default');
        } else {
        $default='<div class="buttons small"><button class="positive" onClick="set_default_footer('.$row['Page Footer Key'].')">'._('Set as default').'</button></div>';
            $is_default=false;


        }

        $adata[]=array(
                     'id'=>$row['Page Footer Key'],
                     'name'=>$row['Page Footer Name'],
                     'pages'=>number($row['Number Pages']),
                     'image'=>'<img alt="preview" style="width:300px" src="image.php?id='.$row['Page Footer Preview Image Key'].'"/>',
                     'default'=>$default,
                     'go'=>sprintf("<a href='edit_page_footer.php?id=%d&referral=%s&referral_key=%s'><img src='art/icons/page_go.png' alt='go'></a>",$row['Page Footer Key'],$parent,$parent_key),

                     'delete'=>(($row['Number Pages'] or $is_default)?'':"<img src='art/icons/cross.png'  alt='"._('Delete')."'  title='"._('Delete')."' />")

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


function set_default_header($data) {
    $site=new Site($data['site_key']);
    if (!$site->id) {
        $response= array('state'=>400,'msg'=>'Site not found');
        echo json_encode($response);

        exit;
    }
    $header=new PageHeader($data['header_key']);
    if (!$header->id) {
        $response= array('state'=>400,'msg'=>'Header not found');
        echo json_encode($response);

        exit;
    }

    if ($header->data['Site Key']!=$site->id) {
        $response= array('state'=>400,'msg'=>'Hader not in Site');
        echo json_encode($response);

        exit;
    }

    $site->set_default_header($data['header_key']);
    if ($site->updated) {
        $response= array('state'=>200,'action'=>'updated');
    } else
        $response= array('state'=>400,'msg'=>$site->msg);


    //$response=array();
    echo json_encode($response);

}

function set_default_footer($data) {
    $site=new Site($data['site_key']);
    if (!$site->id) {
        $response= array('state'=>400,'msg'=>'Site not found');
        echo json_encode($response);

        exit;
    }
    $footer=new PageFooter($data['footer_key']);
    if (!$footer->id) {
        $response= array('state'=>400,'msg'=>'Footer not found');
        echo json_encode($response);

        exit;
    }

    if ($footer->data['Site Key']!=$site->id) {
        $response= array('state'=>400,'msg'=>'Hader not in Site');
        echo json_encode($response);

        exit;
    }

    $site->set_default_footer($data['footer_key']);
    if ($site->updated) {
        $response= array('state'=>200,'action'=>'updated');
    } else
        $response= array('state'=>400,'msg'=>$site->msg);


    //$response=array();
    echo json_encode($response);


}



function list_page_product_lists_for_edition() {
    if (isset( $_REQUEST['parent']) and in_array($_REQUEST['parent'],array('site','page')) ) {
        $parent=$_REQUEST['parent'];

    } else {
        return;
    }

    if (isset( $_REQUEST['parent_key'])) {
        $parent_key=$_REQUEST['parent_key'];

    } else {
        return;
    }


    $conf=$_SESSION['state'][$parent]['edit_product_list'];




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



    $_SESSION['state'][$parent]['edit_product_list']['order']=$order;
    $_SESSION['state'][$parent]['edit_product_list']['order_dir']=$order_direction;
    $_SESSION['state'][$parent]['edit_product_list']['nr']=$number_results;
    $_SESSION['state'][$parent]['edit_product_list']['sf']=$start_from;
    $_SESSION['state'][$parent]['edit_product_list']['f_field']=$f_field;
    $_SESSION['state'][$parent]['edit_product_list']['f_value']=$f_value;
//    $_SESSION['state'][$parent]['edit_headers']['parent_key']=$parent_key;
//    $_SESSION['state'][$parent]['edit_headers']['parent']=$parent;



    switch ($parent) {
    case 'site':
        $table='  `Page Product List Dimension` L  left join `Page Store Dimension` P on (P.`Page Key`=L.`Page Key`)  ';
        $where=sprintf(' where `Site Key`=%d',$parent_key);
        break;
    case 'page':
        $table='  `Page Product List Dimension` L  left join `Page Store Dimension` P on (P.`Page Key`=L.`Page Key`)  ';
        $where=sprintf(' where L.`Page Key`=%d',$parent_key);
        break;
    default:

        break;
    }



    $filter_msg='';
    $wheref='';

    if ($f_field=='code'  and $f_value!='')
        $wheref.=" and `Page Product Form Code` like '".addslashes($f_value)."%'";
//    elseif ($f_field=='title' and $f_value!='')
//    $wheref.=" and  `Page Store Title` like '".addslashes($f_value)."%'";



    $sql="select count(*) as total from $table  $where $wheref";
//print $sql;
    $result=mysql_query($sql);
    if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $total=$row['total'];
    }
    mysql_free_result($result);
    if ($wheref=='') {
        $filtered=0;
        $total_records=$total;
    } else {
        $sql="select count(*) as total from $table  $where  ";
//print $sql;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $total_records=$row['total'];
            $filtered=$total_records-$total;
        }
        mysql_free_result($result);

    }


    $rtext=$total_records." ".ngettext('record','records',$total_records);
    if ($total_records>$number_results)
        $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
    else
        $rtext_rpp=' ('._('Showing all').')';


    $filter_msg='';

    switch ($f_field) {

    case('code'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any record with code")." <b>$f_value</b>* ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('records with code')." <b>$f_value</b>*)";
        break;

    }

    $_dir=$order_direction;
    $_order=$order;


    //if ($order=='pages')
    //    $order='`Number Pages`';
    //else
    $order='`Page Product Form Code`';


    $adata=array();
    $sql="select *  from $table $where $wheref   order by $order $order_direction limit $start_from,$number_results    ";

    $res = mysql_query($sql);

    $total=mysql_num_rows($res);





    while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

        switch ($row['List Order']) {
        case 'Code':
            $list_order=_('Code');
            break;
        case 'Name':
            $list_order=_('Name');
            break;
        case 'Special Characteristic':
            $list_order=_('Description');
            break;
        case 'Price':
            $list_order=_('Price');
            break;
        case 'RRP':
            $list_order=_('RRP');
            break;
        case 'Sales':
            $list_order=_('Sales');
            break;
        case 'Date':
            $list_order=_('Date');


        default:
            $list_order=$row['List Order'];
            break;
        }

        switch ($row['List Product Description']) {
        case 'Units Name':
            $description=_('<i>units</i> x <i>name</i>');
            break;
        case 'Units Special Characteristic':
            $description=_('<i>units</i> x <i>description</i>');
            break;
        case 'Units Name RRP':
            $description=_('<i>units</i> x <i>name</i> RRP');
            break;
        case 'Units Special Characteristic RRP':
            $description=_('<i>units</i> x <i>description</i> RRP');
            break;

        default:
            $description=$row['List Product Description'];
            break;
        }




        $adata[]=array(
                     'id'=>$row['Page Product Form Key'],
                     'code'=>$row['Page Product Form Code'],
                     'products'=>number($row['Page Product List Number Products']),
                     'type'=>($row['Page Product Form Type']=='CustomList'?_('Custom'):_('Family')).' ('.number($row['Page Product List Number Products']).')',
                     'order'=>$row['List Order'],
                     'order_formated'=>$list_order,
                     'description'=>$row['List Product Description'],
                     'range'=>$row['Range'],
                     'description_formated'=>$description,
                     'max'=>$row['List Max Items'],
                     'go'=>sprintf("<div class='buttons small'><button onClick='show_edit_product_list_dialog(".$row['Page Product Form Key'].")'>"._('Edit')."</button></div>"),


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

function list_page_product_buttons_for_edition() {
    if (isset( $_REQUEST['parent']) and in_array($_REQUEST['parent'],array('site','page')) ) {
        $parent=$_REQUEST['parent'];

    } else {
        return;
    }

    if (isset( $_REQUEST['parent_key'])) {
        $parent_key=$_REQUEST['parent_key'];

    } else {
        return;
    }


    $conf=$_SESSION['state'][$parent]['edit_product_button'];




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



    $_SESSION['state'][$parent]['edit_product_button']['order']=$order;
    $_SESSION['state'][$parent]['edit_product_button']['order_dir']=$order_direction;
    $_SESSION['state'][$parent]['edit_product_button']['nr']=$number_results;
    $_SESSION['state'][$parent]['edit_product_button']['sf']=$start_from;
    $_SESSION['state'][$parent]['edit_product_button']['f_field']=$f_field;
    $_SESSION['state'][$parent]['edit_product_button']['f_value']=$f_value;
//    $_SESSION['state'][$parent]['edit_headers']['parent_key']=$parent_key;
//    $_SESSION['state'][$parent]['edit_headers']['parent']=$parent;



    switch ($parent) {
    case 'site':
        $table='  `Page Product Dimension` B  left join `Page Store Dimension` P on (P.`Page Key`=B.`Page Key`)  left join `Product Dimension` PD on (B.`Product ID`=PD.`Product ID`)  ';
        $where=sprintf(' where `Site Key`=%d',$parent_key);
        break;
    case 'page':
        $table='  `Page Product Dimension` B  left join `Page Store Dimension` P on (P.`Page Key`=B.`Page Key`)  left join `Product Dimension` PD on (B.`Product ID`=PD.`Product ID`) ';
        $where=sprintf(' where P.`Page Key`=%d',$parent_key);
        break;
    default:

        break;
    }



    $filter_msg='';
    $wheref='';

    if ($f_field=='code'  and $f_value!='')
        $wheref.=" and `Product Code` like '".addslashes($f_value)."%'";
//    elseif ($f_field=='title' and $f_value!='')
//    $wheref.=" and  `Page Store Title` like '".addslashes($f_value)."%'";



    $sql="select count(*) as total from $table  $where $wheref";
//print $sql;
    $result=mysql_query($sql);
    if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $total=$row['total'];
    }
    mysql_free_result($result);
    if ($wheref=='') {
        $filtered=0;
        $total_records=$total;
    } else {
        $sql="select count(*) as total from $table  $where  ";
//print $sql;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $total_records=$row['total'];
            $filtered=$total_records-$total;
        }
        mysql_free_result($result);

    }


    $rtext=$total_records." ".ngettext('record','records',$total_records);
    if ($total_records>$number_results)
        $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
    else
        $rtext_rpp=' ('._('Showing all').')';


    $filter_msg='';

    switch ($f_field) {

    case('code'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any record with code")." <b>$f_value</b>* ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('records with code')." <b>$f_value</b>*)";
        break;

    }

    $_dir=$order_direction;
    $_order=$order;


    //if ($order=='pages')
    //    $order='`Number Pages`';
    //else
    $order='`Product Code`';


    $adata=array();
    $sql="select *  from $table $where $wheref   order by $order $order_direction limit $start_from,$number_results    ";

    $res = mysql_query($sql);

    $total=mysql_num_rows($res);





    while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {





        $adata[]=array(
                     'id'=>$row['Page Product From Key'],
                     'code'=>$row['Product Code'],

                     'go'=>sprintf("<div class='buttons small'><button onClick='show_edit_product_button_dialog(".$row['Page Product From Key'].")'>"._('Edit')."</button></div>"),


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


function edit_page_product_list($data) {

    $page_product_list_key=$data['id'];

    $sql=sprintf("select * from `Page Product List Dimension` where `Page Product Form Key`=%d",$page_product_list_key);
    $res=mysql_query($sql);
    if ($row=mysql_fetch_assoc($res)) {


        $key_translation=array(
                             'order'=>'List Order',
                             'show_rrp'=>'Show RRP',
                             'max'=>'List Max Items',
                             'range'=>'Range',
                             'code'=>'Page Product Form Code',
                             'description'=>'List Product Description'
                         );


        if (array_key_exists($data['key'],$key_translation)) {


            if ($data['key']=='range') {
                if (!(preg_match('/^[a-z0-9](\-)[a-z0-9]$/',$data['newvalue']) or $data['newvalue']=='')) {
                    $response= array('state'=>400,'msg'=>_('Wrong value, range should have the following format: a-b'),'key'=>$data['key']);
                    echo json_encode($response);
                    return;
                }

            }


            $sql=sprintf("update `Page Product List Dimension` set `%s`=%s where `Page Product Form Key`=%d",
                         $key_translation[$data['key']],
                         prepare_mysql($data['newvalue']),
                         $page_product_list_key);

            mysql_query($sql);

            $response= array('state'=>200,'action'=>'updated','msg'=>'', 'newvalue'=>$data['newvalue'],'key'=>$data['key'],'newdata'=>array());
            if ($data['key']=='description') {
                switch ($data['newvalue']) {
                case 'Units Name':
                    $description=_('<i>units</i> x <i>name</i>');
                    break;
                case 'Units Special Characteristic':
                    $description=_('<i>units</i> x <i>description</i>');
                    break;
                case 'Units Name RRP':
                    $description=_('<i>units</i> x <i>name</i> RRP');
                    break;
                case 'Units Special Characteristic RRP':
                    $description=_('<i>units</i> x <i>description</i> RRP');
                    break;

                default:
                    $description=$data['newvalue'];
                    break;
                }

                $response['newdata']=array('description_formated'=>$description);
            }

            if ($data['key']=='description') {
                switch ($data['newvalue']) {
                case 'Units Name':
                    $description=_('<i>units</i> x <i>name</i>');
                    break;
                case 'Units Special Characteristic':
                    $description=_('<i>units</i> x <i>description</i>');
                    break;
                case 'Units Name RRP':
                    $description=_('<i>units</i> x <i>name</i> RRP');
                    break;
                case 'Units Special Characteristic RRP':
                    $description=_('<i>units</i> x <i>description</i> RRP');
                    break;

                default:
                    $description=$data['newvalue'];
                    break;
                }

                $response['newdata']=array('description_formated'=>$description);
            }


            if ($data['key']=='order') {

                switch ($data['newvalue']) {
                case 'Code':
                    $list_order=_('Code');
                    break;
                case 'Name':
                    $list_order=_('Name');
                    break;
                case 'Special Characteristic':
                    $list_order=_('Description');
                    break;
                case 'Price':
                    $list_order=_('Price');
                    break;
                case 'RRP':
                    $list_order=_('RRP');
                    break;
                case 'Sales':
                    $list_order=_('Sales');
                    break;
                case 'Date':
                    $list_order=_('Date');


                default:
                    $list_order=$row['List Order'];
                    break;
                }

                $response['newdata']=array('order_formated'=>$list_order);
            }






        } else {

            $response= array('state'=>400,'msg'=>'Error 1','key'=>$data['key']);
        }

    } else {
        $response= array('state'=>400,'msg'=>'Error 2','key'=>$data['key']);

    }
    echo json_encode($response);

}

function update_page_preview_snapshot($data) {
    include_once('class.Image.php');
    $page=new Page($data['id']);
    if ($page->id) {
        $page->update_preview_snapshot();
    }
    $response= array('state'=>200,'image_key'=>$page->data['Page Preview Snapshot Image Key'],'formated_date'=>$page->get_preview_snapshot_date());
    echo json_encode($response);
}


function update_page_height($data) {

    $page=new Page($data['id']);
    if ($page->id) {
        $page->update_field_switcher('Page Footer Height',$data['footer'],'no_history');
        $page->update_field_switcher('Page Header Height',$data['header'],'no_history');
        $page->update_field_switcher('Page Content Height',$data['content'],'no_history');
    }

    $response= array('state'=>200);
    echo json_encode($response);
}





?>