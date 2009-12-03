<?php
require_once 'class.Timer.php';

require_once 'common.php';
require_once 'class.Company.php';
require_once 'class.Supplier.php';
require_once 'ar_edit_common.php';



if(!isset($_REQUEST['tipo']))
  {
    $response=array('state'=>405,'resp'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
  }

$editor=array(
	      'Author Name'=>$user->data['User Alias'],
	      'Author Type'=>$user->data['User Type'],
	      'Author Key'=>$user->data['User Parent Key'],
	      'User Key'=>$user->id
	      );



$tipo=$_REQUEST['tipo'];
switch($tipo){
case('new_company'):
 $data=prepare_values($_REQUEST,array(
			     'values'=>array('type'=>'json array')
			   
			     ));
  new_company($data);

break;
case('new_address'):
  new_address();
  break;
case('edit_address_type'):
  edit_address_type();
  break;
case('edit_address'):
   edit_address();
   break;
case('edit_company'):
  edit_company();
  break;
case('edit_customer'):
  edit_customer();
  break;
case('edit_customers'):
  list_customers();
  break;


case('edit_contact'):
   $data=prepare_values($_REQUEST,array(
			     'id'=>array('type'=>'key')
			     ,'value'=>array('type'=>'json array')
			     ,'subject_key'=>array('type'=>'key')
			     ));
  edit_contact($data);
  break;
case('edit_email'):
  $data=prepare_values($_REQUEST,array(
			     'id'=>array('type'=>'key')
			     ,'value'=>array('type'=>'json array','required elements'=>array(
											     'Email'=>'string'
											     ,'Email Key'=>'numeric'
											     ))
			     ,'subject_key'=>array('type'=>'key')
			     ,'subject'=>array('type'=>'enum','valid values regex'=>'/company|contact/i')
			     ));

  edit_email($data);
  break;
case('remove_email'):
case('delete_email'):
  delete_email();
  break;
case('edit_telecom'):
   $data=prepare_values($_REQUEST,array(
			     'id'=>array('type'=>'key')
			     ,'value'=>array('type'=>'json array','required elements'=>array(
											     'Telecom'=>'string'
											     ,'Telecom Key'=>'numeric'
											     ,'Telecom Type'=>'string'
											     ,'Telecom Container'=>'numeric'
											     ,'Telecom Is Main'=>'string'
											     ))
			     ,'subject_key'=>array('type'=>'key')
			     ,'subject'=>array('type'=>'enum','valid values regex'=>'/company|contact/i')
			     ));
  edit_telecom($data);
  break;
  

}
function edit_contact($data){
global $editor;


 $contact=new Contact($data['id']);

 if(!$contact->id){
   $response=array('state'=>400,'msg'=>_('Contact not found'));
 }
  
 $translator=array(
		   'Contact_Name_Components'=>'Contact Name Components'
		   ,'Contact_Gender'=>'Contact Gender'
		   ,'Contact_Title'=>'Contact Title'
		   ,'Contact_Profession'=>'Contact Profession'
		   );
 $components_translator=array(
				   'Contact_First_Name'=>'Contact First Name'
				   ,'Contact_Surname'=>'Contact Surname'
				   ,'Contact_Suffix'=>'Contact Suffix'
				   ,'Contact_Salutation'=>'Contact Salutation'
				   
		   );
 
 
 foreach($data['value'] as $key=>$value){
   if (array_key_exists($key, $translator)) {
     
     if($key=='Contact_Name_Components'){
       $components=array();
       foreach($value as $component_key => $component_value){
	 if (array_key_exists($component_key, $components_translator)) 
	   $components[$components_translator[$component_key]]=$component_value;

       }
       $contact_data[$translator[$key]]=$components;
       
     }else
       $contact_data[$translator[$key]]=$value;

   }

 }

 
 $contact_data['editor']=$editor;
 $contact->update($contact_data);
 
 $contact->reread();
 if($contact->error_updated){
   $response=array('state'=>200,'action'=>'error','msg'=>$contact->msg_updated);
 }else{
   
   if($contact->updated){
     $contact->reread();
     $updated_data_name_components=array(
			 'Contact_First_Name'=>$contact->data['Contact First Name']
			 ,'Contact_Surname'=>$contact->data['Contact Surname']
			 ,'Contact_Suffix'=>$contact->data['Contact Suffix']
			 ,'Contact_Salutation'=>$contact->data['Contact Salutation']
		
			 );

     $updated_data=array(
			 'Contact_Name'=>$contact->data['Contact Name']
			 ,'Name_Data'=>$updated_data_name_components
			 ,'Contact_Gender'=>$contact->data['Contact Gender']
			 ,'Contact_Title'=>$contact->data['Contact Title']
			 ,'Contact_Profession'=>$contact->data['Contact Profession']
			 );
     


     $response=array('state'=>200,'action'=>'updated','msg'=>$contact->msg_updated,'xhtml_subject'=>$contact->display('card'),'updated_data'=>$updated_data);
   }else{
     $response=array('state'=>200,'action'=>'nochange','msg'=>$contact->msg_updated);
     
   }
   
 }
 
 echo json_encode($response);

}
function edit_company(){
global $editor;
 if(!isset($_REQUEST['key']) ){
    $response=array('state'=>400,'msg'=>'Error no key');
     echo json_encode($response);
	 return;
  }
 if( !isset($_REQUEST['newvalue']) ){
   $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
	 return;
 }
 if( !isset($_REQUEST['id']) or !is_numeric($_REQUEST['id'])  ){
   $company_key=$_SESSION['state']['company']['id'];
 }else
   $company_key=$_REQUEST['id'];

 $company=new Company($company_key);

 if(!$company->id){
   $response=array('state'=>400,'msg'=>_('Company not found'));
    echo json_encode($response);
	 return;
 }
  
 $translator=array(
		   'name'=>'Company Name'
		   ,'fiscal_name'=>'Company Fiscal Name'
		   ,'tax_number'=>'Company Tax Number'
		   ,'registration_number'=>'Company Registration Number'
		   

		   );
		  
  if (array_key_exists($_REQUEST['key'], $translator)) {
    $update_data=array(
		       'editor'=>$editor
		       ,$translator[$_REQUEST['key']]=>stripslashes(urldecode($_REQUEST['newvalue']))
		       );
    $company->update($update_data);
    
    if($company->error_updated){
      $response=array('state'=>200,'action'=>'error','msg'=>$company->msg_updated,'key'=>$_REQUEST['key']);
    }else{
    
      if($company->updated){
	$response=array('state'=>200,'action'=>'updated','msg'=>$company->msg_updated,'key'=>$_REQUEST['key'],'newvalue'=>$company->new_value);
      }else{
	$response=array('state'=>200,'action'=>'nochange','msg'=>$company->msg_updated,'key'=>$_REQUEST['key']);

      }

    }


  }else{
    $response=array('state'=>400,'msg'=>_('Key not in Company'));
  }
  echo json_encode($response);

}
function edit_email($data){
  global $editor;
  //  print_r($data);
  if(preg_match('/^company$/i',$data['subject']))
    $subject=new Company($data['subject_key']);
  else{
    $subject=new Contact($data['subject_key']);
   }
  
  if(!$subject->id){
    $response=array('state'=>400,'msg'=>'Subject not found');
    echo json_encode($response);
    return;
  }
  
  if(!isset($data['value']['Email'])){
    $response=array('state'=>400,'msg'=>'No email value');
    echo json_encode($response);
    return;
  }

   $editing=false;
   $creating=false;

   $msg=_('No changes');
  
   if($data['value']['Email Key']>0){
     $action='updated';
     $email=new Email('id',$data['value']['Email Key']);
     if(!$email->id){
       $response=array('state'=>400,'msg'=>'Email not found');
       echo json_encode($response);
       return;
     }
     $email->set_editor($editor);
     $email->update(array('Email'=>$data['value']['Email']));
     if($email->error_updated){
      $response=array('state'=>200,'action'=>'error','msg'=>$email->msg_updated);
       echo json_encode($response);
       return;
     }

     if($email->updated)
       $msg=_('Email updated');

     $update_data=array(
			'Email Key'=>$data['value']['Email Key']
			,'Email Description'=>$data['value']['Email Description']
			,'Email Is Main'=>$data['value']['Email Is Main']
			,'Email Contact Name'=>$data['value']['Email Contact Name']
			);
     $subject->add_email($update_data);
     if($subject->updated)
       $msg=_('Email updated');
     $email->set_scope($data['subject'],$data['subject_key']);

   }else{
     $action='created';
       $update_data=array(
			'Email'=>$data['value']['Email']
			,'Email Description'=>$data['value']['Email Description']
			,'Email Is Main'=>$data['value']['Email Is Main']
			,'Email Contact Name'=>$data['value']['Email Contact Name']
			);
       $subject->add_email($update_data,'if found error');
       if($subject->error){
	 $response=array('state'=>200,'action'=>'error','msg'=>$subject->msg_updated);
	 echo json_encode($response);
	 return;
       }
       if($subject->inserted_email){
	 $email=new Email ($subject->inserted_email);
	 $email->set_scope($data['subject'],$data['subject_key']);
	 $msg=_("Email created");
       }else{
	 $response=array('state'=>200,'action'=>'nochange','msg'=>$subject->msg_updated);
	 echo json_encode($response);
	 return;
       }
   }
  $updated_email_data=array(
			    'Email'=>$email->data['Email']
			    ,'Email_Description'=>$email->data['Email Description']
			    ,'Email_Contact_Name'=> $email->data['Email Contact Name']
			    ,'Email_Is_Main'=> $email->data['Email Is Main']
			    );
  $subject->reread();
  $response=array(
		  'state'=>200
		  ,'action'=>$action
		  ,'msg'=>$msg
		  ,'email_key'=>$email->id
		  ,'updated_data'=>$updated_email_data
		  ,'xhtml_subject'=>$subject->display('card')
		  ,'main_email_key'=>$subject->get_main_email_key()
		  );
    
   echo json_encode($response);

}
function edit_telecom($data){
  global $editor;
  
  if(preg_match('/^company$/i',$data['subject']))
    $subject=new Company($data['subject_key']);
  else{
    $subject=new Contact($data['subject_key']);
   }
  
  if(!$subject->id){
    $response=array('state'=>400,'msg'=>'Subject not found');
    echo json_encode($response);
    return;
  }
  
  

   $editing=false;
   $creating=false;

   $msg=_('No changes');
  
   if($data['value']['Telecom Key']>0){
     $action='updated';
     $telecom=new Telecom('id',$data['value']['Telecom Key']);
     if(!$telecom->id){
       $response=array('state'=>400,'msg'=>'Telecom not found');
       echo json_encode($response);
       return;
     }
     $telecom->set_editor($editor);
     $telecom->update_number($data['value']['Telecom']);
     if($telecom->error_updated){
      $response=array('state'=>200,'action'=>'error','msg'=>$telecom->msg_updated);
       echo json_encode($response);
       return;
     }

     if($telecom->updated)
       $msg=_('Telecom updated');

     $update_data=array(
			'Telecom Key'=>$data['value']['Telecom Key']
			,'Telecom Is Main'=>$data['value']['Telecom Is Main']
			,'Telecom Type'=>$data['value']['Telecom Type']
			);
     $subject->add_tel($update_data);
     if($subject->updated)
       $msg=_('Telecom updated');
     $telecom->set_scope($data['subject'],$data['subject_key']);

   }else{
     $action='created';
       $update_data=array(
			'Telecom'=>$data['value']['Telecom']
			,'Telecom Is Main'=>$data['value']['Telecom Is Main']
            ,'Telecom Type'=>$data['value']['Telecom Type']
			);
       $subject->add_telecom($update_data,'if found error');
       if($subject->error){
	 $response=array('state'=>200,'action'=>'error','msg'=>$subject->msg_updated);
	 echo json_encode($response);
	 return;
       }
       if($subject->inserted_telecom){
	 $telecom=new Telecom ($subject->inserted_telecom);
	 $telecom->set_scope($data['subject'],$data['subject_key']);
	 $msg=_("Telecom created");
       }else{
	 $response=array('state'=>200,'action'=>'nochange','msg'=>$subject->msg_updated);
	 echo json_encode($response);
	 return;
       }
   }
  $updated_telecom_data=array(
			      'Telecom'=>$telecom->display()
			      ,'Telecom_Is_Main'=> $telecom->data['Telecom Is Main']
			      ,'Telecom Type'=>$data['value']['Telecom Type']
			    );
  $subject->reread();
  $response=array(
		  'state'=>200
		  ,'action'=>$action
		  ,'msg'=>$msg
		  ,'telecom_key'=>$telecom->id
		  ,'updated_data'=>$updated_telecom_data
		  ,'xhtml_subject'=>$subject->display('card')
		  ,'main_telecom_key'=>$subject->get_main_telecom_key()
		  );
    
   echo json_encode($response);

}
function new_address(){
global $editor;

if( !isset($_REQUEST['value']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
   
   $tmp=preg_replace('/\\\"/','"',$_REQUEST['value']);
   $tmp=preg_replace('/\\\\\"/','"',$tmp);
   
   $raw_data=json_decode($tmp, true);

   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }


   if( !isset($_REQUEST['subject'])  
       or !is_numeric($_REQUEST['subject_key'])
       or $_REQUEST['subject_key']<=0
       or !preg_match('/^company|contact$/i',$_REQUEST['subject'])
       
       ){
     $response=array('state'=>400,'msg'=>'Error wrong subject/subject key');
      echo json_encode($response);
    return;
   }
 
   $subject=$_REQUEST['subject'];
   $subject_key=$_REQUEST['subject_key'];

   switch($subject){
   case('Company'):
     $subject_object=new Company($subject_key);

     break;
   default:
       
     $response=array('state'=>400,'msg'=>'Error wrong subject/subject key (2)');
     echo json_encode($response);
     return;

   }
   
 $translator=array(
		     'country_code'=>'Address Country Code'
		     ,'country_d1'=>'Address Country Primary Division'
		     ,'country_d2'=>'Address Country Secondary Division'
		     ,'town'=>'Address Town'
		     ,'town_d1'=>'Address Town Primary Division'
		     ,'town_d2'=>'Address Town Secondary Division'
		     ,'postal_code'=>'Address Postal Code'
		     ,'street'=>'Street Data'
		     ,'internal'=>'Address Internal'
		     ,'building'=>'Address Building'
		     ,'type'=>'Address Type'
		     ,'function'=>'Address Function'
		     ,'description'=>'Address Description'
		     
		   );
 
 
   $data=array('editor'=>$editor);
   foreach($raw_data as $key=>$value){
     if (array_key_exists($key, $translator)) {
       $data[$translator[$key]]=$value;
     }
   }
   

   
   $subject_object->add_address($data);
   if($subject_object->added_address_key){
     $contact=new Contact('create anonymous');
     $contact->add_address(array(
				 'Address Key'=>$subject_object->added_address_key
				 ,'Address Type'=>$data['Address Type']
				 ,'Address Function'=>$data['Address Function']
			       ));
     
     $address=new Address($subject_object->added_address_key);
     
     
     $address->set_scope($subject,$subject_key);
     
     $updated_address_data=array(
				 'country'=>$address->data['Address Country Name']
				 ,'country_code'=>$address->data['Address Country Code']
				 ,'country_d1'=> $address->data['Address Country Primary Division']
				 ,'country_d2'=> $address->data['Address Country Secondary Division']
				 ,'town'=> $address->data['Address Town']
				 ,'postal_code'=> $address->data['Address Postal Code']
				 ,'town_d1'=> $address->data['Address Town Primary Division']
				 ,'town_d2'=> $address->data['Address Town Secondary Division']
				 ,'fuzzy'=> $address->data['Address Fuzzy']
				 ,'street'=> $address->display('street')
				 ,'building'=>  $address->data['Address Building']
				 ,'internal'=> $address->data['Address Internal']
				 ,'type'=>$address->data['Address Type']
				 ,'function'=>$address->data['Address Function']
				 ,'description'=>$address->data['Address Description']
				   );
       
     $response=array(
		     'state'=>200
		     ,'action'=>'created'
		     ,'msg'=>$subject_object->msg_updated
		     ,'updated_data'=>$updated_address_data
		     ,'xhtml_address'=>$address->display('xhtml')
		     ,'address_key'=>$address->id
		     );
  echo json_encode($response);
     return;
   
   }else{
     $response=array('state'=>200,'action'=>'nochange','msg'=>_('Address already in company'));
     echo json_encode($response);
     return;
   }

}
function edit_address_type(){
global $editor;

 if( !isset($_REQUEST['value']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
   
   $tmp=preg_replace('/\\\"/','"',$_REQUEST['value']);
   $tmp=preg_replace('/\\\\\"/','"',$tmp);
   //$tmp=$_REQUEST['value'];
   $raw_data=json_decode($tmp, true);
   //   print "$tmp";
   // print_r($raw_data);

   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }
   if( !isset($_REQUEST['id'])  or !is_numeric($_REQUEST['id']) or $_REQUEST['id']<=0  ){
     $response=array('state'=>400,'msg'=>'Error wrong id');
     echo json_encode($response);
    return;
   }



   if( !isset($_REQUEST['subject'])  
       or !is_numeric($_REQUEST['subject_key'])
       or $_REQUEST['subject_key']<=0
       or !preg_match('/^company|contact$/i',$_REQUEST['subject'])
       
       ){
     $response=array('state'=>400,'msg'=>'Error wrong subject/subject key');
      echo json_encode($response);
    return;
   }
   
   $subject=$_REQUEST['subject'];
   $subject_key=$_REQUEST['subject_key'];


   $address=new Address('id',$_REQUEST['id']);

   if(!$address->id){
      $response=array('state'=>400,'msg'=>'Address not found');
      echo json_encode($response);
      return;
   }
   $address->set_editor($editor);
   $address->set_scope($subject,$subject_key);
   $address->update_metadata(
			     array('Type'=>$raw_data)
			     );


   $updated_data=array();
   foreach($address->get('Type') as $type)
     $updated_data[]=$type;
    
   if($address->updated){
     $response=array(
		     'state'=>200
		     ,'action'=>'updated'
		     ,'msg'=>$address->msg_updated
		     ,'key'=>''
		     ,'updated_data'=>$updated_data
		     );
   }else{
     if($address->error_updated)
       $response=array('state'=>200,'action'=>'error','msg'=>$company->msg_updated,'key'=>'');
     else
       $response=array('state'=>200,'action'=>'nochange','msg'=>$address->msg_updated,'key'=>'');
     
   }

    
   echo json_encode($response);
}
function edit_address(){
global $editor;

  if( !isset($_REQUEST['value']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
   
   $tmp=preg_replace('/\\\"/','"',$_REQUEST['value']);
   $tmp=preg_replace('/\\\\\"/','"',$tmp);
   //$tmp=$_REQUEST['value'];
   $raw_data=json_decode($tmp, true);
   //   print "$tmp";
   // print_r($raw_data);

   if(!is_array($raw_data)){
     $response=array('state'=>400,'msg'=>'Wrong value');
     echo json_encode($response);
     return;
   }
   if( !isset($_REQUEST['id'])  or !is_numeric($_REQUEST['id']) or $_REQUEST['id']<=0  ){
     $response=array('state'=>400,'msg'=>'Error wrong id');
     echo json_encode($response);
    return;
   }



   if( !isset($_REQUEST['subject'])  
       or !is_numeric($_REQUEST['subject_key'])
       or $_REQUEST['subject_key']<=0
       or !preg_match('/^company|contact$/i',$_REQUEST['subject'])
       
       ){
     $response=array('state'=>400,'msg'=>'Error wrong subject/subject key');
      echo json_encode($response);
    return;
   }
   $subject=$_REQUEST['subject'];
   $subject_key=$_REQUEST['subject_key'];


   $address=new Address('id',$_REQUEST['id']);

   if(!$address->id){
      $response=array('state'=>400,'msg'=>'Address not found');
      echo json_encode($response);
      return;
   }
   $address->set_editor($editor);
  


   $translator=array(
		     'country_code'=>'Address Country Code'
		     ,'country_d1'=>'Address Country Primary Division'
		     ,'country_d2'=>'Address Country Secondary Division'
		     ,'town'=>'Address Town'
		     ,'town_d1'=>'Address Town Primary Division'
		     ,'town_d2'=>'Address Town Secondary Division'
		     ,'postal_code'=>'Address Postal Code'
		     ,'street'=>'Street Data'
		     ,'internal'=>'Address Internal'
		     ,'building'=>'Address Building');
   

   $update_data=array('editor'=>$editor);
   foreach($raw_data as $key=>$value){
     if (array_key_exists($key, $translator)) {
       $update_data[$translator[$key]]=$value;
     }
   }
   
   $address->find("in $subject $subject_key");
   if($address->found_in){
     $msg=_('Address already associated with contact');
     $response=array('state'=>200,'action'=>'error','msg'=>$msg,'key'=>'');
     echo json_encode($response);
     return;
   }
   $address->update($update_data,'cascade');
  
    
   if($address->updated){
     $updated_address_data=array(
				 'country'=>$address->data['Address Country Name']
				 ,'country_code'=>$address->data['Address Country Code']
				 ,'country_d1'=> $address->data['Address Country Primary Division']
				 ,'country_d2'=> $address->data['Address Country Secondary Division']
				 ,'town'=> $address->data['Address Town']
				 ,'postal_code'=> $address->data['Address Postal Code']
				 ,'town_d1'=> $address->data['Address Town Primary Division']
				 ,'town_d2'=> $address->data['Address Town Secondary Division']
				 ,'fuzzy'=> $address->data['Address Fuzzy']
				 ,'street'=> $address->display('street')
				 ,'building'=>  $address->data['Address Building']
				 ,'internal'=> $address->data['Address Internal']
				 ,'description'=>$address->data['Address Description']
				 
				 );
     $response=array('state'=>200,'action'=>'updated','msg'=>$address->msg_updated,'key'=>'','updated_data'=>$updated_address_data,'xhtml_address'=>$address->display('xhtml'));
   }else{
     if($address->error_updated)
       $response=array('state'=>200,'action'=>'error','msg'=>$company->msg_updated,'key'=>$translator[$_REQUEST['key']]);
     else
       $response=array('state'=>200,'action'=>'nochange','msg'=>$address->msg_updated,'key'=>'');
     
   }

    
   echo json_encode($response);
    
}
function delete_email(){
global $editor;

  if( !isset($_REQUEST['value']) or !is_numeric($_REQUEST['value']) ){
    $response=array('state'=>400,'msg'=>'Error no value');
    echo json_encode($response);
    return;
   }
   
 
   if( !isset($_REQUEST['id'])  or !is_numeric($_REQUEST['id']) or $_REQUEST['id']<=0  ){
     $response=array('state'=>400,'msg'=>'Error wrong id');
     echo json_encode($response);
    return;
   }



   if( !isset($_REQUEST['subject'])  
       or !is_numeric($_REQUEST['subject_key'])
       or $_REQUEST['subject_key']<=0       or !preg_match('/^company|contact$/i',$_REQUEST['subject'])
       
       ){
     $response=array('state'=>400,'msg'=>'Error wrong subject/subject key');
      echo json_encode($response);
    return;
   }
   $subject_type=$_REQUEST['subject'];
   $subject_key=$_REQUEST['subject_key'];

   if(preg_match('/^company$/i',$subject_type))
     $subject=new Company($subject_key);
   else{
     $subject=new Contact($subject_key);
   }
   
   
   if(!$subject->id){
     $response=array('state'=>400,'msg'=>'Subject not found');
     echo json_encode($response);
     return;
   }
   
   
   
   $email_key=$_REQUEST['value'];

  

   $subject->remove_email($email_key);
   
   if($subject->updated){
     $action='deleted';
     $msg=_('Email deleted');
     $subject->reread();
   }else{
     $action='nochage';
     $msg=_('Email could not be deleted');
   }
  
   

   $response=array('state'=>200,'action'=>$action,'msg'=>$msg,'email_key'=>$email_key,'xhtml_subject'=>$subject->display('card'),'main_email_key'=>$subject->get_main_email_key());
     
    
   echo json_encode($response);
}
function edit_company2(){
  $company=new Company($_REQUEST['id']);
   $company->update($_REQUEST['key'],stripslashes(urldecode($_REQUEST['newvalue'])),stripslashes(urldecode($_REQUEST['oldvalue'])));
     
   if($company->updated){
     $response= array('state'=>200,'newvalue'=>$company->newvalue,'key'=>$_REQUEST['key']);
	  
   }else{
     $response= array('state'=>400,'msg'=>$company->msg,'key'=>$_REQUEST['key']);
   }
   echo json_encode($response);  
}
function new_company($data){
  Timer::timing_milestone('begin');


  $company=new Company('find create',$data['values']);
  if($company->new){
    $response= array('state'=>200,'action'=>'created','company_key'=>$company->id);
  }else{
    if($company->found)
      $response= array('state'=>400,'action'=>'found','company_key'=>$company->found_key);
    else
      $response= array('state'=>400,'action'=>'error','company_key'=>0,'msg'=>$company->msg);
  }
 
  //Timer::dump_profile();

  echo json_encode($response);  

}

 

function edit_customer() {
    $customer=new customer($_REQUEST['customer_key']);
    global $editor;
    $customer->editor=$editor;
  
    $key=$_REQUEST['key'];
    $key_dic=array(
    'name'=>'Customer Name'
   ,'email'=>'Customer Email'
			 ,'telephone'=>'Customer Main Plain Telephone'
			 ,'contact_name'=>'Email'
			 ,"address"=>'Address'
			 ,"town"=>'Main Address Town'
			 ,"postcode"=>'Main Address Town'
			 ,"region"=>'Main Address Town'
			 ,"country"=>'Main Address Country'
			 ,"ship_address"=>'Main Ship To'
			 ,"ship_town"=>'Main Ship To Town'
			 ,"ship_postcode"=>'Main Ship To Postal Code'
			 ,"ship_region"=>'Main Ship To Country Region'
			 ,"ship_country"=>'Main Ship To Country'
    
    );
    if(array_key_exists($_REQUEST['key'],$key_dic))
       $key=$key_dic[$_REQUEST['key']];
    
        
    $customer->update(array($key=>stripslashes(urldecode($_REQUEST['newvalue']))));

    if ($customer->updated) {
        $response= array('state'=>200,'newvalue'=>$customer->new_value,'key'=>$_REQUEST['key']);

    } else {
        $response= array('state'=>400,'msg'=>$customer->msg,'key'=>$_REQUEST['key']);
    }
    echo json_encode($response);

}


function list_customers(){


global $myconf;

  $conf=$_SESSION['state']['customers']['table'];
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

   if(isset( $_REQUEST['store_id'])    ){
     $store=$_REQUEST['store_id'];
     $_SESSION['state']['customers']['store']=$store;
   }else
     $store=$_SESSION['state']['customers']['store'];


   $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
   $_SESSION['state']['customers']['table']=array('order'=>$order,'order_dir'=>$order_direction,'nr'=>$number_results,'sf'=>$start_from,'where'=>$where,'f_field'=>$f_field,'f_value'=>$f_value);
   $filter_msg='';
   $wheref='';
   
   
   if(is_numeric($store)){
     $where.=sprintf(' and `Customer Store Key`=%d ',$store);
   }
   


   
   
  if(($f_field=='customer name'     )  and $f_value!=''){
    $wheref="  and  `Customer Name` like '%".addslashes($f_value)."%'";
  }elseif(($f_field=='postcode'     )  and $f_value!=''){
    $wheref="  and  `Customer Main Address Postal Code` like '%".addslashes($f_value)."%'";
    
    
    
  }else if($f_field=='id'  )
     $wheref.=" and  `Customer ID` like '".addslashes(preg_replace('/\s*|\,|\./','',$f_value))."%' ";
  else if($f_field=='maxdesde' and is_numeric($f_value) )
    $wheref.=" and  (TO_DAYS(NOW())-TO_DAYS(`Customer Last Order Date`))<=".$f_value."    ";
  else if($f_field=='mindesde' and is_numeric($f_value) )
    $wheref.=" and  (TO_DAYS(NOW())-TO_DAYS(`Customer Last Order Date`))>=".$f_value."    ";
  else if($f_field=='max' and is_numeric($f_value) )
    $wheref.=" and  `Customer Orders`<=".$f_value."    ";
  else if($f_field=='min' and is_numeric($f_value) )
    $wheref.=" and  `Customer Orders`>=".$f_value."    ";
  else if($f_field=='maxvalue' and is_numeric($f_value) )
    $wheref.=" and  `Customer Net Balance`<=".$f_value."    ";
  else if($f_field=='minvalue' and is_numeric($f_value) )
    $wheref.=" and  `Customer Net Balance`>=".$f_value."    ";






   $sql="select count(*) as total from `Customer Dimension`  $where $wheref";

   $res=mysql_query($sql);
     if($row=mysql_fetch_array($res, MYSQL_ASSOC)) {

     $total=$row['total'];
   }if($wheref!=''){
     $sql="select count(*) as total_without_filters from `Customer Dimension`  $where ";
     $res=mysql_query($sql);
     if($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
    
       $total_records=$row['total_without_filters'];
       $filtered=$row['total_without_filters']-$total;
     }

   }else{
     $filtered=0;
     $filter_total=0;
     $total_records=$total;
   }
    mysql_free_result($res);

   $rtext=$total_records." ".ngettext('identified customers','identified customers',$total_records);
   if($total_records>$number_results)
     $rtext.=sprintf(" <span class='rtext_rpp'>(%d%s)</span>",$number_results,_('rpp'));

   if($total==0 and $filtered>0){
     switch($f_field){
     case('customer name'):
       $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._("There isn't any customer like")." <b>$f_value</b> ";
       break;
     }
   }
   elseif($filtered>0){
     switch($f_field){
     case('customer name'):
       $filter_msg='<img style="vertical-align:bottom" src="art/icons/exclamation.png"/>'._('Showing')." $total "._('customers with name like')." <b>".$f_value."*</b> <span onclick=\"remove_filter($tableid)\" id='remove_filter$tableid' class='remove_filter'>"._('Show All')."</span>";
       break;
     }
   }else
      $filter_msg='';
   




   $_order=$order;
   $_dir=$order_direction;
   // if($order=='location'){
//      if($order_direction=='desc')
//        $order='country_code desc ,town desc';
//      else
//        $order='country_code,town';
//      $order_direction='';
//    }

//     if($order=='total'){
//       $order='supertotal';
//    }
    

   if($order=='name')
     $order='`Customer File As`';
   elseif($order=='id')
     $order='`Customer ID`';
   elseif($order=='location')
     $order='`Customer Main Location`';
   elseif($order=='orders')
     $order='`Customer Orders`';
   elseif($order=='email')
     $order='`Customer Email`';
   elseif($order=='telephone')
     $order='`Customer Main Telehone`';
   elseif($order=='last_order')
     $order='`Customer Last Order Date`';
   elseif($order=='contact_name')
     $order='`Customer Main Contact Name`';
   elseif($order=='address')
     $order='`Customer Main Location`';
   elseif($order=='town')
     $order='`Customer Main Address Town`';
   elseif($order=='postcode')
     $order='`Customer Main Address Postal Code`';
   elseif($order=='region')
     $order='`Customer Main Address Country First Division`';
   elseif($order=='country')
     $order='`Customer Main Address Country`';
   //  elseif($order=='ship_address')
   //  $order='`customer main ship to header`';
   elseif($order=='ship_town')
     $order='`Customer Main Ship To Town`';
   elseif($order=='ship_postcode')
     $order='`Customer Main Ship To Postal Code`';
   elseif($order=='ship_region')
     $order='`Customer Main Ship To Country Region`';
   elseif($order=='ship_country')
     $order='`Customer Main Ship To Country`';
   elseif($order=='net_balance')
     $order='`Customer Net Balance`';
   elseif($order=='balance')
     $order='`Customer Outstanding Net Balance`';
   elseif($order=='total_profit')
     $order='`Customer Profit`';
   elseif($order=='total_payments')
     $order='`Customer Net Payments`';
   elseif($order=='top_profits')
     $order='`Customer Profits Top Percentage`';
   elseif($order=='top_balance')
     $order='`Customer Balance Top Percentage`';
   elseif($order=='top_orders')
     $order='``Customer Orders Top Percentage`';
   elseif($order=='top_invoices')
     $order='``Customer Invoices Top Percentage`';
    elseif($order=='total_refunds')
     $order='`Customer Total Refunds`';
    
  elseif($order=='activity')
     $order='`Customer Type by Activity`';

   $sql="select   *,`Customer Net Refunds`+`Customer Tax Refunds` as `Customer Total Refunds`  from `Customer Dimension`  $where $wheref  order by $order $order_direction limit $start_from,$number_results";
   //   print $sql;
   $adata=array();
  
  
  
  $result=mysql_query($sql);
  while($data=mysql_fetch_array($result, MYSQL_ASSOC)){




   
    $adata[]=array(
		   
		   'customer_key'=>$data['Customer Key'],
		   'name'=>$data['Customer Name'],
		  
		   
		   'email'=>$data['Customer Main Plain Email'],
		   'telephone'=>$data['Customer Main Telephone'],
		   
		   'contact_name'=>$data['Customer Main Contact Name'],
		   'address'=>$data['Customer Main Location'],
		   'town'=>$data['Customer Main Address Town'],
		   'postcode'=>$data['Customer Main Address Postal Code'],
		   'region'=>$data['Customer Main Address Country First Division'],
		   'country'=>$data['Customer Main Address Country'],
		
		   'ship_town'=>$data['Customer Main Ship To Town'],
		   'ship_postcode'>$data['Customer Main Ship To Postal Code'],
		   'ship_region'=>$data['Customer Main Ship To Country Region'],
		   'ship_country'=>$data['Customer Main Ship To Country'],
		 
		   'go'=>sprintf("<a href='edit_customer.php?id=%d'><img src='art/icons/page_go.png' alt='go'></a>",$data['Customer Key'])

		   );
  }
mysql_free_result($result);




  $response=array('resultset'=>
		   array('state'=>200,
			 'data'=>$adata,
			 'rtext'=>$rtext,
			 'sort_key'=>$_order,
			 'sort_dir'=>$_dir,
			 'tableid'=>$tableid,
			 'filter_msg'=>$filter_msg,
			 'total_records'=>$total,
			 'records_offset'=>$start_from,

			 'records_perpage'=>$number_results,
			 'records_order'=>$order,
			 'records_order_dir'=>$order_dir,
			 'filtered'=>$filtered
			 )
		   );
   echo json_encode($response);
}

?>