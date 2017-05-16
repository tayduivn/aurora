<?php

/*
 About:
 Author: Raul Perusquia <raul@inikoo.com>
 Created: 3 December 2016 at 18:35:44 GMT+8, Kuta, Bali, Indonesia
 Copyright (c) 2016, Inikoo

 Version 3

*/


class Public_Customer {

    function __construct($arg1 = false, $arg2 = false, $arg3 = false) {

        global $db;
        $this->db = $db;
        $this->id = false;


        $this->table_name = 'Customer';

        if (is_numeric($arg1)) {
            $this->get_data('id', $arg1);

            return;
        }

        $this->get_data($arg1, $arg2, $arg3);


    }


    function get_data($key, $id, $aux_id = false) {

        if ($key == 'id') {
            $sql = sprintf(
                "SELECT * FROM `Customer Dimension` WHERE `Customer Key`=%d", $id
            );
            if ($this->data = $this->db->query($sql)->fetch()) {
                $this->id          = $this->data['Customer Key'];
            }
        } else {

            return;
        }


    }

    function get($key, $arg1 = '') {

        switch ($key) {
           
            default:


        }

    }



    function get_order_in_process_key($dispatch_state = 'all') {

        if ($dispatch_state == 'all') {
            $dispatch_state_valid_values = "'In Process by Customer','Waiting for Payment Confirmation'";
        } else {
            $dispatch_state_valid_values = "'In Process by Customer'";
        }

        $order_key = false;
        $sql       = sprintf(
            "SELECT `Order Key` FROM `Order Dimension` WHERE `Order Customer Key`=%d AND `Order Current Dispatch State` IN (%s) ", $this->id, $dispatch_state_valid_values
        );


        if ($result=$this->db->query($sql)) {
            if ($row = $result->fetch()) {

                $order_key = $row['Order Key'];
            }
        }else {
            print_r($error_info=$this->db->errorInfo());
            print "$sql\n";
            exit;
        }


        return $order_key;
    }



}


?>
