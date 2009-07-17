<?
/*
 File: Contact.php 

 This file contains the Contact Class

 About: 
 Autor: Raul Perusquia <rulovico@gmail.com>
 
 Copyright (c) 2009, Kaktus 
 
 Version 2.0
*/
include_once('DB_Table.php');

include_once('Telecom.php');
include_once('Email.php');
include_once('Address.php');
include_once('Name.php');

/* class: Contact
 Class to manage the *Contact Dimension* table
*/

class Contact extends DB_Table{
  
   /*
     Constructor: Contact
     
     Initializes the class, Search/Load or Create for the data set 
     
     Parameters:
     arg1 -    (optional) Could be the tag for the Search Options or the Contact Key for a simple object key search
     arg2 -    (optional) Data used to search or create the object

     Returns:
     void
     
     Example:
     (start example)
     // Load data from `Contact Dimension` table where  `Contact Key`=3
     $key=3;
     $contact = New Contact($key); 
     
     // Insert row to `Contact Dimension` table
     $data=array();
     $contact = New Contact('new',$data); 
     
     
     (end example)
     
   */
  function Contact($arg1=false,$arg2=false) {
    
    global $myconf;
    
    $this->table_name='Contact';
    $this->ignore_fields=array('Contact Key');


    

    if(preg_match('/create anonymous|create anonimous$/i',$arg1)){
      $this->create_anonymous();
      return;
    }
    if(preg_match('/^(new|create)$/i',$arg1)){
      $this->create($arg2);
      return;
    }if(preg_match('/find/i',$arg1)){
      $this->find($arg2,$arg1);
      return;
    }

    

    if(is_numeric($arg1) and !$arg2){
      $this->get_data('id',$arg1);
      return;
    }
    $this->get_data($arg1,$arg2);


  }
  /* Function: get_data
       Load the data from de Database
     
     Parameters:
         $key  -  string Search Field
         $id  -  mixed Search Argument
      Return: void
     */

  Protected  function get_data($key,$id){

    
    if($key=='id')
      $sql=sprintf("SELECT * FROM `Contact Dimension` C where `Contact Key`=%d",$id); 
    else
      return;

    $result=mysql_query($sql);
    if($this->data=mysql_fetch_array($result, MYSQL_ASSOC)   )
      $this->id=$this->data['Contact Key'];


  }


  /*
    Method: find
    Find Company with similar data
   
    Returns:
    Key of the Compnay found, if create is found in the options string  returns the new key
   */  
  function find($raw_data,$options){
       //    print $options."\n";
    // print_r($raw_data);

    
    if(isset($raw_data['editor'])){
      foreach($raw_data['editor'] as $key=>$value){

	if(array_key_exists($key,$this->editor))
	  $this->editor[$key]=$value;
		    
      }
    }

    $this->candidate=array();
    $this->found=false;
    $create='';
    $update='';
    if(preg_match('/create/i',$options)){
      $create='create';
    }
    if(preg_match('/update/i',$options)){
      $update='update';
    }

    $data=$this->base_data();
    $address_home_data=array('Contact Home Address Line 1'=>'','Contact Home Address Town'=>'','Contact Home Address Line 2'=>'','Contact Home Address Line 3'=>'','Contact Home Address Postal Code'=>'','Contact Home Address Country Name'=>'','Contact Home Address Country Primary Division'=>'','Contact Home Address Country Secondary Division'=>'');
    $address_work_data=array('Contact Work Address Line 1'=>'','Contact Work Address Town'=>'','Contact Work Address Line 2'=>'','Contact Work Address Line 3'=>'','Contact Work Address Postal Code'=>'','Contact Work Address Country Name'=>'','Contact Work Address Country Primary Division'=>'','Contact Work Address Country Secondary Division'=>'');
    
    if(preg_match('/from supplier/',$options)){
      foreach($raw_data as $key=>$val){
	$_key=preg_replace('/Supplier /i','Contact ',$key);
	$data[$_key]=$val;
      }
      $parent='supplier';
    }elseif(preg_match('/from customer|in customer/i',$options)){
      foreach($raw_data as $key=>$val){
	if(preg_match('/Customer Address/i',$key)){
	  $_key=preg_replace('/Customer Address/i','Contact Work Address',$key);
	}else
	  $_key=preg_replace('/Customer /','Contact ',$key);
	$data[$_key]=$val;

	if(preg_match('/Customer Address/i',$key))
	  $_key=preg_replace('/Customer Address/i','Contact Work Address',$key);
	if(array_key_exists($_key,$address_work_data))
	  $address_work_data[$_key]=$val;
	
	//	print " $key -> $_key = $val \n";

      }
      $parent='customer';

      //  print_r($data);
      //  print_r($address_work_data);
      //  exit;



    }elseif(preg_match('/from Company|in company/i',$options)){
      foreach($raw_data as $key=>$val){
/* 	if($create and preg_match('/address|email|telephone|fax|company name/i',$key)){ */
/* 	  continue; */
/* 	} */
	
//	print "XXXXXXXXXXXXXXXX THE KEY $key\n";

	if($key=='Company Name'){
	  $_key='Contact Company Name';
	}elseif($key=='Company Main Contact Name'){
	  $_key='Contact Name';
	}elseif(preg_match('/Company Address/i',$key)){
	  $_key=preg_replace('/Company Address/i','Contact Work Address',$key);
	}else
	  $_key=preg_replace('/Company /','Contact ',$key);
	

	if(array_key_exists($_key,$data))
	  $data[$_key]=$val;
	


	if(array_key_exists($_key,$address_work_data))
	  $address_work_data[$_key]=$val;
      }
      $parent='company';
      $parent_key=0;

      if(preg_match('/(from Company|in company) \d+/i',$options,$match)){
	$parent_key=preg_replace('/[^\d]/','',$match[0]);

      }


    }else{
      $parent='none';
      foreach($raw_data as $key=>$val){
	if(array_key_exists($key,$data))
	  $data[$key]=$val;
      }
      
      foreach($raw_data as $key=>$val){
	if(array_key_exists($key,$address_home_data)){
	  $key2=preg_replace('/Contact Home /','',$key);
	  $address_data['Home'][$key2]=$val;
	  $address_home_data[$key]=$val;
	}
      }
      foreach($raw_data as $key=>$val){
	if(array_key_exists($key,$address_work_data)){
	  $address_work_data[$key]=$val;
	   $key2=preg_replace('/Contact Work /','',$key);
	  $address_data['Work'][$key2]=$val;
	}
      }


    }



    $options.=' parent:'.$parent;

    $this->candidate=array();

    if($data['Contact Main Plain Email']!=''){
      $email=new Email("find in contact",$data['Contact Main Plain Email']);
      if($email->error){
	$data['Contact Main Plain Email']='';

      }	
      
      if( $email->found){
	$this->found=true;
	$this->found_key=$email->found_key;
      }
      foreach($email->candidate as $key=>$val){
	if(isset($this->candidate[$key]))
	  $this->candidate[$key]+=$val;
	else
	  $this->candidate[$key]=$val;
      }
	 

      
    }
    if(count($this->candidate)>0){
     // print "candidates ofter email:\n";
      //print_r($this->candidate);
    }

    //print "******************************************************\n$options\n";
   
    //print_r($address_work_data);
    //print_r($raw_data);
    //print_r($data);
   if(!array_empty( $address_work_data)){
     $address=new Address("find in contact",$address_work_data);

     $country_code=$address->raw_data['Address Country Code'];
     //print "AC\n";
     //print_r($address->candidate);
       foreach($address->candidate as $key=>$val){
	 if(isset($this->candidate[$key]))
	   $this->candidate[$key]+=$val;
	 else
	   $this->candidate[$key]=$val;
       }
       
       
   }else
     $country_code='UNK';
   


  if($data['Contact Main Telephone']!=''  ){
      $tel=new Telecom("find in contact country code $country_code",$data['Contact Main Telephone']);
      // print_r($tel);
      foreach($tel->candidate as $key=>$val){
	if(isset($this->candidate[$key]))
	  $this->candidate[$key]+=$val;
	else
	  $this->candidate[$key]=$val;
      }
    }

    // if(count($this->candidate)>0){
    //  print "candidates ofter telephone:\n";
    //  print_r($this->candidate);
    // }
    if($data['Contact Main FAX']!='' ){
      $tel=new Telecom("find in contact country code $country_code",$data['Contact Main FAX']);
      foreach($tel->candidate as $key=>$val){
	  if(isset($this->candidate[$key]))
	    $this->candidate[$key]+=$val;
	  else
	    $this->candidate[$key]=$val;
      }
      
    }
	
    //if(count($this->candidate)>0){
      //print "candidates after fax:\n";
      //print_r($this->candidate);
      //}

    if($data['Contact Main Mobile']!='' and !$this->found ){
      $tel=new Telecom("find in contact  country code $country_code",$data['Contact Main Mobile']);
      
      foreach($tel->candidate as $key=>$val){
	if(isset($this->candidate[$key]))
	  $this->candidate[$key]+=$val;
	else
	  $this->candidate[$key]=$val;
      }

    }

   if(count($this->candidate)>0){
     //    print "candidates after mobile:\n";
     // print_r($this->candidate);

    }




   if($data['Contact Old ID']!=''){
     $sql=sprintf("select `Contact Key` from `Contact Dimension` where `Contact Old ID` like '%%,%s,%%'",addslashes($data['Contact Old ID']));
     $res=mysql_query($sql);
     while($row=mysql_fetch_array($res)){
       $val=100;
       $key=$row['Contact Key'];
       	if(isset($this->candidate[$key]))
	  $this->candidate[$key]+=$val;
	else
	  $this->candidate[$key]=$val;
     }
     

   }





   /*  if(count($this->candidate)>0){ */
/*        print "candidates after mobile:\n"; */
/*       print_r($this->candidate); */

/*     } */
    //  exit;
    // find same name
    $name_data=$this->parse_name($data['Contact Name']);
    $name=$this->name($name_data);
    $sql=sprintf("select `Contact Key` from `Contact Dimension` where `Contact Name`=%s",prepare_mysql($name));
    $result=mysql_query($sql);
    
    while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      if(isset($this->candidate[$row['Contact Key']]))
	$this->candidate[$row['Contact Key']]+=100;
      else
	  $this->candidate[$row['Contact Key']]=100;
    }
    
    //if(count($this->candidate)>0){ 
      //  print "candidates after name:$name"."<-  ".count($this->candidate)." \n";
      // 	print_r($this->candidate);
      //    }
    // exit;
    // try to find contacts in the same company with missing parts
    



