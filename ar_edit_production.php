<?php
/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 1 February 2017 at 00:47:49 GMT+8, Cyberjaya, Malaysia
 Copyright (c) 2015, Inikoo

 Version 3

*/

require_once 'common.php';
require_once 'utils/ar_common.php';


if (!isset($_REQUEST['tipo'])) {
    $response = array(
        'state' => 405,
        'resp'  => 'Non acceptable request (t)'
    );
    echo json_encode($response);
    exit;
}


$tipo = $_REQUEST['tipo'];

switch ($tipo) {


    case 'update_job_order_item':
        $data = prepare_values(
            $_REQUEST, array(
                         'key'      => array('type' => 'key'),
                         'action'   => array('type' => 'string'),
                         'qty'      => array('type' => 'numeric'),
                         'qty_type' => array('type' => 'string'),

                     )
        );
        update_job_order_item($data, $db, $editor);
        break;


    default:
        $response = array(
            'state' => 405,
            'resp'  => 'Tipo not found '.$tipo
        );
        echo json_encode($response);
        exit;

}


function update_job_order_item($data, $db, $editor) {

    print_r($data);

    $sql  =
        "select `Purchase Order Transaction Fact Key`,`Purchase Order Key`,`Purchase Order Transaction State`,`Purchase Order Submitted Units Per SKO`,`Purchase Order Submitted SKOs Per Carton` from  `Purchase Order Transaction Fact` where `Purchase Order Transaction Fact Key`=?";
    $stmt = $db->prepare($sql);
    $stmt->execute(
        array(
            $data['key']
        )
    );
    if ($row = $stmt->fetch()) {
        $purchase_order         = get_object('Purchase_Order', $row['Purchase Order Key']);
        $purchase_order->editor = $editor;


        switch ($data['qty_type']) {
            case 'sko':
                $qty = $data['qty'] * $row['Purchase Order Submitted Units Per SKO'];
                break;
            case 'sko':
                $qty = $data['qty'] * $row['Purchase Order Submitted Units Per SKO'] * $row['Purchase Order Submitted SKOs Per Carton'];
                break;
            default:
                $qty = $data['qty'];
        }


        switch ($data['action']) {

            case 'finish_manufacture':
                if ($row['Purchase Order Transaction State'] == 'Confirmed') {
                    $purchase_order->update_purchase_order_transaction($row['Purchase Order Transaction Fact Key'], 'Manufactured', $qty);

                } else {
                    $response = array(
                        'state' => 405,
                        'resp'  => 'Can not do this '.$row['Purchase Order Transaction State']
                    );
                    echo json_encode($response);
                    exit;
                }


                break;
            default:
                break;
        }


    }


}

