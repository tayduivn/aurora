<?php
include_once('common.php');

if (!isset($_REQUEST['tipo'])) {
    $response=array('state'=>405,'msg'=>_('Non acceptable request').' (t)');
    echo json_encode($response);
    exit;
}

$tipo=$_REQUEST['tipo'];
switch ($tipo) {
case('list_orders'):
    list_orders();
    break;
case('transactions_dipatched'):
    transactions_dipatched();
    break;

default:

    $response=array('state'=>404,'msg'=>_('Operation not found'));
    echo json_encode($response);

}

function list_orders(){
    $customer_key=$_REQUEST['customer_key'];
    $adata=array();

    $sql="select `Order Current Payment State`,`Order Current Dispatch State`,`Order Out of Stock Net Amount`,`Order Invoiced Total Net Adjust Amount`,`Order Invoiced Total Tax Adjust Amount`,FORMAT(`Order Invoiced Total Net Adjust Amount`+`Order Invoiced Total Tax Adjust Amount`,2) as `Order Adjust Amount`,`Order Out of Stock Net Amount`,`Order Out of Stock Tax Amount`,FORMAT(`Order Out of Stock Net Amount`+`Order Out of Stock Tax Amount`,2) as `Order Out of Stock Amount`,`Order Balance Total Amount`,`Order Type`,`Order Currency Exchange`,`Order Currency`,`Order Key`,`Order Public ID`,`Order Customer Key`,`Order Customer Name`,`Order Last Updated Date`,`Order Date`,`Order Total Amount` ,`Order Current XHTML State` from `Order Dimension` where `Order Customer Key`=$customer_key order by `Order Date` desc";

    $res = mysql_query($sql);
//print_r($sql);
    $total=mysql_num_rows($res);
//print $sql;
    while ($row=mysql_fetch_array($res, MYSQL_ASSOC)) {
        $mark_out_of_stock="<span style='visibility:hidden'>&otimes;</span>";
        $mark_out_of_credits="<span style='visibility:hidden'>&crarr;</span>";
        $mark_out_of_error="<span style='visibility:hidden'>&epsilon;</span>";
        $out_of_stock=false;
        $errors=false;
        $refunded=false;
        if ($row['Order Out of Stock Amount']!=0) {
            $out_of_stock=true;
            $info='';
            if ($row['Order Out of Stock Net Amount']!=0) {
                $info.=_('Net').': '.money($row['Order Out of Stock Net Amount'],$row['Order Currency'])."";
            }
            if ($row['Order Out of Stock Tax Amount']!=0) {
                $info.='; '._('Tax').': '.money($row['Order Out of Stock Tax Amount'],$row['Order Currency']);
            }
            $info=preg_replace('/^\;\s*/','',$info);
            $mark_out_of_stock="<span style='color:brown'  title='$info'  >&otimes;</span>";

        }

        if ($row['Order Adjust Amount']<-0.01 or $row['Order Adjust Amount']>0.01 ) {
            $errors=true;
            $info='';
            if ($row['Order Invoiced Total Net Adjust Amount']!=0) {
                $info.=_('Net').': '.money($row['Order Invoiced Total Net Adjust Amount'],$row['Order Currency'])."";
            }
            if ($row['Order Invoiced Total Tax Adjust Amount']!=0) {
                $info.='; '._('Tax').': '.money($row['Order Invoiced Total Tax Adjust Amount'],$row['Order Currency']);
            }
            $info=_('Errors').' '.preg_replace('/^\;\s*/','',$info);
            if ($row['Order Adjust Amount']<-1 or $row['Order Adjust Amount']>1 ) {
                $mark_out_of_error ="<span style='color:red' title='$info'>&epsilon;</span>";
            } else {
                $mark_out_of_error ="<span style='color:brown'  title='$info'>&epsilon;</span>";
            }
            //$mark_out_of_error.=$row['Order Adjust Amount'];
        }


        if (!$out_of_stock and !$refunded)
            $mark=$mark_out_of_error.$mark_out_of_stock.$mark_out_of_credits;
        elseif(!$refunded and $out_of_stock and $errors)
        $mark=$mark_out_of_stock.$mark_out_of_error.$mark_out_of_credits;
        else
            $mark=$mark_out_of_stock.$mark_out_of_credits.$mark_out_of_error;


        $adata[]=array(
                     'id'=>sprintf("<a href='profile.php?view=orders&order_id=%d'>%s</a>",$row['Order Key'],$row['Order Public ID']),
			//'id'=>$row['Order Public ID'],
                     'state'=>$row['Order Current XHTML State'],
                     'date'=>strftime("%a %e %b %Y", strtotime($row['Order Date'].' UTC')) ,
                   //  'total'=>money($row['Order Balance Total Amount'],$row['Order Currency']).$mark,
'total'=>money($row['Order Balance Total Amount'],'GBP')

                 );
    }
    mysql_free_result($res);

    $rtext=$total." Orders";	
    $response=array('resultset'=>
                                array('state'=>200,
                                      'data'=>$adata,
                                      'sort_key'=>'date',
                                      'sort_dir'=>'desc',
                                      'tableid'=>0,
                                      'filter_msg'=>'',
                                      'rtext'=>$rtext,
                                      'rtext_rpp'=>'',
                                      'total_records'=>$total,
                                      'records_offset'=>0,
                                      'records_perpage'=>25,
                                     )
                   );
    echo json_encode($response);
}

