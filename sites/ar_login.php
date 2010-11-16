<?php
include('common.php');
include_once('app_files/key.php');

$auth=new Auth(IKEY,SKEY);

$handle = (array_key_exists('login_handle', $_REQUEST)) ? $_REQUEST['login_handle'] : false;
$sk = (array_key_exists('ep', $_REQUEST)) ? $_REQUEST['ep'] : false;
if (!$sk and array_key_exists('mk', $_REQUEST)    ) {
    $auth->authenticate_from_masterkey($_REQUEST['mk']);
}
elseif($handle) {
    //  print urldecode($sk)."\n\n";

    $auth->authenticate($handle,rawurldecode($sk),'customer',$store_key);
}

if ($auth->is_authenticated()) {
    $_SESSION['logged_in']=true;
    $_SESSION['logged_in_page']=$store_key;
   
    $_SESSION['user_key']=$auth->get_user_key();
    $_SESSION['customer_key']=$auth->get_user_parent_key();

    $sql=sprintf("select `Order Key` from `Order Dimension` where `Order Customer Key`=%d and `Order Current Dispatch State`='In Process' ",$_SESSION['customer_key']);
    $res=mysql_query($sql);
    if ($row=mysql_fetch_array($res)) {
        $_SESSION['order_key']=$row['Order Key'];
    } else {
        $_SESSION['order_key']=0;
        $_SESSION['order_data']=array(
                                    'items'=>0,
                                    'shipping'=>0,
                                    'shipping_and_handing'=>0,
                                    'charges'=>0,
                                    'discounts'=>0,
                                    'total_net'=>0,
                                    'tax'=>0,
                                    'total'=>0,
                                    'amount_items'=>money(0),
                                    'amount_discounts'=>money(0),
                                    'amount_shipping'=>money(0),
                                    'amount_shipping_and_handing'=>money(0),
                                    'amount_charges'=>money(0),
                                    'amount_total_net'=>money(0),
                                    'amount_tax'=>money(0),
                                    'amount_total'=>money(0)
                                );

    }

    $response=array('state'=>200,'result'=>'ok');
    echo json_encode($response);
    exit;


} else {
    $response=array('state'=>200,'result'=>'no_valid');
    echo json_encode($response);
    exit;


}







?>