<?php
/*
 File: Customer.php 

 This file contains the Customer Class

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0


 The customer dimension is the  critical element for a CRM, a customer can be a Company or a Contact.

*/
include_once('class.DB_Table.php');
include_once('class.Contact.php');
include_once('class.Order.php');
include_once('class.Address.php');
include_once('class.Attachment.php');

class Customer extends DB_Table{
 var $contact_data=false;
  var $ship_to=array();

  function __construct($arg1=false,$arg2=false) {

    $this->table_name='Customer';
    $this->ignore_fields=array(
			       'Customer Key'
			       ,'Customer Has More Orders Than'
			       ,'Customer Has More  Invoices Than'
			       ,'Customer Has Better Balance Than'
			       ,'Customer Is More Profiteable Than'
			       ,'Customer Order More Frecuently Than'
			       ,'Customer Older Than'
			       ,'Customer Orders Position'
			       ,'Customer Invoices Position'
			       ,'Customer Balance Position'
			       ,'Customer Profit Position'
			       ,'Customer Order Interval'
			       ,'Customer Order Interval STD'
			       ,'Customer Orders Top Percentage'
			       ,'Customer Invoices Top Percentage'
			       ,'Customer Balance Top Percentage'
			       ,'Customer Profits Top Percentage'
			       ,'Customer First Order Date'
			       ,'Customer Last Order Date'
			       ,'Customer Last Ship To Key'
			       );


    $this->status_names=array(0=>'new');
    
    if(is_numeric($arg1) and !$arg2){
      $this->get_data('id',$arg1);
       return;
    }
    if (preg_match('/create anonymous|create anonimous$/i',$arg1)) {
      $this->create_anonymous();
      return;
    }
    

    if($arg1=='new'){
      $this->find($arg2,'create');
       return;
    }elseif(preg_match('/^find staff/',$arg1)){
	$this->find_staff($arg2,$arg1);
	return;
    }elseif(preg_match('/^find/',$arg1)){
	$this->find($arg2,$arg1);
	return;
    }

    $this->get_data($arg1,$arg2);
    
    
  }

/*
    Method: find_staff
    Find Staff Customer 
*/

 function find_staff($staff,$options=''){
   
   $sql=sprintf("select * from `Customer Dimension` where `Customer Staff`='Yes' and `Customer Staff Key`=%d",$staff->id);
   //print $sql;exit;
   $result=mysql_query($sql);
   if($this->data=mysql_fetch_array($result, MYSQL_ASSOC)   ){
     
     $this->id=$this->data['Customer Key'];
   }

   if(!$this->id and preg_match('/create|new/',$options)){
     $raw_data['Customer Type']='Person';
     
     $raw_data['Customer Staff']='Yes';
     if($staff->id){



       $contact=new Contact($staff->data['Staff Contact Key']);
       $_raw_data=$contact->data;
       foreach($_raw_data as $key=>$value){
	 $raw_data[preg_replace('/Contact/','Customer',$key)]=$value;
       }

       $raw_data['Customer Staff Key']=$staff->id;
       $raw_data['Customer Main Contact Key']=$staff->data['Staff Contact Key'];
       $raw_data['Customer Name']=$staff->data['Staff Name'];
     }else{
       $contact=new Contact('create anonymous');
       $_raw_data=$contact->data;
       foreach($raw_data as $key=>$value){
	 $raw_data[preg_replace('/Contact/','Customer',$key)]=$value;
       }
       $raw_data['Customer Staff Key']=0;
       $raw_data['Customer Main Contact Key']=$contact->id;
       $raw_data['Customer Name']=_('Unknown Staff');
     }

     
     $this->create($raw_data);
   }


}
  /*

    Method: find
    Find Customer with similar data
   
   
   */  
  function find($raw_data,$options=''){

    //print "===================================\n";

    $this->found_child=false;
    $this->found_child_key=0;
    $this->found=false;
    $this->found_key=0;


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

    if(
       !isset($raw_data['Customer Store Key']) or 
       !preg_match('/^\d+$/i',$raw_data['Customer Store Key']) ){
      $raw_data['Customer Store Key']=1;
      
    }
    
    //  print_r($raw_data);
    if(!isset($raw_data['Customer Type']) or !preg_match('/^(Company|Person)$/i',$raw_data['Customer Type']) ){
      

      // Try to detect if is a company or a person
      if(
	 (isset($raw_data['Customer Company Name']) and  $raw_data['Customer Company Name']!='' )
	 or (isset($raw_data['Customer Company Key']) and  $raw_data['Customer Company Key'] )
	 )$raw_data['Customer Type']='Company';
      else
	$raw_data['Customer Type']='Person';
      
	    
    }
    $raw_data['Customer Type']=ucwords($raw_data['Customer Type']);
    //print $raw_data['Customer Type']."\n";
    if($raw_data['Customer Type']=='Person'){
      $child=new Contact ('find in customer use old_id',$raw_data);
    }else{
      $child=new Company ('find in customer use old_id',$raw_data);
    }

    // print_r($child);

    if($child->found){
      
      
      $this->found_child=true;
      $this->found_child_key=$child->found_key;
      $customer_found_keys=$child->get_customers_key();
      if(count($customer_found_keys)>0){
	foreach($customer_found_keys as $customer_found_key){
	  $tmp_customer=new Customer($customer_found_key);
	  if($tmp_customer->data['Customer Store Key']==$raw_data['Customer Store Key']){
	    $this->found=true;
	    $this->found_key=$customer_found_key;
	  }
	}
      }
	

    }else{
      $this->candidate=$child->candidate;

    }
    

    

    // print "$options";
    if($this->found){
      $this->get_data('id',$this->found_key);
      //  print "customer Found: ".$this->found_key."  \n";
    }
    
    if($create and (
    ($raw_data['Customer Main Contact Name']=='' and  $raw_data['Customer Type']=='Person') 
    or ($raw_data['Customer Company Name']=='' and  $raw_data['Customer Type']=='company')
    )
    ){
    $this->create_anonymous();
    return;
    }
    
    
    if($create){
    
      if($this->found){

	   if($raw_data['Customer Type']=='Person'){
	  $child=new Contact ('find in customer create update',$raw_data);
   	}else{
	  $child=new Company ('find in customer create update',$raw_data);
	}

	//	$child->editor=$this->editor;
	$this->update($raw_data);

      }else{

	if($this->found_child){
	  //	    print "----------------------------------******************\n";
	  //print_r($raw_data);
	  //print_r( $child->translate_data($raw_data,'from customer')  );
	  //print "-----------------------------------------------\n";
	 
	  if($raw_data['Customer Type']=='Person'){

	    $contact=new contact('find in customer create update',$raw_data);
	    $raw_data['Customer Main Contact Key']=$contact->id;
	    
	  }else{
	    $company=new company('find in customer create update',$raw_data);
	    $raw_data['Customer Company Key']=$company->id;
	  }	  
	  
	  
	}
	$this->create($raw_data);

      }

    }
    

    
 }