    if(isset($raw_data['Contact Old ID']) and $raw_data['Contact Old ID']!=''){
     $sql=sprintf("select `Contact Key` from `Contact Dimension` where `Contact Old ID` like '%%,%s,%%'",addslashes($raw_data['Contact Old ID']));
     $res=mysql_query($sql);
     while($row=mysql_fetch_array($res)){
       $val=100;
       $key=$row['Contact Key'];
       	if(isset($this->candidate[$key]))
	  $this->candidate[$key]+=$val;
	else
	  $this->candidate[$key]=$val;
     }
   }
    if(isset($raw_data['Contact Tax Number'])){
      $contacts_in_company=array();
      $raw_data['Contact Tax Number']=_trim($raw_data['Contact Tax Number']);
      if($raw_data['Contact Tax Number']!=''){
	$sql=sprintf("select `Company Key` from `Company Dimension` where `Company Tax Number`=%s",prepare_mysql($raw_data['Contact Tax Number']));
	$res=mysql_query($sql);
	while($row=mysql_fetch_array($res)){
	  $company=new company($row['Company Key']);
	  $company->load('Contact List');
	  foreach($this->contact_list as $key=>$val){
	    $contacts_in_company[$key]=$key;
	  }
	}
	
	foreach($contacts_in_company as $key){
	  $val=100;
	  $key=$row['Company Key'];
	  if(isset($this->candidate[$key]))
	    $this->candidate[$key]+=$val;
	  else
	    $this->candidate[$key]=$val;
	}
	

      }
    }




 if(isset($raw_data['Contact Company Name'])){
      $contacts_in_company=array();
      $raw_data['Contact Company Name']=_trim($raw_data['Contact Company Name']);
      if($raw_data['Contact Company Name']!=''){
	$sql=sprintf("select `Company Key` from `Company Dimension` where `Company Name`=%s",prepare_mysql($raw_data['Contact Company Name']));
	$res=mysql_query($sql);
	while($row=mysql_fetch_array($res)){
	  $company=new company($row['Company Key']);
	  $company->load('Contact List');
	  foreach($this->contact_list as $key=>$val){
	    $contacts_in_company[$key]=$key;
	  }
	}
	
	foreach($contacts_in_company as $key){
	  $val=40;
	  $key=$row['Company Key'];
	  if(isset($this->candidate[$key]))
	    $this->candidate[$key]+=$val;
	  else
	    $this->candidate[$key]=$val;
	}
	

      }
    }







   
 if(isset($raw_data['Contact Registration Number'])){
      $contacts_in_company=array();
      $raw_data['Contact Registration Number']=_trim($raw_data['Contact Registration Number']);
      if($raw_data['Contact Registration Number']!=''){
	$sql=sprintf("select `Company Key` from `Company Dimension` where `Company Registration Number`=%s",prepare_mysql($raw_data['Contact Registration Number']));
	$res=mysql_query($sql);
	while($row=mysql_fetch_array($res)){
	  $company=new company($row['Company Key']);
	  $company->load('Contact List');
	  foreach($this->contact_list as $key=>$val){
	    $contacts_in_company[$key]=$key;
	  }
	}
	
	foreach($contacts_in_company as $key){
	  $val=100;
	  $key=$row['Company Key'];
	  if(isset($this->candidate[$key]))
	    $this->candidate[$key]+=$val;
	  else
	    $this->candidate[$key]=$val;
	}
	

      }
    }














    if($parent=='company' and $parent_key){
      // look for down grades;
      
      $sql=sprintf("select  `Contact Dimension`.`Contact Key`,`Contact Salutation`,`Contact First Name`,`Contact Surname`,`Contact Suffix` from `Contact Dimension` left join `Contact Bridge` on (`Contact Dimension`.`Contact Key`=`Contact Bridge`.`Contact Key`) where `Subject Key`=%d and `Subject Type`='Company'",$parent_key);
      //  print $sql;
      //print_r($name_data);
      $result=mysql_query($sql);
      $_candidate=array();
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	$_contact_key=$row['Contact Key'];
	unset($row['Contact Key']);
	$_candidate[$_contact_key]=0;
	foreach($row as $key=>$val){
	  if($name_data[$key]!='' and $row[$key]!=''){
	    if($name_data[$key]==$row[$key])
	      $_candidate[$_contact_key]+=1;
	    else
	      $_candidate[$_contact_key]-=2;
	  }
	  
	}
      }
      //asort($_candidate);
      //      print_r($_candidate);ex
      foreach($_candidate as $key=>$val){
	if($val>0){
	  if(isset($this->candidate[$key]))
	    $this->candidate[$key]+=200*$val;
	  else
	    $this->candidate[$key]=200*$val;
	}
      }
      

    }
    
    

    //print_r($this->candidate);
    arsort($this->candidate);

   /*  if((!$create and !$update) and $this->found){ */
/*        print "Candidates from #######################\n"; */
/*       print "direct found!!!!!!\n"; */
      
/*       $cont=new Contact($found_key); */
/* 	print_r($cont->data); */

/* 	print "Candidates from ----------------------|\n"; */
/*     } */

/*     if((!$create and !$update) and count($this->candidate)!=0 ){ */

/*       print "Candidates from #######################\n"; */
/*       print_r($raw_data); */
/*       foreach($this->candidate as $key => $value){ */
/* 	print "Score: $value\n"; */
/* 	$cont=new Contact($key); */
/* 	print_r($cont->data); */
/*       } */
/*       print "Candidates from ----------------------|\n"; */
/*     } */


    foreach($this->candidate as $key => $value){

      if($value>=200){
	//	print "$value $key ################x#######\n";
	$this->found=true;
	$this->found_key=$key;
	break;
      }
      
      
    }
    if(!$this->found){
      $tmp=$data;
      unset($tmp['Contact Name']);
      if(array_empty($tmp)){
	foreach($this->candidate as $key => $value){
	  if($value>=100){
	$this->found=true;
	$this->found_key=$key;
	break;
	  }
	  
	}
      }
    }


    //print "-Contact Candidates $options\n";
    //print_r($this->candidate);

    if($this->found){
      $this->get_data('id',$this->found_key);
      
      //print "Contact found  ".$this->found_key." --->$create-----\n";
      //print_r($this->card());
    }
     
    if($create){
      if($this->found){
	
	
/* 	$data['Home Address']=$address_home_data; */
/* 	$data['Work Address']=$address_work_data; */
//	print "raw data:\n";
//	print_r($raw_data);
	
	$this->update($raw_data,$options);
	if(isset($address_data['Home']))
	  $this->update_address($address_data['Home'],'Home');
	if(isset($address_data['Work']))
	$this->update_address($address_data['Work'],'Work');


      }else{
	//	exit("o no duplicate!!\n");
	//print "creating contact!!!!\n";
	//print_r($data);
	//print "^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n";
	$this->create($data,$options,$address_home_data,$address_work_data);
      }

    }
      


  }


 /* Method: check_others
  Look for similar contacts in the Database
  
  Try to match a contact with the data provided if not found any candidate return 0

  Parameter:
  $data  -     array        Data to be compared with the contacts in the database
  
  Return: 
  integer  - $contact_key   integer     Contact Key of the most probable match or 0 if no match found
 */
  function check_others($data){
    
      $weight=array(
		   'Same Other ID'=>100
		   ,'Same Email'=>100
		   ,'Similar Email'=>20
		   );

      
      if($data['Contact Email']!=''){
	$has_email=true;
	$sql=sprintf("select `Email Key` from `Email Dimension` where `Email`=%s",prepare_mysql($data['Contact Email']));
	$result=mysql_query($sql);
	if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	  $email_key=$row['Email Key'];
	  $sql=sprintf("select `Subject Key` from `Email Bridge` where `Email Key`=%s and `Subject Type`='Contact'",prepare_mysql($email_key));
	  $result2=mysql_query($sql);
	  if($row2=mysql_fetch_array($result2, MYSQL_ASSOC)){
	    // Email found assuming this is th contact
	    $contact_key=$row2['Subject Key'];
	    return $contact_key;
	  }
	}
      }else
	$has_email=false;

     $telephone=Telephone::display(Telephone::parse_telecom(array('Telecom Original Number'=>$data['Telephone']),$data['Country Key']));
     $contact_name= $this->parse_name($data['Name']);
    // Email not found check if we have a mantch in other id
     if($data['Customer Other ID']!=''){
       $no_other_id=false;
	$sql=sprintf("select `Contact Key`,`Contact Name`,`Contact Main Telephone` from `Customer Dimension` CD left join `Contact Bridge` CB on (CB.`Subject Key`=CD.`Customer Key`)  where `Subject Type`='Customer' and `Customer Other ID`=%s",prepare_mysql($data['Customer Other ID']));
	$result=mysql_query($sql);
	$num_rows = mysql_num_rows($result);
	if($num_rows==1){
	  $row=mysql_fetch_array($result, MYSQL_ASSOC);
	  $contact_key=$row2['Contact Key'];
	  return $contact_key;
	}elseif($num_rows>1){
	  // Get the candidates
	  
	  while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	    $this->candidate[$row['Contact Key']]['field']=array('Contact Other ID');
	    $this->candidate[$row['Contact Key']]['points']=$weight['Same Other ID'];
	    // from this candoateed of one has the same name we wouls assume that this is the one
	    if($contact_name!='' and $contact_name==$row['Contact Name'])
	      return $row2['Contact Key'];
	    if($telephone!='' and $telephone==$row['Contact Main Telephone'])
	      return $row2['Contact Key'];

	    
	  }
	  



	}
     }else
       $no_other_id=true;
    



     //If contact has the same name ond same address
     //$addres_finger_print=preg_replace('/[^\d]/','',$data['Full Address']).$data['Address Town'].$data['Postal Code'];


     //if thas the same name,telephone and address get it
    




     if($has_email){
     //Get similar candidates from email
       
       $sql=sprintf("select levenshtein(UPPER(%s),UPPER(`Email`)) as dist1,levenshtein(UPPER(SOUNDEX(%s)),UPPER(SOUNDEX(`Email`))) as dist2, `Subject Key`  from `Email Dimension` left join `Email Bridge` on (`Email Bridge`.`Email Key`=`Email Dimension`.`Email Key`)  where dist1<=2 and  `Subject Type`='Contact'   order by dist1,dist2 limit 20"
		    ,prepare_mysql($data['Contact Email'])
		    ,prepare_mysql($data['Contact Email'])
		    );
       $result=mysql_query($sql);
       while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	  $this->candidate[$row['Subject Key']]['field'][]='Contact Other ID';
	  $dist=0.5*$row['dist1']+$row['dist2'];
	  if($dist==0)
	    $this->candidate[$row['Subject Key']]['points']+=$weight['Same Other ID'];
	  else
	    $this->candidate[$row['Subject Key']]['points']=$weight['Similar Email']/$dist;
       
       }
     }
 

     //Get similar candidates from emailby name
     if($data['Contact Name']!=''){
     $sql=sprintf("select levenshtein(UPPER(%s),UPPER(`Contact Name`)) as dist1,levenshtein(UPPER(SOUNDEX(%s)),UPPER(SOUNDEX(`Contact Name`))) as dist2, `Contact Key`  from `Contact Dimension`   where dist1<=3 and  `Subject Type`='Contact'   order by dist1,dist2 limit 20"
		  ,prepare_mysql($data['Contact Name'])
		  ,prepare_mysql($data['Contact Name'])
		  );
     $result=mysql_query($sql);
     while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       $this->candidate[$row['Subject Key']]['field'][]='Contact Name';
       $dist=0.5*$row['dist1']+$row['dist2'];
       if($dist==0)
	 $this->candidate[$row['Subject Key']]['points']+=$weight['Same Contact Name'];
       else
	 $this->candidate[$row['Subject Key']]['points']=$weight['Similar Contact Name']/$dist;
       
     }
     }
     // Address finger print
     

  }



