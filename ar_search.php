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

case('all'):
    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string')
                         ));
    $data['user']=$user;

    search_full_text($data);
    break;
case('search'):
    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string')
                         ));
    $data['user']=$user;
    search($data);
    break;
case('products'):
    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string')
                                 ,'scope'=>array('type'=>'string')
                         ));
    $data['user']=$user;
    search_products($data);
    break;
case('part'):
case('parts'):

    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string')
                         ));
    $data['user']=$user;
    search_parts($data);
    break;



case('locations'):
    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string')
                                 ,'scope'=>array('type'=>'string')
                         ));
    $data['user']=$user;
    search_locations($data);
    break;


    break;
case('orders'):
case('orders_store'):

    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string')
                                 ,'scope'=>array('type'=>'string')
                         ));
    $data['user']=$user;
    search_orders($data);
    break;
case('customer_name'):
    search_customer_name($user);
    break;
case('customer'):
case('customers'):

//print_r($_REQUEST);
    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string')
                                 ,'scope'=>array('type'=>'string')
                         ));
    $data['user']=$user;
    search_customer($data);
    break;
case('departments'):

    $data=prepare_values($_REQUEST,array(
                             'q'=>array('type'=>'string'),
                             'scope'=>array('type'=>'string'),
                             'scope_key'=>array('type'=>'number')
                         ));
    $data['user']=$user;
    search_departments($data);
    break;
default:
    $response=array('state'=>404,'resp'=>"Operation not found $tipo");
    echo json_encode($response);

}