  function get_data($tag,$id){
    if($tag=='id')
      $sql=sprintf("select * from `Customer Dimension` where `Customer Key`=%s",prepare_mysql($id));
    elseif($tag=='email')
      $sql=sprintf("select * from `Customer Dimension` where `Customer Email`=%s",prepare_mysql($id));
    elseif($tag='all'){
      $this->find($id);
      return true;
    }else
       return false;
    $result=mysql_query($sql);
    if($this->data=mysql_fetch_array($result, MYSQL_ASSOC)   ){
      $this->id=$this->data['Customer Key'];
    }
  }
  
/*   function compley_get_data($data){ */
/*     $weight=array( */
/* 		   'Same Other ID'=>100 */
/* 		   ,'Same Email'=>100 */
/* 		   ,'Similar Email'=>20 */

/* 		   ); */

      
/*       if($data['Customer Email']!=''){ */
/* 	$has_email=true; */
/* 	$sql=sprintf("select `Email Key` from `Email Dimension` where `Email`=%s",prepare_mysql($data['Customer Email'])); */
/* 	$result=mysql_query($sql); */
/* 	if($row=mysql_fetch_array($result, MYSQL_ASSOC)){ */
/* 	  $email_key=$row['Email Key']; */
/* 	  $sql=sprintf("select `Subject Key` from `Email Bridge` where `Email Key`=%s and `Subject Type`='Customer'",prepare_mysql($email_key)); */
/* 	  $result2=mysql_query($sql); */
/* 	  if($row2=mysql_fetch_array($result2, MYSQL_ASSOC)){ */
/* 	    // Email found assuming this is th customer */
	    
/* 	    return $row2['Subject Key']; */
/* 	  } */
/* 	} */
/*       }else */
/* 	$has_email=false; */

/*      $telephone=Telephone::display(Telephone::parse_telecom(array('Telecom Original Number'=>$data['Telephone']),$data['Country Key'])); */
/*     // Email not found check if we have a mantch in other id */
/*      if($data['Customer Other ID']!=''){ */
/*        $no_other_id=false; */
/* 	$sql=sprintf("select `Customer Key`,`Customer Name`,`Customer Main Telephone` from `Customer Dimension` where `Customer Other ID`=%s",prepare_mysql($data['Customer Other ID'])); */
/* 	$result=mysql_query($sql); */
/* 	$num_rows = mysql_num_rows($result); */
/* 	if($num_rows==1){ */
/* 	  $row=mysql_fetch_array($result, MYSQL_ASSOC); */
/* 	  return $row['Customer Key']; */
/* 	}elseif($num_rows>1){ */
/* 	  // Get the candidates */
	  
/* 	  while($row=mysql_fetch_array($result, MYSQL_ASSOC)){ */
/* 	    $candidate[$row['Customer Key']]['field']=array('Customer Other ID'); */
/* 	    $candidate[$row['Customer Key']]['points']=$weight['Same Other ID']; */
/* 	    // from this candoateed of one has the same name we wouls assume that this is the one */
/* 	    if($data['Customer Name']!='' and $data['Customer Name']==$row['Customer Name']) */
/* 	      return $row2['Customer Key']; */
/* 	    if($telephone!='' and $telephone==$row['Customer Main Telephone']) */
/* 	      return $row2['Customer Key']; */

	    
/* 	  } */
	  



/* 	} */
/*      }else */
/*        $no_other_id=true; */
    



/*      //If customer has the same name ond same address */
/*      //$addres_finger_print=preg_replace('/[^\d]/','',$data['Full Address']).$data['Address Town'].$data['Postal Code']; */


/*      //if thas the same name,telephone and address get it */
    




/*      if($has_email){ */
/*      //Get similar candidates from email */
       
/*        $sql=sprintf("select levenshtein(UPPER(%s),UPPER(`Email`)) as dist1,levenshtein(UPPER(SOUNDEX(%s)),UPPER(SOUNDEX(`Email`))) as dist2, `Subject Key`  from `Email Dimension` left join `Email Bridge` on (`Email Bridge`.`Email Key`=`Email Dimension`.`Email Key`)  where dist1<=2 and  `Subject Type`='Customer'   order by dist1,dist2 limit 20" */
/* 		    ,prepare_mysql($data['Customer Email']) */
/* 		    ,prepare_mysql($data['Customer Email']) */
/* 		    ); */
/*        $result=mysql_query($sql); */
/*        while($row=mysql_fetch_array($result, MYSQL_ASSOC)){ */
/* 	  $candidate[$row['Subject Key']]['field'][]='Customer Other ID'; */
/* 	  $dist=0.5*$row['dist1']+$row['dist2']; */
/* 	  if($dist==0) */
/* 	    $candidate[$row['Subject Key']]['points']+=$weight['Same Other ID']; */
/* 	  else */
/* 	    $candidate[$row['Subject Key']]['points']=$weight['Similar Email']/$dist; */
       
/*        } */
/*      } */
 

/*      //Get similar candidates from emailby name */
/*      if($data['Customer Name']!=''){ */
/*      $sql=sprintf("select levenshtein(UPPER(%s),UPPER(`Customer Name`)) as dist1,levenshtein(UPPER(SOUNDEX(%s)),UPPER(SOUNDEX(`Customer Name`))) as dist2, `Customer Key`  from `Customer Dimension`   where dist1<=3 and  `Subject Type`='Customer'   order by dist1,dist2 limit 20" */
/* 		  ,prepare_mysql($data['Customer Name']) */
/* 		  ,prepare_mysql($data['Customer Name']) */
/* 		  ); */
/*      $result=mysql_query($sql); */
/*      while($row=mysql_fetch_array($result, MYSQL_ASSOC)){ */
/*        $candidate[$row['Subject Key']]['field'][]='Customer Name'; */
/*        $dist=0.5*$row['dist1']+$row['dist2']; */
/*        if($dist==0) */
/* 	 $candidate[$row['Subject Key']]['points']+=$weight['Same Customer Name']; */
/*        else */
/* 	 $candidate[$row['Subject Key']]['points']=$weight['Similar Customer Name']/$dist; */
       
/*      } */
/*      } */
/*      // Address finger print */
     



/*  } */




   function load($key='',$arg1=false){
     switch($key){
    case('contact_data'):
    case('contact data'):
      $contact=new Contact($this->get('customer contact key'));
      if($contact->id)
	$this->contact_data=$contact->data;
      else
	$this->errors[]='Error geting contact data object. Contact key:'.$this->get('customer contact key');
      break;
    case('ship to'):
      
      $sql=sprintf('select * from `Ship To Dimension` where `Ship To Key`=%d ',$arg1);

      //  print $sql;
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	$this->ship_to[$row['Ship To Key']]=$row;
	

      }else
	$this->errors[]='Error loading ship to data. Ship to Key:'.$arg1;

      break; 
     }

  }


   function create($raw_data,$args=''){

     $main_telephone_key=false;
     $main_fax_key=false;
     $main_email_key=false;

     //print_r($raw_data);
       //  exit;
     $this->data=$this->base_data();
     foreach($raw_data as $key=>$value){
       if(array_key_exists($key,$this->data)){
	 $this->data[$key]=_trim($value);
       }
    }


     $this->data['Customer ID']=$this->new_id();
     if($this->data['Customer Type']=='Company'){
       $this->data['Customer Main Email Key']=0;
       $this->data['Customer Main XHTML Email']='';
       $this->data['Customer Main Plain Email']='';
       $this->data['Customer Main Telephone Key']=0;
       $this->data['Customer Main Telephone']='';
       $this->data['Customer Main Plain Telephone']='';
       $this->data['Customer Main FAX Key']=0;
       $this->data['Customer Main FAX']='';
       $this->data['Customer Main Plain FAX']='';
       $company=new company('find in customer create update',$raw_data);
       $company_key=$company->id;
       
       
      if($company->data['Company Main Email Key']){
	$main_email_key=$company->data['Company Main Email Key'];
      }
      if($company->data['Company Main Telephone Key']){
	$main_telephone_key=$company->data['Company Main Telephone Key'];
      }
      if($company->data['Company Main FAX Key']){
	$main_fax_key=$company->data['Company Main FAX Key'];
      }
     
      
      
      
     }elseif($this->data['Customer Type']=='Person'){
       $this->data['Customer Main Email Key']=0;
       $this->data['Customer Main XHTML Email']='';
       $this->data['Customer Main Plain Email']='';
       $this->data['Customer Main Telephone Key']=0;
       $this->data['Customer Main Telephone']='';
       $this->data['Customer Main Plain Telephone']='';
       $this->data['Customer Main FAX Key']=0;
       $this->data['Customer Main FAX']='';
       $this->data['Customer Main Plain FAX']='';
      
      if(!$this->data['Customer Main Contact Key'])
	$contact=new contact('find in customer create update',$raw_data);
      else
	$contact=new contact($this->data['Customer Main Contact Key']);
      
      $contact_key=$contact->id;
      

      //address!!!!!!!!!!!!!
      

      



      if($contact->data['Contact Main Email Key']){
	$main_email_key=$contact->data['Contact Main Email Key'];
      }
      if($contact->data['Contact Main Telephone Key']){
	$main_telephone_key=$contact->data['Contact Main Telephone Key'];
	
      }
      if($contact->data['Contact Main FAX Key']){
	$main_fax_key=$contact->data['Contact Main FAX Key'];
	
      }
      $this->data['Customer Company Key']=0;
      
      
     }else{
       $this->error=true;
       $this->msg.=' Error, Wrong Customer Type ->'.$this->data['Customer Type'];
     }
     
     if($this->data['Customer First Contacted Date']==''){
      $this->data['Customer First Contacted Date']=date('Y-m-d H:i:s');
     }
     
     $this->data['Customer Active Ship To Records']=0;
     $this->data['Customer Total Ship To Records']=0;
    
    
    // Ok see if we have a billing address!!!

    if(isset($raw_data['Customer Billing Address'])){
      $billing_address=new address('find create update',$raw_data['Customer Billing Address']);
      $this->data['Customer Main Address Key']=$billing_address->id;
      $this->data['Customer Main Address Country Code']=$billing_address->data['Address Country Code'];
      $this->data['Customer Main Address 2 Alpha Country Code']=$billing_address->data['Address Country 2 Alpha Code'];

      $this->data['Customer Main Location']=$billing_address->data['Address Location'];
      $this->data['Customer Main Address Town']=$billing_address->data['Address Town'];
      $this->data['Customer Main Address Postal Code']=$billing_address->data['Address'];
      $this->data['Customer Main Address Country First Division']=$billing_address->data['Address Country First Division'];
      $this->data['Customer Main XHTML Address']=$billing_address->display('html'); 
      $this->data['Customer Main Plain Address']=$billing_address->display('plain'); 


    }else{
     if($this->data['Customer Type']=='Company'){
       
       $billing_address_key=$company->data['Company Main Address Key'];
     }else{
       $billing_address_key=$contact->data['Contact Main Address Key'];
     }

     if($billing_address_key){
       $billing_address=new address($billing_address_key);
       $this->data['Customer Main Address Key']=$billing_address->id;
       $this->data['Customer Main Address Country Code']=$billing_address->data['Address Country Code'];
       $this->data['Customer Main Address Country 2 Alpha Code']=$billing_address->data['Address Country 2 Alpha Code'];
       $this->data['Customer Main Location']=$billing_address->data['Address Location'];
       $this->data['Customer Main Address Town']=$billing_address->data['Address Town'];
       $this->data['Customer Main Address Postal Code']=$billing_address->data['Address Postal Code'];
       $this->data['Customer Main Address Country First Division']=$billing_address->data['Address Country First Division'];
       $this->data['Customer Main XHTML Address']=$billing_address->display('html');
       $this->data['Customer Main Plain Address']=$billing_address->display('plain');

     }

    }
      

    
    // print_r($raw_data);
    
 // print_r($this->data);
 //  print "in class cust xxxxxxxxxxxxxxxxxxxxxxxxxxxx\n";
 //  exit;
    $keys='';
    $values='';
    foreach($this->data as $key=>$value){
      $keys.=",`".$key."`";

      if(preg_match('/Key$/',$key))
	$values.=','.prepare_mysql($value);
      else
	$values.=','.prepare_mysql($value,false);
    }
    $values=preg_replace('/^,/','',$values);
    $keys=preg_replace('/^,/','',$keys);

    $sql="insert into `Customer Dimension` ($keys) values ($values)";
  
    if(mysql_query($sql)){
      
      $this->id=mysql_insert_id();
      $this->get_data('id',$this->id);
      
     

    
      if($this->data['Customer Type']=='Company'){
	$this->update_company($company_key,true);
	
      }else{
	$this->update_contact($contact_key,true);

      }
       $history_data=array(
			  'note'=>_('Customer Created')
			  ,'details'=>_trim(_('New customer')." \"".$this->data['Customer Name']."\"  "._('added'))
			  ,'action'=>'created'
			  );
      $this->add_history($history_data);
      $this->new=true;
      if($main_email_key){
	$this->update_email($main_email_key);
      }

      if($main_telephone_key){

	$this->add_tel(array(
			     'Telecom Key'=>$main_telephone_key
			     ,'Telecom Type'=>'Contact Telephone'
			     ));
	
      }
      if($main_fax_key){
	$this->add_tel(array(
			     'Telecom Key'=>$main_fax_key
			     ,'Telecom Type'=>'Contact Fax'
			     ));
      }
      


    }else{
      // print "Error can not create supplier $sql\n";
    }






   }


 private function create_anonymous() {

   $contact=new Contact('create anonymous');
   $raw_data=$contact->data;
   foreach($raw_data as $key=>$value){
     $raw_data[preg_replace('/Contact/','Customer',$key)]=$value;
   }
    $raw_data['Customer Staff Key']=0;
    $raw_data['Customer Main Contact Key']=$contact->id;
    $raw_data['Customer Name']=_('Unknown Customer');


 $this->data=$this->base_data();
     foreach($raw_data as $key=>$value){
       if(array_key_exists($key,$this->data)){
	 $this->data[$key]=_trim($value);
       }
    }

 $keys='';
    $values='';
    foreach($this->data as $key=>$value){
      $keys.=",`".$key."`";

      if(preg_match('/Key$/',$key))
	$values.=','.prepare_mysql($value);
      else
	$values.=','.prepare_mysql($value,false);
    }
    $values=preg_replace('/^,/','',$values);
    $keys=preg_replace('/^,/','',$keys);

    $sql="insert into `Customer Dimension` ($keys) values ($values)";
  
    if(mysql_query($sql)){
      
      $this->id=mysql_insert_id();
      $this->get_data('id',$this->id);
      
      $history_data=array(
			  'note'=>_('Anonymous Customer Created')
			  ,'details'=>_trim(_('New anonymous customer added').' ('.$this->get_formated_id_link().')' )
			  ,'action'=>'created'
			  
			  );
      $this->add_history($history_data);
      $this->new=true;
      
      }




 }




 function add_ship_to($ship_to_key,$is_principal='Yes'){
   
   $is_active='Yes';
   



   if($is_principal!='Yes')
     $is_principal='No';
   
   
   $sql=sprintf("insert into `Customer Ship To Bridge` values (%d,%d,'%s','Yes',0,NOW(),NOW()) on duplicate key update `Is Principal`='%s' ,`Is Active`='Yes'  "
		,$this->id
		,$ship_to_key
		,$is_principal
		,$is_principal);
   
   mysql_query($sql);

   
   if($is_principal='Yes'){
     $this->update_main_ship_to($ship_to_key);

   }
   
   $this->update_ship_to_stats();
 }



