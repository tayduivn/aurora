<?
//@author Raul Perusquia <rulovico@gmail.com>
//Copyright (c) 2009 LW
class part{
  

  Public $id=false;

  function __construct($a1,$a2=false) {



      if(is_numeric($a1) and !$a2){      $this->get_data('id',$a1);
    }
    else if(($a1=='new' or $a1=='create') and is_array($a2) ){
      $this->msg=$this->create($a2);
      
    } else
      $this->get_data($a1,$a2);

  }
  



  function get_data($tipo,$tag){
    if($tipo=='id')
      $sql=sprintf("select * from `Part Dimension` where `Part Key`=%d ",$tag);
    elseif($tipo=='sku')
      $sql=sprintf("select * from `Part Dimension` where `Part SKU`=%d ",$tag);

    else
      return;

    $result=mysql_query($sql);
    if(($this->data=mysql_fetch_array($result, MYSQL_ASSOC))){
      $this->id=$this->data['Part Key'];
    }
    

  }
  
  function create($data){
    // print_r($data);
     $base_data=array(
		      'part status'=>'In Use',
		      'part xhtml currently used in'=>'',
		     'part xhtml currently supplied by'=>'',
		     'part xhtml description'=>'',
		     'part unit description'=>'',
		     'part package size metadata'=>'',
		     'part package volume'=>'',
		     'part package minimun orthogonal volume'=>'',
		     'part gross weight'=>'',
		     'part valid from'=>'',
		     'part valid to'=>'',
		     );
     foreach($data as $key=>$value){
       if(isset( $base_data[strtolower($key)]) )
	 $base_data[strtolower($key)]=_trim($value);
     }
 
     //    if(!$this->valid_sku($base_data['part sku']) ){
     $base_data['part sku']=$this->new_sku();
       // }

     $keys='(';$values='values(';
    foreach($base_data as $key=>$value){
      $keys.="`$key`,";
      $values.=prepare_mysql($value).",";
    }
    $keys=preg_replace('/,$/',')',$keys);
    $values=preg_replace('/,$/',')',$values);
    
    $sql=sprintf("insert into `Part Dimension` %s %s",$keys,$values);
    // print "$sql\n";
    // exit;
    if(mysql_query($sql)){
      $this->id = mysql_insert_id();

    //   if($base_data['part most recent']=='Yes')
//       	$sql=sprintf("update  `Part Dimension` set `Part Most Recent Key`=%d where `Part Key`=%d",$this->id,$this->id);
// 	mysql_query($sql);

      $this->get_data('id',$this->id);
    }else{
      print "Error Part can not be created\n";exit;
    }

 }

