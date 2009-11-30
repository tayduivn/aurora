<?php
/*
 File: Department.php

 This file contains the Department Class

 About:
 Autor: Raul Perusquia <rulovico@gmail.com>

 Copyright (c) 2009, Kaktus

 Version 2.0
*/
include_once('class.Family.php');

/* class: Department
   Class to manage the *Product Department Dimension* table
*/
// JFA




class Department extends DB_Table {



    /*
      Constructor: Department
      Initializes the class, trigger  Search/Load/Create for the data set

      Returns:
      void
    */

    function Department ($a1=false,$a2=false,$a3=false) {
        $this->table_name='Product Department';
        $this->ignore_fields=array(
                                 'Product Department Key',
                                 'Product Department Families',
                                 'Product Department For Sale Products',
                                 'Product Department In Process Products',
                                 'Product Department Not For Sale Products',
                                 'Product Department Discontinued Products',
                                 'Product Department Unknown Sales State Products',
                                 'Product Department Surplus Availability Products',
                                 'Product Department Optimal Availability Products',
                                 'Product Department Low Availability Products',
                                 'Product Department Critical Availability Products',
                                 'Product Department Out Of Stock Products',
                                 'Product Department Unknown Stock Products',
                                 'Product Department Total Invoiced Gross Amount',
                                 'Product Department Total Invoiced Discount Amount',
                                 'Product Department Total Invoiced Amount',
                                 'Product Department Total Profit',
                                 'Product Department Total Quantity Ordered',
                                 'Product Department Total Quantity Invoiced',
                                 'Product Department Total Quantity Delivere',
                                 'Product Department Total Days On Sale',
                                 'Product Department Total Days Available',
                                 'Product Department 1 Year Acc Invoiced Gross Amount',
                                 'Product Department 1 Year Acc Invoiced Discount Amount',
                                 'Product Department 1 Year Acc Invoiced Amount',
                                 'Product Department 1 Year Acc Profit',
                                 'Product Department 1 Year Acc Quantity Ordered',
                                 'Product Department 1 Year Acc Quantity Invoiced',
                                 'Product Department 1 Year Acc Quantity Delivere',
                                 'Product Department 1 Year Acc Days On Sale',
                                 'Product Department 1 Year Acc Days Available',
                                 'Product Department 1 Quarter Acc Invoiced Gross Amount',
                                 'Product Department 1 Quarter Acc Invoiced Discount Amount',
                                 'Product Department 1 Quarter Acc Invoiced Amount',
                                 'Product Department 1 Quarter Acc Profit',
                                 'Product Department 1 Quarter Acc Quantity Ordered',
                                 'Product Department 1 Quarter Acc Quantity Invoiced',
                                 'Product Department 1 Quarter Acc Quantity Delivere',
                                 'Product Department 1 Quarter Acc Days On Sale',
                                 'Product Department 1 Quarter Acc Days Available',
                                 'Product Department 1 Month Acc Invoiced Gross Amount',
                                 'Product Department 1 Month Acc Invoiced Discount Amount',
                                 'Product Department 1 Month Acc Invoiced Amount',
                                 'Product Department 1 Month Acc Profit',
                                 'Product Department 1 Month Acc Quantity Ordered',
                                 'Product Department 1 Month Acc Quantity Invoiced',
                                 'Product Department 1 Month Acc Quantity Delivere',
                                 'Product Department 1 Month Acc Days On Sale',
                                 'Product Department 1 Month Acc Days Available',
                                 'Product Department 1 Week Acc Invoiced Gross Amount',
                                 'Product Department 1 Week Acc Invoiced Discount Amount',
                                 'Product Department 1 Week Acc Invoiced Amount',
                                 'Product Department 1 Week Acc Profit',
                                 'Product Department 1 Week Acc Quantity Ordered',
                                 'Product Department 1 Week Acc Quantity Invoiced',
                                 'Product Department 1 Week Acc Quantity Delivere',
                                 'Product Department 1 Week Acc Days On Sale',
                                 'Product Department 1 Week Acc Days Available',
                                 'Product Department Total Quantity Delivered',
                                 'Product Department 1 Year Acc Quantity Delivered',
                                 'Product Department 1 Month Acc Quantity Delivered',
                                 'Product Department 1 Quarter Acc Quantity Delivered',
                                 'Product Department 1 Week Acc Quantity Delivered',
                                 'Product Department Stock Value'


                             );

        if (is_numeric($a1) and !$a2  and $a1>0 )
            $this->get_data('id',$a1,false);
        else if ( preg_match('/new|create/i',$a1)) {
            $this->find($a2,'create');
        } else if ( preg_match('/find/i',$a1)) {
            $this->find($a2,$a3);
        }
        elseif($a2!='')
        $this->get_data($a1,$a2,$a3);

    }


    function find($raw_data,$options) {


        if (isset($raw_data['editor'])) {
            foreach($raw_data['editor'] as $key=>$value) {
                if (array_key_exists($key,$this->editor))
                    $this->editor[$key]=$value;
            }
        }

        $this->found=false;
        $this->found_key=false;
        $create=false;
        $update=false;
        if (preg_match('/create/i',$options)) {
            $create=true;
        }
        if (preg_match('/update/i',$options)) {
            $update=true;
        }

        $data=$this->base_data();
        foreach($raw_data as $key=>$value) {
            if (array_key_exists($key,$data))
                $data[$key]=_trim($value);
        }



        if ($data['Product Department Code']=='' ) {
            $this->msg=_("Error: Wrong department code");
            $this->error=true;
            return;
        }

        if ($data['Product Department Name']=='') {
            $data['Product Department Name']=$data['Product Department Code'];
            $this->msg=_("Warning: No department name");
        }

        if ( !is_numeric($data['Product Department Store Key']) or $data['Product Department Store Key']<=0 ) {
            $this->error=true;
            $this->msg=_("Error: Incorrect Store Key");
            return;
        }
        $sql=sprintf("select `Product Department Key`from `Product Department Dimension` where `Product Department Store Key`=%d and `Product Department Code`=%s "
                     ,$data['Product Department Store Key']
                     ,prepare_mysql($data['Product Department Code'])
                    );
        $res=mysql_query($sql);
        if ($row=mysql_fetch_array($res)) {
            $this->found=true;
            $this->found_key=$row['Product Department Key'];

        }

        if ($this->found)
            $this->get_data('id',$this->found_key);

        if (!$this->found & $create) {
            $this->create($data);
        } else if ($create) {
            $this->msg=_('There is already another department with this code');

        }









    }


    /*
      Function: create
      Crea nuevos registros en la tabla product department dimension, evitando duplicidad de registros.
    */
    // JFA

