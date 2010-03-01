<?php
require_once 'common.php';
require_once('class.Email.php');
require_once 'ar_edit_common.php';


if (!isset($_REQUEST['tipo'])) {
    $response=array('state'=>405,'resp'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
}

$tipo=$_REQUEST['tipo'];
switch ($tipo) {

case('products'):
  //case('product_manage_stock'):
  //case('edit_product'):
  //$q=$_REQUEST['q'];
  //search_products($q,$tipo,$user);
  $data=prepare_values($_REQUEST,array(
			     'q'=>array('type'=>'string')
			     ,'scope'=>array('type'=>'string')
			     ));
    $data['user']=$user;
   search_products($data);
   break;


    
case('location'):
    $q=$_REQUEST['q'];
     search_location($q,$tipo,$user);
    break;
case('customer_name'):
   search_customer_name($user);
   break;
case('customer'):
case('customers'):
   $data=prepare_values($_REQUEST,array(
			     'q'=>array('type'=>'string')
			     ,'scope'=>array('type'=>'string')
			     ));
    $data['user']=$user;
   search_customer($data);
   break;
default:
    $response=array('state'=>404,'resp'=>"Operation not found $tipo");
    echo json_encode($response);

}


function search_customer_by_parts($data){
  
  $user=$data['user'];
  $q=$data['q'];
    // $q=_trim($_REQUEST['q']);
    
  if($data['scope']=='store'){
    $stores=$_SESSION['state']['customers']['store'];
    
  }else
    $stores=join(',',$user->stores);
    
    $total_found=0;
    $emails_found=0;
    $emails_results='<table>';
    // Email serach
    if(strlen($q)>3 or preg_match('/@/',$q)){
      $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Main Plain Email` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Main Plain Email` like "%s%%"  limit 5'
		   ,$stores
		   ,addslashes($q)
		   );
      // print $sql;
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
	$result=sprintf('<tr><td><a href="customer.php?id=%d">%s</a></td><td class="aright">%s</td></tr>',$row['Customer Key'],$row['Customer Name'],$row['Customer Main Plain Email']);
	$emails_found++;
	$emails_results.=$result;
	$total_found++;
      }
      
    }
    $emails_results.='</table>';


 $names_found=0;
 $names_results='<table>';
 // Email serach
 if(strlen($q)>2){
   $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Name`  REGEXP "[[:<:]]%s"   limit 5'
		   ,$stores
		,addslashes($q)
		);
   // print $sql;
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
	$result=sprintf('<tr><td class="aright"><a href="customer.php?id=%d">%s</a></td></tr>',$row['Customer Key'],$row['Customer Name']);
	$names_found++;
	$names_results.=$result;
	$total_found++;
      }

    }
  $names_results.='</table>';



 $contacts_found=0;
 $contacts_results='<table>';
 // Email serach
 if(strlen($q)>2){
   $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Main Contact Name` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Main Contact Name` REGEXP "[[:<:]]%s"   and `Customer Type`="Company" limit 5'
		   ,$stores
		,addslashes($q)
		);
  
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
	$result=sprintf('<tr><td class="aright"><a href="customer.php?id=%d">%s <b>%s</b></a></td></tr>',$row['Customer Key'],$row['Customer Name'],$row['Customer Main Contact Name']);
	$contacts_found++;
	$contacts_results.=$result;
	$total_found++;
      }

    }
  $contacts_results.='</table>';




 $tax_numbers_found=0;
 $tax_numbers_results='<table>';
 // Email serach
 if(strlen($q)>2){

   if(is_numeric($q)){
  $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Tax Number` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Tax Number` like "%%%s%%"  limit 5'
		   ,$stores
		,$q
		);
   }else{
   $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Tax Number` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Tax Number` REGEXP "[[:<:]]%s"  limit 5'
		   ,$stores
		,addslashes($q)
		);
   }
   
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
	$result=sprintf('<tr><td class="aright"><a href="customer.php?id=%d">%s <b>%s</b></a></td></tr>',$row['Customer Key'],$row['Customer Name'],$row['Customer Tax Number']);
	$tax_numbers_found++;
	$tax_numbers_results.=$result;
	$total_found++;
      }

    }
  $tax_numbers_results.='</table>';


    
     $locations_found=0;
 $locations_results='<table>';
 // Email serach
 if(strlen($q)>1){
   $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Main Address Postal Code`,`Customer Main Location` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Main Address Postal Code` like "%s%%"  limit 5'
		   ,$stores
		,addslashes($q)
		);
   // print $sql;
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
	$result=sprintf('<tr><td class="aright"><a href="customer.php?id=%d">%s</a> %s <b>%s</b></td></tr>',$row['Customer Key'],$row['Customer Name'],$row['Customer Main Location'],$row['Customer Main Address Postal Code']);
	$locations_found++;
	$locations_results.=$result;
	$total_found++;
      }

    }
  $locations_results.='</table>';











    
    
    $data=array('results'=>$total_found
		,'emails'=>$emails_found,'emails_results'=>$emails_results
		,'names'=>$names_found,'names_results'=>$names_results
		,'locations'=>$locations_found,'locations_results'=>$locations_results
		,'contacts'=>$contacts_found,'contacts_results'=>$contacts_results
		,'tax_numbers'=>$tax_numbers_found,'tax_numbers_results'=>$tax_numbers_results
		);
    $response=array('state'=>200,'data'=>$data);
    echo json_encode($response);
}



function search_customer($data){
  
  $max_results=10;

  $user=$data['user'];
  $q=$data['q'];
    // $q=_trim($_REQUEST['q']);
    
  if($q==''){
    $response=array('state'=>200,'results'=>0,'data'=>'');
    echo json_encode($response);
    
  }


  if($data['scope']=='store'){
    $stores=$_SESSION['state']['customers']['store'];
    
  }else
    $stores=join(',',$user->stores);
    
  $candidates=array();
  $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and `Customer Name`   REGEXP "[[:<:]]%s" limit 100 ',$stores,$q);
  //print $sql;
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    if($row['Customer Name']==$q)
      $candidates[$row['Customer Key']]=110;
    else{

      $len_name=strlen($row['Customer Name']);
      $len_q=strlen($q);
      $factor=$len_q/$len_name;
      $candidates[$row['Customer Key']]=100*$factor;
    }   
  }

  $sql=sprintf('select `Subject Key`,`Email` from `Email Bridge` EB  left join `Email Dimension` E on (EB.`Email Key`=E.`Email Key`) left join `Customer Dimension` CD on (CD.`Customer Key`=`Subject Key`)  where `Customer Store Key` in (%s)  and `Subject Type`="Customer" and  `Email`  like "%s%%" limit 100 ',$stores,$q);
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    if($row['Email']==$q){
      
      $candidates[$row['Subject Key']]=120;
    }else{

      $len_name=strlen($row['Email']);
      $len_q=strlen($q);
 $factor=$len_q/$len_name;
      $candidates[$row['Subject Key']]=100*$factor;
    }   
  }
  //print_r($candidates);

  
 $sql=sprintf('select `Customer Key`,`Customer Main Address Postal Code` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Main Address Postal Code` like "%s%%"  limit 150'
		   ,$stores
		,addslashes($q)
		);
   // print $sql;
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
  
	if($row['Customer Main Address Postal Code']==$q){
      
	  $candidates[$row['Customer Key']]=50;
    }else{

      $len_name=$row['Customer Main Address Postal Code'];
      $len_q=strlen($q);
      $factor=$len_name/$len_q;
      $candidates[$row['Customer Key']]=20*$factor;
    }   

      }



 $sql=sprintf('select `Subject Key`,`Contact Name` from `Contact Bridge` EB  left join `Contact Dimension` E on (EB.`Contact Key`=E.`Contact Key`) left join `Customer Dimension` CD on (CD.`Customer Key`=`Subject Key`)  where `Customer Store Key` in (%s)  and `Subject Type`="Customer" and  `Contact Name`  REGEXP "[[:<:]]%s"  limit 100 ',$stores,$q);
 //rint $sql; 
