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
case('change_passwd'):
   $user=new User($_REQUEST['user_id']);
     if($user->id){
       $res=$user->change_password($value);
       echo json_encode($res);
       break;
       
   }else
     $response=array('state'=>400,'msg'=>_("User don't exist"));
   echo json_encode($response);
   
   break;
case('edit_user'):
   
   $user=new User($_REQUEST['user_id']);
   if($user->id){
     $data=array(
		 'value'=>$_REQUEST['newValue'],
		 'user_id'=>$LU->getProperty('auth_user_id')
		 );
     // print_r($data);
     $res=$user->set($_REQUEST['key'],$data);
     if($res['ok'])
       $response=array('state'=>200,'data'=>$user->get($_REQUEST['key']));
     else
       $response=array('state'=>400,$res['msg']);
   }else
     $response=array('state'=>400,'msg'=>_("User don't exist"));
   echo json_encode($response);
   
   break;


case ('add_user'):

  $data=array(
	      'handle'=>$_REQUEST['handle'],
	      'passwd'=>$_REQUEST['passwd'],
	      'tipo'=>$_REQUEST['tipo_user'],
	      );
  // print_r($data);

  switch($data['tipo']){
  case 1:
    $data['id_in_table']=$_REQUEST['id_in_table'];
    $staff=new Staff($data['id_in_table']);
    $data['name']=$staff->get('First Name');
    $data['surname']=$staff->get('Surname');
    $data['isactive']=1;
    $data['email']=$staff->get('Email');
    break;
  case 4:
    $data['name']=$_REQUEST['name'];
    $data['surname']=$_REQUEST['surname'];
    $data['isactive']=$_REQUEST['isactive'];
    $data['email']=$_REQUEST['email'];
    $data['groups']=preg_replace('/,%/','',$_REQUEST['groups']);

    break;
  }


  $user=new User('new',$data);

  if($user->new){
    $response= array('state'=>200);
  }else
    $response=array('state'=>400,'msg'=>$user->msg);
    
   echo json_encode($response);  
  break;
 case('add_user_borrarme'):



   $handle = $_REQUEST['handle'];
   $password = $_REQUEST['ep'];
   
   if($handle=='')
     {
       $response=array('state'=>401,'resp'=>_('Wrong user name'));
       echo json_encode($response);
       break;
     }
   
   $sql="select count(*) as numh  from liveuser_users where handle='".addslashes($handle)."'";
   $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
   if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
     if($row['numh']>0){
       $response=array('state'=>401,'resp'=>_('The user')." $handle "._("is already in the database!"));
       echo json_encode($response);
       break;
     }
   }else{
     $response=array('state'=>401,'resp'=>_('Unknown Error'));
     echo json_encode($response);
     break;
   }
   $handle=addslashes($_REQUEST['handle']);
   $pwd=addslashes($_REQUEST['ep']);
   $email =  '"'.addslashes($_REQUEST['email']).'"';
   $name= '"'.addslashes($_REQUEST['name']).'"';
   if($_REQUEST['surname']=='')
     $surname='NULL';
   else
     $surname=  '"'.addslashes($_REQUEST['surname']).'"';
   $isactive = $_REQUEST['isactive'][0];
   $user_type = 1; 
   $lang=$_REQUEST['lang'][0];


   $sql=sprintf("insert into liveuser_users (created,handle,passwd,isactive,name,surname,email,lang_id) values (NOW(),'%s','%s',%d,%s,%s,%s,'%s')",$handle,$pwd,$isactive,$name,$surname,$email,$lang);
   mysql_query($sql);
   //print $sql;
   $user_id=mysql_insert_id();
   if(is_numeric($user_id)){
     $sql=sprintf("insert into liveuser_perm_users (auth_user_id,perm_type) values (%d,%d)",$user_id,$user_type);
     mysql_query($sql);
   }else{
     $response=array('state'=>401,'resp'=>_('Unknown Error'));
     echo json_encode($response);
     break;

   }
   $puser_id=mysql_insert_id();
   
   if(isset($_REQUEST['group'])){
     foreach($_REQUEST['group'] as $group_id){
       if(is_numeric($group_id)){



	 $sql=sprintf("insert into liveuser_groupusers (perm_user_id,group_id) values (%d,%d)",$puser_id,$group_id);
	 mysql_query($sql);
       }
     }
   }


   $sql="
