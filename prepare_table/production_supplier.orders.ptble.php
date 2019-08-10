<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 18-07-2019 17:16:49 MYT, Kuala Lumpur, Malaysia
 Copyright (c) 2019, Inikoo

 Version 3

*/


$group_by = '';
$wheref   = '';

$currency = '';


$where = 'where true ';
$table = '`Purchase Order Dimension`   ';

if ($parameters['parent'] == 'production') {
    $table = '`Purchase Order Dimension` O left join `Supplier Dimension` on (`Supplier Key`=`Purchase Order Parent Key`)  ';

    $where = sprintf('where `Purchase Order Parent`="Supplier" and `Supplier Production`="Yes"');

} elseif ($parameters['parent'] == 'production_supplier' or $parameters['parent'] == 'supplier_production') {
    $table = '`Purchase Order Dimension` O left join `Supplier Dimension` on (`Supplier Key`=`Purchase Order Parent Key`)   ';

    $where = sprintf(
        'where  `Purchase Order Parent`="Supplier" and `Purchase Order Parent Key`=%d and `Supplier Production`="yes" ', $parameters['parent_key']
    );


} elseif ($parameters['parent'] == 'supplier_part') {
    $table
           = ' `Purchase Order Transaction Fact` POTF  left join  `Purchase Order Dimension` O on (POTF.`Purchase Order Key`=O.`Purchase Order Key`) ';
    $where = sprintf(
        'where `Supplier Part Key`=%d  ', $parameters['parent_key']
    );
} elseif ($parameters['parent'] == 'part') {
    $table
           = ' `Purchase Order Transaction Fact` POTF  left join  `Purchase Order Dimension` O on (POTF.`Purchase Order Key`=O.`Purchase Order Key`)
	left join  `Supplier Part Dimension` SP on (POTF.`Supplier Part Key`=SP.`Supplier Part Key`)

	 left join  `Part Dimension` P on (P.`Part SKU`=SP.`Supplier Part Part SKU`)

	 ';
    $where = sprintf('where `Part SKU`=%d  ', $parameters['parent_key']);
} else {
    exit("unknown parent :".$parameters['parent']." \n");
}


if (isset($parameters['period'])) {
    include_once 'utils/date_functions.php';
    list($db_interval, $from, $to, $from_date_1yb, $to_1yb)
        = calculate_interval_dates(
        $db, $parameters['period'], $parameters['from'], $parameters['to']
    );

    $where_interval = prepare_mysql_dates($from, $to, 'O.`Purchase Order Creation Date`');
    $where .= $where_interval['mysql'];
}


if (isset($parameters['elements_type'])) {


    switch ($parameters['elements_type']) {
        case('state'):
            $_elements            = '';
            $num_elements_checked = 0;

            foreach (
                $parameters['elements'][$parameters['elements_type']]['items'] as $_key => $_value
            ) {
                $_value = $_value['selected'];
                if ($_value) {
                    $num_elements_checked++;


                    if ($_key == 'InProcess') {
                        $_elements .= ",'InProcess','Editing_Submitted'";
                    }elseif ($_key == 'Manufacturing') {
                        $_elements .= ",'Submitted'";
                    } elseif ($_key == 'ReceivedChecked') {
                        $_elements .= ",'Received','Checked','Inputted','Dispatched'";
                    }  elseif ($_key == 'Placed') {
                        $_elements .= ",'Placed','Costing','InvoiceChecked'";
                    } else {

                        $_elements .= ",'".addslashes($_key)."'";
                    }
                }
            }

            if ($_elements == '') {
                $where .= ' and false';
            } elseif ($num_elements_checked < 5) {


                $_elements = preg_replace('/^,/', '', $_elements);

                $where .= ' and `Purchase Order State` in ('.$_elements.')';
            }
            break;


    }
}




if (($parameters['f_field'] == 'number') and $f_value != '') {

    $wheref = sprintf(
        '  and  `Purchase Order Public ID`  like "%%%s%%" ', addslashes($f_value)
    );




}




$_order = $order;
$_dir   = $order_direction;


if ($order == 'public_id') {
    $order = '`Purchase Order File As`';
} elseif ($order == 'last_date') {
    $order = 'O.`Purchase Order Last Updated Date`';
} elseif ($order == 'date') {
    $order = 'O.`Purchase Order Creation Date`';
} elseif ($order == 'supplier') {
    $order = 'O.`Purchase Order Supplier Name`';
} elseif ($order == 'state') {
    $order = 'O.`Purchase Order State`';
} elseif ($order == 'total_amount') {
    $order = 'O.`Purchase Order Total Amount`';
}elseif ($order == 'total_ac_amount') {
    $order = 'O.`Purchase Order Total Amount`*`Purchase Order Currency Exchange`';
} else {
    $order = 'O.`Purchase Order Key`';
}

$fields
    = '`Purchase Order Parent`,`Purchase Order Parent Key`,O.`Purchase Order Key`,`Purchase Order State`,`Purchase Order Public ID`,O.`Purchase Order Last Updated Date`,`Purchase Order Creation Date`,
`Purchase Order Parent Code`,`Purchase Order Parent Name`,`Purchase Order Total Amount`,`Purchase Order Currency Code`,`Purchase Order Currency Exchange`
';

$sql_totals
    = "select count(Distinct O.`Purchase Order Key`) as num from $table $where ";
//print "select $fields from $table $where $wheref order by $order $order_direction limit $start_from,$number_results";


?>