/* Method: create
 Create a new Contact record
 
 Parameter:
 $data -     array   Contact data 
 $optuioms - 
 Return: 
 mixed a object property
 
 Example:
 (example start)
 
 (example end)
 
*/
private function create ($data,$options='',$address_home_data=false,$address_work_data=false){
   
  
  //print $options;
  //print_r($data);

  
  global $myconf;
  if(is_string($data))
     $data['Contact Name']=$data;
  
  $this->data=$this->base_data();
  foreach($data as $key=>$value){
     if(array_key_exists($key,$this->data))
       $this->data[$key]=_trim($value);
  }
  

  if(!preg_match('/components ok|components confirmed/i',$options)){
    $parsed_data=$this->parse_name($this->data['Contact Name']);
    foreach($parsed_data as $key=>$val){
      if(array_key_exists($key,$this->data))
	 $this->data[$key]=$val;
    }
  }   


  if($this->data['Contact Old ID']){
    $this->data['Contact Old ID']=",".$this->data['Contact Old ID'].",";
  }
  
  $prepared_data=$this->prepare_name_data($this->data);
  foreach($prepared_data as $key=>$val){
       if(array_key_exists($key,$this->data))
	 $this->data[$key]=$val;
     }
   $this->data['Contact Name']=$this->display('name');
   if(!preg_match('/gender confirmed|gender ok/i',$options))
     $this->data['Contact Gender']=$this->gender($this->data);
   if(!preg_match('/grettings confirmed|grettings ok/i',$options)){
     $this->data['Contact Informal Greeting']=$this->display('informal gretting');
     $this->data['Contact Formal Greeting']=$this->display('formal gretting');
   }

   

   
    if($this->data['Contact Name']==''){
      $this->data['Contact Name']=$myconf['unknown_contact'];
      $this->data['Contact Informal Greeting']=$myconf['unknown_informal_greting'];
      $this->data['Contact Formal Greeting']=$myconf['unknown_formal_greting'];
    }
    


    $this->data['Contact File As']=$this->display('file_as');
    $this->data['Contact ID']=$this->get_id();


    if($this->data['Contact Name']==$myconf['unknown_contact']){
      $this->data['Contact Fuzzy']='Yes';
    }else
      $this->data['Contact Fuzzy']='No';
   

    $keys='(';$values='values(';
    foreach($this->data as $key=>$value){
      // Just insert name fields, company,email,tel,ax,address should be inserted later
      if(preg_match('/fuzzy| id| Salutation|Contact Name|file as|First Name|Surname|Suffix|Gender|Greeting|Profession|Title| plain/i',$key)){

	$keys.="`$key`,";
	if(preg_match('/suffix|plain|old id/i',$key))
	  $print_null=false;
	else
	  $print_null=true;
	$values.=prepare_mysql($value,$print_null).",";
      }
    }
    $keys=preg_replace('/,$/',')',$keys);
    $values=preg_replace('/,$/',')',$values);

    $sql=sprintf("insert into `Contact Dimension` %s %s",$keys,$values);
    //     print "creating contact\n $sql\n";
    // exit;
    if(mysql_query($sql)){
      $this->id= mysql_insert_id();
      $this->new=true;
      $this->get_data('id',$this->id);

      
      if($this->data['Contact Fuzzy']=='No'){
	$history_data=array(
			    'note'=>_('Contact Created')
			    ,'details'=>_trim(_('Contact')." \"".$this->display('name')."\"  "._('created'))
			    ,'action'=>'created'
			    );
	$this->add_history($history_data);
      }
      

      if(preg_match('/parent\:none|parent\:customer/',$options)){


	// Has no parent add emails,tels ect to the contact
	if($this->data['Contact Main Plain Email']!=''){
	  
	  $email_data['Email']=$data['Contact Main Plain Email'];
	  $email_data['Email Contact Name']=$this->display('name');
	  $email_data['editor']=$this->editor;
	  $email=new Email("find in contact ".$this->id." create",$email_data);
	  if(!$email->error){
	    //Collect data about email found
	    //print $email->msg."\n";
	    // exit("find_companycontact: email found\n");
	  
	    
	  $this->add_email(array(
				 'Email Key'=>$email->id
				 ,'Email Type'=>'Personal'
				 ),'principal no_history');
	  }
	  
	}

	if(!array_empty($address_home_data)){
	  $address_home_data['editor']=$this->editor;
	  $home_address=new Address("find in contact ".$this->id." create",$address_home_data);
	  if($home_address->error){
	    print $home_address->msg."\n";
	    exit("find_contact: home address found\n");
	  }
	
	$this->add_address(array(
				    'Address Key'=>$home_address->id
				    ,'Address Type'=>'Home'
				    ,'Address Function'=>'Contact'
				    ,'Address Description'=>'Home Contact Address'
				    ));
	}

	if(!array_empty($address_work_data)){

	  $address_work_data['editor']=$this->editor;
	  $work_address=new Address("find in contact ".$this->id." create",$address_work_data);
	  if($work_address->error){
	    print $work_address->msg."\n";
	    exit("find_contact: work address found\n");
	  }
	
	  $this->add_address(array(
				    'Address Key'=>$work_address->id
				    ,'Address Type'=>'Work'
				    ,'Address Function'=>'Contact'
				    ,'Address Description'=>'Work Contact Address'
				    ));
	}


	



	if($data['Contact Main Telephone']!=''){
	  $telephone_data=array();
	  $telephone_data['editor']=$this->editor;
	  $telephone_data['Telecom Raw Number']=$data['Contact Main Telephone'];



	  $telephone=new Telecom("find in contact ".$this->id." create country code ".$this->data['Contact Main Country Code']."  ",$telephone_data);
	  
	  if(!$telephone->error){
	    $this->add_tel(array(
				 'Telecom Key'=>$telephone->id
				 ,'Telecom Type'=>'Telephone'
				 ));
	  }
	}
	if($data['Contact Main FAX']!=''){
	  //print "addin fax\n";
	  $telephone_data=array();
	  $telephone_data['Telecom Raw Number']=$data['Contact Main FAX'];
	  $telephone_data['editor']=$this->editor;
	  $telephone=new Telecom("find in contact ".$this->id." create  country code ".$this->data['Contact Main Country Code']."   ",$telephone_data);
	 
	  if(!$telephone->error){
	  $this->add_tel(array(
			      'Telecom Key'=>$telephone->id
			      ,'Telecom Type'=>'Fax'
			      ));
 }
	}

	if($data['Contact Main Mobile']!=''){
	  //print "addin fax\n";
	  $telephone_data=array('Telecom Raw Number'=>$data['Contact Main Mobile']);
	  $telephone_data['editor']=$this->editor;
	  $telephone=new Telecom("find in contact ".$this->id." create  country code ".$this->data['Contact Main Country Code']."   ",$telephone_data);
	  if(!$telephone->error){
	    
	    $this->add_tel(array(
				 'Telecom Key'=>$telephone->id
			      ,'Telecom Type'=>'Mobile'
			       ));
 }
	}
	}
	


      



      $this->get_data('id',$this->id);
    }else{
      print $sql;
      $this->msg=_("Error can not create contact");
      $this->new=false;
    }

  }
    
   /* Method: get
  Used to get properties of the class
  
  Try to match a contact with the data provided if not found any candidate return 0

  Parameter:
  $key  -     string        tag key of property to be returned 
  $data -     mixed  extra data or output custimize options
  Return: 
  mixed a object property
 */
  
  
  function get($key='',$data=false){
    

    if(array_key_exists($key,$this->data))
      return $this->data[$key];


    switch($key){
    case('has_email_id'):
      if(!$this->emails)
	$this->load('emails');
      return array_key_exists($data,$this->emails);
      break;
    case('main_email'):
   
      return $this->data['main']['email'];
      break;
    }
    
    exit("$key can not be found in contact class\n");

  }

 /* Method: add_company
  Assign company to to the Contact
  
  Search for an email record maching the email data *$data* if not found create a ne email record then add this record to the Contact


  Parameter:
  $data  -    array   contact data
  $args -     string  options

 
 */

  function add_company($data,$args='principal'){
    if(isset($data['Company Key'])){
      $company=new Company('id',$data['Company Key']);
    }else{
      return;
    }
    if($company->id){
      $sql=sprintf("insert into  `Contact Bridge` (`Contact Key`,`Subject Type`, `Subject Key`,`Is Main`) values (%d,'Company',%d,%s)  "
		   ,$company->id
		   ,$this->id
		   ,prepare_mysql(preg_match('/principal/i',$args)?'Yes':'No')
		   );

      mysql_query($sql);
      if(preg_match('/principal/i',$args)){
	$sql=sprintf("update `Contact Dimension` set `Contact Company Name`=%s ,`Contact Company Key`=%s where `Contact Key`=%d"
		     ,prepare_mysql($company->data['Company Name'])
		     ,prepare_mysql($company->id)
		     ,$this->id);
	$this->data['Contact Company Name']=$company->data['Company Name'];
	$this->data['Contact Company Key']=$company->id;
	
	mysql_query($sql);
      } 
    }
  }
  /* Method: update_main_data
     Update the redundant data

     From data directly from the bridge tables and update the apropiate fields

   */
  public function update_redundant_data($fields='all'){
    // get emails
    if(preg_match('/e?mails?/',$fields) or $fields=='all'){
      // get emails
      $sql=sprintf("select * from `Email Bridge` where `Subject Key`=%d and `Subject Type`='Contact' ",$this->id);
      
    }


  }


  /* Method: add_email
  Add/Update an email to the Contact
  
  Search for an email record maching the email data *$data* if not found create a ne email record then add this record to the Contact


  Parameter:
  $data  -    array   email data
  $args -     string  options
  Return: 
  integer email key of the added/updated email

  Todo: use base_data for defaults/missing parameters
 */

  function add_email($data,$args='principal'){
    global $myconf;
    if(isset($data['Email Key'])){

      $email=new Email('id',$data['Email Key']);
      
    }else{
      
      $email=$data['Email'];
      if(isset($data['email_type']))
	$email_type=$data['Email Type'];
      else
	$email_type='';
      $email_data=array(
			'Email Description'=>'',
			'Email'=>$email,
			'Email Type'=>'Unknown',
			'Email Contact Name'=>'',
			'Email Validated'=>'No',
			'Email Verified'=>'No',
			);


    if($this->data['Contact Name']!=$myconf['unknown_contact'])
      $email_data['Email Contact Name']=$this->data['Contact Name'];
    
    
    $email_data['editor']=$this->editor;
    $email=new email('find in contact '.$this->id.' create',$email_data);
    //exit;

     $this->msg.=' '.$email->msg;

    }
    if($email->id){
      
      if($email->updated or $email->new)
	$this->updated=true;

      if(isset($data['Email Type']))
	$email_type=$data['Email Type'];
      else
	$email_type='';
      
      if(preg_match('/work/i',$email_type))
	$email_data['Email Type']='Work';
      elseif(preg_match('/personal/i',$email_type))
	$email_data['Email Type']='Personal';
      elseif(preg_match('/other/i',$email_type))
	$email_data['Email Type']='Other';
      else
	$email_data['Email Type']='Unknown';
      
      

  /*     if($email->data['Email']=='contact@thebigstink.co.uk'){ */
/* 	print_r($this); */
/* 	print "**************** here \n"; */
/*       } */
      
//	exit;
      $sql=sprintf("insert into  `Email Bridge` (`Email Key`,`Subject Type`, `Subject Key`,`Is Main`,`Email Description`) values (%d,'Contact',%d,%s,%s) ON DUPLICATE KEY UPDATE `Email Description`=%s   "
		   ,$email->id
		   ,$this->id
		   ,prepare_mysql(preg_match('/principal/i',$args)?'Yes':'No')
		   ,prepare_mysql($email_data['Email Type'])
		   ,prepare_mysql($email_data['Email Type'])
		   );
      mysql_query($sql);
      if(mysql_affected_rows() )
	$this->updated=true;

      if($email->new){

	//add  to contact hist
	//	print "New email\n";

      if(preg_match('/principal/i',$args)){

	$sql=sprintf("update `Email Bridge`  set `Is Main`='No' where `Subject Type`='Contact' and  `Subject Key`=%d  and `Email Key`!=%d",
		     $this->id
		     ,$email->id
		     );
	mysql_query($sql);
	$sql=sprintf("update `Email Bridge`  set `Is Main`='Yes' where `Subject Type`='Contact' and  `Subject Key`=%d  and `Email Key`=%d",
		     $this->id
		     ,$email->id
		     );
	mysql_query($sql);
	
	$sql=sprintf("update `Contact Dimension` set `Contact Main XHTML Email`=%s ,`Contact Main Plain Email`=%s,`Contact Main Email Key`=%d where `Contact Key`=%d"
		     ,prepare_mysql($email->display('html'))
		     ,prepare_mysql($email->display('plain'))
		     ,$email->id
		     ,$this->id);
	$this->data['Contact Main XHTML Email']=$email->display('html');
	$this->data['Contact Main Plain Email']=$email->display('plain');
	$this->data['Contact Main Email Key']=$email->id;
	mysql_query($sql);

	$history_data['note']=_('Email Associated (Main)');
	$history_data['details']=_($email->display('plain')." "._('set as the principal email for')." ".$this->display("name")." "._('contact'));
      }else{

	$history_data['note']='Email Associated';
	$history_data['details']=_($email->display('plain')." "._('associated with')." ".$this->display("name")." "._('contact'));
      }
      $history_data['action']='associated';
      $history_data['direct_object']='Email';
      $history_data['direct_object_key']=$email->id;
      $history_data['indirect_object']='Contact';
      $history_data['indirect_object_key']=$this->id;
      $this->add_history($history_data);
      
      $this->add_email=$email->id;
        

      }elseif($email->updated){
	if(array_key_exists('Email',$email->updated_fields)){
	  $history_data['note']=_('Email Edited');
	  $history_data['details']=_('Email Edited from')." ".$email->updated_fields['Email']['Old Value']." -> ".$email->updated_fields['Email']['New Value'];

	  $history_data['action']='edited';
	  $history_data['indirect_object']='Email';
	  $history_data['indirect_object_key']=$email->id;
	  $history_data['direct_object']='Contact';
	  $history_data['direct_object_key']=$this->id;
	  $this->add_history($history_data);

	}
	
      }else{
	$this->add_email=0;
      }
    
    

      if(preg_match('/principal/i',$args) and $this->updated){
	if($company_key=$this->company_key('princial')){
	  $company=new Company('id',$company_key);
	  $company->editor=$this->editor;
	  $company->update_email($email->id);
	  
	  $customer_found_keys=$company->get_customers_key();
	  foreach($customer_found_keys as $customer_found_key){
	    $customer=new Customer($customer_found_key);
	    $customer->editor=$this->editor;
	    $customer->update_email($email->id);
	  }
	}
	
	$customer_found_keys=$this->get_customers_key();
	foreach($customer_found_keys as $customer_found_key){
	  $customer=new Customer($customer_found_key);
	  $customer->editor=$this->editor;
	  $customer->update_email($email->id);
	}
      }


    
    }
  }


 /* Method: del_email
  Delete the email from Contact
  
  Delete telecom record  this record to the Contact


  Parameter:
  $args -     string  options
 */
 function del_email($args='principal'){

   if(preg_match('/principal/i',$args) ){
       
     

       $sql=sprintf("delete `Email Bridge`  where `Subject Type`='Contact' and  `Subject Key`=%d  and `Telecom Key`=%d",
		    $this->id
		    ,$this->data['Contact Main Email Key']
		    );
       mysql_query($sql);
       $sql=sprintf("update `Contact Dimension` set `Contact Main XHTML Email`='' `Contact Main Plain Email`='' , `Contact Main Email Key`=''  where `Contact Key`=%d"
		    ,$this->id
		    );
       //print "$sql\n";
       mysql_query($sql);

       
	 if($company_key=$this->company_key('princial')){
	   $company=new Company('id',$company_key);
	   $company->del_tel($args);
	 }

     }

 }







 /*
    Function: update_address
    Update/Create address
   */
   function update_address($data,$type='Work'){

   
    if(!array_empty($data)){
      $address=new address('find in contact '.$this->id.' '.$type.' ',$data);
      if($address->id){
	if($type=='Home'){
	  $address_data=array(
			      'Address Key'=>$address->id
			      ,'Address Type'=>'Home'
			      ,'Address Function'=>'Contact'
			      ,'Address Description'=>'Home Contact Address'
			      );
	}else{
	  $address_data=array(
			      'Address Key'=>$address->id
			      ,'Address Type'=>'Work'
			      ,'Address Function'=>'Contact'
			      ,'Address Description'=>'Work Contact Address'
			      );
	  
	}
	
	
	$this->add_address($address_data,"principal");
      }
    }

  }
  /*
    Function: create_anonymous
    Create an anonymous contact
   */
  private function create_anonymous(){
    global $myconf;
    $this->data['Contact Fuzzy']='Yes';
    $this->data['Contact Name']=$myconf['unknown_contact'];
    $this->data['Contact Informal Greeting']=$myconf['unknown_informal_greting'];
    $this->data['Contact Formal Greeting']=$myconf['unknown_formal_greting'];
    $this->data['Contact File As']=$this->display('file_as');
    $this->data['Contact ID']=$this->get_id();
      $sql="INSERT INTO `dw`.`Contact Dimension` (`Contact ID`, `Contact Salutation`, `Contact Name`, `Contact File As`, `Contact First Name`, `Contact Surname`, `Contact Suffix`, `Contact Gender`, `Contact Informal Greeting`, `Contact Formal Greeting`, `Contact Profession`, `Contact Title`, `Contact Company Name`, `Contact Company Key`, `Contact Company Department`, `Contact Company Department Key`, `Contact Manager Name`, `Contact Manager Key`, `Contact Assistant Name`, `Contact Assistant Key`, `Contact Main Address Key`, `Contact Main Location`, `Contact Main XHTML Address`, `Contact Main Plain Address`, `Contact Main Country Key`, `Contact Main Country`, `Contact Main Country Code`, `Contact Main Telephone`, `Contact Main Plain Telephone`, `Contact Main Telephone Key`, `Contact Main Mobile`, `Contact Main Plain Mobile`, `Contact Main Mobile Key`, `Contact Main FAX`, `Contact Main Plain FAX`, `Contact Main Fax Key`, `Contact Main XHTML Email`, `Contact Main Plain Email`, `Contact Main Email Key`, `Contact Fuzzy`) VALUES (".$this->data['Contact ID'].", 'NULL', ".prepare_mysql($this->data['Contact Name']).",".prepare_mysql($this->data['Contact File As']).", 'NULL',NULL, NULL, 'Unknown',".prepare_mysql($this->data['Contact Informal Greeting']).",".prepare_mysql($this->data['Contact Formal Greeting']).", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, 'Yes');";
    
    if(mysql_query($sql)){
      $this->id= mysql_insert_id();
      $this->new=true;
      $this->get_data('id',$this->id);
    }else{
      $this->msg="Error can not create anonymous contact";
      $this->new=false;
    }

  }


 /* Method: add_address
  Add/Update an address to the Contact
  
  Search for an address record maching the address data *$data* if not found create a ne address record then add this record to the Contact


  Parameter:
  $data  -    array   address data
  $args -     string  options
  Return: 
  integer address key of the added/updated address
 */