function search($data) {


    $q=$data['q'];
    $conf=$_SESSION['state']['search']['table'];

    $conf_table='search';


    if (isset( $_REQUEST['sf'])) {
        $start_from=$_REQUEST['sf'];


    } else
        $start_from=$conf['sf'];
    if (isset( $_REQUEST['nr'])) {
        $number_results=$_REQUEST['nr'];
        if ($start_from>0) {
            $page=floor($start_from/$number_results);
            $start_from=$start_from-$page;
        }

    } else
        $number_results=$conf['nr'];
    if (isset( $_REQUEST['o']))
        $order=$_REQUEST['o'];
    else
        $order=$conf['order'];
    if (isset( $_REQUEST['od']))
        $order_dir=$_REQUEST['od'];
    else
        $order_dir=$conf['order_dir'];
    $order_direction=(preg_match('/desc/',$order_dir)?'desc':'');
    if (isset( $_REQUEST['where']))

        if (isset( $_REQUEST['f_field']))
            $f_field=$_REQUEST['f_field'];
        else
            $f_field=$conf['f_field'];

    if (isset( $_REQUEST['f_value']))
        $f_value=$_REQUEST['f_value'];
    else
        $f_value=$conf['f_value'];


    if (isset( $_REQUEST['tableid']))
        $tableid=$_REQUEST['tableid'];
    else
        $tableid=0;


    $where='';




    $_SESSION['state'][$conf_table]['table']['order']=$order;
    $_SESSION['state'][$conf_table]['table']['order_dir']=$order_direction;
    $_SESSION['state'][$conf_table]['table']['nr']=$number_results;
    $_SESSION['state'][$conf_table]['table']['sf']=$start_from;
    $_SESSION['state'][$conf_table]['table']['where']=$where;
    $_SESSION['state'][$conf_table]['table']['f_field']=$order;
    $_SESSION['state'][$conf_table]['table']['f_value']=$f_value;


    $filter_msg='';
    $wheref='';

    /*
        $sql=sprintf("select count(*) as total  from `Search Full Text Dimension`  S left join `Store Dimension` SD on (SD.`Store Key`=S.`Store Key`) where match (`First Search Full Text`,`Second Search Full Text`) AGAINST ('%s' IN NATURAL LANGUAGE MODE) ",addslashes($q),addslashes($q));;

        // print $sql;

        $res = mysql_query($sql);
        if ($row=mysql_fetch_array($res)) {
            $total=$row['total'];
        }
        mysql_free_result($res);
    //   if($wheref==''){
        $filtered=0;
        $total_records=$total;
    //  }


        $rtext=$total_records." ".ngettext('match','matches',$total_records);
        if ($total_records>$number_results)
            $rtext_rpp=sprintf(" (%d%s)",$number_results,_('rpp'));
        else
            $rtext_rpp=' ('._('Showing all').')';

    */
    $rtext_rpp='';
    $rtext='';
    $total_records=0;
    $_dir=$order_direction;
    $_order=$order;
//------------------------------------
    $adata=array();
    $ascore=array();
    $q_parts=preg_split('/\s+/',$q);
    foreach($q_parts as $q_part) {
        $sql=sprintf("select `Search Full Text Key`,S.`Store Key`,`Store Code`,`Subject`,`Subject Key`,`Search Full Text Key`,`Search Result Name`,`Search Result Description`,`Search Result Image`   from `Search Full Text Dimension` S left join `Store Dimension` SD on (SD.`Store Key`=S.`Store Key`)    where `Search Result Name`='%s' limit 20",addslashes($q_part));;
        // print $sql;
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {

            $score=1;

            $total_records++;
            switch ($row['Subject']) {
            case 'Product':
                $subject=_('Product');
                $result_name=sprintf('<a href="product.php?id=%d">%s</a>',$row['Subject Key'],$row['Search Result Name']);
                break;
            default:
                $subject=$row['Subject'];
                $result_name=$row['Search Result Name'];
                break;
            }



            $store=sprintf('<a href="store.php?id=%d">%s</a>',$row['Store Key'],$row['Store Code']);

            if (array_key_exists($row['Search Full Text Key'],  $ascore)) {
                $ascore[$row['Search Full Text Key']]+=$store;
            } else {
                $ascore[$row['Search Full Text Key']]=$store;
            }

            $adata[$row['Search Full Text Key']]=array(
                                                     'score'=>$score,
                                                     'store'=>$store,
                                                     'subject'=>$subject,
                                                     'result'=>$result_name,
                                                     'score'=>$ascore[$row['Search Full Text Key']],
                                                     'description'=>$row['Search Result Description']

                                                 );


        }
    }




//-------------------------------



    $sql=sprintf("select S.`Store Key`,`Store Code`,`Subject`,`Subject Key`,`Search Full Text Key`,`Search Result Name`,`Search Result Description`,`Search Result Image`, match (`First Search Full Text`,`Second Search Full Text`) AGAINST ('%s' IN NATURAL LANGUAGE MODE) as score   from `Search Full Text Dimension`  S left join `Store Dimension` SD on (SD.`Store Key`=S.`Store Key`) where match (`First Search Full Text`,`Second Search Full Text`) AGAINST ('%s' IN NATURAL LANGUAGE MODE)ORDER BY  score desc ",addslashes($q),addslashes($q));;

// print $sql;
    $res = mysql_query($sql);




    while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
        $total_records++;
        switch ($row['Subject']) {
        case 'Product':
            $subject=_('Product');
            $result_name=sprintf('<a href="product.php?id=%d">%s</a>',$row['Subject Key'],$row['Search Result Name']);
            break;
        default:
            $subject=$row['Subject'];
            $result_name=$row['Search Result Name'];
            break;
        }




        $store=sprintf('<a href="store.php?id=%d">%s</a>',$row['Store Key'],$row['Store Code']);

        if (array_key_exists($row['Search Full Text Key'],  $ascore)) {
            $ascore[$row['Search Full Text Key']]+=$store;
        } else {
            $ascore[$row['Search Full Text Key']]=$store;
        }


        $adata[$row['Search Full Text Key']]=array(
                                                 'score'=>$ascore[$row['Search Full Text Key']],
                                                 'store'=>$store,
                                                 'subject'=>$subject,
                                                 'result'=>$result_name,

                                                 'description'=>$row['Search Result Description']

                                             );
    }
    mysql_free_result($res);

    array_multisort($ascore, SORT_DESC , $adata);


    $response=array('resultset'=>
                                array('state'=>200,
                                      'data'=>$adata,
                                      'sort_key'=>$_order,
                                      'sort_dir'=>$_dir,
                                      'tableid'=>$tableid,
                                      'filter_msg'=>$filter_msg,
                                      'rtext'=>$rtext,
                                      'rtext_rpp'=>$rtext_rpp,
                                      'total_records'=>$total_records,
                                      'records_offset'=>$start_from,
                                      'records_perpage'=>$number_results,
                                     )
                   );
    echo json_encode($response);
}

function search_departments($data) {
    $the_results=array();

    $max_results=10;
    $user=$data['user'];
    $q=$data['q'];


    if ($q=='') {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }


    if ($data['scope']=='store') {
        $stores=$_SESSION['state']['store']['id'];

    }
    if ($data['scope']=='store_key') {
        if ($data['scope_key'])
            $stores=$data['scope_key'];
        else
            $stores=join(',',$user->stores);

    }  else
        $stores=join(',',$user->stores);


    if (!$stores) {
        $response=array('state'=>200,'results'=>0,'data'=>'','mgs'=>'Store Error');
        echo json_encode($response);
        return;
    }


    $sql=sprintf('select `Product Department Key`,`Product Department Code`,`Product Department Name` from   `Product Department Dimension` where `Product Department Store Key` in (%s) and ( `Product Department Code` like "%s%%"  or `Product Department Name` REGEXP "[[:<:]]%s"    )  ',
                 $stores,
                 addslashes($q),
                 addslashes($q)
                );
// print $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_assoc($res)) {
        $the_results[]=array(
                           'key'=>$row['Product Department Key'],
                           'name'=>$row['Product Department Name'],
                           'code'=>$row['Product Department Code']

                       );
    }


    $response=array('state'=>200,'data'=>$the_results);
    echo json_encode($response);

}