  function load($data_to_be_read,$args=''){
    switch($data_to_be_read){
    case('stock_history'):
    case('calculate_stock_history'):
      global $myconf;
      $force='';
      if(preg_match('/all/',$args))
	$force='all';
      if(preg_match('/last|audit/',$args))
	$force='last';
      if(preg_match('/continue/',$args))
	$force='continue';


      $part_sku=$this->data['Part SKU'];

      if(isset($args) and $args=='today')
	$min=strtotime('today');
      else
	$min=strtotime($myconf["data_from"]);
      
      
      $sql=sprintf("select `Location Key` from `Inventory Transaction Fact` where `Part SKU`=%d group by `Location Key` ",$part_sku);
      $resultxxx=mysql_query($sql);
      while(($rowxxx=mysql_fetch_array($resultxxx, MYSQL_ASSOC))){
	$skip=false;
	$location_key=$rowxxx['Location Key'];
	$pl=new PartLocation($location_key.'_'.$this->data['Part SKU']);
	if($location_key==1){
	  if($force=='all'){
	    $_from=$this->data['Part Valid From'];
	  }elseif($force=='last'){
	    $_from=$pl->last_inventory_audit();
	  }elseif($force=='continue'){
	    $_from=$pl->last_inventory_date();
	  }else{
	    $_from=$pl->first_inventory_transacion();
	  }
	  if(!$_from)
	    $skip=true;
	  $from=strtotime($_from);
	}else{
	  if($force=='first')
	    $_from=$pl->first_inventory_transacion();
	  else
	    $_from=$pl->last_inventory_audit();
	  
	  if(!$_from)
	    $skip=true;
	  $from=strtotime($_from);
	}

       
	
	if($from<$min)
	  $from=$min;
	
	if($this->data['Part Status']=='In Use'){
	  $to=strtotime('today');
	}else{
	  $to=strtotime($this->data['Part Valid To']);
	}

	
	if($from>$to){
	  //   print("error    $part_sku $location_key  ".$rowx['Part Valid From']." ".$rowx['Part Valid To']."   \n   ");
	  continue;
	}
	
	
	if($skip){
	  print "No trasactions $part_sku $location_key "; 
	  continue;
	}
	
	$from=date("Y-m-d",$from);
	$to=date("Y-m-d",$to);
	print "** $part_sku $location_key  $from $to\n";
	//  $pl=new PartLocation(array('LocationPart'=>$location_key."_".$part_sku));
	$pl->redo_daily_inventory($from,$to);
	
	
      }
    

      break;

    case('stock'):

      $this->load('locations');

      $stock='';
      $value='';
      $neg_discrepancy_value='';
      $neg_discrepancy='';
      $sql=sprintf("select sum(`Quantity On Hand`) as stock,sum(`Stock Value`) as value, sum(`Negative Discrepancy`) as neg_discrepancy, sum(`Negative Discrepancy Value`) as neg_discrepancy_value from `Part Location Dimension` where  `Part SKU`=%d ",$this->data['Part SKU']);
      //print $sql;
      $result=mysql_query($sql);
      if(($row=mysql_fetch_array($result, MYSQL_ASSOC))){
	$stock=$row['stock'];
	$value=$row['value'];
	$neg_discrepancy_value=$row['neg_discrepancy_value'];
	$neg_discrepancy=$row['neg_discrepancy'];
      }

      if(!is_numeric($stock))
	$stock='NULL';
       if(!is_numeric($value))
	$value='NULL';

       $sql=sprintf("update `Part Dimension` set `Part Current Stock`=%s ,`Part Current Stock Cost`=%s ,`Part Current Stock Negative Discrepancy`=%f ,`Part Current Stock Negative Discrepancy Value`=%f  where `Part Key`=%d "
		    ,$stock
		    ,$value
		    ,$neg_discrepancy
		    ,$neg_discrepancy_value
		   ,$this->id);

       print "$stock $value $neg_discrepancy $neg_discrepancy_value \n";
       // update products that depends of this part
       $this->load('used in list');
       
       foreach($this->used_in_list as $product_key){
	 $product=new Product($product_key);
	 if(!$product->id){
	   print_r($this->used_in_list);
	   exit("Error can not load prodct $product_key\n");
	 }
	 $product->load('stock');
       }

       if(!mysql_query($sql))
       	exit("  errorcant not uopdate parts stock");

      break;
    case('stock_data'):
      $astock=0;
      $avalue=0;
      
      $sql=sprintf("select ifnull(avg(`Quantity On Hand`),'ERROR') as stock,avg(`Value At Cost`) as value from `Inventory Spanshot Fact` where  `Part SKU`=%d and `Date`>=%s and `Date`<=%s group by `Date`",$this->data['Part SKU'],prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid From']))),prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid To']))  ));
      // print "$sql\n";
      $result=mysql_query($sql);
      $days=0;
      $errors=0;
      $outstock=0;
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	if(is_numeric($row['stock']))
	  $astock+=$row['stock'];
	if(is_numeric($row['value']))
	  $avalue+=$row['value'];
	$days++;

	  if(is_numeric($row['stock']) and $row['stock']==0)
	  $outstock++;
	if($row['stock']=='ERROR')
	  $errors++;
      }
      
      $days_ok=$days-$errors;
      
      $gmroi='NULL';
      if($days_ok>0){
	$astock=$astock/$days_ok;
	$avalue=$avalue/$days_ok;
	if($avalue>0)
	  $gmroi=$this->data['Part Total Profit When Sold']/$avalue;
      }else{
	$astock='NULL';
	$avalue='NULL';
      }

      $tdays = (strtotime($this->data['Part Valid To']) - strtotime($this->data['Part Valid From'])) / (60 * 60 * 24);
      //print "$tdays $days o: $outstock e: $errors \n";
      $unknown=$tdays-$days_ok;
       $sql=sprintf("update `Part Dimension` set `Part Total AVG Stock`=%s ,`Part Total AVG Stock Value`=%s,`Part Total Keeping Days`=%f ,`Part Total Out of Stock Days`=%f , `Part Total Unknown Stock Days`=%s, `Part Total GMROI`=%s where `Part Key`=%d"
		    ,$astock
		    ,$avalue
		    ,$tdays
		    ,$outstock
		    ,$unknown
		    ,$gmroi
		    ,$this->id);
       // print "$sql\n";
       if(!mysql_query($sql))
	 exit("$sql  errot con not update part stock history all");

       $astock=0;
       $avalue=0;
       
       $sql=sprintf("select ifnull(avg(`Quantity On Hand`),'ERROR') as stock,avg(`Value At Cost`) as value from `Inventory Spanshot Fact` where   `Part SKU`=%d and `Date`>=%s and `Date`<=%s  and `Date`>=%s    group by `Date`",$this->data['Part SKU'],prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid From']))),prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid To']))  )  ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -1 year")))  );
       //print "$sql\n";
      $result=mysql_query($sql);
      $days=0;
      $errors=0;
      $outstock=0;
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	if(is_numeric($row['stock']))
	  $astock+=$row['stock'];
	if(is_numeric($row['value']))
	  $avalue+=$row['value'];
	$days++;

	  if(is_numeric($row['stock']) and $row['stock']==0)
	  $outstock++;
	if($row['stock']=='ERROR')
	  $errors++;
      }
      
      $days_ok=$days-$errors;
      
      $gmroi='NULL';
      if($days_ok>0){
	$astock=$astock/$days_ok;
	$avalue=$avalue/$days_ok;
	if($avalue>0)
	  $gmroi=$this->data['Part 1 Year Acc Profit When Sold']/$avalue;
      }else{
	$astock='NULL';
	$avalue='NULL';
      }

      $tdays = (strtotime($this->data['Part Valid To']) - strtotime($this->data['Part Valid From'])) / (60 * 60 * 24);
      //print "$tdays $days o: $outstock e: $errors \n";
      $unknown=$tdays-$days_ok;
       $sql=sprintf("update `Part Dimension` set `Part 1 Year Acc AVG Stock`=%s ,`Part 1 Year Acc AVG Stock Value`=%s,`Part 1 Year Acc Keeping Days`=%f ,`Part 1 Year Acc Out of Stock Days`=%f , `Part 1 Year Acc Unknown Stock Days`=%s, `Part 1 Year Acc GMROI`=%s where `Part Key`=%d"
		    ,$astock
		    ,$avalue
		    ,$tdays
		    ,$outstock
		    ,$unknown
		    ,$gmroi
		    ,$this->id);
       // print "$sql\n";
       if(!mysql_query($sql))
	 exit("$sql errot con not update part stock history yr aa");


  $astock=0;
       $avalue=0;
       
       $sql=sprintf("select ifnull(avg(`Quantity On Hand`),'ERROR') as stock,avg(`Value At Cost`) as value from `Inventory Spanshot Fact` where   `Part SKU`=%d and `Date`>=%s and `Date`<=%s  and `Date`>=%s    group by `Date`",$this->data['Part SKU'],prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid From']))),prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid To']))  )  ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -3 month")))  );
       // print "$sql\n";
      $result=mysql_query($sql);
      $days=0;
      $errors=0;
      $outstock=0;
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	if(is_numeric($row['stock']))
	  $astock+=$row['stock'];
	if(is_numeric($row['value']))
	  $avalue+=$row['value'];
	$days++;

	  if(is_numeric($row['stock']) and $row['stock']==0)
	  $outstock++;
	if($row['stock']=='ERROR')
	  $errors++;
      }
      
      $days_ok=$days-$errors;
      
      $gmroi='NULL';
      if($days_ok>0){
	$astock=$astock/$days_ok;
	$avalue=$avalue/$days_ok;
	if($avalue>0)
	  $gmroi=$this->data['Part 1 Quarter Acc Profit When Sold']/$avalue;
      }else{
	$astock='NULL';
	$avalue='NULL';
      }

      $tdays = (strtotime($this->data['Part Valid To']) - strtotime($this->data['Part Valid From'])) / (60 * 60 * 24);
      //print "$tdays $days o: $outstock e: $errors \n";
      $unknown=$tdays-$days_ok;
       $sql=sprintf("update `Part Dimension` set `Part 1 Quarter Acc AVG Stock`=%s ,`Part 1 Quarter Acc AVG Stock Value`=%s,`Part 1 Quarter Acc Keeping Days`=%f ,`Part 1 Quarter Acc Out of Stock Days`=%f , `Part 1 Quarter Acc Unknown Stock Days`=%s, `Part 1 Quarter Acc GMROI`=%s where `Part Key`=%d"
		    ,$astock
		    ,$avalue
		    ,$tdays
		    ,$outstock
		    ,$unknown
		    ,$gmroi
		    ,$this->id);
       //   print "$sql\n";
       if(!mysql_query($sql))
	 exit("$sql errot con not update part stock history yr bb");

  $astock=0;
       $avalue=0;
       
       $sql=sprintf("select ifnull(avg(`Quantity On Hand`),'ERROR') as stock,avg(`Value At Cost`) as value from `Inventory Spanshot Fact` where `Part SKU`=%d and `Date`>=%s and `Date`<=%s  and `Date`>=%s    group by `Date`",$this->data['Part SKU'],prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid From']))),prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid To']))  )  ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -1 month")))  );
       // print "$sql\n";
      $result=mysql_query($sql);
      $days=0;
      $errors=0;
      $outstock=0;
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	if(is_numeric($row['stock']))
	  $astock+=$row['stock'];
	if(is_numeric($row['value']))
	  $avalue+=$row['value'];
	$days++;

	  if(is_numeric($row['stock']) and $row['stock']==0)
	  $outstock++;
	if($row['stock']=='ERROR')
	  $errors++;
      }
      
      $days_ok=$days-$errors;
      
      $gmroi='NULL';
      if($days_ok>0){
	$astock=$astock/$days_ok;
	$avalue=$avalue/$days_ok;
	if($avalue>0)
	  $gmroi=$this->data['Part 1 Month Acc Profit When Sold']/$avalue;
      }else{
	$astock='NULL';
	$avalue='NULL';
      }

      $tdays = (strtotime($this->data['Part Valid To']) - strtotime($this->data['Part Valid From'])) / (60 * 60 * 24);
      //print "$tdays $days o: $outstock e: $errors \n";
      $unknown=$tdays-$days_ok;
       $sql=sprintf("update `Part Dimension` set `Part 1 Month Acc AVG Stock`=%s ,`Part 1 Month Acc AVG Stock Value`=%s,`Part 1 Month Acc Keeping Days`=%f ,`Part 1 Month Acc Out of Stock Days`=%f , `Part 1 Month Acc Unknown Stock Days`=%s, `Part 1 Month Acc GMROI`=%s where `Part Key`=%d"
		    ,$astock
		    ,$avalue
		    ,$tdays
		    ,$outstock
		    ,$unknown
		    ,$gmroi
		    ,$this->id);
       //   print "$sql\n";
       if(!mysql_query($sql))
	 exit(" $sql errot con not update part stock history yr cc");


  $astock=0;
       $avalue=0;
       
       $sql=sprintf("select ifnull(avg(`Quantity On Hand`),'ERROR') as stock,avg(`Value At Cost`) as value from `Inventory Spanshot Fact` where `Part SKU`=%d and `Date`>=%s and `Date`<=%s  and `Date`>=%s    group by `Date`",$this->data['Part SKU'],prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid From']))),prepare_mysql(date("Y-m-d",strtotime($this->data['Part Valid To']))  )  ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -1 week")))  );
       // print "$sql\n";
      $result=mysql_query($sql);
      $days=0;
      $errors=0;
      $outstock=0;
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	if(is_numeric($row['stock']))
	  $astock+=$row['stock'];
	if(is_numeric($row['value']))
	  $avalue+=$row['value'];
	$days++;

	  if(is_numeric($row['stock']) and $row['stock']==0)
	  $outstock++;
	if($row['stock']=='ERROR')
	  $errors++;
      }
      
      $days_ok=$days-$errors;
      
      $gmroi='NULL';
      if($days_ok>0){
	$tmp=1.0000001/$days_ok;
	$astock=$astock*$tmp;
	$avalue=$avalue*$tmp;
	if($avalue>0)
	  $gmroi=$this->data['Part 1 Week Acc Profit When Sold']/$avalue;
      }else{
	$astock='NULL';
	$avalue='NULL';
      }

      $tdays = (strtotime($this->data['Part Valid To']) - strtotime($this->data['Part Valid From'])) / (60 * 60 * 24);
      //print "$tdays $days o: $outstock e: $errors \n";
      $unknown=$tdays-$days_ok;
       $sql=sprintf("update `Part Dimension` set `Part 1 Week Acc AVG Stock`=%s ,`Part 1 Week Acc AVG Stock Value`=%s,`Part 1 Week Acc Keeping Days`=%f ,`Part 1 Week Acc Out of Stock Days`=%f , `Part 1 Week Acc Unknown Stock Days`=%s, `Part 1 Week Acc GMROI`=%s where `Part Key`=%d"
		    ,$astock
		    ,$avalue
		    ,$tdays
		    ,$outstock
		    ,$unknown
		    ,$gmroi
		    ,$this->id);
       //   print "$sql\n";
       if(!mysql_query($sql))
	 exit("$sql errot con not update part stock history wk");

      break;
    case('used in list'):
      
      $sql=sprintf("select `Product Key` from `Product Part List` PPL left join `Product Dimension` PD on (PD.`Product ID`=PPL.`Product ID`)  where `Part SKU`=%d group by `Product Key`",$this->data['Part SKU']);
      // print $sql;
      $result=mysql_query($sql);
      $this->used_in_list=array();
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$this->used_in_list[]=$row['Product Key'];
      }
    //   print_r($this->used_in_list);
      break;
    case("used in"):
      $used_in_products='';
      $raw_used_in_products='';
      $sql=sprintf("select `Product Same Code Most Recent Key`,`Product Code` from `Product Part List` PPL left join `Product Dimension` PD on (PD.`Product ID`=PPL.`Product ID`)  where `Part SKU`=%d group by `Product Code` order by `product Code`",$this->data['Part SKU']);
      $result=mysql_query($sql);
      //      print "$sql\n";
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$used_in_products.=sprintf(', <a href="product.php?id=%d">%s</a>',$row['Product Same Code Most Recent Key'],$row['Product Code']);
	$raw_used_in_products=' '.$row['Product Code'];
      }
      $used_in_products=preg_replace('/^, /','',$used_in_products);
      $sql=sprintf("update `Part Dimension` set `Part XHTML Currently Used In`=%s ,`Part Currently Used In`=%s  where `Part Key`=%d",prepare_mysql(_trim($used_in_products)),prepare_mysql(_trim($raw_used_in_products)),$this->id);
      //print "$sql\n";
      mysql_query($sql);
      break;
    case("supplied by"):
       $supplied_by='';
      $sql=sprintf("select  (select `Supplier Product Code` from `Supplier Product Dimension` where `Supplier Product ID`=SPPL.`Supplier Product ID` and `Supplier Product Most Recent` limit 1) as `Supplier Product Code`,(select `Supplier Product Key` from `Supplier Product Dimension` where `Supplier Product ID`=SPPL.`Supplier Product ID` and `Supplier Product Most Recent` limit 1) as `Supplier Product Key` ,  SD.`Supplier Key`,`Supplier Code` from `Supplier Product Part List` SPPL   left join `Supplier Dimension` SD on (SD.`Supplier Key`=SPPL.`Supplier Key`)   where `Part SKU`=%d  order by `Supplier Key`;",$this->data['Part SKU']);
      $result=mysql_query($sql);
      //print "$sql\n";
      $supplier=array();
      $current_supplier='_';
      while($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$_current_supplier=$row['Supplier Key'];
	if($_current_supplier!=$current_supplier){
	  $supplied_by.=sprintf(', <a href="supplier.php?id=%d">%s</a>(<a href="supplier_product.php?id=%d">%s</a>',$row['Supplier Key'],$row['Supplier Code'],$row['Supplier Product Key'],$row['Supplier Product Code']);
	  $current_supplier=$_current_supplier;
	}else{
	   $supplied_by.=sprintf(', <a href="supplier_product.php?id=%d">%s</a>',$row['Supplier Product Key'],$row['Supplier Product Code']);

	}
	
      }
      $supplied_by.=")";

      $supplied_by=_trim(preg_replace('/^, /','',$supplied_by));
      if($supplied_by=='')
	$supplied_by=_('Unknown Supplier');


       $sql=sprintf("update `Part Dimension` set `Part XHTML Currently Supplied By`=%s where `Part Key`=%d",prepare_mysql(_trim($supplied_by)),$this->id);
       //       print "$sql\n";exit;
      if(!mysql_query($sql))
	exit("error can no suplied by part 498239048");
      break;


    case("sales"):
      // the product wich this one is 
      $sold=0;
      $required=0;
      $provided=0;
      $given=0;
      $amount_in=0;
      $value=0;
      $value_free=0;
      $margin=0;
      $sql=sprintf("select   ifnull(sum(`Given`*`Inventory Transaction Amount`/(`Inventory Transaction Quantity`)),0) as value_free,   ifnull(sum(`Required`),0) as required, ifnull(sum(`Given`),0) as given, ifnull(sum(`Amount In`),0) as amount_in, ifnull(sum(-`Inventory Transaction Quantity`),0) as qty, ifnull(sum(-`Inventory Transaction Amount`),0) as value from  `Inventory Transaction Fact` where `Part SKU`=%s and `Inventory Transaction Type`='Sale' and `Date`>=%s  and `Date`<=%s   ",prepare_mysql($this->data['Part SKU']),prepare_mysql($this->data['Part Valid From']),prepare_mysql($this->data['Part Valid To'])  );
      //       print "$sql\n\n\n";
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$required=$row['required'];
	$provided=$row['qty'];
	$given=$row['given'];
	$amount_in=floatval($row['amount_in']);
	$value=floatval($row['value']);
	$value_free=floatval($row['value_free']);
	$sold=$row['qty']-$row['given'];
      }
      $abs_profit=$amount_in-$value;
      $profit_sold=$amount_in-$value+$value_free;
      if($amount_in==0)
	$margin=0;
      else{
	$margin=$profit_sold/$amount_in;
	//	$margin=($value-$value_free)/$amount_in;
	//	$margin=sprintf("%.6f",($value)*$tmp);
	$margin=preg_replace('/:/','1',$margin);
	//$margin=$value/$amount_in;
      }

  //     var_dump( $value );