    function create($data) {



        $this->new=false;

        if ($data['Product Department Name']!='')
            $data['Product Department Name']=$this->name_if_duplicated($data);

        $store=new Store($data['Product Department Store Key']);
        if (!$store->id) {
            $this->error=true;
            exit("error");
        }

        $data['Product Department Store Code']=$store->data['Store Code'];
	$data['Product Department Currency Code']=$store->data['Store Currency Code'];

        $keys='(';
        $values='values(';
        foreach($data as $key=>$value) {
            $keys.="`$key`,";
            $values.=prepare_mysql($value).",";
        }
        $keys=preg_replace('/,$/',')',$keys);
        $values=preg_replace('/,$/',')',$values);
        $sql=sprintf("insert into `Product Department Dimension` %s %s",$keys,$values);

        //  print "$sql\n";
        if (mysql_query($sql)) {
            $this->id = mysql_insert_id();
            $this->msg=_("Department Added");
            $this->get_data('id',$this->id,false);
            $this->new=true;

            $editor_data=$this->get_editor_data();
            $sql=sprintf("insert into `History Dimension`  (`Subject`,`Subject Key`,`Action`,`Direct Object`,`Direct Object Key`,`Preposition`,`Indirect Object`,`Indirect Object Key`,`History Abstract`,`History Details`,`History Date`,`Author Name`,`Author Key`) values (%s,%d,%s,%s,%d,%s,%s,%d,%s,%s,%s,%s,%s)   ",

                         prepare_mysql($editor_data['subject']),
                         $editor_data['subject_key'],
                         prepare_mysql('created'),
                         prepare_mysql('Department'),
                         $this->id,
                         "''",
                         "''",
                         0,
                         prepare_mysql(_('Department Created')),
                         prepare_mysql(_('Department')." ".$this->data['Product Department Name']." (".$this->get('Product Department Code').") "._('Created')),
                         prepare_mysql($editor_data['date']),
                         prepare_mysql($editor_data['author']),
                         $editor_data['author_key']
                        );
            mysql_query($sql);



            $store->update_departments();
            return;
        } else {
            $this->msg=_("$sql Error can not create department");

        }

    }

    /*
       Method: get_data
       Obtiene los datos de la tabla Product Department Dimension de acuerdo al Id, al codigo o al code_store.
    */
// JFA

    function get_data($tipo,$tag,$tag2=false) {

        switch ($tipo) {
        case('id'):
            $sql=sprintf("select * from `Product Department Dimension` where `Product Department Key`=%d ",$tag);
            break;
        case('code'):
            $sql=sprintf("select * from `Product Department Dimension` where `Product Department Code`=%s and `Product Department Most Recent`='Yes'",prepare_mysql($tag));
        case('code_store'):
            $sql=sprintf("select * from `Product Department Dimension` where `Product Department Code`=%s and `Product Department Most Recent`='Yes' and `Product Department Store Key`=%d",prepare_mysql($tag),$tag2);

            break;
        default:
            $sql=sprintf("select * from `Product Department Dimension` where `Product Department Type`='Unknown' ");
        }
        //  print "$sql\n";

        $result=mysql_query($sql);
        if ($this->data=mysql_fetch_array($result, MYSQL_ASSOC)   )
            $this->id=$this->data['Product Department Key'];

    }

    /*
        Function: update
        Funcion que permite actualizar el nombre o el codigo en la tabla Product Department Dimension, evitando registros duplicados.
    */
// JFA
    function update($key,$a1=false,$a2=false) {
        $this->updated=false;
        $this->msg='Nothing to change';

        switch ($key) {
        case('code'):

            if ($a1==$this->data['Product Department Code']) {
                $this->updated=true;
                $this->new_value=$a1;
                return;

            }

            if ($a1=='') {
                $this->msg=_('Error: Wrong code (empty)');
                return;
            }

            if (!(strtolower($a1)==strtolower($this->data['Product Department Code']) and $a1!=$this->data['Product Department Code'])) {

                $sql=sprintf("select count(*) as num from `Product Department Dimension` where `Product Department Store Key`=%d and `Product Department Code`=%s  COLLATE utf8_general_ci"
                             ,$this->data['Product Department Store Key']
                             ,prepare_mysql($a1)
                            );
                $res=mysql_query($sql);
                $row=mysql_fetch_array($res);
                if ($row['num']>0) {
                    $this->msg=_("Error: Another department with the same code");
                    return;
                }
            }
            $old_value=$this->get('Product Department Code');
            $sql=sprintf("update `Product Department Dimension` set `Product Department Code`=%s where `Product Department Key`=%d "
                         ,prepare_mysql($a1)
                         ,$this->id
                        );
            if (mysql_query($sql)) {
                $this->msg=_('Department code updated');
                $this->updated=true;
                $this->new_value=$a1;

                $this->data['Product Department Code']=$a1;
                $editor_data=$this->get_editor_data();
                $sql=sprintf("insert into `History Dimension`  (`Subject`,`Subject Key`,`Action`,`Direct Object`,`Direct Object Key`,`Preposition`,`Indirect Object`,`Indirect Object Key`,`History Abstract`,`History Details`,`History Date`,`Author Name`,`Author Key`) values (%s,%d,%s,%s,%d,%s,%s,%d,%s,%s,%s,%s,%s)   ",

                             prepare_mysql($editor_data['subject']),
                             $editor_data['subject_key'],
                             prepare_mysql('edited'),
                             prepare_mysql('Department'),
                             $this->id,
                             "''",
                             "''",
                             0,
                             prepare_mysql(_('Product Department Changed').' ('.$this->get('Product Department Name').')' ),
                             prepare_mysql(_('Store')." ".$this->data['Product Department Name']." "._('code changed from').' '.$old_value." "._('to').' '. $this->get('Product Department Code')  ),
                             prepare_mysql($editor_data['date']),
                             prepare_mysql($editor_data['author']),
                             $editor_data['author_key']
                            );
                mysql_query($sql);



            } else {
                $this->msg=_("Error: Department code could not be updated");

                $this->updated=false;

            }
            break;

        case('name'):

            if ($a1==$this->data['Product Department Name']) {
                $this->updated=true;
                $this->new_value=$a1;
                return;

            }

            if ($a1=='') {
                $this->msg=_('Error: Wrong name (empty)');
                return;
            }
            $sql=sprintf("select count(*) as num from `Product Department Dimension` where `Product Department Store Key`=%d and `Product Department Name`=%s  COLLATE utf8_general_ci"
                         ,$this->data['Product Department Store Key']
                         ,prepare_mysql($a1)
                        );
            $res=mysql_query($sql);
            $row=mysql_fetch_array($res);
            if ($row['num']>0) {
                $this->msg=_("Error: Another department with the same name");
                return;
            }
            $old_value=$this->get('Product Department Name');
            $sql=sprintf("update `Product Department Dimension` set `Product Department Name`=%s where `Product Department Key`=%d "
                         ,prepare_mysql($a1)
                         ,$this->id
                        );
            if (mysql_query($sql)) {
                $this->msg=_('Department name updated');
                $this->updated=true;
                $this->new_value=$a1;
                $this->data['Product Department Name']=$a1;
                $editor_data=$this->get_editor_data();
                $sql=sprintf("insert into `History Dimension`  (`Subject`,`Subject Key`,`Action`,`Direct Object`,`Direct Object Key`,`Preposition`,`Indirect Object`,`Indirect Object Key`,`History Abstract`,`History Details`,`History Date`,`Author Name`,`Author Key`) values (%s,%d,%s,%s,%d,%s,%s,%d,%s,%s,%s,%s,%s)   ",

                             prepare_mysql($editor_data['subject']),
                             $editor_data['subject_key'],
                             prepare_mysql('edited'),
                             prepare_mysql('Department'),
                             $this->id,
                             "''",
                             "''",
                             0,
                             prepare_mysql(_('Product Department Name Changed').' ('.$this->get('Product Department Name').')' ),
                             prepare_mysql(_('Product Department')." ("._('Code').":".$this->data['Product Department Code'].") "._('name changed from').' '.$old_value." "._('to').' '. $this->get('Product Department Name')  ),
                             prepare_mysql($editor_data['date']),
                             prepare_mysql($editor_data['author']),
                             $editor_data['author_key']
                            );
                mysql_query($sql);

            } else {
                $this->msg=_("Error: Department name could not be updated");

                $this->updated=false;

            }
            break;
        }
    }