function update_ship_to_stats(){
$sql=sprintf("select count(*) as total,sum(if(`Is Active`='Yes',1,0)) as active from `Customer Ship To Bridge` where `Customer Key`=%d ",$this->id);
   // print $sql;
   $result=mysql_query($sql);
   if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
     $active=$row['active'];
     $total=$row['total'];
   }


   $sql=sprintf("update `Customer Dimension` set `Customer Active Ship To Records`=%d,`Customer Total Ship To Records`=%d where `Customer Key`=%d"
		
		  ,$active
		  ,$total
		  ,$this->id
		  );
     mysql_query($sql);


}


 function update_main_ship_to($ship_to_key=false){

   if($ship_to_key)
     $ship_to=new Ship_To($ship_to_key);

   else
     $ship_to=new Ship_To($this->data['Customer Main Ship To Key']);


 


 $sql=sprintf("update `Customer Dimension` set `Customer Main Ship To Key`=%d,`Customer Main Ship To Town`=%s,`Customer Main Ship To Postal Code`=%s,`Customer Main Ship To Country`=%s,`Customer Main Ship To Country Key`=%s,`Customer Main Ship To Country Code`=%s,`Customer Main Ship To Country 2 Alpha Code`=%s where `Customer Key`=%d"
		  ,$ship_to->id
		  ,prepare_mysql($ship_to->data['Ship To Town'])
		  ,prepare_mysql($ship_to->data['Ship To Postal Code'])
		  ,prepare_mysql($ship_to->data['Ship To Country'])
		  ,prepare_mysql($ship_to->data['Ship To Country Key'])
		  ,prepare_mysql($ship_to->data['Ship To Country Code'])
		  ,prepare_mysql($ship_to->data['Ship To Country 2 Alpha Code'])
	
		  ,$this->id
		  );
     mysql_query($sql);

     
     $this->data['Customer Main Ship To Key']=$ship_to->id;
     $this->data['Customer Main Ship To Town']=$ship_to->data['Ship To Town'];
     $this->data['Customer Main Ship To Country']=$ship_to->data['Ship To Country'];
     $this->data['Customer Main Ship To Postal Code']=$ship_to->data['Ship To Postal Code'];
     $this->data['Customer Main Ship To Country Key']=$ship_to->data['Ship To Country Key'];
     $this->data['Customer Main Ship To Country Code']=$ship_to->data['Ship To Country Code'];
     $this->data['Customer Main Ship To Country 2 Alpha Code']=$ship_to->data['Ship To Country 2 Alpha Code'];




 }




   /*Function: update_field_switcher
   */
 function update_field_switcher($field,$value,$options=''){

   switch($field){
   case('Note'):
     $this->add_note($value);
     break;
   case('Attach'):
     $this->add_attach($value);
     break; 
   case('Customer Main Telephone'):
   case('Customer Main Plain Telephone'):
   case('Customer Main Telephone Key'):
   case('Customer Main FAX'):
   case('Customer Main Plain FAX'):
   case('Customer Main FAX Key'):
   case('Customer Main XHTML Email'):
   case('Customer Main Email Key'):
   case('Customer Main Plain Email'):
     return;
     break;
   default:
     $base_data=$this->base_data();
     if(array_key_exists($field,$base_data)) {
       $this->update_field($field,$value,$options);
     }
  }
 }

 
 /*
  function:update_main_contact_key
  */
 function update_main_contact_key($contact_key=false){

   if(!$contact_key)
     return;
   
    $contact=new Contact($contact_key);
   if(!$contact->id)
     return;

   if($this->data['Customer Type']=='Company'){
     $sql=sprintf("select `Is Active` from `Contact Bridge` where `Subject`='Company' and `Subjet Key`=%d and `Contact Key`=%d "
		  ,$this->data['Customer Comapany Key']
		  ,$contact->id
		  );
     $res=mysql_query($sql);
     $number=mysql_num_rows($res);
     if($number==0){
       $this->error=true;
       $msg=_('Contact not in company').".";
       $this->msg.=$msg;
       $this->msg_updated.=$msg;
       return;
     }


   }
   $old_key_value=$this->data['Customer Main Contact Key'];
   $old_value=$this->data['Customer Main Contact Name'];
   $old_contact=new Contact ($this->data['Customer Main Contact Key']);
   $sql=sprintf("update `Customer Dimension` set `Customer Main Contact Key`=%d ,`Customer Main Contact Name`=%s where `Customer Key`=%d"
		,$contact->id
		,prepare_mysql($contact->display('name'))
		,$this->id
		);
 
   mysql_query($sql);
   $this->data['Customer Main Contact Key']=$contact->id;
   $this->data['Customer Main Contact Name']=$contact->display('name');

   $updated=false;
   if($this->data['Customer Main Contact Key']==$old_key_value){
     if($this->data['Customer Main Contact Name']!=$old_value){
       $updated=true;
       $field='Customer Contact Name';
       $note=$field.' '._('Changed');
       $details=$field.' '._('changed from')." \"".$old_value."\" "._('to')." \"".$this->data['Customer Main Contact Name']."\"";
     }
       
   }else{// new contact
       $updated=true;
       $field='Customer Contact';
       $note=$field.' '._('Changed');
      
	 $details=$field.' '._('changed from')." \""
	 .$old_value."\"(".$old_contact->get("ID").") "
	 ._('to')." \"".$this->data['Customer Main Contact Name']."\" (".$contact->get("ID").")";

   }


   if($updated){
     $this->updated=true;
     $this->msg=$details;
     $this->msg_updated=$details;
       $history_data=array(
			   'indirect_object'=>$field
			   ,'details'=>$details
			   ,'note'=>$note
			   );
       $this->add_history($history_data);
   }

 }