function add_address($data,$args='principal'){

  // print_r($data);
  


  if(!$data)
    $address=new address('fuzzy all');
  elseif(is_numeric($data) )
    $address=new address('fuzzy country',$data);
  elseif(is_array($data)){

    if(isset($data['Address Key'])){

      $address=new address('id',$data['Address Key']);
    }    else
      $address=new address('find in contact '.$this->id.' create',$data);

  }else
    $address=new address('fuzzy all');

  if(!$address->id){
    
    return;
    
  }
  
  if($address->updated or $address->new)
	$this->updated=true;
  



  $sql=sprintf("insert into `Address Bridge` (`Subject Type`,`Subject Key`,`Address Key`,`Address Type`,`Address Function`,`Address Description`) values ('Contact',%d,%d,%s,%s,%s)  ON DUPLICATE KEY UPDATE `Address Type`=%s,`Address Function`=%s,`Address Description`=%s",
	       $this->id
	       ,$address->id
	       ,prepare_mysql($data['Address Type'])
	       ,prepare_mysql($data['Address Function'])
	       ,prepare_mysql($data['Address Description'])
	       ,prepare_mysql($data['Address Type'])
	       ,prepare_mysql($data['Address Function'])
	       ,prepare_mysql($data['Address Description'])
	       );
 
  mysql_query($sql);
  if(mysql_affected_rows() )
    $this->updated=true;
  //print "*".mysql_affected_rows()."\n";

  //  exit("$sql\n error can no create contact address bridge");
  
  
  if(preg_match('/principal/i',$args) and $this->updated){

      $sql=sprintf("update `Address Bridge`  set `Is Main`='No' where `Subject Type`='Contact' and  `Subject Key`=%d  and `Address Key`!=%d",
  $this->id
		 ,$address->id
		  );
      mysql_query($sql);
      $sql=sprintf("update `Address Bridge`  set `Is Main`='Yes' where `Subject Type`='Contact' and  `Subject Key`=%d  and `Address Key`=%d",
		   $this->id
		  ,$address->id
		   );
      
      mysql_query($sql);
      $old_value=$this->data['Contact Main XHTML Address'];
	
	$sql=sprintf("update `Contact Dimension`  set `Contact Main Plain Address`=%s,`Contact Main Address Key`=%s ,`Contact Main Location`=%s ,`Contact Main XHTML Address`=%s , `Contact Main Country Key`=%d,`Contact Main Country`=%s,`Contact Main Country Code`=%s where `Contact Key`=%d ",
		     prepare_mysql($address->display('plain')),
		     prepare_mysql($address->id),
		     prepare_mysql($address->data['Address Location']),
		     prepare_mysql($address->display('html')),
		     $address->data['Address Country Key'],
		     prepare_mysql($address->data['Address Country Name']),
		     prepare_mysql($address->data['Address Country Code']),
		     $this->id
		     );
      mysql_query($sql);

     
      $this->get_data('id',$this->id);
      if($old_value!=$this->data['Contact Main XHTML Address'] and !$this->new){
	$details=_('Contact address changed from')." \"".$old_value."\" "._('to')." \"".$this->data['Contact Main XHTML Address']."\"";
	$note=_('Address Changed');
	
	
       
       $history_data=array(
			   'indirect_object'=>'Address'
			   ,'details'=>$details
			   ,'note'=>$note
			   );
       $this->add_history($history_data);

      }
      

      //print "$sql\n";
   if($company_key=$this->company_key('princial')){
     $company=new Company('id',$company_key);
     $company->editor=$this->editor;
     $company->add_address(
			   array(
				      'Address Key'=>$address->id
				      ,'Address Type'=>'Office'
				      ,'Address Function'=>'Contact'
				      ,'Address Description'=>'Company Address'
				 )
			   ,'principal'
			   );
     
     $customer_found_keys=$company->get_customers_key();
     foreach($customer_found_keys as $customer_found_key){
       $customer=new Customer($customer_found_key);
       $customer->editor=$this->editor;
       $customer->update_address_data($address->id);
     }
   }
   
   $customer_found_keys=$this->get_customers_key();
   foreach($customer_found_keys as $customer_found_key){
     $customer=new Customer($customer_found_key);
     $customer->editor=$this->editor;
     $customer->update_address_data($address->id);
   }
 }



 

}

