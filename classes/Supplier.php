<?
include_once('Company.php');
include_once('Contact.php');
include_once('Telecom.php');
include_once('Email.php');
include_once('Address.php');
include_once('Name.php');

class supplier{
  var $db;
  var $data=array();
  var $items=array();

  var $id=false;


  function __construct($arg1=false,$arg2=false) {
    //   $this->db =MDB2::singleton();
     

     if(is_numeric($arg1)){
       $this->get_data('id',$arg1);
       return ;
     }
     if(preg_match('/create|new/i',$arg1)){
       $this->create($arg2);
       return;
     }       
     $this->get_data($arg1,$arg2);
     
 }


  function get_data($tipo,$id){
    if($tipo=='id')
      $sql=sprintf("select * from `Supplier Dimension` where `Supplier Key`=%d",$id);
    elseif ($tipo=='code'){
      if($id=='')
	$id=_('Unknown Supplier');
      
      $sql=sprintf("select * from `Supplier Dimension` where `Supplier Code`=%s  and `Supplier Most Recent`='Yes'",prepare_mysql($id));
      
    }

    
    
    $result=mysql_query($sql);
    if($this->data=mysql_fetch_array($result, MYSQL_ASSOC)   )
      $this->id=$this->data['Supplier Key'];
    
     
  }

   function get($key){

     if(array_key_exists($key,$this->data))
       return $this->data[$key];
     
     $_key=preg_replace('/^Supplier /','',$key);
     if(isset($this->data[$_key]))
       return $this->data[$key];

     $_key=ucfirst($key);
     if(isset($this->data[$_key]))
       return $this->data[$_key];
     print "Error $key not found in get from address\n";
     return false;

  }

  function create($data){
    // print_r($data);
    
    if(!is_array($data))
      $data=array('name'=>_('Unknown Supplier'));

    if($data['name']!='')
      $name=$data['name'];
    else
      $name=_('Unknown Supplier');
    if(!isset($data['code']) or $data['code']=='')
      $_code=$this->create_code($name);
    else
      $_code=$data['code'];
    
    $code=$this->check_code($_code);

    if(!isset($data['supplier id']) or !is_valid_id($data['supplier id'])  )
      $data['supplier id']=$this->new_id();

    if(isset($data['contact_name']))
      $data_contact=array('name'=>$data['contact_name']);
    elseif(isset($data['contact_name_data']))
      $data_contact=array('name_data'=>$data['contact_name_data']);
    else
      $data_contact=array(array());


    if(isset($data['address_data']))
      $data_contact['address_data']=$data['address_data'];

    $contact=new contact('new',$data_contact);
   
    $company=new company('new',
			 array('name'=>$name,'contact key'=>$contact->id)
			 );


    $most_recent='Yes';
    $from=date("Y-m-d H:i:s");
    $to=date("Y-m-d H:i:s");
    $most_recent_key='';

    if(isset($data['most_recent']) and preg_match('/no/i',$data['most_recent']))
      $most_recent='No';
    
    if(isset($data['from']))
      $from=$data['from'];
    if(isset($data['to']))
      $to=$data['to'];
    
    if(isset($data['most_recent_key']) and is_numeric($data['most_recent_key'])  and $data['most_recent_key']>0  )
      $most_recent_key=$data['most_recent_key'];

    $sql=sprintf("insert into `Supplier Dimension` (`Supplier Code`,`Supplier Name`,`Supplier Company Key`,`Supplier Main Contact Key`,`Supplier Accounts Payable Contact Key`,`Supplier Sales Contact Key`,`Supplier Valid From`,`Supplier Valid To`,`Supplier Most Recent`,`Supplier Most Recent Key`,`Supplier ID`) values (%s,%s,%d,%d,%d,%d,%s,%s,%s,%s,%s)",
		 prepare_mysql($code),
		 prepare_mysql($name),
		 $company->id,
		 $contact->id,
		 $contact->id,
		 $contact->id,
		 prepare_mysql($from),
		 prepare_mysql($to),
		 prepare_mysql($most_recent),
		 prepare_mysql($most_recent_key),
		 prepare_mysql($data['supplier id'])
		 );
    //  print "$sql\n";
    //    exit;

    if(mysql_query($sql)){

      $this->id=mysql_insert_id();
      $this->get_data('id',$this->id);
      
      if($most_recent=='Yes'){
	$sql=sprintf('update `Supplier Dimension` set `Supplier Most Recent Key`=%d where `Supplier Key`=%d',$this->id,$this->id);
	mysql_query($sql);
      }
    }else{
      print "Error can not create supplier\n";exit;
    }

  }

  function load($key=''){
    switch($key){
   
    case('contacts'):
    case('contact'):
      $this->contact=new Contact($this->data['Supplier Main Contact Key']);
      if($this->contact->id){
	//$this->contact->load('telecoms');
	//$this->contact->load('contacts');
      }

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
  
function new_id(){
  $sql="select max(`Supplier ID`) as id from `Supplier Dimension`";
  $result=mysql_query($sql);
  if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
    $id=$row['id']+1;
  }else{
    $id=1;
  }  
  return $id;
}

function valid_id($id){
  if(is_numeric($id) and $id>0 and $id<9223372036854775807)
    return true;
  else
    return false;
}


}

?>