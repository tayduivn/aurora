<?php

chdir("../");
include_once('common.php');
if (!isset($_REQUEST['tipo'])) {

    exit;
}

$tipo=$_REQUEST['tipo'];

$colors=array('0033CC','0099CC','00CC99','00CC33','CC9900');

switch ($tipo) {

case('store_sales'):

    
    if (!isset($_REQUEST['store_key'])) {
        exit;
    }
    $tmp=preg_split('/\|/', $_REQUEST['store_key']);
    $stores_keys=array();
    foreach($tmp as $store_key) {

        if (is_numeric($store_key) and in_array($store_key, $user->stores)) {
            $stores_keys[]=$store_key;
        }
    }
    $use_corporate=0;
    $staked=false;
    if (isset($_REQUEST['stacked']) and $_REQUEST['stacked'])$staked=true;
    $graphs_data=array();
    $gid=0;
    if ($staked) {
        $sql=sprintf("select `Store Name`,`Store Code`,`Store Currency Code` from `Store Dimension` where `Store Key` in (%s)",addslashes(join(',',$stores_keys)));
        $res=mysql_query($sql);

        while ($row=mysql_fetch_assoc($res)) {
            $graphs_data[]=array(
                               'gid'=>$gid,
                               'title'=>$row['Store Code'],
                               'currency_code'=>$corporate_currency,
                               'color'=>$colors[$gid]
                           );
            $gid++;
        }
        $data_args='tipo=stacked_store_sales&store_key='.join(',',$stores_keys);
        $template='plot_stacked_asset_sales.xml.tpl';

    } else {// no stakecked


        $sql=sprintf("select `Store Name`,`Store Code`,`Store Currency Code` from `Store Dimension` where `Store Key` in (%s)",addslashes(join(',',$stores_keys)));
        $res=mysql_query($sql);
        $title='';
        $currencies=array();
        while ($row=mysql_fetch_assoc($res)) {
            $title.=','.$row['Store Code'];


            $currency_code=$row['Store Currency Code'];
            $currencies[$currency_code]=1;

        }


        if (count($currencies)>1)
            $use_corporate=1;




        $graphs_data[]=array(
                           'gid'=>0,
                           'title'=>$title.' '._('Sales'),
                           'currency_code'=>($use_corporate?$corporate_currency:$currency_code)
                       );
        $data_args='tipo=store_sales&store_key='.join(',',$stores_keys).'&use_corporate='.$use_corporate;
        
        $template='plot_asset_sales.xml.tpl';

    }






    break;
    
   case('department_sales'):

    if (!isset($_REQUEST['department_key'])) {
        exit;
    }
    $tmp=preg_split('/\|/', $_REQUEST['department_key']);
    $departments_keys=array();
    foreach($tmp as $department_key) {

        if (is_numeric($department_key) ) {
            $departments_keys[]=$department_key;
        }
    }
    $use_corporate=0;
    $staked=false;
    if (isset($_REQUEST['stacked']) and $_REQUEST['stacked'])$staked=true;
    $graphs_data=array();
    $gid=0;
    if ($staked) {
        $sql=sprintf("select `Product Department Store Key`,`Product Department Name`,`Product Department Code`,`Store Currency Code` from `Product Department Dimension` left join `Store Dimension` on (`Product Department Store Key`=`Store Key`)  where `Product Department Key` in (%s)",addslashes(join(',',$departments_keys)));
        $res=mysql_query($sql);

        while ($row=mysql_fetch_assoc($res)) {
            if(!in_array($row['Product Department Store Key'], $user->stores)){
                continue;
            }
            $graphs_data[]=array(
                               'gid'=>$gid,
                               'title'=>$row['Product Department Code'],
                               'currency_code'=>$corporate_currency,
                               'color'=>$colors[$gid]
                           );
            $gid++;
        }
        $data_args='tipo=stacked_department_sales&department_key='.join(',',$departments_keys);
        $template='plot_stacked_asset_sales.xml.tpl';

    } else {// no stakecked


        $sql=sprintf("select `Product Department Name`,`Product Department Code`,`Store Currency Code` from `Product Department Dimension` left join `Store Dimension` on (`Product Department Store Key`=`Store Key`) where `Product Department Key` in (%s)",addslashes(join(',',$departments_keys)));
     // print $sql;
      $res=mysql_query($sql);
        $title='';
        $currencies=array();
        while ($row=mysql_fetch_assoc($res)) {
            $title.=','.$row['Product Department Code'];


            $currency_code=$row['Store Currency Code'];
            $currencies[$currency_code]=1;

        }


        if (count($currencies)>1)
            $use_corporate=1;




        $graphs_data[]=array(
                           'gid'=>0,
                           'title'=>$title.' '._('Sales'),
                           'currency_code'=>($use_corporate?$corporate_currency:$currency_code)
                       );
        $data_args='tipo=department_sales&department_key='.join(',',$departments_keys).'&use_corporate='.$use_corporate;
        
        $template='plot_asset_sales.xml.tpl';

    }


break; 
    
 case('family_sales'):

    if (!isset($_REQUEST['family_key'])) {
        exit;
    }
    $tmp=preg_split('/\|/', $_REQUEST['family_key']);
    $familys_keys=array();
    foreach($tmp as $family_key) {

        if (is_numeric($family_key) ) {
            $familys_keys[]=$family_key;
        }
    }
    $use_corporate=0;
    $staked=false;
    if (isset($_REQUEST['stacked']) and $_REQUEST['stacked'])$staked=true;
    $graphs_data=array();
    $gid=0;
    if ($staked) {
        $sql=sprintf("select `Product Family Store Key`,`Product Family Name`,`Product Family Code`,`Store Currency Code` from `Product Family Dimension`  left join `Store Dimension` on (`Product Family Store Key`=`Store Key`) where `Product Family Key` in (%s)",addslashes(join(',',$familys_keys)));
        $res=mysql_query($sql);

        while ($row=mysql_fetch_assoc($res)) {
            if(!in_array($row['Product Family Store Key'], $user->stores)){
                continue;
            }
            $graphs_data[]=array(
                               'gid'=>$gid,
                               'title'=>$row['Product Family Code'],
                               'currency_code'=>$corporate_currency,
                               'color'=>$colors[$gid]
                           );
            $gid++;
        }
        $data_args='tipo=stacked_family_sales&family_key='.join(',',$familys_keys);
        $template='plot_stacked_asset_sales.xml.tpl';

    } else {// no stakecked


        $sql=sprintf("select `Product Family Name`,`Product Family Code`,`Store Currency Code` from `Product Family Dimension` left join `Store Dimension` on (`Product Family Store Key`=`Store Key`) where `Product Family Key` in (%s)",addslashes(join(',',$familys_keys)));
     // print $sql;
      $res=mysql_query($sql);
        $title='';
        $currencies=array();
        while ($row=mysql_fetch_assoc($res)) {
            $title.=','.$row['Product Family Code'];


            $currency_code=$row['Store Currency Code'];
            $currencies[$currency_code]=1;

        }


        if (count($currencies)>1)
            $use_corporate=1;




        $graphs_data[]=array(
                           'gid'=>0,
                           'title'=>$title.' '._('Sales'),
                           'currency_code'=>($use_corporate?$corporate_currency:$currency_code)
                       );
        $data_args='tipo=family_sales&family_key='.join(',',$familys_keys).'&use_corporate='.$use_corporate;
        
        $template='plot_asset_sales.xml.tpl';

    }


break; 
 case('product_id_sales'):

    if (!isset($_REQUEST['product_id'])) {
        exit;
    }
    $tmp=preg_split('/\|/', $_REQUEST['product_id']);
    $product_ids=array();
    foreach($tmp as $product_id) {

        if (is_numeric($product_id) ) {
            $product_ids[]=$product_id;
        }
    }
    $use_corporate=0;
    $staked=false;
    if (isset($_REQUEST['stacked']) and $_REQUEST['stacked'])$staked=true;
    $graphs_data=array();
    $gid=0;
    if ($staked) {
        $sql=sprintf("select `Product Store Key`,`Product Name`,`Product Code`,`Store Currency Code` from `Product Dimension`  left join `Store Dimension` on (`Product Store Key`=`Store Key`) where `Product ID` in (%s)",addslashes(join(',',$product_ids)));
        $res=mysql_query($sql);

        while ($row=mysql_fetch_assoc($res)) {
            if(!in_array($row['Product Store Key'], $user->stores)){
                continue;
            }
            $graphs_data[]=array(
                               'gid'=>$gid,
                               'title'=>$row['Product Code'],
                               'currency_code'=>$corporate_currency,
                               'color'=>$colors[$gid]
                           );
            $gid++;
        }
        $data_args='tipo=stacked_product_id_sales&product_id='.join(',',$product_ids);
        $template='plot_stacked_asset_sales.xml.tpl';

    } else {// no stakecked


        $sql=sprintf("select `Product Name`,`Product Code`,`Store Currency Code` from `Product Dimension` left join `Store Dimension` on (`Product Store Key`=`Store Key`) where `Product ID` in (%s)",addslashes(join(',',$product_ids)));
     // print $sql;
      $res=mysql_query($sql);
        $title='';
        $currencies=array();
        while ($row=mysql_fetch_assoc($res)) {
            $title.=','.$row['Product Code'];


            $currency_code=$row['Store Currency Code'];
            $currencies[$currency_code]=1;

        }


        if (count($currencies)>1)
            $use_corporate=1;




        $graphs_data[]=array(
                           'gid'=>0,
                           'title'=>$title.' '._('Sales'),
                           'currency_code'=>($use_corporate?$corporate_currency:$currency_code)
                       );
        $data_args='tipo=product_id_sales&product_id='.join(',',$product_ids).'&use_corporate='.$use_corporate;
        
        $template='plot_asset_sales.xml.tpl';

    }


break; 


}


    if (isset($_REQUEST['from'])) {
        $smarty->assign('from',$_REQUEST['from']);

        $data_args.=sprintf("&from=%s",$_REQUEST['from']);
    }
    if (isset($_REQUEST['to'])) {
        $smarty->assign('to',$_REQUEST['to']);

        $data_args.=sprintf("&to=%s",$_REQUEST['to']);
    }


$smarty->assign('locale_data',localeconv());
$smarty->assign('graphs_data',$graphs_data);
$smarty->assign('data_args',$data_args);
$smarty->display($template);
?>