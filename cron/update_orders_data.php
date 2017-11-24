
<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 24 November 2017 at 14:29:35 GMT+8, Kuala Lumpur, Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/natural_language.php';
require_once 'utils/date_functions.php';




require_once 'class.Store.php';
require_once 'class.Order.php';


$editor = array(
    'Author Name'  => '',
    'Author Alias' => '',
    'Author Type'  => '',
    'Author Key'   => '',
    'User Key'     => 0,
    'Date'         => gmdate('Y-m-d H:i:s')
);

$print_est = true;

print date('l jS \of F Y h:i:s A')."\n";


$account = new Account();

$account->update_orders();


$sql = sprintf("SELECT `Order Key` FROM `Order Dimension`");
if ($result = $db->query($sql)) {
    foreach ($result as $row) {
        $order = new Order('id', $row['Order Key']);

        $order->update_totals();

    }

} else {
    print_r($error_info = $db->errorInfo());
    exit;
}

?>