/*
  function:update_email
  */
 function update_email($email_key=false){
   if(!$email_key)
     return;
   $email=new Email($email_key);
   if(!$email->id){
     $this->msg='Email not found';
     return;

   }


$old_value=$this->data['Customer Main Email Key'];
   if($old_value  and $old_value!=$email_key   ){
     $this->remove_email();
     }

   $sql=sprintf("insert into `Email Bridge` values (%d,'Customer',%d,%s,'Yes','Yes')",
                $email->id,
                $this->id,
                prepare_mysql(_('Customer Main Email'))
                );
   mysql_query($sql);

   $old_plain_email=$this->data['Customer Main Plain Email'];
   $this->data['Customer Main Email Key']=$email->id;
   $this->data['Customer Main Plain Email']=$email->display('plain');
   $this->data['Customer Main XHTML Email']=$email->display('xhtml');
   $sql=sprintf("update `Customer Dimension` set `Customer Main Email Key`=%d,`Customer Main Plain Email`=%s,`Customer Main XHTML Email`=%s where `Customer Key`=%d"

                ,$this->data['Customer Main Email Key']
                ,prepare_mysql($this->data['Customer Main Plain Email'])
                ,prepare_mysql($this->data['Customer Main XHTML Email'])
                ,$this->id
                );
   if(mysql_query($sql)){
if($old_plain_email!=$this->data['Customer Main Plain Email']){
       $this->updated=true;
       $note=_('Email changed');
       if($old_value){
        
         $details=_('Customer email changed from')." \"".$old_plain_email."\" "._('to')." \"".$this->data['Customer Main Plain Email']."\"";
       }else{
         $details=_('Customer email set to')." \"".$this->data['Customer Main Plain Email']."\"";
       }

       $history_data=array(
                           'indirect_object'=>'Email'
                           ,'indirect_object'=>$email->id
                           ,'details'=>$details
                           ,'note'=>$note
                           );
       $this->add_history($history_data);
     }



   }else{
     $this->error=true;

   }


 }







 /*
  function:update_contact
  */
function update_contact($contact_key=false) {
$this->associated=false;
    if (!$contact_key)
        return;
    $contact=new contact($contact_key);
    if (!$contact->id) {
        $this->msg='contact not found';
        return;

    }


    $old_contact_key=$this->data['Customer Main Contact Key'];

    if ($old_contact_key  and $old_contact_key!=$contact_key   ) {
        $this->remove_contact();
    }

    $sql=sprintf("insert into `Contact Bridge` values (%d,'Customer',%d,'Yes','Yes')",
                 $contact->id,
                 $this->id
                );
    mysql_query($sql);
    if(mysql_affected_rows()){
    $this->associated=true;
    
    }
    
    

    $old_name=$this->data['Customer Main Contact Name'];
    if ($old_name!=$contact->display('name')) {


        if ($this->data['Customer Type']=='Person'
            and $this->data['Customer Name']!=$contact->display('name')) {
            $old_customer_name=$this->data['Customer Name'];
            $this->data['Customer Name']=$contact->display('name');
            $this->data['Customer File As']=$contact->data['Contact File As'];
            $sql=sprintf("update `Customer Dimension` set `Customer Name`=%d,`Customer File As`=%s where `Customer Key`=%d"
                         ,prepare_mysql($this->data['Customer Name'])
                         ,prepare_mysql($this->data['Customer File As'])
                         ,$this->id
                        );
            mysql_query($sql);
            $note=_('Contact name changed');
            $details=_('Customer Name changed from')." \"".$old_customer_name."\" "._('to')." \"".$this->data['Customer Name']."\"";
            $history_data=array(
                              'indirect_object'=>'Customer Name'
                                                ,'details'=>$details
                                                           ,'note'=>$note
                                                                   ,'action'=>'edited'
                          );
            $this->add_history($history_data);

        }

        $this->data['Customer Main Contact Key']=$contact->id;
        $this->data['Customer Main Contact Name']=$contact->display('name');
        $sql=sprintf("update `Customer Dimension` set `Customer Main Contact Key`=%d,`Customer Main Contact Name`=%s where `Customer Key`=%d"

                     ,$this->data['Customer Main Contact Key']
                     ,prepare_mysql($this->data['Customer Main Contact Name'])
                     ,$this->id
                    );
        mysql_query($sql);



        $this->updated=true;






        $note=_('Customer contact name changed');
        if ($old_contact_key) {
            $details=_('Customer contact name changed from')." \"".$old_name."\" "._('to')." \"".$this->data['Customer Main Contact Name']."\"";
        } else {
            $details=_('Customer contact set to')." \"".$this->data['Customer Main Contact Name']."\"";
        }

        $history_data=array(
                          'indirect_object'=>'Customer Main Contact Name'

                                            ,'details'=>$details
                                                       ,'note'=>$note
                                                               ,'action'=>'edited'
                      );
        $this->add_history($history_data);

    }


if($this->associated){
 $note=_('Contact name changed');
            $details=_('Contact')." ".$contact->display('name')." (".$contact->get_formated_id_link().") "._('associated with Customer:')." ".$this->data['Customer Name']." (".$this->get_formated_id_link().")";
            $history_data=array(
                              'indirect_object'=>'Customer Name'
                                                ,'details'=>$details
                                                           ,'note'=>$note
                                                                   ,'action'=>'edited',
                                                                    'deep'=>2
                          );
            $this->add_history($history_data,true);
}

}