$res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    if($row['Contact Name']==$q){
      
      $candidates[$row['Subject Key']]=120;
    }else{

      $len_name=$row['Contact Name'];
      $len_q=strlen($q);
      $factor=$len_name/$len_q;
      $candidates[$row['Subject Key']]=100*$factor;
    }   
  }

  arsort($candidates);
  $total_candidates=count($candidates);
  
  if($total_candidates==0){
    $response=array('state'=>200,'results'=>0,'data'=>'');
    echo json_encode($response);
    return;
  }
  

  $counter=0;
  $customer_keys='';

  $results=array();
 

  foreach($candidates as $key=>$val){
    $counter++;
    $customer_keys.=','.$key;
    $results[$key]='';
    if($counter>$max_results)
      break;
  }
  $customer_keys=preg_replace('/^,/','',$customer_keys);

  $sql=sprintf("select `Customer Key`,`Customer Main Contact Name`,`Customer Name`,`Customer Type`,`Customer Main Plain Email`,`Customer Main Location`,`Customer Tax Number` from `Customer Dimension` where `Customer Key` in (%s)",$customer_keys);
   $res=mysql_query($sql);


   //   $customer_card='<table>';
 while($row=mysql_fetch_array($res)){

   if($row['Customer Type']=='Company'){
     $name=$row['Customer Name'].'<br/>'.$row['Customer Main Contact Name'];
   }else{
     $name=$row['Customer Name'];

   }

   $address=$row['Customer Main Plain Email'].'<br/>'.$row['Customer Main Location'];


   $results[$row['Customer Key']]=array('key'=>sprintf('%05d',$row['Customer Key']),'name'=>$name,'address'=>$address);
  }
 //$customer_card.='</table>';

  
 $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'customer.php?id=');
  echo json_encode($response);
  
}




