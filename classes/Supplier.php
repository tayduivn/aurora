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
     $this->db =MDB2::singleton();
     

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
      $sql=sprintf("select * from `Vendor Dimension` where `Vendor Key`=%d",$id);
    else{
      if($id=='')
	$id=_('Unknown Supplier');
      $sql=sprintf("select * from `Vendor Dimension` where `Vendor Code`=%s",prepare_mysql($id));
    }

    $result =& $this->db->query($sql);
    if($row=$result->fetchRow()){
      $this->data=$row;
      $this->id=$row['vendor key'];
    }
     
  }

   function get($key){
    $key=strtolower($key);
    if(isset($this->data[$key]))
      return $this->data[$key];
   
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

    

    $sql=sprintf("insert into `Vendor Dimension` (`Vendor Code`,`Vendor Name`,`Company Key`,`Vendor Main Contact Key`,`Vendor Accounts Payable Contact Key`,`Vendor Sales Contact Key`) values (%s,%s,%d,%d,%d,%d)",
		 prepare_mysql($code),
		 prepare_mysql($name),
		 $company->id,
		 $contact->id,
		 $contact->id,
		 $contact->id
		 );
    //print "$sql\n";
    //exit;
    $affected=& $this->db->exec($sql);
    if (PEAR::isError($affected)) {
      if(preg_match('/^MDB2 Error: constraint violation$/',$affected->getMessage()))
	return array('ok'=>false,'msg'=>_('Error: Another supplier has the same code/id').'.');
      else
      return array('ok'=>false,'msg'=>_('Unknwon Error').'.');
    }
    $this->id = $this->db->lastInsertID();  
    $this->get_data('id',$this->id);
  
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
  

}

?>