function transactions_dipatched() {
    if (isset( $_REQUEST['id']) and is_numeric( $_REQUEST['id']))
        $order_id=$_REQUEST['id'];
  //  else
  //      $order_id=$_SESSION['state']['order']['id'];




    $where=' where `Order Transaction Type` not in ("Resend")  and  O.`Order Key`='.$order_id;

    $total_charged=0;
    $total_discounts=0;
    $total_picks=0;

    $data=array();

    $order=' order by O.`Product Code`';

    $sql="select O.`Order Transaction Fact Key`,`Deal Info`,`Operation`,`Quantity`,`Order Currency Code`,`Order Quantity`,`Order Bonus Quantity`,`No Shipped Due Out of Stock`,P.`Product ID` ,P.`Product Code`,`Product XHTML Short Description`,`Shipped Quantity`,(`Invoice Transaction Gross Amount`-`Invoice Transaction Total Discount Amount`) as amount
         from `Order Transaction Fact` O left join `Product Dimension` P on (P.`Product ID`=O.`Product ID`)
         left join `Order Post Transaction Dimension` POT on (O.`Order Transaction Fact Key`=POT.`Order Transaction Fact Key`)
         left join `Order Transaction Deal Bridge` DB on (DB.`Order Transaction Fact Key`=O.`Order Transaction Fact Key`)

         $where $order  ";

    //  $sql="select  p.id as id,p.code as code ,product_id,p.description,units,ordered,dispatched,charge,discount,promotion_id    from transaction as t left join product as p on (p.id=product_id)  $where    ";

    //print $sql;

    $result=mysql_query($sql);
    while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {

        $ordered='';
        if ($row['Order Quantity']!=0)
            $ordered.=number($row['Order Quantity']);
        if ($row['Order Bonus Quantity']>0) {
            $ordered='<br/>'._('Bonus').' +'.number($row['Order Bonus Quantity']);
        }
        if ($row['No Shipped Due Out of Stock']>0) {
            $ordered.='<br/> '._('No Stk').' -'.number($row['No Shipped Due Out of Stock']);
        }
        $ordered=preg_replace('/^<br\/>/','',$ordered);
        $code=sprintf('<a href="product.php?pid=%s">%s</a>',$row['Product ID'],$row['Product Code']);

        $dispatched=number($row['Shipped Quantity']);

        if ($row['Quantity']>0  and $row['Operation']=='Resend') {
            $dispatched.='<br/> '._('Resend').' +'.number($row['Quantity']);
        }

        $data[]=array(

                    'code'=>$code
                           ,'description'=>$row['Product XHTML Short Description'].' <span style="color:red">'.$row['Deal Info'].'</span>'

                                          ,'ordered'=>$ordered
                                                     ,'dispatched'=>$dispatched
                                                                   ,'invoiced'=>money($row['amount'],$row['Order Currency Code'])
                );
    }





    $response=array('resultset'=>
                                array('state'=>200,
                                      'data'=>$data
// 			 'total_records'=>$total,
// 			 'records_offset'=>$start_from,
// 			 'records_returned'=>$start_from+$res->numRows(),
// 			 'records_perpage'=>$number_results,
// 			 'records_text'=>$rtext,
// 			 'records_order'=>$order,
// 			 'records_order_dir'=>$order_dir,
// 			 'filtered'=>$filtered
                                     )
                   );
    echo json_encode($response);
}
?>