select u.authuserid,gu.group_id,group_concat(distinct g.group_id separator ',') as groups,u.isactive as isactive,u.name as name ,u.surname as surname,u.email as email,u.handle as handle,lower(c.code2) as countrycode,l.id as lang_id,c.Name as country from liveuser_users as u left join liveuser_perm_users as pu on (u.authuserid=pu.auth_user_id) left join liveuser_groupusers as gu on (gu.perm_user_id=pu.perm_user_id) left join liveuser_groups as g on (g.group_id=gu.group_id)  left join lang as l on (l.id=lang_id) left join country as c on (l.country_id=c.id)  where u.authuserid=$user_id group by u.authuserid ";
   $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
   while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
     $groups=split(',',$row['groups']);
     $sgroups='';

     $sgroups=array();
     foreach($groups as $group)
       if(is_numeric($group)){
	 $sgroups[]='<group id='.$group.'/>'.$_group[$group];
       }

     
     

     $name=trim($row['name'].' '.$row['surname'] );
       
     $lang='';
     if(is_numeric($row['lang_id']))
       $lang=$_lang[$row['lang_id']];
     
     if($row['isactive'])
       $active='<img src="art/icons/status_online.png" />';
     else
       $active='<img src="art/icons/status_offline.png" />';
     

     $data=array(
		 'handle'=>$row['handle'],
		 'id'=>$row['authuserid'],
		 'name'=>$name,
		 'email'=>$row['email'],
		 'lang'=>'<img src="art/flags/'.$row['countrycode'].'.gif" langid="'.$row['lang_id'].'" /> '.$lang,
		 'groups'=>$sgroups,
		 'password'=>'<img src="art/icons/key.png"/>',
		 'active'=>$active,
		 'delete'=>'<img src="art/icons/status_busy.png"/>'
		 );





   }

   $sql="select g.group_id as id, g.name ,ifnull(group_concat(distinct handle order by handle separator ', '),'') as users from liveuser_groups as g left join liveuser_groupusers as gu on (g.group_id=gu.group_id) left join liveuser_perm_users as pu   on (gu.perm_user_id=pu.perm_user_id  ) left join liveuser_users as u on (u.authuserid=pu.auth_user_id)  group by g.group_id  order by id      ";
   
   $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
   $gdata=array();
   while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
     $gdata[]=array(
		    'name'=>$_group[$row['id']],
		    'id'=>$row['id'],
		    'users'=>$row['users']
		    );
   }
   
   
   
   $response=array(
		   'state'=>200,
		   'newuser'=>$handle,
		   'data'=>$data,
		   'gdata'=>$gdata
		   );
 
   echo json_encode($response);

   break;