function search_customer_old($user){

    $q=_trim($_REQUEST['q']);
    $stores=join(',',$user->stores);
    
    if(is_numeric($q)){
        if($found_key=search_customer_id($q,$stores)){
         $url='customer.php?id='. $found_key;
        echo json_encode(array('state'=>200,'url'=>$url));
        return;
        }
            
    }
    
    $postal_code_search=false;
    $search_data=array();
    if(preg_match('/\s*(([A-Z]\d{2}[A-Z]{2})|([A-Z]\d{3}[A-Z]{2})|([A-Z]{2}\d{2}[A-Z]{2})|([A-Z]{2}\d{3}[A-Z]{2})|([A-Z]\d[A-Z]\d[A-Z]{2})|([A-Z]{2}\d[A-Z]\d[A-Z]{2})|(GIR0AA))\s*/i',$q,$match)){
        $search_data['Postal Code']=_trim($match[0]);
        $q=preg_replace('/'.$match[0].'/','',$q);
        $postal_code_search=true;
    }
    $tolkens=preg_split('/\s/',$q);
    foreach($tolkens as $key=>$tolken){
        if(Email::is_valid($tolken)){
            $search_data['Customer Email']=$tolken;
        }elseif(!$postal_code_search and is_postal_code($tolken)){
            $tolken_meaning[$key]='postal_code';
            $search_data['Postal Code']=$tolken;
            $postal_code_search=true;
        }elseif($postal_code_search){
            
        }else
        if(isset($search_data['Customer Name']))
            $search_data['Customer Name'].=' '.$tolken;
        else
            $search_data['Customer Name']=$tolken;
        }
    
    
    
   print_r($search_data);
      $_SESSION['search']=array('Type'=>'Customer','Data'=>$search_data);
        echo json_encode(array('state'=>200,'url'=>'customers_lookup.php?res=y'));
        return;
    
    

}

function search_customer_id($id,$valid_stores=false){
    if($valid_stores){
        $stores=" and `Customer Store Key` in ($valid_stores)";
    }else
        $stores='';
    
    $sql=sprintf("select `Customer Key` from `Customer Dimension` where `Customer Key`=%d %s ",$id,$stores);
    $res=mysql_query($sql);
    if($row=mysql_fetch_array($res)){
        $found=$row['Customer Key'];
    }else
        $found=false;
    return $found;    
}


function search_customer_name($user){
 $target='customer.php';
    $q=$_REQUEST['q'];
    $sql=sprintf("select `Customer Key` from `Customer Dimension` where `Customer Name`=%s ",prepare_mysql($q));
    $result=mysql_query($sql);

    $number_results=mysql_num_rows($result);
    if ($number_results==1) {
        if ($found=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $url=$target.'?id='. $found['id'];
            echo json_encode(array('state'=>200,'url'=>$url));
            return;
        }
    }else{
        
      $_SESSION['search']=array('Type'=>'Customer','Data'=>array('Customer Name'=>$q));
        echo json_encode(array('state'=>200,'url'=>'customer_lookup.php?res=y'));
        return;

    }
mysql_free_result($result);








}