function update_company($company_key=false) {
$this->associated=false;
    if (!$company_key)
        return;
    $company=new company($company_key);
    if (!$company->id) {
        $this->msg='company not found';
        return;

    }


    $old_company_key=$this->data['Customer Company Key'];

    if ($old_company_key  and $old_company_key!=$company_key   ) {
        $this->remove_company();
    }

    $sql=sprintf("insert into `Company Bridge` values (%d,'Customer',%d,'Yes','Yes')",
                 $company->id,
                 $this->id
                );
    mysql_query($sql);
    if(mysql_affected_rows()){
    $this->associated=true;
    
    }
    
    

    $old_name=$this->data['Customer Company Name'];
    if ($old_name!=$company->data['Company Name']) {


        if ($this->data['Customer Type']=='Company' and $this->data['Customer Name']!=$company->data['Company Name']) {
            $old_customer_name=$this->data['Customer Name'];
            $this->data['Customer Name']=$company->data['Company Name'];
            $this->data['Customer File As']=$company->data['Company File As'];
            $sql=sprintf("update `Customer Dimension` set `Customer Main Name`=%d,`Customer File As`=%s where `Customer Key`=%d"
                         ,prepare_mysql($this->data['Customer Name'])
                         ,prepare_mysql($this->data['Customer File As'])
                         ,$this->id
                        );
            mysql_query($sql);
            $note=_('Company name changed');
            $details=_('Customer Name changed from')." \"".$old_customer_name."\" "._('to')." \"".$this->data['Customer Name']."\"";
            $history_data=array(
                              'indirect_object'=>'Customer Name'
                                                ,'details'=>$details
                                                           ,'note'=>$note
                                                                   ,'action'=>'edited'
                          );
            $this->add_history($history_data);

        }

        $this->data['Customer Company Key']=$company->id;
        $this->data['Customer Company Name']=$company->data['Company Name'];
        $sql=sprintf("update `Customer Dimension` set `Customer Company Key`=%d,`Customer Company Name`=%s where `Customer Key`=%d"

                     ,$this->data['Customer Company Key']
                     ,prepare_mysql($this->data['Customer Company Name'])
                     ,$this->id
                    );
        mysql_query($sql);



        $this->updated=true;






        $note=_('Customer company name changed');
        if ($old_company_key) {
            $details=_('Customer company name changed from')." \"".$old_name."\" "._('to')." \"".$this->data['Customer Company Name']."\"";
        } else {
            $details=_('Customer company set to')." \"".$this->data['Customer Company Name']."\"";
        }

        $history_data=array(
                          'indirect_object'=>'Customer Company Name'

                                            ,'details'=>$details
                                                       ,'note'=>$note
                                                               ,'action'=>'edited'
                      );
        $this->add_history($history_data);

    }


if($this->associated){
 $note=_('Company name changed');
            $details=_('Company')." ".$company->data['Company Name']." (".$company->get_formated_id_link().") "._('associated with Customer:')." ".$this->data['Customer Name']." (".$this->get_formated_id_link().")";
            $history_data=array(
                              'indirect_object'=>'Customer Name'
                                                ,'details'=>$details
                                                           ,'note'=>$note
                                                                   ,'action'=>'edited',
                                                                    'deep'=>2
                          );
            $this->add_history($history_data,true);
}

$this->update_contact($company->data['Company Main Contact Key']);

}

 public function update_no_normal_data(){


  $sql="select min(`Order Date`) as date   from `Order Dimension` where `Order Customer Key`=".$this->id;
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)){

	$first_order_date=date('U',strtotime($row['date']));
	if($row['date']!='' 
	   and (
		$this->data['Customer First Contacted Date']=='' 
		or ( date('U',strtotime($this->data['Customer First Contacted Date']))>$first_order_date  )
		)
	   ){
	  $sql=sprintf("update `Customer Dimension` set `Customer First Contacted Date`=%d  where `Customer Key`=%d"
		       ,prepare_mysql($row['date'])
		       ,$this->id
		       );
	  mysql_query($sql);
	}	 
      }
      // $address_fuzzy=false;
      // $email_fuzzy=false;
      // $tel_fuzzy=false;
      // $contact_fuzzy=false;


      // $address=new Address($this->get('Customer Main Address Key'));
      // if($address->get('Fuzzy Address'))
      // 	$address_fuzzy=true;
      


 }


 public function update_activity($date=''){
   if($date=='')
     $date=date("Y-m-d H:i:s");
     $sigma_factor=3.2906;//99.9% value assuming normal distribution
     $this->data['Customer Lost Date']='';
     $this->data['Actual Customer']='Yes';
     $orders= $this->data['Customer Orders'];

     //print $this->id." $orders  \n";

     if($orders==0){
       $this->data['Active Customer']='No';
       $this->data['Customer Type by Activity']='Prospect';
       $this->data['Actual Customer']='No';
     }elseif($orders==1){
       $sql="select avg((`Customer Order Interval`)+($sigma_factor*`Customer Order Interval STD`)) as a from `Customer Dimension` where `Customer Orders`>1";
	 
	 $result2=mysql_query($sql);
	 if($row2=mysql_fetch_array($result2, MYSQL_ASSOC)){
	   $average_max_interval=$row2['a'];
	   //print "$average_max_interval\n";
	   if(is_numeric($average_max_interval)){
	     //print "xxxxxxxxxxxxxx\n";
	     if(   (strtotime('now')-strtotime($this->data['Customer Last Order Date']))/(3600*24)  <  $average_max_interval){
	       // print "xxxxxxxxxxxxxx1\n";
		     
	       $this->data['Active Customer']='Maybe';
	       $this->data['Customer Type by Activity']='New';
	       
	     }else{
	       
	       
	       //print "xxxxxxxxxxxxxx2\n";

	       $this->data['Active Customer']='No';
	       $this->data['Customer Type by Activity']='Inactive';
	       //   print $this->data['Customer Last Order Date']." +$average_max_interval days\n"; 
	       $this->data['Customer Lost Date']=date("Y-m-d H:i:s",strtotime($this->data['Customer Last Order Date']." +".ceil($average_max_interval)." day" ));
	     }

	     
	     //print "+++++++++++++\n";
	   }else{
	     $this->data['Active Customer']='Unknown';
	     $this->data['Customer Type by Activity']='Unknown';
	   }
	   
	 }else{
	    $this->data['Active Customer']='Unknown';
	    $this->data['Customer Type by Activity']='Unknown';
	 }
	 //print "-----------\n";

     }else{
       //print $this->data['Customer Last Order Date']."\n";

       $last_date=date('U',strtotime($this->data['Customer Last Order Date']));
       //print ((date('U')-$last_date)/3600/24)."\n";
       // print_r($this->data);
       
       if($orders==2){
	  $sql="select avg(`Customer Order Interval`) as i, avg((`Customer Order Interval`)+($sigma_factor*`Customer Order Interval STD`)) as a from `Customer Dimension` where `Customer Orders`>2";
	 
	 $result2=mysql_query($sql);
	 if($row2=mysql_fetch_array($result2, MYSQL_ASSOC)){
	   $a_inteval=$row2['a'];
	   $i_inteval=$row2['i'];
	 }
	 if($i_inteval==0)
	   $factor=3;	   
	 else
	   $factor=$a_inteval/$i_inteval;
	     
	 $interval=ceil($this->data['Customer Order Interval']*$factor);
	
       }else
	 $interval=ceil($this->data['Customer Order Interval']+($sigma_factor*$this->data['Customer Order Interval STD']));




       if( (date('U')-$last_date)/24/3600  <$interval){
	   $this->data['Active Customer']='Yes';
	   $this->data['Customer Type by Activity']='Active';
	 }else{
	   $this->data['Active Customer']='No';
	   $this->data['Customer Type by Activity']='Inactive';
	   $this->data['Customer Lost Date']=date("Y-m-d H:i:s",strtotime($this->data['Customer Last Order Date']." +".$interval." day" ));
	 }
     }
 
         $sql=sprintf("update `Customer Dimension` set `Actual Customer`=%s,`Active Customer`=%s,`Customer Type by Activity`=%s , `Customer Lost Date`=%s where `Customer Key`=%d"
		      ,prepare_mysql($this->data['Actual Customer'])
		      ,prepare_mysql($this->data['Active Customer'])
		      ,prepare_mysql($this->data['Customer Type by Activity'])
		      ,prepare_mysql($this->data['Customer Lost Date'])
		      ,$this->id
		    );

	 //	  print "$sql\n";
	 if(!mysql_query($sql))
	 exit("$sql error");
     
 }

 /*
   function: update_orders
   Update order stats
  */

 public function update_orders(){
    $sigma_factor=3.2906;//99.9% value assuming normal distribution

     $sql="select sum(`Order Profit Amount`) as profit,sum(`Order Net Refund Amount`+`Order Net Credited Amount`) as net_refunds,sum(`Order Outstanding Balance Net Amount`) as net_outstanding, sum(`Order Balance Net Amount`) as net_balance,sum(`Order Tax Refund Amount`+`Order Tax Credited Amount`) as tax_refunds,sum(`Order Outstanding Balance Tax Amount`) as tax_outstanding, sum(`Order Balance Tax Amount`) as tax_balance, min(`Order Date`) as first_order_date ,max(`Order Date`) as last_order_date,count(*)as orders, sum(if(`Order Current Payment State` like '%Cancelled',1,0)) as cancelled,  sum( if(`Order Current Payment State` like '%Paid%'    ,1,0)) as invoiced,sum( if(`Order Current Payment State` like '%Refund%'    ,1,0)) as refunded,sum(if(`Order Current Dispatch State`='Unknown',1,0)) as unknown   from `Order Dimension` where `Order Customer Key`=".$this->id;

     $this->data['Customer Orders']=0;
     $this->data['Customer Orders Cancelled']=0;
     $this->data['Customer Orders Invoiced']=0;
     $this->data['Customer First Order Date']='';
     $this->data['Customer Last Order Date']='';
     $this->data['Customer Order Interval']='';
     $this->data['Customer Order Interval STD']='';
     $this->data['Actual Customer']='No';
     $this->data['New Served Customer']='No';
     $this->data['Active Customer']='Unkwnown';
     $this->data['Customer Net Balance']=0;
     $this->data['Customer Net Refunds']=0;
     $this->data['Customer Net Payments']=0;
     $this->data['Customer Tax Balance']=0;
     $this->data['Customer Tax Refunds']=0;
     $this->data['Customer Tax Payments']=0;
     $this->data['Customer Profit']=0;

     //print $sql;exit;
     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       
       $this->data['Customer Orders']=$row['orders'];
       $this->data['Customer Orders Cancelled']=$row['cancelled'];
       $this->data['Customer Orders Invoiced']=$row['invoiced'];
       
       $this->data['Customer Net Balance']=$row['net_balance'];
       $this->data['Customer Net Refunds']=$row['net_refunds'];
       $this->data['Customer Net Payments']=$row['net_balance']-$row['net_outstanding'];
       $this->data['Customer Outstanding Net Balance']=$row['net_outstanding'];

       $this->data['Customer Tax Balance']=$row['tax_balance'];
       $this->data['Customer Tax Refunds']=$row['tax_refunds'];
       $this->data['Customer Tax Payments']=$row['tax_balance']-$row['tax_outstanding'];
       $this->data['Customer Outstanding Tax Balance']=$row['tax_outstanding'];

       $this->data['Customer Profit']=$row['profit'];


       if($this->data['Customer Orders']>0){
	 $this->data['Customer First Order Date']=$row['first_order_date'];
	 $this->data['Customer Last Order Date']=$row['last_order_date'] ;
	 $this->data['Actual Customer']='Yes';
       }else{
	 $this->data['Actual Customer']='No';
	 $this->data['Customer Type By Activity']='Prospect';
	 
       }
       
       if($this->data['Customer Orders']==1){
	 $sql="select avg((`Customer Order Interval`)+($sigma_factor*`Customer Order Interval STD`)) as a from `Customer Dimension`";
	 
	 $result2=mysql_query($sql);
	 if($row2=mysql_fetch_array($result2, MYSQL_ASSOC)){
	   $average_max_interval=$row2['a'];
	   if(is_numeric($average_max_interval)){
	     if(   (strtotime('now')-strtotime($this->data['Customer Last Order Date']))/(3600*24)  <  $average_max_interval){
	       $this->data['Active Customer']='Maybe';
	       $this->data['Customer Type by Activity']='New';
	       
	     }else{
	       $this->data['Active Customer']='No';
	       $this->data['Customer Type by Activity']='Inactive';
	       
	     }
	   }else
	     $this->data['Active Customer']='Unknown';
	   $this->data['Customer Type by Activity']='Unknown';
	   
	   
	 }	
	 
       }
       
       if($this->data['Customer Orders']>1){
	 $sql="select `Order Date` as date from `Order Dimension` where `Order Customer Key`=".$this->id." order by `Order Date`";
	 $last_order=false;
	 $intervals=array();
	 $result2=mysql_query($sql);
	 while($row2=mysql_fetch_array($result2, MYSQL_ASSOC)   ){
	   $this_date=date('U',strtotime($row2['date']));
	   if($last_order){
	     $intervals[]=($this_date-$last_date)/3600/24;
	   }
	   
	   $last_date=$this_date;
	   $last_order=true;
	   
	 }
	 //	 print $sql;
	 //print_r($intervals);
	 
	 
	 $this->data['Customer Order Interval']=average($intervals);
	 $this->data['Customer Order Interval STD']=deviation($intervals);
	 

	 

       }
       
      
       
       $sql=sprintf("update `Customer Dimension` set `Customer Net Balance`=%.2f,`Customer Orders`=%d,`Customer Orders Cancelled`=%d,`Customer Orders Invoiced`=%d,`Customer First Order Date`=%s,`Customer Last Order Date`=%s,`Customer Order Interval`=%s,`Customer Order Interval STD`=%s,`Customer Net Refunds`=%.2f,`Customer Net Payments`=%.2f,`Customer Outstanding Net Balance`=%.2f,`Customer Tax Balance`=%.2f,`Customer Tax Refunds`=%.2f,`Customer Tax Payments`=%.2f,`Customer Outstanding Tax Balance`=%.2f,`Customer Profit`=%.2f where `Customer Key`=%d",
		    $this->data['Customer Net Balance']
		    ,$this->data['Customer Orders']
		    ,$this->data['Customer Orders Cancelled']
		    ,$this->data['Customer Orders Invoiced']
		    ,prepare_mysql($this->data['Customer First Order Date'])
		    ,prepare_mysql($this->data['Customer Last Order Date'])
		    ,prepare_mysql($this->data['Customer Order Interval'])
		    ,prepare_mysql($this->data['Customer Order Interval STD'])
		    ,$this->data['Customer Net Refunds']
		    ,$this->data['Customer Net Payments']
		    ,$this->data['Customer Outstanding Net Balance']
		    
		    ,$this->data['Customer Tax Balance']
		    ,$this->data['Customer Tax Refunds']
		    ,$this->data['Customer Tax Payments']
		    ,$this->data['Customer Outstanding Tax Balance']
		    
		    ,$this->data['Customer Profit']



		    ,$this->id
		    );

       if(!mysql_query($sql))
	 exit("$sql error");
     }


      //      $sql=sprintf("select `Customer Orders` from `Customer Dimension` order by `Customer Order`");



 }





 function updatex($values,$args=''){
    $res=array();
    foreach($values as $data){
      
      $key=$data['key'];
      $value=$data['value'];
      $res[$key]=array('ok'=>false,'msg'=>'');
      
      switch($key){

      case('tax_number_valid'):
	if($value)
	  $this->data['tax_number_valid']=1;
	else
	  $this->data['tax_number_valid']=0;
	
	break;

      case('tax_number'):
	$this->data['tax_number']=$value;
	if($value=='')
	  $this->update(array(array('key'=>'tax_number_valid','value'=>0)),'save');
	break;
      case('main_email'):
	$main_email=new email($value);
	if(!$main_email->id){
	  $res[$key]['msg']=_('Email not found');
	  $res[$key]['ok']=false;
	  continue;
	}
	$this->old['main_email']=$this->data['main']['email'];
	$this->data['main_email']=$value;
	$this->data['main']['email']=$main_email->data['email'];
	$res[$key]['ok']=true;


      } 
      if(preg_match('/save/',$args)){
	$this->save($key);
      }

    }
    return $res;
 }


 function save($key,$history_data=false){
   switch($key){

   case('tax_number'):
   case('tax_number_valid'):
   case('main_email'):
     $sql=sprintf('update customer set %s=%s where id=%d',$key,prepare_mysql($this->data[$key]),$this->id);
     //print "$sql\n";
     mysql_query($sql);
     
	if(is_array($history_data)){
	  $this->save_history($key,$this->old[$key],$this->data['main']['email'],$history_data);
	}
       
	
	break;
    }

 }

 function save_history($key,$old,$new,$data){
     if(isset($data['user_id']))
       $user=$data['user_id'];
     else
       $user=0;
     
     if(isset($data['date']))
       $date=prepare_mysql($data['date']);
     else
       $date='NOW()';

   switch($key){
   case('new_note'):
   case('add_note'):
     if(preg_match('/^\s*$/',$data['note'])){
       $this->msg=_('Invalid value');
       return false;
     
     }

     $tipo='NOTE';
     $note=_trim($data['note']);
     $details='';


     $sql=sprintf("insert into `History Dimension` (`History Date`,`Subject`,`Subject Key`,`Action`,`Direct Object`,`Preposition`,`Indirect Object`,`Indirect Object Key`,`History Abstract`,`History Details`,`Author Name`,`Author Key`) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
		  ,$date
		  ,prepare_mysql('User')
		  ,prepare_mysql($data['user_id'])
		  ,prepare_mysql('wrote')
		  ,prepare_mysql('Note')
		  ,prepare_mysql('about')
		  ,prepare_mysql('Customer')
		  ,prepare_mysql($this->id)
		  ,prepare_mysql($note)
		  ,prepare_mysql($details)
		  ,prepare_mysql($data['author'])
		  ,prepare_mysql($data['author_key'])
		  );
     //   print $sql;
     mysql_query($sql);
     $this->msg=_('Note Added');
     return true;
     break;

       case('new_note'):
   case('order'):
     $tipo='ORDER';
     $order=new order('order',$data['order_id']);
     $action=$data['action'];

     if(isset($data['display']))
       $display=$data['display'];
     else
       $display='normal';

     switch($action){
     case('creation'):
       $_action='DATE_CR';
       $note=_('Customer place order').' <a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a>';
       break;
     case('processed'):
       $_action='DATE_PR';
       $note=_('Order').' <a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a> '._('processed');
       
       break;
     case('invoiced'):
       $_action='DATE_IN';
       $note=_('Order').' <a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a> '._('for').' '.money((float)$order->get('total'));
       break;
     case('cancelled'):
       $_action='DATE_CA';
       $note=_('Order').' <a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a> '._('has been cancelled');
       break;
   case('sample'):
       $_action='DATE_DI';
       $note=_('Sample send').' (<a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a>)';
       break;
   case('donation'):
       $_action='DATE_DI';
       $note=_('Donation').' (<a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a>)';
       break;
   case('replacement'):
       $_action='DATE_DI';
       $parent_order='';
       if($order->get('parent_id')){
	 $parent=new Order($order->get('parent_id'));
	 if($parent->id)
	   $parent_order=' '._('for order').' (<a href="order.php?id='.$parent->id.'">'.$parent->get('public_id').'</a>';
       }
       $note=_('Replacement').' (<a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a>)'.$parent_order;
       break;
   case('shortages'):
       $_action='DATE_DI';
       $parent_order='';
       if($order->get('parent_id')){
	 $parent=new Order($order->get('parent_id'));
	 if($parent->id)
	   $parent_order=' '._('for order').' (<a href="order.php?id='.$parent->id.'">'.$parent->get('public_id').'</a>';
       }
       $note=_('shortages').' (<a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a>)'.$parent_order;
       break;
   case('followup'):
       $_action='DATE_DI';
       $parent_order='';
       if($order->get('parent_id')){
	 $parent=new Order($order->get('parent_id'));
	 if($parent->id)
	   $parent_order=' '._('for order').' (<a href="order.php?id='.$parent->id.'">'.$parent->get('public_id').'</a>';
       }
       $note=_('Follow up').' (<a href="order.php?id='.$order->id.'">'.$order->get('public_id').'</a>)'.$parent_order;
       break;
     default:
       $this->msg=_('Unknown action');
       return false;
     }





     $sql=sprintf("insert into history (date,sujeto,sujeto_id,objeto,objeto_id,tipo,staff_id,old_value,new_value,note,display) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
		  ,$date
		  ,prepare_mysql('CUST')
		  ,prepare_mysql($this->id)
		  ,prepare_mysql($tipo)
		  ,$order->id
		  ,prepare_mysql($_action)
		  ,prepare_mysql($user)
		  ,prepare_mysql($old)	 
		  ,prepare_mysql($new)	 
		  ,prepare_mysql($note)
		  ,prepare_mysql($display)
		  );
     // print "$sql\n";
     mysql_query($sql);
     $this->msg=_('Note Added');
     return true;

   }
 }


 function get($key,$arg1=false){

   

   if(array_key_exists($key,$this->data)){
     return $this->data[$key]; 
   }
   
   if(preg_match('/^contact /i',$key)){
     if(!$this->contact_data)
       $this->load('contact data');
     if(isset($this->contact_data[$key]))
       return $this->contact_data[$key]; 
   }
   



   if(preg_match('/^ship to /i',$key)){
     if(!$arg1)
       $ship_to_key=$this->data['Customer Main Ship To Key'];
     else
       $ship_to_key=$arg1;
      if(!$this->ship_to[$ship_to_key])
	$this->load('ship to',$ship_to_key);
      if(isset($this->ship_to[$ship_to_key])    and  array_key_exists($key,$this->ship_to[$ship_to_key]) )
	return $this->ship_to[$ship_to_key][$key]; 
   }
   


   switch($key){
   case("ID"):
   case("Formated ID"):
     return $this->get_formated_id();
   case('Net Balance'):
     return money($this->data['Customer Net Balance']);
     break;
   case('Total Net Per Order'):
     if($this->data['Customer Orders Invoiced']>0)
       return money($this->data['Customer Net Balance']/$this->data['Customer Orders Invoiced']);
     else
       return _('ND');
     break;
   case('Order Interval'):
     $order_interval=$this->get('Customer Order Interval');
     
     if($order_interval>10){
       $order_interval=round($order_interval/7);
       if( $order_interval==1)
	 $order_interval=_('week');
       else
	 $order_interval=$order_interval.' '._('weeks');
       
     }else if($order_interval=='')
  $order_interval='';
     else
       $order_interval=round($order_interval).' '._('days');
     return $order_interval;
     break;
   case('order within'):
     
     if(!$args)
       $args='1 MONTH';
     //get customer last invoice;
     $sql="select count(*)as num  from `Order Dimension` where `Order Type`='Order' and `Order Current Dispatch State`!='Cancelled' and `Order Customer Key`=".$this->id." and DATE_SUB(CURDATE(),INTERVAL $args) <=`Order Date`  ";
     // print $sql;
     
     $result=mysql_query($sql);
     if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       
       if($row['num']>0)
	 return true;
     }
     return false;
     break;
   case('xhtml ship to'):
   
     
     if(!$arg1)
       $ship_to_key=$this->data['Customer Main Ship To Key'];
     else
       $ship_to_key=$arg1;
     
     if(!$ship_to_key){
       print_r($this->data);
       print "\n*** Warning no ship to key un customer.php\n";
       sdsd();
       exit;
       return false;
       
     }

     if(!isset($this->ship_to[$ship_to_key]['ship to key']))
	 $this->load('ship to',$ship_to_key);
      

     //print_r($this->ship_to);

      if(isset($this->ship_to[$ship_to_key]['Ship To Key'])){
	$contact=$this->ship_to[$ship_to_key]['Ship To Contact Name'];
	$company=$this->ship_to[$ship_to_key]['Ship To Company Name'];
	$address=$this->ship_to[$ship_to_key]['Ship To XHTML Address'];
	$tel=$this->ship_to[$ship_to_key]['Ship To Telephone'];
	$ship_to='';
	if($contact!='')
	  $ship_to.='<b>'.$contact.'</b>';
	if($company!='')
	  $ship_to.='<br/>'.$company;
	if($address!='')
	  $ship_to.='<br/>'.$address;
	if($tel!='')
	  $ship_to.='<br/>'.$tel;
	return $ship_to;
      }
    
      return false;
     break;

     //   case('customer main address key')

     
 //   case('location'):
//      if(!isset($this->data['location']))
//        $this->load('location');
//      return $this->data['location']['country_code'].$this->data['location']['town'];
//      break;
//    case('super_total'):
//           return $this->data['total_nd']+$this->data['total'];
// 	  break;
//    case('orders'):
//      return $this->data['num_invoices']+$this->data['num_invoices_nd'];
//      break;
//    default:
//      if(isset($this->data[$key]))
//        return $this->data[$key];
//      else
//        return '';
   }
   
   $_key=ucwords($key);
   if(isset($this->data[$_key]))
     return $this->data[$_key];

   //print "Error ->$key not found in get,* from Customer\n";
   //exit;
   return false;

 }


  function new_id(){
    
    $sql="select max(`Customer ID`)  as customer_id from `Customer Dimension`";
    $result=mysql_query($sql);
    if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      
      
      if(!preg_match('/\d*/',_trim($row['customer_id']),$match))
	$match[0]=1;
      $right_side=$match[0];
      // print "$right_side\n";
      $number=(double) $right_side;
      $number++;
      $id=$number;
    }else{
      $id=1;
    }  
    // print "$id\n";
    return $id;
  }





 function update_address_data($address_key=false){
   
   if(!$address_key)
     return;
   $address=new Address($address_key);
   if(!$address->id)
     return;

   if($address->id!=$this->data['Customer Main Address Key'] and $this->data['Customer Billing Address Link']=='Contact'){
     $this->data['Customer Billing Address Key']=$address->id;
      $sql=sprintf("update `Customer Dimension` set `Customer Billing Address Key`=%d   where `Customer Key`=%d"
		   
		   ,$this->data['Customer Billing Address Key']
		   

		   
		   ,$this->id
		   );

      
      mysql_query($sql);
      
      
   }


   if(
      $address->id!=$this->data['Customer Main Address Key']
      or $address->display('xhtml')!=$this->data['Customer Main XHTML Address']
      or $address->display('plain')!=$this->data['Customer Main Plain Address']
      or $address->display('location')!=$this->data['Customer Main Location']      ){
     $old_value=$this->data['Customer Main XHTML Address'];
     $this->data['Customer Main Address Key']=$address->id;
     $this->data['Customer Main XHTML Address']=$address->display('xhtml');
     $this->data['Customer Main Address Country Code']=$address->data['Address Country Code'];
     $this->data['Customer Main Address Country 2 Alpha Code']=$address->data['Address Country 2 Alpha Code'];
     


     $this->data['Customer Main Address Country']=$address->data['Address Country Name'];
     $this->data['Customer Main Location']=$address->display('location');
     $this->data['Customer Main Address Town']=$address->data['Address Town'];
     $this->data['Customer Main Address Postal Code']=$address->data['Address Postal Code'];
     $this->data['Customer Main Address Country First Division']=$address->data['Address Country First Division'];
     

     $sql=sprintf("update `Customer Dimension` set `Customer Main Address Key`=%d,`Customer Main Plain Address`=%s,`Customer Main XHTML Address`=%s,`Customer Main Address Country`=%s,`Customer Main Location`=%s,`Customer Main Address Country Code`=%s,`Customer Main Address Country 2 Alpha Code`=%s,`Customer Main Address Town`=%s,`Customer Main Address Postal Code`=%s ,`Customer Main Address Country First Division`=%s    where `Customer Key`=%d"
		  
		  ,$this->data['Customer Main Address Key']
		  ,prepare_mysql($this->data['Customer Main Plain Address'],false)
		  ,prepare_mysql($this->data['Customer Main XHTML Address'])
		  ,prepare_mysql($this->data['Customer Main Address Country'])
		  ,prepare_mysql($this->data['Customer Main Location'])
		  ,prepare_mysql($this->data['Customer Main Address Country Code'])
		  ,prepare_mysql($this->data['Customer Main Address Country 2 Alpha Code'])
		  ,prepare_mysql($this->data['Customer Main Address Town'])
		  ,prepare_mysql($this->data['Customer Main Address Postal Code'])
		  ,prepare_mysql($this->data['Customer Main Address Country First Division'])

		  
		  ,$this->id
		  );


     if(!mysql_query($sql))
       exit("\n\nerror $sql\n");




     
     
  
   



       
     if($old_value!=$this->data['Customer Main XHTML Address']){
     
     $note=_('Address Changed');
     if($old_value!=''){
       $details=_('Customer address changed from')." \"".$old_value."\" "._('to')." \"".$this->data['Customer Main XHTML Address']."\"";
     }else{
       $details=_('Customer address set to')." \"".$this->data['Customer Main XHTML Address']."\"";
     }
       
       $history_data=array(
			   'indirect_object'=>'Address'
			   ,'details'=>$details
			   ,'note'=>$note
			   );
       $this->add_history($history_data);
       
     }




   }

 }


  /*function:get_formated_id_link
     Returns formated id_link
    */
   function get_formated_id_link(){
   
     
    

     return sprintf('<a href="customer.php?id=%d">%s</a>',$this->id, $this->get_formated_id());

   }


   /*function:get_formated_id
     Returns formated id
    */
   function get_formated_id(){
     global $myconf;
     
     $sql="select count(*) as num from `Customer Dimension`";
     $res=mysql_query($sql);
     $min_number_zeros=4;
     if($row=mysql_fetch_array($res)){
       if(strlen($row['num'])-1>$min_number_zeros)
	 $min_number_zeros=strlen($row['num'])-01;
     }
     if(!is_numeric($min_number_zeros))
       $min_number_zeros=4;

     return sprintf("%s%0".$min_number_zeros."d",$myconf['customer_id_prefix'], $this->data['Customer ID']);

   }

