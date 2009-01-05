<?
include_once('Country.php');

class Telecom{
  var $db;
  var $data=array();
  var $id=false;

  
  function __construct($arg1=false,$arg2=false) {
     $this->db =MDB2::singleton();

     if(is_numeric($arg1)){
       $this->get_data('id',$arg1);

     }

   if(is_array($arg2) and $arg1='new'){
       $this->create($arg2);
       return;
     }
  $this->get_data($arg1,$arg2);
  }


  function get_data($tipo,$id){
   $sql=sprintf("select * from `telecom Dimension` where  `Telecom Key`=%d",$id);

   $result =& $this->db->query($sql);
    if($this->data=$result->fetchRow()){
      $this->id=$this->data['telecom key'];
      return true;
    }
    return false;

}

 function display($tipo=''){

   switch($tipo){
   default:
     $tmp=($this->data['telecom country code']!=''?'+'.$this->data['telecom country code'].' ':'').($this->data['telecom area code']!=''?$this->data['telecom area code'].' ':'').$this->get('spaced_number').($this->data['telecom extension']!=''?' '._('ext').' '.$this->data['telecom extension']:'');
     return $tmp;
   }
 }
 

 function get($tipo='')
 {

    $key=strtolower($tipo);
    if(isset($this->data[$key]))
      return $this->data[$key];

   switch($tipo){
   case('spaced_number'):
     return _trim(strrev(chunk_split(strrev($this->data['telecom number']),4," ")));
     break;
   default:
     return false;
   }
   
 }


 function create($data){
   $country=new country('code','UNK');
   $this->data['country key']=$country->id;
   $this->data['telecom original number']='';
   $this->data['telecom original extension']='';
   $this->data['telecom original area code']='';
   $this->data['telecom original country code']='';
   $this->data['telecom original type']='';
      $this->data['telecom original restricted country key']='';

     $this->data['telecom number']='';
   $this->data['telecom extension']='';
   $this->data['telecom area code']='';
   $this->data['telecom country code']='';
   $this->data['telecom type']='';
      $this->data['telecom restricted country key']='';

   
   
   if(isset($data['telecom number']))
     $this->data['telecom original number']=$data['telecom number'];
   if(isset($data['telecom extension']))
     $this->data['telecom original extension']=$data['telecom extension'];
   if(isset($data['telecom area code']))
     $this->data['telecom original area code']=$data['telecom area code'];
   if(isset($data['telecom country code']))
     $this->data['telecom original country code']=$data['telecom country code'];
   if(isset($data['telecom type']))
     $this->data['telecom original type']=$data['telecom type'];
    if(isset($data['telecom restricted country key']))
      $this->data['telecom original restricted country key']=$data['telecom restricted country key'];
    if(isset($data['country key']))
      $this->data['country key']=$data['country key'];
    

    $country=new country('id', $this->data['country key']);
    if($country->id)
      $this->data['telecom country code']=$country->get('Country Telephone Code');
    //    print_r( $data);
    // print_r( $this->data);
    $this->parse_telecom();
    
    //  print_r( $this->data);

    if($this->data['telecom number']==''){
      $this->new=false;
      return false;
    }
    
    $sql=sprintf("insert into `Telecom Dimension` (`Telecom Type`,`Telecom Country Code`,`Telecom Area Code`,`Telecom Number`,`Telecom Extension`,`Telecom Restricted Country Key`) values (%s,%s,%s,%s,%s,%s)",
		 prepare_mysql($this->data['telecom type']),
		 prepare_mysql($this->data['telecom country code']),
		 prepare_mysql($this->data['telecom area code']),
		 prepare_mysql($this->data['telecom number']),
		 prepare_mysql($this->data['telecom extension']),
		 prepare_mysql($this->data['telecom restricted country key'])
		 );
    print "$sql\n";
    $affected=& $this->db->exec($sql);
    if (PEAR::isError($affected)) {
       $this->new=false;
      return false;
    }
    $this->id = $this->db->lastInsertID();
    $this->data['telecom key']=$this->id;
     $this->new=true;
     return true;

 }


 
 function parse_telecom(){
   
   $raw_tel=$this->get('telecom original number');
   
   if($this->data['telecom country code']=='')
     $this->data['telecom country code']=preg_replace('/[^0-9]/','',$this->get('telecom original country code'));
  
     

   $this->data['telecom area code']=preg_replace('/[^0-9]/','',$this->get('telecom original area code'));

   // fisrt try to see if it has an extension;
   $tel_ext=preg_split('/ext/i',$raw_tel);

  if(count($tel_ext)==2){
    $this->data['telecom extension']=preg_replace('/[^0-9]/','',$tel_ext[1]);
    $this->data['telecom number']=preg_replace('/[^0-9]/','',$tel_ext[0]);

  }else{
  
    $this->data['telecom extension']=preg_replace('/[^0-9]/','',$this->get('telecom original extension'));
    $this->data['telecom number']=preg_replace('/[^0-9]/','',$this->get('telecom original number'));

  }
  
  if($this->data['telecom country code']!=''){
    $regex_icode="/^0{0,2}".$this->data['telecom country code']."/";
    $this->data['telecom number']=preg_replace($regex_icode,'',$this->data['telecom number']);
  }
  
  // print_r($this->data);
  // country expcific
  $country_id=$this->get('country key');
  $this->data['is_mobile']='';
  switch($country_id){
     
  case(30)://UK
    if(preg_match('/^0845/',$this->data['telecom number'])){
      $this->data['telecom restricted country key']=30;
      $this->data['telecom country code']='';
      $this->data['telecom area code']='0845';
      $this->data['telecom number']=preg_replace('/^0845/','',$this->data['telecom number']);
    }
    $number=preg_replace('/^0/','',$number);
    if(preg_match('/^7/',$number))
      $this->data['is_mobile']=1;
    else
      $this->data['is_mobile']=0;
    break;
  case(75)://Ireland
    if(preg_match('/^0?8(2|3|5|6|7|8|9)/',$number))
      $this->data['is_mobile']=1;
    else
      $this->data['is_mobile']=0;
    break;
  case(47)://Spain
  case(165)://France
    if(preg_match('/^0?6/',$number))
    $this->data['is_mobile']=1;
    else
      $this->data['is_mobile']=0;
    break;
  }
  
  if($this->data['is_mobile']==1)
    $this->data['telecom type']='Mobile';
  else if($this->data['telecom original type']=='Mobile' and $this->data['is_mobile']==0)
    $this->data['telecom type']='Unknown';
  else 
    $this->data['telecom type']=$this->data['telecom original type'];
  
  
 }
 


}
?>