    /*
        Function: delete
        Funcion que permite eliminar registros en la tabla Product Department Dimension, cuidando la integridad referencial con los productos.
    */
// JFA
    function delete() {
        $this->deleted=false;
        $this->load('products_info');

        if ($this->get('Total Products')==0) {
            $store=new Store($this->data['Product Department Store Key']);
            $sql=sprintf("delete from `Product Department Dimension` where `Product Department Key`=%d",$this->id);
            if (mysql_query($sql)) {

                $this->deleted=true;

            } else {

                $this->msg=_('Error: can not delete department');
                return;
            }

            $this->deleted=true;
        } else {
            $this->msg=_('Department can not be deleted because it has assosiated some products');

        }
    }

    /*
        Method: load
        Carga datos de la base de datos Product Dimension, Product Department Bridge, Product Family Dimension, Product Family Department Bridge, actualizando registros en la tabla Product Department Dimension
    */
// JFA

    function load($tipo,$args=false) {
        switch ($tipo) {

        case('families'):
            $sql=sprintf("select * from `Product Family Dimension` PFD  left join `Product Family Department Bridge` as B on (B.`Product Family Key`=PFD.`Product Family Key`) where `Product Deparment Key`=%d",$this->id);
            //  print $sql;

            $this->families=array();
            $result=mysql_query($sql);
            if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
                $this->families[$row['Product Family Key']]=$row;
            }
            break;

        case('sales'):
            $this->update_sales_data();


            break;


        }

    }

    /*
       Method: save
       Actualiza registros de la tablas product_department, product_group, graba y actualiza datos en la tabla sales
    */
// JFA

    function save($tipo) {
        switch ($tipo) {
        case('first_date'):

            if (is_numeric($this->data['first_date'])) {
                $sql=sprintf("update product_department set first_date=%s where id=%d",
                             prepare_mysql(
                                 date("Y-m-d H:i:s",strtotime('@'.$this->data['first_date'])))
                             ,$this->id);
            } else
                $sql=sprintf("update product_group set first_date=NULL where id=%d",$this->id);

            //     print "$sql;\n";
            mysql_query($sql);

            break;
        case('sales'):
            $sql=sprintf("select id from sales where tipo='dept' and tipo_id=%d",$this->id);
            $res=mysql_query($sql);
            if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
                $sales_id=$row['id'];
            } else {
                $sql=sprintf("insert into sales (tipo,tipo_id) values ('dept',%d)",$this->id);
                mysql_query($sql);
                $sales_id=$this->db->lastInsertID();

            }
            foreach($this->data['sales'] as $key=>$value) {
                if (preg_match('/^aw/',$key)) {
                    if (is_numeric($value))
                        $sql=sprintf("update sales set %s=%f where id=%d",$key,$value,$sales_id);
                    else
                        $sql=sprintf("update sales set %s=NULL where id=%d",$key,$sales_id);
                    mysql_query($sql);

                }
                if (preg_match('/^ts/',$key)) {
                    $sql=sprintf("update sales set %s=%.2f where id=%d",$key,$value,$sales_id);
                    // print "$sql\n";
                    mysql_query($sql);
                }

            }

            break;
        }

    }

    /*
       Function: get
       Obtiene informacion de los diferentes estados de los productos en el departamento
    */
// JFA

    function get($key) {

        if (array_key_exists($key,$this->data))
            return $this->data[$key];

        if (preg_match('/^(Total|1).*(Amount|Profit)$/',$key)) {

            $amount='Product Department '.$key;

            return money($this->data[$amount]);
        }
        if (preg_match('/^(Total|1).*(Quantity (Ordered|Invoiced|Delivered|)|Invoices|Pending Orders|Customers)$/',$key)) {

            $amount='Product Department '.$key;

            return number($this->data[$amount]);
        }

        switch ($key) {
        case('For Sale Products'):
	  return number($this->data['Product Department For Sale Products']);
            break;
        case('Families'):
            return number($this->data['Product Department Families']);
            break;


        case('Total Products'):
            return $this->data['Product Department For Sale Products']+$this->data['Product Department In Process Products']+$this->data['Product Department Not For Sale Products']+$this->data['Product Department Discontinued Products']+$this->data['Product Department Unknown Sales State Products'];
            break;

//   case('weeks'):
//      $_diff_seconds=date('U')-$this->data['first_date'];
//      $day_diff=$_diff_seconds/24/3600;
//      $weeks=$day_diff/7;
//      return $weeks;
        }

    }
    /*
       Method: add_product
       Agrega registros a la tabla Product Department Bridge, actualiza la tabla Product Dimension
    */