/* Method: add_tel
  Add/Update an telecom to the Customer
*/
   function add_tel($data,$args='principal'){
     
     $principal=false;
     if(preg_match('/not? principal/',$args) ){
       $principal=false;
     }elseif( preg_match('/principal/',$args)){
       $principal=true;
     }

   

   
      if(is_numeric($data)){
	$tmp=$data;
	unset($data);
	$data['Telecom Key']=$tmp;
      }
      
      if(isset($data['Telecom Key'])){
	$telecom=new Telecom('id',$data['Telecom Key']);
      }

      if(!isset($data['Telecom Type'])  or $data['Telecom Type']!='Contact Fax' )
	$data['Telecom Type']='Contact Telephone';



      if($data['Telecom Type']=='Contact Telephone'){
	$field='Customer Main Telephone';
	$field_key='Customer Main Telephone Key';
	$field_plain='Customer Main Plain Telephone';
	$old_principal_key=$this->data['Customer Main Telephone Key'];
	$old_value=$this->data['Customer Main Telephone']." (Id:".$this->data['Customer Main Telephone Key'].")";
      }else{
	$field='Customer Main FAX';
	$field_key='Customer Main FAX Key';
	$field_plain='Customer Main Plain FAX';
	$old_principal_key=$this->data['Customer Main FAX Key'];
	$old_value=$this->data['Customer Main FAX']." (Id:".$this->data['Customer Main FAX Key'].")";
      }

	
      
      if($telecom->id){
	
	//	print "$principal $old_principal_key ".$telecom->id."  \n";

	
	if($principal and $old_principal_key!=$telecom->id){
	  $sql=sprintf("update `Telecom Bridge`  set `Is Main`='No' where `Subject Type`='Customer' and  `Subject Key`=%d  ",
		       $this->id
		       ,$telecom->id
		       );
	  mysql_query($sql);
	  
	  $sql=sprintf("update `Customer Dimension` set `%s`=%s , `%s`=%d  , `%s`=%s  where `Customer Key`=%d"
		       ,$field
		       ,prepare_mysql($telecom->display('html'))
		       ,$field_key
		       ,$telecom->id
		       ,$field_plain
		       ,prepare_mysql($telecom->display('plain'))
		       ,$this->id
		      );
	  mysql_query($sql);
	  $history_data=array(
			      'note'=>$field." "._('Changed')
			      ,'details'=>$field." "._('changed')." "
			      .$old_value." -> ".$telecom->display('html')
			      ." (Id:"
			      .$telecom->id
			      .")"
			      ,'action'=>'created'
			      );
	  if(!$this->new)
	    $this->add_history($history_data);
	 
	  
	}

	
	
	$sql=sprintf("insert into  `Telecom Bridge` (`Telecom Key`, `Subject Key`,`Subject Type`,`Telecom Type`,`Is Main`) values (%d,%d,'Customer',%s,%s)  ON DUPLICATE KEY UPDATE `Telecom Type`=%s ,`Is Main`=%s  "
		     ,$telecom->id
		     ,$this->id
		     ,prepare_mysql($data['Telecom Type'])
		     ,prepare_mysql($principal?'Yes':'No')
		     ,prepare_mysql($data['Telecom Type'])
		     ,prepare_mysql($principal?'Yes':'No')
		     );
	mysql_query($sql);
	


	


      }

      
      



    }


