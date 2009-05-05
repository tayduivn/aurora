<?
/**
* This file contains the Contact Class
* @author Raul Perusquia <rulovico@gmail.com>
* @copyright Copyright (c) 2009, Kaktus 
* @version 2.0
* @package Kaktus
*/

include_once('Contact.php');
include_once('Telecom.php');
include_once('Email.php');
include_once('Address.php');
include_once('Name.php');
/**
* Company Class
*
* Class mapping <b>Company Dimension</b> table
* 
* @package Kaktus
* @subpackage Contacts 
*/
class Company{
  /**
   * Mirror of the database data
   * @var array
   */
  var $data=array();
  var $items=array();
  var $id=false;

   /**
     *  Constructor of ContactCompanu
     *
     *  Initializes the class, Search/Load or Create for the data set 
     *
     * 
     *  @param  mixed     $arg1     (optional) Could be the tag for the Search Options or the Contact Key for a simple object key search
     *  @param  mixed     $arg2     (optional) Data used to search or create the object
     *  @return void
     */
  function Company($arg1=false,$arg2=false) {

     

     if(is_numeric($arg1)){
       $this->get_data('id',$arg1);
       return ;
     }
     if(preg_match('/create|new/i',$arg1)){
       $this->create($arg2);
       return;
     }       
      $this->get_data($arg1,$arg2);
       return ;

 }


  
  function get($key,$arg1=false){
    //  print $key."xxxxxxxx";
    
    if(array_key_exists($key,$this->data))
      return $this->data[$key];

    switch($key){
    case('departments'):
      if(!isset($this->departments))
	$this->load('departments');
      return $this->departments;
      break;
    case('department'):
      if(!isset($this->departments))
	$this->load('departments');
      if(is_numeric($arg1)){
	if(isset($this->departments[$arg1]))
	  return $this->departments[$arg1];
	else
	  return false;
      }
      if(is_string($arg1)){
	foreach($this->departments as $department){
	  if($department['company department code']==$arg1)
	    return $department;
	}
	return false;
      }
      
      
    }
   
     $_key=ucfirst($key);
    if(isset($this->data[$_key]))
      return $this->data[$_key];
    print "Error $key not found in get from address\n";

    return false;

  }


  function get_data($tipo,$id){
    $sql=sprintf("select * from `Company Dimension` where `Company Key`=%d",$id);
    // print $sql;
    $result=mysql_query($sql);
    if($this->data=mysql_fetch_array($result, MYSQL_ASSOC)){
      $this->id=$this->data['Company Key'];
    }
  }

  
  function create($data){
    if(!is_array($data))
      $data=array('name'=>_('Unknown Name'));


    // print_r($data);

    $name=$data['name'];
    $file_as=$this->file_as($data['name']);
    $company_id=$this->get_id();
    
    if(!isset($data['contact key']) or !is_numeric($data['contact key'])){
      $contact=new contact($new);
    }else{
    $contact_id=$data['contact key'];
    $contact=new contact($contact_id);
    }

    
    //print_r($contact->data);
    $sql=sprintf("insert into `Company Dimension` (`Company ID`,`Company Name`,`Company File as`,`Company Main Address Key`,`Company Main XHTML Address`,`Company Main Country Key`,`Company Main Country`,`Company Main Location`,`Company Main Contact`,`Company Main Contact Key`,`Company Main Telephone`,`Company Main FAX`,`Company Main XHTML Email`,`Company Main Telephone Key`,`Company Main FAX Key`,`Company Main Email Key`) values (%d,%s,%s,%s,%s,%s,%s,%s,%s,%d,%s,%s,%s,%s,%s,%s)",
		 $company_id,
		 prepare_mysql($name),
		 prepare_mysql($file_as),
		 prepare_mysql($contact->get('Contact Main Address Key')),
		 prepare_mysql($contact->get('Contact Main XHTML Address')),
		 prepare_mysql($contact->get('Contact Main Country Key')),
		 prepare_mysql($contact->get('Contact Main Country')),
		 prepare_mysql($contact->get('Contact Main Location')),
		 prepare_mysql($contact->get('Contact Name')),
		 $contact->id,
		 prepare_mysql($contact->get('Contact Main Telephone')),
		 prepare_mysql($contact->get('Contact Main FAX')),
		 prepare_mysql($contact->get('Contact Main XHTML Email')),
		 prepare_mysql($contact->get('Contact Main Telephone Key')),
		 prepare_mysql($contact->get('Contact Main Fax Key')),
		 prepare_mysql($contact->get('Contact Main Email Key'))

		 );

     if(mysql_query($sql)){
      $this->id = mysql_insert_id();
      $this->get_data('id',$this->id);
     }else{
       print "Error, company can not be created";exit;
     }

  }