function search_products($data){
$max_results=10;
 $user=$data['user'];
  $q=$data['q'];
    // $q=_trim($_REQUEST['q']);
    
  if($q==''){
    $response=array('state'=>200,'results'=>0,'data'=>'');
    echo json_encode($response);
    return;
  }


  if($data['scope']=='store'){
    $stores=$_SESSION['state']['store']['id'];
    
  }else
    $stores=join(',',$user->stores);

 
  if(!$stores){
    $response=array('state'=>200,'results'=>0,'data'=>'','mgs'=>'Store Error');
    echo json_encode($response);
    return;
  }
  $extra_q='';
  $array_q=preg_split('/\s/',$q);
  if(count($array_q>1)){
  $q=array_shift($array_q);
  $extra_q=join(' ',$array_q);
  }
  
  $found_family=false;
  
 $candidates=array();
 $sql=sprintf('select `Product Family Key`,`Product Family Code` from `Product Family Dimension` where `Product Family Store Key` in (%s) and `Product Family Code` like "%s%%" limit 100 ',$stores,addslashes($q));
 //print $sql;
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    if(strtolower($row['Product Family Code'])==strtolower($q)){
      $candidates['F '.$row['Product Family Key']]=210;
     $found_family=$row['Product Family Key'];
  
    }else{

      $len_name=strlen($row['Product Family Code']);
      $len_q=strlen($q);
      $factor=$len_q/$len_name;
      $candidates['F '.$row['Product Family Key']]=200*$factor;
    }   
  }
  //print $extra_q;
 if($found_family){
 if($extra_q){
 
  $sql=sprintf('select damlevlim256(UPPER(%s),UPPER(`Product Name`),100) as dist , `Product ID`,`Product Name` from `Product Dimension` where `Product Family Key`=%d order by damlevlim256(UPPER(%s),UPPER(`Product Name`),100)  limit 6 ',prepare_mysql($extra_q),$found_family,prepare_mysql($extra_q));
  //print $sql;
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
   
     $factor=exp(-$row['dist']*$row['dist']/(strlen($extra_q)));
      $candidates['P '.$row['Product ID']]=100*$factor;
    }   
  }
 
 
 
 
 }else{
  
  
 $sql=sprintf('select `Product ID`,`Product Code` from `Product Dimension` where `Product Store Key` in (%s) and `Product Code` like "%s%%" limit 100 ',$stores,addslashes($q));
  //print $sql;
  $res=mysql_query($sql);
  while($row=mysql_fetch_array($res)){
    if($row['Product Code']==$q)
      $candidates['P '.$row['Product ID']]=110;
    else{

      $len_name=strlen($row['Product Code']);
      $len_q=strlen($q);
      $factor=$len_q/$len_name;
      $candidates['P '.$row['Product ID']]=100*$factor;
    }   
  }
}

  
 arsort($candidates);
 // $candidates=array_reverse($candidates);
 //print_r($candidates); 
 $total_candidates=count($candidates);
  
  if($total_candidates==0){
    $response=array('state'=>200,'results'=>0,'data'=>'');
    echo json_encode($response);
    return;
  }
  

  $counter=0;
  $customer_keys='';

  $results=array();
  $family_keys='';
  $products_keys='';

  foreach($candidates as $key=>$val){
    $_key=preg_split('/ /',$key);
    if($_key[0]=='F'){
      $family_keys.=','.$_key[1];
      $results[$key]='';
    }else{
      $products_keys.=','.$_key[1];
      $results[$key]='';

    }
    
    $counter++;

    if($counter>$max_results)
      break;
  }
  $family_keys=preg_replace('/^,/','',$family_keys);
  $products_keys=preg_replace('/^,/','',$products_keys);

  if($family_keys){
    $sql=sprintf("select `Product Family Key`,`Product Family Name`,`Product Family Code`  from `Product Family Dimension` where `Product Family Key` in (%s)",$family_keys);
    $res=mysql_query($sql);
    while($row=mysql_fetch_array($res)){
       $image='';
      $results['F '.$row['Product Family Key']]=array('image'=>$image,'code'=>$row['Product Family Code'],'description'=>$row['Product Family Name'],'link'=>'family.php?id=','key'=>$row['Product Family Key']);
    }
  }

  if($products_keys){
    $sql=sprintf("select `Product ID`,`Product XHTML Short Description`,`Product Code`,`Product Main Image`  from `Product Dimension`   where `Product ID` in (%s)",$products_keys);
    $res=mysql_query($sql);
    while($row=mysql_fetch_array($res)){
      $image='';
      if($row['Product Main Image']!='art/nopic.png')
	$image=sprintf('<img src="%s"> ',preg_replace('/small/','thumbnails',$row['Product Main Image']));
      $results['P '.$row['Product ID']]=array('image'=>$image,'code'=>$row['Product Code'],'description'=>$row['Product XHTML Short Description'],'link'=>'product.php?pid=','key'=>$row['Product ID']);
    }
  }

  

  
 $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'');
  echo json_encode($response);













}