/* Method: remove_email
  Delete the email from Customer
  
  Delete telecom record  this record to the Customer


  Parameter:
  $args -     string  options
 */
 function remove_email($email_key=false){

   
    if(!$email_key){
     $email_key=$this->data['Customer Main Email Key'];
   }
   
   
   $email=new email($email_key);
   if(!$email->id){
     $this->error=true;
     $this->msg='Wrong email key when trying to remove it';
     $this->msg_updated='Wrong email key when trying to remove it';
   }

   $email->set_scope('Customer',$this->id);
   if( $email->associated_with_scope){
     
     $sql=sprintf("delete `Email Bridge`  where `Subject Type`='Customer' and  `Subject Key`=%d  and `Email Key`=%d",
		  $this->id
		  
		  ,$this->data['Customer Main Email Key']
		  );
     mysql_query($sql);
     
     if($email->id==$this->data['Customer Main Email Key']){
       $sql=sprintf("update `Customer Dimension` set `Customer Main XHTML Email`='', `Customer Main Plain Email`='' , `Customer Main Email Key`=''  where `Customer Key`=%d"
		    ,$this->id
		    );
       
       mysql_query($sql);
     }
   }
   

       

 }



function remove_company($company_key=false){

   
    if(!$company_key){
     $company_key=$this->data['Customer Main Company Key'];
   }
   
   
   $company=new company($company_key);
   if(!$company->id){
     $this->error=true;
     $this->msg='Wrong company key when trying to remove it';
     $this->msg_updated='Wrong company key when trying to remove it';
   }

   $company->set_scope('Customer',$this->id);
   if( $company->associated_with_scope){
     
     $sql=sprintf("delete `Company Bridge`  where `Subject Type`='Customer' and  `Subject Key`=%d  and `Company Key`=%d",
		  $this->id
		  
		  ,$this->data['Customer Main Company Key']
		  );
     mysql_query($sql);
     
     if($company->id==$this->data['Customer Main Company Key']){
       $sql=sprintf("update `Customer Dimension` set `Customer Company Name`='' , `Customer Company Key`=''  where `Customer Key`=%d"
		    ,$this->id
		    );
       
       mysql_query($sql);
       if($this->data['Customer Type']=='Company'){
         $sql=sprintf("update `Customer Dimension` set `Customer Name`='' , `Customer File As`=''  where `Customer Key`=%d"
		    ,$this->id
		    );
       
       mysql_query($sql);
       
       }
       
       
     }
   }
 }