function search_customer_by_parts($data) {

    $user=$data['user'];
    $q=$data['q'];

    if ($data['scope']=='store') {
        $stores=$_SESSION['state']['customers']['store'];

    } else
        $stores=join(',',$user->stores);

    $total_found=0;
    $emails_found=0;
    $emails_results='<table>';
    if (strlen($q)>3 or preg_match('/@/',$q)) {
        $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Main Plain Email` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Main Plain Email` like "%s%%"  limit 5'
                     ,$stores
                     ,addslashes($q)
                    );
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
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
    if (strlen($q)>2) {
        $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Name`  REGEXP "[[:<:]]%s"   limit 5'
                     ,$stores
                     ,addslashes($q)
                    );
        // print $sql;
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
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
    if (strlen($q)>2) {
        $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Main Contact Name` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Main Contact Name` REGEXP "[[:<:]]%s"   and `Customer Type`="Company" limit 5'
                     ,$stores
                     ,addslashes($q)
                    );

        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
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
    if (strlen($q)>2) {

        if (is_numeric($q)) {
            $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Tax Number` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Tax Number` like "%%%s%%"  limit 5'
                         ,$stores
                         ,$q
                        );
        } else {
            $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Tax Number` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Tax Number` REGEXP "[[:<:]]%s"  limit 5'
                         ,$stores
                         ,addslashes($q)
                        );
        }

        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
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
    if (strlen($q)>1) {
        $sql=sprintf('select `Customer Key`,`Customer Name`,`Customer Main Postal Code`,`Customer Main Location` from `Customer Dimension` where `Customer Store Key` in (%s) and  `Customer Main Postal Code` like "%s%%"  limit 5'
                     ,$stores
                     ,addslashes($q)
                    );
        // print $sql;
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
            $result=sprintf('<tr><td class="aright"><a href="customer.php?id=%d">%s</a> %s <b>%s</b></td></tr>',$row['Customer Key'],$row['Customer Name'],$row['Customer Main Location'],$row['Customer Main Postal Code']);
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



function search_orders($data) {
    $max_results=10;
    $user=$data['user'];
    $q=$data['q'];

    if ($q=='') {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        exit;
    }

    $candidates=array();

    if ($data['scope']=='store') {
        $stores=$_SESSION['state']['orders']['store'];

    } else
        $stores=join(',',$user->stores);

    $sql=sprintf("select `Order Customer Name`,`Order Currency`,`Order Balance Total Amount`,`Order Key`,`Order Public ID`,`Order Current XHTML State` from `Order Dimension` where   `ORder Store Key` in (%s)  and `Order Public ID` like '%s%%' order by `Order Date` desc  limit 10",$stores,addslashes($q));
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {
        if ($row['Order Public ID']==$q) {
            $candidates[$row['Order Key']]=110;
            $candidates_data[$row['Order Key']]=array(
                                                    'public_id'=>$row['Order Public ID'],
                                                    'state'=>$row['Order Current XHTML State'],
                                                    'balance'=>money($row['Order Balance Total Amount'],$row['Order Currency']),
                                                    'customer'=>$row['Order Customer Name']
                                                );
        } else {
            $candidates[$row['Order Key']]=100;
            $candidates_data[$row['Order Key']]=array('public_id'=>$row['Order Public ID'],'state'=>$row['Order Current XHTML State'],
                                                'balance'=>money($row['Order Balance Total Amount'],$row['Order Currency']),
                                                'customer'=>$row['Order Customer Name']
                                                     );

        }

    }

    arsort($candidates);
    $total_candidates=count($candidates);

    if ($total_candidates==0) {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }


    $counter=0;
    $customer_keys='';

    $results=array();


    foreach($candidates as $key=>$val) {
        $counter++;
        $results[$key]=$candidates_data[$key];
        if ($counter>$max_results)
            break;
    }


    $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'order.php?id=');
    echo json_encode($response);
}


function search_customer($data) {


    $max_results=10;

    $user=$data['user'];
    $q=html_entity_decode($data['q']);
    // $q=_trim($_REQUEST['q']);
  
    if ($q=='') {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);

    }

    if ($data['scope']=='store') {
        $stores=$_SESSION['state']['customers']['store'];

    } else
        $stores=join(',',$user->stores);

    $candidates=array();

    if (is_numeric($q)) {
        $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and `Customer Key`=%d',
                     $stores,$q);
        //print $sql;
        $res=mysql_query($sql);
        if ($row=mysql_fetch_array($res)) {

            $candidates[$row['Customer Key']]=2000;


        }
    }
    
    $q_just_numbers=preg_replace('/[^\d]/','',$q);
    if(strlen($q_just_numbers)>4 and strlen($q_just_numbers)<=6){
    
     $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and `Customer Main Plain Telephone` like "%s%%"  ',
                     $stores,
                     $q_just_numbers
                     );
        $res=mysql_query($sql);
        if ($row=mysql_fetch_array($res)) {
            $candidates[$row['Customer Key']]=100;
        }
            $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and `Customer Main Plain Mobile` like "%s%%"  ',
                     $stores,
                     $q_just_numbers
                     );
        $res=mysql_query($sql);
        if ($row=mysql_fetch_array($res)) {
            $candidates[$row['Customer Key']]=100;
        }
    }if(strlen($q_just_numbers)>6){
    
     $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and `Customer Main Plain Telephone` like "%%%s%%"  ',
                     $stores,
                     $q_just_numbers
                     );
        $res=mysql_query($sql);
        if ($row=mysql_fetch_array($res)) {
            $candidates[$row['Customer Key']]=100;
        }
            $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and `Customer Main Plain Mobile` like "%%%s%%"  ',
                     $stores,
                     $q_just_numbers
                     );
        $res=mysql_query($sql);
        if ($row=mysql_fetch_array($res)) {
            $candidates[$row['Customer Key']]=100;
        }
    }


    $sql=sprintf('select `Customer Key`,`Customer Name` from `Customer Dimension` where `Customer Store Key` in (%s) and `Customer Name`   REGEXP "[[:<:]]%s" limit 100 ',$stores,$q);
    //print $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {
        if ($row['Customer Name']==$q)
            $candidates[$row['Customer Key']]=110;
        else {

            $len_name=strlen($row['Customer Name']);
            $len_q=strlen($q);
            $factor=$len_q/$len_name;
            $candidates[$row['Customer Key']]=100*$factor;
        }
    }

    $sql=sprintf('select `Subject Key`,`Email` from `Email Bridge` EB  left join `Email Dimension` E on (EB.`Email Key`=E.`Email Key`) left join `Customer Dimension` CD on (CD.`Customer Key`=`Subject Key`)  where `Customer Store Key` in (%s)  and `Subject Type`="Customer" and  `Email`  like "%s%%" limit 100 ',$stores,$q);
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {
        if ($row['Email']==$q) {

            $candidates[$row['Subject Key']]=120;
        } else {

            $len_name=strlen($row['Email']);
            $len_q=strlen($q);
            $factor=$len_q/$len_name;
            $candidates[$row['Subject Key']]=100*$factor;
        }
    }
    //print_r($candidates);




    $sql=sprintf('select `Customer Key`,`Customer Main Postal Code` from `Customer Dimension` where `Customer Store Key` in (%s) and   `Customer Main Postal Code`!="" and   `Customer Main Postal Code` like "%s%%"  limit 150'
                 ,$stores
                 ,addslashes($q)
                );
    // print $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {

        if ($row['Customer Main Postal Code']==$q) {

            $candidates[$row['Customer Key']]=50;
        } else {

            $len_name=$row['Customer Main Postal Code'];
            $len_q=strlen($q);
            $factor=$len_q/$len_name;


            $candidates[$row['Customer Key']]=20*$factor;
        }

    }




    $sql=sprintf('select `Subject Key`,`Contact Name` from `Contact Bridge` EB  left join `Contact Dimension` E on (EB.`Contact Key`=E.`Contact Key`) left join `Customer Dimension` CD on (CD.`Customer Key`=`Subject Key`)  where `Customer Store Key` in (%s)  and `Subject Type`="Customer" and  `Contact Name`  REGEXP "[[:<:]]%s"  limit 100 ',$stores,$q);
//rint $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {
        if ($row['Contact Name']==$q) {

            $candidates[$row['Subject Key']]=120;
        } else {

            $len_name=$row['Contact Name'];
            $len_q=strlen($q);
            $factor=$len_name/$len_q;
            $candidates[$row['Subject Key']]=100*$factor;
        }
    }

//print_r($candidates);

    arsort($candidates);
    $total_candidates=count($candidates);

    if ($total_candidates==0) {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }


    $counter=0;
    $customer_keys='';

    $results=array();


    foreach($candidates as $key=>$val) {
        $counter++;
        $customer_keys.=','.$key;
        $results[$key]='';
        if ($counter>$max_results)
            break;
    }
    $customer_keys=preg_replace('/^,/','',$customer_keys);

    $sql=sprintf("select `Customer Main Email Key`, `Customer Main XHTML Telephone`,`Customer Main Telephone Key`,`Customer Main Postal Code`,`Customer Key`,`Customer Main Contact Name`,`Customer Name`,`Customer Type`,`Customer Main Plain Email`,`Customer Main Location`,`Customer Tax Number` from `Customer Dimension` where `Customer Key` in (%s)",$customer_keys);
    $res=mysql_query($sql);


    //   $customer_card='<table>';
    while ($row=mysql_fetch_array($res)) {

        if ($row['Customer Type']=='Company') {
            $name=$row['Customer Name'].'<br/>'.$row['Customer Main Contact Name'];
        } else {
            $name=$row['Customer Name'];

        }

        $address=$row['Customer Main Plain Email'];

        if ($row['Customer Main Telephone Key'])$address.='<br/>T: '.$row['Customer Main XHTML Telephone'];
        $address.='<br/>'.$row['Customer Main Location'];
        if ($row['Customer Main Postal Code'])$address.=', '.$row['Customer Main Postal Code'];
        $address=preg_replace('/^\<br\/\>/','',$address);


        $results[$row['Customer Key']]=array('key'=>sprintf('%05d',$row['Customer Key']),'name'=>$name,'address'=>$address);
    }
//$customer_card.='</table>';


    $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'customer.php?id=','q'=>$q);
    echo json_encode($response);

}






function search_customer_id($id,$valid_stores=false) {
    if ($valid_stores) {
        $stores=" and `Customer Store Key` in ($valid_stores)";
    } else
        $stores='';

    $sql=sprintf("select `Customer Key` from `Customer Dimension` where `Customer Key`=%d %s ",$id,$stores);
    $res=mysql_query($sql);
    if ($row=mysql_fetch_array($res)) {
        $found=$row['Customer Key'];
    } else
        $found=false;
    return $found;
}


function search_customer_name($user) {
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
    } else {

        $_SESSION['search']=array('Type'=>'Customer','Data'=>array('Customer Name'=>$q));
        echo json_encode(array('state'=>200,'url'=>'customer_lookup.php?res=y'));
        return;

    }
    mysql_free_result($result);








}
function search_locations($data) {
    $max_results=12;
    $user=$data['user'];
    $q=$data['q'];
    if ($q=='') {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }


    if ($data['scope']=='store') {
        $warehouses=$_SESSION['state']['warehouse']['id'];

    } else
        $warehouses=join(',',$user->warehouses);


    $results=array();

    $number_results=0;



    $sql=sprintf("select `Warehouse Area Name` ,`Location Key`,`Location Code`, `Location Mainly Used For` from `Location Dimension` left join `Warehouse Area Dimension` WA on (`Warehouse Area Key`=`Location Warehouse Area Key`)  where `Location Warehouse Key` in (%s) and `Location Code`=%s",$warehouses,prepare_mysql($q));
    // print $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {
        if ($number_results>$max_results)
            continue;
        $results[]=array('type'=>'Location','area'=>$row['Warehouse Area Name'],'code'=>$row['Location Code'],'use'=>$row['Location Mainly Used For'],'link'=>'location.php?id=','key'=>$row['Location Key']);
        $number_results++;

    }



    if (preg_match('/sku\d+/i',$q)) {

        $sql=sprintf('select `Location Mainly Used For`,`Warehouse Area Name` ,L.`Location Key`,`Location Code`,P.`Part SKU`, `Quantity On Hand`,`Part XHTML Description`,`Part Currently Used In`  from `Part Location Dimension`  PL left join `Location Dimension` L on (PL.`Location Key`=L.`Location Key`) left join `Part Dimension` P on (P.`Part SKU`=PL.`Part SKU`) left join `Warehouse Area Dimension` WA on (`Warehouse Area Key`=`Location Warehouse Area Key`) where `Location Warehouse Key` in (%s) and PL.`Part SKU`=%s ',$warehouses,addslashes(preg_replace('/^sku/i','',$q)));
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
            if ($number_results>$max_results)
                continue;
            $results[]=array('type'=>'Part','use'=>$row['Location Mainly Used For'],'area'=>$row['Warehouse Area Name'],  'used_in'=>$row['Part Currently Used In'],'stock'=>$row['Quantity On Hand'],'code'=>$row['Location Code'],'sku'=>$row['Part SKU'],'description'=>$row['Part XHTML Description'],'link'=>'location.php?id=','key'=>$row['Location Key']);
            $number_results++;

        }
    }


    $sql=sprintf('select `Location Mainly Used For`,`Warehouse Area Name` ,`Part XHTML Description`,PL.`Part SKU`,`Quantity On Hand`,L.`Location Key`,GROUP_CONCAT(`Product Code`," (",`Store Code`,")") as `Product Code`,`Location Code` from `Part Location Dimension` PL left join `Location Dimension` L on (PL.`Location Key`=L.`Location Key`) left join `Part Dimension` P on (P.`Part SKU`=PL.`Part SKU`) left join `Product Part List` PPL on (PPL.`Part SKU`=P.`Part SKU`) left join `Product Part Dimension` PPD on (PPD.`Product Part Key`=PPL.`Product Part Key`) left join `Product Dimension` PD on (PD.`Product ID`=PPL.`Product ID`) left join `Store Dimension` on (`Product Store Key`=`Store Key`) left join `Warehouse Area Dimension` WA on (`Warehouse Area Key`=`Location Warehouse Area Key`) where `Product Part Most Recent`="Yes" and `Location Warehouse Key` in (%s) and PD.`Product Code` like "%s%%" group by PL.`Location Key`',$warehouses,addslashes($q));

//print $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {
        if ($number_results>$max_results)
            continue;
        $results[]=array('type'=>'Part','use'=>$row['Location Mainly Used For'],'area'=>$row['Warehouse Area Name'],'used_in'=>$row['Product Code'],'stock'=>$row['Quantity On Hand'],'code'=>$row['Location Code'],'sku'=>$row['Part SKU'],'description'=>$row['Part XHTML Description'],'link'=>'location.php?id=','key'=>$row['Location Key']);
        $number_results++;

    }

    $limit=$max_results-$number_results;
    if ($limit>0) {

        $sql=sprintf('select `Warehouse Area Name` ,`Location Key`,`Location Code`, `Location Mainly Used For` from `Location Dimension` left join `Warehouse Area Dimension` WA on (`Warehouse Area Key`=`Location Warehouse Area Key`)  where `Location Warehouse Key` in (%s) and `Location Code` like "%s%%" limit %d ',$warehouses,addslashes($q),$limit);
        //print $sql;
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {

            $results[]=array('type'=>'Location','area'=>$row['Warehouse Area Name'],'code'=>$row['Location Code'],'use'=>$row['Location Mainly Used For'],'link'=>'location.php?id=','key'=>$row['Location Key']);

        }
    }



    $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'');
    echo json_encode($response);




}


function search_products($data) {
    $the_results=array();

    $max_results=10;
    $user=$data['user'];
    $q=$data['q'];


    if ($q=='') {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }


    if ($data['scope']=='store') {
        $stores=$_SESSION['state']['store']['id'];

    } else
        $stores=join(',',$user->stores);


    if (!$stores) {
        $response=array('state'=>200,'results'=>0,'data'=>'','mgs'=>'Store Error');
        echo json_encode($response);
        return;
    }
    $extra_q='';
    $array_q=preg_split('/\s/',$q);
    if (count($array_q>1)) {
        $q=array_shift($array_q);
        $extra_q=join(' ',$array_q);

    }

    $found_family=false;

    $candidates=array();
    $sql=sprintf('select `Product Family Key`,`Product Family Code` from `Product Family Dimension` where `Product Family Store Key` in (%s) and `Product Family Code` like "%s%%" limit 100 ',$stores,addslashes($q));
//print $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {
        if (strtolower($row['Product Family Code'])==strtolower($q)) {
            $candidates['F '.$row['Product Family Key']]=210;
            $found_family=$row['Product Family Key'];

        } else {

            $len_name=strlen($row['Product Family Code']);
            $len_q=strlen($q);
            $factor=$len_q/$len_name;
            $candidates['F '.$row['Product Family Key']]=200*$factor;
        }
    }
    //print $extra_q;
    if ($found_family) {
        if ($extra_q) {

            $sql=sprintf("SELECT `Product ID`, MATCH(`Product Name`) AGAINST (%s) as Relevance FROM `Product Dimension` WHERE   `Product Record Type` not in ('historic') and  `Product Family Key`=%d  and MATCH
                         (`Product Name`) AGAINST(%s IN
                         BOOLEAN MODE) HAVING Relevance > 0.2 ORDER
                         BY Relevance DESC",prepare_mysql($extra_q),$found_family,prepare_mysql('+'.join(' +',$array_q)));

            //$sql=sprintf('select damlevlim256(UPPER(%s),UPPER(`Product Name`),100) as dist , `Product ID`,`Product Name` from `Product Dimension` where `Product Family Key`=%d order by damlevlim256(UPPER(%s),UPPER(`Product Name`),100)  limit 6 ',prepare_mysql($extra_q),$found_family,prepare_mysql($extra_q));
            //print $sql;
            $res=mysql_query($sql);
            while ($row=mysql_fetch_array($res)) {

                $candidates['P '.$row['Product ID']]=$row['Relevance'];
            }
        }




    } else {


        $sql=sprintf('select `Product ID`,`Product Code` from `Product Dimension` where `Product Record Type` not in ("historic") and   `Product Store Key` in (%s) and `Product Code` like "%s%%" limit 100 ',$stores,addslashes($q));
        //print $sql;
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
            if ($row['Product Code']==$q)
                $candidates['P '.$row['Product ID']]=110;
            else {

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

    if ($total_candidates==0) {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }


    $counter=0;
    $customer_keys='';

    $results=array();
    $family_keys='';
    $products_keys='';

    foreach($candidates as $key=>$val) {
        $_key=preg_split('/ /',$key);
        if ($_key[0]=='F') {
            $family_keys.=','.$_key[1];
            $results[$key]='';
        } else {
            $products_keys.=','.$_key[1];
            $results[$key]='';

        }

        $counter++;

        if ($counter>$max_results)
            break;
    }
    $family_keys=preg_replace('/^,/','',$family_keys);
    $products_keys=preg_replace('/^,/','',$products_keys);

    if ($family_keys) {
        $sql=sprintf("select `Product Family Key`,`Product Family Name`,`Product Family Code`  from `Product Family Dimension` where `Product Family Key` in (%s)",$family_keys);
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
            $the_results[]=array('Title'=>$row['Product Family Code']);
            $image='';
            $results['F '.$row['Product Family Key']]=array('image'=>$image,'code'=>$row['Product Family Code'],'description'=>$row['Product Family Name'],'link'=>'family.php?id=','key'=>$row['Product Family Key']);
        }
    }

    if ($products_keys) {
        $sql=sprintf("select `Product ID`,`Product XHTML Short Description`,`Product Code`,`Product Main Image`  from `Product Dimension`   where `Product ID` in (%s)",$products_keys);
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
            $image='';
            if ($row['Product Main Image']!='art/nopic.png')
                $image=sprintf('<img src="%s"> ',preg_replace('/small/','thumbnails',$row['Product Main Image']));
            $the_results[]=array('Title'=>'<span>'.$row['Product Code'].'</span><span style="margin-left:10px">'.$row['Product XHTML Short Description'].'</span>');

            $results['P '.$row['Product ID']]=array('image'=>$image,'code'=>$row['Product Code'],'description'=>$row['Product XHTML Short Description'],'link'=>'product.php?pid=','key'=>$row['Product ID']);
        }
    }



    $response=array('ResultSet'=>array(
                                    'Result'=>$the_results

                                ));


    $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'');
    echo json_encode($response);













}




function is_postal_code($postalcode) {

    if (preg_match('/^([a-z]{2}-?)?\d{3,10}(-\d{3})?$/i',$postalcode) or preg_match('/^([a-z]\d{4}[a-z]|[A-Z]{2}\d{2}|[A-Z]\d{4}[A-Z]{3}|\d{4}[A-Z]{2})$/i',$postalcode))
        return true;

    return false;
}


function search_parts($data) {
    $the_results=array();

    $max_results=10;
    $user=$data['user'];
    $q=$data['q'];
    // $q=_trim($_REQUEST['q']);

    if ($q=='') {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }

    $candidates=array();

    if (is_numeric($q)  or preg_match('/^sku:\?\d+/i',$q)  ) {
        $_q=preg_replace('/[^\d]/','',$q);
        $sql=sprintf('select `Part SKU`,`Part XHTML Description` from `Part Dimension` where `Part SKU`=%d ',$_q);
//print $sql;
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {

            $candidates[$row['Part SKU']]=210;
            $part_data[$row['Part SKU']]=array('link'=>'part.php?id=','sku'=>$row['Part SKU'],'fsku'=>sprintf('SKU %05d',$row['Part SKU']),'description'=>$row['Part XHTML Description']);

        }
    }
    $sql=sprintf("select `Part XHTML Description`,`Part SKU`,`Part Unit Description`, match (`Part Unit Description`) AGAINST ('%s' IN NATURAL LANGUAGE MODE) as score   from `Part Dimension` where match (`Part Unit Description`) AGAINST ('%s' IN NATURAL LANGUAGE MODE) limit 20",addslashes($q),addslashes($q));;

// print $sql;
    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {

        $candidates[$row['Part SKU']]=$row['score'];
        $part_data[$row['Part SKU']]=array('link'=>'part.php?id=','sku'=>$row['Part SKU'],'fsku'=>sprintf('SKU %05d',$row['Part SKU']),'description'=>$row['Part XHTML Description']);

    }

    /*
      $sql=sprintf('select `Part SKU`,`Part XHTML Description` from `Part Dimension` where `Part Unit Description` like "%%%s%%" limit 20',addslashes($q));

     $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){

          $candidates[$row['Part SKU']]=100;
          $part_data[$row['Part SKU']]=array('sku'=>$row['Part SKU'],'description'=>$row['Part XHTML Description']);

      }


      $qs=preg_split('/\s+|\,/',$q);
      if(count($sq>1)){
        foreach($qs as $q){
         $sql=sprintf('select `Part SKU`,`Part XHTML Description` from `Part Dimension` where `Part Unit Description` like "%%%s%%" limit 20',addslashes($q));

     $res=mysql_query($sql);
      while($row=mysql_fetch_array($res)){

          $candidates[$row['Part SKU']]=100;
          $part_data[$row['Part SKU']]=array('sku'=>$row['Part SKU'],'description'=>$row['Part XHTML Description']);

      }

        }

      }

      */


    arsort($candidates);

    $total_candidates=count($candidates);

    if ($total_candidates==0) {
        $response=array('state'=>200,'results'=>0,'data'=>'');
        echo json_encode($response);
        return;
    }


    $counter=0;
    $customer_keys='';

    $results=array();
    $family_keys='';
    $products_keys='';

    foreach($candidates as $key=>$val) {
        if ($counter>$max_results)
            break;
        $results[$key]=$part_data[$key];

        $counter++;
    }




    $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'part.php?id=');
    echo json_encode($response);













}


function search_full_text($data) {




    $the_results=array();

    $max_results=20;
    $user=$data['user'];
    $q=$data['q'];
    // $q=_trim($_REQUEST['q']);

    if ($q=='') {
        $response=array('state'=>200,'results'=>0,'data'=>'','q'=>$q);
        echo json_encode($response);
        return;
    }

    $candidates=array();

    $q_parts=preg_split('/\s+/',$q);
    foreach($q_parts as $q_part) {
        $sql=sprintf("select `Store Code`,`Subject`,`Subject Key`,`Search Full Text Key`,`Search Result Name`,`Search Result Description`,`Search Result Image`   from `Search Full Text Dimension` S left join `Store Dimension` SD on (SD.`Store Key`=S.`Store Key`)    where `Search Result Name`='%s' limit 20",addslashes($q_part));;
        //  print $sql;
        $res=mysql_query($sql);
        while ($row=mysql_fetch_array($res)) {
            $store_code=$row['Store Code'];
            $candidates[$row['Search Full Text Key']]=100;
            $link='';
            switch ($row['Subject']) {
            case('Product'):
                $link='product.php?pid=';
                $icon='brick.png';
                break;
            case('Order'):
                $link='order.php?id=';
                $icon='basket.png';
                break;
            case('Part'):
                $link='part.php?id=';
                $icon='package_green.png';
                break;
            case('Customer'):
                $link='customer.php?id=';
                $icon='vcard.png';
                break;
            case('Family'):
                $link='family.php?id=';
                $icon='bricks.png';
                break;
            }
            $image='';
            if ($row['Search Result Image']!='')
                $image='<img src="'.$row['Search Result Image'].'">';
            $part_data[$row['Search Full Text Key']]=array('subject'=>$row['Subject'],'store_code'=>$store_code,'icon'=>$icon,'link'=>$link,'key'=>$row['Subject Key'],'name'=>$row['Search Result Name'],'description'=>$row['Search Result Description'],'image'=>$image);

        }
    }

    $sql=sprintf("select `Store Code`,`Subject`,`Subject Key`,`Search Full Text Key`,`Search Result Name`,`Search Result Description`,`Search Result Image`, match (`First Search Full Text`) AGAINST ('%s' IN NATURAL LANGUAGE MODE) as score   from `Search Full Text Dimension`  S left join `Store Dimension` SD on (SD.`Store Key`=S.`Store Key`) where match (`First Search Full Text`) AGAINST ('%s' IN NATURAL LANGUAGE MODE)ORDER BY  score desc limit 20",addslashes($q),addslashes($q));;


    $res=mysql_query($sql);
    while ($row=mysql_fetch_array($res)) {

        if (array_key_exists($row['Search Full Text Key'],$candidates))
            $candidates[$row['Search Full Text Key']]+=$row['score'];
        else
            $candidates[$row['Search Full Text Key']]=$row['score'];

        $link='';
        $store_code=$row['Store Code'];
        switch ($row['Subject']) {

        case('Product'):
            $link='product.php?pid=';
            $icon='brick.png';
            break;
        case('Order'):
            $link='order.php?id=';
            $icon='basket.png';
            break;
        case('Part'):
            $link='part.php?id=';
            $icon='package_green.png';
            break;
        case('Customer'):
            $link='customer.php?id=';
            $icon='vcard.png';

            break;
        case('Family'):
            $link='family.php?id=';
            $icon='bricks.png';
            break;
        }
        $image='';
        if ($row['Search Result Image']!='')
            $image='<img src="'.$row['Search Result Image'].'">';


        $part_data[$row['Search Full Text Key']]=array('store_code'=>$store_code,'icon'=>$icon, 'link'=>$link,'key'=>$row['Subject Key'],'name'=>$row['Search Result Name'],'description'=>$row['Search Result Description'],'image'=>$image);

    }




    arsort($candidates);

    $total_candidates=count($candidates);

    if ($total_candidates==0) {
        $response=array('state'=>200,'results'=>0,'data'=>'','q'=>$q);
        echo json_encode($response);
        return;
    }


    $counter=0;
    $customer_keys='';

    $results=array();
    $family_keys='';
    $products_keys='';

    foreach($candidates as $key=>$val) {
        if ($counter>$max_results)
            break;
        $results[$key]=$part_data[$key];

        $counter++;
    }

    //  print_r($results);
//   exit;


    $response=array('state'=>200,'results'=>count($results),'data'=>$results,'link'=>'','q'=>$q);
    echo json_encode($response);













}



?>