/* Method: add_tel
  Add/Update an telecom to the Contact
  
  Search for an telecom record maching the telecom data *$data* if not found create a ne telecom record then add this record to the Contact


  Parameter:
  $data  -    array   telecom data
  $args -     string  options
  Return: 
  integer telecom key of the added/updated telecom
 */
 function add_tel($data,$args='principal'){

   // print "XXX CACA XXXX\n";
   //print_r($data);

   if(is_string($data)){
     $telecom_data=array();
     $telecom_data['editor']=$this->editor;
     $telecom_data['Telecom Raw Number']=$data;
     
     
     $telecom=new telecom("find in contact create   country code ".$this->data['Contact Main Country Code'],$telecom_data);
     
   }elseif(isset($data['Telecom Key'])){
     $telecom=new Telecom('id',$data['Telecom Key']);
   }else{
     if(!isset($data['Telecom Original Country Key']) or !$data['Telecom Original Country Key'])
       $data['Telecom Original Country Key']=$this->data['Contact Main Country Key'];
     $data['editor']=$this->editor;
     $telecom=new telecom("find in contact create  country code ".$this->data['Contact Main Country Code'],$data);
     
   }

   if($telecom->id){
      

     
     if($telecom->data['Telecom Technology Type']=='Mobile'){
	 $telecom_tipo='Contact Main Mobile';
	 $telecom_tipo_key='Contact Main Mobile Key';
	 $telecom_tipo_plain='Contact Main Plain Mobile';
	 $data['Telecom Type']='Mobile';
     }else{
	 if(preg_match('/fax/i',$data['Telecom Type'])){
	   $telecom_tipo='Contact Main FAX';
	   $telecom_tipo_key='Contact Main FAX Key';
	   $telecom_tipo_plain='Contact Main Plain FAX';


	 }else{
	   $telecom_tipo='Contact Main Telephone';
	    $telecom_tipo_key='Contact Main Telephone Key';
	   $telecom_tipo_plain='Contact Main Plain Telephone';
	   
	 }
       }
     
     

     if(!isset($data['Telecom Description']))
       $data['Telecom Description']=$telecom_tipo;

     

     


     $sql=sprintf("insert into  `Telecom Bridge` (`Telecom Key`, `Subject Key`,`Subject Type`,`Telecom Type`,`Telecom Description`) values (%d,%d,'Contact',%s,%s)   ON DUPLICATE KEY UPDATE `Telecom Type`=%s,`Telecom Description`=%s "
		  ,$telecom->id
		  ,$this->id
		  ,prepare_mysql($data['Telecom Type'])
		  ,prepare_mysql($data['Telecom Description'],false)
		  ,prepare_mysql($data['Telecom Type'])
		  ,prepare_mysql($data['Telecom Description'],false)
		  );
     mysql_query($sql);
     //NOTE:If you use "INSERT INTO ... ON DUPLICATE KEY UPDATE" syntax, mysql_affected_rows() will return you 2 if the UPDATE was made (just as it does with the "REPLACE INTO" syntax) and 1 if the INSERT was.
     if(mysql_affected_rows()==1 ){

       if(preg_match('/principal/i',$args)){
	 $note=$data['Telecom Type']." "._('Associated (Main)');
	 $description=_('Main').' '.$data['Telecom Type'].' '._('set to')." ".$telecom->display('xhtml');
       }else{
	 $note=$data['Telecom Type']." "._('Associated');
	 $description=$data['Telecom Type']." ".$telecom->display('xhtml').' '._('associated to contact')." ".$this->display('name');
	 
       }

       $history_data=array(
			   'note'=>$note
			   ,'details'=>$description
			   ,'action'=>'associated'
			   ,'direct_object'=>$data['Telecom Type']
			   ,'direct_object_key'=>$telecom->id
			   ,'indirect_object'=>'Contact'
			   ,'indirect_object_key'=>$this->id
			   );
       if(!$this->new)
	 $this->add_history($history_data);
       
     }

     // print $sql;
     
     if(preg_match('/principal/i',$args)){
       
       $sql=sprintf("update `Telecom Bridge`  set `Is Main`='No' where `Subject Type`='Contact' and  `Subject Key`=%d  and `Telecom Key`!=%d",
		    $this->id
		    ,$telecom->id
		  );
       mysql_query($sql);
       $sql=sprintf("update `Telecom Bridge`  set `Is Main`='Yes' where `Subject Type`='Contact' and  `Subject Key`=%d  and `Telecom Key`=%d",
		    $this->id
		    ,$telecom->id
		    );
       mysql_query($sql);
       
       // $plain_number=preg_replace('/[^\d]/','',$telecom->display('html'));
	
	 $sql=sprintf("update `Contact Dimension` set `%s`=%s , `%s`=%s , `%s`=%s  where `Contact Key`=%d"
		      ,$telecom_tipo
		      ,prepare_mysql($telecom->display('html'))
		      ,$telecom_tipo_plain
		      ,prepare_mysql($telecom->display('plain'))
		      ,$telecom_tipo_key
		      ,prepare_mysql($telecom->id)

		      ,$this->id
		      );
	 //print "$sql\n";
	 mysql_query($sql);
    
	 
      if($company_key=$this->company_key('princial')){

	$company_telecom_tipo_key=preg_match('/Contact/','Company',$telecom_tipo_key);
	$company=new Company('id',$company_key);
	$company->add_tel(array(
				'Telecom Key'=>$telecom->id
				,'Telecom Type'=>$data['Telecom Type']
				));
      }
     
     }
       
     

     

       
       $this->add_telecom=$telecom->id;

       
   }else{
       $this->add_telecom=0;
	
     }

    

   }
 /* Method: del_tel
  Delete an telecom  in Contact
  
  Delete telecom record  this record to the Contact


  Parameter:
  $args -     string  options
 */
 function del_tel($args='principal'){

   if(preg_match('/principal/i',$args)){
       
       if(preg_match('/fax/i',$args)){
	 

	 $tel_key=$this->data['Contact Main FAX Key'];
	 $telecom_tipo='Contact Main FAX';
	 $telecom_tipo_key='Contact Main FAX Key';
	 $telecom_tipo_plain='Contact Main Plain FAX';
       }if(preg_match('/mobile/i',$args)){
	 $tel_key=$this->data['Contact Main Mobile Key'];
 $telecom_tipo='Contact Main Mobile';
	 $telecom_tipo_key='Contact Main Mobile Key';
	 $telecom_tipo_plain='Contact Main Plain Mobile';
       }else{
	 $tel_key=$this->data['Contact Main Telephone Key'];
 $telecom_tipo='Contact Main Telephone';
	    $telecom_tipo_key='Contact Main Telephone Key';
	   $telecom_tipo_plain='Contact Main Plain Telephone';
       }

       $sql=sprintf("delete `Telecom Bridge`  where `Subject Type`='Contact' and  `Subject Key`=%d  and `Telecom Key`=%d",
		    $this->id
		    ,$tel_key
		    );
       mysql_query($sql);
       $sql=sprintf("update `Contact Dimension` set `%s`='' `%s`='' , `%s`=''  where `Contact Key`=%d"
		      ,$telecom_tipo
		      ,$telecom_tipo_plain
		      ,$telecom_tipo_key
		      ,$this->id
		      );
	 //print "$sql\n";
	 mysql_query($sql);

       
	 if($company_key=$this->company_key('princial')){
	   
	   $company_telecom_tipo_key=preg_match('/Contact/','Company',$telecom_tipo_key);
	   
	   $company=new Company('id',$company_key);
	   $company->del_tel($args);
	 }




     }

 }

           

 /*Function: update_field_switcher
   Custom update switcher
  */