// JFA
    function add_product($product_id,$args=false) {


        $product=New Product($product_id);
        if ($product->id) {
            $sql=sprintf("insert into `Product Department Bridge` (`Product Key`,`Product Department Key`) values (%d,%d)",$product->id,$this->id);
            mysql_query($sql);
            $this->load('products_info');

            //  $sql=sprintf("select sum(if(`Product Sales State`='Unknown',1,0)) as sale_unknown, sum(if(`Product Sales State`='Discontinued',1,0)) as discontinued,sum(if(`Product Sales State`='Not for sale',1,0)) as not_for_sale,sum(if(`Product Sales State`='For sale',1,0)) as for_sale,sum(if(`Product Sales State`='In Process',1,0)) as in_process,sum(if(`Product Availability State`='Unknown',1,0)) as availability_unknown,sum(if(`Product Availability State`='Optimal',1,0)) as availability_optimal,sum(if(`Product Availability State`='Low',1,0)) as availability_low,sum(if(`Product Availability State`='Critical',1,0)) as availability_critical,sum(if(`Product Availability State`='Out Of Stock',1,0)) as availability_outofstock from `Product Dimension` P left join  `Product Department Bridge` B on (P.`Product Key`=B.`Product Key`) where `Product Department Key`=%d",$this->id);
//      //  print $sql;
//      $res = $this->db->query($sql);
//      if($row=mysql_fetch_array($result, MYSQL_ASSOC)){

//        $sql=sprintf("update `Product Department Dimension` set `Product Department For Sale Products`=%d ,`Product Department Discontinued Products`=%d ,`Product Department Not For Sale Products`=%d ,`Product Department Unknown Sales State Products`=%d, `Product Department Optimal Availability Products`=%d , `Product Department Low Availability Products`=%d ,`Product Department Critical Availability Products`=%d ,`Product Department Out Of Stock Products`=%d,`Product Department Unknown Stock Products`=%d ,`Product Department Surplus Availability Products`=%d where `Product Department Key`=%d  ",
// 		    $row['for_sale'],
// 		    $row['discontinued'],
// 		    $row['not_for_sale'],
// 		    $row['sale_unknown'],
// 		    $row['availability_optimal'],
// 		    $row['availability_low'],
// 		    $row['availability_critical'],
// 		    $row['availability_outofstock'],
// 		    $row['availability_unknown'],
// 		    $row['availability_surplus'],
// 		    $this->id
// 	    );
//        //  print "$sql\n";exit;
//        mysql_query($sql);


//      }

            if (preg_match('/principal/',$args)) {
                $sql=sprintf("update  `Product Dimension` set `Product Main Department Key`=%d ,`Product Main Department Code`=%s,`Product Main Department Name`=%s where `Product ID`=%d    "
                             ,$this->id
                             ,prepare_mysql($this->get('Product Department Code'))
                             ,prepare_mysql($this->get('Product Department Name'))
                             ,$product->pid);

                mysql_query($sql);
            }
        }
    }

    /*
       Method: add_family
       Agrega registros a la tabla Product Family Department Bridge, actualiza la tabla Product Department Dimension, Product Family Dimension
    */
// JFA

    function add_family($family_id,$args=false) {
        $family=New Family($family_id);
        if ($family->id) {
            $sql=sprintf("insert into `Product Family Department Bridge` (`Product Family Key`,`Product Department Key`) values (%d,%d)",$family->id,$this->id);
            mysql_query($sql);

            $sql=sprintf("select count(*) as num from `Product Family Department Bridge`  where `Product Department Key`=%d",$this->id);
            $result=mysql_query($sql);
            if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
                $sql=sprintf("update `Product Department Dimension` set `Product Department Families`=%d   where `Product Department Key`=%d  ",
                             $row['num'],
                             $this->id
                            );
                //  print "$sql\n";exit;
                mysql_query($sql);
            }
            if (!preg_match('/noproduct/i',$args) ) {
                foreach($family->get('products') as $key => $value) {
                    $this->add_product($key,$args);
                }
            }

            if (preg_match('/principal/',$args)) {
                $sql=sprintf("update  `Product Family Dimension` set `Product Family Main Department Key`=%d ,`Product Family Main Department Code`=%s,`Product Family Main Department Name`=%s where `Product Family Key`=%s    "
                             ,$this->id
                             ,prepare_mysql($this->get('Product Department Code'))
                             ,prepare_mysql($this->get('Product Department Name'))
                             ,$family->id);
                mysql_query($sql);
            }
        }
    }


    function update_sales_data() {
        $on_sale_days=0;

        $sql="select count(*) as prods,min(`Product For Sale Since Date`) as ffrom ,max(`Product Last Sold Date`) as tto, sum(if(`Product Sales State`='For sale',1,0)) as for_sale   from `Product Dimension`  where `Product Main Department Key`=".$this->id;

        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $from=strtotime($row['ffrom']);
            $_from=date("Y-m-d H:i:s",$from);
            if ($row['for_sale']>0) {
                $to=strtotime('today');
                $_to=date("Y-m-d H:i:s");
            } else {
                $to=strtotime($row['tto']);
                $_to=date("Y-m-d H:i:s",$to);
            }
            $on_sale_days=($to-$from)/ (60 * 60 * 24);

            if ($row['prods']==0)
                $on_sale_days=0;

        }
        $sql="select count(Distinct `Order Key`) as pending_orders   from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where  `Current Dispatching State` not in ('Unknown','Dispached','Cancelled')  and  `Product Main Department Key`=".$this->id;
        $result=mysql_query($sql);
        $pending_orders=0;
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pending_orders=$row['pending_orders'];
        }
        $sql="select    count(Distinct `Customer Key`)as customers ,count(Distinct `Invoice Key`)as invoices ,  sum(`Cost Supplier`) as cost_sup,sum(`Invoice Transaction Gross Amount`) as gross ,sum(`Invoice Transaction Total Discount Amount`)as disc ,sum(`Shipped Quantity`) as delivered,sum(`Order Quantity`) as ordered,sum(`Invoice Quantity`) as invoiced  from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where `Product Main Department Key`=".$this->id;




        $result=mysql_query($sql);

        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $this->data['Product Department Total Invoiced Gross Amount']=$row['gross'];
            $this->data['Product Department Total Invoiced Discount Amount']=$row['disc'];
            $this->data['Product Department Total Invoiced Amount']=$row['gross']-$row['disc'];

            $this->data['Product Department Total Profit']=$row['gross']-$row['disc']-$row['cost_sup'];
            $this->data['Product Department Total Quantity Ordered']=$row['ordered'];
            $this->data['Product Department Total Quantity Invoiced']=$row['invoiced'];
            $this->data['Product Department Total Quantity Delivered']=$row['delivered'];
            $this->data['Product Department Total Days On Sale']=$on_sale_days;
            $this->data['Product Department Total Customers']=$row['customers'];
            $this->data['Product Department Total Invoices']=$row['invoices'];
            $this->data['Product Department Total Pending Orders']=$pending_orders;


            $this->data['Product Department Valid From']=$_from;
            $this->data['Product Department Valid To']=$_to;
            $sql=sprintf("update `Product Department Dimension` set `Product Department Total Invoiced Gross Amount`=%s,`Product Department Total Invoiced Discount Amount`=%s,`Product Department Total Invoiced Amount`=%s,`Product Department Total Profit`=%s, `Product Department Total Quantity Ordered`=%s , `Product Department Total Quantity Invoiced`=%s,`Product Department Total Quantity Delivered`=%s ,`Product Department Total Days On Sale`=%f ,`Product Department Valid From`=%s,`Product Department Valid To`=%s ,`Product Department Total Customers`=%d,`Product Department Total Invoices`=%d,`Product Department Total Pending Orders`=%d where `Product Department Key`=%d "
                         ,prepare_mysql($this->data['Product Department Total Invoiced Gross Amount'])
                         ,prepare_mysql($this->data['Product Department Total Invoiced Discount Amount'])
                         ,prepare_mysql($this->data['Product Department Total Invoiced Amount'])
                         ,prepare_mysql($this->data['Product Department Total Profit'])
                         ,prepare_mysql($this->data['Product Department Total Quantity Ordered'])
                         ,prepare_mysql($this->data['Product Department Total Quantity Invoiced'])
                         ,prepare_mysql($this->data['Product Department Total Quantity Delivered'])
                         ,$on_sale_days
                         ,prepare_mysql($this->data['Product Department Valid From'])
                         ,prepare_mysql($this->data['Product Department Valid To'])
                         ,$this->data['Product Department Total Customers']
                         ,$this->data['Product Department Total Invoices']
                         ,$this->data['Product Department Total Pending Orders']

                         ,$this->id
                        );
            //  print "$sql\n";
            //  exit;
            if (!mysql_query($sql))
                exit("$sql\ncan not update dept sales\n");
        }
        // days on sale

        $on_sale_days=0;



        $sql="select count(*) as prods,min(`Product For Sale Since Date`) as ffrom ,max(`Product Last Sold Date`) as `to`, sum(if(`Product Sales State`='For sale',1,0)) as for_sale   from `Product Dimension` as P  where `Product Main Department Key`=".$this->id;
