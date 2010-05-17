<?php
/*
 File: ar_users.php 

 Ajax Server Anchor for the User Class

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/
require_once 'common.php';
require_once 'class.User.php';
require_once 'class.Staff.php';


if(!isset($_REQUEST['tipo']))
  {
    $response=array('state'=>405,'msg'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
  }

$tipo=$_REQUEST['tipo'];
switch($tipo){
case('staff_users'):
    list_staff_users();
    break;
 case('users'):
    list_users();
    break;
 case('groups'):
    list_groups();
    break;
 default:
   $response=array('state'=>404,'msg'=>_('Operation not found'));
   echo json_encode($response);
   
 }

function list_users(){
 $conf=$_SESSION['state']['users']['staff'];
  if(isset( $_REQUEST['sf']))
     $start_from=$_REQUEST['sf'];
   else
     $start_from=$conf['sf'];
   if(isset( $_REQUEST['nr']))
     $number_results=$_REQUEST['nr'];
   else
     $number_results=$conf['nr'];
  if(isset( $_REQUEST['o']))
    $order=$_REQUEST['o'];
  else
    $order=$conf['order'];
  if(isset( $_REQUEST['od']))
    $order_dir=$_REQUEST['od'];
  else
    $order_dir=$conf['order_dir'];
    if(isset( $_REQUEST['f_field']))
     $f_field=$_REQUEST['f_field'];
   else
     $f_field=$conf['f_field'];

  if(isset( $_REQUEST['f_value']))
     $f_value=$_REQUEST['f_value'];
   else
     $f_value=$conf['f_value'];
  if(isset( $_REQUEST['where']))
     $where=$_REQUEST['where'];
   else
     $where=$conf['where'];
  
   if(isset( $_REQUEST['tableid']))
    $tableid=$_REQUEST['tableid'];
  else
    $tableid=0;

   
 if(isset( $_REQUEST['type']))
    $type=$_REQUEST['type'];
  else
    $type=$conf['type'];


 $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
   $_order=$order;
   $_dir=$order_direction;
   $filter_msg='';


  $_SESSION['state']['users']['staff']=array(
						 'type'=>$type
						 ,'order'=>$order
						 ,'order_dir'=>$order_direction
						 ,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);



  $where=sprintf('where `User Type`=%s',prepare_mysql($type));


  $filter_msg='';
  $wheref='';
  if($f_field=='handle' and $f_value!='')
    $wheref.=" and  `User Handle` like '".addslashes($f_value)."%'";
  elseif($f_field=='name' and $f_value!='')
    $wheref.=" and  `User Alias` like '%".addslashes($f_value)."%'";
  
  $sql="select count(*) as total from `User Dimension`  $where $wheref   ";
  
     $res=mysql_query($sql);
     if($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
       $total=$row['total'];
     }
     mysql_free_result($res);
     if($wheref==''){
       $filtered=0;
       $total_records=$total;
     } else{
       $sql="select count(*) as total from `Product Dimension`  $where   ";
       $res=mysql_query($sql);
       if($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
	 $total_records=$row['total'];
	 $filtered=$total_records-$total;
       }
  mysql_free_result($res);
   }

     
   $rtext=$total_records." ".ngettext('user','users',$total_records);
     if($total_records>$number_results)
       $rtext_rpp=sprintf("(%d%s)",$number_results,_('rpp'));
     else
       $rtext_rpp=_('(Showing all)');


     $translations=array('handle'=>'`User Handle`');
     if(array_key_exists($order,$translations))
       $order=$translations[$order];
     
       
     

   $adata=array();
   $sql="Select *,(select GROUP_CONCAT(UGUD.`User Group Key`) from `User Group User Bridge` UGUD left join  `User Group Dimension` UGD on (UGUD.`User Group Key`=UGD.`User Group Key`)      where UGUD.`User Key`=U.`User Key` ) as Groups,(select GROUP_CONCAT(URSB.`Scope Key`) from `User Right Scope Bridge` URSB where URSB.`User Key`=U.`User Key` and `Scope`='Store'  ) as Stores,(select GROUP_CONCAT(URSB.`Scope Key`) from `User Right Scope Bridge` URSB where URSB.`User Key`=U.`User Key`and `Scope`='Warehouse'  ) as Warehouses  from `User Dimension` U  $where $wheref   order by $order $order_direction limit $start_from,$number_results;";
   //print $sql;
   $res=mysql_query($sql);
   
   while($row=mysql_fetch_array($res)) {


 
     $groups=preg_split('/,/',$row['Groups']);
     $stores=preg_split('/,/',$row['Stores']);
     $warehouses=preg_split('/,/',$row['Warehouses']);

$locale=$row['User Preferred Locale'];
preg_match('/^[a-z]{2}/',$locale,$match);
$lang=$match[0];
     $adata[]=array(
		   'handle'=>$row['User Handle'],
		   'tipo'=>$row['User Type'],
		   'id'=>$row['User Key'],
		   'name'=>$row['User Alias'],
		   'email'=>$row['User Email'],
		   'lang'=>$lang,
		   'groups'=>$groups,
		   'stores'=>$stores,
		   'warehouses'=>$warehouses,
		   'password'=>'<img style="cursor:pointer" user_name="'.$row['User Alias'].'" user_id="'.$row['User Key'].'" onClick="change_passwd(this)" src="art/icons/key.png"/>',
		   'passwordmail'=>($row['User Email']!=''?'<img src="art/icons/key_go.png"/>':''),
		   //'isactive'=>($row['User Active']=='Yes'?'<img src="art/icons/status_online.png" alt="'._('active').'" Title="'._('Active').'"  />':'<img src="art/icons/status_offline.png" Title="'._('Inactive').'"  alt="'._('inactive').'/>'),
		   'isactive'=>$row['User Active'],

		   'delete'=>'<img src="art/icons/status_busy.png"/>'
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
			 'total_records'=>$total,
			 'records_offset'=>$start_from,
			 'records_returned'=>$total,
			 'records_perpage'=>$number_results,
			 'records_text'=>$rtext,
			 'records_order'=>$order,
			 'records_order_dir'=>$order_dir,
			 'filtered'=>$filtered,
			 'rtext'=>$rtext,
			'rtext_rpp'=>$rtext_rpp
			 )
		   );
     
   echo json_encode($response);
}
function list_groups(){
$conf=$_SESSION['state']['users']['groups'];
  if(isset( $_REQUEST['sf']))
     $start_from=$_REQUEST['sf'];
   else
     $start_from=$conf['sf'];
   if(isset( $_REQUEST['nr']))
     $number_results=$_REQUEST['nr'];
   else
     $number_results=$conf['nr'];
  if(isset( $_REQUEST['o']))
    $order=$_REQUEST['o'];
  else
    $order=$conf['order'];
  if(isset( $_REQUEST['od']))
    $order_dir=$_REQUEST['od'];
  else
    $order_dir=$conf['order_dir'];
    if(isset( $_REQUEST['f_field']))
     $f_field=$_REQUEST['f_field'];
   else
     $f_field=$conf['f_field'];

  if(isset( $_REQUEST['f_value']))
     $f_value=$_REQUEST['f_value'];
   else
     $f_value=$conf['f_value'];
  if(isset( $_REQUEST['where']))
     $where=$_REQUEST['where'];
   else
     $where=$conf['where'];
  
   if(isset( $_REQUEST['tableid']))
    $tableid=$_REQUEST['tableid'];
  else
    $tableid=0;

 $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
   $_order=$order;
   $_dir=$order_direction;
   $filter_msg='';


  $_SESSION['state']['users']['groups']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);


   $filtered=0;

   $data=array();
$_order=$order;
if($order=='name'){
$_order='`User Group Name`';
}else
    $_order='`User Group Name`';
 
   $sql="select *,(select GROUP_CONCAT(`User Alias`) from `User Dimension` U left join `User Group User Bridge` UGUB on (U.`User Key`=UGUB.`User Key`) 
   where UGUB.`User Group Key`=UG.`User Group Key` ) as Users from `User Group Dimension` UG  order by $_order $order_direction limit $start_from,$number_results       ";
//print $sql;
   $res=mysql_query($sql);
   $total=mysql_num_rows($res);
   if($total<$number_results)
     $rtext=$total.' '.ngettext('work group','work groups',$total);
   else
     $rtext='';
   while($row=mysql_fetch_array($res, MYSQL_ASSOC)){

     $data[]=array(
		   'name'=>$row['User Group Name'],
		   'id'=>$row['User Group Key'],
		   'users'=>$row['Users']
		   );
   }
     mysql_free_result($res);
   $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$data,
			 	 'sort_key'=>$_order,
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


function list_staff_users() {
    global $myconf;

    $conf=$_SESSION['state']['users']['staff'];
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

 




    if (isset( $_REQUEST['tableid']))
        $tableid=$_REQUEST['tableid'];
    else
        $tableid=0;

    $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
    $_order=$order;
    $_dir=$order_direction;



    $_SESSION['state']['users']['staff']=array(
                                           
                                             'order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);



    $wheref='';
    if ($f_field=='name' and $f_value!=''  )
        $wheref.=" and  name like '%".addslashes($f_value)."%'    ";
    else if ($f_field=='position_id' or $f_field=='area_id'   and is_numeric($f_value) )
        $wheref.=sprintf(" and  $f_field=%d ",$f_value);

 $where.=" and `User Key` IS NOT NULL  ";
   

    $sql="select count(*) as total from `Staff Dimension` SD  left join `User Dimension` on (`User Parent Key`=`Staff Key`) $where $wheref";


    $res=mysql_query($sql);
    if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
        $total=$row['total'];
    }
    if ($wheref!='') {
        $sql="select count(*) as total from `Staff Dimension` SD  left join `User Dimension` on (`User Parent Key`=`Staff Key`)  $where ";
        $res=mysql_query($sql);
        if ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
            $total_records=$row['total'];
            $filtered=$row['total']-$total;
        }

    } else {
        $filtered=0;
        $total_records=$total;
    }

    mysql_free_result($res);
    $rtext=$total_records." ".ngettext('record','records',$total_records);
    if ($total_records>$number_results)
        $rtext.=sprintf(" <span class='rtext_rpp'>(%d%s)</span>",$number_results,_('rpp'));
    $filter_msg='';

    switch ($f_field) {
    case('name'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There is no staff with name")." <b>*".$f_value."*</b> ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('staff with name')." <b>*".$f_value."*</b>) <span onclick=\"remove_filter($tableid)\" id='remove_filter$tableid' class='remove_filter'>"._('Show All')."</span>";
        break;
    case('area_id'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There is no staff on area")." <b>".$f_value."</b> ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('staff on area')." <b>".$f_value."</b>) <span onclick=\"remove_filter($tableid)\" id='remove_filter$tableid' class='remove_filter'>"._('Show All')."</span>";
        break;
    case('position_id'):
        if ($total==0 and $filtered>0)
            $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There is no staff with position")." <b>".$f_value."</b> ";
        elseif($filtered>0)
        $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total ("._('staff with position')." <b>".$f_value."</b>) <span onclick=\"remove_filter($tableid)\" id='remove_filter$tableid' class='remove_filter'>"._('Show All')."</span>";
        break;

    }

$_order=$order;
    if ($order=='name')
        $order='`Staff Name`';
    elseif($order=='position')
    $order='position';
    else
        $order='`Staff Name`';
    $sql="select (select GROUP_CONCAT(distinct `Company Position Title`) from `Company Position Staff Bridge` PSB  left join `Company Position Dimension` P on (`Company Position Key`=`Position Key`) where PSB.`Staff Key`= SD.`Staff Key`) as position, `Staff Alias`,`Staff Key`,`Staff Name` from `Staff Dimension` SD  left join `User Dimension` on (`User Parent Key`=`Staff Key`) $where  $wheref and `User Type`='Staff' order by $order $order_direction limit $start_from,$number_results";

    $sql="select `User Alias`,(select GROUP_CONCAT(URSB.`Scope Key`) from `User Right Scope Bridge` URSB where URSB.`User Key`=U.`User Key` and `Scope`='Store'  ) as Stores,(select GROUP_CONCAT(URSB.`Scope Key`) from `User Right Scope Bridge` URSB where URSB.`User Key`=U.`User Key`and `Scope`='Warehouse'  ) as Warehouses ,(select GROUP_CONCAT(UGUD.`User Group Key`) from `User Group User Bridge` UGUD left join  `User Group Dimension` UGD on (UGUD.`User Group Key`=UGD.`User Group Key`)      where UGUD.`User Key`=U.`User Key` ) as Groups,`User Key`,`User Active`, `Staff Alias`,`Staff Key`,`Staff Name` from `Staff Dimension` SD  left join `User Dimension` U on (`User Parent Key`=`Staff Key`) $where  $wheref and (`User Type`='Staff' or `User Type` is null ) order by $order $order_direction limit $start_from,$number_results";
    // print $sql;
    $adata=array();
    $res=mysql_query($sql);
    while ($data=mysql_fetch_array($res)) {

 $groups=preg_split('/,/',$data['Groups']);
      $stores=preg_split('/,/',$data['Stores']);
     $warehouses=preg_split('/,/',$data['Warehouses']);

        //   $_id=$myconf['staff_prefix'].sprintf('%03d',$data['Staff Key']);
        //  $id=sprintf('<a href="staff.php?id=%d">%s</a>',$data['Staff Key'],$_id);
        $is_active='No';

        if ($data['User Active']=='Yes')
            $is_active='Yes';

$password='';
  if ($data['User Key']){
  $password='<img style="cursor:pointer" user_name="'.$data['User Alias'].'" user_id="'.$data['User Key'].'" onClick="change_passwd(this)" src="art/icons/key.png"/>';
  }

        $adata[]=array(
                     'id'=>$data['User Key'],
                     'staff_id'=>$data['Staff Key'],
                     'alias'=>$data['Staff Alias'],
                     'name'=>$data['Staff Name'],
                     		   'password'=>$password,

		   'groups'=>$groups,
   'stores'=>$stores,
		   'warehouses'=>$warehouses,
                     'isactive'=>$is_active
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
                                      'total_records'=>$total,
                                      'records_offset'=>$start_from,
                                      'records_returned'=>$start_from+$total,
                                      'records_perpage'=>$number_results,
                                      'rtext'=>$rtext,
                                      'records_order'=>$_order,
                                      'records_order_dir'=>$order_dir,
                                      'filtered'=>$filtered
                                     )
                   );
    echo json_encode($response);
}


?>