function remove_contact($contact_key=false){

   
    if(!$contact_key){
     $contact_key=$this->data['Customer Main Contact Key'];
   }
   
   
   $contact=new contact($contact_key);
   if(!$contact->id){
     $this->error=true;
     $this->msg='Wrong contact key when trying to remove it';
     $this->msg_updated='Wrong contact key when trying to remove it';
   }

   $contact->set_scope('Customer',$this->id);
   if( $contact->associated_with_scope){
     
     $sql=sprintf("delete `Contact Bridge`  where `Subject Type`='Customer' and  `Subject Key`=%d  and `Contact Key`=%d",
		  $this->id
		  
		  ,$this->data['Customer Main Contact Key']
		  );
     mysql_query($sql);
     
     if($contact->id==$this->data['Customer Main Contact Key']){
       $sql=sprintf("update `Customer Dimension` set `Customer Main Contact Name`='' , `Customer Main Contact Key`=''  where `Customer Key`=%d"
		    ,$this->id
		    );
       
       mysql_query($sql);
       if($this->data['Customer Type']=='Person'){
         $sql=sprintf("update `Customer Dimension` set `Customer Name`='' , `Customer File As`=''  where `Customer Key`=%d"
		    ,$this->id
		    );
       
       mysql_query($sql);
       
       }
       
       
     }
   }
 }


 function get_main_email_key(){
    return $this->data['Customer Main Email Key'];
  }


 function get_last_order(){
   $order_key=0;
   $sql=sprintf("select `Order Key` from `Order Dimension` where `Order Customer Key`=%d order by `Order Date` desc  ",$this->id);
   // $sql=sprintf("select *  from `Order Dimension` limit 10");
   // print "$sql\n";
   $res=mysql_query($sql);

   if($row=mysql_fetch_array($res,MYSQL_ASSOC)){
     //   print_r($row);
       $order_key=$row['Order Key'];
       //print "****************$order_key\n";

       //  exit;
   }
  
   return $order_key;
 }

 function add_note($note){
   $note=_trim($note);
   if($note==''){
     $this->msg=_('Empty note');
     return;
   }
   $details='';
   if(strlen($note)>64){
     $words=preg_split('/\s/',$note);
     $len=0;
     $note='';
     $details='';
     foreach($words as $word){
       $len+=strlen($world);
       if($note=='')
	 $note=$world;
       else{
	 if($len<64)
	   $note.=' '.$world;
	 else
	   $details.=' '.$world;

       }
     }
     
     
     
   }

  $history_data=array(
		      'note'=>$note
		      ,'details'=>$details
		      ,'action'=>'created'
		      ,'direct_object'=>'Note'
		      ,'prepostion'=>'about'
		      ,'indirect_object'=>'Customer'
		      ,'indirect_object_key'=>$this->id
		      );
  $this->add_history($history_data);
  $this->updated=true;
  $this->new_value='';
 }






 function add_attach($file,$data){


   $data=array(
	       'file'=>$file
	       ,'Attachment Caption'=>$data['Caption']
	       ,'Attachment MIME Type'=>$data['Type']
	       ,'Attachment File Original Name'=>$data['Original Name']
	       );

  
   $attach=new Attachment('find',$data,'create');

   if($attach->new){


     $history_data=array(
			 'note'=>$attach->get_abstract()
			 ,'details'=>$attach->get_details()
			 ,'action'=>'associated'
			 ,'direct_object'=>'Attachment'
			 ,'prepostion'=>''
			 ,'indirect_object'=>'Customer'
			 ,'indirect_object_key'=>$this->id
			 );
     $this->add_history($history_data);
     $this->updated=true;
     $this->new_value='';
   }

 }


 function delivery_address_xhtml(){
   if($this->data['Customer Delivery Address Link']=='None'){
     $deliver_address=new Ship_To($this->data['Customer Main Ship To Key']);
     return $deliver_address->data['Ship To XHTML Address'];
   }

   if($this->data['Customer Delivery Address Link']=='Billing')
     $address=new Address($this->data['Customer Billing Address Key']);
   else
     $address=new Address($this->data['Customer Main Address Key']);

   return $address->display('xhtml');

 }


function set_current_ship_to($return='key'){
  if(preg_match('/object/i',$return))
    return $this->set_current_ship_to_get_object();
  else
    return $this->set_current_ship_to_get_key();
  
}


 function set_current_ship_to_get_key(){
   if($this->data['Customer Delivery Address Link']=='None'){
     return $this->data['Customer Main Ship To Key'];
   }

   if($this->data['Customer Delivery Address Link']=='Billing')
     $address=new Address($this->data['Customer Billing Address Key']);
   else
     $address=new Address($this->data['Customer Main Address Key']);
   
   
     $line=$address->display('3lines');
     
     $shipping_addresses['Address Line 1']=$line[1];
     $shipping_addresses['Address Line 2']=$line[2];
     $shipping_addresses['Address Line 3']=$line[3];
     $shipping_addresses['Address Town']=$address->data['Address Town'];
     $shipping_addresses['Address Postal Code']=$address->data['Address Postal Code'];
     $shipping_addresses['Address Country Name']=$address->data['Address Country Name'];
     $shipping_addresses['Address Country Primary Division']=$address->data['Address Country First Division'];
     $shipping_addresses['Address Country Secondary Division']=$address->data['Address Country Second Division'];
     $ship_to= new Ship_To('find create',$shipping_addresses);
     
     
     
     return $ship_to->id;
     

 }


 function set_current_ship_to_get_object(){
   $ship_to=new Ship_To($this->set_current_ship_to());
   return $ship_to;
     

 }



 }
?>