protected function update_field_switcher($field,$value,$options=''){


  

  switch($field){


  case('Contact Name'):
    $this->update_Contact_Name($value,$options);
    break;
  case('Contact Main Plain Email'):

    if($value==''){
      $this->del_email('principal');
    }elseif(!email::wrong_email($value)){
      $value=email::prepare_email($value);
      $email_data=array('Email'=>$value,'editor'=>$this->editor);
   
      $this->add_email($email_data,$options.' principal');
    }
    break;  
  case('Contact Main Telephone'):
    // print "xxx\n";
    // check if plain numbers are the same
    $tel_data=Telecom::parse_number($value);
    $plain_tel=Telecom::plain_number($tel_data);
    if($plain_tel!=$this->data['Contact Main Plain Telephone']){
      if($plain_tel==''){
	// Remove main telephone
	$this->del_tel('principal');
      }else{
	$tel_data=array(
			'Telecom Raw Number'=>$value
		      ,'Telecom Type'=>'Telephone'
			);
	$this->add_tel($tel_data,$options.' principal');
      }
    }
    break;  
 case('Contact Main FAX'):
   // print "y\n";
 $tel_data=Telecom::parse_number($value);
    $plain_tel=Telecom::plain_number($tel_data);
    if($plain_tel!=$this->data['Contact Main Plain FAX']){
   
      if($plain_tel==''){
	// Remove main telephone
	$this->del_tel('principal fax');
      }else{
	$tel_data=array(
			'Telecom Raw Number'=>$value
			,'Telecom Type'=>'Fax'
			);
	$this->add_tel($tel_data,$options.' principal');
      }
    }
    break;  

  case('Contact Old ID'):  
    $this->update_Contact_Old_ID($value,$options);
    break;

  case('Contact Main Mobile'):
    $tel_data=Telecom::parse_number($value);
    $plain_tel=Telecom::plain_number($tel_data);
    


    if($plain_tel!=$this->data['Contact Main Plain Mobile']){
      if($plain_tel==''){
	// Remove main telephone
	$this->del_tel('principal mobile');
      }else{
      
      $tel_data=array(
		      'Telecom Raw Number'=>$value
		      ,'Telecom Type'=>'Mobile'
		      );
      $this->add_tel($tel_data,$options.' principal');
      }
    }   
    break;  
  case('Home Address'):

    break;

  default:
    print "--------------------";
    $this->update_field($field,$value,$options);
  }
  
}


/*Method:update_Contact_Name
 Update contact name
 

*/

function update_Contact_Name($data,$options=''){
  global $myconf;

  $parsed_data=$this->parse_name($data);
  foreach($parsed_data as $key=>$val){
    if(array_key_exists($key,$this->data))
      $this->data[$key]=$val;
  }
  $this->data['Contact Name']=$this->display('name');
  
  if($data==''){
    $this->msg.=_('Warning, contact name should not be blank')."\n";
    $this->warning=true;
    $this->data['Contact Name']=$myconf['unknown_contact'];
    $this->data['Contact Informal Greeting']=$myconf['unknown_informal_greting'];
    $this->data['Contact Formal Greeting']=$myconf['unknown_formal_greting'];
    $this->data['Contact Gender']=$this->gender($this->data);
  }else{

    $this->data['Contact Name']=$this->display('name');
    $this->data['Contact File As']=$this->display('file_as');
    $this->data['Contact Gender']=$this->gender($this->data);
    $this->data['Contact Informal Greeting']=$this->display('informal gretting');
    $this->data['Contact Formal Greeting']=$this->display('formal gretting');

  }
  $this->data['Contact File As']=$this->display('file_as');
  $values='';
  foreach($this->data as $key=>$value){
    // Just insert name fields, company,email,tel,ax,address should be inserted later
    if(preg_match('/Salutation|Contact Name|Contact File As|First Name|Surname|Suffix|Gender|Greeting/i',$key)){
      
      $values.=" `$key`=";
      if(preg_match('/suffix|plain/i',$key))
	  $print_null=false;
      else
	$print_null=true;
      $values.=prepare_mysql($value,$print_null).",";
      }
  }
  $values=preg_replace('/,$/',' ',$values);

  $sql=sprintf("update `Contact Dimension` set %s where `Contact Key`=%d",$values,$this->id);
  //print $sql;
  mysql_query($sql);
  $affected=mysql_affected_rows();
  if($affected==-1){
    $this->msg=_('Contact name can not be updated')."\n";
    $this->error=true;
    return;
  }elseif($affected==0){
    //$this->msg=_('Same value as the old record');
    
  }else{
    
    $this->msg=_('Contact name  updated');
    $this->updated=true;
   
  }

}
/* Function:update_Contact_Old_ID
   Updates the contact old id

 */
private function update_Contact_Old_ID($contact_old_id,$options){
  $contact_old_id=_($contact_old_id);
  if($contact_old_id==''){
  $this->new=false;
    $this->msg.=" Contact Old ID name should have a value";
    $this->error=true;
    if(preg_match('/exit on errors/',$options))
      exit($this->msg);
    return false;
  }

  $old_value=$this->data['Contact Old ID'];
  $individual_ids=array();
  foreach(preg_split('/,/',$old_value) as $individual_id){
    if(_trim($individual_id)!=''){
      $individual_ids[$individual_id]=true;
    }
  }
  
  if(array_key_exists($contact_old_id, $individual_ids)){
       $this->msg.=' '._('Contact Old ID already in record')."\n";
       $this->warning=true;
       return;
  }
  
  
  
  $this->data['Contact Old ID']=',';
  foreach($individual_ids as $key=>$val){
    $this->data['Contact Old ID'].=$key.',';
  }
  
  $sql=sprintf("update `Contact Dimension` set `'Contact Old ID`=%s where `Contact Key`=%d "
	       ,prepare_mysql($this->data['Contact Old ID'])
	       ,$this->id);
  mysql_query($sql);
  $affected=mysql_affected_rows();
  
  if($affected==-1){
    $this->msg.=' '._('Contact Old ID  can not be updated')."\n";
    $this->error=true;
    return;
  }elseif($affected==0){
    //$this->msg.=' '._('Same value as the old record');
    
  }else{
    $this->msg.=' '._('Record updated')."\n";
    $this->updated=true;

  
    
  }  
  
}
  
/*   function update($values,$args='',$history_data=false){ */

/*     $key=key($data); */
/*     $value=$data['value']; */

/*     switch($key){ */
/*     case('main_email'): */
/*       if($value==$this->get($key)){ */
/* 	$this->msg=_('Same value'); */
/* 	$this->updated=false; */
/* 	return; */
/*       } */
/*       $main_email=new email($value); */
/*       if(!$main_email->id){ */
/* 	$this->msg=_('Email not found'); */
/* 	$this->updated=false; */
/* 	return; */
/*       } */
/*       $email_contact_id=$main_email->get('contact_id'); */
/*       if($email_contact_id==0 or $email_contact_id){ */
/* 	$main_email->update( */
/* 			    array('contact_id'=>array('value'=>$this->id)) */
/* 			    ,'save',$history_data */
/* 			    ); */
/*       } */

/*       if($main_email->get('contact_id')){ */
/* 	$this->msg=_('Email not found'); */
/* 	$this->updated=false; */
/* 	return; */
/*       } */

/*       $this->old['main_email']=$this->data['main']['email']; */
/*       $this->data['main_email']=$value; */
/*       $this->data['main']['email']=$main_email->data['email']; */
/*       $this->updated=true; */
/*     case('gender'): */
/*       if($value==$this->get($key)){ */
/* 	$this->msg=_('Same value'); */
/* 	$this->updated=false; */
/* 	return; */
/*       } */
/*       $valid_values=array('male','felame','neutro','unknown'); */
/*       if(!is_in_array($value,$valid_values)){ */
/* 	$this->msg=_('Same value'); */
/* 	$this->updated=false; */
/* 	return; */
/*       } */
/*       $this->old['gender']=$this->get($key); */
/*       $this->data['gender']=$value; */
       

/*     } */

    
   
/*   } */


/*   function save($key,$history_data=false){ */
/*     switch($key){ */
/*     case('main_email'): */
/*       $sql=sprintf('update contact set %s=%s where id=%d',$key,prepare_mysql($this->data[$key]),$this->id); */
/*       //	print "$sql\n"; */
/*       mysql_query($sql); */

/*       if(is_array($history_data)){ */
/* 	$this->save_history($key,$this->old[$key],$this->data['main']['email'],$history_data); */
/*       } */


/*       break; */
/*     } */

/*   } */

/*   function save_history($key,$old,$new,$data){ */
   
/*   } */

/*   function load($key=''){ */
/*     switch($key){ */
/*     case('telecoms'): */
/*       $this->load('tels'); */
/*       $this->load('emails'); */
/*       break; */
/*     case('tels'): */
/*       $sql=sprintf("select telecom_id from telecom2contact left join telecom on (telecom.id=telecom_id) where contact_id=%d ",$this->id); */
/*       $result=mysql_query($sql); */
/*       while($row=mysql_fetch_array($result, MYSQL_ASSOC)){ */
	
	
/* 	if($tel=new telecom($row['telecom_id'])) */
/* 	  $this->tel[]=$tel; */
/*       } */
/*       break; */
/*     case('emails'): */
/*       //   $this->emails=array(); */
/*       //       $sql=sprintf("select id from email where contact_id=%d ",$this->id); */
/*       //       $result =& $this->db->query($sql); */

/*       //       while($row=$result->fetchRow()){ */
/*       // 	$email=new Email($row['id']); */
/*       // 	$this->emails[$email->id]=array( */
/*       // 					'email'=>$email->get('email'), */
/*       // 				       'email_link'=>$email->get('link') */
/*       // 				       ); */
/*       //       } */
/*       break; */
/*     case('contacts'): */
/*       //    $sql=sprintf("select child_id from contact_relations where parent_id=%d ",$this->id); */
/*       //       $result =& $this->db->query($sql); */
/*       //       while($row=$result->fetchRow()){ */
/*       // 	if($child=new telecom($row['child_id'])) */
/*       // 	  $this->contacts[]=$child; */
/*       //       } */
/*       break; */