  function get_id(){
    
    $sql="select max(`Company ID`)  as company_id from `Company Dimension`";
    $result=mysql_query($sql);
    if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      if(!preg_match('/\d*/',_trim($row['company_id']),$match))
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

  function load($key=''){
    switch($key){
   
    case('contacts'):
    case('contact'):
      $this->contact=new Contact($this->data['contact_id']);
      if($this->contact->id){
	$this->contact->load('telecoms');
	$this->contact->load('contacts');
      }

    }
    
  }
  function add_page($page_data,$args='principal'){
    $url=$data['page url'];
    if(isset($data['page_type']) and preg_match('/internal/i',$data['page_type']))
      $email_type='Internal';
    else
      $email_type='External';
    $url_data=array(
		     'page description'=>'',
		     'page url'=>$url,
		     'page type'=>$email_type,
		     'page validated'=>0,
		     'page verified'=>0,
		     );
    
    if(isset($data['page description']) and $data['page description']!='')
      $url_data['page description']=$data['page description'];
    $page=new page('new',$url_data);
   if($email->new){
     
     $sql=sprintf("insert into  `Company Web Site Bridge` (`Page Key`, `Company Key`) values (%d,%d)  ",$page->id,$this->id);
     mysql_query($sql);
     if(preg_match('/principal/i',$args)){
     $sql=sprintf("update `Company Dimension` set `Company Main XHTML Page`=%s where `Contact Key`=%d",prepare_mysql($page->display('html')),$this->id);
     // print "$sql\n";
     mysql_query($sql);
     }

     $this->add_page=true;
   }else{
     $this->add_page=false;
     
   }

  }

  function add_email($email_data,$args='principal'){
    //  $emails=$this->get('emails');
    //  print_r($this->data);

    $contact=new contact($this->get('company main contact key'));
    if($contact->id){
    
      $contact->add_email($email_data,$args);
      
      if($contact->add_email){
	$this->msg['email added'];
	if(preg_match('/principal/i',$args)){
	  $sql=sprintf("update `Company Dimension` set `Company Main XHTML Email`=%s where `Company Key`=%d",prepare_mysql($contact->get('Contact Main XHTML Email')),$this->id);
	  mysql_query($sql);
	}
	
      }
    }
  }

 function add_tel($tel_data,$args='principal'){

   $tel_data['country key']=$this->get('Company main Country Key');
   $contact=new contact($this->get('company main contact key'));
   //print_r($this->data);
   if($contact->id){
   $contact->add_tel($tel_data,$args);
   
   if($contact->add_tel){
      $this->msg['telecom added'];
        if(preg_match('/principal/i',$args)){
	  $sql=sprintf("update `Company Dimension` set `Company Main Telephone`=%s where `Company Key`=%d",prepare_mysql($contact->get('Contact Main Telephone')),$this->id);
	  $this->db->exec($sql);
	  $sql=sprintf("update `Company Dimension` set `Company Main FAX`=%s where `Company Key`=%d",prepare_mysql($contact->get('Contact Main Fax')),$this->id);
	  mysql_query($sql);
	}

    }
 }else
   print "Error\n";
  }


function add_contact($data,$args='principal'){
  
  if(is_numeric($data))
    $contact=new Contact('id',$data);
  else
    $contact=new Contact('new',$data);
  
  if(!$contact->id)
    exit("can not find contact");

    $sql=sprintf("insert into  `Contact Bridge` (`Contact Key`, `Subject Key`,`Subject Type`,`Is Main`) values (%d,%d,'Company',%s,%s)  "
		   ,$contact->id
		   ,$this->id
		   ,prepare_mysql($telecom_tipo)
		   ,prepare_mysql(preg_match('/principal/i',$args)?'Yes':'No')
		   );
      mysql_query($sql);
      
      if(preg_match('/principal/i',$args)){
	$sql=sprintf("update `Contact Dimension` set `Company Main Contact`=%s and  `Company Main Contact Key`=%s,`Company Main Address Key`=%d,`Company Main XHTML Address`=%s,`Company Main Plain Address`=%s,`Company Main Country Key`=%d,`Company Main Country`=%s ,`Company Main Location`=%s,`Company Main Telephone`=%s,`Company Main Plain Telephone`=%,`Company Main Telephone Key`=%d,`Company Main FAX`=%s , `Company Main Plain FAX`=%s,`Company Main FAX Key`=%s ,`Company Main XHTML Email`=%s,`Company Main Plain Email`=%s, `Company Main Email Key`=%d  where `Company Key`=%d"
		     ,$contact->display('name')
		     ,$contact->id
		     ,$contact->data['Contact Main Address Key']
		     ,prepare_mysql($contact->data['Contact Main XHTML Address'])
		     ,prepare_mysql($contact->data['Contact Main Plain Address'])
		     ,$contact->data['Contact Main Country Key']
		     ,prepare_mysql($contact->data['Contact Main Country'])
		     ,prepare_mysql($contact->data['Contact Main Location'])
		     ,prepare_mysql($contact->data['Contact Main Telephone'])
		     ,prepare_mysql($contact->data['Contact Main Plain Telephone'])
		     ,$contact->data['Contact Main Telephone Key']
		     ,prepare_mysql($contact->data['Contact Main FAX'])
		     ,prepare_mysql($contact->data['Contact Main Plain FAX'])
		     ,$contact->data['Contact Main FAX Key']
		     ,prepare_mysql($contact->data['Contact Main XHTML Email'])
		     ,prepare_mysql($contact->data['Contact Main Plain Email'])
		     ,$contact->data['Contact Main Email Key']
		     ,$this->id
		     );
	mysql_query($sql);
	
	
      }
      


}

  function create_code($name){
    preg_replace('/[!a-z]/i','',$name);
    preg_replace('/^(the|el|la|les|los|a)\s+/i','',$name);
    preg_replace('/\s+(plc|inc|co|ltd)$/i','',$name);
    preg_split('/\s*/',$name);
    return $name;
  }

   function check_code($name){
    return $name;
  }
   function file_as($name){
    return $name;
  }

}

?>