//       var_dump(  $amount_in);
//       var_dump( 0.7/7 );
      $sql=sprintf("update `Part Dimension` set `Part Total Required`=%f ,`Part Total Provided`=%f,`Part Total Given`=%f ,`Part Total Sold Amount`=%f ,`Part Total Absolute Profit`=%f ,`Part Total Profit When Sold`=%f , `Part Total Sold`=%f , `Part Total Margin`=%f  where `Part Key`=%d "
		   ,$required
		   ,$provided
		   ,$given
		   ,$amount_in
		   ,$abs_profit
		   ,$profit_sold,$sold,$margin
		   ,$this->id);
      //    print "$sql\n";
      if(!mysql_query($sql))
       	exit("  error a $margin b $value c $value_free d $amount_in  con not uopdate product part when loading sales");
	
      $sold=0;
      $required=0;
      $provided=0;
      $given=0;
      $amount_in=0;
      $value=0;
      $value_free=0;
      $margin=0;
      $sql=sprintf("select   ifnull(sum(`Given`*`Inventory Transaction Amount`/(`Inventory Transaction Quantity`)),0) as value_free,   ifnull(sum(`Required`),0) as required, ifnull(sum(`Given`),0) as given, ifnull(sum(`Amount In`),0) as amount_in, ifnull(sum(-`Inventory Transaction Quantity`),0) as qty, ifnull(sum(-`Inventory Transaction Amount`),0) as value from  `Inventory Transaction Fact` where `Part SKU`=%s and `Inventory Transaction Type`='Sale' and `Date`>=%s  and `Date`<=%s  and `Date`>=%s     ",prepare_mysql($this->data['Part SKU']),prepare_mysql($this->data['Part Valid From']),prepare_mysql($this->data['Part Valid To']) ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -1 year")))  );
      // print "$sql\n";
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$required=$row['required'];
	$provided=$row['qty'];
	$given=$row['given'];
	$amount_in=$row['amount_in'];
	$value=$row['value'];
	$value_free=$row['value_free'];
	$sold=$row['qty']-$row['given'];
      }
      $abs_profit=$amount_in-$value;
      $profit_sold=$amount_in-$value+$value_free;
      if($amount_in==0)
	$margin=0;
      else
	$margin=$profit_sold/$amount_in;
      $sql=sprintf("update `Part Dimension` set `Part 1 Year Acc Required`=%f ,`Part 1 Year Acc Provided`=%f,`Part 1 Year Acc Given`=%f ,`Part 1 Year Acc Sold Amount`=%f ,`Part 1 Year Acc Absolute Profit`=%f ,`Part 1 Year Acc Profit When Sold`=%f , `Part 1 Year Acc Sold`=%f , `Part 1 Year Acc Margin`=%s where `Part Key`=%d "
		   ,$required
		   ,$provided
		   ,$given
		   ,$amount_in
		   ,$abs_profit
		   ,$profit_sold,$sold,$margin
		   ,$this->id);
      //  print "$sql\n";
      if(!mysql_query($sql))
	exit(" $sql\n error con not uopdate product part when loading sales");
      
      $sold=0;
       $required=0;
      $provided=0;
      $given=0;
      $amount_in=0;
      $value=0;
      $value_free=0;
      $margin=0;
      $sql=sprintf("select   ifnull(sum(`Given`*`Inventory Transaction Amount`/(`Inventory Transaction Quantity`)),0) as value_free,   ifnull(sum(`Required`),0) as required, ifnull(sum(`Given`),0) as given, ifnull(sum(`Amount In`),0) as amount_in, ifnull(sum(-`Inventory Transaction Quantity`),0) as qty, ifnull(sum(-`Inventory Transaction Amount`),0) as value from  `Inventory Transaction Fact` where `Part SKU`=%s and `Inventory Transaction Type`='Sale' and `Date`>=%s  and `Date`<=%s  and `Date`>=%s     ",prepare_mysql($this->data['Part SKU']),prepare_mysql($this->data['Part Valid From']),prepare_mysql($this->data['Part Valid To']) ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -3 month")))  );
      //      print "$sql\n";
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$required=$row['required'];
	$provided=$row['qty'];
	$given=$row['given'];
	$amount_in=$row['amount_in'];
	$value=$row['value'];
	$value_free=$row['value_free'];
	$sold=$row['qty']-$row['given'];
      }
      $abs_profit=$amount_in-$value;
      $profit_sold=$amount_in-$value+$value_free;

      if($amount_in==0)
	$margin=0;
      else
	$margin=$profit_sold/$amount_in;

      $sql=sprintf("update `Part Dimension` set `Part 1 Quarter Acc Required`=%f ,`Part 1 Quarter Acc Provided`=%f,`Part 1 Quarter Acc Given`=%f ,`Part 1 Quarter Acc Sold Amount`=%f ,`Part 1 Quarter Acc Absolute Profit`=%f ,`Part 1 Quarter Acc Profit When Sold`=%f  , `Part 1 Quarter Acc Sold`=%f  , `Part 1 Quarter Acc Margin`=%s where `Part Key`=%d "
		   ,$required
		   ,$provided
		   ,$given
		   ,$amount_in
		   ,$abs_profit
		   ,$profit_sold,$sold,$margin
		   ,$this->id);
      //            print "$sql\n";
      if(!mysql_query($sql))
	exit("error con not uopdate product part when loading sales");
      
      $sold=0;
       $required=0;
      $provided=0;
      $given=0;
      $amount_in=0;
      $value=0;
      $value_free=0;
      $margin=0;
      $sql=sprintf("select   ifnull(sum(`Given`*`Inventory Transaction Amount`/(`Inventory Transaction Quantity`)),0) as value_free,   ifnull(sum(`Required`),0) as required, ifnull(sum(`Given`),0) as given, ifnull(sum(`Amount In`),0) as amount_in, ifnull(sum(-`Inventory Transaction Quantity`),0) as qty, ifnull(sum(-`Inventory Transaction Amount`),0) as value from  `Inventory Transaction Fact` where `Part SKU`=%s and `Inventory Transaction Type`='Sale' and `Date`>=%s  and `Date`<=%s  and `Date`>=%s     ",prepare_mysql($this->data['Part SKU']),prepare_mysql($this->data['Part Valid From']),prepare_mysql($this->data['Part Valid To']) ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -1 month")))  );
      //      print "$sql\n";
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$required=$row['required'];
	$provided=$row['qty'];
	$given=$row['given'];
	$amount_in=$row['amount_in'];
	$value=$row['value'];
	$value_free=$row['value_free'];
	$sold=$row['qty']-$row['given'];
      }
      $abs_profit=$amount_in-$value;
      $profit_sold=$amount_in-$value+$value_free;

      if($amount_in==0)
	$margin=0;
      else
	$margin=$profit_sold/$amount_in;


      $sql=sprintf("update `Part Dimension` set `Part 1 Month Acc Required`=%f ,`Part 1 Month Acc Provided`=%f,`Part 1 Month Acc Given`=%f ,`Part 1 Month Acc Sold Amount`=%f ,`Part 1 Month Acc Absolute Profit`=%f ,`Part 1 Month Acc Profit When Sold`=%f  , `Part 1 Month Acc Sold`=%f , `Part 1 Month Acc Margin`=%s  where `Part Key`=%d "
		   ,$required
		   ,$provided
		   ,$given
		   ,$amount_in
		   ,$abs_profit
		   ,$profit_sold,$sold,$margin
		   ,$this->id);
      //            print "$sql\n";
      if(!mysql_query($sql))
	exit(" $sql\n error con not uopdate product part when loading sales");

  $sold=0;
         $required=0;
      $provided=0;
      $given=0;
      $amount_in=0;
      $value=0;
      $value_free=0;
      $margin=0;
      $sql=sprintf("select   ifnull(sum(`Given`*`Inventory Transaction Amount`/(`Inventory Transaction Quantity`)),0) as value_free,   ifnull(sum(`Required`),0) as required, ifnull(sum(`Given`),0) as given, ifnull(sum(`Amount In`),0) as amount_in, ifnull(sum(-`Inventory Transaction Quantity`),0) as qty, ifnull(sum(-`Inventory Transaction Amount`),0) as value from  `Inventory Transaction Fact` where `Part SKU`=%s and `Inventory Transaction Type`='Sale' and `Date`>=%s  and `Date`<=%s  and `Date`>=%s     ",prepare_mysql($this->data['Part SKU']),prepare_mysql($this->data['Part Valid From']),prepare_mysql($this->data['Part Valid To']) ,prepare_mysql(date("Y-m-d H:i:s",strtotime("now -1 week")))  );
      //        print "$sql\n";
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)   ){
	$required=$row['required'];
	$provided=$row['qty'];
	$given=$row['given'];
	$amount_in=$row['amount_in'];
	$value=$row['value'];
	$value_free=$row['value_free'];$sold=$row['qty']-$row['given'];
      }

      $abs_profit=$amount_in-$value;
      $profit_sold=$amount_in-$value+$value_free;
      if($amount_in==0)
	$margin=0;
      else
	$margin=$profit_sold/$amount_in;

      $sql=sprintf("update `Part Dimension` set `Part 1 Week Acc Required`=%f ,`Part 1 Week Acc Provided`=%f,`Part 1 Week Acc Given`=%f ,`Part 1 Week Acc Sold Amount`=%f ,`Part 1 Week Acc Absolute Profit`=%f ,`Part 1 Week Acc Profit When Sold`=%f  , `Part 1 Week Acc Sold`=%f , `Part 1 Week Acc Margin`=%s where `Part Key`=%d "
		   ,$required
		   ,$provided
		   ,$given
		   ,$amount_in
		   ,$abs_profit
		   ,$profit_sold,$sold,$margin
		   ,$this->id);
      //            print "$sql\n";
      if(!mysql_query($sql))
	exit(" $sql\n error con not uopdate product part when loading sales");

      break;
    case('future costs'):
    case('estimated cost'):
     $sql=sprintf("select min(`Supplier Product Cost`*`Supplier Product Units Per Part`) as min_cost ,avg(`Supplier Product Cost`*`Supplier Product Units Per Part`) as avg_cost from `Supplier Product Dimension` SPD left join  `Supplier Product Part List` SPPL on (SPD.`Supplier Product ID`=SPPL.`Supplier Product ID`)  left join `Supplier Dimension` SD on (SD.`Supplier Key`=SPPL.`Supplier Key`)   where `Part SKU`=%d and `Supplier Product Part Most Recent`='Yes'",$this->data['Part SKU']);
     //   print "$sql\n";
      $result=mysql_query($sql);
      if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
        if(is_numeric($row['avg_cost']))
	  $avg_cost=$row['avg_cost'];
	else
	  $avg_cost='NULL';
	if(is_numeric($row['min_cost']))
	  $min_cost=$row['min_cost'];
	else
	  $min_cost='NULL';
	
      }else{
	$avg_cost='NULL';
	$min_cost='NULL';
      }

      $sql=sprintf("update `Part Dimension` set `Part Average Future Cost`=%s,`Part Minimum Future Cost`=%s where `Part Key`=%d "
		   ,$avg_cost
		   ,$min_cost
		   ,$this->id);
      //            print "$sql\n";
      if(!mysql_query($sql))
	exit(" $sql\n error con not uopdate part futire costss");

      break;
    }


  }
  
  function get($key='',$args=false){
   
    if(array_key_exists($key,$this->data))
      return $this->data[$key];

     $_key=preg_replace('/^part /','',$key);
    if(isset($this->data[$_key]))
      return $this->data[$key];

    
    switch($key){
    case('Picking Location Key'):
      break;
 case('Current Associated Locations'):

      $associated=array();
      
      $sql=sprintf("select `Location Key` from `Part Location Dimension` where  `Part SKU`=%d   ",$this->data['Part SKU']);
      //  print $sql;
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
	$associated[]=$row['Location Key'];
      }
   
      
      return $associated;
   break;

    case('Associated Locations'):
      $associate=array();
      $associated=array();
      
      if($args!=''){
	$date=" and `Date`<='".date("Y-m-d H:i:s",strtotime($args))."'";
      }else
	$date='';
      
      $sql=sprintf("select `Location Key` from `Inventory Transaction Fact` where `Inventory Transaction Type`='Associate' and `Part SKU`=%d  %s  group by `Location Key`  ",$this->data['Part SKU'],$date);
      //  print $sql;
      $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){
	$associate[]=$row['Location Key'];
      }
      foreach($associate as $location_key){
	$sql=sprintf("select `Inventory Transaction Type` from `Inventory Transaction Fact` where (`Inventory Transaction Type`='Associate' or `Inventory Transaction Type`='Disassociate') and `Part SKU`=%d and `Location Key`=%d %s order by `Date` desc limit 1 ",$this->data['Part SKU'],$location_key,$date);
	//	  print $sql;
	  $res=mysql_query($sql);
	  if($row=mysql_fetch_array($res)){

	    if($row['Inventory Transaction Type']=='Associate')
	      $associated[]=$location_key;
	  }
	  
      }
      
      return $associated;
   break;
      
    }
    
    return false;
  }
  

 function valid_sku($sku){
   // print "validadndo sku $sku";
   if(is_numeric($sku) and $sku>0 and $sku<9223372036854775807)
     return true;
   else
     return false;
 }

function used_sku($sku){
  $sql="select count(*) as num from `Part Dimension` where `Part SKU`=".prepare_mysql($sku);
  // print "$sql\n";
  $result=mysql_query($sql);
  if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
    if($row['num']>0)
      return true;
  }
  return false;
}

 function new_sku(){
   $sql="select max(`Part SKU`) as sku from `Part Dimension`";
   //   print "$sql\n";
   $result=mysql_query($sql);
   if($row=mysql_fetch_array($result, MYSQL_ASSOC)){
     return $row['sku']+1;
   }else
     return 1;
   
 }
 








}