case('updateone'):

   $key=$_REQUEST['key'];
   switch($key){
   case('active'):
     $sql=sprintf("update liveuser_users set isactive=%d where authuserid=%d",$_REQUEST['value'],$_REQUEST['id'] );
     mysql_query($sql);
     $response=array('state'=>200);
     echo json_encode($response);
     break;

   case('password'):
     $password= mysql_real_escape_string($_REQUEST['value']);
     $sql=sprintf("update liveuser_users set passwd='%s' where authuserid=%d",$password,$_REQUEST['id'] );
     mysql_query($sql);
     $response=array('state'=>200,'newvalue'=>'******');
     echo json_encode($response);
     break;
   case('groups'):
     $groups=split(",",$_REQUEST['value']);


     $sql=sprintf("select perm_user_id  from liveuser_perm_users where auth_user_id=%d",$_REQUEST['id']);
     $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
     if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
       $puser_id=$row['perm_user_id'];
     }

     $sql=sprintf("delete from  liveuser_groupusers where perm_user_id=%d",$puser_id);
     mysql_query($sql);

     foreach($groups as $group){
       $group=preg_replace('/(.*?)id=/','',$group);
       $group=preg_replace('/>(.*?)$/','',$group);
       $group=str_replace('"','' ,$group);
       $group=str_replace('\\','',$group);
       $group=str_replace(' ','' ,$group);
       $group=str_replace('/','' ,$group);
       if(is_numeric($group)){

	 

	 $sql=sprintf("insert into liveuser_groupusers (perm_user_id,group_id) values (%d,%d)",$puser_id,$group);
	 mysql_query($sql);
       }
       
     }





     
     $sql="select g.group_id as id, g.name ,ifnull(group_concat(distinct handle order by handle separator ', '),'') as users from liveuser_groups as g left join liveuser_groupusers as gu on (g.group_id=gu.group_id) left join liveuser_perm_users as pu   on (gu.perm_user_id=pu.perm_user_id  ) left join liveuser_users as u on (u.authuserid=pu.auth_user_id)  group by g.group_id  order by id      ";
     
     $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
     $gdata=array();
     while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
       $gdata[]=array(
		      'name'=>$_group[$row['id']],
		      'id'=>$row['id'],
		      'users'=>$row['users']
		      );
     }
     $response=array('state'=>200,'gdata'=>$gdata);
     echo json_encode($response);

     break;
   case('email'):
     $email=mysql_real_escape_string($_REQUEST['value']);
     $sql=sprintf("update liveuser_users set email='%s' where authuserid=%d",$email,$_REQUEST['id']);
     mysql_query($sql);
     $response=array('state'=>200,'newvalue'=>$_REQUEST['value']);
     echo json_encode($response);
     break;
   case('name'):
     $name='NULL';
     $surname='NULL';
     
     // $names=str_replace("\'","'",$_REQUEST['value']);
     $names=mysql_real_escape_string($_REQUEST['value']);
     $names=split(" ",$names);
     if(count($names)==1)
       $name="'".$names[0]."'";
     
     else{
       $name="'".array_shift($names)."'";
       $surname="'".join(" ",$names)."'";
     }
     
     $sql=sprintf("update liveuser_users set name=%s , surname=%s  where authuserid=%d",$name,$surname,$_REQUEST['id']);

     mysql_query($sql);
     $response=array('state'=>200,'data'=>array('newvalue'=>$_REQUEST['value']));
     echo json_encode($response);
     break;
   case('lang'):
     $lang=$_REQUEST['value'];
     $lang=preg_replace('/(.*?)langid=/','',$lang);
     $lang=preg_replace('/>(.*?)$/','',$lang);
     $lang=str_replace('"','',$lang);
     $lang=str_replace('\\','',$lang);
     $lang=str_replace(' ','',$lang);
     $lang=str_replace('/','',$lang);


     if(is_numeric($lang)){
       $sql=sprintf("update liveuser_users set lang_id=%d  where authuserid=%d",$lang,$_REQUEST['id']);
       mysql_query($sql);
       $response=array('state'=>200,'data'=>array('newvalue'=>$_REQUEST['value']));
     }else
       $response=array('state'=>$lang);

     echo  json_encode($response);
     break;
   case('delete'):

     if($_REQUEST['value']==1){
       $sql=sprintf("select perm_user_id  from liveuser_perm_users where auth_user_id=%d",$_REQUEST['id']);
       $result = mysql_query($sql) or die('Query failed: ' . mysql_error());
       if($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	 $puser_id=$row['perm_user_id'];
       }


       $sql=sprintf("delete from liveuser_users where authuserid=%d",$_REQUEST['id']);
       mysql_query($sql);
       $sql=sprintf("delete from liveuser_perm_users where perm_user_id=%d",$puser_id);
       mysql_query($sql);
       $sql=sprintf("delete from liveuser_userrights where perm_user_id=%d",$puser_id);
       mysql_query($sql);
       $sql=sprintf("delete from liveuser_group_users where perm_user_id=%d",$puser_id);
       mysql_query($sql);
     
     

       $response=array('state'=>200);
       echo json_encode($response);
     }
     break;
   default:
     $response=array('state'=>404,'msg'=>_('Sub-operation not found'));
     echo json_encode($response);
}


?>