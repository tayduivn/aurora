<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created:  19 August 2017 at 15:32:51 GMT+5:30, Flight Delhi India to Bangkok
 Copyright (c) 2017, Inikoo

 Version 3

*/




$group_by = '';
$wheref   = '';

$currency = '';


$where =sprintf( 'where ((`Order State`="Dispatched" and `Order Dispatched Date`>%s ) or  `Order Replacements Dispatched Today`>0) ',
                 prepare_mysql(gmdate('Y-m-d 00:00:00')),  prepare_mysql(gmdate('Y-m-d 00:00:00'))

);
$table = '`Order Dimension` O left join `Payment Account Dimension` P on (P.`Payment Account Key`=O.`Order Payment Account Key`)';


if ($parameters['parent'] == 'store') {
    if (is_numeric($parameters['parent_key']) and in_array($parameters['parent_key'], $user->stores)) {
        $where .= sprintf(' and  `Order Store Key`=%d ', $parameters['parent_key']);
        if (!isset($store)) {
            $store = get_object('Store', $parameters['parent_key']);
        }
        $currency = $store->data['Store Currency Code'];
        $home_country = $store->get('Store Home Country Code 2 Alpha');

    } else {
        $where .= sprintf(' and  false');
    }


} elseif ($parameters['parent'] == 'account') {
    if (is_numeric($parameters['parent_key']) and in_array($parameters['parent_key'], $user->stores)) {
        $home_country = $account->get('Account Country 2 Alpha Code');

        if (count($user->stores) == 0) {
            $where .= ' and false';
        } else {

            $where .= sprintf('and  `Order Store Key` in (%s)  ', join(',', $user->stores));
        }
    }
}


if (isset($parameters['elements_type'])) {



    switch ($parameters['elements_type']) {

        case('location'):
            $_elements            = '';
            $num_elements_checked = 0;
            foreach (
                $parameters['elements']['location']['items'] as $_key => $_value
            ) {
                $_value = $_value['selected'];
                if ($_value) {
                    $num_elements_checked++;
                    $_elements .= $_key;
                }
            }

            if ($_elements == '') {
                $where .= ' and false';
            } elseif ($num_elements_checked == 2) {

            } else {
                if ($_elements == "Export") {
                    $where .= sprintf('and `Order Invoice Address Country 2 Alpha Code`!=%s', prepare_mysql($home_country));
                } else {
                    $where .= sprintf('and `Order Invoice Address Country 2 Alpha Code`=%s', prepare_mysql($home_country));
                }
            }
            break;
        case('state'):
            $_elements            = '';
            $num_elements_checked = 0;
            foreach (
                $parameters['elements']['state']['items'] as $_key => $_value
            ) {
                $_value = $_value['selected'];
                if ($_value) {
                    $num_elements_checked++;

                    $_elements .= ", '$_key'";
                }
            }

            if ($_elements == '') {
                $where .= ' and false';
            } elseif ($num_elements_checked == 7) {

            } else {
                $_elements = preg_replace('/^,/', '', $_elements);
                $where .= ' and `Order State` in ('.$_elements.')';
            }
            break;
        case('source'):
            $_elements            = '';
            $num_elements_checked = 0;
            foreach (
                $parameters['elements']['source']['items'] as $_key => $_value
            ) {
                $_value = $_value['selected'];
                if ($_value) {
                    $num_elements_checked++;

                    $_elements .= ", '$_key'";
                }
            }

            if ($_elements == '') {
                $where .= ' and false';
            } elseif ($num_elements_checked == 6) {

            } else {
                $_elements = preg_replace('/^,/', '', $_elements);
                $where .= ' and `Order Main Source Type` in ('.$_elements.')';
            }
            break;
        case('type'):
            $_elements            = '';
            $num_elements_checked = 0;
            foreach (
                $parameters['elements']['type']['items'] as $_key => $_value
            ) {
                $_value = $_value['selected'];
                if ($_value) {
                    $num_elements_checked++;

                    $_elements .= ", '$_key'";
                }
            }

            if ($_elements == '') {
                $where .= ' and false';
            } elseif ($num_elements_checked == 6) {

            } else {
                $_elements = preg_replace('/^,/', '', $_elements);
                $where .= ' and `Order Type` in ('.$_elements.')';
            }
            break;
        case('payment'):
            $_elements            = '';
            $num_elements_checked = 0;

            //'Waiting Payment','Paid','Partially Paid','Unknown','No Applicable'

            foreach (
                $parameters['elements']['payment']['items'] as $_key => $_value
            ) {
                $_value = $_value['selected'];
                if ($_value) {
                    $num_elements_checked++;



                    $_elements .= ", '$_key'";
                }
            }

            if ($_elements == '') {
                $where .= ' and false';
            } elseif ($num_elements_checked == 4) {

            } else {
                $_elements = preg_replace('/^,/', '', $_elements);
                $where .= ' and `Order Payment State` in ('.$_elements.')';
            }
            break;
    }
}




if (($parameters['f_field'] == 'customer') and $f_value != '') {
    $wheref = sprintf(
        '  and  `Order Customer Name`  REGEXP "\\\\b%s" ', addslashes($f_value)
    );
}  elseif ($parameters['f_field'] == 'number' and $f_value != '') {
    $wheref = " and  `Order Public ID`  like '".addslashes($f_value)."%'";
}


$_order = $order;
$_dir   = $order_direction;


if ($order == 'public_id') {
    $order = '`Order File As`';
} elseif ($order == 'last_date' or $order == 'date') {
    $order = 'O.`Order Date`';
} elseif ($order == 'customer') {
    $order = 'O.`Order Customer Name`';
} elseif ($order == 'dispatch_state') {
    $order = 'O.`Order State`';
} elseif ($order == 'payment_state') {
    $order = 'O.`Order Payment State`';
} elseif ($order == 'total_amount') {
    $order = 'O.`Order Total Amount`';
} else {
    $order = 'O.`Order Key`';
}

$fields
    = '`Order Invoiced`,`Order Number Items`,`Order Store Key`,`Payment Account Name`,`Order Payment Method`,`Order Balance Total Amount`,`Order Payment State`,`Order State`,`Order Type`,`Order Currency Exchange`,`Order Currency`,O.`Order Key`,O.`Order Public ID`,`Order Customer Key`,`Order Customer Name`,O.`Order Last Updated Date`,O.`Order Date`,`Order Total Amount`,
     (select group_concat(`Delivery Note Key`) from `Delivery Note Dimension` where `Delivery Note Order Key`=O.`Order Key`   ) as delivery_notes
    
    
    ';

$sql_totals = "select count(Distinct O.`Order Key`) as num from $table $where";
//$sql="select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";
//print $sql;


?>