// print "$sql\n\n";
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            if ($row['prods']==0)
                $on_sale_days=0;
            else {


                if ($row['for_sale']>0)
                    $to=strtotime('today');
                else
                    $to=strtotime($row['to']);
                // print "*** ".$row['to']." T:$to  ".strtotime('today')."  ".strtotime('today -1 year')."  \n";
                // print "*** T:$to   ".strtotime('today -1 year')."  \n";
                if ($to>strtotime('today -1 year')) {
                    //print "caca";
                    $from=strtotime($row['ffrom']);
                    if ($from<strtotime('today -1 year'))
                        $from=strtotime('today -1 year');

                    //	    print "*** T:$to F:$from\n";
                    $on_sale_days=($to-$from)/ (60 * 60 * 24);
                } else {
                    $on_sale_days=0;

                }
            }
        }



        //$sql="select sum(`Product 1 Year Acc Invoiced Gross Amount`) as net,sum(`Product 1 Year Acc Invoiced Gross Amount`) as gross,sum(`Product 1 Year Acc Invoiced Discount Amount`) as disc, sum(`Product 1 Year Acc Profit`)as profit ,sum(`Product 1 Year Acc Quantity Delivered`) as delivered,sum(`Product 1 Year Acc Quantity Ordered`) as ordered,sum(`Product 1 Year Acc Quantity Invoiced`) as invoiced  from `Product Dimension` as P where `Product main Department Key`=".$this->id;
        $sql=sprintf("select count(Distinct `Order Key`) as pending_orders   from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where  `Current Dispatching State` not in ('Unknown','Dispached','Cancelled') 
        and  `Product Main Department Key`=%d and `Invoice Date`>=%s ",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 1 year"))));
        
        $result=mysql_query($sql);
        $pending_orders=0;
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pending_orders=$row['pending_orders'];
        }
        $sql=sprintf("select    count(Distinct `Customer Key`)as customers ,count(Distinct `Invoice Key`)as invoices ,  sum(`Cost Supplier`) as cost_sup,sum(`Invoice Transaction Gross Amount`) as gross 
        ,sum(`Invoice Transaction Total Discount Amount`)as disc ,sum(`Shipped Quantity`) as delivered,sum(`Order Quantity`) as ordered,sum(`Invoice Quantity`) as invoiced  
        from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) 
        left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where `Product Main Department Key`=%d and  `Invoice Date`>=%s",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 1 year"))));



        $result=mysql_query($sql);

        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $this->data['Product Department 1 Year Acc Invoiced Gross Amount']=$row['gross'];
            $this->data['Product Department 1 Year Acc Invoiced Discount Amount']=$row['disc'];
            $this->data['Product Department 1 Year Acc Invoiced Amount']=$row['gross']-$row['disc'];

            $this->data['Product Department 1 Year Acc Profit']=$row['gross']-$row['disc']-$row['cost_sup'];
            $this->data['Product Department 1 Year Acc Quantity Ordered']=$row['ordered'];
            $this->data['Product Department 1 Year Acc Quantity Invoiced']=$row['invoiced'];
            $this->data['Product Department 1 Year Acc Quantity Delivered']=$row['delivered'];
	    $this->data['Product Department 1 Year Acc Customers']=$row['customers'];
            $this->data['Product Department 1 Year Acc Invoices']=$row['invoices'];
            $this->data['Product Department 1 Year Acc Pending Orders']=$pending_orders;

            $sql=sprintf("update `Product Department Dimension` set `Product Department 1 Year Acc Invoiced Gross Amount`=%s,`Product Department 1 Year Acc Invoiced Discount Amount`=%s,`Product Department 1 Year Acc Invoiced Amount`=%s,`Product Department 1 Year Acc Profit`=%s, `Product Department 1 Year Acc Quantity Ordered`=%s , `Product Department 1 Year Acc Quantity Invoiced`=%s,`Product Department 1 Year Acc Quantity Delivered`=%s ,`Product Department 1 Year Acc Days On Sale`=%f  ,`Product Department 1 Year Acc Customers`=%d,`Product Department 1 Year Acc Invoices`=%d,`Product Department 1 Year Acc Pending Orders`=%d where `Product Department Key`=%d "
                         ,prepare_mysql($this->data['Product Department 1 Year Acc Invoiced Gross Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Year Acc Invoiced Discount Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Year Acc Invoiced Amount'])

                         ,prepare_mysql($this->data['Product Department 1 Year Acc Profit'])
                         ,prepare_mysql($this->data['Product Department 1 Year Acc Quantity Ordered'])
                         ,prepare_mysql($this->data['Product Department 1 Year Acc Quantity Invoiced'])
                         ,prepare_mysql($this->data['Product Department 1 Year Acc Quantity Delivered'])
                         ,$on_sale_days
			 ,$this->data['Product Department 1 Year Acc Customers']
                         ,$this->data['Product Department 1 Year Acc Invoices']
                         ,$this->data['Product Department 1 Year Acc Pending Orders']
                         ,$this->id
                        );
            //  print "$sql\n";
            if (!mysql_query($sql))
                exit("$sql\ncan not update dept sales\n");
        }
        // exit;
        $on_sale_days=0;


        $sql="select count(*) as prods,min(`Product For Sale Since Date`) as ffrom ,max(`Product Last Sold Date`) as `to`, sum(if(`Product Sales State`='For sale',1,0)) as for_sale   from `Product Dimension` as P  where `Product Main Department Key`=".$this->id;

        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            if ($row['prods']==0)
                $on_sale_days=0;
            else {


                if ($row['for_sale']>0)
                    $to=strtotime('today');
                else
                    $to=strtotime($row['to']);
                if ($to>strtotime('today -3 month')) {

                    $from=strtotime($row['ffrom']);
                    if ($from<strtotime('today -3 month'))
                        $from=strtotime('today -3 month');


                    $on_sale_days=($to-$from)/ (60 * 60 * 24);
                } else
                    $on_sale_days=0;
            }
        }

        //$sql="select sum(`Product 1 Quarter Acc Invoiced Amount`) as net,sum(`Product 1 Quarter Acc Invoiced Gross Amount`) as gross,sum(`Product 1 Quarter Acc Invoiced Discount Amount`) as disc, sum(`Product 1 Quarter Acc Profit`)as profit ,sum(`Product 1 Quarter Acc Quantity Delivered`) as delivered,sum(`Product 1 Quarter Acc Quantity Ordered`) as ordered,sum(`Product 1 Quarter Acc Quantity Invoiced`) as invoiced  from `Product Dimension` as P   where `Product Main Department Key`=".$this->id;
 $sql=sprintf("select count(Distinct `Order Key`) as pending_orders   from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where  `Current Dispatching State` not in ('Unknown','Dispached','Cancelled') 
        and  `Product Main Department Key`=%d and `Invoice Date`>=%s ",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 1 month"))));
        
        $result=mysql_query($sql);
        $pending_orders=0;
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pending_orders=$row['pending_orders'];
        }
        $sql=sprintf("select    count(Distinct `Customer Key`)as customers ,count(Distinct `Invoice Key`)as invoices ,  sum(`Cost Supplier`) as cost_sup,sum(`Invoice Transaction Gross Amount`) as gross 
        ,sum(`Invoice Transaction Total Discount Amount`)as disc ,sum(`Shipped Quantity`) as delivered,sum(`Order Quantity`) as ordered,sum(`Invoice Quantity`) as invoiced  
        from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) 
        left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where `Product Main Department Key`=%d and  `Invoice Date`>=%s",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 3 month"))));

        $result=mysql_query($sql);

        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $this->data['Product Department 1 Quarter Acc Invoiced Gross Amount']=$row['gross'];
            $this->data['Product Department 1 Quarter Acc Invoiced Discount Amount']=$row['disc'];
            $this->data['Product Department 1 Quarter Acc Invoiced Amount']=$row['gross']-$row['disc'];

            $this->data['Product Department 1 Quarter Acc Profit']=$row['gross']-$row['disc']-$row['cost_sup'];
            $this->data['Product Department 1 Quarter Acc Quantity Ordered']=$row['ordered'];
            $this->data['Product Department 1 Quarter Acc Quantity Invoiced']=$row['invoiced'];
            $this->data['Product Department 1 Quarter Acc Quantity Delivered']=$row['delivered'];
	    $this->data['Product Department 1 Quarter Acc Customers']=$row['customers'];
            $this->data['Product Department 1 Quarter Acc Invoices']=$row['invoices'];
            $this->data['Product Department 1 Quarter Acc Pending Orders']=$pending_orders;

            $sql=sprintf("update `Product Department Dimension` set `Product Department 1 Quarter Acc Invoiced Gross Amount`=%s,`Product Department 1 Quarter Acc Invoiced Discount Amount`=%s,`Product Department 1 Quarter Acc Invoiced Amount`=%s,`Product Department 1 Quarter Acc Profit`=%s, `Product Department 1 Quarter Acc Quantity Ordered`=%s , `Product Department 1 Quarter Acc Quantity Invoiced`=%s,`Product Department 1 Quarter Acc Quantity Delivered`=%s  ,`Product Department 1 Quarter Acc Days On Sale`=%f  ,`Product Department 1 Quarter Acc Customers`=%d,`Product Department 1 Quarter Acc Invoices`=%d,`Product Department 1 Quarter Acc Pending Orders`=%d where `Product Department Key`=%d "
                         ,prepare_mysql($this->data['Product Department 1 Quarter Acc Invoiced Gross Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Quarter Acc Invoiced Discount Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Quarter Acc Invoiced Amount'])

                         ,prepare_mysql($this->data['Product Department 1 Quarter Acc Profit'])
                         ,prepare_mysql($this->data['Product Department 1 Quarter Acc Quantity Ordered'])
                         ,prepare_mysql($this->data['Product Department 1 Quarter Acc Quantity Invoiced'])
                         ,prepare_mysql($this->data['Product Department 1 Quarter Acc Quantity Delivered'])
                         ,$on_sale_days
			 ,$this->data['Product Department 1 Quarter Acc Customers']
                         ,$this->data['Product Department 1 Quarter Acc Invoices']
                         ,$this->data['Product Department 1 Quarter Acc Pending Orders']
                         ,$this->id
                        );
            // print "$sql\n";
            if (!mysql_query($sql))
                exit("$sql\ncan not update dept sales\n");
        }

        $on_sale_days=0;

        $sql="select count(*) as prods,min(`Product For Sale Since Date`) as ffrom ,max(`Product Last Sold Date`) as `to`, sum(if(`Product Sales State`='For sale',1,0)) as for_sale   from `Product Dimension` as P where `Product Main Department Key`=".$this->id;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            if ($row['prods']==0)
                $on_sale_days=0;
            else {


                if ($row['for_sale']>0)
                    $to=strtotime('today');
                else
                    $to=strtotime($row['to']);
                if ($to>strtotime('today -1 month')) {

                    $from=strtotime($row['ffrom']);
                    if ($from<strtotime('today -1 month'))
                        $from=strtotime('today -1 month');


                    $on_sale_days=($to-$from)/ (60 * 60 * 24);
                } else
                    $on_sale_days=0;
            }
        }

        //$sql="select  sum(`Product 1 Month Acc Invoiced Amount`) as net,sum(`Product 1 Month Acc Invoiced Gross Amount`) as gross,sum(`Product 1 Month Acc Invoiced Discount Amount`) as disc, sum(`Product 1 Month Acc Profit`)as profit ,sum(`Product 1 Month Acc Quantity Delivered`) as delivered,sum(`Product 1 Month Acc Quantity Ordered`) as ordered,sum(`Product 1 Month Acc Quantity Invoiced`) as invoiced  from `Product Dimension` as P  where `Product Main Department Key`=".$this->id;
  $sql=sprintf("select count(Distinct `Order Key`) as pending_orders   from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where  `Current Dispatching State` not in ('Unknown','Dispached','Cancelled') 
        and  `Product Main Department Key`=%d and `Invoice Date`>=%s ",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 1 month"))));
        
        $result=mysql_query($sql);
        $pending_orders=0;
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pending_orders=$row['pending_orders'];
        }
        $sql=sprintf("select    count(Distinct `Customer Key`)as customers ,count(Distinct `Invoice Key`)as invoices ,  sum(`Cost Supplier`) as cost_sup,sum(`Invoice Transaction Gross Amount`) as gross 
        ,sum(`Invoice Transaction Total Discount Amount`)as disc ,sum(`Shipped Quantity`) as delivered,sum(`Order Quantity`) as ordered,sum(`Invoice Quantity`) as invoiced  
        from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) 
        left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where `Product Main Department Key`=%d and  `Invoice Date`>=%s",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 1 month"))));

        $result=mysql_query($sql);

        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $this->data['Product Department 1 Month Acc Invoiced Gross Amount']=$row['gross'];
            $this->data['Product Department 1 Month Acc Invoiced Discount Amount']=$row['disc'];
            $this->data['Product Department 1 Month Acc Invoiced Amount']=$row['gross']-$row['disc'];

            $this->data['Product Department 1 Month Acc Profit']=$row['gross']-$row['disc']-$row['cost_sup'];
            $this->data['Product Department 1 Month Acc Quantity Ordered']=$row['ordered'];
            $this->data['Product Department 1 Month Acc Quantity Invoiced']=$row['invoiced'];
            $this->data['Product Department 1 Month Acc Quantity Delivered']=$row['delivered'];
	    $this->data['Product Department 1 Month Acc Customers']=$row['customers'];
            $this->data['Product Department 1 Month Acc Invoices']=$row['invoices'];
            $this->data['Product Department 1 Month Acc Pending Orders']=$pending_orders;

            $sql=sprintf("update `Product Department Dimension` set `Product Department 1 Month Acc Invoiced Gross Amount`=%s,`Product Department 1 Month Acc Invoiced Discount Amount`=%s,`Product Department 1 Month Acc Invoiced Amount`=%s,`Product Department 1 Month Acc Profit`=%s, `Product Department 1 Month Acc Quantity Ordered`=%s , `Product Department 1 Month Acc Quantity Invoiced`=%s,`Product Department 1 Month Acc Quantity Delivered`=%s  ,`Product Department 1 Month Acc Days On Sale`=%f ,`Product Department 1 Month Acc Customers`=%d,`Product Department 1 Month Acc Invoices`=%d,`Product Department 1 Month Acc Pending Orders`=%d where `Product Department Key`=%d "
                         ,prepare_mysql($this->data['Product Department 1 Month Acc Invoiced Gross Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Month Acc Invoiced Discount Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Month Acc Invoiced Amount'])

                         ,prepare_mysql($this->data['Product Department 1 Month Acc Profit'])
                         ,prepare_mysql($this->data['Product Department 1 Month Acc Quantity Ordered'])
                         ,prepare_mysql($this->data['Product Department 1 Month Acc Quantity Invoiced'])
                         ,prepare_mysql($this->data['Product Department 1 Month Acc Quantity Delivered'])
                         ,$on_sale_days
	 ,$this->data['Product Department 1 Month Acc Customers']
                         ,$this->data['Product Department 1 Month Acc Invoices']
                         ,$this->data['Product Department 1 Month Acc Pending Orders']
                         ,$this->id

                        );
            // print "$sql\n";
            if (!mysql_query($sql))
                exit("$sql\ncan not update dept sales\n");
        }

        $on_sale_days=0;
        $sql="select count(*) as prods,min(`Product For Sale Since Date`) as ffrom ,max(`Product Last Sold Date`) as `to`, sum(if(`Product Sales State`='For sale',1,0)) as for_sale   from `Product Dimension` as P   where `Product Main Department Key`=".$this->id;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            if ($row['prods']==0)
                $on_sale_days=0;
            else {


                if ($row['for_sale']>0)
                    $to=strtotime('today');
                else
                    $to=strtotime($row['to']);
                if ($to>strtotime('today -1 week')) {

                    $from=strtotime($row['ffrom']);
                    if ($from<strtotime('today -1 week'))
                        $from=strtotime('today -1 week');


                    $on_sale_days=($to-$from)/ (60 * 60 * 24);
                } else
                    $on_sale_days=0;
            }
        }



	// $sql="select sum(`Product 1 Week Acc Invoiced Amount`) as net,sum(`Product 1 Week Acc Invoiced Gross Amount`) as gross,sum(`Product 1 Week Acc Invoiced Discount Amount`) as disc, sum(`Product 1 Week Acc Profit`)as profit ,sum(`Product 1 Week Acc Quantity Delivered`) as delivered,sum(`Product 1 Week Acc Quantity Ordered`) as ordered,sum(`Product 1 Week Acc Quantity Invoiced`) as invoiced  from `Product Dimension` as P   where `Product Main Department Key`=".$this->id;
  $sql=sprintf("select count(Distinct `Order Key`) as pending_orders   from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where  `Current Dispatching State` not in ('Unknown','Dispached','Cancelled') 
        and  `Product Main Department Key`=%d and `Invoice Date`>=%s ",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 1 week"))));
        
        $result=mysql_query($sql);
        $pending_orders=0;
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $pending_orders=$row['pending_orders'];
        }
        $sql=sprintf("select    count(Distinct `Customer Key`)as customers ,count(Distinct `Invoice Key`)as invoices ,  sum(`Cost Supplier`) as cost_sup,sum(`Invoice Transaction Gross Amount`) as gross 
        ,sum(`Invoice Transaction Total Discount Amount`)as disc ,sum(`Shipped Quantity`) as delivered,sum(`Order Quantity`) as ordered,sum(`Invoice Quantity`) as invoiced  
        from `Order Transaction Fact`  OTF left join    `Product History Dimension` as PH  on (OTF.`Product Key`=PH.`Product Key`) 
        left join `Product Dimension` P on (PH.`Product ID`=P.`Product ID`)   where `Product Main Department Key`=%d and  `Invoice Date`>=%s",$this->id,prepare_mysql(date("Y-m-d",strtotime("- 1 week"))));


        $result=mysql_query($sql);

        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $this->data['Product Department 1 Week Acc Invoiced Gross Amount']=$row['gross'];
            $this->data['Product Department 1 Week Acc Invoiced Discount Amount']=$row['disc'];
            $this->data['Product Department 1 Week Acc Invoiced Amount']=$row['gross']-$row['disc'];
            $this->data['Product Department 1 Week Acc Profit']=$row['gross']-$row['disc']-$row['cost_sup'];
            $this->data['Product Department 1 Week Acc Quantity Ordered']=$row['ordered'];

            $this->data['Product Department 1 Week Acc Quantity Ordered']=$row['ordered'];
            $this->data['Product Department 1 Week Acc Quantity Invoiced']=$row['invoiced'];
            $this->data['Product Department 1 Week Acc Quantity Delivered']=$row['delivered'];
   $this->data['Product Department 1 Week Acc Customers']=$row['customers'];
            $this->data['Product Department 1 Week Acc Invoices']=$row['invoices'];
            $this->data['Product Department 1 Week Acc Pending Orders']=$pending_orders;

            $sql=sprintf("update `Product Department Dimension` set `Product Department 1 Week Acc Invoiced Gross Amount`=%s,`Product Department 1 Week Acc Invoiced Discount Amount`=%s,`Product Department 1 Week Acc Invoiced Amount`=%s,`Product Department 1 Week Acc Profit`=%s, `Product Department 1 Week Acc Quantity Ordered`=%s , `Product Department 1 Week Acc Quantity Invoiced`=%s,`Product Department 1 Week Acc Quantity Delivered`=%s ,`Product Department 1 Week Acc Days On Sale`=%f ,`Product Department 1 Week Acc Customers`=%d,`Product Department 1 Week Acc Invoices`=%d,`Product Department 1 Week Acc Pending Orders`=%d  where `Product Department Key`=%d "
                         ,prepare_mysql($this->data['Product Department 1 Week Acc Invoiced Gross Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Week Acc Invoiced Discount Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Week Acc Invoiced Amount'])
                         ,prepare_mysql($this->data['Product Department 1 Week Acc Profit'])
                         ,prepare_mysql($this->data['Product Department 1 Week Acc Quantity Ordered'])
                         ,prepare_mysql($this->data['Product Department 1 Week Acc Quantity Invoiced'])
                         ,prepare_mysql($this->data['Product Department 1 Week Acc Quantity Delivered'])
                         ,$on_sale_days
	 ,$this->data['Product Department 1 Week Acc Customers']
                         ,$this->data['Product Department 1 Week Acc Invoices']
                         ,$this->data['Product Department 1 Week Acc Pending Orders']
                         ,$this->id
                        );
            // print "$sql\n";
            if (!mysql_query($sql))
                exit("$sql\ncan not update dept sales\n");

        }
    }
    function name_if_duplicated($data) {

        $sql=sprintf("select * from `Product Department Dimension` where `Product Department Name`=%s  and `Product Department Store Key`=%d "
                     ,prepare_mysql($data['Product Department Name'])
                     ,$data['Product Department Store Key']
                    );

        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $s_char=$row['Product Department Name'];
            $number=1;
            $sql=sprintf("select * from `Product Department Dimension` where `Product Department Name` like '%s (%%)'  and `Product Department Store Key`=%d "
                         ,addslashes($data['Product Department Name'])
                         ,$data['Product Department Store Key']
                        );
            $result2=mysql_query($sql);

            while ($row2=mysql_fetch_array($result2, MYSQL_ASSOC)) {

                if (preg_match('/\(\d+\)$/',$row2['Product Department Name'],$match))
                    $_number=preg_replace('/[^\d]/','',$match[0]);
                if ($_number>$number)
                    $number=$_number;
            }

            $number++;

            return $data['Product Department Name']." ($number)";

        } else {
            return $data['Product Department Name'];
        }


    }


    function update_product_data() {
        $sql=sprintf("select sum(if(`Product Sales State`='Unknown',1,0)) as sale_unknown, sum(if(`Product Sales State`='Discontinued',1,0)) as discontinued,sum(if(`Product Sales State`='Not for sale',1,0)) as not_for_sale,sum(if(`Product Sales State`='For sale',1,0)) as for_sale,sum(if(`Product Record Type`='In Process',1,0)) as in_process,sum(if(`Product Availability State`='Unknown',1,0)) as availability_unknown,sum(if(`Product Availability State`='Optimal',1,0)) as availability_optimal,sum(if(`Product Availability State`='Low',1,0)) as availability_low,sum(if(`Product Availability State`='Critical',1,0)) as availability_critical,sum(if(`Product Availability State`='Surplus',1,0)) as availability_surplus,sum(if(`Product Availability State`='Out Of Stock',1,0)) as availability_outofstock from `Product Dimension` P  where `Product Main Department Key`=%d",$this->id);
        //print "$sql\n\n\n";
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

            $sql=sprintf("update `Product Department Dimension` set `Product Department In Process Products`=%d,`Product Department For Sale Products`=%d ,`Product Department Discontinued Products`=%d ,`Product Department Not For Sale Products`=%d ,`Product Department Unknown Sales State Products`=%d, `Product Department Optimal Availability Products`=%d , `Product Department Low Availability Products`=%d ,`Product Department Critical Availability Products`=%d ,`Product Department Out Of Stock Products`=%d,`Product Department Unknown Stock Products`=%d ,`Product Department Surplus Availability Products`=%d where `Product Department Key`=%d  ",
                         $row['in_process'],
                         $row['for_sale'],
                         $row['discontinued'],
                         $row['not_for_sale'],
                         $row['sale_unknown'],
                         $row['availability_optimal'],
                         $row['availability_low'],
                         $row['availability_critical'],
                         $row['availability_outofstock'],
                         $row['availability_unknown'],
                         $row['availability_surplus'],
                         $this->id
                        );

            mysql_query($sql);



        }




        $this->get_data('id',$this->id);
    }

    function update_families() {
        $sql=sprintf("select count(*) as num from `Product Family Dimension`  where `Product Family Main Department Key`=%d",$this->id);
        //print $sql;
        $result=mysql_query($sql);
        if ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
            $this->data['Product Department Families']=$row['num'];
            $sql=sprintf("update `Product Department Dimension` set `Product Department Families`=%d  where `Product Department Key`=%d  ",
                         $this->data['Product Department Families'],
                         $this->id
                        );
            //  print "$sql\n";
            mysql_query($sql);
        }
    }

}

?>
