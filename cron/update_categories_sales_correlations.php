<?php
/*2
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 11 April 2018 at 14:37:59 GMT+8, Kuala Lumpur Malaysia
 Copyright (c) 2016, Inikoo

 Version 3

*/

require_once __DIR__.'/common.php';
include_once 'utils/new_fork.php';

$editor = array(
    'Author Type'  => '',
    'Author Key'   => '',
    'User Key'     => 0,
    'Date'         => gmdate('Y-m-d H:i:s'),
    'Subject'      => 'System',
    'Subject Key'  => 0,
    'Author Name'  => 'System',
    'Author Alias' => 'System',
);

$print_est = false;
$fork = false;


if (!empty($argv[1]) and $argv[1] == 'Departments') {
    $subject = 'Category';
} else {
    $subject = 'Product';

}

if (!empty($argv[2])){

    if ($argv[2] == 'Verbose') {
        $print_est = true;
    } elseif($argv[2] == 'Fork') {
        $fork=true;
    }

}


if (!empty($argv[3]) and is_numeric($argv[3]) and $argv[3] > 0) {
    $time_limit = ceil(3600 * $argv[3]);
} else {
    $time_limit = 0;
}







$dns_replica = $dns_replicas[array_rand($dns_replicas, 1)];
$db_replica  = new PDO(
    "mysql:host=".$dns_replica['host'].";dbname=".$dns_replica['db'].";charset=utf8mb4", $dns_replica['user'], $dns_replica['pwd']
);
$db_replica->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


$sql  = "select count(distinct `Category Key`) as num from `Category Dimension` where  `Category Scope`='Product' and `Category Subject`=?  and `Category Branch Type`='Head' ";
$stmt = $db->prepare($sql);
$stmt->execute(
    array(
        $subject
    )
);
if ($row = $stmt->fetch()) {
    $total = $row['num'];
} else {
    $total = 0;
}


$lap_time0 = date('U');
$contador  = 0;

$sql = "select `Category Key` from `Category Dimension` where `Category Scope`='Product' and `Category Subject`=? and `Category Branch Type`='Head' order by rand() ";

$stmt = $db->prepare($sql);
$stmt->execute(
    array(
        $subject
    )
);
while ($row = $stmt->fetch()) {



    if($fork){

        new_housekeeping_fork(
            'au_sales_correlation', array(
            'object' => 'Category',
            'key'    => $row['Category Key'],
            'editor'=>$editor

        ), $account->get('Account Code'), $db
        );

    }else {

        /**
         * @var $category \ProductCategory
         */
        $category = get_object('Category', $row['Category Key']);
        $category->update_product_category_sales_correlations($db_replica);


        $contador++;
        $lap_time1 = date('U');
        if ($print_est) {
            print 'Cate sales correlation '.percentage($contador, $total, 3)."  lap time ".sprintf("%.2f", ($lap_time1 - $lap_time0) / $contador)." EST  ".sprintf(
                    "%.1f", (($lap_time1 - $lap_time0) / $contador) * ($total - $contador) / 3600
                )."h  ($contador/$total) \r";
        }

        if ($time_limit > 0 and ($lap_time1 - $lap_time0) > $time_limit) {
            print "Finishing after timeout reached\n";
            break;
        }
    }
}