/*     } */
    
/*   } */
  
 

  //  function get_date($key='',$tipo='dt'){
  //     if(isset($this->dates['ts_'.$key]) and is_numeric($this->dates['ts_'.$key]) ){

  //       switch($tipo){
  //       case('dt'):
  //       default:
  // 	return strftime("%e %B %Y %H:%M", $porder['date_expected']);
  //       }
  //     }else
  //       return false;
  //   }
  

  //   function file_as(){
    
  //     if($this->data['contact name']==$this->unknown_name)
  //       return $this->unknown_name;
  //     else
  //       return $this->data['contact name'];
  //   }

  function get_id(){
    
    $sql="select max(`Contact ID`)  as contact_id from `Contact Dimension`";
 $result=mysql_query($sql);
  if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      
      if(!preg_match('/\d*/',_trim($row['contact_id']),$match))
	$match[0]=1;
      $right_side=$match[0];
      $number=(double) $right_side;
      $number++;
      $id=$number;
    }else{
      $id=1;
    }  
    return $id;
  }



  function parse_address($data){
    $address_data=array(
			'Street Number'=>'',
			'Street Name'=>'',
			'Street Type'=>'',
			'Street Direction'=>'',
			'Post Box'=>'',
			'Suite'=>'',
			'City Subdivision'=>'',
			'City Division'=>'',
			'City'=>'',
			'District'=>'',
			'Second District'=>'',
			'State'=>'',
			'Region'=>'',
			'Address Country Key'=>'',
			'Address Country Name'=>'',
			'Address Country Code'=>'',
			'Address Country 2 Alpha Code'=>'',
			'Address World Region'=>'',
			'Address Continent'=>'',
			'Postal Code'=>'',
			'Primary Postal Code'=>'',
			'Secondary Postal Code'=>'',
			'Postal Code Separator'=>'',
			'Fuzzy Address'=>''
			);
    
    return $address_data;
  }
    /* Method: prepare_name_data
  Clean the Name data array
 */ 
  
  public static function prepare_name_data($raw_data){

    if(isset($raw_data['Contact Salutation']))
      $data['Contact Salutation']=mb_ucwords(_trim($raw_data['Contact Salutation']));
    if(isset($raw_data['Contact First Name']))
      $data['Contact First Name']=mb_ucwords(_trim($raw_data['Contact First Name']));
    else
      $data['Contact First Name']='';
    if( isset($raw_data['Contact Middle Name']))
      $data['Contact First Name'].=mb_ucwords(_trim(' '.$raw_data['Contact Middle Name']));

    if(isset($raw_data['Contact Surname']))
      $data['Contact Surname']=mb_ucwords(_trim($raw_data['Contact Surname']));
    if(isset($raw_data['Contact Suffix']))
      $data['Contact Suffix']=mb_ucwords(_trim($raw_data['Contact Suffix']));

    $data['Contact Gender']='Unknown';
    if(isset($raw_data['Contact Gender']) and ($raw_data['Contact Gender']=='Male' or $raw_data['Contact Gender']=='Female'))
      $data['Contact Gender']=_trim($raw_data['Contact Gender']);
   
    if($data['Contact Gender']=='Unknown')
      $data['Contact Gender']=Contact::gender($raw_data);

   
    return $data;

  }

 /* Method: parse_name
  Parse a name detecting its components
  
Parameter:
string with the name to be parsed

 Returns:
 Array with the name componets: Contact Salutation, Contact First Name, Contact Surname, Contact Suffix

 */ 
  public static function parse_name($raw_name){
     $name=array(
		'prefix'=>'',
		'first'=>'',
		'middle'=>'',
		'last'=>'',
		'suffix'=>'',
		'alias'=>''
		);
    $raw_name=_trim($raw_name);
    $forbiden_names=array('sir/madam','sir,madam');

    if(in_array(strtolower($raw_name),$forbiden_names))
      $raw_name='';
    if(Contact::is_prefix($raw_name))
      $raw_name='';

  
     
    $raw_name=_trim($raw_name);
    $raw_name=preg_replace('/\./',' ',$raw_name);
    $names=preg_split('/\s+/',$raw_name);

    $parts=count($names);

    switch($parts){
    case(1):
      if(Contact::is_surname($names[0]))
	$name['last']=$names[0];
      else if(Contact::is_givenname($names[0]))
	$name['first']=$names[0];
      else if(Contact::is_prefix($names[0]))
	$name['prefix']=$names[0];
      else
	$name['first']=$names[0];
      break;
    case(2):
      // firt the most obious choise
    
      if(Contact::is_givenname($names[0])){
	$name['first']=$names[0];
	$name['last']=$names[1];
      

      }else if(Contact::is_givenname($names[0]) and   Contact::is_surname($names[1])){
	$name['first']=$names[0];
	$name['last']=$names[1];

      }else if( Contact::is_prefix($names[0]) and   Contact::is_surname($names[1])){
	$name['prefix']=$names[0];
	$name['last']=$names[1];
      }else if( Contact::is_prefix($names[0]) and   Contact::is_givenname($names[1])){
	$name['prefix']=$names[0];
	$name['first']=$names[1];
      }else if( Contact::is_surname($names[0]) and   Contact::is_surname($names[1])){
	$name['last']=$names[0].' '.$names[1];
      }else{
	$name['first']=$names[0];
	$name['last']=$names[1];

      }
      break;
    case(3):
      // firt the most obious choise
      
      
      if(!Contact::is_prefix($names[0]) and  strlen($names[1])==1   and   strlen($names[2])>1  ){
	$name['first']=$names[0];
	$name['middle']=$names[1];
	$name['last']=$names[2];
      }elseif( Contact::is_prefix($names[0])) {
	$name['prefix']=$names[0];
	$name['first']=$names[1];
	$name['last']=$names[2];

	
	// 	if(   Contact::is_givenname($names[1]) and   Contact::is_surname($names[2])){

	// 	  $name['first']=$names[1];
	// 	  $name['last']=$names[2];
	// 	}else if(    strlen($names[1])==1 and   Contact::is_surname($names[2])){
	  
	// 	  $name['first']=$names[1];
	// 	  $name['last']=$names[2];
	// 	}else if(   Contact::is_givenname($names[1])    and   Contact::is_givenname($names[2])){
	  
	// 	  $name['first']=$names[1].' '.$names[2];
	// 	}else if(  Contact::is_surname($names[1])    and   Contact::is_surname($names[2])){
	  
	// 	  $name['last']=$names[1].' '.$names[2];
	// 	}else{
	// 	  $name['first']=$names[1];
	// 	  $name['last']=$names[2];
	  
	// 	}
	

      }else if(  Contact::is_givenname($names[0])   and   Contact::is_givenname($names[1])  and   Contact::is_surname($names[2])){
	$name['first']=$names[0].' '.$names[1];
	$name['last']=$names[2];
      }else if(  Contact::is_givenname($names[0])   and   Contact::is_surname($names[1])  and   Contact::is_surname($names[2])){
	$name['first']=$names[0];
	$name['last']=$names[1].' '.$names[2];
      }else if( Contact::is_givenname($names[0]) and     strlen($names[1])==1 and   Contact::is_surname($names[2])){
	$name['first']=$names[0];
	$name['middle']=$names[1];
	$name['last']=$names[2];
      }else{
	$name['first']=$names[0];
	$name['last']=$names[1].' '.$names[2];
      }
      break;
    case(4):


      
      if( Contact::is_prefix($names[0])) {
	$name['prefix']=$names[0];
	
	if(  Contact::is_givenname($names[1]) and    strlen($names[2])==1 and  Contact::is_surname($names[3])){

	  $name['first']=$names[1];
	  $name['middle']=$names[2];
	  $name['last']=$names[3];
	}else if(  Contact::is_givenname($names[1]) and   Contact::is_givenname($names[2])  and  Contact::is_surname($names[3])){

	  $name['first']=$names[1].' '.$names[2];
	  $name['last']=$names[3];
	}else if( Contact::is_prefix($names[0]) and     Contact::is_givenname($names[1]) and   Contact::is_surname($names[2])  and  Contact::is_surname($names[3])){
	  
	  $name['first']=$names[1];
	  $name['last']=$names[2].' '.$names[3];
	  
	}else
	  $name['first']=$names[1].' '.$names[2];
	$name['last']=$names[3];
	

	// firt the most obious choise
      }else if(      Contact::is_givenname($names[0]) and Contact::is_givenname($names[1]) and    Contact::is_surname($names[2])  and  Contact::is_surname($names[3])     ){

	$name['first']=$names[0].' '.$names[1];
	$name['last']=$names[2].' '.$names[3];
      }else  if(      Contact::is_givenname($names[0]) and Contact::is_givenname($names[1]) and    Contact::is_givenname($names[2])  and  Contact::is_surname($names[3])     ){

	$name['first']=$names[0].' '.$names[1].' '.$names[2];
	$name['last']=$names[3];
      }else{
	$name['first']=$names[0];
	$name['last']=$names[1].' '.$names[2].' '.$names[3];
      }
      break;
    case(5):
      if( Contact::is_prefix($names[0]) and     Contact::is_givenname($names[1]) and   Contact::is_givenname($names[2])   and  Contact::is_surname($names[3]) and Contact::is_surname($names[4])  ){
	$name['prefix']=$names[0];
	$name['first']=$names[1].' '.$names[2];
	$name['first']=$names[3].' '.$names[4];
      }
      else
	$name['last']=join(' ',$names);
      break;
    default:
      $name['last']=join(' ',$names);
    
    }

  

    foreach($name as $key=>$value){
      $name[$key]=mb_ucwords($value);
     
    }
  


 
    $data['Contact Salutation']=_trim($name['prefix']);
    $data['Contact First Name']=_trim($name['first'].' '.$name['middle']);
    $data['Contact Surname']=_trim($name['last']);
    $data['Contact Suffix']=_trim($name['suffix']);
    

    return $data;
    


  }
  



  /*Function:display

   */

  public function display($tipo='name'){
    
  global $myconf;

    switch($tipo){
    case('name'):
      $name=$this->name($this->data);
      return $name;
      break;
    case('file_as'):
    case('file as'):
      if($this->data['Contact Fuzzy']=='Yes')
	$name=_trim($this->data['Contact Name']);
      else
	$name=_trim($this->data['Contact Surname'].' '.$this->data['Contact First Name']);
      return $name;
  

  case('informal gretting'):

      $gretting=_('Hello').' ';
      $name=_trim($this->data['Contact First Name']);
      $name=preg_replace('/\s+/',' ',$name);
      if(strlen($name)>1 and !preg_match('/^[a-z] [a-z]$/i',$name) and  !preg_match('/^[a-z] [a-z] [a-z]$/i',$name)  ) 
	return $gretting.$name;
  
   

    if(strlen($this->data['Contact Surname'])>1){
	$name=_trim(
		    $this->data['Contact Salutation'].' '.$this->data['Contact Surname']
		    );
   

	$name=preg_replace('/\s+/',' ',$name);
	return $gretting.$name;
      }
       
   return $myconf['unknown_informal_greting'];
      
      
    case('formal gretting'):
      $gretting=_('Dear').' ';
      if(strlen($this->data['Contact Surname'])>1){
	 
	if($this->data['Contact Salutation']!=''){
	  $name=_trim($this->data['Contact Salutation'].' '.$this->data['Contact Surname']);
	  return $gretting.$name;
	}elseif($this->data['Contact First Name']!=''){
	  $name=_trim($this->data['Contact First Name'].' '.$this->data['Contact Surname']);
	  return $gretting.$name;
	}
      }
      return $myconf['unknown_formal_greting'];	 
      
    }
    
    return false;
    
  }
  /*function: name

   */
  public static function name($data){
    global $myconf;
    if(array_empty($data))
      return $myconf['unknown_contact'];
    
    $name=_trim($data['Contact Salutation'].' '.$data['Contact First Name'].' '.$data['Contact Surname'].' '.$data['Contact Suffix']);
    return $name;
      
  }

  /*
   Method: gender
   Guess the gender from the name components

   Parameter:
   array with keys *Contact Salutation* and *Contact First Name*

   Return:
   Male,Felame,Unknown

   */
  
  public static function gender($data){
  
    $prefix=$data['Contact Salutation'];
    $first_name=$data['Contact First Name'];
    $sql=sprintf("select `Gender` from  `Salutation Dimension`  where `Salutation`=%s ",prepare_mysql($prefix));
    $result=mysql_query($sql);
    if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      
      if($row['Gender']=='Male' or $row['Gender']=='Female')
	return $row['Gender'];
    }
    
    
    $male=0;
    $felame=0;
    $names=preg_split('/\s+/',$first_name);
    foreach($names as $name){
      $sql=sprintf("select `Gender` as genero from  `First Name Dimension` where `First Name`=%s",prepare_mysql($name));
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	
	if($row['genero']=='Male')
	  $male++;
	if($row['genero']=='Felame')
	  $felame++;
      }
    }
    if($felame>$male)
      return 'Felame';
    else if ($male>$felame)
      return 'Male';
    else
      return 'Unknown';
  
  }
 /*
   Method: is_givenname
   Look for the First Name in the DB

   Parameter:
   string First Name

   Return:
   First Name Key of the First Name Dimension  DB record or 0 if not found

   */
  function is_givenname($name){
    $sql=sprintf("select `First Name Key` as id from  `First Name Dimension` where `First Name`=%s",prepare_mysql($name));
    $result=mysql_query($sql);
    if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      return $row['id'];
    }else
      return 0;
  }
 /*
   Method: is_surname
   Look for the Surname in the DB

   Parameter:
   string Surname

   Return:
   Key of the Surname Dimension  DB record or 0 if not found

   */


  function is_surname($name){

    $sql=sprintf("select `Surname` as id from  `Surname Dimension` where `Surname`=%s",prepare_mysql($name));
    $result=mysql_query($sql);
    if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      return $row['id'];
    }else
      return 0;
  }

 /*
   Method: is_prefix
   Look for the saludation in the DB

   Parameter:
   string Saludation

   Return:
   Key of the Saludation Dimension  DB record or 0 if not found

   */

  public static function is_prefix($name){
    $sql=sprintf("select `Salutation` as id from `Salutation Dimension`  where `Salutation`=%s",prepare_mysql($name));
    $result=mysql_query($sql);
    if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      return $row['id'];
    }else
      return 0;
  }

  /*
    Function: company_key
    Returns the key of the contact company

    Parameter:
    options - string, principal return company key only if this is  the principal cpntact

    Returns:
    the key of the contact company or false if contact has not company associates
    
   */
   public  function company_key($options=''){
     if(preg_match('/principal/',$options)){
       $sql=sprintf("select `Subject Key` from `Conatct Bridge` where `Subject Key`='Company' and `Is Main`='Yes' and `Contact Key`=%d",$this->id);
       $result=mysql_query($sql); 
       if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	 return $row['Subject Key'];

       }
     }else{
       //     if(!isset($this->data['Contact Company Key'])){
       //	 print_r($this);
       //	 exit;
       // }
       
       return $this->data['Contact Company Key'];

     }
     return false;
     
  }

 /*
     function: card
     Returns an array with the contact details
    */
   function card(){


     $card=array(
		 'Contact Name'=>$this->data['Contct Name']
		 ,'Emails'=>array()
		 ,'Telephones'=>array()
		 ,'Addresses'=>array()
		 );
     
     $sql=sprintf("select   ED.`Email`,ED.`Email Key`,EB.`Is Main`,EB.`Email Description`  from `Email Bridge` EB left join `Email Dimension` on (EB.`Email Key`=ED.`Email Key`) where `Subject Type`='Contact' and `Subject Key`=%d and `Is Active`='Yes' order by `Is Main` desc",$this->id);
     $result=mysql_query($sql);
     while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       $card['Emails'][$row['Email Key']]=array(
						'Address'=>$row['Email']
						,'Description'=>$row['Email Description']
						,'Principal'=>$row['Is Main']
						);
     }
     $sql=sprintf("select TB.`Telecom Key`,TB.`Is Main`,TB.`Telecom Description`  from `Telecom Bridge`  where `Subject Type`='Contact' and `Subject Key`=%d and `Is Active`='Yes' order by `Is Main` desc",$this->id);
      $result=mysql_query($sql);
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	$telecom=new Telecom($row['Telecom Key']);
	$card['Emails'][$row['Telecom Key']]=array(
						   'Number'=>$telecom->display()
						   ,'Description'=>$row['Telecom Description']
						   ,'Principal'=>$row['Is Main']
						 );
      }

      $sql=sprintf("select AB.`Address Key`,AB.`Is Main`,AB.`Address Description`  from `Address Bridge`  where `Subject Type`='Contact' and `Subject Key`=%d and `Is Active`='Yes' order by `Is Main` desc",$this->id);
      $result=mysql_query($sql);
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
	$telecom=new Address($row['Address Key']);
	$card['Addresses'][$row['Address Key']]=array(
						   'Address'=>$address->display()
						   ,'Description'=>$row['Address Description']
						   ,'Principal'=>$row['Is Main']
						 );
      }



      return $card;

   }
   /*
     function:get_main_telephone_data
     Array with the data components of the main telephone
    */
   function get_main_telephone_data(){
     $telephone=array('Telecom Country Telephone Code'=>'','Telecom Area Code'=>'','Telecom Number'=>'','Telecom Extension'=>'');
     if($this->data['Contact Main Telephone Key']){
       $telecom=new Telecom($this->data['Contact Main Telephone Key']);
       $telephone['Telecom Country Telephone Code']=$telecom->data['Telecom Country Telephone Code'];
       $telephone['Telecom National Access Code']=$telecom->data['Telecom Area Code'];
       $telephone['Telecom Area Code']=$telecom->data['Telecom Area Code'];
       $telephone['Telecom Number']=$telecom->data['Telecom Number'];
       $telephone['Telecom Extension']=$telecom->data['Telecom Extension'];
     }
     return $telephone;
   }

