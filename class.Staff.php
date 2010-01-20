<?php
/*
 File: Staff.php 

 This file contains the Staff Class

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/

  //require_once 'class.Name.php';
require_once 'class.Email.php';

class Staff extends DB_Table{

  var $data=array();
  var $items=array();
  var $status_names=array();
  var $id;
  var $tipo;
  var $contact=false;




  function __construct($key='id',$data=false) {

     $this->table_name='Staff';
     $this->ignore_fields=array('Staff Key');


     $this->status_names=array(0=>'new');


     if($key=='new' and is_array($data)){
       $this->create_order($data);
       if(!$this->id)
	 return;
       $key='id';
       $data=$this->id;
     }
     
     
     if(is_numeric($key) and !$data){
       $data=$key;
       $key='id';
     }
     $this->get_data($key,$data);
     

  }

  






  function get_data($key,$id){
    
    if($key=='alias')
      $sql=sprintf("select * from `Staff Dimension` where `Staff Alias`=%s",prepare_mysql($id));
    elseif($key=='id')
            $sql=sprintf("select * from  `Staff Dimension`     where `Staff Key`=%d",$id);
    else
      return;

   $result=mysql_query($sql);
   if($this->data=mysql_fetch_array($result, MYSQL_ASSOC)   )
     $this->id=$this->data['Staff Key'];
    

  }

  function get($key){
    if(!$this->id)
      return;
     if(array_key_exists($key,$this->data))
      return $this->data[$key];
     switch($key){
     case('Formated ID'):
     case("ID"):
        return $this->get_formated_id();
     case('First Name'):
       if(!is_object($this->contact))
	 $this->contact=new Contact($this->data['Staff Contact Key']);
       if($this->contact->id)
	 return $this->contact->data['Contact First Name'];
       else
	 return '';
       break;
     case('Surname'):
       if(!is_object($this->contact))
	 $this->contact=new Contact($this->data['Staff Contact Key']);
       if($this->contact->id)
	 return $this->contact->data['Contact Surname'];
       else
	 return '';
       break;
     case('Email'):
       if(!is_object($this->contact))
	 $this->contact=new Contact($this->data['Staff Contact Key']);
       if($this->contact->id)
	 return strip_tags($this->contact->data['Contact Main XHTML Email']);
       else
	 return '';
       break;


     }

    
  }

   function get_formated_id(){
     global $myconf;
     
     $sql="select count(*) as num from `Staff Dimension`";
     $res=mysql_query($sql);
     $min_number_zeros=4;
     if($row=mysql_fetch_array($res)){
       if(strlen($row['num'])-1>$min_number_zeros)
	 $min_number_zeros=strlen($row['num'])-01;
     }
     if(!is_numeric($min_number_zeros))
       $min_number_zeros=4;

     return sprintf("%s%0".$min_number_zeros."d",$myconf['staff_id_prefix'], $this->data['Staff ID']);

   }



   function find($raw_data){

     if(isset($raw_data['editor'])){
       foreach($raw_data['editor'] as $key=>$value){
	 
	 if(array_key_exists($key,$this->editor))
	 $this->editor[$key]=$value;
	 
       }
     }
     
     
     $create='';
     $update='';
     if(preg_match('/create/i',$options)){
       $create='create';
     }
     if(preg_match('/update/i',$options)){
       $update='update';
     }

     if($create){
        $child=new Contact ('find in staff create update',$raw_data);
	if($child->error){
	  $this->error=true;
	  $this->error=$child->error;
	  return;
	}
	$raw_data['Staff Contact Key']=$child->id;
	
     }



   }


   function create($data){
   //print_r($raw_data);


     $contact=new Contact($data['Staff Contact Key']);
     $data['Staff Name']=$contact->display('name');
     


    $this->data=$this->base_data();
    foreach($data as $key=>$value){
      if(array_key_exists($key,$this->data)){
	$this->data[$key]=_trim($value);
      }
    }

  

   


    $keys='';
    $values='';
    foreach($this->data as $key=>$value){
      $keys.=",`".$key."`";
      $values.=','.prepare_mysql($value,false);
    }
    $values=preg_replace('/^,/','',$values);
    $keys=preg_replace('/^,/','',$keys);

    $sql="insert into `Staff Dimension` ($keys) values ($values)";
    //print $sql;

    if(mysql_query($sql)){

      $this->id=mysql_insert_id();
      $this->get_data('id',$this->id);
      
      $this->update_company($company->id,true);
       $history_data=array(
			  'note'=>_('Staff Created')
			  ,'details'=>_trim(_('New staff')." \"".$this->data['Staff Name']."\"  "._('added'))
			  ,'action'=>'created'
			  );
      $this->add_history($history_data);
      $this->new=true;

      
  


      
    }else{
      // print "Error can not create staff $sql\n";
    }




   }


}