function search_products_old($q,$tipo,$user){
  global $myconf;
 if ($tipo=='product_manage_stock')
        $target='product_manage_stock.php';
    else
        $target='product.php';

    $q=$_REQUEST['q'];
    $sql=sprintf("select `Product Code`  from `Product Dimension` where `Product Code`='%s'  and `Product Store Key` in (%s)     "
		 ,addslashes($q)
		 ,join(',',$user->stores)
		 );
    $res = mysql_query($sql);
    if ($found=mysql_fetch_array($res)) {
        $url=$target.'?code='. $found['Product Code'];
        echo json_encode(array('state'=>200,'url'=>$url));
        mysql_free_result($res);
        return;
    }
    mysql_free_result($res);
  
    if ($tipo=='product') {
        $sql=sprintf("select `Product Family Key` as id  from `Product Family Dimension` where `Product Family Code`='%s' and `Product Family Store Key` in (%s)   "
		     ,addslashes($q)
		     ,join(',',$user->stores)
		     );
        $result=mysql_query($sql);
        if ($found=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $url='family.php?id='. $found['id'];
            echo json_encode(array('state'=>200,'url'=>$url));
            mysql_free_result($result);
            return;
        }
        mysql_free_result($result);
    }
    // try to get similar results
    //   if($myconf['product_code_separator']!=''){
    if (  ($myconf['product_code_separator']!='' and   preg_match('/'.$myconf['product_code_separator'].'/',$q)) or  $myconf['product_code_separator']==''  ) {
        $sql=sprintf("select damlev(UPPER(%s),UPPER(`Product Code`)) as dist1,    damlev(UPPER(SOUNDEX(%s)),UPPER(SOUNDEX(`Product Code`))) as dist2,        `Product Code` as code,`product id` as id from `Product Dimension`  where  `Product Store Key` in (%s)     order by dist1,dist2 limit 1;"
		     ,prepare_mysql($q)
		     ,prepare_mysql($q)
		     ,join(',',$user->stores)
		     );
        $result=mysql_query($sql);
	//	print $sql;     
   if ($found=mysql_fetch_array($result, MYSQL_ASSOC)) {
            if ($found['dist1']<3) {
                echo json_encode(array('state'=>400,'msg1'=>_('Did you mean'),'msg2'=>'<a href="'.$target.'?pid='.$found['id'].'">'.$found['code'].'</a>'));
                mysql_free_result($result);
                return;
            }
        }
        mysql_free_result($result);


    }
    elseif($tipo=='product') {
        // look on the family list
      $sql=sprintf("select damlev(UPPER(%s),UPPER(`Product Family Code`)) as dist1, damlev(UPPER(SOUNDEX(%s)),UPPER(SOUNDEX(`Product Family Code`))) as dist2, `Product Family Code` as name ,`Product Family Key` id from `Product Family Dimension`  where  `Product Family Store Key` in (%s)     order by dist1,dist2 limit 1;",prepare_mysql($q),prepare_mysql($q),join(',',$user->stores));
        $result=mysql_query($sql);
        if ($found=mysql_fetch_array($result, MYSQL_ASSOC)) {
            if ($found['dist1']<3) {
                echo json_encode(array('state'=>400,'msg1'=>_('Did you mean'),'msg2'=>'<a href="family.php?id='.$found['id'].'">'.$found['name'].'</a> '._('family') ));
                

                    return;
            }
        }
        
        mysql_free_result($result);
    }
    
    echo json_encode(array('state'=>500,'msg'=>_('Product not found')));
}


function search_location($q,$tipo,$user){
    $sql=sprintf("select id from location where name='%s' ",addslashes($q));
    $result=mysql_query($sql);
    if ($found=mysql_fetch_array($result, MYSQL_ASSOC)) {
        $url='location.php?id='. $found['id'];
        echo json_encode(array('state'=>200,'url'=>$url));
        return;
    }
    mysql_free_result($result);
    $sql=sprintf("select damlev(UPPER(%s),UPPER(name)) as dist1,    damlev(UPPER(SOUNDEX(%s)),UPPER(SOUNDEX(name))) as dist2,name,id from location  order by dist1,dist2 limit 1;",prepare_mysql($q),prepare_mysql($q));
    $result=mysql_query($sql);
    if ($found=mysql_fetch_array($result, MYSQL_ASSOC)) {
        if ($found['dist1']<3) {
            echo json_encode(array('state'=>400,'msg1'=>_('Did you mean'),'msg2'=>'<a href="location.php?id='.$found['id'].'">'.$found['name'].'</a>'));
            return;
        }
    }
    mysql_free_result($result);
    echo json_encode(array('state'=>500,'msg'=>_('Location not found')));
    return;
    }
    
    
    
    function is_postal_code($postalcode){
    
    if(preg_match('/^([a-z]{2}-?)?\d{3,10}(-\d{3})?$/i',$postalcode) or preg_match('/^([a-z]\d{4}[a-z]|[A-Z]{2}\d{2}|[A-Z]\d{4}[A-Z]{3}|\d{4}[A-Z]{2})$/i',$postalcode))
        return true;
  
     return false;   
    }
    
?>