/*
     function:get_main_fax_data
     Array with the data components of the main fax
    */
   function get_main_fax_data(){
     $fax=array('Telecom Country Fax Code'=>'','Telecom Area Code'=>'','Telecom Number'=>'','Telecom Extension'=>'');
     if($this->data['Contact Main FAX Key']){
       $telecom=new Telecom($this->data['Contact Main FAX Key']);
       $fax['Telecom Country Telephone Code']=$telecom->data['Telecom Country Telephone Code'];
       $fax['Telecom National Access Code']=$telecom->data['Telecom Area Code'];
       $fax['Telecom Area Code']=$telecom->data['Telecom Area Code'];
       $fax['Telecom Number']=$telecom->data['Telecom Number'];

     }
     return $fax;
   }
   
/*
     function:get_main_mobile_data
     Array with the data components of the main mobile
    */
   function get_main_mobile_data(){
     $mobile=array('Telecom Country Mobile Code'=>'','Telecom Area Code'=>'','Telecom Number'=>'','Telecom Extension'=>'');
     if($this->data['Contact Main Mobile Key']){
       $telecom=new Telecom($this->data['Contact Main Mobile Key']);
       $mobile['Telecom Country Telephone Code']=$telecom->data['Telecom Country Telephone Code'];
       $mobile['Telecom National Access Code']=$telecom->data['Telecom Area Code'];
       $mobile['Telecom Area Code']=$telecom->data['Telecom Area Code'];
       $mobile['Telecom Number']=$telecom->data['Telecom Number'];

     }
     return $mobile;
   }

/*
     function:get_main_address_data
     Array with the data components of the main address
    */
   function get_main_address_data(){
     $address_data=array('Country Name'=>'','Town'=>'','Internal'=>'','Bulding'=>'','Street'=>'','Country First Division'=>'','Country Secondary Division'=>'');
     if($this->data['Contact Main Address Key']){
       $address=new Address($this->data['Contact Main Address Key']);
       $address_data['Town']=$address->data['Address Town'];
       $address_data['Postal Code']=$address->data['Address Postal Code'];
       $address_data['Country Name']=$address->data['Address Country Name'];
       $address_data['Internal']=$address->data['Address Internal'];
       $address_data['Building']=$address->data['Address Building'];
       $address_data['Street']=$address->display('street');


     }
     return $address_data;
   }
/*
     function: get_customer_key
     Returns the Customer Key if the contact is one
    */
   function get_customers_key(){
     $sql=sprintf("select `Customer Key` from `Customer Dimension` where `Customer Type`='Person' and `Customer Main Contact Key`=%d  ",$this->id);
     $customer_keys=array();
     $result=mysql_query($sql);
     while($row=mysql_fetch_array($result, MYSQL_ASSOC)){
       $customer_keys[$row['Customer Key']]= $row['Customer Key'];
       
     }
     return $customer_keys;
   }

} 
 ?>